/**
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2024 Daries.dev
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 */

import { dialogFactory } from "WoltLabSuite/Core/Component/Dialog";
import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";

export function setup(): void {
  wheneverFirstSeen("[data-weather-info-full-view]", (element: HTMLElement) => {
    element.addEventListener("click", () => {
      showDialog(element);
    });
  });
}

function showDialog(element: HTMLElement): void {
  const dialog = dialogFactory().fromHtml(element.innerHTML).withoutControls();
  dialog.show(element.dataset.weatherInfoFullView!);
}
