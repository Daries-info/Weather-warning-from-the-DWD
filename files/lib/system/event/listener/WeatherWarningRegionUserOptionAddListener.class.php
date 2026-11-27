<?php

namespace wcf\system\event\listener;

use wcf\acp\form\UserOptionAddForm;

/**
 * Inserts 'weatherWarningRegion' in the available option types of user option
 *
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
class WeatherWarningRegionUserOptionAddListener implements IParameterizedEventListener
{
    #[\Override]
    public function execute($eventObj, $className, $eventName, array &$parameters): void
    {
        UserOptionAddForm::$availableOptionTypes[] = 'weatherWarningRegion';
    }
}
