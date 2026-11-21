<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateUser;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('users:create {--name= : Name} {--email= : Email address} {--password= : Password (visible in the process list, prefer the prompt on shared machines)}')]
#[Description('Create a user with a verified email, e.g. the first administrator of an environment')]
final class CreateUserCommand extends Command {
    public function handle(): int {
        $name_option = $this->option('name');
        $email_option = $this->option('email');
        $password_option = $this->option('password');

        $name = \is_string($name_option) ? $name_option : text('Name', required: true);
        $email = \is_string($email_option) ? $email_option : text('Email', required: true, validate: ['email' => 'required|email']);

        if (User::query()->whereRaw('lower(email) = ?', [mb_strtolower($email)])->exists()) {
            $this->components->error("A user with the email {$email} already exists.");

            return self::FAILURE;
        }

        $password = \is_string($password_option) ? $password_option : password('Password', required: true, validate: ['password' => ['required', Password::default()]]);

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            ['name' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', Password::default()]],
        );

        if ($validator->fails()) {
            $this->components->error($validator->errors()->first());

            return self::FAILURE;
        }

        $user = CreateUser::run($name, $email, $password);

        $this->components->info("User {$user->email} created.");

        return self::SUCCESS;
    }
}
