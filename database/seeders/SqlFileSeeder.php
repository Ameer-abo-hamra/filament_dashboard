<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlFile = database_path('migrations/wemarketglobal_we_ameer_new__7_12_2024.sql');

        // First, check if required country codes exist
        $this->ensureCountryCodesExist();

        if (!file_exists($sqlFile)) {
            throw new \Exception("SQL file not found at: $sqlFile");
        }

        $sql = file_get_contents($sqlFile);

        // Split by delimiter while preserving the statements structure
        $statements = preg_split('/;\s*\n/', $sql);

        // First run all INSERT statements for subscribers
        foreach ($statements as $statement) {
            if (stripos($statement, 'INSERT INTO `subscribers`') !== false) {
                try {
                    if (!empty($statement)) {
                        DB::unprepared($statement . ';');
                    }
                } catch (\Exception $e) {
                    if (!str_contains($e->getMessage(), 'already exists')) {
                        throw $e;
                    }
                }
            }
        }

        foreach ($statements as $statement) {
            try {
                if (!empty($statement)) {
                    DB::unprepared($statement . ';');
                }
            } catch (\Exception $e) {
                if (!str_contains($e->getMessage(), 'already exists')) {
                    throw $e;
                }
            }
        }
    }

    private function ensureCountryCodesExist(): void
    {
        // Insert minimum required country codes if they don't exist
        DB::table('country_codes')->insertOrIgnore([
            ['id' => 1, 'name' => 'Syria', 'code' => '+963'],
            ['id' => 2, 'name' => 'UAE', 'code' => '+971'],
            // Add any other country codes that your data references
        ]);
    }
}
