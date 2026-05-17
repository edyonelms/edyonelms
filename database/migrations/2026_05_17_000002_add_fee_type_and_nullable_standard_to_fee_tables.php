<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. fee_type column + make standard_id nullable in structures
        Schema::table('super_admin_fee_structures', function (Blueprint $table) {
            $table->enum('fee_type', ['class_wise', 'one_time'])->default('class_wise')->after('organization_id');
            $table->dropForeign(['standard_id']);
            $table->dropUnique('unique_org_standard_year');
        });
        Schema::table('super_admin_fee_structures', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_id')->nullable()->change();
            $table->foreign('standard_id')->references('id')->on('standards')->nullOnDelete();
        });

        // 2. make standard_id nullable in payments
        Schema::table('super_admin_fee_payments', function (Blueprint $table) {
            $table->dropForeign(['standard_id']);
        });
        Schema::table('super_admin_fee_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_id')->nullable()->change();
            $table->foreign('standard_id')->references('id')->on('standards')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('super_admin_fee_structures', function (Blueprint $table) {
            $table->dropColumn('fee_type');
            $table->dropForeign(['standard_id']);
        });
        Schema::table('super_admin_fee_structures', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_id')->nullable(false)->change();
            $table->foreign('standard_id')->references('id')->on('standards')->cascadeOnDelete();
            $table->unique(['organization_id', 'standard_id', 'academic_year'], 'unique_org_standard_year');
        });

        Schema::table('super_admin_fee_payments', function (Blueprint $table) {
            $table->dropForeign(['standard_id']);
        });
        Schema::table('super_admin_fee_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('standard_id')->nullable(false)->change();
            $table->foreign('standard_id')->references('id')->on('standards')->cascadeOnDelete();
        });
    }
};
