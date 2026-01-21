<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @use HasFactory<UserFactory>
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'full_name',
        'address',
        'city',
        'country',
        'phone_number',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token) : void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the language classes taught by the professor.
     * @return HasMany
     */
    public function taughtClasses() : HasMany
    {
        return $this->hasMany(LanguageClass::class, 'professor_id');
    }

    /**
     * Get the language classes the student is enrolled in.
     * @return BelongsToMany
     */
    public function enrolledClasses() : BelongsToMany
    {
        return $this->belongsToMany(
            LanguageClass::class,
            'language_class_assignments',
            'student_id',
            'language_class_id'
        )->withPivot('status')->withTimestamps();
    }

    public function isAdmin() : bool
    {
        return $this->role === 'admin';
    }

    public function isProfessor() : bool
    {
        return $this->role === 'professor';
    }

    public function isStudent() : bool
    {
        return $this->role === 'student';
    }
}
