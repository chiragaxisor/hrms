<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'DEP-IT',
                'name' => 'Information Technology',
                'description' => 'Software development, infrastructure, and technical support.',
                'designations' => [
                    ['code' => 'DES-SE', 'title' => 'Software Engineer'],
                    ['code' => 'DES-SSE', 'title' => 'Senior Software Engineer'],
                    ['code' => 'DES-TL', 'title' => 'Tech Lead'],
                    ['code' => 'DES-QA', 'title' => 'QA Engineer'],
                    ['code' => 'DES-DEV', 'title' => 'DevOps Engineer'],
                    ['code' => 'DES-DR', 'title' => 'Dr ritesh Agrawal'],
                ],
            ],
            [
                'code' => 'DEP-HR',
                'name' => 'Human Resources',
                'description' => 'Talent acquisition, employee engagement, and statutory compliance.',
                'designations' => [
                    ['code' => 'DES-HRE', 'title' => 'HR Executive'],
                    ['code' => 'DES-HRM', 'title' => 'HR Manager'],
                    ['code' => 'DES-REC', 'title' => 'Talent Acquisition Specialist'],
                ],
            ],
            [
                'code' => 'DEP-FIN',
                'name' => 'Finance & Accounts',
                'description' => 'Accounting, taxation, payroll processing, and financial auditing.',
                'designations' => [
                    ['code' => 'DES-ACC', 'title' => 'Accountant'],
                    ['code' => 'DES-FINM', 'title' => 'Finance Manager'],
                    ['code' => 'DES-PAY', 'title' => 'Payroll Specialist'],
                ],
            ],
            [
                'code' => 'DEP-SALES',
                'name' => 'Sales & Marketing',
                'description' => 'Business development, client acquisition, branding, and marketing.',
                'designations' => [
                    ['code' => 'DES-BDE', 'title' => 'Business Development Executive'],
                    ['code' => 'DES-BDM', 'title' => 'Business Development Manager'],
                    ['code' => 'DES-MKT', 'title' => 'Digital Marketing Lead'],
                ],
            ],
            [
                'code' => 'DEP-OPS',
                'name' => 'Operations',
                'description' => 'Operational efficiency, logistics, and resource management.',
                'designations' => [
                    ['code' => 'DES-OPSE', 'title' => 'Operations Executive'],
                    ['code' => 'DES-OPSM', 'title' => 'Operations Manager'],
                ],
            ],
            [
                'code' => 'DEP-QA',
                'name' => 'Quality Assurance',
                'description' => 'Standards adherence, product validation, and inspection.',
                'designations' => [
                    ['code' => 'DES-QAL', 'title' => 'QA Lead'],
                    ['code' => 'DES-QAI', 'title' => 'Quality Inspector'],
                ],
            ],
        ];

        foreach ($data as $deptData) {
            $designations = $deptData['designations'];
            unset($deptData['designations']);

            $department = Department::firstOrCreate(
                ['code' => $deptData['code']],
                $deptData
            );

            foreach ($designations as $desig) {
                Designation::firstOrCreate(
                    ['code' => $desig['code']],
                    [
                        'department_id' => $department->id,
                        'title' => $desig['title'],
                        'status' => 'Active',
                    ]
                );
            }
        }
    }
}
