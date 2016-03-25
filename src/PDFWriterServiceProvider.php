<?php

namespace Greenleaf\PDFWriter;

use Illuminate\Support\ServiceProvider;

/**
 * The PDFServiceProvider class.
 *
 * @author John Hoopes <john@greenleafmedia.com>
 */
class PDFWriterServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/pdfwriter.php' => config_path('pdfwriter.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/pdfwriter.php', 'pdfwriter'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {

    }
}
