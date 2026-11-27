<?php

namespace wcf\system\cache\tolerant\data;

use wcf\data\weather\warning\WeatherWarning;

/**
 * Weather warning cache data structure.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 2.0.1
 */
final readonly class WeatherWarningCacheData
{
    /**
     * @param array<string, WeatherWarning[]> $warnings
     * @param array<string, string> $germanyMaps
     */
    public function __construct(
        public array $warnings,
        public int $time,
        public string $forestFireHazardIndexWBI,
        public string $grasslandFireIndex,
        public array $germanyMaps
    ) {
    }

    /**
     * Returns the filename of the map of Germany for the given key.
     */
    public function getGermanyMap(string $key): string
    {
        return $this->germanyMaps[$key] ?? '';
    }
}
