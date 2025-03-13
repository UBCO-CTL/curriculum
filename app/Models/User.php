<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
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
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Role::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Course::class, 'course_users', 'user_id', 'course_id')->withPivot('permission');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Program::class, 'program_users', 'user_id', 'program_id')->withPivot('permission');
    }

    public function syllabi(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\syllabus\Syllabus::class, 'syllabi_users', 'user_id', 'syllabus_id')->withPivot('permission');
    }

    public function hasAnyRoles($roles)
    {
        if ($this->roles()->whereIn('role', $roles)->first()) {
            return true;
        }

        return false;
    }

    public function hasRole($role)
    {
        if ($this->roles()->where('role', $role)->first()) {
            return true;
        }

        return false;
    }
}
