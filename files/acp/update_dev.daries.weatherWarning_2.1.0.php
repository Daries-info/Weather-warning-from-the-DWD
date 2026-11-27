<?php

use wcf\data\option\OptionEditor;
use wcf\system\WCF;

/**
 * Replaces the region list with the warn areas the DWD actually issues warnings for.
 *
 * The previous list also contained municipalities, districts of cities, lakes and sea areas, which never
 * receive a warning. Stored regions are moved to the warn area they belong to where this is unambiguous
 * and cleared otherwise, so the default region applies again.
 *
 * @author  Marco D., Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 2.1
 */

/** @var array<int, string> $regions */
$regions = require __DIR__ . '/regions_dev.daries.weatherWarning.php';

/**
 * Regions the DWD has merged into a combined area or split off an island from.
 */
$renamedRegions = [
    'Kreis Leer' => 'Kreis Leer (ohne Borkum)',
    'Kreis Pinneberg' => 'Kreis Pinneberg (ohne Helgoland)',
    'Kreis Oldenburg' => 'Kreis und Stadt Oldenburg',
    'Stadt Oldenburg (Oldb)' => 'Kreis und Stadt Oldenburg',
    'Kreis Osnabrück' => 'Kreis und Stadt Osnabrück',
    'Stadt Osnabrück' => 'Kreis und Stadt Osnabrück',
    'Kreis Darmstadt-Dieburg' => 'Kreis Darmstadt-Dieburg und Stadt Darmstadt',
    'Stadt Darmstadt' => 'Kreis Darmstadt-Dieburg und Stadt Darmstadt',
    'Kreis Offenbach am Main' => 'Kreis und Stadt Offenbach',
    'Stadt Offenbach' => 'Kreis und Stadt Offenbach',
    'Kreis Kassel' => 'Kreis und Stadt Kassel',
    'Stadt Kassel' => 'Kreis und Stadt Kassel',
    'Kreis Trier-Saarburg' => 'Kreis Trier-Saarburg und Stadt Trier',
    'Stadt Trier' => 'Kreis Trier-Saarburg und Stadt Trier',
    'Kreis Kaiserslautern' => 'Kreis und Stadt Kaiserslautern',
    'Stadt Kaiserslautern' => 'Kreis und Stadt Kaiserslautern',
    'Rhein-Pfalz-Kreis' => 'Rhein-Pfalz-Kreis und Stadt Ludwigshafen',
    'Stadt Ludwigshafen am Rhein' => 'Rhein-Pfalz-Kreis und Stadt Ludwigshafen',
    'Kreis Mainz-Bingen' => 'Kreis Mainz-Bingen und Stadt Mainz',
    'Stadt Mainz' => 'Kreis Mainz-Bingen und Stadt Mainz',
    'Kreis Südwestpfalz' => 'Kreis Südwestpfalz und Stadt Pirmasens',
    'Stadt Pirmasens' => 'Kreis Südwestpfalz und Stadt Pirmasens',
    'Kreis Heilbronn' => 'Kreis und Stadt Heilbronn',
    'Stadt Heilbronn' => 'Kreis und Stadt Heilbronn',
    'Kreis Karlsruhe' => 'Kreis und Stadt Karlsruhe',
    'Stadt Karlsruhe' => 'Kreis und Stadt Karlsruhe',
    'Rhein-Neckar-Kreis' => 'Rhein-Neckar-Kreis und Stadt Heidelberg',
    'Stadt Heidelberg' => 'Rhein-Neckar-Kreis und Stadt Heidelberg',
    'Enzkreis' => 'Enzkreis und Stadt Pforzheim',
    'Stadt Pforzheim' => 'Enzkreis und Stadt Pforzheim',
    'Kreis Breisgau-Hochschwarzwald' => 'Kreis Breisgau-Hochschwarzwald und Stadt Freiburg',
    'Stadt Freiburg im Breisgau' => 'Kreis Breisgau-Hochschwarzwald und Stadt Freiburg',
    'Alb-Donau-Kreis' => 'Alb-Donau-Kreis und Stadt Ulm',
    'Stadt Ulm' => 'Alb-Donau-Kreis und Stadt Ulm',
    'Kreis München' => 'Kreis und Stadt München',
    'Stadt München' => 'Kreis und Stadt München',
    'Kreis Rosenheim' => 'Kreis und Stadt Rosenheim',
    'Stadt Rosenheim' => 'Kreis und Stadt Rosenheim',
    'Kreis Landshut' => 'Kreis und Stadt Landshut',
    'Stadt Landshut' => 'Kreis und Stadt Landshut',
    'Kreis Passau' => 'Kreis und Stadt Passau',
    'Stadt Passau' => 'Kreis und Stadt Passau',
    'Kreis Straubing-Bogen' => 'Kreis Straubing-Bogen und Stadt Straubing',
    'Stadt Straubing' => 'Kreis Straubing-Bogen und Stadt Straubing',
    'Kreis Regensburg' => 'Kreis und Stadt Regensburg',
    'Stadt Regensburg' => 'Kreis und Stadt Regensburg',
    'Kreis Bamberg' => 'Kreis und Stadt Bamberg',
    'Stadt Bamberg' => 'Kreis und Stadt Bamberg',
    'Kreis Bayreuth' => 'Kreis und Stadt Bayreuth',
    'Stadt Bayreuth' => 'Kreis und Stadt Bayreuth',
    'Kreis Coburg' => 'Kreis und Stadt Coburg',
    'Stadt Coburg' => 'Kreis und Stadt Coburg',
    'Kreis Hof' => 'Kreis und Stadt Hof',
    'Stadt Hof' => 'Kreis und Stadt Hof',
    'Kreis Ansbach' => 'Kreis und Stadt Ansbach',
    'Stadt Ansbach' => 'Kreis und Stadt Ansbach',
    'Kreis Fürth' => 'Kreis und Stadt Fürth',
    'Stadt Fürth' => 'Kreis und Stadt Fürth',
    'Kreis Aschaffenburg' => 'Kreis und Stadt Aschaffenburg',
    'Stadt Aschaffenburg' => 'Kreis und Stadt Aschaffenburg',
    'Kreis Schweinfurt' => 'Kreis und Stadt Schweinfurt',
    'Stadt Schweinfurt' => 'Kreis und Stadt Schweinfurt',
    'Kreis Würzburg' => 'Kreis und Stadt Würzburg',
    'Stadt Würzburg' => 'Kreis und Stadt Würzburg',
    'Kreis Augsburg' => 'Kreis und Stadt Augsburg',
    'Stadt Augsburg' => 'Kreis und Stadt Augsburg',
];

/** @var array<string, int> $municipalityRegions */
$municipalityRegions = require __DIR__ . '/regionMigration_dev.daries.weatherWarning_2.1.0.php';

$resolveRegion = static function (string $region) use ($regions, $renamedRegions, $municipalityRegions): string {
    if (\in_array($region, $regions, true)) {
        return $region;
    }

    if (isset($renamedRegions[$region])) {
        return $renamedRegions[$region];
    }

    return $regions[$municipalityRegions[$region] ?? 0] ?? '';
};

WCF::getDB()->beginTransaction();

$sql = "DELETE FROM wcf1_weather_warning_region";
$statement = WCF::getDB()->prepare($sql);
$statement->execute();

$sql = "INSERT INTO wcf1_weather_warning_region
                    (regionName)
        VALUES      (?)";
$statement = WCF::getDB()->prepare($sql);
foreach ($regions as $regionName) {
    $statement->execute([$regionName]);
}

$sql = "SELECT optionID
        FROM   wcf1_user_option
        WHERE  optionName = ?";
$statement = WCF::getDB()->prepare($sql);
$statement->execute(['weatherWarningRegion']);
$optionID = $statement->fetchSingleColumn();

if ($optionID !== false) {
    $column = 'userOption' . $optionID;

    $sql = "SELECT DISTINCT {$column}
            FROM            wcf1_user_option_value
            WHERE           {$column} <> ?";
    $statement = WCF::getDB()->prepare($sql);
    $statement->execute(['']);
    $storedRegions = $statement->fetchList($column);

    $sql = "UPDATE wcf1_user_option_value
            SET    {$column} = ?
            WHERE  {$column} = ?";
    $statement = WCF::getDB()->prepare($sql);
    foreach ($storedRegions as $storedRegion) {
        $newRegion = $resolveRegion($storedRegion);
        if ($newRegion !== $storedRegion) {
            $statement->execute([$newRegion, $storedRegion]);
        }
    }
}

WCF::getDB()->commitTransaction();

$defaultRegion = WEATHER_WARNING_DEFAULT_REGION;
if ($defaultRegion !== '') {
    $newDefaultRegion = $resolveRegion($defaultRegion);

    if ($newDefaultRegion !== $defaultRegion) {
        OptionEditor::import(['weather_warning_default_region' => $newDefaultRegion]);
    }
}
