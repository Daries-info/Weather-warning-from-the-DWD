# Projekt: Wetterwarnungen vom DWD (WoltLab Suite 6.2 Plugin)

## Kontext

Bestehendes WSC-6.2-Plugin. Liefert Wetterwarnungen des DWD für ganz Deutschland,
inkl. Graslandfeuerindex und Waldbrand-Gefahrenindex. Regionale Filterung,
Boxen-Integration, mehrsprachig (DE/EN).

## Referenzen

- WCF-Framework: `/mnt/storagebox2-coding/raw/WCF-6.2/`

Bei Unsicherheit zu WCF-APIs immer zuerst im Framework-Pfad nachsehen,
nicht raten.

## PHP

- Minimum: PHP 8.3. Code muss bis einschließlich PHP 8.4 fehlerfrei laufen.
- PHP-8.3-Features sind erlaubt und erwünscht: `readonly` auf Klassenebene,
  typed class constants, Enums, First-class callable syntax.
- Strikte Typisierung (`declare(strict_types=1)`), vollständige Typehints
  inkl. Rückgabetypen.
- PHPStan Level 6 muss ohne Fehler bestehen – bei jeder Änderung beachten.
- Formulare: `FormBuilder` verwenden, kein manuelles Formularhandling.
- Tabellarische ACP-Ansichten: `Grid Views` oder `List Views`, keine
  Legacy-`*List`-Klassen ohne Grund.
- Caching: neue Eager-Cache-Mechanik nutzen. Kein Redis.
- Kein toter/auskommentierter Code, keine Legacy-Kompatibilitätsschichten
  für PHP < 8.3.

## JavaScript / Frontend

- Natives, modernes TypeScript/JavaScript (ES6+).
- Kein jQuery. Vanilla-JS oder WSC-Core-Komponenten verwenden.
- Bestehende Core-Module (z. B. `WoltLabSuite/Core/...`) statt eigener
  Reimplementierungen nutzen, wo vorhanden.

## Code-Stil

- Konsistent mit bestehendem WCF-Coding-Style (PSR-12 als Basis, WCF-Konventionen
  wo abweichend haben Vorrang).
- Sprechende Namen, kurze Methoden, keine God-Classes.
- Kommentare nur wo Logik nicht selbsterklärend ist.

## Vorgehen bei Änderungen

- Vor Implementierung: kurz prüfen, ob es bereits eine passende WCF-Klasse/API gibt.
- Änderungen minimal und fokussiert halten, keine ungefragten Refactorings.
- Sprachvariablen (DE/EN) bei neuen UI-Texten immer in beiden Sprachdateien pflegen.
- Bei Unklarheiten zur Anforderung nachfragen statt zu raten.
