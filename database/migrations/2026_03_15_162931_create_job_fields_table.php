// database/migrations/2024_01_01_000006_create_job_fields_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_type_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->string('field_name');
            $table->enum('field_type', [
                'text', 'textarea', 'number', 'dropdown', 'yes_no', 
                'temperature', 'photo', 'date', 'ppm', 'ph', 'ec',
                'time', 'datetime', 'signature'
            ]);
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->foreignId('dropdown_source_id')->nullable()->constrained('dropdown_lists');
            $table->json('validation_rules')->nullable();
            $table->json('settings')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_fields');
    }
};