export default function (pc, SC) {
    const { mouse, fine, reduce } = SC;
    const hero = pc.parentElement;
    const label = hero.dataset.pingLabel ?? '';
    const cx = pc.getContext('2d');
    let W = 0;
    let H = 0;
    let node = [0, 0];
    let packets = [];
    let rings = [];
    let last = null;
    let idleT = 0;
    let ringT = 0;

    const size = () => {
        const b = hero.getBoundingClientRect();
        const dpr = Math.min(devicePixelRatio, 2);
        W = b.width;
        H = b.height;
        pc.width = W * dpr;
        pc.height = H * dpr;
        pc.style.width = W + 'px';
        pc.style.height = H + 'px';
        cx.setTransform(dpr, 0, 0, dpr, 0, 0);
        node = [W * 0.8, H * 0.34];
    };
    size();
    addEventListener('resize', size);

    const send = (x, y) => {
        const mx = (x + node[0]) / 2 + (Math.random() - 0.5) * 160;
        const my = Math.min(y, node[1]) - 80 - Math.random() * 120;
        packets.push({ a: [x, y], c: [mx, my], t: 0, sp: 0.5 + Math.random() * 0.35 });
    };

    SC.onFrame.push((now, dt) => {
        const b = hero.getBoundingClientRect();
        if (b.bottom < 0) {
            return;
        }
        const mx = mouse.x - b.left;
        const my = mouse.y - b.top;
        if (fine && mouse.moved && mx > 0 && my > 0 && mx < W && my < H) {
            if (!last || Math.hypot(mx - last[0], my - last[1]) > 46) {
                send(mx, my);
                last = [mx, my];
            }
        }
        idleT += dt;
        if (idleT > 0.7 && !reduce) {
            idleT = 0;
            send(Math.random() < 0.5 ? -10 : Math.random() * W * 0.5, H * (0.2 + Math.random() * 0.8));
        }
        ringT += dt;
        if (ringT > 2.2) {
            ringT = 0;
            rings.push({ r: 8, a: 1 });
        }
        cx.clearRect(0, 0, W, H);
        cx.fillStyle = 'rgba(241,240,236,.06)';
        for (let y = 16; y < H; y += 28) {
            for (let x = 16; x < W; x += 28) {
                cx.fillRect(x, y, 1.5, 1.5);
            }
        }
        packets = packets.filter((p) => {
            p.t += dt * p.sp * (reduce ? 4 : 1);
            if (p.t >= 1) {
                rings.push({ r: 6, a: 0.7 });
                return false;
            }
            const q = (t) => {
                const u = 1 - t;
                return [
                    u * u * p.a[0] + 2 * u * t * p.c[0] + t * t * node[0],
                    u * u * p.a[1] + 2 * u * t * p.c[1] + t * t * node[1],
                ];
            };
            cx.strokeStyle = 'rgba(241,240,236,.12)';
            cx.lineWidth = 1;
            cx.beginPath();
            const from = Math.max(0, p.t - 0.25);
            for (let k = from; k <= p.t; k += 0.02) {
                const z = q(k);
                k === from ? cx.moveTo(z[0], z[1]) : cx.lineTo(z[0], z[1]);
            }
            cx.stroke();
            const z = q(p.t);
            cx.fillStyle = '#FF4A1C';
            cx.fillRect(z[0] - 2, z[1] - 2, 4, 4);
            return true;
        });
        rings = rings.filter((r) => {
            r.r += dt * 90;
            r.a -= dt * 0.45;
            if (r.a <= 0) {
                return false;
            }
            cx.strokeStyle = `rgba(255,74,28,${r.a.toFixed(3)})`;
            cx.lineWidth = 1.2;
            cx.beginPath();
            cx.arc(node[0], node[1], r.r, 0, Math.PI * 2);
            cx.stroke();
            return true;
        });
        cx.fillStyle = '#FF4A1C';
        cx.fillRect(node[0] - 6, node[1] - 6, 12, 12);
        cx.font = '700 11px Archivo, Arial';
        cx.fillStyle = 'rgba(241,240,236,.6)';
        cx.textAlign = 'left';
        cx.fillText(label, node[0] + 18, node[1] + 4);
    });

    return () => removeEventListener('resize', size);
}
