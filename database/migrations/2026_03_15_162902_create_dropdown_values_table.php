// database/migrations/2024_01_01_000005_create_dropdown_values_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dropdown_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dropdown_list_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->string('label')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dropdown_values');
    }
};