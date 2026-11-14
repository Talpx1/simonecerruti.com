export default function (rc, SC) {
    const { lerp, reduce, fine, mouse } = SC;
    const cx = rc.getContext('2d');
    const hero = rc.parentElement;
    const LABELS = JSON.parse(rc.dataset.labels || '[]');
    const N = innerWidth < 900 ? 420 : 1100;
    const parts = [];
    let W = 0;
    let H = 0;
    let curves = [];
    let hl = -1;
    let hlS = [0, 0, 0];

    const size = () => {
        const b = hero.getBoundingClientRect();
        const dpr = Math.min(devicePixelRatio, 2);
        W = b.width;
        H = b.height;
        rc.width = W * dpr;
        rc.height = H * dpr;
        rc.style.width = W + 'px';
        rc.style.height = H + 'px';
        cx.setTransform(dpr, 0, 0, dpr, 0, 0);
        const s = [W * -0.02, H * 0.36];
        const ends = [H * 0.16, H * 0.36, H * 0.56];
        curves = ends.map((y) => [s, [W * 0.32, s[1]], [W * 0.5, y], [W * 0.97, y]]);
    };

    const bez = (c, t) => {
        const u = 1 - t;
        return [
            u * u * u * c[0][0] + 3 * u * u * t * c[1][0] + 3 * u * t * t * c[2][0] + t * t * t * c[3][0],
            u * u * u * c[0][1] + 3 * u * u * t * c[1][1] + 3 * u * t * t * c[2][1] + t * t * t * c[3][1],
        ];
    };

    size();
    addEventListener('resize', size);

    for (let i = 0; i < N; i++) {
        parts.push({
            c: i % 3,
            t: Math.random(),
            sp: 0.05 + Math.random() * 0.08,
            off: (Math.random() - 0.5) * 34,
            ph: Math.random() * 6.28,
            sz: 1 + Math.random() * 1.8,
        });
    }

    SC.onFrame.push((now, dt) => {
        const b = hero.getBoundingClientRect();
        if (b.bottom < 0) {
            return;
        }
        const mx = mouse.x - b.left;
        const my = mouse.y - b.top;
        const t = now / 1000;
        let near = -1;
        if (fine && mouse.moved && mx > 0 && my > 0 && mx < W && my < H) {
            let best = 60;
            curves.forEach((c, i) => {
                for (let k = 0; k <= 30; k++) {
                    const p = bez(c, k / 30);
                    const d = Math.hypot(p[0] - mx, p[1] - my);
                    if (d < best) {
                        best = d;
                        near = i;
                    }
                }
            });
        }
        const picked = Number.parseInt(rc.dataset.route ?? '', 10);
        hl = near >= 0 ? near : Number.isNaN(picked) ? -1 : picked;
        hlS = hlS.map((v, i) => lerp(v, i === hl ? 1 : 0, 0.08));
        cx.clearRect(0, 0, W, H);
        curves.forEach((c, i) => {
            cx.strokeStyle = `rgba(241,240,236,${(0.06 + hlS[i] * 0.18).toFixed(3)})`;
            cx.lineWidth = 1;
            cx.setLineDash([2, 8]);
            cx.beginPath();
            cx.moveTo(c[0][0], c[0][1]);
            cx.bezierCurveTo(c[1][0], c[1][1], c[2][0], c[2][1], c[3][0], c[3][1]);
            cx.stroke();
            cx.setLineDash([]);
            cx.font = '700 11px Archivo, Arial';
            cx.textAlign = 'right';
            cx.letterSpacing = '2px';
            cx.fillStyle = hlS[i] > 0.5 ? '#FF4A1C' : `rgba(241,240,236,${(0.35 + hlS[i] * 0.5).toFixed(3)})`;
            cx.fillText((LABELS[i] || '').toUpperCase(), c[3][0], c[3][1] - 14);
            cx.fillStyle = hlS[i] > 0.5 ? '#FF4A1C' : 'rgba(241,240,236,.5)';
            cx.fillRect(c[3][0] - 3, c[3][1] - 3, 6, 6);
        });
        parts.forEach((p) => {
            const boost = 1 + hlS[p.c] * 1.4;
            if (!reduce) {
                p.t += dt * p.sp * boost;
            }
            if (p.t > 1) {
                p.t -= 1;
                p.c = Math.random() < 0.34 ? 0 : Math.random() < 0.5 ? 1 : 2;
            }
            const c = curves[p.c];
            const q = bez(c, p.t);
            const q2 = bez(c, Math.min(1, p.t + 0.01));
            const nx = -(q2[1] - q[1]);
            const ny = q2[0] - q[0];
            const nl = Math.hypot(nx, ny) || 1;
            const spread = p.off * (0.25 + 0.75 * Math.min(1, p.t * 1.6)) * (1 - hlS[p.c] * 0.6);
            let x = q[0] + (nx / nl) * spread + Math.sin(t * 1.3 + p.ph) * 2;
            let y = q[1] + (ny / nl) * spread + Math.cos(t * 1.1 + p.ph) * 2;
            const d = Math.hypot(x - mx, y - my);
            if (d < 90 && fine) {
                x += ((x - mx) / d) * (90 - d) * 0.25;
                y += ((y - my) / d) * (90 - d) * 0.25;
            }
            const a = (0.18 + 0.4 * Math.sin(Math.PI * p.t)) * (1 + hlS[p.c] * 1.4);
            cx.fillStyle =
                hlS[p.c] > 0.4 && p.sz > 1.6
                    ? `rgba(255,74,28,${Math.min(1, a).toFixed(3)})`
                    : `rgba(241,240,236,${Math.min(1, a).toFixed(3)})`;
            cx.fillRect(x, y, p.sz, p.sz);
        });
    });

    return () => removeEventListener('resize', size);
}
