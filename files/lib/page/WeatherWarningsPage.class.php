<?php

namespace wcf\page;

use wcf\system\event\EventHandler;
use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * Shows the weather warnings.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.dev - Free License <https://daries.dev/en/license-for-free-plugins>
 */
class WeatherWarningsPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_WEATHER_WARNING'];

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 10;

    /**
     * list of weather warnings
     */
    public array $weatherWarnings;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'weatherWarnings' => $this->weatherWarnings,
            'warningTime' => WeatherWarningHandler::getInstance()->getWeatherWarningTime(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function countItems(): int
    {
        // call countItems event
        EventHandler::getInstance()->fireAction($this, 'countItems');

        return \count($this->weatherWarnings);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        AbstractPage::readData();

        $this->weatherWarnings = WeatherWarningHandler::getInstance()->getWeatherWarning();

        $this->calculateNumberOfPages();

        $this->weatherWarnings = \array_slice(
            $this->weatherWarnings,
            $this->startIndex - 1,
            $this->endIndex - $this->startIndex + 1,
            true
        );
    }
}
