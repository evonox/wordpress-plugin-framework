<?php

namespace __PLUGIN__\Extensions\MVP\Presenter;

use __PLUGIN__\Extensions\MVP\Interfaces\HttpRequest;
use __PLUGIN__\Framework\Attributes\Inject;
use __PLUGIN__\Framework\DI\Container;

// TODO: TEMPLATE + SET VIEWMODEL PARAMETERS
abstract class Component
{
    protected HttpRequest $request;

    /**
     * @var array<string, Component>
     */
    #[Inject("HTTP_REQUEST")]
    protected array $components = [];

    public function constructInternalComponents(): void
    {
        $components = $this->defineComponents();
        foreach ($components as $name => $componentClass) {
            $this->components[$name] = Container::get()->make($componentClass);
            $this->components[$name]->constructInternalComponents();
        }
    }

    /**
     * @return array<string, string>
     */
    protected function defineComponents(): array
    {
        return [];
    }

    protected function onStartup(): void
    {
        foreach ($this->components as $name => $component) {
            $component->onStartup();
        }
    }

    protected function beforeRender(): void
    {
        foreach ($this->components as $name => $component) {
            $component->beforeRender();
        }
    }

    protected function afterRender(): void
    {
        foreach ($this->components as $name => $component) {
            $component->afterRender();
        }
    }

    protected function onShutdown(): void
    {
        foreach ($this->components as $name => $component) {
            $component->onShutDown();
        }
    }
}
