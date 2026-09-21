<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece bilet için zorunlu tabloları dolduruyoruz, Factory vs. yok
        if (Schema::hasTable('categories')) {
            DB::table('categories')->insertOrIgnore([
                ['id' => 1, 'name' => 'Genel Arıza / Teknik', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'Yazılım / Sistem', 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'İdari / Diğer', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('priorities')) {
            DB::table('priorities')->insertOrIgnore([
                ['id' => 1, 'name' => 'Düşük', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'name' => 'Orta',  'level' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'name' => 'Yüksek', 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'name' => 'Acil',   'level' => 4, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
