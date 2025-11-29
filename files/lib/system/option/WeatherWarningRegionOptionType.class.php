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
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningRegionOptionType extends TextOptionType
{
    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value): string
    {
        WCF::getTPL()->assign([
            'option' => $option,
            'inputType' => $this->inputType,
            'inputClass' => $this->inputClass,
            'value' => $value,
        ]);

        return WCF::getTPL()->fetch('weatherWarningRegionOptionType');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue): void
    {
        if (!empty($newValue)) {
            $regionList = new WeatherWarningRegionList();
            $regionList->getConditionBuilder()->add('regionName = ?', [$newValue]);
            $count = $regionList->countObjects();

            if (!$count) {
                throw new UserInputException($option->optionName, 'noExist');
            }
        }
    }
}
