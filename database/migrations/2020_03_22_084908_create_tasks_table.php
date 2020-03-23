<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); 
            $table->integer('user_id')->unsigned() ;
            $table->integer('admin_id')->unsigned();
            $table->string('task_title');
            $table->text('description');
            $table->boolean('status') ;
            $table->integer('priority')->default(0) ;
            $table->boolean('completed')->default(0) ;          
            $table->timestamps();
            $table->dateTime('duedate')->nullable();  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
