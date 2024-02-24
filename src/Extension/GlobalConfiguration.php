<?php

namespace SilverStripe\FeatureFlag\Extension;

use SilverStripe\FeatureFlag\Extension\Traits\DbFieldTrait;
use SilverStripe\ORM\DataExtension;

/**
 * @method \SilverStripe\SiteConfig\SiteConfig getOwner()
 */
class GlobalConfiguration extends DataExtension
{

    use DbFieldTrait;

}
