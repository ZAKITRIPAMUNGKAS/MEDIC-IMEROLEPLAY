<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master sub-jabatan (divisi)
        Schema::create('staff_sub_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // slug: ie, pnd, msl, ga, comdis
            $table->string('display_name');                  // nama tampilan: "Industrial & Employee Relations"
            $table->string('short_name')->nullable();        // singkatan: IE, PND, MSL, GA, Comdis
            $table->string('hospital')->default('alta');     // alta | roxwood
            $table->text('description')->nullable();
            $table->string('color')->default('#6366f1');     // warna badge hex
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);       // urutan tampilan
            $table->timestamps();
        });

        // Tambah kolom sub_role_id ke tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('sub_role_id')
                  ->nullable()
                  ->after('role_id')
                  ->constrained('staff_sub_roles')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['sub_role_id']);
            $table->dropColumn('sub_role_id');
        });

        Schema::dropIfExists('staff_sub_roles');
    }
};
