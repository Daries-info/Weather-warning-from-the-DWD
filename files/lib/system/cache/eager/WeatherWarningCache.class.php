<?php

namespace wcf\system\cache\eager;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use wcf\data\weather\warning\WeatherWarning;
use wcf\system\cache\eager\data\WeatherWarningCacheData;
use wcf\system\io\HttpFactory;

/**
 * Eager cache implementation for DWD weather warnings, warning maps and fire indices.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @extends AbstractEagerCache<WeatherWarningCacheData>
 */
final class WeatherWarningCache extends AbstractEagerCache
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

    private ClientInterface $httpClient;

    /**
     * Prevents infinite recursion when {@see self::getPreviousCacheData()} falls back to
     * {@see self::getCache()} while a rebuild for this very instance is already in progress.
     */
    private bool $isReadingPreviousCacheData = false;

    #[\Override]
    protected function getCacheData(): WeatherWarningCacheData
    {
        $forestFireHazardIndexWBI = '';
        if (WEATHER_WARNING_ENABLE_FOREST_FIRE_HAZARD_INDEX_WBI) {
            $forestFireHazardIndexWBI = $this->loadImage(self::GERMANY_FORESTFIREHAZARDINDEXWBI_URL);
        }

        $grasslandFireIndex = '';
        if (WEATHER_WARNING_ENABLE_GRASSLAND_FIRE_INDEX) {
            $grasslandFireIndex = $this->loadImage(self::GERMANY_GRASSLANDFIREINDEX_URL);
        }

        $germanyMaps = [];
        foreach (self::GERMANY_MAP_URLS as $mapKey => $mapURL) {
            $germanyMaps[$mapKey] = $this->loadImage($mapURL);
        }

        // The warnings are the safety-relevant part of this cache: if the DWD cannot be
        // reached, keep the last known good data instead of wiping it to an empty list.
        $warnings = [];
        $time = 0;
        $freshWeatherWarning = $this->loadWeatherWarnings();
        if ($freshWeatherWarning !== null) {
            [$warnings, $time] = $freshWeatherWarning;
        } else {
            $previous = $this->getPreviousCacheData();
            if ($previous !== null) {
                $warnings = $previous->warnings;
                $time = $previous->time;
            }
        }

        return new WeatherWarningCacheData($warnings, $time, $forestFireHazardIndexWBI, $grasslandFireIndex, $germanyMaps);
    }

    /**
     * Creates and configures an HTTP client with a timeout setting of 2 seconds.
     */
    private function getHttpClient(): ClientInterface
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = HttpFactory::makeClientWithTimeout(2);
        }

        return $this->httpClient;
    }

    /**
     * Loads an image from the given URL and returns it as a base64-encoded data URI,
     * or an empty string if the image could not be loaded.
     */
    private function loadImage(string $url): string
    {
        $dataString = '';
        $response = null;

        $request = new Request('GET', $url, ['accept' => 'image/*']);
        try {
            $response = $this->getHttpClient()->send($request);

            while (!$response->getBody()->eof()) {
                try {
                    $dataString .= $response->getBody()->read(8192);
                } catch (\RuntimeException $e) {
                    return '';
                }
            }
        } catch (TransferException $e) {
            return '';
        } finally {
            $response?->getBody()->close();
        }

        if ($dataString === '') {
            return '';
        }

        return \sprintf('data:image/png;base64,%s', \base64_encode($dataString));
    }

    /**
     * Loads and parses the regional weather warnings.
     *
     * @return array{0: array<string, WeatherWarning[]>, 1: int}|null `null` if the warnings could not be loaded.
     */
    private function loadWeatherWarnings(): ?array
    {
        $request = new Request('GET', self::GERMANY_REGION_URL, [
            'accept' => 'application/json',
        ]);

        $weatherWarning = [];
        try {
            $response = $this->getHttpClient()->send($request);
            $parsed = (string)$response->getBody();

            \preg_match('/warnWetter\.loadWarnings\((\{.*\})\);/', $parsed, $matches);
            $parsed = $matches[1] ?? '{}';

            try {
                $weatherWarning = \json_decode($parsed, true, flags: \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                if (ENABLE_DEBUG_MODE) {
                    throw $e;
                }

                return null;
            }
        } catch (TransferException $e) {
            return null;
        }

        if ($weatherWarning === []) {
            return null;
        }

        $warnings = \array_merge_recursive(
            $this->readWeatherWarning($weatherWarning['warnings'] ?? []),
            $this->readWeatherWarning($weatherWarning['vorabInformation'] ?? [])
        );

        $this->sortWeatherWarnings($warnings);

        return [$warnings, (int)(($weatherWarning['time'] ?? 0) / 1000)];
    }

    /**
     * Reads weather warnings and sorts by region.
     *
     * @param array<string, array<int, array<string, mixed>>> $weatherWarning
     * @return array<string, WeatherWarning[]>
     */
    private function readWeatherWarning(array $weatherWarning): array
    {
        $list = [];
        if ($weatherWarning === []) {
            return $list;
        }

        $tempID = 0;
        foreach ($weatherWarning as $infos) {
            foreach ($infos as $info) {
                $info['warningID'] = 'temp-' . $tempID++;

                $weatherWarningObject = WeatherWarning::createWarning($info);
                $list[$weatherWarningObject->getRegionName()] ??= [];
                $list[$weatherWarningObject->getRegionName()][] = $weatherWarningObject;
            }
        }

        return $list;
    }

    /**
     * Sorts an array of WeatherWarning objects by region name and within each region by start time.
     *
     * @param array<string, WeatherWarning[]> $weatherWarnings
     */
    private function sortWeatherWarnings(array &$weatherWarnings): void
    {
        \ksort($weatherWarnings);

        foreach ($weatherWarnings as &$warnings) {
            \usort($warnings, static fn($a, $b) => $a->getStart() <=> $b->getStart());
        }
    }

    /**
     * Returns the currently cached data, if any, without triggering a nested rebuild.
     */
    private function getPreviousCacheData(): ?WeatherWarningCacheData
    {
        if ($this->isReadingPreviousCacheData) {
            return null;
        }

        $this->isReadingPreviousCacheData = true;
        try {
            return $this->getCache();
        } finally {
            $this->isReadingPreviousCacheData = false;
        }
    }
}
