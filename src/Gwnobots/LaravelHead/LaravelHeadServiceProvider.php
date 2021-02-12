<?php namespace Gwnobots\LaravelHead;

use Illuminate\Support\ServiceProvider;

class LaravelHeadServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        $configPath = __DIR__.'/../../config/config.php';
        $this->publishes([
            $configPath => config_path('laravel-head.php'),
        ]);

        $this->mergeConfigFrom($configPath, 'laravel-head');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bind('laravel-head', function($app)
        {
            return new LaravelHead;
        }, true);

        $this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Head', 'Gwnobots\LaravelHead\LaravelHeadFacade');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('laravel-head');
	}

}
