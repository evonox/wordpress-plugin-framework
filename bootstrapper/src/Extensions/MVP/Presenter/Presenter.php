<?php

namespace __PLUGIN__\Extensions\MVP\Presenter;

use __PLUGIN__\Extensions\MVP\Http\HttpRequestAdapter;
use __PLUGIN__\Extensions\MVP\Http\HttpResponseAdapter;
use __PLUGIN__\Extensions\MVP\Interfaces\HttpResponse;
use __PLUGIN__\Framework\DI\Container;

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

        $this->response = new HttpResponseAdapter();
        if ($this->checkAccessRights() !== true) {
            $this->onShutdown();

            $this->response->setStatusCode(401);
            $this->response->setBody("Not Authorized");
            $this->response->send();
        }

        $this->dispatchAction();

        $this->beforeRender();
        $this->renderView();
        $this->afterRender();

        $this->onShutdown();
        $this->response?->send();
    }

    protected function onStartup(): void
    {
        parent::onStartup();
    }

    protected function checkAccessRights(): bool
    {
        return true;
    }

    private function dispatchAction(): void
    {
        // TODO: VALIDATION / SANITIZATION / ACCESS RIGHTS CHECK
        // TODO: ACTION
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
    }

    public function renderView(): void
    {
        $content = parent::render();
        $this->response?->setBody($content);
    }

    protected function afterRender(): void
    {
        parent::afterRender();
    }

    protected function onShutdown(): void
    {
        parent::onShutdown();
    }

    protected function downloadFile(string $filePath, string $fileName, string $mime = "application/octet-stream"): void
    {
        $this->onShutdown();
        $this->response?->downloadFile($filePath, $fileName, $mime);
    }

    protected function redirect(string $url): void
    {
        $this->onShutdown();
        $this->response?->redirect($url);
    }
}
