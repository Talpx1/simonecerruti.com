const RAIL_WIDTH = 16;
const DOT_SPACING = 14;
const WAVE_RADIUS = 90;
const IDLE_HIDE_DELAY_MS = 1200;

export function initCustomScrollbar() {
    if (!matchMedia('(pointer: fine)').matches) {
        return;
    }

    const rootElement = document.documentElement;
    const railElement = document.createElement('canvas');
    railElement.className = 'site-scrollbar';
    railElement.setAttribute('aria-hidden', 'true');
    const railContext = railElement.getContext('2d');

    let railHeight = 0;
    let hideTimeout = null;
    let isDragging = false;
    let isScrollable = false;
    let hoverY = null;

    const getScrollableHeight = () => Math.max(0, rootElement.scrollHeight - innerHeight);

    const wake = () => {
        railElement.classList.add('is-awake');
        clearTimeout(hideTimeout);
        hideTimeout = setTimeout(() => {
            if (!isDragging && hoverY === null) {
                railElement.classList.remove('is-awake');
            }
        }, IDLE_HIDE_DELAY_MS);
    };

    const resize = () => {
        const pixelRatio = Math.min(devicePixelRatio || 1, 2);
        railHeight = railElement.clientHeight;
        railElement.width = RAIL_WIDTH * pixelRatio;
        railElement.height = railHeight * pixelRatio;
        railContext.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        draw();
    };

    const draw = () => {
        const scrollableHeight = getScrollableHeight();
        const nextIsScrollable = scrollableHeight > 1;
        if (nextIsScrollable !== isScrollable) {
            isScrollable = nextIsScrollable;
            railElement.classList.toggle('is-scrollable', isScrollable);
        }
        railContext.clearRect(0, 0, RAIL_WIDTH, railHeight);
        if (!isScrollable) {
            return;
        }
        const progress = Math.min(1, Math.max(0, scrollY / scrollableHeight));
        const currentY = progress * railHeight;
        const dotCount = Math.floor(railHeight / DOT_SPACING);
        const dotOffset = (railHeight - (dotCount - 1) * DOT_SPACING) / 2;
        for (let index = 0; index < dotCount; index++) {
            const dotY = dotOffset + index * DOT_SPACING;
            const waveDistance = Math.abs(dotY - currentY);
            const wave = waveDistance < WAVE_RADIUS ? 1 - waveDistance / WAVE_RADIUS : 0;
            const hoverDistance = hoverY === null ? WAVE_RADIUS : Math.abs(dotY - hoverY);
            const hoverWave = hoverDistance < WAVE_RADIUS ? 1 - hoverDistance / WAVE_RADIUS : 0;
            const isPassed = dotY < currentY;
            const size = 2 + wave * wave * 7 + hoverWave * 2;
            railContext.fillStyle =
                wave > 0.55
                    ? '#ff4a1c'
                    : `rgba(241,240,236,${(isPassed ? 0.38 : 0.16) + wave * 0.4 + hoverWave * 0.25})`;
            railContext.fillRect(RAIL_WIDTH / 2 - size / 2, dotY - size / 2, size, size);
        }
    };

    const scrollToPointer = (event, behavior) => {
        const bounds = railElement.getBoundingClientRect();
        const progress = Math.min(1, Math.max(0, (event.clientY - bounds.top) / bounds.height));
        scrollTo({ top: progress * getScrollableHeight(), behavior });
    };

    railElement.addEventListener('pointerdown', (event) => {
        event.preventDefault();
        isDragging = true;
        railElement.setPointerCapture(event.pointerId);
        railElement.classList.add('is-awake');
        scrollToPointer(event, 'smooth');
    });
    railElement.addEventListener('pointermove', (event) => {
        hoverY = event.clientY - railElement.getBoundingClientRect().top;
        if (isDragging) {
            scrollToPointer(event, 'instant');
        }
        draw();
    });
    const stopDragging = () => {
        isDragging = false;
        wake();
    };
    railElement.addEventListener('pointerup', stopDragging);
    railElement.addEventListener('pointercancel', stopDragging);
    railElement.addEventListener('pointerleave', () => {
        hoverY = null;
        draw();
        wake();
    });
    railElement.addEventListener('pointerenter', wake);

    const mountRail = () => {
        if (!railElement.isConnected) {
            document.body.appendChild(railElement);
        }
        resize();
    };

    rootElement.classList.add('has-custom-scrollbar');
    mountRail();
    addEventListener(
        'scroll',
        () => {
            draw();
            wake();
        },
        { passive: true },
    );
    addEventListener('resize', resize, { passive: true });
    document.addEventListener('livewire:navigated', mountRail);
    new ResizeObserver(draw).observe(rootElement);
}
