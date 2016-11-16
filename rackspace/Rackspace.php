<?php

namespace craft\plugins\rackspace;

use Craft;
use craft\app\errors\VolumeException;

/**
 * Plugin represents the Rackspace volume plugin.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */

class Rackspace extends \craft\app\base\Plugin
{
    // Public Methods
    // =========================================================================
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Craft::$app->getVolumes()->on('registerVolumeTypes', [$this, 'registerVolumeType']);

        // TODO for now rely on Craft having all the needed stuff for us.
        //require __DIR__.'/vendor/autoload.php';
    }

    /**
     * Register the Volume Types
     *
     * @return void
     */
    public function registerVolumeType($event)
    {
        $event->types = array_merge($event->types, [
            Volume::className(),
        ]);
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
            if ($volume->className() == 'craft\app\volumes\MissingVolume' && $volume->expectedType == 'craft\app\volumes\Rackspace') {
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