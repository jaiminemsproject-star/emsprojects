<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrEmployeeQualification extends Model
{
    protected $table = 'hr_employee_qualifications';

    protected $fillable = [
        'hr_employee_id',
        'qualification_type',
        'qualification_name',
        'specialization',
        'institution',
        'board_university',
        'year_of_passing',
        'percentage',
        'grade',
        'document_path',
        'is_verified',
        'remarks',
    ];

    protected $casts = [
        'year_of_passing' => 'integer',
        'percentage' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'hr_employee_id');
    }

    // ==================== SCOPES ====================

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('qualification_type', $type);
    }

    // ==================== ACCESSORS ====================

    public function getQualificationTypeLabelAttribute(): string
    {
        return match($this->qualification_type) {
            'ssc' => 'SSC / 10th',
            'hsc' => 'HSC / 12th',
            'diploma' => 'Diploma',
            'graduation' => 'Graduation',
            'post_graduation' => 'Post Graduation',
            'doctorate' => 'Doctorate (PhD)',
            'professional' => 'Professional Certification',
            'other' => 'Other',
            default => ucfirst($this->qualification_type ?? '-'),
        };
    }

    public function getFullQualificationAttribute(): string
    {
        $parts = [$this->qualification_name];
        if ($this->specialization) {
            $parts[] = "({$this->specialization})";
        }
        return implode(' ', $parts);
    }
}
