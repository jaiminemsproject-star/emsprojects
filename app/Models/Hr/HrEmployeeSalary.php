<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class HrEmployeeSalary extends Model
{
    protected $table = 'hr_employee_salaries';

    protected $fillable = [
        'hr_employee_id',
        'hr_salary_structure_id',
        'effective_from',
        'effective_to',
        'basic',
        'hra',
        'da',
        'special_allowance',
        'conveyance',
        'medical',
        'other_allowances',
        'gross_salary',
        'pf_applicable',
        'pf_employee',
        'pf_employer',
        'esi_applicable',
        'esi_employee',
        'esi_employer',
        'pt_applicable',
        'professional_tax',
        'tds_applicable',
        'lwf_applicable',
        'total_deductions',
        'net_salary',
        'ctc',
        'is_current',
        'revision_reason',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'basic' => 'decimal:2',
        'hra' => 'decimal:2',
        'da' => 'decimal:2',
        'special_allowance' => 'decimal:2',
        'conveyance' => 'decimal:2',
        'medical' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'pf_employee' => 'decimal:2',
        'pf_employer' => 'decimal:2',
        'esi_employee' => 'decimal:2',
        'esi_employer' => 'decimal:2',
        'professional_tax' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'ctc' => 'decimal:2',
        'pf_applicable' => 'boolean',
        'esi_applicable' => 'boolean',
        'pt_applicable' => 'boolean',
        'tds_applicable' => 'boolean',
        'lwf_applicable' => 'boolean',
        'is_current' => 'boolean',
        'approved_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'hr_employee_id');
    }

    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(HrSalaryStructure::class, 'hr_salary_structure_id');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function components(): HasMany
    {
        return $this->hasMany(HrEmployeeSalaryComponent::class, 'hr_employee_salary_id');
    }

    // ==================== SCOPES ====================

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActiveOn($query, $date)
    {
        return $query->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $date);
            });
    }

    // ==================== ACCESSORS ====================

    public function getFormattedGrossAttribute(): string
    {
        return '₹' . number_format($this->gross_salary, 0);
    }

    public function getFormattedNetAttribute(): string
    {
        return '₹' . number_format($this->net_salary, 0);
    }

    public function getFormattedCtcAttribute(): string
    {
        return '₹' . number_format($this->ctc, 0);
    }

    public function getTotalEarningsAttribute(): float
    {
        return $this->basic + $this->hra + $this->da + $this->special_allowance + 
               $this->conveyance + $this->medical + $this->other_allowances;
    }

    public function getTotalEmployerContributionAttribute(): float
    {
        return $this->pf_employer + $this->esi_employer;
    }

    // ==================== METHODS ====================

    /**
     * Calculate daily salary
     */
    public function getDailySalary(int $workingDays = 26): float
    {
        return $this->gross_salary / $workingDays;
    }

    /**
     * Calculate hourly salary (assuming 8 hours/day)
     */
    public function getHourlySalary(int $workingDays = 26, int $hoursPerDay = 8): float
    {
        return $this->getDailySalary($workingDays) / $hoursPerDay;
    }

    /**
     * Mark as current and deactivate previous
     */
    public function markAsCurrent(): bool
    {
        // Deactivate all other salary records for this employee
        static::where('hr_employee_id', $this->hr_employee_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->is_current = true;
        return $this->save();
    }
}
