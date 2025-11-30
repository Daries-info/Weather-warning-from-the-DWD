<?php

/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */

use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    DatabaseTable::create('wcf1_weather_warning_region')
        ->columns([
            ObjectIdDatabaseTableColumn::create('regionID'),
            NotNullVarchar255DatabaseTableColumn::create('regionName')
                ->defaultValue(''),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['regionID']),
        ]),
];
