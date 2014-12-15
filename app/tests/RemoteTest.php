<?php

class RemoteTest extends TestCase
{
    public function testConnection()
    {
        $data = LotteryRemote::getData();
        $this->assertInternalType('array', $data);

        $single = current($data);
        $this->assertInternalType('array', $single);

        $this->assertArrayHasKey('draw_date', $single);
        $this->assertArrayHasKey('winning_numbers', $single);
        $this->assertArrayHasKey('mega_ball', $single);

        $this->assertCount(5, $single['winning_numbers']);
    }
} 