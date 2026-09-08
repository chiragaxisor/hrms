<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // 1. Employee & Job Details
            $table->string('employee_name');
            $table->string('employee_code')->unique();
            $table->string('department');
            $table->string('designation');
            $table->string('reporting_person_name')->nullable();
            $table->string('work_location')->nullable();
            $table->string('branch')->nullable();
            $table->string('office_number')->nullable();
            $table->string('company_email')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('employment_type')->default('Full Time'); // Full Time, Part Time, Contract, Intern
            $table->string('status')->default('Active'); // Active, On Leave, Resigned, Terminated

            // 2. Personal Details
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // Male, Female, Other
            $table->string('marital_status')->nullable(); // Single, Married, Other
            $table->date('anniversary_date')->nullable();
            $table->string('personal_mobile')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('aadhaar_linked_mobile')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('aadhaar_number')->nullable();

            // 3. Statutory / Government Details (stored in JSON or columns)
            $table->json('statutory_details')->nullable(); // uan_number, esic_number, prev_pf_uan, prev_esic, docs_received, verified_by, verification_date

            // 4. Bank / Salary Account Details
            $table->json('bank_details')->nullable(); // bank_name, account_holder, account_number, ifsc_code, branch_name, account_type, cheque_received, verified_by

            // 5. Address Details
            $table->json('address_details')->nullable(); // communication address, permanent address, is_same

            // 6. Emergency & Reference Contact
            $table->json('emergency_contact')->nullable(); // name, mobile, relation, address
            $table->json('reference_contact')->nullable(); // name, mobile, relation, checked_by, check_date, remarks

            // 7. Nominee Details
            $table->json('nominee_details')->nullable(); // name, aadhaar_mobile, relation, dob, aadhaar, pan, occupation, bank (name, holder, acc_no, ifsc, branch, type), verified_by, verification_date

            // 8. Family Member Details
            $table->json('family_members')->nullable(); // array of family members: name, aadhaar_mobile, relation, dob, aadhaar, pan, occupation, checked_by

            // 9. Document Submission Checklist
            $table->json('document_checklist')->nullable(); // aadhaar, pan, bank_proof, photo, address_proof, pf_uan, esic, family_photo, other_details

            // 10. Asset / Device Details
            $table->json('asset_details')->nullable(); // laptop_pc, asset_id, serial_number, mobile_sim, office_phone, other_asset, handover_date, handover_by

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
