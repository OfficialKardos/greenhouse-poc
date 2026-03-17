// database/migrations/2024_01_01_000008_create_job_entry_values_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_entry_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_entry_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_field_id')->constrained();
            $table->text('value');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['job_entry_id', 'job_field_id'], 'entry_field_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_entry_values');
    }
};