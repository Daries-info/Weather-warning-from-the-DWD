<?php

namespace wcf\data\weather\warning\region;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit regions.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 *
 * @method static   WeatherWarningRegion    create(array $parameters = [])
 * @method          WeatherWarningRegion    getDecoratedObject()
 * @mixin           WeatherWarningRegion
 */
class WeatherWarningRegionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = WeatherWarningRegion::class;
}
