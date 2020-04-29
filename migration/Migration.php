<?php

namespace Migration;

use App\Facades\DB;
use RuntimeException;

class Migration
{
    public function __construct()
    {
        if (PHP_SAPI !== 'cli') {
            throw new RuntimeException('Illegal call to database migration');
        }
    }

    public function create(string $table, array $field): void
    {
        $options = [];
        foreach ($field as $key => $value) {
            $options[] = "$key $value";
        }
        $options = implode(', ', $options);
        $sql = "CREATE TABLE $table ($options)";
        DB::select($sql);
    }

    public function delete(string $table): void
    {
        DB::select("DROP TABLE $table");
    }
}
