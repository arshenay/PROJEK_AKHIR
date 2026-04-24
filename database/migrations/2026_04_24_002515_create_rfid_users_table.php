<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void {
    Schema::create('rfid_users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('rfid_uid')->unique();
        $table->boolean('is_active')->default(true); // Buat fitur On/Off akses
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_users');
    }
};
