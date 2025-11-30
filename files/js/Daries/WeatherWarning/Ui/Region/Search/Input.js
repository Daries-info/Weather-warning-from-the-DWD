/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */
define(["require", "exports", "tslib", "WoltLabSuite/Core/Core", "WoltLabSuite/Core/Ui/Search/Input"], function (require, exports, tslib_1, Core, Input_1) {
    "use strict";
    Core = tslib_1.__importStar(Core);
    Input_1 = tslib_1.__importDefault(Input_1);
    class UiWeatherWarningRegionSearchInput extends Input_1.default {
        constructor(element, options) {
            options = Core.extend({
                ajax: {
                    className: "wcf\\data\\weather\\warning\\region\\WeatherWarningRegionAction",
                },
            }, options);
            super(element, options);
        }
    }
    return UiWeatherWarningRegionSearchInput;
});
