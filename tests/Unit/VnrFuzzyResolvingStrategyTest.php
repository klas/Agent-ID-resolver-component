<?php

namespace Tests\Unit;

use App\Services\FuzzyService;
use App\Strategy\VnrFuzzyResolvingStrategy;
use Tests\TestCase;

class VnrFuzzyResolvingStrategyTest extends TestCase
{
    private $strategy;

    public function setUp(): void
    {
        $this->strategy = new VnrFuzzyResolvingStrategy(new FuzzyService);
    }

    public function testIntersectionEmptyStrings()
    {
        $this->assertEquals('', $this->strategy->intersection('', ''));
    }

    public function testIntersectionSingleCharacterStrings()
    {
        $this->assertEquals('a', $this->strategy->intersection('a', 'a'));
        $this->assertEquals('', $this->strategy->intersection('a', 'b'));
    }

    public function testIntersectionMultipleCharacterStringsCommon()
    {
        $this->assertEquals('ab', $this->strategy->intersection('abc', 'abd'));
        $this->assertEquals('abc', $this->strategy->intersection('abc', 'abc'));
    }

    public function testIntersectionMultipleCharacterStringsNoCommon()
    {
        $this->assertEquals('', $this->strategy->intersection('abc', 'def'));
    }

    public function testIntersectionStringsDuplicateCharacters()
    {
        $this->assertEquals('aa', $this->strategy->intersection('aaa', 'aa'));
        $this->assertEquals('a', $this->strategy->intersection('aaa', 'ab'));
    }

    public function testIntersectionStringsDifferentCase()
    {
        $this->assertEquals('', $this->strategy->intersection('Abc', 'abc'));
    }
}
