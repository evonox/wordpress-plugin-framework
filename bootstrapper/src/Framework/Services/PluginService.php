<?php

namespace __PLUGIN__\Framework\Services;

use __PLUGIN__\Framework\Attributes\WPFilter;
use __PLUGIN__\Framework\Attributes\WPAction;
use __PLUGIN__\Framework\Helpers\ReflectionHelper;
use __PLUGIN__\Framework\DI\Container;
use ReflectionMethod;

class PluginService
{
    /** @var array<string> */
    private static array $hookHandlers = [];
    private static PluginService|null $serviceInstance = null;

    public static function bootService(): void
    {
        self::registerStaticHooks();

        $className = static::class;
        $methodName = "registerDynamicHooks";
        if (method_exists($className, $methodName)) {
            $className::$methodName();
        }
    }

    /**
     * @param array<mixed> $args
     */
    public static function __callStatic(string $name, array $args): mixed
    {
        if (strpos($name, "hook_handler_") === 0) {
            $hookName = str_replace("hook_handler_", "", $name);
            if (isset(self::$hookHandlers[$hookName]) === false) {
                throw new \Exception("Hook $hookName does not have registered any method handler.");
            }
            $handlerName = self::$hookHandlers[$hookName];
            return self::invokeHandler($handlerName, $args);
        } else {
            throw new \Exception("Call to undefined static method in class: " . static::class);
        }
    }

    /**
     * @param array<mixed> $args
     */
    private static function invokeHandler(string $methodName, array $args): mixed
    {
        if (is_null(self::$serviceInstance)) {
            self::$serviceInstance = Container::get()->make(static::class);
        }

        if (method_exists(self::$serviceInstance, $methodName)) {
            return self::$serviceInstance->{$methodName}(...$args);
        } else {
            throw new \Exception("Call to undefined hook handler '$methodName' in class: " . static::class);
        }
    }


    private static function registerStaticHooks(): void
    {
        $methods = ReflectionHelper::getInstanceMethods(static::class);

        foreach ($methods as $method) {
            $actionAttr = ReflectionHelper::getMethodAttribute($method, WPAction::class);
            if ($actionAttr !== false) {
                /** @var WPAction $actionAttr */
                self::registerAction($method, $actionAttr);
            }

            $filterAttr = ReflectionHelper::getMethodAttribute($method, WPFilter::class);
            if ($filterAttr !== false) {
                /** @var WPFilter $filterAttr */
                self::registerFilter($method, $filterAttr);
            }
        }
    }

    private static function registerAction(ReflectionMethod $method, WPAction $actionAttr): void
    {
        $actionName = $actionAttr->actionName;
        $actionPriority = $actionAttr->priority;
        $methodName = $method->getName();
        $parameterCount = count($method->getParameters());

        $staticHandlerName = "hook_handler_" . $actionName;
        self::$hookHandlers[$actionName] = $methodName;

        add_action($actionName, [static::class, $staticHandlerName], $actionPriority, $parameterCount);
    }

    private static function registerFilter(ReflectionMethod $method, WPFilter $filterAttr): void
    {
        $filterName = $filterAttr->filterName;
        $filterPriority = $filterAttr->priority;
        $methodName = $method->getName();
        $parameterCount = count($method->getParameters());

        $staticHandlerName = "hook_handler_" . $filterName;
        self::$hookHandlers[$filterName] = $methodName;

        add_filter($filterName, [static::class, $staticHandlerName], $filterPriority, $parameterCount);
    }
}
