<?php

namespace App\Models;

<<<<<<< HEAD
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

=======
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
    protected $fillable = [
        'name',
        'email',
        'password',
<<<<<<< HEAD
        'rol',
        'telefono',
        'avatar',
        'is_active',
    ];

=======
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
    protected $hidden = [
        'password',
        'remember_token',
    ];

<<<<<<< HEAD
=======
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
<<<<<<< HEAD

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
=======
}
>>>>>>> 433b97d2585cb68ef73e2952cfbbe7259e4eeed7
