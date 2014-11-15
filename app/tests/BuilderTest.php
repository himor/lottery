<?php

class BuilderTest extends TestCase
{
    public function testBuilder()
    {
        $numbers = Builder::getNumbers(true);
        $this->assertNotNull($numbers);
        $this->assertEquals($numbers, []);

        Builder::generate();
        $numbers = Builder::getNumbers(true);
        $this->assertNotNull($numbers);

        $this->assertArrayHasKey('set', $numbers);
        $this->assertArrayHasKey('mb', $numbers);

        $this->assertCount(5, $numbers['set']);
        $this->assertNotNull($numbers['mb']);
    }
}