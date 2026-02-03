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
         Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time')->nullable();
            $table->enum('type', ['online', 'offline'])->default('online');
            $table->string('location')->nullable(); // For offline events
            $table->string('meeting_link')->nullable(); // For online events
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('capacity')->nullable(); // Max participants
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Event creator (admin)
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->json('questions'); // Store survey questions as JSON
            $table->boolean('is_active')->default(true);
            $table->boolean('send_on_checkin')->default(true);
            $table->boolean('send_on_checkout')->default(false);
            $table->timestamps();
        });

        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('registration_number')->unique()->nullable();
            $table->boolean('checked_in')->default(false);
            $table->boolean('checked_out')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->boolean('survey_sent')->default(false);
            $table->boolean('survey_completed')->default(false);
            $table->boolean('certificate_sent')->default(false);
            $table->text('additional_info')->nullable(); // Custom fields
            $table->timestamps();
            
            // Ensure user can't register for same event twice
            $table->unique(['user_id', 'event_id']);
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->json('answers'); // Store answers as JSON
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Ensure participant can only submit survey once
            $table->unique(['participant_id', 'survey_id']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('participant_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('emailed_at')->nullable();
            $table->text('email_status')->nullable(); // Success/failed message
            $table->timestamps();
        });

    

    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        


    }
};
