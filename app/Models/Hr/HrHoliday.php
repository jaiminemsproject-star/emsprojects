<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HrHoliday extends Model
{
    protected $table = 'hr_holidays';

    protected $fillable = [
        'company_id',
        'hr_holiday_calendar_id',
        'name',
        'holiday_date',        // This is the correct column name
        'holiday_type',
        'is_optional',
        'is_restricted',
        'applicable_religions',
        'applicable_genders',
        'applicable_locations',
        'description',
        'is_active',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_optional' => 'boolean',
        'is_restricted' => 'boolean',
        'is_active' => 'boolean',
        'applicable_religions' => 'array',
        'applicable_genders' => 'array',
        'applicable_locations' => 'array',
    ];

    // ==================== RELATIONSHIPS ====================

    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(HrHolidayCalendar::class, 'hr_holiday_calendar_id');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForYear($query, $year)
    {
        return $query->whereYear('holiday_date', $year);
    }

    public function scopeForMonth($query, $month, $year = null)
    {
        $query->whereMonth('holiday_date', $month);
        if ($year) {
            $query->whereYear('holiday_date', $year);
        }
        return $query;
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('holiday_date', [$startDate, $endDate]);
    }

    public function scopePublic($query)
    {
        return $query->where('is_optional', false)->where('is_restricted', false);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_optional', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('holiday_type', $type);
    }

    // ==================== ACCESSORS ====================

    public function getDayNameAttribute(): string
    {
        return $this->holiday_date ? $this->holiday_date->format('l') : '-';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->holiday_date ? $this->holiday_date->format('d M Y') : '-';
    }

    public function getShortDateAttribute(): string
    {
        return $this->holiday_date ? $this->holiday_date->format('d M') : '-';
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->holiday_type) {
            'national' => 'danger',
            'state' => 'warning',
            'religious' => 'info',
            'company' => 'primary',
            'optional' => 'secondary',
            default => 'dark',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->holiday_type) {
            'national' => 'National Holiday',
            'state' => 'State Holiday',
            'religious' => 'Religious Holiday',
            'company' => 'Company Holiday',
            'optional' => 'Optional Holiday',
            default => ucfirst($this->holiday_type ?? '-'),
        };
    }

    // ==================== METHODS ====================

    /**
     * Check if holiday is applicable for an employee
     */
    public function isApplicableFor(HrEmployee $employee): bool
    {
        // If holiday is restricted, check applicability
        if ($this->is_restricted) {
            // Check religion
            if ($this->applicable_religions && count($this->applicable_religions) > 0) {
                if (!in_array($employee->religion, $this->applicable_religions)) {
                    return false;
                }
            }

            // Check gender
            if ($this->applicable_genders && count($this->applicable_genders) > 0) {
                if (!in_array($employee->gender, $this->applicable_genders)) {
                    return false;
                }
            }

            // Check location
            if ($this->applicable_locations && count($this->applicable_locations) > 0) {
                if (!in_array($employee->hr_work_location_id, $this->applicable_locations)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if a date is a holiday
     */
    public static function isHoliday(Carbon $date, ?int $calendarId = null): bool
    {
        $query = static::where('holiday_date', $date->format('Y-m-d'))
            ->where('is_active', true)
            ->where('is_optional', false);

        if ($calendarId) {
            $query->where('hr_holiday_calendar_id', $calendarId);
        }

        return $query->exists();
    }

    /**
     * Get holidays between dates
     */
    public static function getHolidaysBetween(Carbon $startDate, Carbon $endDate, ?int $calendarId = null): \Illuminate\Support\Collection
    {
        $query = static::whereBetween('holiday_date', [$startDate, $endDate])
            ->where('is_active', true)
            ->orderBy('holiday_date');

        if ($calendarId) {
            $query->where('hr_holiday_calendar_id', $calendarId);
        }

        return $query->get();
    }

    /**
     * Count holidays in date range
     */
    public static function countHolidaysBetween(Carbon $startDate, Carbon $endDate, ?int $calendarId = null): int
    {
        $query = static::whereBetween('holiday_date', [$startDate, $endDate])
            ->where('is_active', true)
            ->where('is_optional', false);

        if ($calendarId) {
            $query->where('hr_holiday_calendar_id', $calendarId);
        }

        return $query->count();
    }

    /**
     * Get holiday type options
     */
    public static function getHolidayTypeOptions(): array
    {
        return [
            'national' => 'National Holiday',
            'state' => 'State Holiday',
            'religious' => 'Religious Holiday',
            'company' => 'Company Holiday',
            'optional' => 'Optional Holiday',
        ];
    }
}
