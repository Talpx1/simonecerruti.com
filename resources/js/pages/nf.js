export default function (nf, SC) {
    const { mouse, fine, reduce } = SC;
    const hero = nf.parentElement;
    const cx = nf.getContext('2d');
    const code = nf.dataset.code ?? '404';
    let W = 0;
    let H = 0;
    let P = [];
    let down = false;

    const build = () => {
        const b = hero.getBoundingClientRect();
        const dpr = Math.min(devicePixelRatio, 2);
        W = b.width;
        H = b.height;
        nf.width = W * dpr;
        nf.height = H * dpr;
        nf.style.width = W + 'px';
        nf.style.height = H + 'px';
        cx.setTransform(dpr, 0, 0, dpr, 0, 0);
        const mob = W < 900;
        const fs = mob ? W * 0.42 : Math.min(W * 0.25, 380);
        const step = mob ? 7 : 9;
        const off = document.createElement('canvas');
        const ow = Math.ceil(fs * 2.4);
        const oh = Math.ceil(fs * 1.05);
        off.width = ow;
        off.height = oh;
        const o = off.getContext('2d');
        o.fillStyle = '#fff';
        o.font = `900 ${fs}px Archivo, Arial Black, sans-serif`;
        o.textBaseline = 'middle';
        o.textAlign = 'center';
        o.fillText(code, ow / 2, oh / 2);
        const d = o.getImageData(0, 0, ow, oh).data;
        const ox = mob ? (W - ow) / 2 : W - ow - W * 0.04;
        const oy = mob ? H * 0.1 : H * 0.07;
        const old = P;
        P = [];
        for (let y = 0; y < oh; y += step) {
            for (let x = 0; x < ow; x += step) {
                if (d[(y * ow + x) * 4 + 3] < 128) {
                    continue;
                }
                const q = old[P.length];
                P.push({
                    hx: ox + x,
                    hy: oy + y,
                    x: q ? q.x : ox + x + (Math.random() - 0.5) * W,
                    y: q ? q.y : -Math.random() * H,
                    vx: 0,
                    vy: 0,
                    a: Math.random() < 0.07,
                    z: step - 3,
                });
            }
        }
    };

    const onDown = (e) => {
        if (!e.target.closest('a, button')) {
            down = true;
        }
    };
    const onUp = () => {
        down = false;
    };

    build();
    addEventListener('resize', build);
    addEventListener('pointerdown', onDown);
    addEventListener('pointerup', onUp);
    document.fonts && document.fonts.ready.then(build);

    SC.onFrame.push((now, dt) => {
        const b = hero.getBoundingClientRect();
        if (b.bottom < 0) {
            return;
        }
        const mx = mouse.sx - b.left;
        const my = mouse.sy - b.top;
        const live = fine && mouse.moved;
        const R = down ? 220 : 110;
        const damp = Math.pow(0.004, dt);
        cx.clearRect(0, 0, W, H);
        for (const p of P) {
            let ax = (p.hx - p.x) * 30;
            let ay = (p.hy - p.y) * 30;
            if (live) {
                const dx = p.x - mx;
                const dy = p.y - my;
                const dd = Math.hypot(dx, dy);
                if (dd < R && dd > 0.1) {
                    const f = 1 - dd / R;
                    ax += (dx / dd) * f * (down ? 26000 : 9000);
                    ay += (dy / dd) * f * (down ? 26000 : 9000) + f * 3000;
                }
            }
            p.vx = (p.vx + ax * dt) * damp;
            p.vy = (p.vy + ay * dt) * damp;
            if (reduce) {
                p.x = p.hx;
                p.y = p.hy;
            } else {
                p.x += p.vx * dt;
                p.y += p.vy * dt;
            }
            const s = Math.min(1, Math.hypot(p.x - p.hx, p.y - p.hy) / 30);
            cx.fillStyle = p.a || s > 0.5 ? '#FF4A1C' : `rgba(241,240,236,${(0.9 - s * 0.4).toFixed(2)})`;
            cx.fillRect(p.x, p.y, p.z, p.z);
        }
    });

    return () => {
        removeEventListener('resize', build);
        removeEventListener('pointerdown', onDown);
        removeEventListener('pointerup', onUp);
    };
}
