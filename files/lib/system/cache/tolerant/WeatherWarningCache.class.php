<?php

namespace wcf\system\cache\tolerant;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use wcf\data\weather\warning\WeatherWarning;
use wcf\system\cache\tolerant\data\WeatherWarningCacheData;
use wcf\system\io\HttpFactory;

/**
 * Tolerant cache implementation for DWD weather warnings, warning maps and fire indices.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 2.0.1
 *
 * @extends AbstractTolerantCache<WeatherWarningCacheData>
 */
final class WeatherWarningCache extends AbstractTolerantCache
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
     * Rebuilds no more often than every 15 minutes, matching the DWD's own update cadence.
     */
    private const LIFETIME = 900;

    private ClientInterface $httpClient;

    #[\Override]
    public function getLifetime(): int
    {
        return self::LIFETIME;
    }

    /**
     * Fetches all warnings, maps and fire indices from the DWD.
     *
     * If any single resource cannot be loaded, the whole rebuild is aborted and the
     * previously cached (now merely stale) data continues to be served, instead of
     * overwriting good data with a partial or empty result.
     */
    #[\Override]
    protected function rebuildCacheData(): WeatherWarningCacheData
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

        [$warnings, $time] = $this->loadWeatherWarnings();

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
     * Loads an image from the given URL and returns it as a base64-encoded data URI.
     *
     * @throws \RuntimeException if the image could not be loaded.
     */
    private function loadImage(string $url): string
    {
        $dataString = '';
        $response = null;

        $request = new Request('GET', $url, ['accept' => 'image/*']);
        try {
            $response = $this->getHttpClient()->send($request);

            while (!$response->getBody()->eof()) {
                $dataString .= $response->getBody()->read(8192);
            }
        } catch (TransferException|\RuntimeException $e) {
            throw new \RuntimeException(\sprintf("Failed to load weather warning image from '%s'.", $url), previous: $e);
        } finally {
            $response?->getBody()->close();
        }

        if ($dataString === '') {
            throw new \RuntimeException(\sprintf("Received an empty weather warning image from '%s'.", $url));
        }

        return \sprintf('data:image/png;base64,%s', \base64_encode($dataString));
    }

    /**
     * Loads and parses the regional weather warnings.
     *
     * @return array{0: array<string, WeatherWarning[]>, 1: int}
     * @throws \RuntimeException if the warnings could not be loaded or parsed.
     */
    private function loadWeatherWarnings(): array
    {
        $request = new Request('GET', self::GERMANY_REGION_URL, [
            'accept' => 'application/json',
        ]);

        try {
            $response = $this->getHttpClient()->send($request);
            $parsed = (string)$response->getBody();
        } catch (TransferException $e) {
            throw new \RuntimeException('Failed to load weather warnings from the DWD.', previous: $e);
        }

        \preg_match('/warnWetter\.loadWarnings\((\{.*\})\);/', $parsed, $matches);
        $parsed = $matches[1] ?? '{}';

        try {
            $weatherWarning = \json_decode($parsed, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Failed to parse weather warnings from the DWD.', previous: $e);
        }

        if ($weatherWarning === []) {
            throw new \RuntimeException('Received no weather warning data from the DWD.');
        }

        $warnings = \array_merge_recursive(
            $this->readWeatherWarning($weatherWarning['warnings'] ?? [], 'warning'),
            $this->readWeatherWarning($weatherWarning['vorabInformation'] ?? [], 'preliminary')
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
    private function readWeatherWarning(array $weatherWarning, string $sourcePrefix): array
    {
        $list = [];
        if ($weatherWarning === []) {
            return $list;
        }

        $tempID = 0;
        foreach ($weatherWarning as $infos) {
            foreach ($infos as $info) {
                $info['warningID'] = \sprintf('temp-%s-%d', $sourcePrefix, $tempID++);

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
}
