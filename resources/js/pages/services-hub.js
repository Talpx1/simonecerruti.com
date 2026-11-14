export default function (root, SC) {
    const { $, $$, clamp, reduce } = SC;
    const config = JSON.parse(root.dataset.config || '{}');
    const routes = $('#routes');
    const verdict = $('#verdict', root);
    const go = $('#verdict-go', root);
    const cards = $$('.scard', root);
    const flip = $('#flip', root);
    const switches = $$('.switch button', root);
    const listeners = [];
    let auto = true;
    let timer = null;

    const listen = (el, type, fn) => {
        el.addEventListener(type, fn);
        listeners.push(() => el.removeEventListener(type, fn));
    };

    const escapeHtml = (s) =>
        s.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

    const goals = $$('.goal', root);
    goals.forEach((g) =>
        listen(g, 'click', () => {
            goals.forEach((x) => x.classList.toggle('on', x === g));
            const to = g.dataset.to;
            const label = g.querySelector('span').textContent;
            verdict.innerHTML = config.verdict
                .replace(':goal', escapeHtml(label))
                .replace(':service', escapeHtml(config.names[to]));
            go.hidden = false;
            go.href = '#area-' + to;
            cards.forEach((c) => c.classList.toggle('pick', c.id === 'area-' + to));
            if (routes) {
                routes.dataset.route = String(config.channels[to]);
            }
        }),
    );

    SC.onFrame.push(() => {
        if (reduce || innerWidth < 900) {
            return;
        }
        cards.forEach((c, i) => {
            const next = cards[i + 1];
            if (!next) {
                return;
            }
            const p = clamp(1 - (next.getBoundingClientRect().top - 96) / (innerHeight * 0.8), 0, 1);
            c.style.transform = `scale(${(1 - p * 0.06).toFixed(4)})`;
            c.style.filter = `brightness(${(1 - p * 0.5).toFixed(3)})`;
        });
    });

    const setMode = (m) => {
        flip.dataset.mode = m;
        switches.forEach((b) => {
            b.classList.toggle('on', b.dataset.m === m);
            b.classList.toggle('acc-on', b.dataset.m === 'custom');
        });
    };
    switches.forEach((b) =>
        listen(b, 'click', () => {
            auto = false;
            setMode(b.dataset.m);
        }),
    );
    SC.onFrame.push(() => {
        if (!auto) {
            return;
        }
        if (flip.getBoundingClientRect().top < innerHeight * 0.35) {
            auto = false;
            timer = setTimeout(() => setMode('custom'), 500);
        }
    });

    return () => {
        listeners.forEach((off) => off());
        clearTimeout(timer);
    };
}
