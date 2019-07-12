<?php

namespace guifcoelho\ImmuTable\Testing;

use Illuminate\Database\Capsule\Manager;
use guifcoelho\ImmuTable\Testing\Support\DatabaseAssertions;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;

class Database {

    /**
     * The path to migration files
     *
     * @var string
     */
    protected $migrations_path = __DIR__."/../../tests/Unit/SampleModels/Migrations";

    /**
     * The database manager instance
     *
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $manager;

    /**
     * The migrator instance
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Instanciates a Database object
     */
    public function __construct(){
        if(file_exists(__DIR__.'/../../.env')){
            $dotenv = \Dotenv\Dotenv::create(__DIR__.'/../../');
            $dotenv->load();
        }       

        $this->manager = new Manager;

        $this->manager->addConnection([
            'driver'    => env('DB_DRIVER', 'mysql'),
            'host'      => env('DB_HOST', 'localhost'),
            'database'  => env('DB_NAME', 'database'),
            'username'  => env('DB_USER', 'root'),
            'password'  => env('DB_PASSWORD', ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'port'      => env('DB_PORT', 3306)
        ]);
    }

    /**
     * Boots the database connection and Eloquent
     *
     * @return self
     */
    public static function boot():self{
        $database = new static;
        $database->getManager()->bootEloquent();
        $database->getManager()->setAsGlobal();
        $database->setMigrator();
        return $database;
    }

    public static function install():void{
        $database = static::boot();
        $repository = $database->getMigrator()->getRepository();
        if(!$repository->repositoryExists()){
            $repository->createRepository();
            print("Migrations repository created successfully".PHP_EOL);
        }
    }

    /**
     * Runs the pending migrations
     *
     * @return void
     */
    public static function migrate():void{
        $database = static::boot();
        $migrator = $database->getMigrator();
        static::install();
        if(!$database->hasPendingMigrations()){
            print("Nothing to migrate".PHP_EOL);
            return;
        }
        $migrator->run($database->getMigrationsPath());
        print("All migrations ran".PHP_EOL);
    }

    public static function reset():void{
        $database = static::boot();
        static::install();
        $database->getMigrator()->reset([$database->getMigrationsPath()]);
        print("All tables dropped".PHP_EOL);
    }

    /**
     * Refreshes the database migrations
     *
     * @return void
     */
    public static function migrateFresh():void{
        static::reset();
        static::migrate();
    }

    /**
     * Sets the database migrator instance
     *
     * @return self
     */
    protected function setMigrator():self{
        $connection  = $this->manager->getConnection();
        $resolver = new ConnectionResolver(['default' => $connection]);
        $repository = new DatabaseMigrationRepository($resolver, 'migrations');
        $this->migrator = new Migrator(
            $repository,
            $resolver,
            new Filesystem
        );
        $this->migrator->setConnection('default');
        return $this;
    }

    /**
     * Returns the database manager
     *
     * @return Manager
     */
    public function getManager():Manager{
        return $this->manager;
    }

    /**
     * Returns the migrations path
     *
     * @return string
     */
    public function getMigrationsPath():string{
        return $this->migrations_path;
    }

    public function getMigrator():Migrator{
        return $this->migrator;
    }

    public function hasPendingMigrations(){
        $files = $this->migrator->getMigrationFiles($this->getMigrationsPath());
        $ran = $this->migrator->getRepository()->getRan();
        $migrations_pending = Collection::make($files)
                ->reject(function ($file) use ($ran) {
                    return in_array($this->migrator->getMigrationName($file), $ran);
                })->values()->all();
        return count($migrations_pending) > 0;
    }

}