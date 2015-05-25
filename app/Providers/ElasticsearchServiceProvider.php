<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('elasticsearch', function($app) {
            return new \Elasticsearch\Client();
        });
    }
}
