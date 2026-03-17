// database/migrations/2024_01_01_000007_create_job_entries_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_entries', function (Blueprint $table) {
            $table->id();
            $table->string('job_number')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('greenhouse_id')->constrained();
            $table->foreignId('bay_id')->constrained();
            $table->foreignId('job_type_id')->constrained();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('submitted');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_entries');
    }
};