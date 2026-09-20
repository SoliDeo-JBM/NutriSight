<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbfp_parent_approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sbfp_participant_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('token_hash')->unique();
            $table->decimal('weight', 8, 2);
            $table->decimal('height', 8, 2);
            $table->decimal('bmi', 8, 2);
            $table->string('bmi_category');
            $table->string('status')->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->string('decision_reason')->nullable();
            $table->string('closed_reason')->nullable();
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sbfp_participant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbfp_parent_approval_requests');
    }
};
