import os
from docx import Document

docx_path = "/Users/chiraglimbachiya/Sites/laravel/HRMS/HRMS_Feature_List.docx"
doc = Document()
doc.add_heading('HRMS (Human Resource Management System) - Detailed Feature List', 0)

features = [
    ("1. User Management & Authentication", [
        "Secure Login & Registration: Role-based access (Admin, Employee, HR, etc.).",
        "API Token Authentication: For mobile app integration (using Laravel Sanctum or Passport).",
        "Profile Management: Employees can update their personal details, contact info, and profile pictures."
    ]),
    ("2. Employee Management", [
        "Employee Onboarding: Add new employees with detailed personal and professional information.",
        "Employee Directory: View the list of all employees and their statuses.",
        "Status Tracking: Manage active/inactive employee statuses."
    ]),
    ("3. Department & Designation Management", [
        "Departments: Create, update, and manage various departments within the organization (e.g., IT, HR, Sales).",
        "Designations: Manage job titles and roles associated with departments."
    ]),
    ("4. Attendance Management", [
        "Check-In / Check-Out: Employees can mark their attendance with timestamps.",
        "Break Timings: Track employee breaks during the workday.",
        "Geofencing Support: Location-based attendance marking (ensures employees are at the office/worksite).",
        "Face Photo Verification: Capture photos during attendance for enhanced security and validation."
    ]),
    ("5. Leave Management", [
        "Leave Requests: Employees can apply for different types of leaves (Sick, Casual, Earned, etc.).",
        "Leave Approval Workflow: Admins or Managers can approve or reject leave requests.",
        "Leave Balance Tracking: Keep track of consumed and available leave quotas."
    ]),
    ("6. Daily Work Reports (DWR)", [
        "Task Submission: Employees can submit daily reports detailing their tasks and accomplishments.",
        "Custom Fields: Support for custom reporting formats or specific project-related fields.",
        "Review & Feedback: Managers can review and track daily productivity."
    ]),
    ("7. Payroll & Salary Management", [
        "Salary Structures: Define and manage base salary, allowances, deductions, and bonuses.",
        "Payroll Processing: Generate monthly payroll calculations based on attendance and leave records.",
        "Payslip Generation: Ability to view and download salary slips."
    ]),
    ("8. System Settings & Configuration", [
        "Global Settings: Configure company details, timezones, working hours, and email settings.",
        "Role & Permissions (Implicit): Manage system configurations and operational defaults."
    ]),
    ("9. Comprehensive API (For Mobile/Web Apps)", [
        "API Endpoints: Ready-to-use APIs for authentication, attendance, leave requests, and reports, making the system fully compatible with Android/iOS applications."
    ])
]

for section_title, items in features:
    doc.add_heading(section_title, level=1)
    for item in items:
        doc.add_paragraph(item, style='List Bullet')

doc.save(docx_path)
print(f"DOCX generated successfully at {docx_path}")
