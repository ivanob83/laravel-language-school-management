<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageClass extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'title',
        'description',
        'professor_id',
        'student_id',
        'schedule_time',
        'status', // scheduled / completed
    ];

    /**
     * Casts
     */
    protected $casts = [
        'schedule_time' => 'datetime',
    ];

    /**
     * The professor teaching this class
     */
    public function professor() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    /**
     * Students enrolled in this class (via pivot table)
     */
    public function students() : \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'language_class_assignments',
            'language_class_id',
            'student_id'
        )->withPivot('status')->withTimestamps();
    }

    /**
     * Pivot records (assignments) for this class
     */
    public function assignments() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LanguageClassAssignment::class, 'language_class_id');
    }
}
