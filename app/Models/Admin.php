<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    protected $table = 'admins';
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'password',
        'is_main',
        'is_super_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password'
    ];
    // protected $appends = ['is_supper'];
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_roles', 'admin_id', 'role_id');
    }
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'admin_id', 'id');
    }

    public function getTeacherIfExists()
    {
        return $this->teacher()->first();
    }
}
