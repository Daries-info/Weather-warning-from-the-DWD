<?php

use wcf\system\WCF;

/**
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */

/** @var array<int, string> $regions */
$regions = require __DIR__ . '/regions_dev.daries.weatherWarning.php';

$sql = "INSERT INTO wcf1_weather_warning_region
                    (regionName)
        VALUES      (?)";
$statement = WCF::getDB()->prepare($sql);

WCF::getDB()->beginTransaction();
foreach ($regions as $regionName) {
    $statement->execute([$regionName]);
}
WCF::getDB()->commitTransaction();
