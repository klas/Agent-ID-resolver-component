<?php

namespace Tests\Unit;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\AgentDTO;
use App\Models\Company;
use App\Models\Agent;
use App\Models\Aidalias;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use App\Strategy\AidStepFilteringResolvingStrategy;
use Mockery;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Doubles\TestFilterDefinition;
use Tests\TestCase;

class OldAidStepFilteringResolvingStrategyTestX extends TestCase
{
    protected AidStepFilteringResolvingStrategy $strategy;
    protected $stepFilterBuilderMock;
    protected $aidAliasRepositoryMock;

    public function setUp(): void
    {
        $this->markTestSkipped(
            'OLD'
        );

        parent::setUp();

        $this->stepFilterBuilderMock = Mockery::mock(StepFilterBuilderInterface::class);
        $this->aidAliasRepositoryMock = Mockery::mock(AidAliasRepositoryInterface::class);

        $this->strategy = new AidStepFilteringResolvingStrategy(
            $this->stepFilterBuilderMock,
            $this->aidAliasRepositoryMock
        );
    }

    public function testResolveExactMatch()
    {
        $data = ['company' => 'Test Company', 'aid' => '12345'];
        $agent = Agent::create(['name' => 'Test Agent']);
        $company = Company::create(['name' => 'Test Company']);
        $company->agents()->attach($agent);
        $company->save();
        $company->refresh();

        $agent = $company->agents->firstWhere('id', '==', $agent->id);
        $aidalias = Aidalias::create(['name' => '12345', 'gm_id' => $agent->pivot->id]);

        $this->stepFilterBuilderMock->expects($this->never())
            ->method('setFilterable');  // Should not be called for exact match

        $result = $this->strategy->resolve($data);
        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals($agent->name, $result->name);
    }

    public function testResolveFilteredMatch()
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

        $this->stepFilterBuilderMock->expects($this->atLeastOnce())
            ->method('setFilterable')
            ->with('12345x');
        $this->stepFilterBuilderMock->expects($this->atLeastOnce())
            ->method('getFiltered')
            ->willReturn('12345');

        $result = $this->strategy->resolve($data);
        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals($agent->name, $result->name);
    }

    public function testResolveNoMatch()
    {
        $this->addTestFilterDefinition();

        $data = ['company' => 'Test Company', 'aid' => '12345'];

        $this->stepFilterBuilderMock->expects($this->once())
            ->method('setFilterable')
            ->with('12345');
        $this->stepFilterBuilderMock->expects($this->once())
            ->method('getFiltered')
            ->willReturn('');

        $result = $this->strategy->resolve($data);
        $this->assertNull($result);
    }

    public function testResolveInvalidData()
    {
        $data = ['company' => 'Test Company'];

        $this->expectException(\InvalidArgumentException::class);
        $this->strategy->resolve($data);
    }

    public function testFilterAid()
    {
        $this->addTestFilterDefinition();

        $filterable = '12345x';
        $company = 'Test Company';

        $this->stepFilterBuilderMock->expects($this->once())
            ->method('setFilterable')
            ->with($filterable);
        $this->stepFilterBuilderMock->expects($this->once())
            ->method('getFiltered')
            ->willReturn('12345');

        $reflection = new ReflectionClass($this->strategy);
        $method = $reflection->getMethod('filterAid');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->strategy, [$filterable, $company]);
        $this->assertEquals('12345', $result);
    }

    public function testResolveReturnsAgentDTOSuccessfullyOnExactAidMatch()
    {
        // Create a partial mock of the strategy to override `getAgentPerExactAid`
        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getAgentPerExactAid'])
            ->getMock();

        // Stub `getAgentPerExactAid` to return a mock "Agent" object with a name
        $strategy->method('getAgentPerExactAid')->willReturn(new Agent(['name' => 'AgentName']));

        $data = ['company' => 'some_company', 'aid' => 'some_aid'];

        $result = $strategy->resolve($data);

        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals('AgentName', $result->name);
    }

    public function testResolveFiltersAndFindsAlias()
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getAgentPerExactAid', 'getSearchableAidAliases', 'filterAid'])
            ->getMock();

        // Stub `getAgentPerExactAid` to return null (no exact match)
        $strategy->method('getAgentPerExactAid')->willReturn(null);

        // Stub `getSearchableAidAliases` to return a collection with a matching alias
        $mockAlias = (object) [
            'name' => 'filtered_aid',
            'companies_agent' => (object) ['agent' => (object) ['name' => 'AgentFromAlias']],
            'gm_id' => 1,
        ];
        $strategy->method('getSearchableAidAliases')->willReturn(collect([$mockAlias]));

        // Stub `filterAid` to return a filtered value that matches alias name
        $strategy->method('filterAid')->willReturn('filtered_aid');

        $data = ['company' => 'some_company', 'aid' => 'some_aid'];

        $result = $strategy->resolve($data);

        $this->assertInstanceOf(AgentDTO::class, $result);
        $this->assertEquals('AgentFromAlias', $result->name);
    }

    public function testResolveThrowsNotFoundHttpExceptionIfFilterDefinitionIsMissing()
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getAgentPerExactAid', 'getSearchableAidAliases', 'filterAid'])
            ->getMock();

        // Stub `getAgentPerExactAid` to return null
        $strategy->method('getAgentPerExactAid')->willReturn(null);

        // Stub `getSearchableAidAliases` to return an empty collection
        $strategy->method('getSearchableAidAliases')->willReturn(collect());

        // Make `filterAid` throw the NotFoundHttpException
        $strategy->method('filterAid')->willThrowException(new NotFoundHttpException('Filter Definition Not Found'));

        $data = ['company' => 'some_company', 'aid' => 'some_aid'];

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Filter Definition Not Found');

        $strategy->resolve($data);
    }

    public function testResolveReturnsNullIfNoMatchIsFound()
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(AidStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getAgentPerExactAid', 'getSearchableAidAliases', 'filterAid'])
            ->getMock();

        // Stub methods to simulate no match found
        $strategy->method('getAgentPerExactAid')->willReturn(null);
        $strategy->method('getSearchableAidAliases')->willReturn(collect());
        $strategy->method('filterAid')->willReturn(null);

        $data = ['company' => 'some_company', 'aid' => 'some_aid'];

        $result = $strategy->resolve($data);

        $this->assertNull($result);
    }

    protected function addTestFilterDefinition(): void
    {
        $this->app->tag(
            [
                TestFilterDefinition::class,
            ],
            'filter_definition');
    }
}
