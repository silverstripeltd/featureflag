<?php

namespace SilverStripe\FeatureFlag\Extension;

use SilverStripe\FeatureFlag\Extension\Traits\DbFieldTrait;
use SilverStripe\ORM\DataExtension;

/**
 * @method \SilverStripe\Security\Member getOwner()
 */
class MemberConfiguration extends DataExtension
{

    use DbFieldTrait;

}
