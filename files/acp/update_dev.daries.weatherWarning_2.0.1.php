<?php

use wcf\system\cache\eager\WeatherWarningCache;

/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */

// Seed the new eager cache immediately so the first visitor after the update
// never hits the (blocking) cold-start rebuild path.
(new WeatherWarningCache())->rebuild();
