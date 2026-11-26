<?php

namespace wcf\system\listView\user;

use wcf\data\DatabaseObjectList;
use wcf\data\weather\warning\WeatherWarning;
use wcf\event\listView\user\WeatherWarningListViewInitialized;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\listView\AbstractListView;
use wcf\system\listView\ListViewSortField;
use wcf\system\view\filter\BooleanFilter;
use wcf\system\view\filter\MultipleSelectFilter;
use wcf\system\view\filter\SelectFilter;
use wcf\system\WCF;
use wcf\system\weather\warning\UserWeatherWarningHandler;
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
    /**
     * Language items of the warning types of the DWD.
     */
    private const array TYPE_LANGUAGE_ITEMS = [
        0 => 'wcf.weatherWarning.dwd.map.thunder',
        1 => 'wcf.weatherWarning.dwd.map.storm',
        2 => 'wcf.weatherWarning.dwd.map.rain',
        3 => 'wcf.weatherWarning.dwd.map.snow',
        4 => 'wcf.weatherWarning.dwd.map.fog',
        5 => 'wcf.weatherWarning.dwd.map.frost',
        6 => 'wcf.weatherWarning.dwd.map.blackIce',
        7 => 'wcf.weatherWarning.dwd.map.thaw',
        8 => 'wcf.weatherWarning.dwd.map.heat',
        9 => 'wcf.weatherWarning.dwd.map.uv',
    ];

    /** @var array<string, string> */
    private array $availableRegions;

    /** @var array<string, string> */
    private array $availableStates;

    /** @var array<int, string> */
    private array $availableTypes;

    public function __construct()
    {
        $this->addAvailableFilters([
            new class($this->getAvailableRegions(), 'region', 'wcf.weatherWarning.region') extends MultipleSelectFilter {
                #[\Override]
                public function getFormField(): AbstractFormField
                {
                    /** @var MultipleSelectionFormField $formField */
                    $formField = parent::getFormField();
                    $formField->filterable(true);

                    return $formField;
                }
            },
            new MultipleSelectFilter($this->getAvailableStates(), 'state', 'wcf.weatherWarning.state', labelLanguageItems: false),
            new MultipleSelectFilter($this->getAvailableTypes(), 'type', 'wcf.weatherWarning.type', labelLanguageItems: false),
            new SelectFilter([
                2 => 'wcf.weatherWarning.minLevel.2',
                3 => 'wcf.weatherWarning.minLevel.3',
                4 => 'wcf.weatherWarning.minLevel.4',
            ], 'minLevel', 'wcf.weatherWarning.minLevel'),
            new BooleanFilter('onlyActive', 'wcf.weatherWarning.onlyActive'),
            new BooleanFilter('hidePreliminary', 'wcf.weatherWarning.hidePreliminary'),
        ]);

        $this->addAvailableSortField(new ListViewSortField('regionName', 'wcf.weatherWarning.region'));
        $this->setDefaultSortField('regionName');
        $this->setItemsPerPage(50);
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

    /**
     * @return array<string, string>
     */
    private function getAvailableStates(): array
    {
        if (!isset($this->availableStates)) {
            $this->availableStates = [];
            foreach (WeatherWarningHandler::getInstance()->getWeatherWarning() as $warnings) {
                foreach ($warnings as $warning) {
                    $this->availableStates[$warning->getState()] = $warning->getState();
                }
            }

            \ksort($this->availableStates);
        }

        return $this->availableStates;
    }

    /**
     * @return array<int, string>
     */
    private function getAvailableTypes(): array
    {
        if (!isset($this->availableTypes)) {
            $this->availableTypes = [];
            foreach (WeatherWarningHandler::getInstance()->getWeatherWarning() as $warnings) {
                foreach ($warnings as $warning) {
                    $type = $warning->getType();
                    if (isset(self::TYPE_LANGUAGE_ITEMS[$type])) {
                        $this->availableTypes[$type] = WCF::getLanguage()->get(self::TYPE_LANGUAGE_ITEMS[$type]);
                    }
                }
            }

            \asort($this->availableTypes);
        }

        return $this->availableTypes;
    }

    /**
     * Returns the conditions of the active filters that each weather warning must meet,
     * the region filter is already applied when the data is loaded.
     *
     * @return list<\Closure(WeatherWarning): bool>
     */
    private function getFilterConditions(): array
    {
        $activeFilters = $this->getActiveFilters();
        $conditions = [];

        if (($activeFilters['state'] ?? '') !== '') {
            $states = \explode(',', $activeFilters['state']);
            $conditions[] = static fn(WeatherWarning $warning): bool => \in_array($warning->getState(), $states, true);
        }

        if (($activeFilters['type'] ?? '') !== '') {
            $types = \array_map(\intval(...), \explode(',', $activeFilters['type']));
            $conditions[] = static fn(WeatherWarning $warning): bool => \in_array($warning->getType(), $types, true);
        }

        if (($activeFilters['minLevel'] ?? '') !== '') {
            $minLevel = (int)$activeFilters['minLevel'];
            $conditions[] = static fn(WeatherWarning $warning): bool => $warning->getSeverityLevel() >= $minLevel;
        }

        if (isset($activeFilters['onlyActive'])) {
            $conditions[] = static fn(WeatherWarning $warning): bool => $warning->getStart() <= TIME_NOW;
        }

        if (isset($activeFilters['hidePreliminary'])) {
            $conditions[] = static fn(WeatherWarning $warning): bool => !$warning->isPreliminary();
        }

        return $conditions;
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
            // The warnings of the own region always come first.
            $ownRegionComparison = $this->isOwnRegion($b) <=> $this->isOwnRegion($a);
            if ($ownRegionComparison !== 0) {
                return $ownRegionComparison;
            }

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

    /**
     * Returns the region of the user or the default region, if the user has not set one.
     *
     * @since 2.0.2
     */
    public function getOwnRegion(): string
    {
        return UserWeatherWarningHandler::getInstance()->getRegion();
    }

    /**
     * Returns the weather warnings of the current page that belong to the own region.
     *
     * @return array<int, WeatherWarning>
     * @since 2.0.2
     */
    public function getOwnRegionItems(): array
    {
        return \array_filter($this->getItems(), $this->isOwnRegion(...));
    }

    /**
     * Returns the weather warnings of the current page that do not belong to the own region.
     *
     * @return array<int, WeatherWarning>
     * @since 2.0.2
     */
    public function getOtherRegionItems(): array
    {
        return \array_filter($this->getItems(), fn(WeatherWarning $warning): bool => !$this->isOwnRegion($warning));
    }

    /**
     * Returns true if the own region is shown in a section of its own. Without warnings for the own region,
     * the section is only shown on the first page without filters, to point out that there are no warnings.
     *
     * @since 2.0.2
     */
    public function hasOwnRegionSection(): bool
    {
        if ($this->getOwnRegion() === '') {
            return false;
        }

        if ($this->getOwnRegionItems() !== []) {
            return true;
        }

        return $this->getPageNo() === 1 && $this->getActiveFilters() === [];
    }

    /**
     * Returns true if the user is shown the default region, but could set a region in the profile.
     *
     * @since 2.0.2
     */
    public function canSetOwnRegion(): bool
    {
        return WCF::getUser()->userID !== 0
            && UserWeatherWarningHandler::getInstance()->isDefaultRegion()
            && WCF::getUserProfileHandler()->canEditOwnProfile();
    }

    private function isOwnRegion(WeatherWarning $warning): bool
    {
        return $warning->getRegionName() === $this->getOwnRegion();
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return parent::isAccessible() && \MODULE_WEATHER_WARNING;
    }

    /**
     * @return array<int, WeatherWarning>
     */
    protected function loadDataSource(): array
    {
        $weatherWarnings = WeatherWarningHandler::getInstance()->getWeatherWarning();

        $regionFilterValue = $this->getActiveFilters()['region'] ?? '';
        if ($regionFilterValue !== '') {
            $weatherWarnings = \array_intersect_key($weatherWarnings, \array_flip(\explode(',', $regionFilterValue)));
        }

        $conditions = $this->getFilterConditions();

        $list = [];
        foreach ($weatherWarnings as $warnings) {
            foreach ($warnings as $warning) {
                foreach ($conditions as $condition) {
                    if (!$condition($warning)) {
                        continue 2;
                    }
                }

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
