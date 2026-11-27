<?php

namespace wcf\data\weather\warning\region;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit regions.
 *
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
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
