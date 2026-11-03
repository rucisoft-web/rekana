<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema Rekana V1: owner (tenant), rekanan bersama, M1 Penilaian Risiko, M2 Prakualifikasi,
 * M3 Seleksi, M4 Orientasi dan kartu akses, serta Permit to Work dasar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
            $table->boolean('is_platform_admin')->default(false)->after('is_active');
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });

        // ---- Owner (pemberi kerja) dan aksesnya -------------------------------------------------
        Schema::create('owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 20)->nullable();
            $table->string('industry')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('hse_email')->nullable();
            $table->boolean('accepts_registration')->default(true);
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30)->nullable();
            $table->string('name');
            $table->string('hazard_class', 20)->default('umum');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('role', 20);
            $table->string('position_title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['owner_id', 'user_id']);
        });

        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->string('code', 40);
            $table->unsignedSmallInteger('year');
            $table->unsignedInteger('last_number')->default(0);
            $table->unique(['owner_id', 'code', 'year']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 30);
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('label')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['owner_id', 'created_at']);
        });

        // ---- Rekanan (dipakai bersama antar-owner) ----------------------------------------------
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('nib', 30)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('business_field')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('director_name')->nullable();
            $table->string('hse_contact')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_demo')->default(false);
            $table->timestamps();
        });

        Schema::create('contractor_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20)->default('admin');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['contractor_id', 'user_id']);
        });

        Schema::create('contractor_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('vendor_code', 30)->nullable();
            $table->string('status', 20)->default('menunggu');
            $table->date('registered_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('blacklisted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('blacklisted_at')->nullable();
            $table->text('blacklist_reason')->nullable();
            $table->timestamps();
            $table->unique(['owner_id', 'contractor_id']);
        });

        Schema::create('contractor_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->string('title');
            $table->string('number')->nullable();
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('file_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('safety_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedBigInteger('manhours')->default(0);
            $table->unsignedInteger('fatalities')->default(0);
            $table->unsignedInteger('lti_cases')->default(0);
            $table->unsignedInteger('recordable_cases')->default(0);
            $table->unsignedInteger('lost_days')->default(0);
            $table->timestamps();
            $table->unique(['contractor_id', 'year']);
        });

        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nik', 20)->nullable();
            $table->string('position')->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('birth_date')->nullable();
            $table->date('medical_until')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('worker_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('number')->nullable();
            $table->string('issuer')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // ---- M2: template kuesioner prakualifikasi ----------------------------------------------
        Schema::create('questionnaire_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code', 40);
            $table->string('name');
            $table->string('standard')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('template_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_template_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10);
            $table->string('name');
            $table->unsignedSmallInteger('sort')->default(0);
        });

        Schema::create('template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_element_id')->constrained()->cascadeOnDelete();
            $table->string('code', 12);
            $table->text('question');
            $table->text('guidance')->nullable();
            $table->string('evidence_hint')->nullable();
            $table->unsignedTinyInteger('weight')->default(1);
            $table->boolean('is_critical')->default(false);
            $table->string('min_risk', 10)->default('rendah');
            $table->unsignedSmallInteger('sort')->default(0);
        });

        // ---- M1: rencana pekerjaan dan penilaian risiko -----------------------------------------
        Schema::create('work_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requester_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('number', 30)->nullable();
            $table->string('title');
            $table->text('scope')->nullable();
            $table->date('planned_start')->nullable();
            $table->date('planned_end')->nullable();
            $table->decimal('contract_value', 16, 2)->nullable();
            $table->unsignedInteger('worker_estimate')->nullable();
            $table->boolean('subcontracting')->default(false);
            $table->string('contract_mode', 10)->nullable();
            $table->unsignedTinyInteger('risk_score')->nullable();
            $table->string('risk_category', 10)->nullable();
            $table->json('forced_by')->nullable();
            $table->string('status', 20)->default('draf');
            $table->foreignId('assessed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('risk_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_plan_id')->constrained()->cascadeOnDelete();
            $table->string('activity');
            $table->string('hazard', 30);
            $table->unsignedTinyInteger('likelihood');
            $table->unsignedTinyInteger('severity');
            $table->unsignedTinyInteger('score');
            $table->text('controls')->nullable();
            $table->timestamps();
        });

        // ---- M2: prakualifikasi dan sertifikat CSMS ---------------------------------------------
        Schema::create('prequalifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_template_id')->constrained()->restrictOnDelete();
            $table->string('number', 30)->nullable();
            $table->string('target_risk', 10)->default('menengah');
            $table->string('status', 20)->default('draf');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('result_risk', 10)->nullable();
            $table->boolean('critical_failed')->default(false);
            $table->date('site_visit_date')->nullable();
            $table->text('site_visit_notes')->nullable();
            $table->text('clarification_request')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pq_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prequalification_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_item_id')->constrained()->cascadeOnDelete();
            $table->text('answer')->nullable();
            $table->foreignId('contractor_document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('evidence_path')->nullable();
            $table->unsignedTinyInteger('self_score')->nullable();
            $table->unsignedTinyInteger('score')->nullable();
            $table->text('evaluator_note')->nullable();
            $table->timestamps();
            $table->unique(['prequalification_id', 'template_item_id']);
        });

        Schema::create('csms_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prequalification_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 40);
            $table->string('max_risk', 10);
            $table->decimal('score', 5, 2)->nullable();
            $table->date('issued_at');
            $table->date('valid_until');
            $table->timestamp('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->uuid('verify_code')->unique();
            $table->timestamps();
        });

        // ---- M3: seleksi ------------------------------------------------------------------------
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_plan_id')->constrained()->cascadeOnDelete();
            $table->string('number', 30)->nullable();
            $table->string('title');
            $table->string('status', 20)->default('draf');
            $table->timestamp('closes_at')->nullable();
            $table->unsignedTinyInteger('hse_pass_score')->default(60);
            $table->unsignedTinyInteger('hse_weight')->default(30);
            $table->timestamps();
        });

        Schema::create('tender_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('diundang');
            $table->string('hse_plan_path')->nullable();
            $table->text('hse_plan_summary')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->json('hse_scores')->nullable();
            $table->decimal('hse_score', 5, 2)->nullable();
            $table->boolean('hse_passed')->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_winner')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tender_id', 'contractor_id']);
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 40);
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('value', 16, 2)->nullable();
            $table->string('status', 20)->default('mobilisasi');
            $table->foreignId('hse_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('kickoff_at')->nullable();
            $table->text('kickoff_notes')->nullable();
            $table->text('bridging_document')->nullable();
            $table->timestamp('start_approved_at')->nullable();
            $table->foreignId('start_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // ---- M4: orientasi, pelatihan, kartu akses ----------------------------------------------
        Schema::create('inductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->date('held_at');
            $table->string('trainer')->nullable();
            $table->json('topics')->nullable();
            $table->unsignedTinyInteger('passing_score')->default(70);
            $table->unsignedTinyInteger('valid_months')->default(12);
            $table->timestamps();
        });

        Schema::create('induction_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('induction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('pre_score')->nullable();
            $table->unsignedTinyInteger('post_score')->nullable();
            $table->boolean('passed')->default(false);
            $table->date('valid_until')->nullable();
            $table->timestamps();
            $table->unique(['induction_id', 'worker_id']);
        });

        Schema::create('access_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->uuid('code')->unique();
            $table->date('issued_at');
            $table->date('valid_until');
            $table->string('status', 20)->default('aktif');
            $table->string('blocked_reason')->nullable();
            $table->timestamps();
        });

        // ---- Permit to Work dasar ---------------------------------------------------------------
        Schema::create('work_permits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number', 30)->nullable();
            $table->string('type', 20);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location_detail')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status', 20)->default('draf');
            $table->json('checklist')->nullable();
            $table->json('gas_test')->nullable();
            $table->text('isolation_points')->nullable();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('close_notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('permit_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_permit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('worker_id')->constrained()->cascadeOnDelete();
            $table->unique(['work_permit_id', 'worker_id']);
        });
    }

    public function down(): void
    {
        foreach ([
            'permit_workers', 'work_permits', 'access_cards', 'induction_attendees', 'inductions', 'contracts', 'tender_bids', 'tenders',
            'csms_certificates', 'pq_answers', 'prequalifications', 'risk_lines', 'work_plans', 'template_items', 'template_elements',
            'questionnaire_templates', 'worker_competencies', 'workers', 'safety_statistics', 'contractor_documents',
            'contractor_registrations', 'contractor_users', 'contractors', 'audit_logs', 'sequences', 'memberships', 'areas', 'owners',
        ] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['is_active', 'is_platform_admin', 'app_authentication_secret', 'app_authentication_recovery_codes', 'last_login_at']));
    }
};
