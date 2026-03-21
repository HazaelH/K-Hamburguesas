<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes; // <-- 1. IMPORTAR AQUÍ

class User extends Authenticatable implements MustVerifyEmail
{
    // <-- 2. AGREGAR SoftDeletes AQUÍ
    use HasFactory, Notifiable, SoftDeletes; 

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'telefono',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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
                if ($this->avatar && (str_starts_with($this->avatar, 'avatars/') || str_starts_with($this->avatar, 'users/'))) {
                    return asset('storage/' . $this->avatar);
                }
                if ($this->avatar && str_starts_with($this->avatar, 'avatar_')) {
                    return asset('assets/avatars/' . $this->avatar);
                }
                $name = urlencode($this->name);
                return "https://ui-avatars.com/api/?name={$name}&background=ea580c&color=ffffff&size=128";
            }
        );
    }

    public function getRolTraducidoAttribute()
    {
        // 1. Limpiamos el valor de la BD (quitamos espacios y forzamos minúsculas)
        $llaveRol = strtolower(trim($this->rol));

        // 2. Mapeamos las llaves a sus traducciones
        // VERIFICA: Si tu archivo es lang/en/admin/users.php, quita el "/users" repetido de aquí abajo
        $roles = [
            'cliente'    => __('admin/users/users.role_cliente'), 
            'mesero'     => __('admin/users/users.role_mesero'),
            'cajero'     => __('admin/users/users.role_cajero'),
            'cocinero'   => __('admin/users/users.role_cocinero'),
            'repartidor' => __('admin/users/users.role_repartidor'),
            'admin'      => __('admin/users/users.role_admin'),
        ];

        // 3. Buscamos la llave limpia. Si no existe, devolvemos el rol original con mayúscula inicial.
        return $roles[$llaveRol] ?? ucfirst($llaveRol);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
}  