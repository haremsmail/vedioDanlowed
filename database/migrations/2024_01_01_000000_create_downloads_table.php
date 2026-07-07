<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048);
            $table->string('title')->nullable();
            $table->string('format_id', 100);
            $table->string('quality', 50);
            $table->string('status')->default('pending');
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_address', 50)->nullable();
            //increse some things
            
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
