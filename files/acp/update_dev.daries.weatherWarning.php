<?php

use wcf\data\box\Box;
use wcf\data\box\BoxAction;

/**
 * @author  Marco Daries", Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */

$box = Box::getBoxByIdentifier('dev.daries.weatherWarning.germany');
if ($box !== false) {
    $additionalData = $box->additionalData;
    $additionalData['viewMapInfo'] = 1;

    $action = new BoxAction([$box], 'update', [
        'data' => [
            'additionalData' => \serialize($additionalData),
        ],
    ]);
    $action->executeAction();
}
