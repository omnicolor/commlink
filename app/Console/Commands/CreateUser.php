<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function event;
use function filter_var;

use const FILTER_VALIDATE_EMAIL;

/**
 * @codeCoverageIgnore
 */
class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:create-user';

    /**
     * The console command description.
     * @var ?string
     */
    protected $description = 'Create a new user, prompting for all information';

    public function handle(): int
    {
        $name = text(
            label: 'Enter the user\'s name',
            required: true,
        );
        $email = text(
            label: 'Enter the user\'s email address',
            required: true,
            validate: function (string $email): ?string {
                if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return 'That doesn\'t appear to be a valid email address';
                }
                return null;
            },
        );
        $password = password(
            label: 'Enter the user\'s password',
            required: true,
        );
        $roles = multiselect(
            hint: 'Selecting none creates a normal user',
            label: 'What role(s) should the user have?',
            options: Role::all()->pluck('name'),
        );

        try {
            $user = User::create([
                'email' => $email,
                'name' => $name,
                'password' => Hash::make($password),
            ]);
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
            return self::FAILURE;
        }
        $user->syncRoles($roles);
        event(new Registered($user));
        return self::SUCCESS;
    }
}
