<?php

namespace __PLUGIN__\Extensions\MVP\Presenter;

abstract class Control extends Component
{
    protected function onStartup(): void
    {
        parent::onStartup();
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();
    }

    public function render(): string
    {
        return "";
    }

    protected function afterRender(): void
    {
        parent::afterRender();
    }

    protected function onShutdown(): void
    {
        parent::onShutdown();
    }
}
