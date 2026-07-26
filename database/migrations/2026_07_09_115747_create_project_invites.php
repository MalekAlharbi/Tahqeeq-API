<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inviter_id')->constrained('users');
            $table->foreignId('project_id')->constrained('projects');
            $table->string('email');
            $table->string('token');
            $table->enum('status',['pending', 'accepted', 'expired'])->default('pending');
            $table->dateTime('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_invites');
    }
};
