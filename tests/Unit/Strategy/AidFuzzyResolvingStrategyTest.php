<?php
namespace Tests\Unit\Strategy;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\AgentDTO;
use App\Models\Agent;
use App\Repositories\Contracts\AidAliasRepositoryInterface;
use App\Services\FuzzyInterface;
use App\Strategy\AidFuzzyResolvingStrategy;
use InvalidArgumentException;
use Mockery;
use Tests\TestCase;

class AidFuzzyResolvingStrategyTest extends TestCase
{
    protected AidFuzzyResolvingStrategy $strategy;
    protected $stepFilterBuilderMock;
    protected $fuzzyMock;
    protected $aidAliasRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->stepFilterBuilderMock = Mockery::mock(StepFilterBuilderInterface::class);
        $this->fuzzyMock = Mockery::mock(FuzzyInterface::class);
        $this->aidAliasRepositoryMock = Mockery::mock(AidAliasRepositoryInterface::class);

        $this->strategy = new AidFuzzyResolvingStrategy(
            $this->stepFilterBuilderMock,
            $this->fuzzyMock,
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

    public function testResolveWithInvalidData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->strategy->resolve(['company' => 'Test Company']); // Missing 'aid'
    }

    public function testResolveReturnsNullWhenNoMatch(): void
    {
        $data = ['company' => 'Test Company', 'aid' => '12345'];

        $this->aidAliasRepositoryMock
            ->shouldReceive('getAgentByExactAid')
            ->once()
            ->with('Test Company', '12345')
            ->andReturn(null);

        $this->aidAliasRepositoryMock
            ->shouldReceive('getSearchableAidAliases')
            ->once()
            ->with('Test Company')
            ->andReturn(collect([]));

        $result = $this->strategy->resolve($data);

        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
