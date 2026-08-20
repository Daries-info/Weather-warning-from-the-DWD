<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\system\cache\eager\WeatherWarningCache;

/**
 * Refreshes the DWD weather warning cache.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 1.5.4
 */
final class RefreshDWDWarningsCronjob extends AbstractCronjob
{
    #[\Override]
    public function execute(Cronjob $cronjob): void
    {
        parent::execute($cronjob);

        (new WeatherWarningCache())->rebuild();
    }
}
