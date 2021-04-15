<?php

namespace Qualp\Yii2XMLParser\Providers;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Qualp\Yii2XMLParser\XmlResponseFormatter;

class Yii2XMLParserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadLaravelResponseMacros();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    protected function loadLaravelResponseMacros()
    {
        ResponseFactory::macro('yii2Xml', function ($xml, $status = 200, array $headers = [], $xmlRoot = 'response') {
            if ($xml !== null) {
                $xml = (new XmlResponseFormatter())->format($xml);
            }

            if (!isset($headers['Content-Type'])) {
                $headers = array_merge($headers, ['Content-Type' => 'application/xml']);
            }

            return ResponseFactory::make($xml, $status, $headers);
        });
    }
}
