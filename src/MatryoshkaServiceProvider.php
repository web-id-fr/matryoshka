<?php

namespace Laracasts\Matryoshka;

use Blade;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class MatryoshkaServiceProvider extends ServiceProvider
{
    /**
     * Return the config files to publish
     *
     * @return array
     */
    protected function getConfigFiles()
    {
        return [
            __DIR__ . '/config/matryoshka.php' => config_path('matryoshka.php'),
        ];
    }

    /**
     * Bootstrap any application services.
     *
     * @param Kernel $kernel
     */
    public function boot(Kernel $kernel)
    {
        $this->publishes($this->getConfigFiles(), 'config');

        if ($this->app->isLocal() && config('matryoshka.autoclear-cache')) {
            $kernel->pushMiddleware('Laracasts\Matryoshka\FlushViews');
        }

        Blade::directive('cache', function ($expression) {
            return "<?php if (! app('Laracasts\Matryoshka\BladeDirective')->setUp({$expression})) : ?>";
        });

        Blade::directive('endcache', function () {
            return "<?php endif; echo app('Laracasts\Matryoshka\BladeDirective')->tearDown() ?>";
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(key($this->getConfigFiles()), 'matryoshka');
        $this->app->singleton(BladeDirective::class);
    }
}

