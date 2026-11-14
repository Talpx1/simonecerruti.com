export default function (canvas, SC) {
    const { reduce, fine, mouse, lerp } = SC;
    const context = canvas.getContext('2d');
    const hero = canvas.parentElement;
    const born = performance.now();
    const GAP = 26;
    let width = 0;
    let height = 0;
    let points = [];
    let pointer_x = 0;
    let pointer_y = 0;

    const size = () => {
        const box = hero.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio, 2);
        width = box.width;
        height = box.height;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        context.setTransform(dpr, 0, 0, dpr, 0, 0);
        points = [];
        for (let y = GAP / 2; y < height; y += GAP) {
            for (let x = GAP / 2; x < width; x += GAP) {
                points.push({ x, y, a: 0, h: 0 });
            }
        }
    };

    size();
    pointer_x = width * 0.7;
    pointer_y = height * 0.4;
    window.addEventListener('resize', size);

    SC.onFrame.push((now, dt) => {
        const box = hero.getBoundingClientRect();
        if (box.bottom < 0) {
            return;
        }
        const t = (now - born) / 1000;
        const live = fine && mouse.moved && mouse.sy > box.top && mouse.sy < box.bottom;
        const target_x = live ? mouse.sx - box.left : width * (0.68 + 0.18 * Math.sin(t * 0.31));
        const target_y = live ? mouse.sy - box.top : height * (0.38 + 0.18 * Math.sin(t * 0.47 + 2));
        pointer_x = lerp(pointer_x, target_x, 1 - Math.pow(0.002, dt));
        pointer_y = lerp(pointer_y, target_y, 1 - Math.pow(0.002, dt));
        context.clearRect(0, 0, width, height);
        context.lineCap = 'round';
        const radius = Math.min(width, height) * 0.42;
        const k = 1 - Math.pow(0.02, dt);
        for (const point of points) {
            const dx = point.x - pointer_x;
            const dy = point.y - pointer_y;
            const distance = Math.hypot(dx, dy);
            const falloff = Math.max(0, 1 - distance / radius);
            const base = Math.sin(point.x * 0.006 + t * 0.35) + Math.cos(point.y * 0.008 - t * 0.27);
            const swirl = Math.atan2(dy, dx) + Math.PI / 2;
            const target = reduce ? base : base + (swirl - base) * falloff * falloff * 1.6;
            const delta = ((((target - point.a + Math.PI) % (Math.PI * 2)) + Math.PI * 2) % (Math.PI * 2)) - Math.PI;
            point.a += delta * k;
            point.h = lerp(point.h, falloff, k);
            const length = 5 + point.h * 9;
            const cx = Math.cos(point.a) * length;
            const cy = Math.sin(point.a) * length;
            context.strokeStyle = point.h > 0.55 ? `rgba(255,74,28,${(0.25 + point.h * 0.6).toFixed(3)})` : `rgba(241,240,236,${(0.07 + point.h * 0.35).toFixed(3)})`;
            context.lineWidth = 1 + point.h * 0.8;
            context.beginPath();
            context.moveTo(point.x - cx, point.y - cy);
            context.lineTo(point.x + cx, point.y + cy);
            context.stroke();
        }
    });

    return () => window.removeEventListener('resize', size);
}
