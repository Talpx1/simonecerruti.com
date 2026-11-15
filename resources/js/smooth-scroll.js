import Lenis from 'lenis';

const WHEEL_INTERPOLATION_FACTOR = 0.085;
const WHEEL_DELTA_MULTIPLIER = 0.9;
const ANCHOR_OFFSET_PX = -96;
const ANCHOR_SCROLL_DURATION_SECONDS = 1.4;

export function initSmoothScroll() {
    if (matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    window.smoothScroller = new Lenis({
        lerp: WHEEL_INTERPOLATION_FACTOR,
        wheelMultiplier: WHEEL_DELTA_MULTIPLIER,
        anchors: { offset: ANCHOR_OFFSET_PX, duration: ANCHOR_SCROLL_DURATION_SECONDS },
        autoRaf: true,
    });
}
