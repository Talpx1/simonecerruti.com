export default function (mc, SC) {
    const { reduce, fine, mouse } = SC;
    const cx = mc.getContext('2d');
    const hero = mc.parentElement;
    const GAP = 30;
    let W = 0;
    let H = 0;
    let cols = 0;
    let rows = 0;
    let X;
    let Y;
    let VX;
    let VY;
    let RX;
    let RY;
    let pm = null;
    let down = false;

    const size = () => {
        const b = hero.getBoundingClientRect();
        const dpr = Math.min(devicePixelRatio, 2);
        W = b.width;
        H = b.height;
        mc.width = W * dpr;
        mc.height = H * dpr;
        mc.style.width = W + 'px';
        mc.style.height = H + 'px';
        cx.setTransform(dpr, 0, 0, dpr, 0, 0);
        cols = Math.ceil(W / GAP) + 2;
        rows = Math.ceil(H / GAP) + 2;
        const n = cols * rows;
        X = new Float32Array(n);
        Y = new Float32Array(n);
        VX = new Float32Array(n);
        VY = new Float32Array(n);
        RX = new Float32Array(n);
        RY = new Float32Array(n);
        for (let j = 0; j < rows; j++) {
            for (let i = 0; i < cols; i++) {
                const k = j * cols + i;
                RX[k] = X[k] = (i - 0.5) * GAP;
                RY[k] = Y[k] = (j - 0.5) * GAP;
            }
        }
    };
    size();

    const onDown = (e) => {
        if (!e.target.closest('a, button, input, textarea')) {
            down = true;
        }
    };
    const onUp = () => {
        down = false;
    };
    addEventListener('resize', size);
    addEventListener('pointerdown', onDown);
    addEventListener('pointerup', onUp);

    SC.onFrame.push((now, dt) => {
        const b = hero.getBoundingClientRect();
        if (b.bottom < 0) {
            return;
        }
        const mx = mouse.sx - b.left;
        const my = mouse.sy - b.top;
        const t = now / 1000;
        const vx = pm ? mx - pm[0] : 0;
        const vy = pm ? my - pm[1] : 0;
        pm = [mx, my];
        const R = down ? 190 : 130;
        const inside = fine && mouse.moved && my > -50 && my < H + 50;
        const k = 34;
        const damp = Math.pow(0.0025, dt);
        for (let n = 0; n < X.length; n++) {
            const bx = RX[n];
            const by = RY[n] + (reduce ? 0 : Math.sin(bx * 0.008 + t * 0.9) * 4);
            let ax = (bx - X[n]) * k;
            let ay = (by - Y[n]) * k;
            if (inside) {
                const dx = X[n] - mx;
                const dy = Y[n] - my;
                const d = Math.hypot(dx, dy);
                if (d < R && d > 0.1) {
                    const f = 1 - d / R;
                    ax += (dx / d) * f * (down ? 5200 : 2600) + vx * f * 60;
                    ay += (dy / d) * f * (down ? 5200 : 2600) + vy * f * 60;
                }
            }
            VX[n] = (VX[n] + ax * dt) * damp;
            VY[n] = (VY[n] + ay * dt) * damp;
            X[n] += VX[n] * dt;
            Y[n] += VY[n] * dt;
        }
        cx.clearRect(0, 0, W, H);
        cx.lineWidth = 1;
        cx.strokeStyle = 'rgba(241,240,236,.075)';
        cx.beginPath();
        for (let j = 0; j < rows; j++) {
            for (let i = 0; i < cols; i++) {
                const n = j * cols + i;
                if (i < cols - 1) {
                    cx.moveTo(X[n], Y[n]);
                    cx.lineTo(X[n + 1], Y[n + 1]);
                }
                if (j < rows - 1) {
                    cx.moveTo(X[n], Y[n]);
                    cx.lineTo(X[n + cols], Y[n + cols]);
                }
            }
        }
        cx.stroke();
        for (let n = 0; n < X.length; n++) {
            const s = Math.hypot(X[n] - RX[n], Y[n] - RY[n]);
            if (s < 3) {
                cx.fillStyle = 'rgba(241,240,236,.22)';
                cx.fillRect(X[n] - 1, Y[n] - 1, 2, 2);
                continue;
            }
            const h = Math.min(1, s / 40);
            cx.fillStyle =
                h > 0.35
                    ? `rgba(255,74,28,${(0.4 + 0.6 * h).toFixed(3)})`
                    : `rgba(241,240,236,${(0.3 + h).toFixed(3)})`;
            const z = 2 + h * 3;
            cx.fillRect(X[n] - z / 2, Y[n] - z / 2, z, z);
        }
    });

    return () => {
        removeEventListener('resize', size);
        removeEventListener('pointerdown', onDown);
        removeEventListener('pointerup', onUp);
    };
}
