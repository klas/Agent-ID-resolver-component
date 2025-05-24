<?php

namespace Tests\Unit\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\AgentDTO;
use App\Models\Agent;
use App\Models\Aidalias;
use App\Models\Company;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use App\Strategy\AidStepFilteringResolvingStrategy;
use InvalidArgumentException;
use Mockery;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Doubles\TestFilterDefinition;
use Tests\TestCase;

class AidStepFilteringResolvingStrategyTest extends TestCase
{
    protected AidStepFilteringResolvingStrategy $strategy;

    protected $stepFilterBuilderMock;

    protected $aidAliasRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->stepFilterBuilderMock = Mockery::mock(StepFilterBuilderInterface::class);
        $this->aidAliasRepositoryMock = Mockery::mock(AidAliasRepositoryInterface::class);

        $this->strategy = new AidStepFilteringResolvingStrategy(
            $this->stepFilterBuilderMock,
            $this->aidAliasRepositoryMock
        );
    }

    public function testResolveWithExactMatch(): void
    {
        $data = ['company' => 'Test Company', 'aid' => '12345'];
        $agent = new Agent(['name' => 'Test Agent']);

        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->with('Test Company', '12345')
            ->andReturn($agent);

        $result = $this->strategy->resolve($data);

        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals('Test Agent', $result->name);
    }

    public function testResolveExactMatch(): void
    {
        // Integration test with real database
        $data = ['company' => 'Test Company', 'aid' => '12345'];
        $agent = Agent::create(['name' => 'Test Agent']);
        $company = Company::create(['name' => 'Test Company']);
        $company->agents()->attach($agent);
        $company->save();
        $company->refresh();

        $agent = $company->agents->firstWhere('id', '==', $agent->id);
        $aidalias = Aidalias::create(['name' => '12345', 'gm_id' => $agent->pivot->id]);

        // Use real repository for integration test
        $realStrategy = app(AidStepFilteringResolvingStrategy::class);

        $result = $realStrategy->resolve($data);
        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals($agent->name, $result->name);
    }

    public function testResolveFilteredMatch(): void
    {
        $this->addTestFilterDefinition();

        $data = ['company' => 'Test Company', 'aid' => '12345x'];
        $agent = Agent::create(['name' => 'Test Agent']);
        $company = Company::create(['name' => 'Test Company']);
        $company->agents()->attach($agent);
        $company->save();
        $company->refresh();

        $agent = $company->agents->firstWhere('id', '==', $agent->id);
        $aidalias = Aidalias::create(['name' => '12345', 'gm_id' => $agent->pivot->id]);

        // Use real repository for integration test
        $realStrategy = app(AidStepFilteringResolvingStrategy::class);

        $result = $realStrategy->resolve($data);
        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals($agent->name, $result->name);
    }

    public function testResolveNoMatch(): void
    {
        $this->addTestFilterDefinition();

        $data = ['company' => 'Test Company', 'aid' => '12345'];

        // Use real repository for integration test
        $realStrategy = app(AidStepFilteringResolvingStrategy::class);

        $result = $realStrategy->resolve($data);
        $this->assertNull($result);
    }

    public function testResolveInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->strategy->resolve(['company' => 'Test Company']); // Missing 'aid'
    }

    public function testFilterAid(): void
    {
        $this->addTestFilterDefinition();

        $filterable = '12345x';
        $company = 'Test Company';

        // Use real strategy with real dependencies for protected method testing
        $realStrategy = app(AidStepFilteringResolvingStrategy::class);

        $reflection = new ReflectionClass($realStrategy);
        $method = $reflection->getMethod('filterAid');
        $method->setAccessible(true);

        $result = $method->invokeArgs($realStrategy, [$filterable, $company]);
        $this->assertEquals('12345', $result);
    }

    public function testResolveReturnsAgentDTOSuccessfullyOnExactAidMatch(): void
    {
        $data = ['company' => 'some_company', 'aid' => 'some_aid'];
        $agent = new Agent(['name' => 'AgentName']);

        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->with('some_company', 'some_aid')
            ->andReturn($agent);

        $result = $this->strategy->resolve($data);

        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals('AgentName', $result->name);
    }

    public function testResolveFiltersAndFindsAlias(): void
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock, $this->aidAliasRepositoryMock])
            ->onlyMethods(['filterAid'])
            ->getMock();

        //$this->addTestFilterDefinition();

        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->andReturn(null);

        // Stub `filterAid` to return a filtered value that matches alias name
        $strategy->method('filterAid')->willReturn('filtered_aid');

        $this->aidAliasRepositoryMock
            ->shouldReceive('findByFilteredAid')
            ->once()
            ->andReturn(null);

        // Create mock alias object
        $mockAlias = (object) [
            'name' => 'filtered_aid',
            'companies_agent' => (object) ['agent' => (object) ['name' => 'AgentFromAlias']],
            'gm_id' => 1,
        ];

        $this->aidAliasRepositoryMock
            ->shouldReceive('getSearchableAidAliases')
            ->once()
            ->andReturn(collect([$mockAlias]));

        $this->aidAliasRepositoryMock
            ->shouldReceive('create')
            ->twice()
            ->andReturn(new Aidalias);

        $this->stepFilterBuilderMock
            ->shouldReceive('setFilterable')
            ->andReturnSelf();

        $this->stepFilterBuilderMock
            ->shouldReceive('getFiltered')
            ->andReturn('filtered_aid');

        $data = ['company' => 'some_company', 'aid' => 'some_aid'];
        $result = $strategy->resolve($data);

        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals('AgentFromAlias', $result->name);
    }

    public function testResolveThrowsNotFoundHttpExceptionIfFilterDefinitionIsMissing(): void
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock, $this->aidAliasRepositoryMock])
            ->onlyMethods(['filterAid'])
            ->getMock();

        // Stub `getAgentPerExactAid` to return null
        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->andReturn(null);

        // Stub `getSearchableAidAliases` to return an empty collection
        $this->aidAliasRepositoryMock->shouldReceive('getSearchableAidAliases')->andReturn(collect());

        // Make `filterAid` throw the NotFoundHttpException
        $strategy->method('filterAid')->willThrowException(new NotFoundHttpException('Filter Definition Not Found'));

        $data = ['company' => 'some_company', 'aid' => 'some_aid'];

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Filter Definition Not Found');

        $strategy->resolve($data);
    }

    public function testResolveReturnsNullIfNoMatchIsFound(): void
    {

        $this->addTestFilterDefinition();

        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->andReturn(null);

        $this->aidAliasRepositoryMock
            ->shouldReceive('findByFilteredAid')
            ->once()
            ->andReturn(null);

        $this->aidAliasRepositoryMock
            ->shouldReceive('getSearchableAidAliases')
            ->once()
            ->andReturn(collect([]));

        $this->stepFilterBuilderMock
            ->shouldReceive('setFilterable')
            ->andReturnSelf();
        $this->stepFilterBuilderMock
            ->shouldReceive('getFiltered')
            ->andReturn('filtered_aid');

        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock, $this->aidAliasRepositoryMock])
            ->onlyMethods(['filterAid'])
            ->getMock();

        // Stub `filterAid` to return not foundable value value
        $strategy->method('filterAid')->willReturn('not_foundable_aid');

        $data = ['company' => 'Test Company', 'aid' => '12345'];
        $result = $strategy->resolve($data);

        $this->assertNull($result);
    }

    public function testResolveWithMockedRepository(): void
    {
        $data = ['company' => 'Test Company', 'aid' => '12345'];
        $this->addTestFilterDefinition();

        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->with('Test Company', '12345')
            ->andReturn(null);

        $this->aidAliasRepositoryMock
            ->shouldReceive('findByFilteredAid')
            ->once()
            ->andReturn(null);

        $this->aidAliasRepositoryMock
            ->shouldReceive('getSearchableAidAliases')
            ->once()
            ->andReturn(collect([]));

        $this->stepFilterBuilderMock
            ->shouldReceive('filterNonNumeric')
            ->andReturnSelf();

        $this->stepFilterBuilderMock
            ->shouldReceive('setFilterable')
            ->andReturnSelf();
        $this->stepFilterBuilderMock
            ->shouldReceive('getFiltered')
            ->andReturn('12345');

        $result = $this->strategy->resolve($data);

        $this->assertNull($result);
    }

    protected function addTestFilterDefinition(): void
    {
        $this->app->tag(
            [
                TestFilterDefinition::class,
            ],
            'filter_definition'
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
