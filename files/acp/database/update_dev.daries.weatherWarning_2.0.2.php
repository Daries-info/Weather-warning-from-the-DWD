<?php

/**
 * Adds the unique index on the region name. Runs after the update script, which removes the duplicates.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 2.0.2
 */

use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\PartialDatabaseTable;

return [
    PartialDatabaseTable::create('wcf1_weather_warning_region')
        ->indices([
            DatabaseTableIndex::create('regionName')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['regionName']),
        ]),
];
