/**
 * Cycles through the weather warnings of the region box, if there is more than one.
 *
 * The progress bar of the active dot sets the pace: the next warning is shown when its animation ends,
 * so that pausing the animation also pauses the slider. Without animations, for example if the visitor
 * prefers reduced motion, the warnings only change on request.
 *
 * @author  Marco Daries, Alexander Langer (Source of ideas)
 * @copyright   2020-2026 Daries.info
 * @license Daries.info - Free License <https://daries.info/license/free.html>
 * @since 2.1
 */

import { wheneverFirstSeen } from "WoltLabSuite/Core/Helper/Selector";

const PROGRESS_ANIMATION = "weatherWarningSliderProgress";

const enum Direction {
  Previous = -1,
  Next = 1,
}

class WeatherWarningSlider {
  readonly #root: HTMLElement;
  readonly #slides: HTMLElement[];
  readonly #dots: HTMLButtonElement[];
  readonly #counter: HTMLElement;
  readonly #current: HTMLElement;
  #index = 0;

  constructor(root: HTMLElement) {
    this.#root = root;
    this.#slides = Array.from(root.querySelectorAll<HTMLElement>(".weatherWarningContent > .weatherWarningRegion"));
    this.#dots = Array.from(root.querySelectorAll<HTMLButtonElement>("[data-slider-dot]"));
    this.#counter = root.querySelector<HTMLElement>(".weatherWarningSlider__counter")!;
    this.#current = root.querySelector<HTMLElement>("[data-slider-current]")!;

    root.querySelector("[data-slider-previous]")!.addEventListener("click", () => {
      this.#showManually(this.#index - 1, Direction.Previous);
    });
    root.querySelector("[data-slider-next]")!.addEventListener("click", () => {
      this.#showManually(this.#index + 1, Direction.Next);
    });
    this.#dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        this.#showManually(index, index < this.#index ? Direction.Previous : Direction.Next);
      });
    });

    root.addEventListener("animationend", (event) => {
      if (event.animationName === PROGRESS_ANIMATION) {
        // Only announce the position if the visitor changed it, not on every automatic change.
        this.#counter.setAttribute("aria-live", "off");
        this.#show(this.#index + 1, Direction.Next);
      }
    });

    // Keyboard focus pauses the slider as well, the focus of a clicked button does not.
    root.addEventListener("mouseenter", () => this.#setPaused(true));
    root.addEventListener("mouseleave", () => this.#setPaused(this.#hasKeyboardFocus()));
    root.addEventListener("focusin", () => this.#setPaused(root.matches(":hover") || this.#hasKeyboardFocus()));
    root.addEventListener("focusout", (event) => {
      if (!root.contains(event.relatedTarget as Node | null)) {
        this.#setPaused(root.matches(":hover"));
      }
    });
  }

  #show(index: number, direction: Direction): void {
    const nextIndex = (index + this.#slides.length) % this.#slides.length;
    if (nextIndex === this.#index) {
      this.#restartProgress();
      return;
    }

    const outgoing = this.#slides[this.#index];
    const incoming = this.#slides[nextIndex];

    // Move the incoming warning to the side it comes from, without animating this step.
    incoming.classList.add("weatherWarningSlider__slide--instant");
    incoming.dataset.slidePosition = direction === Direction.Next ? "after" : "before";
    incoming.getBoundingClientRect();
    incoming.classList.remove("weatherWarningSlider__slide--instant");
    delete incoming.dataset.slidePosition;
    incoming.classList.add("active");

    outgoing.classList.remove("active");
    outgoing.dataset.slidePosition = direction === Direction.Next ? "before" : "after";

    this.#dots[this.#index].removeAttribute("aria-current");
    this.#dots[nextIndex].setAttribute("aria-current", "true");

    this.#index = nextIndex;
    this.#current.textContent = String(nextIndex + 1);
  }

  #showManually(index: number, direction: Direction): void {
    this.#counter.setAttribute("aria-live", "polite");
    this.#show(index, direction);
  }

  #restartProgress(): void {
    const dot = this.#dots[this.#index];
    dot.removeAttribute("aria-current");
    dot.getBoundingClientRect();
    dot.setAttribute("aria-current", "true");
  }

  #hasKeyboardFocus(): boolean {
    return this.#root.querySelector(":focus-visible") !== null;
  }

  #setPaused(isPaused: boolean): void {
    this.#root.classList.toggle("weatherWarningSlider--paused", isPaused);
  }
}

export function setup(): void {
  wheneverFirstSeen(".weatherWarningSlider", (element: HTMLElement) => {
    new WeatherWarningSlider(element);
  });
}
