# Axisor HRMS — Master Business Logic & Module Directory

> **Document Status**: Active Master Reference  
> **Repository**: `hrms`  
> **Framework**: Laravel 13 (PHP 8.3+) | Tailwind CSS | Alpine.js | MySQL  
> **Last Updated**: 2026-11-05  

---

## 1. Standard Operating Procedure (SOP) for System Changes

This document serves as the **Single Source of Truth (SSOT)** for all business rules, calculations, security constraints, and module relationships in the HRMS application.

Whenever a new feature, modification, bug fix, or database refactoring is requested, **all developers and AI agents must strictly follow this 3-step SOP**:

```
 ┌─────────────────────────────────────────────────────────────┐
 │ STEP 1: PRE-CHANGE IMPACT ANALYSIS                         │
 │ • Open this file (HRMS_MASTER_BUSINESS_LOGIC.md)            │
 │ • Check the target module's business logic & rules          │
 │ • Consult the Cross-Module Dependency Matrix                │
 │ • Identify all upstream & downstream affected modules       │
 └──────────────────────────────┬──────────────────────────────┘
                                │
                                ▼
 ┌─────────────────────────────────────────────────────────────┐
 │ STEP 2: CODE IMPLEMENTATION & VERIFICATION                 │
 │ • Implement modifications matching project conventions     │
 │ • Ensure database constraints & authorizations align        │
 │ • Run automated tests: `php artisan test --compact`        │
 │ • Format code: `vendor/bin/pint --format agent`             │
 └──────────────────────────────┬──────────────────────────────┘
                                │
                                ▼
 ┌─────────────────────────────────────────────────────────────┐
 │ STEP 3: MASTER NOTE SYNCHRONIZATION                         │
 │ • Update modified business logic in the corresponding module│
 │ • Update data schemas, formulas, or permissions if altered  │
 │ • Append entry to Section 12 (Change Log / Audit Trail)     │
 │ • Never leave business logic changes unrecorded             │
 └─────────────────────────────────────────────────────────────┘
```

### SOP Execution Checklist
- [ ] **Pre-check**: Did you read the target module's section in this document before writing code?
- [ ] **Cross-module review**: Which other modules consume or trigger this module's data?
- [ ] **Authorization check**: Does the change respect Superadmin vs HR Admin vs Standard Employee roles?
- [ ] **Mathematical & rule consistency**: Are pro-rata payroll calculations, geofence formulas, or attendance synchronization side-effects maintained?
- [ ] **Post-update documentation**: Is this MD file updated with the latest logic and logged in the Change Audit Trail?

---

## 2. Global Architecture & Access Control Hierarchy

### Role-Based Access Control (RBAC)
The system supports three user roles stored in `users.role`:

| Role | Database Value | Scope & Capabilities |
| :--- | :--- | :--- |
| **Super Admin** | `'superadmin'` | Full unrestricted system access. Exclusively allowed to manage DWR custom fields, edit/delete existing daily work reports, configure system settings, and manage master records. |
| **HR Administrator** | `'admin'` | Operational administrative access. Manages employees, departments, designations, attendances, leave approvals/rejections, payroll generation & payouts, and general system settings. |
| **Employee** | `'employee'` | Self-service access only. Can mark own attendance (camera + GPS), view own monthly attendance, submit leave requests, submit daily work reports, view own payslips, and edit non-restricted personal profile sections. |

#### Helper Methods & Middlewares
- `User::isSuperAdmin()`: Returns `true` only if `$user->role === 'superadmin'`.
- `User::isAdmin()`: Returns `true` if role is `'superadmin'` or `'admin'`.
- `EnsureSuperAdmin` middleware (`alias: superadmin`): Enforces `isSuperAdmin()`.
- `EnsureUserIsAdmin` middleware (`alias: admin`): Enforces `isAdmin()`.
- `Employee` model link: `User::hasOne(Employee::class)` and `Employee::belongsTo(User::class)`.

---

## 3. Comprehensive Cross-Module Dependency Matrix

| If You Modify... | It Directly Affects... | Why / Dependent Business Logic |
| :--- | :--- | :--- |
| **Employee Master** (`employees`) | • Users (`users`)<br>• Attendances (`attendances`)<br>• Payrolls (`payrolls`)<br>• Salary Structures (`salary_structures`)<br>• Daily Work Reports (`daily_work_reports`)<br>• Leave Requests (`leave_requests`) | • Creating employee with company email auto-creates `users` login account.<br>• Changing `employee_code` affects payroll code formatting (`PAY-YYYYMM-EMPCODE`).<br>• Inactivating an employee excludes them from batch attendance and automatic monthly payroll generation.<br>• Embedded salary structure updates annual CTC and gross. |
| **Attendance** (`attendances`) | • Payrolls (`payrolls`)<br>• Dashboard (`dashboard`)<br>• Mobile API (`api/attendance`) | • Attendance records (`Present`, `Half Day`, `Leave`, `Holiday`, `Week Off`, `Absent`) dictate `payable_days`, prorated basic/HRA/allowances, and unpaid leave deduction in payroll.<br>• Feeds live headcount, late arrival counts, and break counters on dashboard. |
| **Leave Requests** (`leave_requests`) | • Attendances (`attendances`)<br>• Payrolls (`payrolls`)<br>• Dashboard (`dashboard`) | • **Approving** a leave auto-inserts/updates `attendances` table with `'Leave'` or `'Half Day'` (4.50 hrs) across date range.<br>• **Rejecting/Deleting** an approved leave removes/cleans up the auto-created attendance records.<br>• Approved leaves become `paid_leaves` in payroll calculations. |
| **Salary Structure** (`salary_structures`) | • Payrolls (`payrolls`)<br>• Employee Profile (`employees`) | • Defines baseline `monthly_gross`, `basic_salary`, `hra`, `special_allowance`, `pf_deduction`, `esic_deduction`, `professional_tax`, `tds`.<br>• Directly drives monthly pro-rata earnings and statutory deduction totals in payroll runs. |
| **System Settings** (`settings`) | • Attendances (`attendances`)<br>• Payrolls (`payrolls`)<br>• Daily Work Reports (`daily_work_reports`)<br>• Dashboard (`dashboard`) | • Geofence coordinates (`office_latitude`, `office_longitude`, `geofence_radius_meters`) and camera punch toggle validate punch actions.<br>• Standard work hours (`09:30` start time) control `late_minutes` calculation.<br>• Default PT, PF %, and salary disbursement day affect payroll defaults.<br>• `daily_work_report_target_calls` controls DWR KPI achievement calculations. |
| **Departments & Designations** (`departments`, `designations`) | • Employees (`employees`)<br>• Daily Work Reports filter<br>• Payrolls filter | • Employees are linked by department name and designation title.<br>• Deletion constraint: cannot delete department/designation if active employees are assigned. |
| **Daily Work Reports** (`daily_work_reports`) | • Settings (`settings`)<br>• Dashboard (`dashboard`) | • Dynamic custom fields are defined in `settings.dwr_custom_fields`.<br>• System field label overrides and toggles are stored in `settings.dwr_system_field_overrides`.<br>• Daily call counts and social outreach sums feed dashboard daily productivity metrics. |

---

## 4. Module 1: Employee Management & 10-Step Master Data

### 4.1. Core Business Rules
1. **Employee Code Generation**:
   - Auto-suggested format: `EMP-YYYY-XXXX` where `XXXX` is padded from `Employee::max('id') + 1`. Must be unique in `employees.employee_code`.
2. **Automatic User Account Provisioning**:
   - If `company_email` is provided during store or update:
     - Automatically executes `User::firstOrCreate()` with `email = company_email`, `role = 'employee'`, `password = bcrypt('password')`, `email_verified_at = now()`.
     - Sets `employees.user_id = user.id`.
   - If `company_email` is updated, the linked `User` record's name and email are synchronized automatically.
3. **Embedded Salary Structure Creation**:
   - If `salary_structure.monthly_gross` is supplied in the employee form, `SalaryStructure::updateOrCreate()` automatically computes:
     - `ctc_annual = round(monthly_gross * 12, 2)`
     - `basic_salary = monthly_gross * 0.50` (50%)
     - `hra = monthly_gross * 0.25` (25%)
     - `special_allowance = monthly_gross * 0.25` (25%)
     - `pf_deduction = 1800.00`
     - `professional_tax = 200.00`
     - `effective_from = now()->toDateString()`
4. **Self-Service Restrictions (`/my-employee-data`)**:
   - Employees can view and edit their own personal master data across sections 2 through 10.
   - **Immutable Fields for Employees**: Employees **CANNOT** modify:
     - `employee_code`
     - `status`
     - `employment_type`
     - `department`
     - `designation`
   - Attempting to pass these fields via self-service update is filtered out by validation in `ProfileController::employeeUpdate()`.

### 4.2. The 10 Master Data Sections

```
Step 1: Job Details (Code, Dept, Designation, Reporting Person, Location, Branch, Email, Joining Date, Type, Status, Salary)
Step 2: Personal Details (DOB, Gender, Marital Status, Anniversary, Mobile, Personal Email, Aadhaar Mobile, Blood Group, PAN, Aadhaar)
Step 3: Statutory Details [JSON] (UAN, PF Number, ESIC Number, Gratuity Eligibility, Disability Status)
Step 4: Bank Details [JSON] (Bank Name, Account Holder, Account Number, IFSC, Branch, Account Type)
Step 5: Address Details [JSON] (Communication Address & Permanent Address with 'Same as' replication)
Step 6: Emergency & Reference Contacts [JSON] (Emergency Contact Name, Relation, Phone; Reference Name, Org, Phone)
Step 7: Nominee Details [JSON] (Nominee Name, Relationship, DOB, Allocation Share %, Address)
Step 8: Family Members [JSON] (Father, Mother, Spouse, Children details with dependency flags)
Step 9: Document Checklist [JSON] (PAN, Aadhaar, Resume, Photos, Marksheets, Relieving Letter, Salary Slips, Form 16)
Step 10: Asset / Device Allocation [JSON] (Laptop, Monitor, Keyboard, Mobile, SIM, Serial Numbers, Issue Date, Status)
```

---

## 5. Module 2: Attendance, Geofencing & Biometrics

### 5.1. Punch Lifecycle & Status Transitions
- Daily punch states:
  - `clock_in`: Initiates attendance. Sets status to `'Present'` (if not set). Records time, IP address, GPS latitude/longitude, and captured face photo.
  - `break_in`: Marks start of break. Captures break-in face photo and timestamp.
  - `break_out`: Marks end of break. Computes incremental break duration: `break_in->diffInMinutes(break_out)`, adds to cumulative `break_minutes`, appends session entry to `breaks_history` JSON.
  - `clock_out`: Finalizes work day. Computes `total_hours = round(max(0, (clock_out - clock_in - break_minutes)) / 60, 2)`.

### 5.2. Geofence Verification (Haversine Formula)
- When `require_geofence` is active in `Setting`:
  - Earth Radius: `6,371,000` meters.
  - Target Coordinates: `Setting::get('office_latitude')` and `Setting::get('office_longitude')`.
  - Max Allowed Radius: `Setting::get('geofence_radius_meters')` (default 250 meters).
  - Validation: If user's distance in meters exceeds radius, punch is rejected with an error message indicating the exact distance outside boundary.

### 5.3. Camera Face Capture Verification
- When `require_camera_punch` is active in `Setting`:
  - Frontend captures webcam/camera frame as base64 Data URI (`data:image/jpeg;base64,...`).
  - Controller decodes base64 string and writes file to `storage/app/public/attendance_faces/{employee_id}/{timestamp}_{actionType}.jpg`.
  - Punch fails validation if photo is missing or unparseable.

### 5.4. Late Arrival & Shift Calculation
- Standard office start time: `09:30 AM` (configurable in settings).
- If `clock_in` time is after `09:30 AM`, `late_minutes = diffInMinutes('09:30', clock_in)`.

### 5.5. Admin Batch Operations
- Admin can batch-mark attendance for any date with mode:
  - `all`: All active employees.
  - `all_unmarked`: Active employees without an existing attendance record for that date.
  - `selected`: Explicit array of employee IDs.
- Defaults: If status is `'Present'`, default `clock_in = 09:30`, `clock_out = 18:30`, `total_hours = 9.00`.

---

## 6. Module 3: Leave Management & Attendance Synchronization

### 6.1. Leave Request Workflow
1. **Submission**:
   - Standard employee can only submit leaves for their own linked employee account.
   - If `is_half_day` is checked: `total_days = 0.5`, `end_date = start_date`, `half_day_type` can be `first_half` or `second_half`.
   - If full day: `total_days = startDate->diffInDays(endDate) + 1`.
   - Initial status: `'Pending'`.
2. **Approval (Crucial Sync)**:
   - Admin approves request with optional `admin_remarks`.
   - Controller loops through each date in `CarbonPeriod($leave->start_date, $leave->end_date)`:
     - Sets/Updates `Attendance` record with:
       - `status = $leave->is_half_day ? 'Half Day' : 'Leave'`
       - `total_hours = $leave->is_half_day ? 4.50 : 0.00`
       - `remarks = "Approved Leave: {$leave->leave_type} - {$leave->reason}"`
       - Resets clock times and break minutes to 0/null.
3. **Rejection & Deletion (Rollback Sync)**:
   - If an already `'Approved'` leave is rejected or deleted:
     - The system iterates over the date range and deletes any attendance records where `remarks` start with `'Approved Leave%'` and status is in `['Leave', 'Half Day']`.

---

## 7. Module 4: Daily Work Reports (DWR) & Dynamic Field Engine

### 7.1. Reporting Structure
- Tracks daily marketing, calling, and outreach activities per employee.
- **Default Fixed Columns**:
  - `calls`: Phone calls count.
  - `instagram_dms`, `facebook_dms`, `linkedin_dms`, `whatsapp_dms`.
  - `linkedin_posts`, `instagram_posts`.
  - `facebook_group_actions`, `facebook_group_dms`, `linkedin_connections`.
  - `remarks`: Daily notes or Sunday tags.
  - `custom_fields`: JSON storage for dynamic attributes.

### 7.2. Dynamic Custom Field Engine
- **Field Configuration**: Stored in `Setting::get('dwr_custom_fields')`.
- **System Field Overrides**: Stored in `Setting::get('dwr_system_field_overrides')` (label rename, active/hidden toggle).
- **Reserved Keys**: `calls`, `instagram_dms`, `facebook_dms`, `linkedin_dms`, `whatsapp_dms`, `linkedin_posts`, `instagram_posts`, `facebook_group_actions`, `facebook_group_dms`, `linkedin_connections`, `remarks`, `date`, `id`, `employee_id`, `report_date`. Collisions are rejected.
- **Superadmin Only**: Adding, modifying labels, toggling enabled state, or deleting custom fields is restricted to `superadmin` role only.

### 7.3. Total Social Outreach Formula
- Dynamic computation via `DailyWorkReport::getSocialOutreachActivitiesAttribute()`:
  $$\text{Social Activities} = \sum (\text{Enabled Social DMs} + \text{Enabled Posts} + \text{Enabled Group Actions} + \text{Custom Fields where } is\_social = true)$$

### 7.4. Strict Anti-Tamper Editing Rule
- Employees may create a new daily report for today/past date if no record exists.
- **Lockout Rule**: Once a report exists for an employee on a given date, **only Superadmin can edit or delete it**. Standard employees or regular admins attempting to edit an existing report receive `403 Forbidden`.

### 7.5. KPI & Productivity Metrics
- `total_working_days`: Month calendar days excluding Sundays.
- `avg_calls_per_day`: $\text{Total Calls} / \text{Logged Days Count}$.
- `call_achievement_rate`: $(\text{Total Calls} / (\text{Total Working Days} \times \text{target\_calls\_per\_day})) \times 100$ (capped at 100%).
- `consistency_rate`: $(\text{Logged Days Count} / \text{Total Working Days}) \times 100$.

---

## 8. Module 5: Payroll Processing, Compensation & Payslips

### 8.1. Salary Structure Specifications
- Configured per employee in `salary_structures`:
  - `ctc_annual`, `monthly_gross`, `basic_salary`, `hra`, `special_allowance`, `conveyance_allowance`, `medical_allowance`.
  - Statutory Deductions: `pf_deduction` (default ₹1,800), `esic_deduction`, `professional_tax` (default ₹200), `tds`.

### 8.2. Automated Monthly Payroll Generation
Admin generates payroll for month/year (all active employees or selected IDs):

1. **Working Days & Calendar Days**:
   - `total_month_days` = Total days in selected calendar month.
   - `total_sundays` = Count of Sundays in that month.
   - `working_days` = `total_month_days - total_sundays`.
2. **Attendance Factor Evaluation**:
   - Evaluates all `attendances` in that month:
     - `presentDays = presentCount + (halfDayCount * 0.5)`
     - `paidLeaves = leaveCount`
     - `holidaysWeekoffs = max(holidayCount, totalSundays)`
     - `payableDays = min(totalMonthDays, presentDays + paidLeaves + holidaysWeekoffs)`
     - `absentDays = max(0, totalMonthDays - payableDays)`
   - **Zero-Attendance Fallback**: If an employee has zero attendance records logged for the entire month, the system defaults `payableDays = totalMonthDays` to prevent unintended zero payouts.
3. **Pro-Rata Earnings Formula**:
   - Proration Ratio: $R = \frac{\text{payable\_days}}{\text{total\_month\_days}}$
   - $\text{earned\_basic} = \text{round}(\text{basic\_salary} \times R, 2)$
   - $\text{earned\_hra} = \text{round}(\text{hra} \times R, 2)$
   - $\text{earned\_allowances} = \text{round}((\text{special} + \text{conveyance} + \text{medical}) \times R, 2)$
   - $\text{total\_earnings} = \text{earned\_basic} + \text{earned\_hra} + \text{earned\_allowances}$
4. **Deductions & Net Pay**:
   - $\text{unpaid\_leave\_deduction} = \text{round}(\max(0, \text{monthly\_gross} - \text{total\_earnings}), 2)$
   - $\text{total\_deductions} = \text{round}(\text{pf} + \text{esic} + \text{prof\_tax} + \text{tds}, 2)$
   - $\text{net\_salary} = \max(0, \text{round}(\text{total\_earnings} - \text{total\_deductions}, 2))$
5. **Payroll Code**:
   - Formatted as: `PAY-{YYYY}{MM}-{EMPLOYEE_CODE}` (e.g. `PAY-202609-EMP-2026-0001`).

### 8.3. Payout Management & Payslips
- Payout statuses: `Pending` $\rightarrow$ `Processing` $\rightarrow$ `Paid`.
- Supported payment modes: `Bank Transfer`, `NEFT/RTGS`, `UPI`, `Cheque`, `Cash`.
- Payslip Route (`/payrolls/{payroll}/payslip`):
  - Authorization: Standard employees can only view their own payslip (`$payroll->employee_id === $user->employee->id`). Admins can view any payslip.

---

## 9. Module 6: Department & Designation Masters

### 9.1. Departments
- Fields: `code` (unique, auto-suggested `DEP-XXX`), `name`, `description`, `status` (`Active`/`Inactive`).
- Relationships: `hasMany(Designation::class)`, `hasMany(Employee::class, 'department', 'name')`.
- **Integrity Rule**: Deletion is blocked if any active employee is currently assigned to this department.

### 9.2. Designations
- Fields: `code` (unique, auto-suggested `DES-XXX`), `title`, `department_id`, `description`, `status` (`Active`/`Inactive`).
- Relationships: `belongsTo(Department::class)`, `hasMany(Employee::class, 'designation', 'title')`.
- **Integrity Rule**: Deletion is blocked if any active employee is currently assigned to this designation.

---

## 10. Module 7: System Settings & Configurations

Stored in the `settings` table using key-value pairs with automatic type casting (`boolean`, `integer`, `json`, `string`).

| Group | Key | Type | Default Value | Description |
| :--- | :--- | :--- | :--- | :--- |
| **company** | `company_name` | string | `HRMS Enterprise Inc.` | Company corporate name |
| **company** | `company_email` | string | `contact@hrms-enterprise.com` | Official contact email |
| **company** | `timezone` | string | `Asia/Kolkata` | Application timezone |
| **company** | `currency_symbol`| string | `₹` | Currency symbol |
| **company** | `date_format` | string | `d-M-Y` | System date presentation |
| **attendance**| `office_start_time` | string | `09:30` | Shift start benchmark |
| **attendance**| `office_end_time` | string | `18:30` | Shift end benchmark |
| **attendance**| `half_day_hours` | string | `4.5` | Minimum hours for half day |
| **attendance**| `full_day_hours` | string | `8.0` | Minimum hours for full day |
| **attendance**| `grace_period_minutes`| integer| `15` | Grace buffer for clock-in |
| **attendance**| `office_latitude` | string | `23.0225` | Geofence office latitude |
| **attendance**| `office_longitude`| string | `72.5714` | Geofence office longitude |
| **attendance**| `geofence_radius_meters`| integer | `250` | Allowed punch radius in meters |
| **attendance**| `require_geofence`| boolean| `1` | Enforce GPS distance check |
| **attendance**| `require_camera_punch`| boolean | `1` | Enforce selfie camera punch |
| **payroll** | `default_pt_amount` | string | `200` | Monthly professional tax |
| **payroll** | `pf_employee_percent`| string | `12` | PF employee contribution % |
| **payroll** | `salary_disbursement_day`| integer| `7` | Day of month for payout |
| **payroll** | `basic_salary_percentage`| string| `50` | Default basic % of gross |
| **payroll** | `hra_salary_percentage` | string | `25` | Default HRA % of gross |
| **notifications**| `daily_work_report_target_calls`| integer| `50` | Benchmark daily calls for DWR |

---

## 11. Module 8: Mobile REST API & Sanctum

- **Authentication**: Laravel Sanctum bearer tokens.
- **Endpoints**:
  - `POST /api/login`: Validates credentials, issues `employee-app` token, returns user ID, name, role, avatar, and designation.
  - `POST /api/logout`: Revokes the active token.
  - `GET /api/user`: Returns current user profile.
  - `GET /api/attendance/history`: Returns last 30 attendance records.
  - `POST /api/attendance/punch`: Supports mobile punches (`IN`, `OUT`, `BREAK_IN`, `BREAK_OUT`) with GPS coordinates and photo upload.

---

## 12. Change Log & Audit Trail

> **Notice**: Whenever any module or business logic is updated, append an entry to this table following the SOP.

| Date | Module Changed | Nature of Change & Business Logic Updated | Affected Modules | Author / Agent |
| :--- | :--- | :--- | :--- | :--- |
| **2026-11-05** | **All Modules** | Initial comprehensive baseline creation of Master Business Logic and SOP. | All modules documented & cross-referenced. | Antigravity AI |

