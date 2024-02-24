<?php

namespace SilverStripe\FeatureFlag\Extension\Traits;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;

trait DbFieldTrait
{

    use Configurable;

    /**
     * Collection of implemented features that can be enabled/disabled.
     * The feature defined as <db field name|label>
     *
     * @config
     */
    private static array $features = [];

    public static function get_extra_config($class = null, $extensionClass = null, $args = null): array
    {
        $features = (array)self::config()->get('features');

        if (!count($features)) {
            return [];
        }

        // Create an array of db field where the key is db field  name and value is db type boolean
        return [
            'db' => array_combine(array_keys($features), array_pad([], count($features), 'Boolean(false)')),
        ];
    }

    public function updateCMSFields(FieldList $fields): void
    {
        // Collection of implemented features
        $features = (array)self::config()->get('features');

        // Create Yes/No field to enable/disable each feature
        foreach ($features as $name => $data) {
            $fields->addFieldToTab(
                'Root.Features',
                OptionsetField::create($name, $data['Label'], [
                    1 => 'Yes',
                    0 => 'No',
                ])->setDescription($data['Help']),
            );
        }
    }

}
