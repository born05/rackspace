<?php

namespace craft\rackspace;

use Craft;
use craft\errors\VolumeException;
use craft\events\RegisterComponentTypesEvent;
use craft\volumes\MissingVolume;

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

    /**
     * Convert the legacy Rackspace volumes
     *
     * @throws VolumeException
     * @return void
     */
    public function afterInstall()
    {
        $volumes = Craft::$app->getVolumes();
        $allVolumes = $volumes->getAllVolumes();

        foreach ($allVolumes as $volume) {
            /** @var Volume $volume */
            if ($volume instanceof MissingVolume && $volume->expectedType === 'craft\volumes\Rackspace') {
                /** @var Volume $convertedVolume */
                $convertedVolume = $volumes->createVolume([
                    'id' => $volume->id,
                    'type' => Volume::class,
                    'name' => $volume->name,
                    'handle' => $volume->handle,
                    'hasUrls' => $volume->hasUrls,
                    'url' => $volume->url,
                    'settings' => $volume->settings
                ]);
                $convertedVolume->setFieldLayout($volume->getFieldLayout());

                if (!$volumes->saveVolume($convertedVolume)) {
                   throw new VolumeException('Unable to convert the legacy “{volume}” Rackspace volume.', ['volume' => $volume->name]);
                }
            }
        }
    }
}
