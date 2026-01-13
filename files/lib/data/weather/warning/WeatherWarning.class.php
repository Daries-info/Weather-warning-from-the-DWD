<?php

namespace wcf\data\weather\warning;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a weather warning.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 *
 * @property-read string $warningID The unique ID of the weather warning.
 * @property-read int $type The type of the weather warning.
 * @property-read int $level The level of severity of the weather warning.
 * @property-read int $start The start time of the weather warning.
 * @property-read int|null $end The end time of the weather warning.
 * @property-read string $event The event description of the weather warning.
 * @property-read string $regionName The name of the region affected by the weather warning.
 * @property-read string $state The state affected by the weather warning.
 * @property-read string $stateShort The abbreviated state name.
 * @property-read string $headline The headline of the weather warning.
 * @property-read string $description The detailed description of the weather warning.
 * @property-read string $instruction The safety instructions related to the weather warning.
 * @property-read int|null $altitudeStart The starting altitude affected by the weather warning.
 * @property-read int|null $altitudeEnd The ending altitude affected by the weather warning.
 */
final class WeatherWarning extends DatabaseObject
{
    protected static $databaseTableIndexName = 'warningID';

    /**
     * Creates a new instance of WeatherWarning class.
     *
     * @param array<string, mixed> $row
     */
    protected function __construct(array $row)
    {
        parent::__construct(null, $row);
    }

    /**
     * Creates a new WeatherWarning instance from an associative array.
     * 
     * @param array<string, mixed> $warning
     */
    public static function createWarning(array $warning): self
    {
        return new self(
            [
                'altitudeEnd' => $warning['altitudeEnd'],
                'altitudeStart' => $warning['altitudeStart'],
                'description' => $warning['description'],
                'end' => ($warning['end'] / 1000),
                'event' => $warning['event'],
                'headline' => $warning['headline'],
                'instruction' => $warning['instruction'],
                'level' => $warning['level'],
                'regionName' => $warning['regionName'],
                'start' => ($warning['start'] / 1000),
                'state' => $warning['state'],
                'stateShort' => $warning['stateShort'],
                'warningID' => $warning['warningID'],
                'type' => $warning['type']
            ]
        );
    }

    public function getAltitudeEnd(): ?int
    {
        return $this->altitudeEnd;
    }

    public function getAltitudeStart(): ?int
    {
        return $this->altitudeStart;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getHeadline(): string
    {
        return $this->headline;
    }

    public function getIcon(): string
    {
        $level = $this->getLevel();

        // individual level
        switch ($this->getType()) {
            case 8:
                if ($level > 38) {
                    $level = 2;
                } else {
                    $level = 1;
                }
                break;

            case 9:
                $level = 1;
                break;
        }

        return \sprintf(
            '<img src="%simages/weather/icon_%d_%d.png" alt="">',
            WCF::getPath(),
            $this->getType(),
            $level
        );
    }

    public function getInstruction(): string
    {
        return $this->instruction;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getRegionName(): string
    {
        return $this->regionName;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getStateShort(): string
    {
        return $this->stateShort;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
