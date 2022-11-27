<?php

namespace Tests\Unit\Helpers;

use App\Helpers\PaginationHelper;
use App\Interfaces\Resources\UserServiceInterface;
use App\Models\User;
use App\Services\Resources\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PaginationHelperTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private PaginationHelper $paginationHelper;
    private UserServiceInterface $userRepository;
    private int $usersCount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paginationHelper = new PaginationHelper();
        $this->userService = new UserService(new User());
    }

    public function test_can_format_length_aware_pagination()
    {
        $dummyCollection = User::query()->paginate()->toArray();
        $results = $this->paginationHelper->formatLengthAwarePagination($dummyCollection);
        $expectedFields = [
            'total', 'current_page', 'last_page',
            'first_page_url', 'next_page_url', 'prev_page_url',
            'last_page_url', 'from', 'to', 'per_page', 'path'
        ];
        $this->assertTrue(Arr::has($results['pagination'], $expectedFields));
    }

    public function test_can_format_simple_pagination()
    {
        $dummyCollection = User::query()->simplePaginate()->toArray();
        $results = $this->paginationHelper->formatSimplePagination($dummyCollection);
        $expectedFields = ['first_page_url', 'next_page_url', 'prev_page_url', 'from', 'to', 'per_page', 'path'];
        $this->assertTrue(Arr::has($results['pagination'], $expectedFields));
    }

    public function test_can_format_cursor_pagination()
    {
        $dummyCollection = User::query()->cursorPaginate()->toArray();
        $results = $this->paginationHelper->formatCursorPagination($dummyCollection);
        $expectedFields = ['next_cursor', 'prev_cursor', 'prev_page_url', 'next_page_url', 'per_page', 'path'];
        $this->assertTrue(Arr::has($results['pagination'], $expectedFields));
    }
}
