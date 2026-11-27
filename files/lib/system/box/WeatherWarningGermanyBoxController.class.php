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
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningGermanyBoxController extends AbstractBoxController implements IConditionBoxController
{
    protected string $map = 'map';

    protected bool $viewMapInfo = false;

    #[\Override]
    protected function getAdditionalData(): array
    {
        return [
            'map' => $this->map,
            'viewMapInfo' => $this->viewMapInfo,
        ];
    }

    #[\Override]
    public function getConditionDefinition(): string
    {
        return '';
    }

    #[\Override]
    public function getConditionObjectTypes(): array
    {
        return [];
    }

    #[\Override]
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

        return WCF::getTPL()->render('wcf', 'boxWeatherWarningGermanyConditions', [
            'map' => $this->map,
            'mapOptions' => $mapOptions,
            'viewMapInfo' => $this->viewMapInfo,
        ]);
    }

    #[\Override]
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
        if ($germanyMap === '') {
            return;
        }

        $this->content = WCF::getTPL()->render(
            'wcf',
            'boxWeatherWarningGermany',
            [
                'germanyMap' => $germanyMap,
                'map' => $this->map,
                'viewMapInfo' => $this->viewMapInfo,
            ]
        );
    }

    #[\Override]
    public function readConditions(): void
    {
        if (($_POST['map'] ?? '') !== '') {
            $this->map = StringUtil::trim($_POST['map']);
        }

        if ((int)($_POST['viewMapInfo'] ?? 0) === 1) {
            $this->viewMapInfo = true;
        }
    }

    #[\Override]
    public function setBox(Box $box, bool $setConditionData = true): void
    {
        parent::setBox($box);

        if ($setConditionData) {
            if (($this->box->additionalData['map'] ?? '') !== '') {
                $this->map = $this->box->additionalData['map'];
            }

            if ((int)($this->box->additionalData['viewMapInfo'] ?? 0) === 1) {
                $this->viewMapInfo = true;
            }
        }
    }

    #[\Override]
    public function validateConditions(): void
    {
    }
}
