<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use PHPUnit\Framework\Assert;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    public const BASE_API_URI = '/api/v1';

    protected function setUp(): void
    {
        parent::setUp();

        // prevent throttling because we run test in parallel
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    /**
     * Check if two arrays have the same value
     *
     * @param array $arr1
     * @param array $arr2
     * @return bool
     */
    protected function arraysHaveSameValue(array $arr1, array $arr2): bool
    {
        return (count($arr1) === count($arr2)) && !array_diff($arr1, $arr2);
    }

    /**
     * Generate required user info input
     *
     * @return array
     */
    protected function getRequiredUserInputSample(): array
    {
        return [
            'email' => fake()->unique()->safeEmail,
            'username' => fake()->unique()->userName,
            'password' => 'Sample_Password_1',
            'password_confirmation' => 'Sample_Password_1',
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName
        ];
    }
}
