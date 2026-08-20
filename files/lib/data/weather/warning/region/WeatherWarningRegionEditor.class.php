<?php

namespace wcf\data\weather\warning\region;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit regions.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @extends DatabaseObjectEditor<WeatherWarningRegion>
 */
class WeatherWarningRegionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = WeatherWarningRegion::class;
}
