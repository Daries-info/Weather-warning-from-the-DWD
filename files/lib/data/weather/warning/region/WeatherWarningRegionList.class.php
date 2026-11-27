<?php

namespace wcf\data\weather\warning\region;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of regions.
 *
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @extends DatabaseObjectList<WeatherWarningRegion>
 */
class WeatherWarningRegionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = WeatherWarningRegion::class;
}
