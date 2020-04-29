<?php
if (PHP_SAPI !== 'cli') {
    throw new RuntimeException('Illegal call to database migration');
}

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$except = ['Migration', 'run'];

$migrations = array_map(
    function ($file) {
        return "Migration\\" . str_replace(".php", "", $file);
    },
    array_values(
        array_filter(scandir(__DIR__), function ($file) use ($except) {
            return !(
                $file === '.' ||
                $file === '..' ||
                in_array(str_replace('.php', '', $file), $except, true)
            );
        })
    )
);

$run_type = $argc > 1 && $argv[1] === 'drop' ? 'drop' : 'up';

foreach ($migrations as $class) {
    (new $class())->$run_type();
    echo "Migration $class ($run_type)\n";
}
