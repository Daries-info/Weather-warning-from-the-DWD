<?php

namespace wcf\event\listView\user;

use wcf\event\IPsr14Event;
use wcf\system\listView\user\WeatherWarningListView;

/**
 * Indicates that the weather warning list view has been initialized.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningListViewInitialized implements IPsr14Event
{
    public function __construct(public readonly WeatherWarningListView $listView) {}
}
