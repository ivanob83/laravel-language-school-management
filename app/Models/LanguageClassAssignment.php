<?php

namespace App\Models;

use App\Models\LanguageClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LanguageClassAssignment extends Model
{
    use HasFactory;

    protected $model = LanguageClassAssignment::class;

    /**
     * Table name
     */
    protected $table = 'language_class_assignments';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'language_class_id',
        'student_id',
        'status', // assigned, passed, failed
    ];

    /**
     * Relationships
     */

    // The class this assignment belongs to
    public function languageClass(): BelongsTo
    {
        return $this->belongsTo(LanguageClass::class, 'language_class_id');
    }

    // The student this assignment belongs to
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
