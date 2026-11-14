export default function (root, SC) {
    const { reduce, $, $$ } = SC;
    const rail = $('#rail', root);
    const covers = $$('.feat .cover .in', root);
    const driftCovers = () => {
        if (reduce) {
            return;
        }
        covers.forEach((el) => {
            const b = el.parentElement.getBoundingClientRect();
            const p = (b.top + b.height / 2 - innerHeight / 2) / innerHeight;
            el.style.setProperty('--py', (p * -40).toFixed(1) + 'px');
        });
    };

    if (!rail) {
        SC.onFrame.push(driftCovers);
        return undefined;
    }

    const bar = $('#railbar', root);
    const empty = $('#rail-empty', root);
    let down = false;
    let start_x = 0;
    let start_left = 0;
    let moved = 0;

    const onDown = (e) => {
        if (e.pointerType !== 'mouse') {
            return;
        }
        down = true;
        moved = 0;
        start_x = e.clientX;
        start_left = rail.scrollLeft;
    };
    const onMove = (e) => {
        if (!down) {
            return;
        }
        const dx = e.clientX - start_x;
        moved = Math.max(moved, Math.abs(dx));
        if (moved > 6) {
            rail.classList.add('drag');
        }
        rail.scrollLeft = start_left - dx;
    };
    const onUp = () => {
        if (!down) {
            return;
        }
        down = false;
        setTimeout(() => rail.classList.remove('drag'), 0);
    };
    const onClick = (e) => {
        if (moved > 6) {
            e.preventDefault();
            moved = 0;
        }
    };
    const onWheel = (e) => {
        if (Math.abs(e.deltaY) > Math.abs(e.deltaX) && rail.scrollWidth > rail.clientWidth) {
            const max = rail.scrollWidth - rail.clientWidth;
            if ((e.deltaY > 0 && rail.scrollLeft < max - 2) || (e.deltaY < 0 && rail.scrollLeft > 2)) {
                e.preventDefault();
                rail.scrollLeft += e.deltaY;
            }
        }
    };
    const step = () => {
        const card = rail.querySelector('.rcard:not(.out)');
        return card ? card.getBoundingClientRect().width + 20 : 400;
    };
    const onRootClick = (e) => {
        const control = e.target.closest('[data-rail-step], .filters button');
        if (!control || !root.contains(control)) {
            return;
        }
        if (control.dataset.railStep) {
            rail.scrollBy({ left: step() * Number(control.dataset.railStep), behavior: reduce ? 'auto' : 'smooth' });
            return;
        }
        $$('.rail-head .filters button', root).forEach((x) => x.classList.toggle('on', x === control));
        const filter = control.dataset.f;
        let visible = 0;
        $$('.rcard', root).forEach((card) => {
            const show = filter === '*' || card.dataset.types.split('|').includes(filter);
            card.classList.toggle('out', !show);
            if (show) {
                visible++;
            }
        });
        if (empty) {
            empty.classList.toggle('on', visible === 0);
        }
        rail.scrollTo({ left: 0 });
    };

    rail.addEventListener('pointerdown', onDown);
    addEventListener('pointermove', onMove);
    addEventListener('pointerup', onUp);
    rail.addEventListener('click', onClick, true);
    rail.addEventListener('wheel', onWheel, { passive: false });
    root.addEventListener('click', onRootClick);

    SC.onFrame.push(() => {
        if (bar) {
            const max = rail.scrollWidth - rail.clientWidth;
            const vis = rail.clientWidth / Math.max(1, rail.scrollWidth);
            bar.style.width = vis * 100 + '%';
            bar.style.left = (max > 0 ? (rail.scrollLeft / max) * (1 - vis) * 100 : 0) + '%';
        }
        driftCovers();
    });

    return () => {
        removeEventListener('pointermove', onMove);
        removeEventListener('pointerup', onUp);
        root.removeEventListener('click', onRootClick);
    };
}
