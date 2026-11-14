export default function (el, SC) {
    const { $, $$, reduce, clamp } = SC;
    const cv = $('#paint', el);
    const cx = cv.getContext('2d');
    const portrait = $('.portrait');
    const hello = cv.dataset.hello;
    const controller = new AbortController();
    const { signal } = controller;

    const reset = () => {
        cx.fillStyle = '#F1F0EC';
        cx.fillRect(0, 0, cv.width, cv.height);
        cx.fillStyle = '#0A0A0A';
        cx.font = '700 15px Archivo, Arial';
        cx.fillText(hello, 14, 26);
    };
    reset();

    let col = '#0A0A0A';
    let drawing = false;
    let last = null;
    const swatches = $$('.crt-tools [data-col]', el);
    swatches.forEach((b) =>
        b.addEventListener(
            'click',
            () => {
                col = b.dataset.col;
                swatches.forEach((x) => x.classList.toggle('on', x === b));
            },
            { signal },
        ),
    );
    $('#clear', el).addEventListener('click', reset, { signal });

    const pt = (e) => {
        const r = cv.getBoundingClientRect();
        return [((e.clientX - r.left) / r.width) * cv.width, ((e.clientY - r.top) / r.height) * cv.height];
    };
    const dot = (x, y) => {
        cx.fillStyle = col;
        cx.fillRect(Math.round(x) - 2, Math.round(y) - 2, 4, 4);
    };
    cv.addEventListener(
        'pointerdown',
        (e) => {
            drawing = true;
            last = pt(e);
            dot(...last);
            cv.setPointerCapture(e.pointerId);
        },
        { signal },
    );
    cv.addEventListener(
        'pointermove',
        (e) => {
            if (!drawing) {
                return;
            }
            const p = pt(e);
            const n = Math.ceil(Math.hypot(p[0] - last[0], p[1] - last[1]) / 2);
            for (let i = 0; i <= n; i++) {
                dot(last[0] + ((p[0] - last[0]) * i) / n, last[1] + ((p[1] - last[1]) * i) / n);
            }
            last = p;
        },
        { signal },
    );
    addEventListener('pointerup', () => (drawing = false), { signal });

    $$('.xrow').forEach((r) => r.addEventListener('click', () => r.classList.toggle('open'), { signal }));

    SC.onFrame.push(() => {
        if (!reduce && portrait) {
            const t = portrait.getBoundingClientRect().top;
            portrait.style.setProperty('--rev', clamp((innerHeight - t) / (innerHeight * 0.55), 0, 1).toFixed(3));
        }
    });

    return () => controller.abort();
}
