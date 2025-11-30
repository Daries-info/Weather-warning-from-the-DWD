/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */

import * as Core from "WoltLabSuite/Core/Core";
import { SearchInputOptions } from "WoltLabSuite/Core/Ui/Search/Data";
import UiSearchInput from "WoltLabSuite/Core/Ui/Search/Input";

class UiWeatherWarningRegionSearchInput extends UiSearchInput {
  constructor(element: HTMLInputElement, options: SearchInputOptions) {
    options = Core.extend(
      {
        ajax: {
          className: "wcf\\data\\weather\\warning\\region\\WeatherWarningRegionAction",
        },
      },
      options,
    ) as SearchInputOptions;

    super(element, options);
  }
}

export = UiWeatherWarningRegionSearchInput;
