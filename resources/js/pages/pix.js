export default function (pc, SC) {
    const { reduce, fine, mouse } = SC;
    const ctx = pc.getContext('2d');
    const hero = pc.parentElement;
    const CELL = 14;
    let W = 0;
    let H = 0;
    let cols = 0;
    let rows = 0;
    let I;
    let F;
    let base = null;
    let pathPts = [];

    const svgNS = 'http://www.w3.org/2000/svg';
    const holder = document.createElementNS(svgNS, 'svg');
    holder.setAttribute('width', '0');
    holder.setAttribute('height', '0');
    holder.style.position = 'absolute';
    const path = document.createElementNS(svgNS, 'path');
    path.setAttribute(
        'd',
        'M3 51.4989C24 51.4989 26.0781 44.3738 36.5 29.9988C51 9.99881 74 -6.50109 97 9.99882C74 9.99882 74 28.4987 74 29.9987C74 31.4987 74 51.4987 97 51.4987',
    );
    holder.appendChild(path);
    document.body.appendChild(holder);

    const size = () => {
        const b = hero.getBoundingClientRect();
        const dpr = Math.min(devicePixelRatio, 2);
        W = b.width;
        H = b.height;
        pc.width = W * dpr;
        pc.height = H * dpr;
        pc.style.width = W + 'px';
        pc.style.height = H + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        cols = Math.ceil(W / CELL);
        rows = Math.ceil(H / CELL);
        I = new Float32Array(cols * rows);
        F = new Float32Array(cols * rows);
        base = document.createElement('canvas');
        base.width = W * dpr;
        base.height = H * dpr;
        const bx = base.getContext('2d');
        bx.setTransform(dpr, 0, 0, dpr, 0, 0);
        bx.fillStyle = 'rgba(241,240,236,.07)';
        for (let y = 0; y < rows; y++) {
            for (let x = 0; x < cols; x++) {
                bx.fillRect(x * CELL + CELL / 2 - 1, y * CELL + CELL / 2 - 1, 2, 2);
            }
        }
        const L = path.getTotalLength();
        const sc = Math.min((W * 0.42) / 100, (H * 0.62) / 54);
        const ox = W * 0.94 - 100 * sc;
        const oy = H * 0.42 - 27 * sc;
        pathPts = [];
        for (let i = 0; i <= 260; i++) {
            const q = path.getPointAtLength((L * i) / 260);
            pathPts.push([ox + q.x * sc, oy + q.y * sc]);
        }
    };
    size();
    addEventListener('resize', size);

    const paint = (x, y, r, fresh) => {
        const cx = Math.floor(x / CELL);
        const cy = Math.floor(y / CELL);
        for (let j = -r; j <= r; j++) {
            for (let i = -r; i <= r; i++) {
                const X = cx + i;
                const Y = cy + j;
                if (X < 0 || Y < 0 || X >= cols || Y >= rows) {
                    continue;
                }
                const d = Math.hypot(i, j);
                if (d > r + 0.2) {
                    continue;
                }
                const k = Y * cols + X;
                I[k] = Math.min(1, I[k] + (1 - d / (r + 1)) * 0.9);
                F[k] = Math.max(F[k], fresh);
            }
        }
    };

    let brush = 0;
    let last = null;
    SC.onFrame.push((now, dt) => {
        const b = hero.getBoundingClientRect();
        if (b.bottom < 0) {
            return;
        }
        if (!reduce) {
            brush += dt * 0.3;
            const p = brush % 1.35;
            if (p < 1) {
                const q = pathPts[Math.floor(p * (pathPts.length - 1))];
                if (q) {
                    paint(q[0], q[1], 1, 0.15);
                }
            }
        } else if (pathPts.length) {
            pathPts.forEach((q) => paint(q[0], q[1], 1, 0));
        }
        if (mouse.moved && fine) {
            const mx = mouse.x - b.left;
            const my = mouse.y - b.top;
            if (mx >= 0 && my >= 0 && mx <= W && my <= H) {
                if (last) {
                    const st = Math.max(1, Math.ceil(Math.hypot(mx - last[0], my - last[1]) / (CELL / 2)));
                    for (let i = 1; i <= st; i++) {
                        paint(last[0] + ((mx - last[0]) * i) / st, last[1] + ((my - last[1]) * i) / st, 1, 1);
                    }
                }
                last = [mx, my];
            } else {
                last = null;
            }
        }
        ctx.clearRect(0, 0, W, H);
        ctx.drawImage(base, 0, 0, W, H);
        const slow = reduce ? 1 : Math.pow(0.8, dt);
        const fast = Math.pow(0.3, dt);
        const fdec = Math.pow(0.25, dt);
        for (let k = 0; k < I.length; k++) {
            const v = I[k];
            if (v < 0.02) {
                I[k] = 0;
                continue;
            }
            const x = (k % cols) * CELL;
            const y = Math.floor(k / cols) * CELL;
            const s = 3 + v * (CELL - 5);
            const f = F[k];
            ctx.fillStyle =
                f > 0.3
                    ? `rgba(255,74,28,${(0.35 + 0.65 * v).toFixed(3)})`
                    : `rgba(241,240,236,${(0.12 + 0.55 * v).toFixed(3)})`;
            ctx.fillRect(x + (CELL - s) / 2, y + (CELL - s) / 2, s, s);
            I[k] = v * (f > 0.3 ? fast : slow);
            F[k] = f * fdec;
        }
    });

    return () => {
        removeEventListener('resize', size);
        holder.remove();
    };
}
