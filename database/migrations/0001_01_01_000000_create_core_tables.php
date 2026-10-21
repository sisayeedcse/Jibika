<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('worker'); // worker, community, contributor, vendor, admin
            $table->string('status')->default('active'); // active, suspended
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('occupation')->nullable();
            $table->integer('experience_years')->default(0);
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });

        Schema::create('community_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('full_name');
            $table->string('location');
            $table->timestamps();
        });

        Schema::create('vendor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('company_name');
            $table->string('supplier_type')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn');
            $table->decimal('estimated_cost_bdt', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('users');
            $table->foreignId('category_id')->nullable()->constrained('asset_categories');
            $table->enum('status', [
                'draft','submitted','under_review','needs_information','verified',
                'ai_assessed','funding_open','funded','procurement','procured',
                'delivered','monitoring','completed','rejected','withdrawn',
                'funding_cancelled','procurement_failed','delivery_disputed','suspended'
            ])->default('draft');
            $table->decimal('requested_amount_bdt', 10, 2);
            $table->text('reason')->nullable();
            $table->text('livelihood_justification')->nullable();
            $table->foreignId('verification_id')->nullable()->constrained('community_verifications');
            $table->foreignId('assessment_id')->nullable()->constrained('assessment_records');
            $table->foreignId('funding_pool_id')->nullable()->constrained('funding_pools');
            $table->foreignId('procurement_order_id')->nullable()->constrained('procurement_orders');
            $table->timestamps();
        });

        Schema::create('asset_request_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('asset_requests')->onDelete('cascade');
            $table->string('file_path');
            $table->string('mime_type');
            $table->integer('size_bytes');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('community_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('asset_requests');
            $table->foreignId('community_member_id')->constrained('users');
            $table->enum('status', ['pending','verified','flagged','rejected','needs_info'])->default('pending');
            $table->text('evidence_notes')->nullable();
            $table->text('decision_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('assessment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('asset_requests');
            $table->enum('suitability_category', ['HIGH','MEDIUM','LOW']);
            $table->decimal('numerical_score', 5, 2)->nullable();
            $table->json('explanation_json');
            $table->string('method_version');
            $table->boolean('is_ai_generated')->default(false);
            $table->string('provider_name')->nullable();
            $table->timestamps();
        });

        Schema::create('funding_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('asset_requests');
            $table->decimal('target_amount_bdt', 10, 2);
            $table->decimal('current_amount_bdt', 10, 2)->default(0);
            $table->enum('status', ['open','completed','cancelled'])->default('open');
            $table->boolean('is_demo')->default(true);
            $table->boolean('is_simulated_mode')->default(true);
            $table->timestamps();
        });

        Schema::create('contribution_or_pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pool_id')->constrained('funding_pools')->onDelete('cascade');
            $table->foreignId('contributor_id')->constrained('users');
            $table->decimal('amount_bdt', 10, 2);
            $table->boolean('is_confirmed_transaction')->default(false);
            $table->boolean('is_demo')->default(false);
            $table->timestamp('pledged_at')->useCurrent();
        });

        Schema::create('vendor_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_order_id')->nullable()->constrained('procurement_orders');
            $table->foreignId('vendor_id')->constrained('users');
            $table->decimal('quoted_amount_bdt', 10, 2);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending','selected','rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('procurement_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('asset_requests');
            $table->foreignId('vendor_id')->nullable()->constrained('users');
            $table->foreignId('quotation_id')->nullable()->constrained('vendor_quotations');
            $table->enum('status', ['pending','procured','failed'])->default('pending');
            $table->decimal('cost_bdt', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procurement_order_id')->constrained('procurement_orders');
            $table->enum('status', ['pending','delivered','disputed'])->default('pending');
            $table->string('evidence_image_path')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('monitoring_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('asset_requests');
            $table->string('metric_name');
            $table->string('metric_value');
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->string('title_bn');
            $table->text('body_en');
            $table->text('body_bn');
            $table->string('type');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('previous_state')->nullable();
            $table->json('new_state')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('monitoring_records');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('procurement_orders');
        Schema::dropIfExists('vendor_quotations');
        Schema::dropIfExists('contribution_or_pledges');
        Schema::dropIfExists('funding_pools');
        Schema::dropIfExists('assessment_records');
        Schema::dropIfExists('community_verifications');
        Schema::dropIfExists('asset_request_documents');
        Schema::dropIfExists('asset_requests');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('vendor_profiles');
        Schema::dropIfExists('community_profiles');
        Schema::dropIfExists('worker_profiles');
    }
};
