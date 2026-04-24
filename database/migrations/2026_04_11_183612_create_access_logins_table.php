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
        Schema::create('access_logins', function (Blueprint $table) {
            $table->id();
            $table->string('rfid_uid'); //  simpan ID kartu
            $table->string('status');   // 'Success' atau 'Denied'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logins');
    }
};
