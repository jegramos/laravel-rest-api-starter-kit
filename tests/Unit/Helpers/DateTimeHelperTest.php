<?php

namespace Tests\Unit\Helpers;

use App\Helpers\DateTimeHelper;
use PHPUnit\Framework\TestCase;

class DateTimeHelperTest extends TestCase
{
    private DateTimeHelper $dateTimeHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dateTimeHelper = new DateTimeHelper();
    }


    /** @dataProvider stringSamples */
    public function test_it_can_append_current_timestamp_with_a_separator(array $input)
    {
        $string = $this->dateTimeHelper->appendTimestamp($input[0], $input[1] ?? '::');
        $separator = $input[1] ?? '::';
        $result = explode($separator, $string);
        $this->assertEquals(2, count($result));
    }

    public function test_appended_timestamp_can_be_converted_to_datetime()
    {
        $string = $this->dateTimeHelper->appendTimestamp('info_string');
        $timeStamp = explode('::', $string)[1];

        $datetime = date('Y-m-d', $timeStamp);
        $this->assertTrue(!!strtotime($datetime));
    }

    public function stringSamples(): array
    {
        return [
            [['message1', null]],
            [['another message', '-']],
            [['message number 3', '_']],
            [['message_number_4', '::']]
        ];
    }
}
