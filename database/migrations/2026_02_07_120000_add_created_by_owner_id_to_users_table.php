<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Track which Owner created which Admin.
 *  - Owner / Tenant: column is NULL.
 *  - Admin created by an Owner: column points to that Owner's user id.
 *  - "Super Admin" (seeded platform admin): column stays NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->unsignedBigInteger('created_by_owner_id')->nullable()->after('role_id');
            $t->index('created_by_owner_id');
            $t->foreign('created_by_owner_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropForeign(['created_by_owner_id']);
            $t->dropIndex(['created_by_owner_id']);
            $t->dropColumn('created_by_owner_id');
        });
    }
};
