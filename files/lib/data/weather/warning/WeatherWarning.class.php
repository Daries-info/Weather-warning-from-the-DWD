<?php

namespace wcf\data\weather\warning;

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

    private const int TYPE_HEAT = 8;

    private const int TYPE_UV = 9;

    /**
     * A preliminary information always announces a severe weather warning.
     */
    private const int PRELIMINARY_LEVEL = 3;

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
                'end' => $warning['end'] !== null ? \intdiv((int)$warning['end'], 1000) : null,
                'event' => $warning['event'],
                'headline' => $warning['headline'],
                'instruction' => $warning['instruction'],
                'level' => $warning['level'],
                'regionName' => $warning['regionName'],
                'start' => \intdiv((int)$warning['start'], 1000),
                'state' => $warning['state'],
                'stateShort' => $warning['stateShort'],
                'warningID' => $warning['warningID'],
                'type' => $warning['type'],
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

    /**
     * Returns the URL of the icon for the type and warning level.
     *
     * @since 2.0.2
     */
    public function getIconUrl(): string
    {
        return \sprintf('%simages/weather/icon_%d_%d.webp', WCF::getPath(), $this->getType(), $this->getSeverityLevel());
    }

    public function getInstruction(): string
    {
        return $this->instruction;
    }

    /**
     * Returns true if the warning has an end that has already passed.
     *
     * @since 2.0.2
     */
    public function isExpired(): bool
    {
        $end = $this->getEnd();

        return $end !== null && $end < TIME_NOW;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Returns the warning level of the DWD (1 to 4), or 0 for a preliminary information.
     *
     * The `level` of `warnings.json` is the warning level plus one, except for heat and UV warnings,
     * which use levels of their own.
     *
     * @since 2.0.2
     */
    public function getWarnLevel(): int
    {
        return match ($this->getType()) {
            self::TYPE_HEAT => $this->getLevel() > 38 ? 2 : 1,
            self::TYPE_UV => 1,
            default => \max(0, \min(4, $this->getLevel() - 1)),
        };
    }

    /**
     * Returns the highest warning level the DWD uses for the type of this warning.
     *
     * @since 2.0.2
     */
    public function getMaxWarnLevel(): int
    {
        return match ($this->getType()) {
            self::TYPE_HEAT => 2,
            self::TYPE_UV => 1,
            default => 4,
        };
    }

    /**
     * Returns the warning level, where a preliminary information counts as the warning it announces.
     *
     * @since 2.0.2
     */
    public function getSeverityLevel(): int
    {
        $level = $this->getWarnLevel();

        return $level === 0 ? self::PRELIMINARY_LEVEL : $level;
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
