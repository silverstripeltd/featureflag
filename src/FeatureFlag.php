<?php

namespace SilverStripe\FeatureFlag;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Security;
use SilverStripe\View\ArrayData;
use SilverStripe\View\TemplateGlobalProvider;

/**
 * Class FeatureFlag provides feature flag functions
 */
class FeatureFlag implements TemplateGlobalProvider
{

    use Injectable;

    /**
     * Collection of registered feature flags. Feature name is the key, value is feature class
     */
    protected array $flags = [];

    /**
     * Register a feature by a class name
     */
    public function register(string $classname): self
    {
        // Feature must implement FeatureFlagInterface
        $class = singleton($classname);
        assert($class instanceof FeatureFlagInterface);

        $this->flags[$class->getName()] = $class;

        return $this;
    }

    /**
     * Provides a global $FeatureFlag variable to be used in templates.
     */
    public static function get_template_global_variables(): array
    {
        return [
            'FeatureFlag' => 'checkFeatureFlag',
            'FeatureMessages' => 'getFeatureMessages',
        ];
    }

    /**
     * Template function that returns boolean. Used to enable a feature
     *
     * <code>
     * <% if $FeatureFlag('Silverplasty') %>
     *     Do something cool in Silverplasty!
     * <% end_if %>
     * </code>
     */
    public static function checkFeatureFlag(string $name): bool
    {
        return self::singleton()->processFlag($name);
    }

    public static function getFeatureMessages(): ArrayList
    {
        $collection = [];
        $features = self::singleton()->getFeatures();

        foreach ($features as $feature) {
            assert($feature instanceof FeatureFlagInterface);

            // Ensure that the feature would like to show a message banner
            if (!$feature->showMessage(Security::getCurrentUser())) {
                continue;
            }

            $collection[] = ArrayData::create([
                'Name' => $feature->getName(),
                'Message' => $feature->getMessage(),
            ]);
        }

        return ArrayList::create($collection);
    }

    /**
     * Method used to contain feature within a feature flag ($name).
     * If the feature enabled, then execute its own logic.
     * If not enabled, there is option for optional fallback.
     */
    public static function withFeature(string $name, bool|callable $callback = true, ?callable $default = null): mixed
    {
        if (self::checkFeatureFlag($name)) {
            return is_callable($callback)
                ? $callback()
                : $callback;
        }

        return is_callable($default)
            ? $default()
            : $default;
    }

    /**
     * Process a flag to check if the feature is enabled
     */
    public function processFlag(string $name): bool
    {
        if (array_key_exists($name, $this->flags)) {
            return $this->flags[$name]->checkFeature(Security::getCurrentUser());
        }

        return false;
    }

    public function getFeatures(): array
    {
        return $this->flags;
    }

    /**
     * Magic method to allow for executing feature flag as follow,
     * <code>
     * FeatureFlag::withSilverplasty(...);
     * </code>
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        // Only if the method name start with `with`, then strip out the `with`
        // and use the reset of the method name as feature name.
        if (!str_starts_with($name, 'with')) {
            return null;
        }

        return self::withFeature((string)substr($name, 4), ...$arguments);
    }

}
