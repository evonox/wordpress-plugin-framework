<?php

namespace __PLUGIN__\Extensions\MVP\Presenter;

use __PLUGIN__\Extensions\MVP\Attributes\Get;
use __PLUGIN__\Extensions\MVP\Attributes\Post;
use __PLUGIN__\Extensions\MVP\Http\HttpRequestAdapter;
use __PLUGIN__\Extensions\MVP\Http\HttpResponseAdapter;
use __PLUGIN__\Extensions\MVP\Interfaces\HttpResponse;
use __PLUGIN__\Framework\DI\Container;
use ReflectionClass;
use ReflectionMethod;

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
        $action = $this->request->getParam("action");
        if ($action === null) {
            $action = "index";
        }

        $methodName = $this->lookupActionHandler($action);
        if ($methodName !== null && method_exists($this, $methodName)) {
            $this->invokeActionMethod($methodName);
        } else {
            $this->onShutdown();
            $this->response->setStatusCode(404);
            $this->response->send();
        }
    }

    private function invokeActionMethod(string $methodName): void
    {
        /**
         * Zjistit parametry metody a datovy typ
         * Zjistit, jestli ma request vsechny pozadovane attributy
         * Zjistit, zda ma POST anebo GET attribut + NAZEV ACTION - UPRAVIT HLEDANI METODY / LOOKUP
         */

        // TODO: VALIDATION / SANITIZATION
        // TODO: ACTION
    }

    private function lookupActionHandler(string $action): string|null
    {
        $reflection = new ReflectionClass($this);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $attributes = [];
            if ($this->request->getMethod() === "GET") {
                /** @var Get $attribute */
                $attributes = $method->getAttributes(Get::class);
            } elseif ($this->request->getMethod() === "POST") {
                $attributes = $method->getAttributes(Post::class);
            }

            if (count($attributes) > 0) {
                /** @var Get|Post $attribute */
                $attribute = $attributes[0]->newInstance();
                if ($attribute->action === $action) {
                    return $method->getName();
                }
            }
        }

        return null;
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
