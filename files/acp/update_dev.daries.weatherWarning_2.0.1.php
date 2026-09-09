<?php

use wcf\system\registry\RegistryHandler;

/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */

// Remove the values written by the old RegistryHandler-based storage (< 2.0.1);
// the data now lives exclusively in the WeatherWarningCache.
$packageName = 'dev.daries.weatherWarning';

$registryFields = [
    'weatherWarning',
    'weatherWarningTime',
    'forestFireHazardIndexWBI',
    'grasslandFireIndex',
    'germanyMap_blackIce',
    'germanyMap_frost',
    'germanyMap_fog',
    'germanyMap_heat',
    'germanyMap_map',
    'germanyMap_rain',
    'germanyMap_snow',
    'germanyMap_storm',
    'germanyMap_thaw',
    'germanyMap_thunder',
    'germanyMap_uv',
];

foreach ($registryFields as $field) {
    RegistryHandler::getInstance()->delete($packageName, $field);
}
