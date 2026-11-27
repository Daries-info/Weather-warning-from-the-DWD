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
define(["require", "exports", "WoltLabSuite/Core/Helper/Selector"], function (require, exports, Selector_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    const PROGRESS_ANIMATION = "weatherWarningSliderProgress";
    class WeatherWarningSlider {
        #root;
        #slides;
        #dots;
        #counter;
        #current;
        #index = 0;
        constructor(root) {
            this.#root = root;
            this.#slides = Array.from(root.querySelectorAll(".weatherWarningContent > .weatherWarningRegion"));
            this.#dots = Array.from(root.querySelectorAll("[data-slider-dot]"));
            this.#counter = root.querySelector(".weatherWarningSlider__counter");
            this.#current = root.querySelector("[data-slider-current]");
            root.querySelector("[data-slider-previous]").addEventListener("click", () => {
                this.#showManually(this.#index - 1, -1 /* Direction.Previous */);
            });
            root.querySelector("[data-slider-next]").addEventListener("click", () => {
                this.#showManually(this.#index + 1, 1 /* Direction.Next */);
            });
            this.#dots.forEach((dot, index) => {
                dot.addEventListener("click", () => {
                    this.#showManually(index, index < this.#index ? -1 /* Direction.Previous */ : 1 /* Direction.Next */);
                });
            });
            root.addEventListener("animationend", (event) => {
                if (event.animationName === PROGRESS_ANIMATION) {
                    // Only announce the position if the visitor changed it, not on every automatic change.
                    this.#counter.setAttribute("aria-live", "off");
                    this.#show(this.#index + 1, 1 /* Direction.Next */);
                }
            });
            // Keyboard focus pauses the slider as well, the focus of a clicked button does not.
            root.addEventListener("mouseenter", () => this.#setPaused(true));
            root.addEventListener("mouseleave", () => this.#setPaused(this.#hasKeyboardFocus()));
            root.addEventListener("focusin", () => this.#setPaused(root.matches(":hover") || this.#hasKeyboardFocus()));
            root.addEventListener("focusout", (event) => {
                if (!root.contains(event.relatedTarget)) {
                    this.#setPaused(root.matches(":hover"));
                }
            });
        }
        #show(index, direction) {
            const nextIndex = (index + this.#slides.length) % this.#slides.length;
            if (nextIndex === this.#index) {
                this.#restartProgress();
                return;
            }
            const outgoing = this.#slides[this.#index];
            const incoming = this.#slides[nextIndex];
            // Move the incoming warning to the side it comes from, without animating this step.
            incoming.classList.add("weatherWarningSlider__slide--instant");
            incoming.dataset.slidePosition = direction === 1 /* Direction.Next */ ? "after" : "before";
            incoming.getBoundingClientRect();
            incoming.classList.remove("weatherWarningSlider__slide--instant");
            delete incoming.dataset.slidePosition;
            incoming.classList.add("active");
            outgoing.classList.remove("active");
            outgoing.dataset.slidePosition = direction === 1 /* Direction.Next */ ? "before" : "after";
            this.#dots[this.#index].removeAttribute("aria-current");
            this.#dots[nextIndex].setAttribute("aria-current", "true");
            this.#index = nextIndex;
            this.#current.textContent = String(nextIndex + 1);
        }
        #showManually(index, direction) {
            this.#counter.setAttribute("aria-live", "polite");
            this.#show(index, direction);
        }
        #restartProgress() {
            const dot = this.#dots[this.#index];
            dot.removeAttribute("aria-current");
            dot.getBoundingClientRect();
            dot.setAttribute("aria-current", "true");
        }
        #hasKeyboardFocus() {
            return this.#root.querySelector(":focus-visible") !== null;
        }
        #setPaused(isPaused) {
            this.#root.classList.toggle("weatherWarningSlider--paused", isPaused);
        }
    }
    function setup() {
        (0, Selector_1.wheneverFirstSeen)(".weatherWarningSlider", (element) => {
            new WeatherWarningSlider(element);
        });
    }
});
