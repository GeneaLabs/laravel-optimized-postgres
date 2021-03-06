<?php namespace GeneaLabs\LaravelOptimizedPostgres\Providers;

use GeneaLabs\LaravelOptimizedPostgres\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;

class LaravelOptimizedPostgresService extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
    }

    public function register()
    {
        //TODO: figure out how to overwrite the already loaded schema alias.
        AliasLoader::getInstance()->alias('Schema', Schema::class);
        $this->registerSchemaMacros();
    }

    public function provides() : array
    {
        return ['genealabs-laravel-optimized-postgres'];
    }

    protected function registerSchemaMacros()
    {
        Blueprint::macro('dropIndexIfExists', function (string $index): Fluent {
            if ($this->hasIndex($index)) {
                return $this->dropIndex($index);
            }

            return new Fluent();
        });

        Blueprint::macro('hasIndex', function (string $index): bool {
            return Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableDetails($this->getTable())
                ->hasIndex($index);
        });
    }
}
