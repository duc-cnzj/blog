<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('content')->nullable();
            $table->ipAddress('ip');
            $table->string('url');
            $table->string('method');
            $table->longText('response')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('visited_at')->nullable();
            $table->nullableMorphs('userable');
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
        Schema::dropIfExists('histories');
    }
}
