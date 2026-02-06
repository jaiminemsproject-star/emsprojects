<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrAttendancePunch extends Model
{
    protected $table = 'hr_attendance_punches';

    protected $fillable = [
        'hr_employee_id',
        'punch_time',
        'punch_type',
        'device_id',
        'device_name',
        'location',
        'latitude',
        'longitude',
        'is_valid',
        'is_manual',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
        'is_valid' => 'boolean',
        'is_manual' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // ==================== RELATIONSHIPS ====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrEmployee::class, 'hr_employee_id');
    }

    // ==================== SCOPES ====================

    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('punch_time', $date);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('hr_employee_id', $employeeId);
    }

    public function scopeInTime($query)
    {
        return $query->where('punch_type', 'in');
    }

    public function scopeOutTime($query)
    {
        return $query->where('punch_type', 'out');
    }

    // ==================== ACCESSORS ====================

    public function getFormattedTimeAttribute(): string
    {
        return $this->punch_time ? $this->punch_time->format('h:i A') : '-';
    }

    public function getPunchTypeLabelAttribute(): string
    {
        return match($this->punch_type) {
            'in' => 'IN',
            'out' => 'OUT',
            'break_start' => 'Break Start',
            'break_end' => 'Break End',
            default => ucfirst($this->punch_type ?? '-'),
        };
    }

    public function getPunchTypeColorAttribute(): string
    {
        return match($this->punch_type) {
            'in' => 'success',
            'out' => 'danger',
            'break_start', 'break_end' => 'warning',
            default => 'secondary',
        };
    }

    // ==================== METHODS ====================

    public function markInvalid(string $reason = null): bool
    {
        $this->is_valid = false;
        if ($reason) {
            $this->remarks = $reason;
        }
        return $this->save();
    }

    public function markValid(): bool
    {
        $this->is_valid = true;
        return $this->save();
    }
}
