<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->default('#3b82f6');
            $table->timestamps();
        });

        Schema::create('assigned_email_tag', function (Blueprint $table) {
            $table->foreignId('assigned_email_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['assigned_email_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('assigned_email_tag');
        Schema::dropIfExists('tags');
    }
};