<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Hr\HrHoliday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HrHolidayCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // FIXED: Use 'holiday_date' instead of 'date'
        $holidays = HrHoliday::whereYear('holiday_date', $year)
                             ->orderBy('holiday_date')
                             ->get()
                             ->groupBy(function ($holiday) {
                                 return $holiday->holiday_date->format('F');
                             });

        $years = range(date('Y') - 1, date('Y') + 2);

        $stats = [
            'total' => HrHoliday::whereYear('holiday_date', $year)->count(),
            'mandatory' => HrHoliday::whereYear('holiday_date', $year)->where('is_optional', false)->count(),
            'optional' => HrHoliday::whereYear('holiday_date', $year)->where('is_optional', true)->count(),
        ];

        return view('hr.holidays.index', compact('holidays', 'year', 'years', 'stats'));
    }

    public function create()
    {
        return view('hr.holidays.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'holiday_date' => 'required|date',
            'holiday_type' => 'required|in:national,state,religious,company,optional',
            'description' => 'nullable|string|max:500',
            'is_optional' => 'boolean',
            'is_restricted' => 'boolean',
            'applicable_religions' => 'nullable|array',
            'applicable_genders' => 'nullable|array',
            'applicable_locations' => 'nullable|array',
        ]);

        $validated['is_optional'] = $request->boolean('is_optional', false);
        $validated['is_restricted'] = $request->boolean('is_restricted', false);
        $validated['is_active'] = true;

        HrHoliday::create($validated);

        $year = Carbon::parse($validated['holiday_date'])->year;

        return redirect()->route('hr.holiday-calendars.index', ['year' => $year])
                         ->with('success', 'Holiday created successfully.');
    }

    public function show(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        $holidays = HrHoliday::whereYear('holiday_date', $year)
                             ->orderBy('holiday_date')
                             ->get();

        return view('hr.holidays.show', compact('holidays', 'year'));
    }

    public function edit(HrHoliday $holidayCalendar)
    {
        return view('hr.holidays.form', ['holiday' => $holidayCalendar]);
    }

    public function update(Request $request, HrHoliday $holidayCalendar)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'holiday_date' => 'required|date',
            'holiday_type' => 'required|in:national,state,religious,company,optional',
            'description' => 'nullable|string|max:500',
            'is_optional' => 'boolean',
            'is_restricted' => 'boolean',
            'applicable_religions' => 'nullable|array',
            'applicable_genders' => 'nullable|array',
            'applicable_locations' => 'nullable|array',
        ]);

        $validated['is_optional'] = $request->boolean('is_optional', false);
        $validated['is_restricted'] = $request->boolean('is_restricted', false);

        $holidayCalendar->update($validated);

        $year = Carbon::parse($validated['holiday_date'])->year;

        return redirect()->route('hr.holiday-calendars.index', ['year' => $year])
                         ->with('success', 'Holiday updated successfully.');
    }

    public function destroy(HrHoliday $holidayCalendar)
    {
        $year = $holidayCalendar->holiday_date ? $holidayCalendar->holiday_date->year : date('Y');
        $holidayCalendar->delete();

        return redirect()->route('hr.holiday-calendars.index', ['year' => $year])
                         ->with('success', 'Holiday deleted successfully.');
    }

    public function copyToNextYear(Request $request)
    {
        $fromYear = $request->get('from_year', date('Y'));
        $toYear = $fromYear + 1;

        $holidays = HrHoliday::whereYear('holiday_date', $fromYear)->get();
        $copied = 0;

        foreach ($holidays as $holiday) {
            $newDate = Carbon::parse($holiday->holiday_date)->addYear();
            
            // Check if already exists
            $exists = HrHoliday::where('name', $holiday->name)
                               ->whereYear('holiday_date', $toYear)
                               ->exists();
            
            if (!$exists) {
                HrHoliday::create([
                    'company_id' => $holiday->company_id,
                    'hr_holiday_calendar_id' => $holiday->hr_holiday_calendar_id,
                    'name' => $holiday->name,
                    'holiday_date' => $newDate,
                    'holiday_type' => $holiday->holiday_type,
                    'is_optional' => $holiday->is_optional,
                    'is_restricted' => $holiday->is_restricted,
                    'description' => $holiday->description,
                    'applicable_religions' => $holiday->applicable_religions,
                    'applicable_genders' => $holiday->applicable_genders,
                    'applicable_locations' => $holiday->applicable_locations,
                    'is_active' => true,
                ]);
                $copied++;
            }
        }

        return redirect()->route('hr.holiday-calendars.index', ['year' => $toYear])
                         ->with('success', "{$copied} holidays copied to {$toYear}. Note: Variable date holidays (like Diwali, Holi) need manual adjustment.");
    }
}
