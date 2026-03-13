<?php

namespace __PLUGIN__\Extensions\MVP\Presenter;

use __PLUGIN__\Extensions\MVP\Interfaces\HttpRequest;
use __PLUGIN__\Extensions\MVP\Templates\Template;
use __PLUGIN__\Framework\Attributes\Inject;
use __PLUGIN__\Framework\DI\Container;
use PHPStan\Type\StringNeverAcceptingObjectWithToStringType;

abstract class Component
{
    #[Inject("HTTP_REQUEST")]
    public HttpRequest $request;

    #[Inject()]
    public Template $template;

    /**
     * @var array<string, Component>
     */
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

    public function render(): string
    {
        return $this->template->renderTemplate();
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
