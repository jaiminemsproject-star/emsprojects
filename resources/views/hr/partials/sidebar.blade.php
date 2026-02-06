{{-- HR Module Sidebar Navigation --}}
{{-- Include this in your main sidebar: @include('hr.partials.sidebar') --}}

@canany(['hr.dashboard.view', 'hr.employee.view', 'hr.attendance.view', 'hr.leave.view', 'hr.payroll.view'])
<li class="nav-item">
    <a class="nav-link {{ request()->is('hr*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#hrMenu" role="button" aria-expanded="{{ request()->is('hr*') ? 'true' : 'false' }}">
        <i class="bi bi-people-fill me-2"></i>
        HR Management
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="collapse {{ request()->is('hr*') ? 'show' : '' }}" id="hrMenu">
        <ul class="nav flex-column ms-3">
            {{-- Dashboard --}}
            @can('hr.dashboard.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}" href="{{ route('hr.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            @endcan

            {{-- Employees --}}
            @can('hr.employee.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}" href="{{ route('hr.employees.index') }}">
                    <i class="bi bi-person-badge me-2"></i> Employees
                </a>
            </li>
            @endcan

            {{-- Attendance --}}
            @can('hr.attendance.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.attendance.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#attendanceMenu">
                    <i class="bi bi-calendar-check me-2"></i> Attendance
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('hr.attendance.*') ? 'show' : '' }}" id="attendanceMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.attendance.index') ? 'active' : '' }}" href="{{ route('hr.attendance.index') }}">
                                Daily View
                            </a>
                        </li>
                        @if(Route::has('hr.attendance.monthly'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.attendance.monthly') ? 'active' : '' }}" href="{{ route('hr.attendance.monthly') }}">
                                Monthly View
                            </a>
                        </li>
                        @endif
                        @can('hr.attendance.approve')
                        @if(Route::has('hr.attendance.ot-approval'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.attendance.ot-approval') ? 'active' : '' }}" href="{{ route('hr.attendance.ot-approval') }}">
                                OT Approval
                            </a>
                        </li>
                        @endif
                        @if(Route::has('hr.attendance.regularization'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.attendance.regularization') ? 'active' : '' }}" href="{{ route('hr.attendance.regularization') }}">
                                Regularization
                            </a>
                        </li>
                        @endif
                        @endcan
                        @if(Route::has('hr.attendance.report'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.attendance.report') ? 'active' : '' }}" href="{{ route('hr.attendance.report') }}">
                                Report
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan

            {{-- Leave --}}
            @can('hr.leave.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.leave.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#leaveMenu">
                    <i class="bi bi-calendar-x me-2"></i> Leave
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('hr.leave.*') ? 'show' : '' }}" id="leaveMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.leave.index') ? 'active' : '' }}" href="{{ route('hr.leave.index') }}">
                                Applications
                            </a>
                        </li>
                        @can('hr.leave.approve')
                        @if(Route::has('hr.leave.pending'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.leave.pending') ? 'active' : '' }}" href="{{ route('hr.leave.pending') }}">
                                Pending Approval
                                @php
                                    try {
                                        $pendingCount = \App\Models\Hr\HrLeaveApplication::where('status', 'pending')->count();
                                    } catch (\Exception $e) {
                                        $pendingCount = 0;
                                    }
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        @endif
                        @endcan
                        @if(Route::has('hr.leave.calendar'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.leave.calendar') ? 'active' : '' }}" href="{{ route('hr.leave.calendar') }}">
                                Calendar
                            </a>
                        </li>
                        @endif
                        @if(Route::has('hr.leave.balance-report'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.leave.balance-report') ? 'active' : '' }}" href="{{ route('hr.leave.balance-report') }}">
                                Balance Report
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan

            {{-- Payroll --}}
            @can('hr.payroll.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.payroll.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#payrollMenu">
                    <i class="bi bi-currency-rupee me-2"></i> Payroll
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('hr.payroll.*') ? 'show' : '' }}" id="payrollMenu">
                    <ul class="nav flex-column ms-3">
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.payroll.index') ? 'active' : '' }}" href="{{ route('hr.payroll.index') }}">
                                Payroll Periods
                            </a>
                        </li>
                        @if(Route::has('hr.payroll.salary-register'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.payroll.salary-register') }}">
                                Salary Register
                            </a>
                        </li>
                        @endif
                        @if(Route::has('hr.payroll.bank-statement'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.payroll.bank-statement') }}">
                                Bank Statement
                            </a>
                        </li>
                        @endif
                        @if(Route::has('hr.payroll.pf-report'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.payroll.pf-report') }}">
                                PF Report
                            </a>
                        </li>
                        @endif
                        @if(Route::has('hr.payroll.esi-report'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.payroll.esi-report') }}">
                                ESI Report
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan

            {{-- Loans & Advances --}}
            @can('hr.loan.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.loans.*') || request()->routeIs('hr.advances.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#loanMenu">
                    <i class="bi bi-cash-stack me-2"></i> Loans & Advances
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('hr.loans.*') || request()->routeIs('hr.advances.*') ? 'show' : '' }}" id="loanMenu">
                    <ul class="nav flex-column ms-3">
                        @if(Route::has('hr.loans.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.loans.*') ? 'active' : '' }}" href="{{ route('hr.loans.index') }}">
                                Employee Loans
                            </a>
                        </li>
                        @endif
                        @if(Route::has('hr.advances.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1 {{ request()->routeIs('hr.advances.*') ? 'active' : '' }}" href="{{ route('hr.advances.index') }}">
                                Salary Advances
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan

            {{-- Reports --}}
            @can('hr.report.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.reports.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#hrReportMenu">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Reports
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('hr.reports.*') ? 'show' : '' }}" id="hrReportMenu">
                    <ul class="nav flex-column ms-3">
                        @if(Route::has('hr.reports.headcount'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.reports.headcount') }}">Headcount</a>
                        </li>
                        @endif
                        @if(Route::has('hr.reports.attrition'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.reports.attrition') }}">Attrition</a>
                        </li>
                        @endif
                        @if(Route::has('hr.reports.birthday'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.reports.birthday') }}">Birthday List</a>
                        </li>
                        @endif
                        @if(Route::has('hr.reports.anniversary'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.reports.anniversary') }}">Work Anniversary</a>
                        </li>
                        @endif
                        @if(Route::has('hr.reports.probation-due'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.reports.probation-due') }}">Probation Due</a>
                        </li>
                        @endif
                        @if(Route::has('hr.reports.muster-roll'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.reports.muster-roll') }}">Muster Roll</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan

            {{-- HR Masters --}}
            @can('hr.settings.view')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('hr.masters.*') || request()->routeIs('hr.settings.*') ? '' : 'collapsed' }}" data-bs-toggle="collapse" href="#hrMasterMenu">
                    <i class="bi bi-gear me-2"></i> Masters & Settings
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('hr.masters.*') || request()->routeIs('hr.settings.*') ? 'show' : '' }}" id="hrMasterMenu">
                    <ul class="nav flex-column ms-3">
                        @if(Route::has('hr.masters.designations.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.designations.index') }}">Designations</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.grades.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.grades.index') }}">Grades</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.shifts.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.shifts.index') }}">Shifts</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.work-locations.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.work-locations.index') }}">Work Locations</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.leave-types.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.leave-types.index') }}">Leave Types</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.holiday-calendars.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.holiday-calendars.index') }}">Holiday Calendar</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.salary-components.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.salary-components.index') }}">Salary Components</a>
                        </li>
                        @endif
                        @if(Route::has('hr.masters.salary-structures.index'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.masters.salary-structures.index') }}">Salary Structures</a>
                        </li>
                        @endif
                        @if(Route::has('hr.settings.statutory-slabs'))
                        <li class="nav-item">
                            <a class="nav-link py-1" href="{{ route('hr.settings.statutory-slabs') }}">Statutory Slabs</a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endcan
        </ul>
    </div>
</li>
@endcanany
