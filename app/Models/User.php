<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'telefono',
        'avatar',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                // 1. Si es archivo subido (avatars/...)
                if ($this->avatar && (str_starts_with($this->avatar, 'avatars/') || str_starts_with($this->avatar, 'users/'))) {
                    return asset('storage/' . $this->avatar);
                }

                // 2. Si es preset local (avatar_X.png)
                // Checamos si empieza con 'avatar_'
                if ($this->avatar && str_starts_with($this->avatar, 'avatar_')) {
                    return asset('assets/avatars/' . $this->avatar);
                }

                // 3. Fallback (UI Avatars)
                $name = urlencode($this->name);
                return "https://ui-avatars.com/api/?name={$name}&background=ea580c&color=ffffff&size=128";
            }
        );
    }
}
