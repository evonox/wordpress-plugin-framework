<?php

namespace __PLUGIN__\Extensions\MVP\Templates;

use __PLUGIN__\Framework\Attributes\Inject;
use __PLUGIN__\Framework\Attributes\PostConstruct;

class Template
{
    #[Inject("TEMPLATE_FILE_PATH")]
    public string $template_file_path;

    #[Inject("TEMPLATE_CACHE_PATH")]
    public string $template_cache_path;

    private string $template_file = "";

    /**
     * @var array<string, mixed>
     */
    private array $view_data = [];

    private \Twig\Environment $twig;

    #[PostConstruct()]
    public function onInitialize(): void
    {
        $loader = new \Twig\Loader\FilesystemLoader($this->template_file_path);
        $this->twig = new \Twig\Environment($loader, [
            'cache' => $this->template_cache_path
        ]);
    }

    public function setTemplateFile(string $template_file): void
    {
        $this->template_file = $template_file;
    }

    public function renderTemplate(): string
    {
        return $this->twig->render($this->template_file, $this->view_data);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->view_data[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->view_data[$name] ?? null;
    }
}
