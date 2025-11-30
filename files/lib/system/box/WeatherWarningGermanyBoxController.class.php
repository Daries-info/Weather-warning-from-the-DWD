<?php

namespace wcf\system\box;

use wcf\data\box\Box;
use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;
use wcf\util\StringUtil;

/**
 * Box that shows the german warning weather map.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
class WeatherWarningGermanyBoxController extends AbstractBoxController implements IConditionBoxController
{
    protected string $map = "map";

    protected bool $viewMapInfo = false;

    /**
     * @inheritDoc
     */
    protected function getAdditionalData(): array
    {
        return [
            'map' => $this->map,
            'viewMapInfo' => $this->viewMapInfo,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConditionDefinition(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getConditionObjectTypes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getConditionsTemplate(): string
    {
        $mapOptions = [
            'blackIce' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.blackIce'),
            'frost' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.frost'),
            'fog' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.fog'),
            'heat' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.heat'),
            'map' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map'),
            'rain' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.rain'),
            'snow' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.snow'),
            'storm' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.storm'),
            'thaw' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.thaw'),
            'thunder' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.thunder'),
            'uv' => WCF::getLanguage()->get('wcf.weatherWarning.dwd.map.uv'),
        ];

        \asort($mapOptions);

        return WCF::getTPL()->fetch('boxWeatherWarningGermanyConditions', 'wcf', [
            'map' => $this->map,
            'mapOptions' => $mapOptions,
            'viewMapInfo' => $this->viewMapInfo,
        ], true);
    }

    /**
     * @inheritDoc
     */
    protected function loadContent(): void
    {
        if (!MODULE_WEATHER_WARNING) {
            return;
        }

        $user = WCF::getUser();
        if ($user->userID && !$user->getUserOption('weatherWarningGermanyEnable')) {
            return;
        }

        $germanyMap = WeatherWarningHandler::getInstance()->getGermanyMap($this->map);

        $this->content = WCF::getTPL()->fetch(
            'boxWeatherWarningGermany',
            'wcf',
            [
                'germanyMap' => $germanyMap,
                'map' => $this->map,
                'viewMapInfo' => $this->viewMapInfo,
            ],
            true
        );
    }

    /**
     * @inheritDoc
     */
    public function readConditions(): void
    {
        if (!empty($_POST['map'])) {
            $this->map = StringUtil::trim($_POST['map']);
        }

        if (!empty($_POST['viewMapInfo'])) {
            $this->viewMapInfo = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function setBox(Box $box, $setConditionData = true): void
    {
        parent::setBox($box);

        if ($setConditionData) {
            if ($this->box->map) {
                $this->map = $this->box->map;
            }

            if ($this->box->viewMapInfo) {
                $this->viewMapInfo = $this->box->viewMapInfo;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateConditions(): void
    {
    }
}
