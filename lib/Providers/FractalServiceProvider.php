<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   Proprietary
 */
namespace CHMS\Common\Providers;

use Illuminate\Support\ServiceProvider;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Illuminate\Http\Request;

class FractalServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Collection::class, function ($app) {
            return new Collection();
        });
        $this->app->singleton(Item::class, function ($app) {
            return new Item();
        });
        $this->app->bind(Manager::class, function ($app) {
            $manager = new Manager();
            $request = app('Request');
            $manager->setSerializer(new JsonApiSerializer($request::getSchemeAndHttpHost()));
            return $manager;
        });
    }

    /**
     * Boot the console specific services
     *
     * @return void
     */
    public function boot()
    {
    }
}
