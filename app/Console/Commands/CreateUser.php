<?php

namespace App\Console\Commands;

use App\Http\Requests\UserRequest;
use App\Interfaces\HttpResources\UserServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CreateUser extends Command
{
    private UserServiceInterface $userService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user';

    public function __construct(UserServiceInterface $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $firstName = $this->ask('First Name');
        $lastName = $this->ask('Last Name');
        $email = $this->ask('Email');
        $username = $this->ask('Username');
        $password = $this->secret('Password');
        $passwordConfirmation = $this->secret('Confirm Password');
        $role = $this->choice('Role', $this->getAllRoles());
        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'roles' => [Role::findByName($role, 'sanctum')->id],
            'email_verified' => true,
        ];
        $createUserRules = (new UserRequest())->getStoreUserRules();

        try {
            Validator::validate($data, $createUserRules);
        } catch (ValidationException $e) {
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        $user = $this->userService->create($data);
        $this->info("User created: #$user->id | $user->username | $user->email | {$user->userProfile->full_name}");

        return Command::SUCCESS;
    }

    /**
     * Get all roles in the database
     *
     * @return array
     */
    public function getAllRoles(): array
    {
        $roles = [];
        Role::all()->each(function (Role $role) use (&$roles) {
            $roles[$role->id] = $role->name;
        });

        return $roles;
    }
}
