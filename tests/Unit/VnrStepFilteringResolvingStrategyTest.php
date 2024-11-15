<?php

namespace Tests\Unit;

use App\Builder\StepFilterBuilderInterface;
use App\DTO\MaklerDTO;
use App\Models\Gesellschaft;
use App\Models\Makler;
use App\Models\Vnralias;
use App\Strategy\VnrStepFilteringResolvingStrategy;
use ReflectionClass;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Doubles\TestFilterDefinition;
use Tests\TestCase;

class VnrStepFilteringResolvingStrategyTest extends TestCase
{
    private $strategy;

    private $stepFilterBuilderMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->stepFilterBuilderMock = $this->getMockBuilder(StepFilterBuilderInterface::class)
            ->getMock();
        $this->strategy = new VnrStepFilteringResolvingStrategy($this->stepFilterBuilderMock);
    }

    public function testResolveExactMatch()
    {
        $data = ['gesellschaft' => 'Test Gesellschaft', 'vnr' => '12345'];
        $makler = Makler::create(['name' => 'Test Makler']);
        $gesellschaft = Gesellschaft::create(['name' => 'Test Gesellschaft']);
        $gesellschaft->maklers()->attach($makler);
        $gesellschaft->save();
        $gesellschaft->refresh();

        $makler = $gesellschaft->maklers->firstWhere('id', '==', $makler->id);
        $vnralias = Vnralias::create(['name' => '12345', 'gm_id' => $makler->pivot->id]);

        $this->stepFilterBuilderMock->expects($this->never())
            ->method('setFilterable');  // Should not be called for exact match

        $result = $this->strategy->resolve($data);
        $this->assertInstanceOf(MaklerDTO::class, $result);
        $this->assertEquals($makler->name, $result->name);
    }

    public function testResolveFilteredMatch()
    {
        $this->app->tag(
            [
                TestFilterDefinition::class,
            ],
            'filter_definition');


        $data = ['gesellschaft' => 'Test Gesellschaft', 'vnr' => '12345'];
        $makler = Makler::create(['name' => 'Test Makler']);
        $gesellschaft = Gesellschaft::create(['name' => 'Test Gesellschaft']);
        $gesellschaft->maklers()->attach($makler);
        $gesellschaft->save();
        $gesellschaft->refresh();

        $makler = $gesellschaft->maklers->firstWhere('id', '==', $makler->id);
        $vnralias = Vnralias::create(['name' => '12345x', 'gm_id' => $makler->pivot->id]);

        /*$this->stepFilterBuilderMock->expects($this->once())
            ->method('setFilterable')
            ->with('12345');
        $this->stepFilterBuilderMock->expects($this->once())
            ->method('getFiltered')
            ->willReturn('12345');*/

        $result = $this->strategy->resolve($data);
        $this->assertInstanceOf(MaklerDTO::class, $result);
        $this->assertEquals($makler->name, $result->name);
    }

    public function testResolveNoMatch()
    {
        $data = ['gesellschaft' => 'Test Gesellschaft', 'vnr' => '12345'];

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
        $data = ['gesellschaft' => 'Test Gesellschaft'];

        $this->expectException(\InvalidArgumentException::class);
        $this->strategy->resolve($data);
    }

    public function testFilterVnr()
    {
        $filterable = '12345';
        $gesellschaft = 'Test Gesellschaft';

        $this->stepFilterBuilderMock->expects($this->once())
            ->method('setFilterable')
            ->with($filterable);
        $this->stepFilterBuilderMock->expects($this->once())
            ->method('getFiltered')
            ->willReturn('12345');

        $reflection = new ReflectionClass($this->strategy);
        $method = $reflection->getMethod('filterVnr');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->strategy, [$filterable, $gesellschaft]);
        $this->assertEquals('12345', $result);
    }

    public function testResolveReturnsMaklerDTOSuccessfullyOnExactVnrMatch()
    {
        // Create a partial mock of the strategy to override `getMaklerPerExactVnr`
        $strategy = $this->getMockBuilder(VnrStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getMaklerPerExactVnr'])
            ->getMock();

        // Stub `getMaklerPerExactVnr` to return a mock "Makler" object with a name
        $strategy->method('getMaklerPerExactVnr')->willReturn((object) ['name' => 'MaklerName']);

        $data = ['gesellschaft' => 'some_gesellschaft', 'vnr' => 'some_vnr'];

        $result = $strategy->resolve($data);

        $this->assertInstanceOf(MaklerDTO::class, $result);
        $this->assertEquals('MaklerName', $result->name);
    }

    public function testResolveFiltersAndFindsAlias()
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(VnrStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getMaklerPerExactVnr', 'getSearchableVnrAliases', 'filterVnr'])
            ->getMock();

        // Stub `getMaklerPerExactVnr` to return null (no exact match)
        $strategy->method('getMaklerPerExactVnr')->willReturn(null);

        // Stub `getSearchableVnrAliases` to return a collection with a matching alias
        $mockAlias = (object) [
            'name' => 'filtered_vnr',
            'gesellschafts_makler' => (object) ['makler' => (object) ['name' => 'MaklerFromAlias']],
        ];
        $strategy->method('getSearchableVnrAliases')->willReturn(collect([$mockAlias]));

        // Stub `filterVnr` to return a filtered value that matches alias name
        $strategy->method('filterVnr')->willReturn('filtered_vnr');

        $data = ['gesellschaft' => 'some_gesellschaft', 'vnr' => 'some_vnr'];

        $result = $strategy->resolve($data);

        $this->assertInstanceOf(MaklerDTO::class, $result);
        $this->assertEquals('MaklerFromAlias', $result->name);
    }

    public function testResolveThrowsNotFoundHttpExceptionIfFilterDefinitionIsMissing()
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(VnrStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getMaklerPerExactVnr', 'getSearchableVnrAliases', 'filterVnr'])
            ->getMock();

        // Stub `getMaklerPerExactVnr` to return null
        $strategy->method('getMaklerPerExactVnr')->willReturn(null);

        // Stub `getSearchableVnrAliases` to return an empty collection
        $strategy->method('getSearchableVnrAliases')->willReturn(collect());

        // Make `filterVnr` throw the NotFoundHttpException
        $strategy->method('filterVnr')->willThrowException(new NotFoundHttpException('Filter Definition Not Found'));

        $data = ['gesellschaft' => 'some_gesellschaft', 'vnr' => 'some_vnr'];

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Filter Definition Not Found');

        $strategy->resolve($data);
    }

    public function testResolveReturnsNullIfNoMatchIsFound()
    {
        // Create a partial mock of the strategy to override methods
        $strategy = $this->getMockBuilder(VnrStepFilteringResolvingStrategy::class)
            ->setConstructorArgs([$this->stepFilterBuilderMock])
            ->onlyMethods(['getMaklerPerExactVnr', 'getSearchableVnrAliases', 'filterVnr'])
            ->getMock();

        // Stub methods to simulate no match found
        $strategy->method('getMaklerPerExactVnr')->willReturn(null);
        $strategy->method('getSearchableVnrAliases')->willReturn(collect());
        $strategy->method('filterVnr')->willReturn(null);

        $data = ['gesellschaft' => 'some_gesellschaft', 'vnr' => 'some_vnr'];

        $result = $strategy->resolve($data);

        $this->assertNull($result);
    }
}
