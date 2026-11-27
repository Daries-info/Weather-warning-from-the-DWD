<?php

namespace wcf\system\option;

use wcf\data\option\Option;
use wcf\data\weather\warning\region\WeatherWarningRegionList;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Option type implementation for region input fields.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
class WeatherWarningRegionOptionType extends TextOptionType
{
    #[\Override]
    public function getFormElement(Option $option, $value): string
    {
        return WCF::getTPL()->render('wcf', 'weatherWarningRegionOptionType', [
            'option' => $option,
            'inputType' => $this->inputType,
            'inputClass' => $this->inputClass,
            'value' => $value,
        ]);
    }

    #[\Override]
    public function validate(Option $option, $newValue): void
    {
        if ($newValue !== null && $newValue !== '') {
            $regionList = new WeatherWarningRegionList();
            $regionList->getConditionBuilder()->add('regionName = ?', [$newValue]);
            $count = $regionList->countObjects();

            if (!$count) {
                throw new UserInputException($option->optionName, 'noExist');
            }
        }
    }
}
