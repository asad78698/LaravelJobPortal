<?php

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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('category_id')->constrained('category')->onDelete('cascade');
            $table->foreignId('jobtypes_id')->constrained('jobtypes')->onDelete('cascade');
            $table->integer('vacancies');
            $table->string('salary')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->text('benefits')->nullable();
            $table->text('responsibilies')->nullable();
            $table->text('qualification')->nullable();
            $table->text('keywords')->nullable();
            $table->string('experience')->nullable();
            $table->string('companyname')->nullable();
            $table->string('companylocation')->nullable();
            $table->string('companywebsite')->nullable();
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
        Schema::dropIfExists('jobs');
    }
};
