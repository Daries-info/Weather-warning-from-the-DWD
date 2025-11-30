<?php

namespace wcf\system\weather\warning;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use wcf\data\weather\warning\WeatherWarning;
use wcf\system\exception\SystemException;
use wcf\system\io\HttpFactory;
use wcf\system\registry\RegistryHandler;
use wcf\system\SingletonFactory;
use wcf\util\JSON;

/**
 * Weather warning handler.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningHandler extends SingletonFactory
{
    /**
     * URL to the forest fire hazard index in Germany.
     */
    public const GERMANY_FORESTFIREHAZARDINDEXWBI_URL = 'https://www.dwd.de/DWD/warnungen/agrar/wbx/wbx_stationen.png';

    /**
     * URL to grassland fire index in Germany.
     */
    public const GERMANY_GRASSLANDFIREINDEX_URL = 'https://www.dwd.de/DWD/warnungen/agrar/glfi/glfi_stationen.png';

    /**
     * URL for regional weather warnings in Germany.
     */
    public const GERMANY_REGION_URL = 'https://www.dwd.de/DWD/warnungen/warnapp/json/warnings.json';

    /**
     * URLs for various warning cards in Germany.
     */
    public const GERMANY_MAP_URLS = [
        'blackIce' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_glatteis.png',
        'frost' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_frost.png',
        'fog' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_nebel.png',
        'heat' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_hitze.png',
        'map' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de.png',
        'rain' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_regen.png',
        'snow' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_schnee.png',
        'storm' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_sturm.png',
        'thaw' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_tauwetter.png',
        'thunder' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_gewitter.png',
        'uv' => 'https://www.dwd.de/DWD/warnungen/warnapp_gemeinden/json/warnungen_gemeinde_map_de_uv.png',
    ];

    /**
     * Package name for the registration action.
     */
    public const PACKAGE_NAME = "dev.daries.weatherWarning";

    /**
     * The HTTP client instance used for making requests.
     */
    private ClientInterface $httpClient;

    /**
     * Returns the forest fire hazard index.
     */
    public function getForestFireHazardIndexWBI(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "forestFireHazardIndexWBI") ?? "";
    }

    /**
     * Returns the map of Germany.
     */
    public function getGermanyMap(string $key): string
    {
        $key = \sprintf('germanyMap_%s', $key);

        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, $key) ?? "";
    }

    /**
     * Returns the grassland fire index.
     */
    public function getGrasslandFireIndex(): string
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "grasslandFireIndex") ?? "";
    }

    /**
     * Creates and configures an HTTP client with a timeout setting of 30 seconds.
     */
    private function getHttpClient(): ClientInterface
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = HttpFactory::makeClientWithTimeout(10);
        }

        return $this->httpClient;
    }

    /**
     * Returns the weather warnings.
     */
    public function getWeatherWarning(): array
    {
        $weatherWarning = RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherWarning");

        return $weatherWarning !== null ? \unserialize($weatherWarning) : [];
    }

    /**
     * Returns the time of the weather warnings.
     */
    public function getWeatherWarningTime(): int
    {
        return RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "weatherWarningTime") ?? 0;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $lastUpdate = RegistryHandler::getInstance()->get(self::PACKAGE_NAME, "lastUpdate");
        if ($lastUpdate === null || $lastUpdate < TIME_NOW - 900) {
            RegistryHandler::getInstance()->set(self::PACKAGE_NAME, "lastUpdate", TIME_NOW);

            if (WEATHER_WARNING_ENABLE_FOREST_FIRE_HAZARD_INDEX_WBI) {
                // load germany forest fire index wbi map
                $this->loadImage('forestFireHazardIndexWBI', self::GERMANY_FORESTFIREHAZARDINDEXWBI_URL);
            }

            if (WEATHER_WARNING_ENABLE_GRASSLAND_FIRE_INDEX) {
                // load germany grassland fire index map
                $this->loadImage('grasslandFireIndex', self::GERMANY_GRASSLANDFIREINDEX_URL);
            }

            // load various warning cards in Germany.
            foreach (self::GERMANY_MAP_URLS as $mapKey => $mapURL) {
                $mapKey = \sprintf('germanyMap_%s', $mapKey);
                $this->loadImage($mapKey, $mapURL);
            }

            // load region warning information
            $request = new Request('GET', self::GERMANY_REGION_URL, [
                'accept' => 'application/json',
            ]);

            $weatherWarning = [];
            try {
                $response = $this->getHttpClient()->send($request);
                $parsed = (string)$response->getBody();

                \preg_match('/warnWetter\.loadWarnings\((\{.*\})\);/', $parsed, $matches);
                $parsed = $matches[1] ?? "{}";

                try {
                    $weatherWarning = JSON::decode($parsed);
                } catch (SystemException $e) {
                    if (ENABLE_DEBUG_MODE) {
                        throw $e;
                    }
                }
            } catch (TransferException $e) {
                // nothings
            }

            if (!empty($weatherWarning)) {
                RegistryHandler::getInstance()->set(self::PACKAGE_NAME, 'weatherWarningTime', ($weatherWarning['time'] ?? 0) / 1000);

                $warnings = \array_merge_recursive(
                    $this->readWeatherWarning($weatherWarning['warnings'] ?? []),
                    $this->readWeatherWarning($weatherWarning['vorabInformation'] ?? [])
                );

                $this->sortWeatherWarnings($warnings);

                RegistryHandler::getInstance()->set(self::PACKAGE_NAME, "weatherWarning", \serialize($warnings));
            }
        }
    }

    /**
     * Loads an image from a specified URL and saves it as a base64-encoded string in the registry.
     */
    private function loadImage(string $name, string $url)
    {
        $dataString = "";
        $response = null;

        $request = new Request('GET', $url, ['accept' => 'image/*']);
        try {
            $response = $this->getHttpClient()->send($request);

            while (!$response->getBody()->eof()) {
                try {
                    $dataString .= $response->getBody()->read(8192);
                } catch (\RuntimeException $e) {
                    return;
                }
            }
        } catch (TransferException $e) {
            return;
        } finally {
            if ($response && $response->getBody()) {
                $response->getBody()->close();
            }

            if ($dataString !== "") {
                $dataString = \sprintf("data:image/png;base64,%s", \base64_encode($dataString));
            }

            RegistryHandler::getInstance()->set(
                self::PACKAGE_NAME,
                $name,
                $dataString
            );
        }
    }

    /**
     * Reads weather warnings and sorts by region.
     */
    private function readWeatherWarning(array $weatherWarning): array
    {
        $list = [];
        if (empty($weatherWarning)) {
            return $list;
        }

        foreach ($weatherWarning as $infos) {
            foreach ($infos as $info) {
                $weatherWarning = WeatherWarning::createWarning($info);
                $list[$weatherWarning->getRegionName()] ??= [];
                $list[$weatherWarning->getRegionName()][] = $weatherWarning;
            }
        }

        return $list;
    }

    /**
     * Sorts an array of WeatherWarning objects by region name and within each region by start time.
     */
    private function sortWeatherWarnings(array &$weatherWarnings): void
    {
        \ksort($weatherWarnings);

        foreach ($weatherWarnings as &$warnings) {
            \usort($warnings, static fn($a, $b) => $a->getStart() <=> $b->getStart());
        }
    }
}
