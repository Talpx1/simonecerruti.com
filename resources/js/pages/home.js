const SW = 4.4;
const SH = 2.75;
const GU = 44;
const GV = 27.5;
const FONT = '"Archivo", "Helvetica Neue", Arial, sans-serif';
const toW = (u, v) => [(u / GU) * SW - SW / 2, SH / 2 - (v / GV) * SH];

const UIR = [[0, 0, 44, 27.5], [0, 0, 8, 27.5], [8, 0, 36, 3.4], [9.5, 4.4, 7.6, 5.4], [18.3, 4.4, 7.6, 5.4], [27.1, 4.4, 7.6, 5.4], [35.9, 4.4, 7.6, 5.4], [9.5, 11, 21.5, 15.3], [32.3, 11, 11.2, 15.3], [35.5, 0.9, 7, 1.6], [24, 0.9, 10.5, 1.6]];
const CORE = [15.5, 10.9, 13, 5.7];
const MODS = [[3, 2.6, 9, 3.2], [17.5, 1.6, 9, 3.2], [32, 2.6, 9, 3.2], [1, 12.15, 8, 3.2, 1], [34.5, 12.15, 8.5, 3.2], [3, 21.7, 9, 3.2, 1], [17.5, 22.7, 9, 3.2], [32, 21.7, 9, 3.2]];
const BARS = [6, 8, 5, 9, 7, 4, 3, 7, 9, 6, 10, 8, 9, 11];

function exitPoint(r, cx, cy, dx, dy) {
    const hw = r[2] / 2;
    const hh = r[3] / 2;
    const tx = dx ? hw / Math.abs(dx) : 1e9;
    const ty = dy ? hh / Math.abs(dy) : 1e9;
    const t = Math.min(tx, ty);
    return [cx + dx * t, cy + dy * t];
}

const CONN = MODS.map((m) => {
    const mc = [m[0] + m[2] / 2, m[1] + m[3] / 2];
    const cc = [CORE[0] + CORE[2] / 2, CORE[1] + CORE[3] / 2];
    const dx = cc[0] - mc[0];
    const dy = cc[1] - mc[1];
    const l = Math.hypot(dx, dy);
    const a = exitPoint(m, mc[0], mc[1], dx / l, dy / l);
    const b = exitPoint(CORE, cc[0], cc[1], -dx / l, -dy / l);
    return [a[0], a[1], b[0], b[1], m[4] ? 1 : 0];
});

const rectSegs = (r) => [
    [r[0], r[1], r[0] + r[2], r[1]],
    [r[0] + r[2], r[1], r[0] + r[2], r[1] + r[3]],
    [r[0] + r[2], r[1] + r[3], r[0], r[1] + r[3]],
    [r[0], r[1] + r[3], r[0], r[1]],
];

function roundRect(x, X, Y, W, H, R) {
    x.beginPath();
    x.moveTo(X + R, Y);
    x.arcTo(X + W, Y, X + W, Y + H, R);
    x.arcTo(X + W, Y + H, X, Y + H, R);
    x.arcTo(X, Y + H, X, Y, R);
    x.arcTo(X, Y, X + W, Y, R);
    x.closePath();
}

export default function (el, SC) {
    const { reduce, fine, clamp, lerp, smooth, $, $$, mouse } = SC;
    const cfg = JSON.parse(el.dataset.config || '{}');
    const doc = document.documentElement;
    const ease = (t) => 1 - Math.pow(1 - clamp(t, 0, 1), 3);

    let disposed = false;
    let vw = innerWidth;
    let vh = innerHeight;
    let sy = scrollY;
    let horizontal = vw > 900;
    let gl = null;
    let mqT = 0;
    let lastChap = -1;
    const secs = {};
    const cleanups = [];
    const on = (target, type, fn, opts) => {
        target.addEventListener(type, fn, opts);
        cleanups.push(() => target.removeEventListener(type, fn, opts));
    };

    const mqRows = $$('.mq-in', el).map((e) => ({ e, dir: Number(e.parentElement.dataset.dir) }));
    const screen = $('.screen', el);
    const wItems = $$('.w-it', el);
    const wShots = $$('.shot', el);
    const wNows = $$('.w-now', el);
    const giants = $$('.giant span', el);
    const chs = $$('.ch', el);
    const idxBtns = $$('.ch-index button', el);
    const portrait = $('.portrait', el);
    let wActive = -1;
    let scT = 1;
    let scV = 1;
    let scHover = false;
    let scSince = 0;

    const setWork = (i) => {
        if (i === wActive) {
            return;
        }
        wActive = i;
        wItems.forEach((e, j) => e.classList.toggle('on', j === i));
        wShots.forEach((e, j) => e.classList.toggle('on', j === i));
        wNows.forEach((e, j) => e.classList.toggle('on', j === i));
        scV = 0;
        scSince = performance.now();
    };

    if (screen) {
        on(screen, 'pointermove', (e) => {
            const b = screen.getBoundingClientRect();
            scHover = true;
            scT = clamp((e.clientX - b.left) / b.width, 0, 1);
        });
        on(screen, 'pointerleave', () => {
            scHover = false;
            scT = 1;
        });
    }

    wItems.forEach((b, i) =>
        on(b, 'click', () => {
            const s = secs.works;
            if (horizontal && s) {
                scrollTo({ top: s.top + ((i + 0.5) / wItems.length) * (s.h - vh), behavior: reduce ? 'auto' : 'smooth' });
            } else {
                setWork(i);
            }
        }),
    );

    idxBtns.forEach((b) =>
        on(b, 'click', () => {
            const s = secs.story;
            if (!s) {
                return;
            }
            scrollTo({ top: s.top + ((Number(b.dataset.go) + 0.6) / 6) * (s.h - vh), behavior: reduce ? 'auto' : 'smooth' });
        }),
    );

    function layout() {
        vw = innerWidth;
        vh = innerHeight;
        horizontal = vw > 900;
        $$('[data-sec]', el).forEach((s) => {
            const r = s.getBoundingClientRect();
            secs[s.dataset.sec] = { top: r.top + scrollY, h: s.offsetHeight };
        });
        if (gl) {
            gl.renderer.setSize(vw, vh, false);
            gl.camera.aspect = vw / vh;
            gl.camera.updateProjectionMatrix();
        }
    }

    on(window, 'resize', layout);
    on(window, 'load', layout);
    const ro = new ResizeObserver(layout);
    ro.observe(el);
    cleanups.push(() => ro.disconnect());
    layout();

    const anchors = {
        hero: { x: 0, y: 0, s: 1, g: 1 },
        mq: { x: 0, y: 0, s: 1, g: 0.6 },
        story: { x: 0, y: 0.15, s: 1, g: 1 },
        works: { x: 0, y: 0.15, s: 1, g: 0 },
        svc: { x: 0, y: 0, s: 1, g: 0 },
        man: { x: 0, y: 0, s: 1, g: 0 },
        about: { x: 0, y: 0, s: 1, g: 0 },
        contact: { x: 0, y: 0, s: 1, g: 0 },
    };

    const archLabels = cfg.arch?.modules || [];
    const ui = cfg.ui || {};

    function drawArch(cv, p) {
        const x = cv.getContext('2d');
        const k = cv.width / GU;
        x.clearRect(0, 0, cv.width, cv.height);
        x.lineCap = 'round';
        const elp = (i) => clamp((p - 0.12 - i * 0.075) / 0.22, 0, 1);
        x.globalAlpha = clamp(p / 0.2, 0, 1);
        x.strokeStyle = 'rgba(241,240,236,.55)';
        x.lineWidth = 1.6;
        x.setLineDash([]);
        x.strokeRect(CORE[0] * k, CORE[1] * k, CORE[2] * k, CORE[3] * k);
        x.fillStyle = 'rgba(241,240,236,.9)';
        x.font = `800 ${0.62 * k}px ${FONT}`;
        x.textAlign = 'center';
        x.fillText(cfg.arch?.core || '', (CORE[0] + CORE[2] / 2) * k, (CORE[1] + 2.75) * k);
        x.fillStyle = 'rgba(241,240,236,.4)';
        x.font = `500 ${0.42 * k}px ${FONT}`;
        x.fillText(cfg.arch?.core_sub || '', (CORE[0] + CORE[2] / 2) * k, (CORE[1] + 3.75) * k);
        MODS.forEach((m, i) => {
            const e = elp(i);
            if (e <= 0) {
                return;
            }
            x.globalAlpha = Math.min(1, e * 1.6) * 0.9;
            x.strokeStyle = 'rgba(241,240,236,.32)';
            x.lineWidth = 1.3;
            x.setLineDash([]);
            x.strokeRect(m[0] * k, m[1] * k, m[2] * k, m[3] * k);
            x.fillStyle = 'rgba(241,240,236,.7)';
            x.font = `650 ${0.46 * k}px ${FONT}`;
            x.textAlign = 'center';
            x.fillText(archLabels[i] || '', (m[0] + m[2] / 2) * k, (m[1] + m[3] / 2 + 0.17) * k);
            const c = CONN[i];
            const d = clamp((e - 0.35) / 0.65, 0, 1);
            if (d > 0) {
                x.strokeStyle = 'rgba(241,240,236,.3)';
                x.lineWidth = 1.3;
                x.setLineDash([2, 7]);
                x.beginPath();
                x.moveTo(c[0] * k, c[1] * k);
                x.lineTo((c[0] + (c[2] - c[0]) * d) * k, (c[1] + (c[3] - c[1]) * d) * k);
                x.stroke();
                if (d >= 1) {
                    x.fillStyle = 'rgba(241,240,236,.6)';
                    x.beginPath();
                    x.arc(c[2] * k, c[3] * k, 4, 0, Math.PI * 2);
                    x.fill();
                }
            }
        });
        x.setLineDash([]);
        x.globalAlpha = 1;
    }

    const formatNumber = (value, digits) =>
        value.toLocaleString(cfg.locale || undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits });

    function drawUI(cv, a) {
        const x = cv.getContext('2d');
        const k = cv.width / GU;
        const S = (u) => u * k;
        const txt = (t, u, v, size, weight, color, align) => {
            x.font = `${weight} ${S(size)}px ${FONT}`;
            x.fillStyle = color;
            x.textAlign = align || 'left';
            x.fillText(t, S(u), S(v));
        };
        const skel = (u, v, w, h) => {
            x.fillStyle = '#222';
            roundRect(x, S(u), S(v), S(w), S(h), S(Math.min(h / 2, 0.3)));
            x.fill();
        };
        const data = ease(a / 0.42);
        const arrived = ease((a - 0.5) / 0.28);
        x.clearRect(0, 0, cv.width, cv.height);
        x.fillStyle = '#0F0F0F';
        x.fillRect(0, 0, cv.width, cv.height);
        x.fillStyle = '#0A0A0A';
        x.fillRect(0, 0, S(8), cv.height);
        x.fillStyle = '#FF4A1C';
        x.fillRect(S(1), S(1.05), S(0.9), S(0.9));
        txt(ui.app || '', 2.3, 1.82, 0.62, 800, '#F1F0EC');
        (ui.nav || []).forEach((n, i) => {
            const v = 5.2 + i * 2.05;
            if (i === 0) {
                x.fillStyle = '#1C1C1C';
                roundRect(x, S(0.6), S(v - 1.0), S(6.8), S(1.5), S(0.25));
                x.fill();
                x.fillStyle = '#FF4A1C';
                x.fillRect(0, S(v - 0.9), S(0.18), S(1.3));
            }
            txt(n, 1.4, v, 0.58, i === 0 ? 750 : 550, i === 0 ? '#F1F0EC' : '#8C8B86');
        });
        x.fillStyle = '#262626';
        x.beginPath();
        x.arc(S(1.9), S(25.6), S(0.7), 0, Math.PI * 2);
        x.fill();
        txt(ui.user || '', 3, 25.8, 0.52, 650, '#BDBCB7');
        txt(ui.greeting || '', 9.5, 2.05, 0.95, 850, '#F1F0EC');
        const nameU = 9.5 + x.measureText(`${ui.greeting || ''} `).width / k;
        if (data > 0.02) {
            x.globalAlpha = data;
            txt(ui.first_name || '', nameU, 2.05, 0.95, 850, '#F1F0EC');
            x.globalAlpha = 1;
        } else {
            skel(nameU, 1.25, 4.2, 0.95);
        }
        txt(data > 0.5 ? ui.all_good : ui.loading, 9.5, 2.95, 0.46, 550, '#8C8B86');
        x.strokeStyle = '#2A2A2A';
        x.lineWidth = 2;
        roundRect(x, S(24), S(0.9), S(10.5), S(1.6), S(0.3));
        x.stroke();
        txt(ui.search || '', 24.6, 1.92, 0.5, 500, '#6E6D69');
        x.fillStyle = '#FF4A1C';
        roundRect(x, S(35.5), S(0.9), S(7), S(1.6), S(0.3));
        x.fill();
        txt(ui.new_order || '', 39, 1.93, 0.55, 800, '#0A0A0A', 'center');
        const kpis = ui.kpis || [];
        const values = [
            () => String(Math.round(128 * data) + (arrived > 0.5 ? 1 : 0)),
            () => String(Math.round(36 * data) + (arrived > 0.5 ? 1 : 0)),
            () => `${ui.currency || ''} ${formatNumber(84.2 * data, 1)}k`,
            () => String(Math.round(4 * data)),
        ];
        kpis.forEach((c, i) => {
            const u = 9.5 + i * 8.8;
            x.fillStyle = '#161616';
            roundRect(x, S(u), S(4.4), S(7.6), S(5.4), S(0.35));
            x.fill();
            txt(c.label, u + 0.6, 5.6, 0.48, 600, '#8C8B86');
            if (data < 0.02) {
                skel(u + 0.6, 6.6, 3.6, 1.5);
                skel(u + 0.6, 8.55, 5, 0.45);
            } else {
                x.globalAlpha = Math.min(1, data * 2);
                txt(values[i](), u + 0.6, 7.95, 1.55, 850, i === 3 ? '#FF4A1C' : '#F1F0EC');
                txt(c.sub, u + 0.6, 9.05, 0.42, 550, i === 3 ? '#FF4A1C' : '#6E6D69');
                x.globalAlpha = 1;
            }
        });
        x.fillStyle = '#141414';
        roundRect(x, S(9.5), S(11), S(21.5), S(15.3), S(0.35));
        x.fill();
        txt(ui.chart_title || '', 10.2, 12.4, 0.55, 750, '#F1F0EC');
        BARS.forEach((b, i) => {
            const local = ease(clamp((data - i * 0.03) / 0.6, 0, 1));
            const h = Math.max(0.25, b * 0.85 * local);
            const u = 10.6 + i * 1.42;
            x.fillStyle = data < 0.02 ? '#202020' : i === 13 ? '#FF4A1C' : 'rgba(241,240,236,.82)';
            x.fillRect(S(u), S(24.6 - h), S(0.82), S(h));
        });
        x.strokeStyle = '#2A2A2A';
        x.lineWidth = 2;
        x.beginPath();
        x.moveTo(S(10.2), S(24.6));
        x.lineTo(S(30.3), S(24.6));
        x.stroke();
        x.fillStyle = '#141414';
        roundRect(x, S(32.3), S(11), S(11.2), S(15.3), S(0.35));
        x.fill();
        txt(ui.orders_title || '', 33, 12.4, 0.55, 750, '#F1F0EC');
        const tones = [['#2A2A2A', '#F1F0EC'], ['#1F1F1F', '#BDBCB7'], ['#1F1F1F', '#BDBCB7'], ['#1F1F1F', '#8C8B86'], ['#1F1F1F', '#8C8B86']];
        const rows = (ui.rows || []).map((r, i) => [r.id, r.who, r.status, tones[i][0], tones[i][1]]);
        const shift = arrived * 2.55;
        x.save();
        x.beginPath();
        x.rect(S(32.3), S(13), S(11.2), S(13));
        x.clip();
        const row = (r, v, alpha, hl) => {
            x.globalAlpha = alpha;
            if (hl) {
                x.fillStyle = 'rgba(255,74,28,.14)';
                x.fillRect(S(32.6), S(v - 1.2), S(10.6), S(2.2));
                x.fillStyle = '#FF4A1C';
                x.fillRect(S(32.6), S(v - 1.2), S(0.14), S(2.2));
            }
            txt(r[0], 33.1, v - 0.2, 0.46, 800, hl ? '#FF4A1C' : '#F1F0EC');
            txt(r[1], 33.1, v + 0.55, 0.42, 550, '#8C8B86');
            x.fillStyle = r[3];
            roundRect(x, S(38.6), S(v - 0.75), S(4.3), S(1.05), S(0.5));
            x.fill();
            txt(r[2], 40.75, v - 0.02, 0.36, 700, r[4], 'center');
            x.globalAlpha = 1;
        };
        if (arrived > 0 && ui.incoming) {
            row([ui.incoming.id, ui.incoming.who, ui.incoming.status, 'rgba(255,74,28,.25)', '#FF4A1C'], 14.6 - (1 - arrived) * 2.55, arrived, true);
        }
        rows.forEach((r, i) => {
            const v = 14.6 + i * 2.55 + shift;
            const al = clamp((data - i * 0.08) / 0.4, 0, 1);
            if (al <= 0) {
                skel(33.1, v - 0.75, 4 + (i % 2), 0.45);
                skel(33.1, v + 0.15, 2.8, 0.35);
                skel(38.6, v - 0.75, 4.3, 1.05);
            } else {
                row(r, v, al, false);
            }
        });
        x.restore();
        x.strokeStyle = '#2E2E2E';
        x.lineWidth = 3;
        x.strokeRect(1.5, 1.5, cv.width - 3, cv.height - 3);
        x.strokeStyle = '#1F1F1F';
        x.lineWidth = 2;
        x.beginPath();
        x.moveTo(S(8), 0);
        x.lineTo(S(8), cv.height);
        x.stroke();
    }

    function drawToast(cv) {
        const x = cv.getContext('2d');
        const W = cv.width;
        const H = cv.height;
        x.clearRect(0, 0, W, H);
        x.fillStyle = '#1B1B1B';
        roundRect(x, 2, 2, W - 4, H - 4, 22);
        x.fill();
        x.strokeStyle = '#333';
        x.lineWidth = 2;
        x.stroke();
        x.fillStyle = '#FF4A1C';
        x.beginPath();
        x.arc(70, H / 2, 30, 0, Math.PI * 2);
        x.fill();
        x.strokeStyle = '#0A0A0A';
        x.lineWidth = 7;
        x.lineCap = 'round';
        x.beginPath();
        x.moveTo(56, H / 2);
        x.lineTo(66, H / 2 + 11);
        x.lineTo(86, H / 2 - 12);
        x.stroke();
        x.fillStyle = '#F1F0EC';
        x.font = `800 38px ${FONT}`;
        x.textAlign = 'left';
        x.fillText(cfg.toast?.title || '', 124, H / 2 - 8);
        x.fillStyle = '#8C8B86';
        x.font = `550 28px ${FONT}`;
        x.fillText(cfg.toast?.sub || '', 124, H / 2 + 34);
    }

    function makeAtlas() {
        const c = document.createElement('canvas');
        c.width = 512;
        c.height = 64;
        const x = c.getContext('2d');
        x.fillStyle = '#fff';
        x.textAlign = 'center';
        x.textBaseline = 'middle';
        ['{', '}', '<', '/>', ';', '=', '(', ')'].forEach((g, i) => {
            x.font = `700 ${g.length > 1 ? 34 : 46}px ${FONT}`;
            x.fillText(g, i * 64 + 32, 34);
        });
        return c;
    }

    function init3D(THREE) {
        const canvas = $('#gl', el);
        let renderer;
        try {
            renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
        } catch (e) {
            return null;
        }
        THREE.ColorManagement.enabled = false;
        renderer.outputColorSpace = THREE.LinearSRGBColorSpace;
        renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
        renderer.setSize(vw, vh, false);
        renderer.localClippingEnabled = true;
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(35, vw / vh, 0.1, 100);
        camera.position.set(0, 0, 9);
        const group = new THREE.Group();
        scene.add(group);
        const maxAniso = renderer.capabilities.getMaxAnisotropy();
        const canvasTex = (cv) => {
            const t = new THREE.CanvasTexture(cv);
            t.anisotropy = maxAniso;
            t.minFilter = THREE.LinearFilter;
            t.generateMipmaps = false;
            return t;
        };

        const sCluster = () => {
            const m = MODS[Math.floor(Math.random() * MODS.length)];
            return toW(m[0] + 0.5 + Math.random() * (m[2] - 1), m[1] + 0.5 + Math.random() * (m[3] - 1));
        };
        const archSegs = [];
        rectSegs(CORE).forEach((s) => archSegs.push(s));
        MODS.forEach((m) => rectSegs(m).forEach((s) => archSegs.push(s)));
        CONN.forEach((c) => archSegs.push(c.slice(0, 4)));
        const sampler = (segs) => {
            const len = segs.map((s) => Math.hypot(s[2] - s[0], s[3] - s[1]));
            const tot = len.reduce((a, b) => a + b, 0);
            return () => {
                let d = Math.random() * tot;
                let k = 0;
                while (k < segs.length - 1 && d > len[k]) {
                    d -= len[k];
                    k++;
                }
                const s = segs[k];
                const f = len[k] ? d / len[k] : 0;
                return toW(s[0] + (s[2] - s[0]) * f, s[1] + (s[3] - s[1]) * f);
            };
        };
        const sArch = sampler(archSegs);

        const COUNT = vw < 900 ? 2600 : 6000;
        const pos = new Float32Array(COUNT * 3);
        const aW = new Float32Array(COUNT * 4);
        const aR = new Float32Array(COUNT * 3);
        const aQ = new Float32Array(COUNT * 3);
        const seed = new Float32Array(COUNT);
        for (let i = 0; i < COUNT; i++) {
            const r = 2.4 + Math.pow(Math.random(), 0.7) * 6.2;
            const th = Math.random() * Math.PI * 2;
            const ph = Math.acos(2 * Math.random() - 1);
            pos[i * 3] = r * Math.sin(ph) * Math.cos(th) * 1.5;
            pos[i * 3 + 1] = r * Math.cos(ph) * 0.85;
            pos[i * 3 + 2] = r * Math.sin(ph) * Math.sin(th) * 0.9;
            const perLine = Math.floor(COUNT / 30);
            aW[i * 4] = ((i % perLine) / perLine) * 18 - 9 + (Math.random() - 0.5) * 0.015;
            aW[i * 4 + 1] = Math.floor(i / perLine) % 30;
            aW[i * 4 + 2] = (Math.floor(i / perLine) % 7) / 7;
            aW[i * 4 + 3] = -1;
            const a = sCluster();
            aR[i * 3] = a[0] + (Math.random() - 0.5) * 0.01;
            aR[i * 3 + 1] = a[1] + (Math.random() - 0.5) * 0.01;
            aR[i * 3 + 2] = 0.02;
            const b = sArch();
            aQ[i * 3] = b[0];
            aQ[i * 3 + 1] = b[1];
            aQ[i * 3 + 2] = 0.03;
            seed[i] = Math.random();
        }
        const pg = new THREE.BufferGeometry();
        pg.setAttribute('position', new THREE.BufferAttribute(pos, 3));
        pg.setAttribute('aW', new THREE.BufferAttribute(aW, 4));
        pg.setAttribute('aR', new THREE.BufferAttribute(aR, 3));
        pg.setAttribute('aQ', new THREE.BufferAttribute(aQ, 3));
        pg.setAttribute('aSeed', new THREE.BufferAttribute(seed, 1));
        const atlas = new THREE.CanvasTexture(makeAtlas());
        const pm = new THREE.ShaderMaterial({
            transparent: true,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
            uniforms: {
                uTime: { value: 0 },
                uPre: { value: 0 },
                uT1: { value: 0 },
                uT2: { value: 0 },
                uFade: { value: 1 },
                uIntro: { value: 0 },
                uGlobal: { value: 1 },
                uMouse: { value: new THREE.Vector3(99, 99, 0) },
                uRepel: { value: 1 },
                uPix: { value: renderer.getPixelRatio() },
                uAcc: { value: new THREE.Color(0xff4a1c) },
                uAtlas: { value: atlas },
            },
            vertexShader: `
        uniform float uTime, uPre, uT1, uT2, uFade, uIntro, uRepel, uPix, uGlobal; uniform vec3 uMouse;
        attribute vec4 aW; attribute vec3 aR; attribute vec3 aQ; attribute float aSeed;
        varying float vA; varying float vAcc; varying float vG; varying float vGi;
        void main() {
          float L = aW.y;
          float x = mod(aW.x + uTime * (0.16 + 0.12 * aW.z) + 9.0, 18.0) - 9.0;
          float amp = 0.34 + 0.2 * sin(L * 0.37);
          float y = -2.55 + L * 0.175 + amp * sin(x * 0.48 + uTime * 0.42 + L * 0.2) + 0.1 * sin(x * 1.25 - uTime * 0.3 + L);
          vec3 w = vec3(x, y, -0.5 - mod(L, 3.0) * 0.4);
          vec3 c = position;
          float a = uTime * (0.025 + 0.05 * aSeed); float ca = cos(a), sa = sin(a);
          c.xz = mat2(ca, -sa, sa, ca) * c.xz;
          c.y += sin(uTime * 0.45 + aSeed * 40.0) * 0.16;
          float h = smoothstep(aSeed * 0.3, 0.7 + aSeed * 0.3, uPre);
          vec3 p = mix(w, c, h);
          float t1 = smoothstep(aSeed * 0.45, 0.55 + aSeed * 0.45, uT1);
          p = mix(p, aR, t1);
          float t2 = smoothstep(aSeed * 0.4, 0.6 + aSeed * 0.4, uT2);
          p = mix(p, aQ, t2);
          vec4 wp = modelMatrix * vec4(p, 1.0);
          vec2 d = wp.xy - uMouse.xy; float dist = length(d);
          float hot = exp(-dist * dist * 1.6);
          wp.y += hot * 0.28 * (1.0 - h);
          float f = smoothstep(1.3, 0.0, dist) * uRepel * h;
          wp.xy += normalize(d + 0.0001) * f * 0.45; wp.z += f * 0.25;
          vec4 mv = viewMatrix * wp;
          gl_Position = projectionMatrix * mv;
          float glyph = step(0.0, aW.w) * (1.0 - t1);
          float dotSize = mix(mix(2.0 + 0.9 * aSeed, 1.2 + 2.4 * aSeed, h), 1.6 + 0.8 * aSeed, t1);
          gl_PointSize = mix(dotSize * (9.0 / -mv.z), 12.0 + 6.0 * aSeed, glyph) * uPix * (1.0 + hot * 0.6 * (1.0 - h));
          float waveA = (0.5 + 0.35 * aSeed) * (0.6 + 0.4 * sin(aW.x * 0.35 + L * 0.5 + uTime * 0.6)) * uIntro + hot * 0.7;
          float chaosA = (0.25 + 0.75 * aSeed) * (0.6 + f * 0.8);
          vA = uFade * uGlobal * mix(mix(waveA, chaosA, h), 0.85, t1);
          vAcc = (step(0.95, aSeed) + hot * (1.0 - h) * step(0.55, aSeed)) * (1.0 - t1) + f * 0.5;
          vG = glyph; vGi = aW.w;
        }`,
            fragmentShader: `
        uniform vec3 uAcc; uniform sampler2D uAtlas;
        varying float vA; varying float vAcc; varying float vG; varying float vGi;
        void main() {
          vec2 pc = gl_PointCoord;
          float dotA = smoothstep(0.5, 0.0, length(pc - 0.5));
          float glyA = texture2D(uAtlas, vec2((pc.x + max(vGi, 0.0)) / 8.0, 1.0 - pc.y)).a;
          float s = mix(dotA, glyA, step(0.5, vG));
          if (s < 0.02) discard;
          vec3 col = mix(vec3(0.95, 0.94, 0.92), uAcc, clamp(vAcc, 0.0, 1.0));
          gl_FragColor = vec4(col, vA * s);
        }`,
        });
        group.add(new THREE.Points(pg, pm));

        const fragG = new THREE.Group();
        scene.add(fragG);
        const fragMat = new THREE.LineBasicMaterial({ color: 0xf1f0ec, transparent: true, opacity: 0 });
        const shapes = {
            sheet() {
                const s = [];
                const w = 1.6;
                const h = 1.1;
                for (let i = 0; i <= 6; i++) {
                    s.push(-w / 2 + (i * w) / 6, -h / 2, -w / 2 + (i * w) / 6, h / 2);
                }
                for (let j = 0; j <= 5; j++) {
                    s.push(-w / 2, -h / 2 + (j * h) / 5, w / 2, -h / 2 + (j * h) / 5);
                }
                return s;
            },
            mail() {
                const w = 1.4;
                const h = 0.9;
                return [-w / 2, -h / 2, w / 2, -h / 2, w / 2, -h / 2, w / 2, h / 2, w / 2, h / 2, -w / 2, h / 2, -w / 2, h / 2, -w / 2, -h / 2, -w / 2, h / 2, 0, 0, 0, 0, w / 2, h / 2];
            },
            doc() {
                const w = 1.0;
                const h = 1.3;
                const s = [-w / 2, -h / 2, w / 2, -h / 2, w / 2, -h / 2, w / 2, h / 2, w / 2, h / 2, -w / 2, h / 2, -w / 2, h / 2, -w / 2, -h / 2];
                for (let i = 0; i < 7; i++) {
                    const y = h / 2 - 0.22 - i * 0.15;
                    s.push(-w / 2 + 0.12, y, w / 2 - 0.12 - (i % 3) * 0.15, y);
                }
                return s;
            },
            note() {
                const w = 0.8;
                const s = [-w / 2, -w / 2, w / 2, -w / 2, w / 2, -w / 2, w / 2, w / 2, w / 2, w / 2, -w / 2, w / 2, -w / 2, w / 2, -w / 2, -w / 2];
                for (let i = 0; i < 3; i++) {
                    s.push(-0.28, 0.18 - i * 0.16, 0.24 - i * 0.08, 0.18 - i * 0.16);
                }
                return s;
            },
            cal() {
                const s = [];
                const w = 1.2;
                const h = 1.0;
                for (let i = 0; i <= 7; i++) {
                    s.push(-w / 2 + (i * w) / 7, -h / 2, -w / 2 + (i * w) / 7, h / 2 - 0.2);
                }
                for (let j = 0; j <= 5; j++) {
                    s.push(-w / 2, -h / 2 + (j * (h - 0.2)) / 5, w / 2, -h / 2 + (j * (h - 0.2)) / 5);
                }
                s.push(-w / 2, h / 2, w / 2, h / 2, -w / 2, h / 2, -w / 2, h / 2 - 0.2, w / 2, h / 2, w / 2, h / 2 - 0.2);
                return s;
            },
        };
        const plan = [['sheet', -3.6, 1.2, 0.2, 3, 1.6], ['mail', 3.4, 1.4, -0.4, 5, 1.4], ['doc', -3.1, -1.4, 0.3, 7, 1.0], ['note', 2.9, -1.3, 0.5, 2, 0.8], ['cal', 0.3, 2.1, -0.8, 6, 1.2], ['sheet', 1.0, -2.2, -0.2, 0, 1.6], ['doc', -1.4, 2.4, -1.0, 4, 1.0], ['note', 4.2, -0.2, 0.2, 1, 0.8]];
        const fragGeos = [];
        const frags = plan.map((p, i) => {
            const arr = shapes[p[0]]().reduce((acc, v, k) => {
                acc.push(v);
                if (k % 2 === 1) {
                    acc.push(0);
                }
                return acc;
            }, []);
            const g = new THREE.BufferGeometry();
            g.setAttribute('position', new THREE.Float32BufferAttribute(arr, 3));
            fragGeos.push(g);
            const m = new THREE.LineSegments(g, fragMat);
            m.userData = {
                home: new THREE.Vector3(p[1], p[2], p[3]),
                spin: new THREE.Vector3(Math.random() - 0.5, Math.random() - 0.5, Math.random() - 0.5).multiplyScalar(0.5),
                ph: Math.random() * 6,
                rot: new THREE.Euler(Math.random() * 2, Math.random() * 2, Math.random()),
                mod: p[4],
                w: p[5],
                k: i,
            };
            fragG.add(m);
            return m;
        });

        const archCv = document.createElement('canvas');
        archCv.width = 1760;
        archCv.height = 1100;
        const archTex = canvasTex(archCv);
        const archMat = new THREE.MeshBasicMaterial({ map: archTex, transparent: true, opacity: 0, depthWrite: false });
        const arch = new THREE.Mesh(new THREE.PlaneGeometry(SW, SH), archMat);
        arch.position.z = 0.04;
        arch.renderOrder = 3;
        group.add(arch);

        const lp = [];
        UIR.forEach((r) =>
            rectSegs(r).forEach((s) => {
                const a = toW(s[0], s[1]);
                const b = toW(s[2], s[3]);
                lp.push(a[0], a[1], 0.05, b[0], b[1], 0.05);
            }),
        );
        [5.2, 7.25, 9.3, 11.35, 13.4, 15.45].forEach((v) => {
            const a = toW(1.4, v - 0.3);
            const b = toW(6.4, v - 0.3);
            lp.push(a[0], a[1], 0.05, b[0], b[1], 0.05);
        });
        for (let i = 0; i < 14; i++) {
            const u = 10.6 + i * 1.42;
            const h = BARS[i] * 0.85;
            rectSegs([u, 24.6 - h, 0.82, h]).forEach((s) => {
                const a = toW(s[0], s[1]);
                const b = toW(s[2], s[3]);
                lp.push(a[0], a[1], 0.05, b[0], b[1], 0.05);
            });
        }
        const lg = new THREE.BufferGeometry();
        lg.setAttribute('position', new THREE.Float32BufferAttribute(lp, 3));
        const wclip = new THREE.Plane(new THREE.Vector3(0, 1, 0), 50);
        const wireMat = new THREE.LineDashedMaterial({ color: 0xf1f0ec, transparent: true, opacity: 0, dashSize: 0.0001, gapSize: 50, depthWrite: false, clippingPlanes: [wclip] });
        const wire = new THREE.LineSegments(lg, wireMat);
        const ld = new Float32Array(lp.length / 3);
        for (let i = 0; i < ld.length; i += 2) {
            ld[i] = 0;
            ld[i + 1] = Math.hypot(lp[(i + 1) * 3] - lp[i * 3], lp[(i + 1) * 3 + 1] - lp[i * 3 + 1]);
        }
        lg.setAttribute('lineDistance', new THREE.BufferAttribute(ld, 1));
        wire.renderOrder = 4;
        group.add(wire);

        const uiCv = document.createElement('canvas');
        uiCv.width = 2200;
        uiCv.height = 1375;
        const uiTex = canvasTex(uiCv);
        const clip = new THREE.Plane(new THREE.Vector3(0, -1, 0), -10);
        const uiMat = new THREE.MeshBasicMaterial({ map: uiTex, transparent: true, opacity: 1, clippingPlanes: [clip], depthWrite: false });
        const uiMesh = new THREE.Mesh(new THREE.PlaneGeometry(SW, SH), uiMat);
        uiMesh.position.z = 0;
        uiMesh.renderOrder = 2;
        group.add(uiMesh);
        const scan = new THREE.Mesh(new THREE.PlaneGeometry(SW * 1.06, 0.014), new THREE.MeshBasicMaterial({ color: 0xff4a1c, transparent: true, opacity: 0, depthWrite: false }));
        scan.renderOrder = 10;
        group.add(scan);

        const toastCv = document.createElement('canvas');
        toastCv.width = 820;
        toastCv.height = 160;
        drawToast(toastCv);
        const toastMat = new THREE.MeshBasicMaterial({ map: canvasTex(toastCv), transparent: true, opacity: 0, depthWrite: false });
        const toast = new THREE.Mesh(new THREE.PlaneGeometry(1.95, (1.95 * 160) / 820), toastMat);
        toast.renderOrder = 12;
        group.add(toast);

        const redraw = { arch: -1, ui: -1 };
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => {
                redraw.arch = -1;
                redraw.ui = -1;
                drawToast(toastCv);
                toastMat.map.needsUpdate = true;
            });
        }

        const dispose = () => {
            scene.traverse((o) => {
                o.geometry?.dispose();
                const mats = Array.isArray(o.material) ? o.material : o.material ? [o.material] : [];
                mats.forEach((m) => {
                    m.map?.dispose();
                    m.dispose();
                });
            });
            atlas.dispose();
            fragGeos.forEach((g) => g.dispose());
            renderer.dispose();
            renderer.forceContextLoss();
        };

        return {
            THREE,
            renderer,
            scene,
            camera,
            group,
            pm,
            frags,
            fragMat,
            wclip,
            tmpV: new THREE.Vector3(),
            tmpQ: new THREE.Quaternion(),
            tmpQ2: new THREE.Quaternion(),
            archCv,
            archTex,
            archMat,
            wireMat,
            uiCv,
            uiTex,
            uiMat,
            clip,
            scan,
            toast,
            toastMat,
            redraw,
            dispose,
            cur: { x: 0, y: 0, s: 1, g: 1 },
            rot: { x: 0, y: 0 },
            mw: new THREE.Vector3(),
            start: performance.now(),
            aliveAt: null,
        };
    }

    function renderGl(now, dt) {
        const T = gl.THREE;
        const t = (now - gl.start) / 1000;
        const k = 1 - Math.pow(0.0008, dt);
        const center = sy + vh * 0.5;
        const nx = (mouse.sx / vw) * 2 - 1;
        const ny = -(mouse.sy / vh) * 2 + 1;
        const st = secs.story;
        const P = st ? clamp((sy - st.top) / Math.max(1, st.h - vh), 0, 1) * 6 : 6;
        const pre = st ? clamp((sy - st.top + vh * 0.7) / (vh * 0.75), 0, 1) : 1;

        const lastKey = Object.keys(secs).reduce((a, b) => (secs[b].top > (secs[a]?.top ?? -1) ? b : a), 'hero');
        const lastSec = secs[lastKey];
        let anchor = lastSec && center >= lastSec.top + lastSec.h ? anchors.contact : anchors.hero;
        for (const key in secs) {
            const s = secs[key];
            if (center >= s.top && center < s.top + s.h && anchors[key]) {
                anchor = anchors[key];
            }
        }
        const narrow = vw < 900;
        const kk = 1 - Math.pow(0.02, dt);
        gl.cur.x = lerp(gl.cur.x, anchor.x, kk);
        gl.cur.y = lerp(gl.cur.y, anchor.y, kk);
        gl.cur.s = lerp(gl.cur.s, narrow ? anchor.s * 0.72 : anchor.s, kk);
        gl.cur.g = lerp(gl.cur.g, anchor.g, 1 - Math.pow(0.004, dt));
        const g = gl.cur.g < 0.01 ? 0 : gl.cur.g;
        gl.group.position.set(gl.cur.x, gl.cur.y, 0);
        gl.group.scale.setScalar(gl.cur.s);

        const v3 = new T.Vector3(nx, ny, 0.5).unproject(gl.camera).sub(gl.camera.position).normalize();
        gl.mw.copy(gl.camera.position).add(v3.multiplyScalar(-gl.camera.position.z / v3.z));
        if (!mouse.moved || !fine) {
            gl.mw.set(99, 99, 0);
        }

        const t1 = smooth(0.7, 1.8, P);
        const t2 = smooth(1.8, 2.7, P);
        const archP = smooth(1.9, 2.95, P);
        const archOut = smooth(2.95, 3.4, P);
        const wireIn = smooth(3.05, 3.35, P);
        const wireDraw = smooth(3.05, 3.9, P);
        const build = smooth(4.0, 4.9, P);
        if (P >= 5.05 && g > 0.5) {
            if (gl.aliveAt === null) {
                gl.aliveAt = t;
            }
        } else {
            gl.aliveAt = null;
        }
        const alive = gl.aliveAt === null ? 0 : clamp((t - gl.aliveAt) / 3.6, 0, 1);

        const tilt = 0.35 + 0.65 * smooth(1.5, 5, P);
        gl.rot.x = lerp(gl.rot.x, (fine ? -ny * 0.2 * tilt : 0) + (reduce ? 0 : Math.sin(t * 0.35) * 0.03 * tilt), k);
        gl.rot.y = lerp(gl.rot.y, (fine ? nx * 0.3 * tilt : 0) + (reduce ? 0 : Math.sin(t * 0.27) * 0.05 * tilt), k);
        gl.group.rotation.set(gl.rot.x, gl.rot.y, 0);

        const u = gl.pm.uniforms;
        u.uTime.value = reduce ? 0 : t;
        u.uIntro.value = reduce ? 1 : smooth(0, 2.2, t);
        u.uPre.value = pre;
        u.uT1.value = t1;
        u.uT2.value = t2;
        u.uFade.value = 1 - smooth(2.85, 3.3, P);
        u.uRepel.value = (1 - t1 * 0.85) * (reduce ? 0 : 1);
        u.uGlobal.value = g;
        u.uMouse.value.copy(gl.mw);

        gl.group.updateMatrixWorld();
        const fragA = pre * (1 - smooth(2.1, 2.7, P)) * g;
        gl.fragMat.opacity = fragA * lerp(0.5, 0.75, smooth(0.7, 1.9, P));
        gl.frags.forEach((m) => {
            const d = m.userData;
            const mod = MODS[d.mod];
            const c = toW(mod[0] + mod[2] / 2, mod[1] + mod[3] / 2);
            gl.tmpV.set(c[0], c[1], 0.06);
            gl.group.localToWorld(gl.tmpV);
            const pf = smooth(0.75 + d.k * 0.09, 1.45 + d.k * 0.09, P);
            const fx = Math.sin(t * 0.4 + d.ph) * 0.12 * (1 - pf);
            const fy = Math.cos(t * 0.33 + d.ph) * 0.1 * (1 - pf);
            m.position.set(lerp(d.home.x + fx, gl.tmpV.x, pf), lerp(d.home.y + fy, gl.tmpV.y, pf), lerp(d.home.z, gl.tmpV.z, pf));
            if (!reduce) {
                d.rot.x += d.spin.x * dt * (1 - pf);
                d.rot.y += d.spin.y * dt * (1 - pf);
                d.rot.z += d.spin.z * dt * 0.5 * (1 - pf);
            }
            gl.tmpQ.setFromEuler(d.rot);
            gl.tmpQ2.copy(gl.group.quaternion);
            m.quaternion.copy(gl.tmpQ).slerp(gl.tmpQ2, pf);
            const target = (mod[2] * 0.1 * gl.cur.s * 0.62) / d.w;
            m.scale.setScalar(Math.max(0.001, lerp(narrow ? 0.6 : 1, target, pf)));
            m.visible = fragA > 0.002;
        });

        const archA = smooth(1.9, 2.3, P) * (1 - archOut) * g * 0.85;
        gl.archMat.opacity = archA;
        if (archA > 0.001) {
            const q = Math.round(archP * 200) / 200;
            if (q !== gl.redraw.arch) {
                gl.redraw.arch = q;
                drawArch(gl.archCv, q);
                gl.archTex.needsUpdate = true;
            }
        }

        gl.wireMat.opacity = wireIn * 0.85 * g;
        gl.wireMat.dashSize = 0.0001 + wireDraw * 4.6;

        const yLocal = -SH / 2 + (SH + 0.04) * build;
        gl.clip.constant = gl.cur.y + yLocal * gl.cur.s + (build >= 1 ? 50 : 0);
        gl.wclip.constant = build >= 1 ? -50 : -(gl.cur.y + yLocal * gl.cur.s) + 0.002;
        gl.uiMat.visible = build > 0.001 && g > 0.001;
        gl.uiMat.opacity = g;
        const qa = Math.round(alive * 120) / 120;
        if (gl.uiMat.visible && qa !== gl.redraw.ui) {
            gl.redraw.ui = qa;
            drawUI(gl.uiCv, qa);
            gl.uiTex.needsUpdate = true;
        }
        gl.scan.position.set(0, yLocal, 0.12);
        gl.scan.material.opacity = build > 0.001 && build < 0.999 ? g : 0;
        const ta = smooth(0.62, 0.78, alive);
        gl.toastMat.opacity = ta * g;
        gl.toast.position.set(SW / 2 - 0.7, -SH / 2 + 0.2 - (1 - ta) * 0.25, 0.45);

        if (g > 0) {
            gl.renderer.render(gl.scene, gl.camera);
        } else {
            gl.renderer.clear();
        }
    }

    SC.onFrame.push((now, dt) => {
        sy = scrollY;

        if (!reduce) {
            mqT += dt * 38;
            mqRows.forEach((r) => {
                const half = r.e.scrollWidth / 2 || 1;
                const x = (mqT + sy * 0.45) % half;
                r.e.style.transform = `translate3d(${r.dir > 0 ? -x : x - half}px, 0, 0)`;
            });
        }

        const st = secs.story;
        const P = st ? clamp((sy - st.top) / Math.max(1, st.h - vh), 0, 1) * 6 : 6;
        const chap = Math.min(5, Math.floor(P >= 6 ? 5.99 : P));
        if (chap !== lastChap) {
            lastChap = chap;
            giants.forEach((e, i) => e.classList.toggle('on', i === chap));
            chs.forEach((e, i) => e.classList.toggle('on', i === chap));
            idxBtns.forEach((e, i) => e.classList.toggle('on', i === chap));
        }

        const wk = secs.works;
        if (wk && wItems.length) {
            if (horizontal) {
                const p = clamp((sy - wk.top) / Math.max(1, wk.h - vh), 0, 0.999);
                setWork(Math.floor(p * wItems.length));
            } else if (wActive < 0) {
                setWork(0);
            }
            const target = scHover ? scT : now - scSince > 250 ? 1 : 0;
            scV = reduce ? target : lerp(scV, target, 1 - Math.pow(scHover ? 0.0005 : 0.06, dt));
            screen?.style.setProperty('--sc', scV.toFixed(4));
        }

        if (portrait && !reduce) {
            const top = portrait.getBoundingClientRect().top;
            portrait.style.setProperty('--rev', clamp((vh - top) / (vh * 0.55), 0, 1).toFixed(3));
        }

        if (gl) {
            renderGl(now, dt);
        }
    });

    import('three')
        .then((THREE) => {
            if (disposed) {
                return;
            }
            gl = init3D(THREE);
            if (!gl) {
                doc.classList.add('no-gl');
            }
        })
        .catch(() => doc.classList.add('no-gl'));

    return () => {
        disposed = true;
        cleanups.forEach((fn) => fn());
        if (gl) {
            gl.dispose();
            gl.THREE.ColorManagement.enabled = true;
            gl = null;
        }
        doc.classList.remove('no-gl');
    };
}
