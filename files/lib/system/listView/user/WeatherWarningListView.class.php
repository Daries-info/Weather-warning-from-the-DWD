<?php

namespace wcf\system\listView\user;

use wcf\data\DatabaseObjectList;
use wcf\data\weather\warning\WeatherWarning;
use wcf\event\listView\user\WeatherWarningListViewInitialized;
use wcf\system\listView\AbstractListView;
use wcf\system\listView\ListViewSortField;
use wcf\system\view\filter\SelectFilter;
use wcf\system\WCF;
use wcf\system\weather\warning\WeatherWarningHandler;

/**
 * List view for the list of weather warnings.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @extends AbstractListView<WeatherWarning, DatabaseObjectList<WeatherWarning>>
 */
class WeatherWarningListView extends AbstractListView
{
    /** @var array<string, string> */
    private array $availableRegions;

    public function __construct()
    {
        $this->addAvailableFilters([
            new SelectFilter($this->getAvailableRegions(), 'region', 'wcf.weatherWarning.region'),
        ]);

        $this->addAvailableSortField(new ListViewSortField('regionName', 'wcf.weatherWarning.region'));
        $this->setDefaultSortField('regionName');
    }

    #[\Override]
    protected function applyFilters(): void
    {
        // Overwrite the default filtering, as this is already applied when the data is loaded.
    }

    /**
     * @return DatabaseObjectList<WeatherWarning>
     */
    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new class extends DatabaseObjectList {
            public $className = WeatherWarning::class;
        };
    }

    #[\Override]
    public function countItems(): int
    {
        if (!isset($this->objectCount)) {
            $this->getObjectList();
        }

        return $this->objectCount;
    }

    /**
     * @return array<string, string>
     */
    private function getAvailableRegions(): array
    {
        if (!isset($this->availableRegions)) {
            $this->availableRegions = [];
            $weatherWarnings = WeatherWarningHandler::getInstance()->getWeatherWarning();
            foreach ($weatherWarnings as $regionName => $warnings) {
                $this->availableRegions[$regionName] = $regionName;
            }
        }

        return $this->availableRegions;
    }

    #[\Override]
    protected function getInitializedEvent(): WeatherWarningListViewInitialized
    {
        return new WeatherWarningListViewInitialized($this);
    }

    #[\Override]
    public function getItems(): array
    {
        if (!isset($this->objects)) {
            $this->getObjectList();
        }

        return $this->objects;
    }

    #[\Override]
    protected function initObjectList(): void
    {
        $this->objectList = $this->createObjectList();
        $this->fireInitializedEvent();
        $this->validate();

        $objects = $this->loadDataSource();
        $this->objectCount = \count($objects);
        \uasort($objects, function (WeatherWarning $a, WeatherWarning $b) {
            $order = $this->getSortOrder() === 'ASC' ? 1 : -1;

            // Sort by regionName first
            $regionComparison = \strcmp($a->getRegionName(), $b->getRegionName());
            if ($regionComparison !== 0) {
                return $regionComparison * $order;
            }

            // If regionName is the same, sort by start
            return ($a->getStart() <=> $b->getStart()) * $order;
        });
        $this->objects = \array_slice($objects, ($this->getPageNo() - 1) * $this->getItemsPerPage(), $this->getItemsPerPage());
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return !!\MODULE_WEATHER_WARNING;
    }

    /**
     * @return array<int, WeatherWarning>
     */
    protected function loadDataSource(): array
    {
        $weatherWarnings = WeatherWarningHandler::getInstance()->getWeatherWarning();

        $region = $this->getActiveFilters()['region'] ?? '';
        if ($region !== '') {
            $list = [];
            foreach ($this->getAvailableRegions() as $availableRegion) {
                if (\str_contains($availableRegion, $region)) {
                    foreach ($weatherWarnings[$availableRegion] as $warning) {
                        $list[$warning->getObjectID()] = $warning;
                    }
                }
            }

            return $list;
        }

        $list = [];
        foreach ($weatherWarnings as $warnings) {
            foreach ($warnings as $warning) {
                $list[$warning->getObjectID()] = $warning;
            }
        }

        return $list;
    }

    #[\Override]
    public function renderItems(): string
    {
        return WCF::getTPL()->render('wcf', 'weatherWarningItems', ['view' => $this]);
    }
}
