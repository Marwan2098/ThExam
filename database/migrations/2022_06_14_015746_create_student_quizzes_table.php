<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_quizzes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quizze_id')->references('id')->on('quizzes')->onDelete('cascade');

            $table->foreignId('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->integer('score');

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
        Schema::dropIfExists('student_quizzes');
    }
}
