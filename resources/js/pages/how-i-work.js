export default function (root, SC) {
    const { $, $$, reduce } = SC;
    const config = JSON.parse($('.fx-config', root).textContent);
    const t = config.text;
    const controller = new AbortController();
    const { signal } = controller;
    const timers = [];
    let alive = true;

    const on = (target, type, handler, options = {}) => target.addEventListener(type, handler, { signal, ...options });
    const html = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        if (text !== undefined) {
            node.textContent = text;
        }
        return node;
    };
    const wait = (ms) =>
        new Promise((resolve) => {
            const id = setTimeout(resolve, ms);
            timers.push(id);
        });

    const fit = $('#fit', root);
    const rulesEl = $('.rules', fit);
    const flow = $('#flow', fit);
    const score = $('#fit-score', fit);
    const icon = { x: '✕', w: '↻', v: '✓' };
    let rIdx = 0;
    let mode = 't';

    config.rules.forEach((r, i) => {
        const b = html('button', i === 0 ? 'on' : '', r.k);
        b.type = 'button';
        b.dataset.i = i;
        rulesEl.appendChild(b);
    });

    const renderFit = () => {
        const r = config.rules[rIdx];
        const items = mode === 'c' ? r.c : r.t;
        $$('.switch button', fit).forEach((b) => {
            b.classList.toggle('on', b.dataset.m === mode);
            b.classList.toggle('acc-on', b.dataset.m === 'c');
        });
        $('#fit-title', fit).textContent = '«' + r.rule + '»';
        flow.replaceChildren(
            ...items.map((it, i) => {
                const li = html('li', 'f-' + it[0]);
                li.style.transitionDelay = i * 90 + 'ms';
                li.append(html('i', '', icon[it[0]]), html('span', '', it[1]));
                return li;
            }),
        );
        requestAnimationFrame(() => $$('#flow li', fit).forEach((li) => li.classList.add('in')));
        const manual = items.filter((i) => i[0] === 'w').length;
        const [before, after] = t.workarounds.split(':count');
        const count = html('b', mode === 'c' ? 'ok' : '', mode === 'c' ? '0' : String(manual));
        score.replaceChildren(before, count);
        if (after) {
            score.append(after);
        }
        if (mode !== 'c') {
            score.append(' ', html('span', '', t.rule_stays_outside));
        }
    };
    $$('.rules button', fit).forEach((b) =>
        on(b, 'click', () => {
            rIdx = Number(b.dataset.i);
            $$('.rules button', fit).forEach((x) => x.classList.toggle('on', x === b));
            renderFit();
        }),
    );
    $$('.switch button', fit).forEach((b) =>
        on(b, 'click', () => {
            mode = b.dataset.m;
            renderFit();
        }),
    );
    renderFit();

    const tests = $('#tests', root);
    const runs = $$('.run', tests);
    const meter = $('.meter i', tests);
    let ran = false;
    const setState = (run, text) => (run.querySelector('b').textContent = text);
    const runTests = async () => {
        runs.forEach((r) => {
            r.classList.remove('done');
            setState(r, t.waiting);
        });
        meter.style.transform = 'scaleX(0)';
        for (let i = 0; i < runs.length; i++) {
            const isSecurity = runs[i].classList.contains('sec');
            setState(runs[i], isSecurity ? t.analysing : t.running);
            await wait(reduce ? 0 : 480);
            if (!alive) {
                return;
            }
            runs[i].classList.add('done');
            setState(runs[i], i === runs.length - 1 ? t.none : isSecurity ? t.clean : t.passed);
            meter.style.transform = `scaleX(${(i + 1) / runs.length})`;
        }
    };
    on($('#rerun', tests), 'click', runTests);

    const nav = $('#ma-nav', root);
    const grid = $('#ma-grid', root);
    const count = $('#modcount', root);
    const addBtn = $('#addmod', root);
    let n = 3;
    const viz = (k) => {
        const b = html('b', 'v-' + { bar: 'bar', chart: 'chart', rows: 'rows', phone: 'phone', ai: 'ai' }[k] || 'dots');
        const kids = { bar: 1, chart: 5, rows: 3, phone: 0, ai: 0 }[k] ?? 5;
        for (let i = 0; i < kids; i++) {
            const bar = html('i');
            if (k === 'chart') {
                bar.style.height = [40, 65, 50, 85, 100][i] + '%';
            }
            b.appendChild(bar);
        }
        if (k === 'ai') {
            b.textContent = 'AI';
        }
        return b;
    };
    const drawApp = (fresh) => {
        const mods = config.modules.slice(0, n);
        nav.replaceChildren(
            ...mods.map((m, i) => {
                const li = html('li', (i === 0 ? 'cur' : '') + (fresh && i === n - 1 ? ' fresh' : ''), m.name);
                if (fresh && i === n - 1) {
                    li.appendChild(html('em', '', t.new));
                }
                return li;
            }),
        );
        grid.replaceChildren(
            ...mods.map((m, i) => {
                const card = html('div', 'ma-card' + (fresh && i === n - 1 ? ' fresh' : ''));
                card.append(html('span', '', m.name), html('strong', '', m.value), html('small', '', m.label), viz(m.viz));
                return card;
            }),
        );
        count.textContent = t.modules.replace(':count', String(n));
    };
    drawApp(false);
    on(addBtn, 'click', () => {
        if (n >= config.modules.length) {
            n = 3;
            drawApp(false);
        } else {
            n++;
            drawApp(true);
        }
        addBtn.querySelector('span').textContent = n >= config.modules.length ? t.restart : t.add_module;
    });

    const autos = $('#autos', root);
    const alog = $('#alog', root);
    const manualEl = $('#manual', root);
    const total = config.automations.length;
    const idleItem = () => html('li', 'idle', t.all_manual);
    autos.replaceChildren(
        ...config.automations.map((a, i) => {
            const b = html('button', 'auto');
            b.type = 'button';
            b.dataset.i = i;
            b.setAttribute('aria-pressed', 'false');
            const txt = html('span', 'a-txt');
            txt.append(html('b', '', a.name), html('span', 'pain', a.pain), html('span', 'fixed', t.done_automatically));
            const sw = html('span', 'a-sw');
            sw.appendChild(html('i'));
            b.append(txt, sw);
            return b;
        }),
    );
    alog.replaceChildren(idleItem());
    let clock = 8 * 60 + 2;
    const stamp = () => {
        clock += 3 + Math.floor(Math.random() * 9);
        return String(Math.floor(clock / 60) % 24).padStart(2, '0') + ':' + String(clock % 60).padStart(2, '0');
    };
    const pushLog = (i) => {
        alog.querySelector('.idle')?.remove();
        const a = config.automations[i];
        const li = html('li');
        li.append(
            html('time', '', stamp()),
            html('i', '', '✓'),
            html('span', '', a.ev[Math.floor(Math.random() * a.ev.length)]),
        );
        alog.prepend(li);
        requestAnimationFrame(() => li.classList.add('in'));
        while (alog.children.length > 4) {
            alog.lastChild.remove();
        }
    };
    const onIdx = () =>
        $$('.auto', autos)
            .filter((b) => b.getAttribute('aria-pressed') === 'true')
            .map((b) => Number(b.dataset.i));
    const renderManual = (active) => {
        manualEl.textContent = t.by_hand.replace(':left', String(total - active)).replace(':total', String(total));
        manualEl.classList.toggle('ok', active === total);
    };
    renderManual(0);
    $$('.auto', autos).forEach((b) =>
        on(b, 'click', () => {
            const isOn = b.getAttribute('aria-pressed') !== 'true';
            b.setAttribute('aria-pressed', String(isOn));
            if (isOn) {
                pushLog(Number(b.dataset.i));
            }
            const active = onIdx().length;
            renderManual(active);
            if (!active) {
                alog.replaceChildren(idleItem());
            }
        }),
    );
    timers.push(
        setInterval(() => {
            const active = onIdx();
            if (!active.length || reduce) {
                return;
            }
            const r = autos.getBoundingClientRect();
            if (r.bottom < 0 || r.top > innerHeight) {
                return;
            }
            pushLog(active[Math.floor(Math.random() * active.length)]);
        }, 2400),
    );

    const validate = (id, test, ok, bad) => {
        const f = $(id, root);
        const input = f.querySelector('input');
        const msg = f.querySelector('.msg');
        on(input, 'input', () => {
            const v = input.value.trim();
            f.classList.remove('good', 'bad');
            if (!v) {
                msg.textContent = t.write_something;
                return;
            }
            const passes = test(v);
            f.classList.add(passes ? 'good' : 'bad');
            msg.textContent = passes ? ok : bad;
        });
    };
    validate('#f-mail', (v) => /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(v), t.email_ok, t.email_bad);
    validate('#f-iva', (v) => /^\d{11}$/.test(v), t.vat_ok, t.vat_bad);

    const tabs = $$('.uix-tabs button', root);
    tabs.forEach((b) =>
        on(b, 'click', () => {
            tabs.forEach((x) => x.classList.toggle('on', x === b));
            $$('.uix-pane', root).forEach((p) => p.classList.toggle('on', p.dataset.p === b.dataset.t));
        }),
    );

    let dragged = null;
    const kbCols = $$('.kb-col', root);
    const place = (card, col) => {
        col.appendChild(card);
        card.classList.toggle('done', col.dataset.c === '2');
        card.classList.remove('pop');
        void card.offsetWidth;
        card.classList.add('pop');
    };
    $$('.kb-card', root).forEach((c) => {
        on(c, 'dragstart', (e) => {
            dragged = c;
            c.classList.add('lift');
            e.dataTransfer.effectAllowed = 'move';
            try {
                e.dataTransfer.setData('text/plain', 'x');
            } catch (_) {
                /* some browsers refuse setData */
            }
        });
        on(c, 'dragend', () => {
            c.classList.remove('lift');
            kbCols.forEach((k) => k.classList.remove('over'));
            dragged = null;
        });
        on(c, 'click', () => place(c, kbCols[(Number(c.parentElement.dataset.c) + 1) % 3]));
    });
    kbCols.forEach((col) => {
        on(col, 'dragover', (e) => {
            e.preventDefault();
            col.classList.add('over');
        });
        on(col, 'dragleave', () => col.classList.remove('over'));
        on(col, 'drop', (e) => {
            e.preventDefault();
            col.classList.remove('over');
            if (dragged) {
                place(dragged, col);
            }
        });
    });

    const sign = $('.ph-sign', root);
    const sx = sign.getContext('2d');
    const send = $('.ph-send', root);
    const checks = $$('.chk', root);
    let signed = false;
    let sd = false;
    let sl = null;
    const check = () => {
        send.disabled = !(checks.every((c) => c.classList.contains('on')) && signed);
    };
    const clearSign = () => {
        sx.fillStyle = '#F1F0EC';
        sx.fillRect(0, 0, sign.width, sign.height);
        sx.strokeStyle = '#C9C8C3';
        sx.beginPath();
        sx.moveTo(20, 96);
        sx.lineTo(440, 96);
        sx.stroke();
    };
    clearSign();
    const sp = (e) => {
        const r = sign.getBoundingClientRect();
        return [((e.clientX - r.left) / r.width) * sign.width, ((e.clientY - r.top) / r.height) * sign.height];
    };
    on(sign, 'pointerdown', (e) => {
        sd = true;
        sl = sp(e);
        sign.setPointerCapture(e.pointerId);
    });
    on(sign, 'pointermove', (e) => {
        if (!sd) {
            return;
        }
        const p = sp(e);
        sx.strokeStyle = '#0A0A0A';
        sx.lineWidth = 3;
        sx.lineCap = 'round';
        sx.beginPath();
        sx.moveTo(sl[0], sl[1]);
        sx.lineTo(p[0], p[1]);
        sx.stroke();
        sl = p;
        signed = true;
        check();
    });
    on(window, 'pointerup', () => (sd = false));
    checks.forEach((c) =>
        on(c, 'click', () => {
            c.classList.toggle('on');
            check();
        }),
    );
    on(send, 'click', () => {
        send.textContent = t.report_sent;
        send.classList.add('sent');
        timers.push(
            setTimeout(() => {
                send.textContent = t.close_job;
                send.classList.remove('sent');
                checks.forEach((c) => c.classList.remove('on'));
                signed = false;
                clearSign();
                check();
            }, 2600),
        );
    });

    const cfg = { mat: 'rovere', len: '160', del: 'std' };
    const MAT = { rovere: ['#B8895A', 0], noce: ['#6B4A33', 180], nero: ['#1A1A1A', 120] };
    const LEN = { 120: [0.6, 980], 160: [0.8, 1240], 200: [1, 1520] };
    const drawCfg = () => {
        const o = $('#cfg-obj', root);
        o.style.background = MAT[cfg.mat][0];
        o.style.width = LEN[cfg.len][0] * 100 + '%';
        o.style.borderColor = cfg.mat === 'nero' ? '#3A3A3A' : 'transparent';
        $('#cfg-dim', root).textContent = cfg.len + ' cm';
        const price = LEN[cfg.len][1] + MAT[cfg.mat][1] + (cfg.del === 'exp' ? 90 : 0);
        $('#cfg-price', root).textContent = '€ ' + price.toLocaleString(config.locale);
    };
    $$('.cfg-row', root).forEach((row) =>
        $$('button', row).forEach((b) =>
            on(b, 'click', () => {
                $$('button', row).forEach((x) => x.classList.toggle('on', x === b));
                cfg[row.dataset.k] = b.dataset.v;
                drawCfg();
            }),
        ),
    );
    drawCfg();

    SC.onFrame.push(() => {
        if (!ran && tests.getBoundingClientRect().top < innerHeight * 0.7) {
            ran = true;
            runTests();
        }
    });

    return () => {
        alive = false;
        controller.abort();
        timers.forEach((id) => {
            clearTimeout(id);
            clearInterval(id);
        });
    };
}
