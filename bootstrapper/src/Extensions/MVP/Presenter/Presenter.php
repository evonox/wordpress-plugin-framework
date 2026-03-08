<?php

namespace __PLUGIN__\Extensions\MVP\Presenter;

use __PLUGIN__\Extensions\MVP\Http\HttpRequestAdapter;
use __PLUGIN__\Extensions\MVP\Interfaces\HttpResponse;
use __PLUGIN__\Framework\DI\Container;

// TODO: RESPONSE HANDLING FACTORY METHODS
abstract class Presenter extends Component
{
    protected ?HttpResponse $response;

    public static function bootstrap(): void
    {
        $httpRequest = new HttpRequestAdapter();
        Container::get()->bind("HTTP_REQUEST")->toConstantValue($httpRequest);

        $presenterClassName = static::class;
        $presenter = Container::get()->make($presenterClassName);
        $presenter->constructInternalComponents();

        $presenter->handleRequest();
    }

    public function handleRequest(): void
    {
        $this->onStartup();
        // TODO: VALIDATION / SANITIZATION / ACCESS RIGHTS CHECK
        // TODO: ACTION
        // TODO: SIGNAL
        $this->beforeRender();
        // TODO: HANDLE TEMPLATE RENDER + TWIG ENVIRONMENT
        $this->afterRender();
        $this->onShutdown();
        $this->response?->send();
    }

    protected function onStartup(): void
    {
        parent::onStartup();
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
    }

    public function render(): void
    {
    }

    protected function afterRender(): void
    {
        parent::afterRender();
    }

    protected function onShutdown(): void
    {
        parent::onShutdown();
    }

    protected function downloadFile(string $filePath, string $fileName): void
    {
    }

    protected function redirect(string $url): void
    {
    }
}
