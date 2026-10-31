<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;

/**
 * Inserts rows into the legacy tables for tests.
 *
 * Many legacy columns are NOT NULL without a default, and MySQL strict mode
 * rejects inserts that omit them. fill() supplies a type-appropriate blank
 * value for every such column, so tests only state the columns they care about.
 */
class Fixtures
{
    private static $requiredColumns = [];

    public static function insert($table, array $attributes = [])
    {
        return DB::table($table)->insertGetId(self::fill($table, $attributes));
    }

    public static function fill($table, array $attributes = [])
    {
        foreach (self::requiredColumns($table) as $column => $type) {
            if (!array_key_exists($column, $attributes)) {
                $attributes[$column] = self::blank($type);
            }
        }

        return $attributes;
    }

    private static function requiredColumns($table)
    {
        if (!isset(self::$requiredColumns[$table])) {
            $rows = DB::select(
                "SELECT column_name AS name, data_type AS type FROM information_schema.columns
                 WHERE table_schema = DATABASE() AND table_name = ? AND is_nullable = 'NO'
                 AND column_default IS NULL AND extra NOT LIKE '%auto_increment%'",
                [$table]
            );
            self::$requiredColumns[$table] = collect($rows)->pluck('type', 'name')->all();
        }

        return self::$requiredColumns[$table];
    }

    private static function blank($type)
    {
        switch (strtolower($type)) {
            case 'tinyint': case 'smallint': case 'mediumint': case 'int': case 'bigint':
            case 'decimal': case 'float': case 'double':
                return 0;
            case 'date':
                return '2026-01-01';
            case 'datetime': case 'timestamp':
                return '2026-01-01 00:00:00';
            case 'time':
                return '00:00:00';
            default:
                return '';
        }
    }
}
