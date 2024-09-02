<?php

namespace Test\Unit\Adapters\Database\Sqlite;

use Override;
use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected ?PDO $pdo = null;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownDatabase();
    }

    private function setUpDatabase(): void
    {
        $this->startConnection();
        $this->executeMigrations();
    }

    private function startConnection(): void
    {
        $dbHost = $_ENV['DB_HOST'];
        if (str_contains($dbHost, ':memory:')) {
            $this->pdo = new PDO('sqlite::memory:');

            return;
        }

        if (str_contains($dbHost, 'sqlite')) {
            $dbHost = BASE_DIR . '/database/sqlite3/' . $dbHost;
        }

        if (file_exists($dbHost)) {
            unlink($dbHost);
            touch($dbHost);

            $this->pdo = new PDO('sqlite:' . $dbHost);
        } else {
            throw new RuntimeException('SQLite database file not found: ' . $dbHost);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function executeMigrations(): void
    {
        $migrationsDir = BASE_DIR . '/database/migrations';

        /**
         * @var string[] $migrationFiles
         */
        $migrationFiles = scandir($migrationsDir);

        foreach ($migrationFiles as $migrationFile) {
            if ($migrationFile === '.' || $migrationFile === '..') {
                continue;
            }

            $migrationPath = $migrationsDir . '/' . $migrationFile;
            $migrationSql = file_get_contents($migrationPath);

            $result = $this->pdo->exec($migrationSql);
            if ($result === false) {
                throw new RuntimeException('Error executing migration: ' . $migrationPath);
            }
        }
    }

    private function tearDownDatabase(): void
    {
        $this->clearDatabase();
        $this->closeConnection();
    }

    private function closeConnection(): void
    {
        $this->pdo = null;
    }

    private function clearDatabase(): void
    {
        $dbHost = $_ENV['DB_HOST'];
        if (str_contains($dbHost, 'sqlite')) {
            $dbHost = BASE_DIR . '/database/sqlite3/' . $dbHost;
        }

        if (file_exists($dbHost)) {
            unlink($dbHost);
            touch($dbHost);

            $this->pdo = new PDO('sqlite:' . $dbHost);
        }
    }
}
