<?php

namespace src\controllers;

use Throwable;
use src\app\Request;
use src\app\Response;
use src\app\Stdio;
use src\database\Database;
use src\database\queries\Query;
use src\models\Migration;

class ManagementController extends Controller
{
    public function initDatabase()
    {
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $engine = $_ENV['DB_ENGINE'];
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $name = $_ENV['DB_NAME'];
        $debug = $_ENV['DB_DEBUG'];

        $database = Database::createInstanceWithoutDatabase(
            $engine,
            $host,
            $port,
            $username,
            $password,
            $debug,
        );

        $database->createDatabase(
            $name,
            true,
            true
        );

        $migration = new Migration($database);
        $migration->createTable(true);

        Stdio::printLn('Migrations table created');
    }

    public function runMigrations(Database $database)
    {
        $migrationsDir = getMigrationsDir();

        $scannedFiles = scandir($migrationsDir);
        $sortedScannedFiles = [];

        foreach ($scannedFiles as $scannedFile) {
            $match = preg_match('/(.[^\D+$]*)/', $scannedFile, $matches);
            if (!$match) {
                continue;
            }

            $key = $matches[1];
            $sortedScannedFiles[$key] = $scannedFile;
        }

        ksort($sortedScannedFiles);

        $migrations = array_filter(
            $sortedScannedFiles,
            function (string $item) {
                return str_ends_with($item, '.sql');
            }
        );

        foreach ($migrations as $migration) {
            $query = trim(file_get_contents(sprintf('%s/%s', $migrationsDir, $migration)));

            $migrationAlreadyApplied = $database->query()
                ->table(Migration::getTable())
                ->where('filename', Query::EQUALS, $migration)
                ->exists();

            if ($migrationAlreadyApplied) {
                Stdio::printFLn('migration: "%s" already applied', $migration);
                continue;
            }

            if (substr($query, -1) != ';') {
                $query .= ';';
            }

            try {
                $database->transactionAsCallback(function ($connection) use ($query) {
                    $connection->exec(sprintf('%s', $query));
                });
            } catch (Throwable $err) {
                throw $err;
            }

            Stdio::printFLn('migration: "%s" applied', $migration);

            $migrationModel = new Migration($database);
            $migrationModel->filename = $migration;
            $migrationModel->appliedAt = Query::now();
            $migrationModel->insert();
        }
    }

    public function initModel(Database $database, array $flags)
    {
        if (!isset($flags['class'])) {
            Stdio::errorLn('No flag --class={className} set');
            return;
        }

        $className = sprintf('\\src\\models\\%s', $flags['class']);

        if (!class_exists($className)) {
            Stdio::errorFLn('Model %s does not exist', $className);
            return;
        }

        $class = new $className($database);
        $statement = $class->createTable();
        $query = $statement->queryString;

        $migrationName = sprintf(
            '%s_create_%s_table.sql',
            date('YmdHis'),
            $class->getTable()
        );

        $migrationModel = new Migration($database);
        $migrationModel->filename = $migrationName;
        $migrationModel->appliedAt = Query::now();
        $migrationModel->insert();

        file_put_contents(sprintf('%s/%s', getMigrationsDir(), $migrationName), $query);

        Stdio::printFLn('Migration for model %s created successfully', $flags['class']);
    }

    public function runTests()
    {
        /**
         * Each file that doesn't abide by the PHPUnit format will be converted before running tests
         */
        $testsDir = getTestsDir();

        $testFiles = array_filter(
            scandir($testsDir),
            function ($fileName): bool {
                return (!in_array($fileName, ['.', '..']) && str_ends_with($fileName, '.php'));
            }
        );

        array_walk(
            $testFiles,
            function ($fileName) use ($testsDir): void {
                if (str_ends_with($fileName, 'Test.php')) {
                    return;
                }

                $filePath = sprintf('%s/%s', $testsDir, $fileName);
                $fileNameWithoutType = rtrim($fileName, '.php');
                $fileNameWithTest = sprintf('%sTest', $fileNameWithoutType);
                $fileContents = file_get_contents($filePath);

                file_put_contents(
                    $filePath,
                    str_replace(
                        sprintf('class %s extends', $fileNameWithoutType),
                        sprintf('class %s extends', $fileNameWithTest),
                        $fileContents
                    )
                );

                rename(
                    $filePath,
                    sprintf('%s/%s.php', $testsDir, $fileNameWithTest)
                );
            }
        );

        exec('phpunit tests/', $output);

        Stdio::printLn(implode(PHP_EOL, $output));
    }
}
