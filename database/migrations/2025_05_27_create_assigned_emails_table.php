<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assigned_emails', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('subject');
            $table->text('body_preview');
            $table->string('from_email');
            $table->string('from_name');
            $table->dateTime('received_at');
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('assigned_by')->nullable()->constrained('users');
            $table->enum('status', ['unassigned', 'assigned', 'in_progress', 'completed'])->default('unassigned');
            $table->text('notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('auto_assigned')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assigned_emails');
    }
};