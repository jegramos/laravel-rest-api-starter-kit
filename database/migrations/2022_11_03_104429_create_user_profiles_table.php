<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('first_name')->fulltext();
            $table->string('last_name')->fulltext();
            $table->string('middle_name')->nullable()->fulltext();
            $table->string('mobile_number')->nullable();
            $table->string('telephone_number')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->date('birthday')->nullable();
            // Address columns are created in a way that will enable us to work with multiple countries
            $table->string('address_line_1')->nullable(); // Building number, Building name
            $table->string('address_line_2')->nullable(); // Street, Road name
            $table->string('address_line_3')->nullable(); // Additional address info
            $table->string('district')->nullable(); // Barangay, etc.
            $table->string('city')->nullable(); // or Municipality
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
