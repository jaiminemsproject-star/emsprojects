<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HrLeavePolicy extends Model
{
    protected $table = 'hr_leave_policies';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'description',
        'effective_from',
        'effective_to',
        'applicable_employee_types',
        'applicable_grades',
        'applicable_departments',
        'applicable_designations',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'applicable_employee_types' => 'array',
        'applicable_grades' => 'array',
        'applicable_departments' => 'array',
        'applicable_designations' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(HrLeavePolicyDetail::class, 'hr_leave_policy_id');
    }

    public function leaveTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            HrLeaveType::class,
            'hr_leave_policy_details',
            'hr_leave_policy_id',
            'hr_leave_type_id'
        )->withPivot([
            'days_allowed',
            'carry_forward_allowed',
            'max_carry_forward_days',
            'encashment_allowed',
            'max_encashment_days',
            'is_active',
        ])->withTimestamps();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(HrEmployee::class, 'hr_leave_policy_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeEffective($query, $date = null)
    {
        $date = $date ?? now();
        return $query->where('effective_from', '<=', $date)
                     ->where(function ($q) use ($date) {
                         $q->whereNull('effective_to')
                           ->orWhere('effective_to', '>=', $date);
                     });
    }

    // ==================== ACCESSORS ====================

    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $now = now();
        if ($this->effective_from > $now) {
            return 'Upcoming';
        }

        if ($this->effective_to && $this->effective_to < $now) {
            return 'Expired';
        }

        return 'Active';
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Active' => 'success',
            'Inactive' => 'secondary',
            'Upcoming' => 'info',
            'Expired' => 'warning',
            default => 'secondary',
        };
    }

    // ==================== METHODS ====================

    /**
     * Check if policy is applicable for an employee
     */
    public function isApplicableFor(HrEmployee $employee): bool
    {
        // Check employee type
        if ($this->applicable_employee_types && count($this->applicable_employee_types) > 0) {
            if (!in_array($employee->employee_type, $this->applicable_employee_types)) {
                return false;
            }
        }

        // Check grade
        if ($this->applicable_grades && count($this->applicable_grades) > 0) {
            if (!in_array($employee->hr_grade_id, $this->applicable_grades)) {
                return false;
            }
        }

        // Check department
        if ($this->applicable_departments && count($this->applicable_departments) > 0) {
            if (!in_array($employee->department_id, $this->applicable_departments)) {
                return false;
            }
        }

        // Check designation
        if ($this->applicable_designations && count($this->applicable_designations) > 0) {
            if (!in_array($employee->hr_designation_id, $this->applicable_designations)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get leave entitlement for a leave type
     */
    public function getEntitlementFor(int $leaveTypeId): ?object
    {
        return $this->details()->where('hr_leave_type_id', $leaveTypeId)->first();
    }

    /**
     * Get default policy
     */
    public static function getDefault(): ?self
    {
        return static::where('is_default', true)
                     ->where('is_active', true)
                     ->first();
    }
}
