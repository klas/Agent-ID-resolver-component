<?php

namespace Tests\Unit;

use App\Services\FuzzyService;
use Illuminate\Foundation\Testing\TestCase;

class FuzzyServiceTest extends TestCase
{
    protected FuzzyService $fuzzyService;

    protected function setUp(): void
    {
        $this->fuzzyService = new FuzzyService;
    }

    public function testLevenshtein(): void
    {
        $this->assertEquals(3, $this->fuzzyService->levenshtein('dog', 'cat'));
    }

    public function testTextSimilarity(): void
    {
        $percent = 0;
        $this->assertEquals(3, $this->fuzzyService->textSimilarity('dog', 'doggy', $percent));
        $this->assertEquals(75, $percent);
    }

    public function testFuzzyWuzzy(): void
    {
        $this->assertEquals(66, $this->fuzzyService->fuzzyWuzzy('dog', 'dost'));
        $this->assertEquals(100, $this->fuzzyService->fuzzyWuzzy('dog', 'doggy'));
    }

    public function testDeleteOperationsScoreBothSides(): void
    {
        $this->assertEquals(6, $this->fuzzyService->deleteOperationsScoreBothSides('dog', 'cat'));
        $this->assertEquals(2, $this->fuzzyService->deleteOperationsScoreBothSides('dog', 'doggy'));
    }

    public function testStringDiff()
    {
        $this->assertEquals('dogcat', $this->fuzzyService->stringDiff('dog', 'cat'));
        $this->assertEquals('', $this->fuzzyService->stringDiff('dog', 'dog'));
        $this->assertEquals('ou', $this->fuzzyService->stringDiff('dog', 'dug'));
        $this->assertEquals('Q87', $this->fuzzyService->stringDiff('412548787', 'Q412548777'));
        $this->assertEquals('Q', $this->fuzzyService->stringDiff('412548777', 'Q412548777'));
    }
}
