<?php

namespace SilverStripe\FeatureFlag;

use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Security\Member;

interface FeatureFlagInterface
{

    /**
     * Get unique name of the feature
     */
    public function getName(): string;

    /**
     * Check whether the feature enabled or not
     */
    public function checkFeature(?Member $member = null): bool;

    /**
     * Whether or not to show a message banner about the feature
     */
    public function showMessage(?Member $member = null): bool;

    /**
     * Get message to be used in CMS within a banner at the top of the page
     */
    public function getMessage(): DBField;

}
