<?php

namespace src\controllers;

use src\app\Request;
use src\app\Response;
use src\app\Stdio;
use src\database\Database;
use src\database\queries\Query;

class ManagementController extends Controller
{
    public function initDatabase(Database $database)
    {
        if ($database->getType() == Database::MYSQL) {
            $database->exec('CREATE TABLE IF NOT EXISTS `migrations` (`id` INT NOT NULL AUTO_INCREMENT , `filename` VARCHAR(255) NOT NULL , `applied_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;');
        }

        Stdio::printLn('Migrations table created');
    }

    public function runMigrations(Database $database)
    {
        $migrationsDir = sprintf(
            '%s/%s',
            BASEDIR,
            MIGRATIONSDIR
        );

        $migrations = array_filter(
            scandir($migrationsDir),
            function (string $item) {
                return str_ends_with($item, '.sql');
            }
        );

        foreach ($migrations as $migration) {
            $query = file_get_contents(sprintf('%s/%s', $migrationsDir, $migration));

            $migrationAlreadyApplied = $database->query()
                ->table('migrations')
                ->where('filename', '=', $migration)
                ->exists();

            if ($migrationAlreadyApplied) {
                Stdio::printFLn('migration: "%s" already applied', $migration);
                continue;
            }

            $database->exec($query);
            Stdio::printFLn('migration: "%s" applied', $migration);

            $database->query()
                ->table('migrations')
                ->values([
                    'filename' => $migration,
                    'applied_at' => Query::now()
                ])
                ->insert();
        }
    }
}