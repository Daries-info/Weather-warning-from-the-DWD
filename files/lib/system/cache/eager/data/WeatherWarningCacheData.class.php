<?php

namespace wcf\system\cache\eager\data;

use wcf\data\weather\warning\WeatherWarning;

/**
 * Weather warning cache data structure.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
final class WeatherWarningCacheData
{
    /**
     * @param array<string, WeatherWarning[]> $warnings
     * @param array<string, string> $germanyMaps
     */
    public function __construct(
        public readonly array $warnings,
        public readonly int $time,
        public readonly string $forestFireHazardIndexWBI,
        public readonly string $grasslandFireIndex,
        public readonly array $germanyMaps
    ) {
    }

    /**
     * Returns the map of Germany for the given key.
     */
    public function getGermanyMap(string $key): string
    {
        return $this->germanyMaps[$key] ?? '';
    }
}
