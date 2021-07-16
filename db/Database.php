<?php

namespace app\Core\db;
use app\Core\Application;

class Database
{
    /** @var \PDO $pdo */
    public $pdo;

    /**
     * Database constructor.
     * @param \PDO $pdo
     */
    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";

        try {
            $this->pdo = new \PDO("mysql:host=$servername;dbname=mvc_framework", $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public function applyMigrations(){
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();
        $files = scandir(Application::$ROOT_DIR.'/Migrations');
        $toApplyMigrations = array_diff($files , $appliedMigrations);
        $newMigrations = [];
        foreach ($toApplyMigrations as $migration){
            if($migration === '.' || $migration === '..'){
                continue;
            }
            require_once Application::$ROOT_DIR.'/Migrations/'.$migration;
            $className = pathinfo($migration , PATHINFO_FILENAME);
            $instance = new $className();
            $this->log("Applying Migrations " + $migration);
            $instance->up();
            $this->log("Applied Migrations " + $migration);
            $newMigrations[] = $migration;
        }
        if(!empty($newMigrations))
            $this->saveMigrations($newMigrations);
        else
            $this->log("All migrations are done");
    }
    public function saveMigrations($migrations){
        foreach ($migrations as $mig){
            $str = "('$mig')";
            $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $str");
            $statement->execute();
        }
    }
    public function prepare($query){
        return $this->pdo->prepare($query);
    }
    protected function log($message){
        echo '[ ' .date('y-m-d h-m-s'). ' ] = ' .$message;
    }
    public function createMigrationsTable(){
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS migrations(
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP )
                 ENGINE=INNODB;"
            );
    }
    public function getAppliedMigrations(){
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
}