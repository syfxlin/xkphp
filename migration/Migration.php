<?php

namespace Migration;

use App\Facades\DB;

class Migration
{
    public function __construct()
    {
        if (php_sapi_name() !== 'cli') {
            throw new \RuntimeException('Illegal call to database migration');
        }
    }

    public function create(string $table, array $field)
    {
        $options = [];
        foreach ($field as $key => $value) {
            $options[] = "$key $value";
        }
        $options = join(", ", $options);
        $sql = "CREATE TABLE $table ($options)";
        DB::select($sql);
    }

    public function delete(string $table)
    {
        DB::select("DROP TABLE $table");
    }
}
