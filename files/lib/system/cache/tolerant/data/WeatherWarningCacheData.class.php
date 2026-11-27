<?php

namespace wcf\system\cache\tolerant\data;

use wcf\data\weather\warning\WeatherWarning;

/**
 * Weather warning cache data structure.
 *
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 2.0.1
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
     * Returns the filename of the map of Germany for the given key.
     */
    public function getGermanyMap(string $key): string
    {
        return $this->germanyMaps[$key] ?? '';
    }
}
