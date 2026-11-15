const MINIMUM_THUMB_HEIGHT = 56;
const IDLE_HIDE_DELAY_MS = 900;

export function initCustomScrollbar() {
    if (!matchMedia('(pointer: fine)').matches) {
        return;
    }

    const rootElement = document.documentElement;
    const trackElement = document.createElement('div');
    trackElement.className = 'site-scrollbar';
    trackElement.setAttribute('aria-hidden', 'true');
    trackElement.innerHTML =
        '<div class="site-scrollbar-fill"></div>' +
        '<div class="site-scrollbar-thumb"><span class="site-scrollbar-grip"></span><span class="site-scrollbar-label">00</span></div>';
    const fillElement = trackElement.querySelector('.site-scrollbar-fill');
    const thumbElement = trackElement.querySelector('.site-scrollbar-thumb');
    const labelElement = trackElement.querySelector('.site-scrollbar-label');

    let hideTimeout = null;
    let dragOffset = null;
    let isTrackVisible = false;

    const getMetrics = () => {
        const scrollableHeight = Math.max(0, rootElement.scrollHeight - innerHeight);
        const trackHeight = trackElement.clientHeight;
        const thumbHeight = Math.max(MINIMUM_THUMB_HEIGHT, (innerHeight / rootElement.scrollHeight) * trackHeight);
        return { scrollableHeight, trackHeight, thumbHeight, travel: Math.max(1, trackHeight - thumbHeight) };
    };

    const wake = () => {
        trackElement.classList.add('is-awake');
        clearTimeout(hideTimeout);
        hideTimeout = setTimeout(() => {
            if (dragOffset === null && !trackElement.matches(':hover')) {
                trackElement.classList.remove('is-awake');
            }
        }, IDLE_HIDE_DELAY_MS);
    };

    const update = () => {
        const { scrollableHeight, thumbHeight, travel } = getMetrics();
        const isScrollable = scrollableHeight > 1;
        if (isScrollable !== isTrackVisible) {
            isTrackVisible = isScrollable;
            trackElement.classList.toggle('is-scrollable', isScrollable);
        }
        if (!isScrollable) {
            return;
        }
        const progress = Math.min(1, Math.max(0, scrollY / scrollableHeight));
        const thumbTop = progress * travel;
        thumbElement.style.height = `${thumbHeight}px`;
        thumbElement.style.transform = `translate3d(0, ${thumbTop}px, 0)`;
        fillElement.style.height = `${thumbTop + thumbHeight / 2}px`;
        labelElement.textContent = String(Math.round(progress * 100)).padStart(2, '0');
    };

    const onScroll = () => {
        update();
        wake();
    };

    thumbElement.addEventListener('pointerdown', (event) => {
        event.preventDefault();
        thumbElement.setPointerCapture(event.pointerId);
        dragOffset = event.clientY - thumbElement.getBoundingClientRect().top;
        trackElement.classList.add('is-dragging', 'is-awake');
    });
    thumbElement.addEventListener('pointermove', (event) => {
        if (dragOffset === null) {
            return;
        }
        const { scrollableHeight, travel } = getMetrics();
        const trackTop = trackElement.getBoundingClientRect().top;
        const progress = Math.min(1, Math.max(0, (event.clientY - trackTop - dragOffset) / travel));
        scrollTo({ top: progress * scrollableHeight, behavior: 'instant' });
    });
    const stopDragging = () => {
        dragOffset = null;
        trackElement.classList.remove('is-dragging');
        wake();
    };
    thumbElement.addEventListener('pointerup', stopDragging);
    thumbElement.addEventListener('pointercancel', stopDragging);

    trackElement.addEventListener('pointerdown', (event) => {
        if (event.target.closest('.site-scrollbar-thumb')) {
            return;
        }
        const { scrollableHeight, thumbHeight, travel } = getMetrics();
        const trackTop = trackElement.getBoundingClientRect().top;
        const progress = Math.min(1, Math.max(0, (event.clientY - trackTop - thumbHeight / 2) / travel));
        scrollTo({ top: progress * scrollableHeight, behavior: 'smooth' });
    });
    trackElement.addEventListener('pointerenter', wake);
    trackElement.addEventListener('pointerleave', wake);

    const mountTrack = () => {
        if (!trackElement.isConnected) {
            document.body.appendChild(trackElement);
        }
        update();
    };

    rootElement.classList.add('has-custom-scrollbar');
    mountTrack();
    addEventListener('scroll', onScroll, { passive: true });
    addEventListener('resize', update, { passive: true });
    document.addEventListener('livewire:navigated', mountTrack);
    new ResizeObserver(update).observe(rootElement);
}
