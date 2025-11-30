<?php

namespace wcf\data\weather\warning\region;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of regions.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @method      WeatherWarningRegion        current()
 * @method      WeatherWarningRegion[]      getObjects()
 * @method      WeatherWarningRegion|null   search($objectID)
 * @property    WeatherWarningRegion[]      $objects
 */
class WeatherWarningRegionList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = WeatherWarningRegion::class;
}
