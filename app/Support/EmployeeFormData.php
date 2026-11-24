<?php

namespace App\Support;

use App\Models\Employee;
use App\Models\User;

class EmployeeFormData
{
    /**
     * Get predefined blood groups.
     *
     * @return array<int, string>
     */
    public static function bloodGroups(): array
    {
        return [
            'A+',
            'A-',
            'B+',
            'B-',
            'AB+',
            'AB-',
            'O+',
            'O-',
            'Other',
        ];
    }

    /**
     * Get common work locations.
     *
     * @return array<int, string>
     */
    public static function workLocations(): array
    {
        return [
            'Head Office',
            'Branch Office',
            'Remote / Work From Home',
            'Hybrid',
            'On-Site / Client Location',
            'Regional Office',
        ];
    }

    /**
     * Get common branch offices.
     *
     * @return array<int, string>
     */
    public static function branches(): array
    {
        return [
            'Ahmedabad (Head Office)',
            'Surat Branch',
            'Vadodara Branch',
            'Rajkot Branch',
            'Mumbai Branch',
            'Pune Branch',
            'Bangalore Branch',
            'Hyderabad Branch',
            'Delhi NCR Branch',
            'Kolkata Branch',
            'Chennai Branch',
            'Indore Branch',
            'Jaipur Branch',
        ];
    }

    /**
     * Get popular Indian public, private and multinational banks.
     *
     * @return array<int, string>
     */
    public static function banks(): array
    {
        return [
            'State Bank of India (SBI)',
            'HDFC Bank',
            'ICICI Bank',
            'Axis Bank',
            'Kotak Mahindra Bank',
            'Bank of Baroda',
            'Punjab National Bank (PNB)',
            'Canara Bank',
            'Union Bank of India',
            'IndusInd Bank',
            'IDFC First Bank',
            'Yes Bank',
            'Federal Bank',
            'Central Bank of India',
            'Indian Bank',
            'Bank of India',
            'Indian Overseas Bank',
            'UCO Bank',
            'Bank of Maharashtra',
            'Punjab & Sind Bank',
            'South Indian Bank',
            'RBL Bank',
            'Bandhan Bank',
            'IDBI Bank',
            'AU Small Finance Bank',
            'Equitas Small Finance Bank',
            'Standard Chartered Bank',
            'HSBC India',
            'Citibank',
        ];
    }

    /**
     * Get all 28 Indian States & 8 Union Territories.
     *
     * @return array<int, string>
     */
    public static function states(): array
    {
        return [
            'Andhra Pradesh',
            'Arunachal Pradesh',
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Manipur',
            'Meghalaya',
            'Mizoram',
            'Nagaland',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Sikkim',
            'Tamil Nadu',
            'Telangana',
            'Tripura',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal',
            'Andaman and Nicobar Islands',
            'Chandigarh',
            'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi (NCT)',
            'Jammu and Kashmir',
            'Ladakh',
            'Lakshadweep',
            'Puducherry',
        ];
    }

    /**
     * Get major Indian cities for autocomplete.
     *
     * @return array<int, string>
     */
    public static function cities(): array
    {
        return [
            'Ahmedabad',
            'Surat',
            'Vadodara',
            'Rajkot',
            'Bhavnagar',
            'Jamnagar',
            'Gandhinagar',
            'Junagadh',
            'Anand',
            'Navsari',
            'Morbi',
            'Bharuch',
            'Mumbai',
            'Pune',
            'Nagpur',
            'Thane',
            'Nashik',
            'Aurangabad',
            'Bengaluru',
            'Hyderabad',
            'Chennai',
            'New Delhi',
            'Noida',
            'Gurugram',
            'Faridabad',
            'Ghaziabad',
            'Kolkata',
            'Jaipur',
            'Indore',
            'Bhopal',
            'Lucknow',
            'Kanpur',
            'Chandigarh',
            'Patna',
            'Kochi',
            'Coimbatore',
            'Visakhapatnam',
        ];
    }

    /**
     * Get emergency relations.
     *
     * @return array<int, string>
     */
    public static function emergencyRelations(): array
    {
        return [
            'Father',
            'Mother',
            'Spouse',
            'Brother',
            'Sister',
            'Son',
            'Daughter',
            'Friend',
            'Guardian',
            'Relative',
            'Other',
        ];
    }

    /**
     * Get professional reference relation / associations.
     *
     * @return array<int, string>
     */
    public static function referenceRelations(): array
    {
        return [
            'Ex-Manager / Supervisor',
            'Colleague / Peer',
            'Team Lead / Project Manager',
            'HR Manager / Recruiter',
            'Department Head / Director',
            'Client / Business Partner',
            'Professor / Academic Mentor',
            'Other',
        ];
    }

    /**
     * Get nominee relations.
     *
     * @return array<int, string>
     */
    public static function nomineeRelations(): array
    {
        return [
            'Spouse',
            'Father',
            'Mother',
            'Son',
            'Daughter',
            'Brother',
            'Sister',
            'Guardian',
            'Other',
        ];
    }

    /**
     * Get family member relations.
     *
     * @return array<int, string>
     */
    public static function familyRelations(): array
    {
        return [
            'Father',
            'Mother',
            'Spouse',
            'Son',
            'Daughter',
            'Brother',
            'Sister',
            'Grandfather',
            'Grandmother',
            'Other',
        ];
    }

    /**
     * Get occupations.
     *
     * @return array<int, string>
     */
    public static function occupations(): array
    {
        return [
            'Homemaker',
            'Salaried / Employed',
            'Government Service / PSU',
            'Business / Self-Employed',
            'Professional (Doctor, CA, Lawyer, etc.)',
            'Retired / Pensioner',
            'Student',
            'Dependent / Minor',
            'Unemployed',
            'Other',
        ];
    }

    /**
     * Get laptop & PC models.
     *
     * @return array<int, string>
     */
    public static function laptopModels(): array
    {
        return [
            'Not Allocated / None',
            'Apple MacBook Pro 16"',
            'Apple MacBook Pro 14"',
            'Apple MacBook Air M3',
            'Apple MacBook Air M2',
            'Apple MacBook Air M1',
            'Dell Latitude 5420 / 5430',
            'Dell Latitude 3420 / 3520',
            'Dell XPS 13 / 15',
            'Dell Inspiron 15',
            'HP ProBook 440 / 450 G9/G10',
            'HP EliteBook 840 G8/G9',
            'Lenovo ThinkPad T14 / L14',
            'Lenovo ThinkPad E14 / E15',
            'Lenovo IdeaPad Slim 3 / 5',
            'Asus ExpertBook',
            'Commercial Desktop Workstation',
            'Custom Assembled PC',
        ];
    }

    /**
     * Get mobile & SIM options.
     *
     * @return array<int, string>
     */
    public static function simOptions(): array
    {
        return [
            'Not Allocated / None',
            'Company SIM (Jio Postpaid)',
            'Company SIM (Airtel Postpaid)',
            'Company SIM (Vodafone Idea Vi)',
            'Company Smartphone (Samsung) + SIM',
            'Company Smartphone (Apple iPhone) + SIM',
            'Company Smartphone (Redmi / OnePlus) + SIM',
            'CUG SIM Only',
            'BYOD / Personal Mobile Allowed',
            'Other',
        ];
    }

    /**
     * Get reporting persons (existing active employees + management presets).
     *
     * @return array<int, array{value: string, label: string, sub?: string}>
     */
    public static function reportingPersons(?int $excludeEmployeeId = null): array
    {
        $query = Employee::where('status', 'Active');
        if ($excludeEmployeeId) {
            $query->where('id', '!=', $excludeEmployeeId);
        }

        $employees = $query->orderBy('employee_name')->get();
        $list = [];

        // Management presets
        $list[] = [
            'value' => 'CEO / Managing Director',
            'label' => 'CEO / Managing Director',
            'sub' => 'Executive Management',
        ];
        $list[] = [
            'value' => 'Founder / Director',
            'label' => 'Founder / Director',
            'sub' => 'Board of Directors',
        ];
        $list[] = [
            'value' => 'HR Head / Director',
            'label' => 'HR Head / Director',
            'sub' => 'HR Management',
        ];

        foreach ($employees as $emp) {
            $subParts = array_filter([$emp->designation, $emp->department, $emp->employee_code]);
            $list[] = [
                'value' => $emp->employee_name,
                'label' => $emp->employee_name,
                'sub' => implode(' • ', $subParts),
            ];
        }

        return $list;
    }

    /**
     * Get staff users & verifiers.
     *
     * @return array<int, array{value: string, label: string, sub?: string}>
     */
    public static function verifiers(): array
    {
        $users = User::orderBy('name')->get();
        $list = [];

        foreach ($users as $user) {
            $list[] = [
                'value' => $user->name,
                'label' => $user->name,
                'sub' => ucfirst((string) $user->role).' ('.$user->email.')',
            ];
        }

        return $list;
    }

    /**
     * Get all form data arrays packaged together.
     *
     * @return array<string, mixed>
     */
    public static function all(?int $excludeEmployeeId = null): array
    {
        return [
            'bloodGroups' => static::bloodGroups(),
            'workLocations' => static::workLocations(),
            'branches' => static::branches(),
            'banks' => static::banks(),
            'states' => static::states(),
            'cities' => static::cities(),
            'emergencyRelations' => static::emergencyRelations(),
            'referenceRelations' => static::referenceRelations(),
            'nomineeRelations' => static::nomineeRelations(),
            'familyRelations' => static::familyRelations(),
            'occupations' => static::occupations(),
            'laptopModels' => static::laptopModels(),
            'simOptions' => static::simOptions(),
            'reportingPersons' => static::reportingPersons($excludeEmployeeId),
            'verifiers' => static::verifiers(),
        ];
    }
}

