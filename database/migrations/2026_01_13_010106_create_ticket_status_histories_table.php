<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_status_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_id')->constrained('users')->restrictOnDelete();

            $table->string('from_status', 32);
            $table->string('to_status', 32);

            $table->string('note', 255)->nullable();

            $table->timestamps();

            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_status_histories');
    }
};
