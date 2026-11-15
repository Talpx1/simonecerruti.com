export default function (root, siteRuntime) {
    const { prefersReducedMotion, queryOne, queryAll } = siteRuntime;
    const rail = queryOne('#projects-rail', root);
    const covers = queryAll('.project-feature .project-cover .project-cover-inner', root);
    const driftCovers = () => {
        if (prefersReducedMotion) {
            return;
        }
        covers.forEach((cover) => {
            const parentBounds = cover.parentElement.getBoundingClientRect();
            const viewportOffset = (parentBounds.top + parentBounds.height / 2 - innerHeight / 2) / innerHeight;
            cover.style.setProperty('--cover-parallax-offset', (viewportOffset * -40).toFixed(1) + 'px');
        });
    };

    if (!rail) {
        siteRuntime.frameCallbacks.push(driftCovers);
        return undefined;
    }

    const scrollbar = queryOne('#projects-rail-progress', root);
    const emptyMessage = queryOne('#projects-rail-empty', root);
    let isDragging = false;
    let dragStartX = 0;
    let dragStartScrollLeft = 0;
    let draggedDistance = 0;

    const onPointerDown = (event) => {
        if (event.pointerType !== 'mouse') {
            return;
        }
        isDragging = true;
        draggedDistance = 0;
        dragStartX = event.clientX;
        dragStartScrollLeft = rail.scrollLeft;
    };
    const onPointerMove = (event) => {
        if (!isDragging) {
            return;
        }
        const dragDeltaX = event.clientX - dragStartX;
        draggedDistance = Math.max(draggedDistance, Math.abs(dragDeltaX));
        if (draggedDistance > 6) {
            rail.classList.add('is-dragging');
        }
        rail.scrollLeft = dragStartScrollLeft - dragDeltaX;
    };
    const onPointerUp = () => {
        if (!isDragging) {
            return;
        }
        isDragging = false;
        setTimeout(() => rail.classList.remove('is-dragging'), 0);
    };
    const onRailClick = (event) => {
        if (draggedDistance > 6) {
            event.preventDefault();
            draggedDistance = 0;
        }
    };
    const onWheel = (event) => {
        if (Math.abs(event.deltaY) > Math.abs(event.deltaX) && rail.scrollWidth > rail.clientWidth) {
            const maxScrollLeft = rail.scrollWidth - rail.clientWidth;
            if ((event.deltaY > 0 && rail.scrollLeft < maxScrollLeft - 2) || (event.deltaY < 0 && rail.scrollLeft > 2)) {
                event.preventDefault();
                rail.scrollLeft += event.deltaY;
            }
        }
    };
    const getScrollStepWidth = () => {
        const firstVisibleCard = rail.querySelector('.projects-rail-card:not(.is-filtered-out)');
        return firstVisibleCard ? firstVisibleCard.getBoundingClientRect().width + 20 : 400;
    };
    const onRootClick = (event) => {
        const control = event.target.closest('[data-rail-step], .filter-bar button');
        if (!control || !root.contains(control)) {
            return;
        }
        if (control.dataset.railStep) {
            rail.scrollBy({ left: getScrollStepWidth() * Number(control.dataset.railStep), behavior: prefersReducedMotion ? 'auto' : 'smooth' });
            return;
        }
        queryAll('.projects-rail-head .filter-bar button', root).forEach((filterButton) => filterButton.classList.toggle('is-active', filterButton === control));
        const activeFilter = control.dataset.f;
        let visibleCardCount = 0;
        queryAll('.projects-rail-card', root).forEach((card) => {
            const isShown = activeFilter === '*' || card.dataset.types.split('|').includes(activeFilter);
            card.classList.toggle('is-filtered-out', !isShown);
            if (isShown) {
                visibleCardCount++;
            }
        });
        if (emptyMessage) {
            emptyMessage.classList.toggle('is-active', visibleCardCount === 0);
        }
        rail.scrollTo({ left: 0 });
    };

    rail.addEventListener('pointerdown', onPointerDown);
    addEventListener('pointermove', onPointerMove);
    addEventListener('pointerup', onPointerUp);
    rail.addEventListener('click', onRailClick, true);
    rail.addEventListener('wheel', onWheel, { passive: false });
    root.addEventListener('click', onRootClick);

    siteRuntime.frameCallbacks.push(() => {
        if (scrollbar) {
            const maxScrollLeft = rail.scrollWidth - rail.clientWidth;
            const visibleRatio = rail.clientWidth / Math.max(1, rail.scrollWidth);
            scrollbar.style.width = visibleRatio * 100 + '%';
            scrollbar.style.left = (maxScrollLeft > 0 ? (rail.scrollLeft / maxScrollLeft) * (1 - visibleRatio) * 100 : 0) + '%';
        }
        driftCovers();
    });

    return () => {
        removeEventListener('pointermove', onPointerMove);
        removeEventListener('pointerup', onPointerUp);
        root.removeEventListener('click', onRootClick);
    };
}
