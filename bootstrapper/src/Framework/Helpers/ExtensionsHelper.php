<?php

namespace __PLUGIN__\Framework\Helpers;

class ExtensionsHelper
{
    /**
     * @var array<string>
     */
    private static $extensions = [];

    public static function bootstrapExtensions(): void
    {
        self::discoverExtensions();
        foreach (self::$extensions as $extension) {
            $bootstrapFile = self::getExtensionBootstrapFile($extension);
            if (file_exists($bootstrapFile)) {
                require_once $bootstrapFile;
            }
        }
    }

    /**
     * @return array<string>
     */
    public static function getExtensions(): array
    {
        return self::$extensions;
    }

    public static function isExtensionInstalled(string $extension): bool
    {
        return in_array($extension, self::$extensions);
    }

    private static function discoverExtensions(): void
    {
        $extensionDirectory = self::getExtensionDirectory();
        if (is_dir($extensionDirectory)) {
            $extensionDirectories = scandir($extensionDirectory);
            foreach ($extensionDirectories as $dir) {
                if ($dir === "." || $dir === "..") {
                    continue;
                }
                if (is_dir($extensionDirectory . "/" . $dir)) {
                    self::$extensions[] = $dir;
                }
            }
        }
    }

    private static function getExtensionBootstrapFile(string $extension): string
    {
        return self::getExtensionDirectory() . "/$extension/bootstrap.php";
    }

    private static function getExtensionDirectory(): string
    {
        return __DIR__ . '/../../Extensions';
    }
}
