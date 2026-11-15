<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property-read string $initials
 * @property string|null $app_authentication_secret
 * @property list<string>|null $app_authentication_recovery_codes
 * @property bool $has_email_authentication
 */
#[Fillable([
    'name',
    'email',
    'password',
])]
#[Hidden([
    'password',
    'remember_token',
    'app_authentication_secret',
    'app_authentication_recovery_codes',
])]
final class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, HasEmailAuthentication {
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'has_email_authentication' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'app_authentication_secret' => 'encrypted',
            'app_authentication_recovery_codes' => 'encrypted:array',
            'has_email_authentication' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool {
        return $this->hasVerifiedEmail() && str_ends_with($this->email, '@simonecerruti.com');
    }

    public function getAppAuthenticationSecret(): ?string {
        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void {
        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string {
        return $this->email;
    }

    /**
     * @return list<string>|null
     */
    public function getAppAuthenticationRecoveryCodes(): ?array {
        return $this->app_authentication_recovery_codes;
    }

    /**
     * @param  list<string>|null  $codes
     */
    public function saveAppAuthenticationRecoveryCodes(?array $codes): void {
        $this->app_authentication_recovery_codes = $codes;
        $this->save();
    }

    public function hasEmailAuthentication(): bool {
        return $this->has_email_authentication;
    }

    public function toggleEmailAuthentication(bool $condition): void {
        $this->has_email_authentication = $condition;
        $this->save();
    }

    /** @return Attribute<string, never> */
    protected function initials(): Attribute {
        return Attribute::get(fn (): string => str($this->name)
            ->explode(' ')
            ->filter()
            ->map(fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)))
            ->take(2)
            ->implode(''));
    }
}
