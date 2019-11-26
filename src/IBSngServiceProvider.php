<?php


    namespace blackpanda\ibs;


    use App\Providers\EventServiceProvider;
    use Illuminate\Foundation\AliasLoader;
    use Illuminate\Support\ServiceProvider;

    class IBSngServiceProvider extends ServiceProvider
    {

        public function register()
        {

            // Register IBSng Package
            $this->app->bind('IBSng',function(){
                return new IBSng();
            });

            // Register Facade
            $alias = AliasLoader::getInstance();
            $alias->alias('IBSng','blackpanda\ibs\IBSngFacade');

            // Register Events Service Provider
            $this->app->register(EventServiceProvider::class);
        }

        public function boot()
        {

            // Publishes

            // Configs
            $this->publishes([
                __DIR__ . '/../publishes/config' => config_path('/'),
            ], 'ibs-configs');

            // Migrations
            $this->publishes([
                __DIR__ . '/../publishes/migrations' => database_path('/migrations'),
            ], 'ibs-migrations');

            // Models
            $this->publishes([
                __DIR__ . '/../publishes/Models' => app_path(),
            ], 'ibs-Models');
        }

    }
