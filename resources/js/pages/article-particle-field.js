export default function (canvas, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, pointer, linearInterpolate } = siteRuntime;
    const context = canvas.getContext('2d');
    const hero = canvas.parentElement;
    const startTimestamp = performance.now();
    const GRID_SPACING = 26;
    let width = 0;
    let height = 0;
    let points = [];
    let focusX = 0;
    let focusY = 0;

    const resizeCanvas = () => {
        const heroBounds = hero.getBoundingClientRect();
        const pixelRatio = Math.min(window.devicePixelRatio, 2);
        width = heroBounds.width;
        height = heroBounds.height;
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        points = [];
        for (let gridY = GRID_SPACING / 2; gridY < height; gridY += GRID_SPACING) {
            for (let gridX = GRID_SPACING / 2; gridX < width; gridX += GRID_SPACING) {
                points.push({ x: gridX, y: gridY, angle: 0, heat: 0 });
            }
        }
    };

    resizeCanvas();
    focusX = width * 0.7;
    focusY = height * 0.4;
    window.addEventListener('resize', resizeCanvas);

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = hero.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const elapsedSeconds = (timestamp - startTimestamp) / 1000;
        const isPointerInsideHero = hasFinePointer && pointer.hasMoved && pointer.smoothY > heroBounds.top && pointer.smoothY < heroBounds.bottom;
        const targetFocusX = isPointerInsideHero ? pointer.smoothX - heroBounds.left : width * (0.68 + 0.18 * Math.sin(elapsedSeconds * 0.31));
        const targetFocusY = isPointerInsideHero ? pointer.smoothY - heroBounds.top : height * (0.38 + 0.18 * Math.sin(elapsedSeconds * 0.47 + 2));
        focusX = linearInterpolate(focusX, targetFocusX, 1 - Math.pow(0.002, deltaSeconds));
        focusY = linearInterpolate(focusY, targetFocusY, 1 - Math.pow(0.002, deltaSeconds));
        context.clearRect(0, 0, width, height);
        context.lineCap = 'round';
        const influenceRadius = Math.min(width, height) * 0.42;
        const easeFactor = 1 - Math.pow(0.02, deltaSeconds);
        for (const point of points) {
            const deltaX = point.x - focusX;
            const deltaY = point.y - focusY;
            const distance = Math.hypot(deltaX, deltaY);
            const falloff = Math.max(0, 1 - distance / influenceRadius);
            const baseAngle = Math.sin(point.x * 0.006 + elapsedSeconds * 0.35) + Math.cos(point.y * 0.008 - elapsedSeconds * 0.27);
            const swirlAngle = Math.atan2(deltaY, deltaX) + Math.PI / 2;
            const targetAngle = prefersReducedMotion ? baseAngle : baseAngle + (swirlAngle - baseAngle) * falloff * falloff * 1.6;
            const angleDifference = ((((targetAngle - point.angle + Math.PI) % (Math.PI * 2)) + Math.PI * 2) % (Math.PI * 2)) - Math.PI;
            point.angle += angleDifference * easeFactor;
            point.heat = linearInterpolate(point.heat, falloff, easeFactor);
            const strokeLength = 5 + point.heat * 9;
            const offsetX = Math.cos(point.angle) * strokeLength;
            const offsetY = Math.sin(point.angle) * strokeLength;
            context.strokeStyle = point.heat > 0.55 ? `rgba(255,74,28,${(0.25 + point.heat * 0.6).toFixed(3)})` : `rgba(241,240,236,${(0.07 + point.heat * 0.35).toFixed(3)})`;
            context.lineWidth = 1 + point.heat * 0.8;
            context.beginPath();
            context.moveTo(point.x - offsetX, point.y - offsetY);
            context.lineTo(point.x + offsetX, point.y + offsetY);
            context.stroke();
        }
    });

    return () => window.removeEventListener('resize', resizeCanvas);
}
