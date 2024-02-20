<?php

namespace src\controllers;

use DateTime;
use DotEnv;
use Throwable;
use src\app\Stdio;
use src\database\Database;
use src\database\queries\Query;
use src\filesystem\File;
use src\filesystem\Filesystem;
use src\models\Migration;
use src\util\Strings;

class ManagementController extends Controller
{
    public function startServer(): void
    {
        exec(
            sprintf(
                'php -S %s:%s %s/index.php',
                env('SERVER_HOST', '0.0.0.0'),
                env('SERVER_PORT', 8000),
                BASEDIR
            )
        );
    }

    public function createDatabase(): void
    {
        $username = env('DB_USERNAME', '');
        $password = env('DB_PASSWORD', '');
        $engine = env('DB_ENGINE', '');
        $host = env('DB_HOST', '');
        $port = env('DB_PORT', '');
        $name = env('DB_NAME', '');
        $debug = env('DB_DEBUG', '');

        $database = Database::createInstanceWithoutDatabase(
            $engine,
            $host,
            $port,
            $username,
            $password,
            $debug,
        );

        $database->createDatabase($name, true);

        Stdio::printFLn('Database %s created successfully', $name);
    }

    public function initDatabase(Database $database): void
    {
        $migration = new Migration($database);
        $migration->createTable(true);

        Stdio::printLn('Migrations table created');
    }

    public function runMigrations(Database $database): void
    {
        $migrationsDir = getMigrationsDir();

        $scannedFiles = Filesystem::scandir($migrationsDir, false, ['.sql']);
        $migrations = [];

        foreach ($scannedFiles as $scannedFile) {
            $match = preg_match('/(.[^\D+$]*)/', $scannedFile, $matches);
            if (!$match) {
                continue;
            }

            $key = $matches[1];
            $migrations[$key] = $scannedFile;
        }

        if (!$migrations) {
            Stdio::printLn('No migrations found');
            return;
        }

        foreach ($migrations as $migration) {
            $query = trim(
                File::read(appendToBaseDir($migrationsDir, $migration))
            );

            $migrationAlreadyApplied = $database->query()
                ->model(Migration::class)
                ->where('filename', Query::EQUALS, $migration)
                ->exists();

            if ($migrationAlreadyApplied) {
                Stdio::printFLn('Migration: "%s" already applied', $migration);
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

            Stdio::printFLn('Migration: "%s" applied', $migration);

            $migrationModel = new Migration($database);
            $migrationModel->filename = $migration;
            $migrationModel->appliedAt = Query::now();
            $migrationModel->insert();
        }
    }

    public function initModel(Database $database, array $flags): void
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

    public function runTests(): void
    {
        /**
         * Each file that doesn't abide by the PHPUnit format will be converted before running tests
         */
        $testsDir = getTestsDir();

        $testFiles = Filesystem::scandir($testsDir, false);

        array_walk(
            $testFiles,
            function ($fileName) use ($testsDir): void {
                if (!str_ends_with($fileName, '.php')) {
                    return;
                }

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

    public function dockerInit(): void
    {
        Stdio::printFLn(
            'Removing old %s project and building new project. This might take a while',
            env('DOCKER_PROJECT_NAME', '')
        );

        $this->dockerDown();
        $this->dockerRebuild();
    }

    public function dockerUp(): void
    {
        exec(
            sprintf(
                'docker compose -p %s up -d',
                env('DOCKER_PROJECT_NAME', '')
            )
        );
    }

    public function dockerDown(): void
    {
        exec(
            sprintf(
                'docker compose -p %s down',
                env('DOCKER_PROJECT_NAME', '')
            )
        );
    }

    public function dockerRebuild(): void
    {
        exec(
            sprintf(
                'docker compose -p %s up -d --force-recreate',
                env('DOCKER_PROJECT_NAME', '')
            )
        );
    }

    public function fixDotEnv(): void
    {
        $dotEnv = '.env';
        $dotEnvExample = '.env.example';

        $dotEnvFilePath = appendToBaseDir(BASEDIR, $dotEnv);
        $dotEnvExampleFilePath = appendToBaseDir(BASEDIR, $dotEnvExample);

        $dotEnvData = DotEnv::parseFile($dotEnvFilePath);
        $dotEnvExampleData = DotEnv::parseFileRaw($dotEnvExampleFilePath);

        $missingVariables = [];

        foreach ($dotEnvExampleData as $key => $value) {
            if (key_exists($key, $dotEnvData)) {
                continue;
            }

            $missingVariables[$key] = $value;
        }

        if (!$missingVariables) {
            Stdio::printFLn(
                '%s is up to date',
                $dotEnv
            );
            return;
        }

        $dotEnvFileContents = File::read($dotEnvFilePath);
        $newLine = Strings::detectNewline($dotEnvFileContents);

        if (!str_ends_with($dotEnvFilePath, $newLine)) {
            File::append($dotEnvFilePath, $newLine);
        }

        File::append(
            $dotEnvFilePath,
            sprintf(
                '# imported variables from %s on %s',
                $dotEnvExample,
                (new DateTime)->format('F jS Y, H:i')
            )
        );
        File::append(
            $dotEnvFilePath,
            $newLine
        );

        foreach ($missingVariables as $key => $value) {
            File::append(
                $dotEnvFilePath,
                sprintf(
                    '%s=%s',
                    $key,
                    $value,
                    $newLine
                )
            );
            File::append(
                $dotEnvFilePath,
                $newLine
            );
        }

        Stdio::printFLn('Added %s variables to .env', count($missingVariables));
    }
}
