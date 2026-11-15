const LOGO_PATH_LENGTH = 186;
const PARALLAX_FOLLOW_FACTOR = 0.06;
const GLOW_POSITION_FOLLOW_FACTOR = 0.14;
const GLOW_OPACITY_FOLLOW_FACTOR = 0.08;
const LOGO_DRAW_INERTIA_PER_SECOND = 4.5;
const MAXIMUM_DELTA_SECONDS = 0.05;

const easeInOutCubic = (progress) =>
    progress < 0.5 ? 4 * progress ** 3 : 1 - Math.pow(-2 * progress + 2, 3) / 2;

export default function (showcaseElement, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, clamp } = siteRuntime;
    const photoCardElement = showcaseElement.querySelector('.portrait-showcase-photo-card');
    const cursorGlowElement = showcaseElement.querySelector('.portrait-showcase-cursor-glow');
    const logoWaveElement = showcaseElement.querySelector('.portrait-showcase-logo-wave');
    const logoWavePathElement = logoWaveElement.querySelector('path');
    const isInteractive = hasFinePointer && !prefersReducedMotion;

    let targetParallaxX = 0;
    let targetParallaxY = 0;
    let currentParallaxX = 0;
    let currentParallaxY = 0;
    let targetGlowXPercent = 50;
    let targetGlowYPercent = 40;
    let currentGlowXPercent = 50;
    let currentGlowYPercent = 40;
    let targetGlowOpacity = 0;
    let currentGlowOpacity = 0;
    let currentLogoDrawProgress = 0;
    let isDisposed = false;

    const updateParallaxTarget = (pointerEvent) => {
        const showcaseBounds = showcaseElement.getBoundingClientRect();
        if (showcaseBounds.bottom < 0 || showcaseBounds.top > innerHeight) {
            return;
        }
        targetParallaxX = clamp((pointerEvent.clientX - (showcaseBounds.left + showcaseBounds.width / 2)) / (innerWidth / 2), -1, 1);
        targetParallaxY = clamp((pointerEvent.clientY - (showcaseBounds.top + showcaseBounds.height / 2)) / (innerHeight / 2), -1, 1);
    };

    const updateGlowTarget = (pointerEvent) => {
        const photoCardBounds = photoCardElement.getBoundingClientRect();
        targetGlowXPercent = ((pointerEvent.clientX - photoCardBounds.left) / photoCardBounds.width) * 100;
        targetGlowYPercent = ((pointerEvent.clientY - photoCardBounds.top) / photoCardBounds.height) * 100;
        targetGlowOpacity = 1;
    };

    const hideGlow = () => {
        targetGlowOpacity = 0;
    };

    if (isInteractive) {
        addEventListener('pointermove', updateParallaxTarget);
        photoCardElement.addEventListener('pointermove', updateGlowTarget);
        photoCardElement.addEventListener('pointerleave', hideGlow);
    }

    const frameCallback = (timestamp, deltaSeconds) => {
        if (isDisposed) {
            return;
        }
        const boundedDeltaSeconds = Math.min(MAXIMUM_DELTA_SECONDS, deltaSeconds);

        currentParallaxX += (targetParallaxX - currentParallaxX) * PARALLAX_FOLLOW_FACTOR;
        currentParallaxY += (targetParallaxY - currentParallaxY) * PARALLAX_FOLLOW_FACTOR;
        showcaseElement.style.setProperty('--portrait-parallax-x', currentParallaxX.toFixed(4));
        showcaseElement.style.setProperty('--portrait-parallax-y', currentParallaxY.toFixed(4));

        currentGlowXPercent += (targetGlowXPercent - currentGlowXPercent) * GLOW_POSITION_FOLLOW_FACTOR;
        currentGlowYPercent += (targetGlowYPercent - currentGlowYPercent) * GLOW_POSITION_FOLLOW_FACTOR;
        currentGlowOpacity += (targetGlowOpacity - currentGlowOpacity) * GLOW_OPACITY_FOLLOW_FACTOR;
        cursorGlowElement.style.setProperty('--portrait-glow-x', `${currentGlowXPercent.toFixed(2)}%`);
        cursorGlowElement.style.setProperty('--portrait-glow-y', `${currentGlowYPercent.toFixed(2)}%`);
        cursorGlowElement.style.opacity = currentGlowOpacity.toFixed(3);

        const logoWaveBounds = logoWaveElement.getBoundingClientRect();
        const logoWaveCenterY = logoWaveBounds.top + logoWaveBounds.height / 2;
        const scrollDrawTarget = prefersReducedMotion
            ? 1
            : clamp((innerHeight * 1.05 - logoWaveCenterY) / (innerHeight * 0.55), 0, 1);
        currentLogoDrawProgress += (scrollDrawTarget - currentLogoDrawProgress) * (1 - Math.exp(-boundedDeltaSeconds * LOGO_DRAW_INERTIA_PER_SECOND));
        const easedLogoDrawProgress = easeInOutCubic(currentLogoDrawProgress);
        logoWavePathElement.style.strokeDashoffset = (LOGO_PATH_LENGTH * (1 - easedLogoDrawProgress)).toFixed(2);
        showcaseElement.style.setProperty('--portrait-logo-draw-progress', easedLogoDrawProgress.toFixed(4));
    };

    siteRuntime.frameCallbacks.push(frameCallback);

    return () => {
        isDisposed = true;
        removeEventListener('pointermove', updateParallaxTarget);
    };
}
