<?php

namespace Tests\Unit\Helpers;

use App\Enums\PaginationType;
use App\Helpers\PaginationHelper;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserProfile;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PaginationHelperTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private PaginationHelper $paginationHelper;
    private UserRepositoryInterface $userRepository;
    private int $usersCount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paginationHelper = new PaginationHelper();
        $this->userRepository = new UserRepository(new User());
        $this->usersCount = 10;
        User::factory()->has(UserProfile::factory())->count($this->usersCount)->create();
    }

    public function test_can_format_length_aware_pagination()
    {
        $users = $this->userRepository->all(PaginationType::LENGTH_AWARE);
        $results = $this->paginationHelper->formatLengthAwarePagination($users);
        $expectedFields = [
            'total', 'current_page', 'last_page',
            'first_page_url', 'next_page_url', 'prev_page_url',
            'last_page_url', 'from', 'to', 'per_page', 'path'
        ];
        $this->assertTrue(Arr::has($results['pagination'], $expectedFields));
        $this->assertEquals($this->usersCount, $results['pagination']['total']);
    }

    public function test_can_format_simple_pagination()
    {
        $users = $this->userRepository->all(PaginationType::SIMPLE);
        $results = $this->paginationHelper->formatSimplePagination($users);
        $expectedFields = ['first_page_url', 'next_page_url', 'prev_page_url', 'from', 'to', 'per_page', 'path'];
        $this->assertTrue(Arr::has($results['pagination'], $expectedFields));
    }

    public function test_can_format_cursor_pagination()
    {
        $users = $this->userRepository->all(PaginationType::CURSOR);
        $results = $this->paginationHelper->formatCursorPagination($users);
        $expectedFields = ['next_cursor', 'prev_cursor', 'prev_page_url', 'next_page_url', 'per_page', 'path'];
        $this->assertTrue(Arr::has($results['pagination'], $expectedFields));
    }
}
