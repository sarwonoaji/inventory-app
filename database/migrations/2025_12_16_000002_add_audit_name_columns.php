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
                    if (!Schema::hasColumn($t->getTable(), 'created_by_name')) {
                        $t->string('created_by_name')->nullable()->after('created_by');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'last_modified_by_name')) {
                        $t->string('last_modified_by_name')->nullable()->after('last_modified_by');
                    }
                    if (!Schema::hasColumn($t->getTable(), 'deleted_by_name')) {
                        $t->string('deleted_by_name')->nullable()->after('deleted_by');
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
                    foreach (['created_by_name','last_modified_by_name','deleted_by_name'] as $c) {
                        if (Schema::hasColumn($t->getTable(), $c)) {
                            $t->dropColumn($c);
                        }
                    }
                });
            }
        }
    }
};
