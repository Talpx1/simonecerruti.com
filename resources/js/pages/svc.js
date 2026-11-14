export default function (sv, SC) {
    const { clamp, lerp, reduce, fine, mouse } = SC;
    const cx = sv.getContext('2d');
    const hero = sv.parentElement;
    const mode = sv.dataset.mode;
    const born = performance.now();
    let W = 0;
    let H = 0;
    let items = [];
    let px = -999;
    let py = -999;

    const size = () => {
        const b = hero.getBoundingClientRect();
        const dpr = Math.min(devicePixelRatio, 2);
        W = b.width;
        H = b.height;
        sv.width = W * dpr;
        sv.height = H * dpr;
        sv.style.width = W + 'px';
        sv.style.height = H + 'px';
        cx.setTransform(dpr, 0, 0, dpr, 0, 0);
        items = [];
        if (mode === 'cells') {
            const cw = W < 900 ? 46 : 62;
            const ch = cw * 0.42;
            const x0 = W < 900 ? W * 0.04 : W * 0.44;
            const y0 = H * 0.16;
            const cols = Math.floor((W * 0.96 - x0) / cw);
            const rows = Math.floor((H * 0.42) / ch);
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < cols; c++) {
                    items.push({
                        hx: x0 + c * cw,
                        hy: y0 + r * ch,
                        w: cw,
                        h: ch,
                        ox: (Math.random() - 0.5) * 70,
                        oy: (Math.random() - 0.5) * 46,
                        rot: (Math.random() - 0.5) * 0.6,
                        k: 0,
                        fill: Math.random() < 0.14,
                        seed: Math.random() * 6.28,
                    });
                }
            }
        } else if (mode === 'flow') {
            const n = W < 900 ? 16 : 30;
            for (let i = 0; i < n; i++) {
                items.push({
                    x: W * (0.3 + Math.random() * 0.68),
                    y: H * (0.14 + Math.random() * 0.5),
                    a: Math.random() * 6.28,
                    sp: 18 + Math.random() * 26,
                    st: 0,
                    k: 0,
                    o: 0,
                });
            }
            items.btn = { x: W < 900 ? W * 0.5 : W * 0.74, y: H * 0.33, w: 168, h: 54, pulse: 0 };
            items.rip = [];
        } else {
            const n = 7;
            const x0 = W < 900 ? W * 0.06 : W * 0.5;
            const w = W < 900 ? W * 0.88 : W * 0.44;
            for (let i = 0; i < n; i++) {
                items.push({
                    x: x0,
                    w,
                    y: 0,
                    score: Math.random(),
                    mine: i === n - 1,
                    t1: 0.35 + Math.random() * 0.5,
                    t2: 0.2 + Math.random() * 0.4,
                });
            }
            items[n - 1].score = -1;
        }
    };

    const drawCursor = (x, y, s, col, al) => {
        cx.save();
        cx.translate(x, y);
        cx.scale(s, s);
        cx.beginPath();
        cx.moveTo(0, 0);
        cx.lineTo(0, 15);
        cx.lineTo(4, 11.5);
        cx.lineTo(7, 17.5);
        cx.lineTo(9.6, 16.4);
        cx.lineTo(6.8, 10.6);
        cx.lineTo(11.6, 10.6);
        cx.closePath();
        cx.globalAlpha = al;
        cx.fillStyle = col;
        cx.fill();
        cx.globalAlpha = 1;
        cx.restore();
    };

    const drawCells = (t, dt) => {
        for (const c of items) {
            const d = Math.hypot(c.hx + c.w / 2 - px, c.hy + c.h / 2 - py);
            const target = reduce ? 1 : clamp(1.25 - d / 240, 0, 1);
            c.k = lerp(c.k, target, 1 - Math.pow(0.02, dt));
            const chaos = 1 - c.k;
            const wob = Math.sin(t * 0.6 + c.seed) * 6 * chaos;
            cx.save();
            cx.translate(c.hx + c.w / 2 + c.ox * chaos + wob, c.hy + c.h / 2 + c.oy * chaos);
            cx.rotate(c.rot * chaos);
            cx.strokeStyle = `rgba(241,240,236,${(0.045 + c.k * 0.22).toFixed(3)})`;
            cx.strokeRect(-c.w / 2 + 0.5, -c.h / 2 + 0.5, c.w - 1, c.h - 1);
            if (c.fill) {
                cx.fillStyle = c.k > 0.6 ? `rgba(255,74,28,${(0.15 + c.k * 0.45).toFixed(3)})` : 'rgba(241,240,236,.07)';
                cx.fillRect(-c.w / 2 + 6, -1.5, (c.w - 12) * (0.4 + 0.5 * ((c.seed * 7) % 1)), 3);
            }
            cx.restore();
        }
    };

    const drawFlow = (dt) => {
        const B = items.btn;
        B.pulse = Math.max(0, B.pulse - dt * 2.5);
        const s = 1 + B.pulse * 0.06;
        const bw = B.w * s;
        const bh = B.h * s;
        cx.fillStyle = '#FF4A1C';
        cx.fillRect(B.x - bw / 2, B.y - bh / 2, bw, bh);
        cx.save();
        cx.translate(B.x - 12, B.y - 10);
        cx.strokeStyle = '#0A0A0A';
        cx.lineWidth = 2.2;
        cx.lineJoin = 'round';
        cx.beginPath();
        cx.moveTo(-2, 0);
        cx.lineTo(3, 0);
        cx.lineTo(7, 13);
        cx.lineTo(20, 13);
        cx.lineTo(23, 4);
        cx.lineTo(5, 4);
        cx.stroke();
        cx.fillStyle = '#0A0A0A';
        cx.beginPath();
        cx.arc(9, 18, 2.2, 0, 6.3);
        cx.arc(18.5, 18, 2.2, 0, 6.3);
        cx.fill();
        cx.restore();
        cx.lineWidth = 1;
        items.rip = items.rip.filter((r) => (r.t += dt) < 0.9);
        items.rip.forEach((r) => {
            cx.strokeStyle = `rgba(255,74,28,${(1 - r.t / 0.9) * 0.8})`;
            cx.beginPath();
            cx.arc(r.x, r.y, 4 + r.t * 46, 0, 6.3);
            cx.stroke();
        });
        for (const p of items) {
            if (p.st === 0) {
                p.a += (Math.random() - 0.5) * dt * 2;
                p.x += Math.cos(p.a) * p.sp * dt;
                p.y += Math.sin(p.a) * p.sp * dt;
                if (p.x < W * 0.28 || p.x > W - 20) {
                    p.a = Math.PI - p.a;
                }
                if (p.y < H * 0.13 || p.y > H * 0.66) {
                    p.a = -p.a;
                    p.y = clamp(p.y, H * 0.13, H * 0.66);
                }
                if (p.x < W * 0.28 || p.x > W - 20) {
                    p.x = clamp(p.x, W * 0.28, W - 20);
                }
                p.o = Math.min(1, p.o + dt);
                const near = Math.hypot(p.x - px, p.y - py) < 120;
                if ((near && !reduce) || Math.random() < dt * 0.05) {
                    p.st = 1;
                }
            } else if (p.st === 1) {
                const dx = B.x + 20 - p.x;
                const dy = B.y + 6 - p.y;
                const d = Math.hypot(dx, dy);
                const v = Math.min(d, 380 * dt);
                if (d > 3) {
                    p.x += (dx / d) * v;
                    p.y += (dy / d) * v;
                } else {
                    p.st = 2;
                    p.k = 0;
                    B.pulse = 1;
                    items.rip.push({ x: p.x, y: p.y, t: 0 });
                }
            } else {
                p.k += dt;
                p.o = Math.max(0, 1 - p.k * 2);
                if (p.k > 0.8) {
                    p.st = 0;
                    p.o = 0;
                    p.x = W * (0.3 + Math.random() * 0.7);
                    p.y = H * (0.14 + Math.random() * 0.5);
                }
            }
            drawCursor(p.x, p.y, 1, p.st ? '#FF4A1C' : '#F1F0EC', p.o * (p.st ? 1 : 0.45));
        }
    };

    const drawRank = (t, dt) => {
        const cyc = (t % 14) / 14;
        items.forEach((r, i) => {
            r.score = r.mine ? (cyc < 0.75 ? -0.3 + cyc * 1.9 : 1.2) : 0.5 + 0.45 * Math.sin(t * r.t2 + i * 1.7);
        });
        const order = [...items].sort((a, b) => b.score - a.score);
        const rh = Math.min(52, (H * 0.44) / items.length);
        order.forEach((r, i) => {
            const ty = H * 0.13 + i * rh;
            r.y = r.y ? lerp(r.y, ty, 1 - Math.pow(0.01, dt)) : ty;
        });
        items.forEach((r) => {
            const hov = px > r.x && px < r.x + r.w && py > r.y && py < r.y + rh - 12;
            const a = r.mine ? 1 : hov ? 0.5 : 0.22;
            cx.strokeStyle = r.mine ? 'rgba(255,74,28,.9)' : `rgba(241,240,236,${a})`;
            cx.fillStyle = cx.strokeStyle;
            cx.strokeRect(r.x + 0.5, r.y + 4.5, 14, 14);
            cx.fillRect(r.x + 26, r.y + 6, r.w * r.t1, 4);
            cx.globalAlpha = 0.55;
            cx.fillRect(r.x + 26, r.y + 18, r.w * r.t2 * 0.8, 2);
            cx.globalAlpha = 1;
            if (r.mine) {
                cx.beginPath();
                cx.moveTo(r.x - 18, r.y + 11);
                cx.lineTo(r.x - 8, r.y + 11);
                cx.moveTo(r.x - 12, r.y + 7);
                cx.lineTo(r.x - 8, r.y + 11);
                cx.lineTo(r.x - 12, r.y + 15);
                cx.stroke();
            }
        });
    };

    size();
    addEventListener('resize', size);

    SC.onFrame.push((now, dt) => {
        const b = hero.getBoundingClientRect();
        if (b.bottom < 0) {
            return;
        }
        const t = (now - born) / 1000;
        const live = fine && mouse.moved && mouse.sy > b.top && mouse.sy < b.bottom;
        const mx = live ? mouse.sx - b.left : W * (0.66 + 0.2 * Math.sin(t * 0.4));
        const my = live ? mouse.sy - b.top : H * (0.36 + 0.16 * Math.sin(t * 0.57 + 1));
        px = px < -900 ? mx : lerp(px, mx, 1 - Math.pow(0.001, dt));
        py = py < -900 ? my : lerp(py, my, 1 - Math.pow(0.001, dt));
        cx.clearRect(0, 0, W, H);
        cx.lineWidth = 1;
        if (mode === 'cells') {
            drawCells(t, dt);
        } else if (mode === 'flow') {
            drawFlow(dt);
        } else {
            drawRank(t, dt);
        }
    });

    return () => removeEventListener('resize', size);
}
