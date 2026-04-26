<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE json USING data::json');

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE notifications MODIFY data JSON NOT NULL');

            return;
        }

        // SQLite stores JSON as TEXT already; no-op.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE text');

            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE notifications MODIFY data TEXT NOT NULL');
        }
    }
};
