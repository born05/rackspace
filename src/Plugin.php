<?php

namespace craft\rackspace;

use Craft;
use craft\events\RegisterComponentTypesEvent;

/**
 * Plugin represents the Rackspace Cloud Files volume plugin.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class Plugin extends \craft\base\Plugin
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Craft::$app->getVolumes()->on('registerVolumeTypes', function(RegisterComponentTypesEvent $event) {
            $event->types[] = Volume::class;
        });
    }
}
