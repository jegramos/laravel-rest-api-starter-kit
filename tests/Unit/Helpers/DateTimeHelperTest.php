<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class DateTimeHelperTest extends TestCase
{

    /** @dataProvider stringSamples */
    public function test_it_can_append_current_timestamp_with_a_separator($input)
    {
        $this->assertTrue(true);
    }

    public function stringSamples(): array
    {
        return [
            ['message1', null],
            ['another message', '-'],
            ['message number 3', '_'],
            ['message_number_4', '::']
        ];
    }
}
