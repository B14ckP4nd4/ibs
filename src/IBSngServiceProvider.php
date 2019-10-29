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
            ], 'configs');
        }

    }
