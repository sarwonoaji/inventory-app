<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tables = [
            'barangs',
            'barang_masuks',
            'barang_masuk_details',
            'barang_keluars',
            'barang_keluar_details',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) {
                    if (!Schema::hasColumn($t->getTable(), 'created_by')) {
                        $t->unsignedBigInteger('created_by')->nullable()->after('id');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'created_at')) {
                        $t->timestamp('created_at')->nullable()->after('created_by');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'last_modified_by')) {
                        $t->unsignedBigInteger('last_modified_by')->nullable()->after('created_at');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'last_modified_at')) {
                        $t->timestamp('last_modified_at')->nullable()->after('last_modified_by');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'deleted_by')) {
                        $t->unsignedBigInteger('deleted_by')->nullable()->after('last_modified_at');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'deleted_at')) {
                        $t->timestamp('deleted_at')->nullable()->after('deleted_by');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'is_deleted')) {
                        $t->tinyInteger('is_deleted')->default(0)->after('deleted_at');
                    }
                });
            }
        }
    }

    public function down()
    {
        $tables = [
            'barangs',
            'barang_masuks',
            'barang_masuk_details',
            'barang_keluars',
            'barang_keluar_details',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) {
                    $cols = ['created_by','created_at','last_modified_by','last_modified_at','deleted_by','deleted_at','is_deleted'];
                    foreach ($cols as $c) {
                        if (Schema::hasColumn($t->getTable(), $c)) {
                            $t->dropColumn($c);
                        }
                    }
                });
            }
        }
    }
};
