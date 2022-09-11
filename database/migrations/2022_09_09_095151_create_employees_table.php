<?php

use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Country::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(State::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(City::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Department::class)->nullable()->constrained()->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('address');
            $table->char('zip_code');
            $table->date('birth_date');
            $table->date('hired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
