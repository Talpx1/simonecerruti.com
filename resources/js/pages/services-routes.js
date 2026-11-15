export default function (canvas, siteRuntime) {
    const { linearInterpolate, prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    const context = canvas.getContext('2d');
    const hero = canvas.parentElement;
    const LABELS = JSON.parse(canvas.dataset.labels || '[]');
    const PARTICLE_COUNT = innerWidth < 900 ? 420 : 1100;
    const particles = [];
    let width = 0;
    let height = 0;
    let curves = [];
    let highlightedCurveIndex = -1;
    let curveHighlightLevels = [0, 0, 0];

    const resizeCanvas = () => {
        const heroBounds = hero.getBoundingClientRect();
        const pixelRatio = Math.min(devicePixelRatio, 2);
        width = heroBounds.width;
        height = heroBounds.height;
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        const startPoint = [width * -0.02, height * 0.36];
        const endHeights = [height * 0.16, height * 0.36, height * 0.56];
        curves = endHeights.map((endY) => [startPoint, [width * 0.32, startPoint[1]], [width * 0.5, endY], [width * 0.97, endY]]);
    };

    const getBezierPoint = (curve, progress) => {
        const inverseProgress = 1 - progress;
        return [
            inverseProgress * inverseProgress * inverseProgress * curve[0][0] + 3 * inverseProgress * inverseProgress * progress * curve[1][0] + 3 * inverseProgress * progress * progress * curve[2][0] + progress * progress * progress * curve[3][0],
            inverseProgress * inverseProgress * inverseProgress * curve[0][1] + 3 * inverseProgress * inverseProgress * progress * curve[1][1] + 3 * inverseProgress * progress * progress * curve[2][1] + progress * progress * progress * curve[3][1],
        ];
    };

    resizeCanvas();
    addEventListener('resize', resizeCanvas);

    for (let i = 0; i < PARTICLE_COUNT; i++) {
        particles.push({
            curveIndex: i % 3,
            progress: Math.random(),
            speed: 0.05 + Math.random() * 0.08,
            lateralOffset: (Math.random() - 0.5) * 34,
            wobblePhase: Math.random() * 6.28,
            size: 1 + Math.random() * 1.8,
        });
    }

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = hero.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const pointerX = pointer.x - heroBounds.left;
        const pointerY = pointer.y - heroBounds.top;
        const elapsedSeconds = timestamp / 1000;
        let nearestCurveIndex = -1;
        if (hasFinePointer && pointer.hasMoved && pointerX > 0 && pointerY > 0 && pointerX < width && pointerY < height) {
            let nearestDistance = 60;
            curves.forEach((curve, curveIndex) => {
                for (let sampleIndex = 0; sampleIndex <= 30; sampleIndex++) {
                    const samplePoint = getBezierPoint(curve, sampleIndex / 30);
                    const sampleDistance = Math.hypot(samplePoint[0] - pointerX, samplePoint[1] - pointerY);
                    if (sampleDistance < nearestDistance) {
                        nearestDistance = sampleDistance;
                        nearestCurveIndex = curveIndex;
                    }
                }
            });
        }
        const pickedRouteIndex = Number.parseInt(canvas.dataset.route ?? '', 10);
        highlightedCurveIndex = nearestCurveIndex >= 0 ? nearestCurveIndex : Number.isNaN(pickedRouteIndex) ? -1 : pickedRouteIndex;
        curveHighlightLevels = curveHighlightLevels.map((level, curveIndex) => linearInterpolate(level, curveIndex === highlightedCurveIndex ? 1 : 0, 0.08));
        context.clearRect(0, 0, width, height);
        curves.forEach((curve, curveIndex) => {
            context.strokeStyle = `rgba(241,240,236,${(0.06 + curveHighlightLevels[curveIndex] * 0.18).toFixed(3)})`;
            context.lineWidth = 1;
            context.setLineDash([2, 8]);
            context.beginPath();
            context.moveTo(curve[0][0], curve[0][1]);
            context.bezierCurveTo(curve[1][0], curve[1][1], curve[2][0], curve[2][1], curve[3][0], curve[3][1]);
            context.stroke();
            context.setLineDash([]);
            context.font = '700 11px Archivo, Arial';
            context.textAlign = 'right';
            context.letterSpacing = '2px';
            context.fillStyle = curveHighlightLevels[curveIndex] > 0.5 ? '#FF4A1C' : `rgba(241,240,236,${(0.35 + curveHighlightLevels[curveIndex] * 0.5).toFixed(3)})`;
            context.fillText((LABELS[curveIndex] || '').toUpperCase(), curve[3][0], curve[3][1] - 14);
            context.fillStyle = curveHighlightLevels[curveIndex] > 0.5 ? '#FF4A1C' : 'rgba(241,240,236,.5)';
            context.fillRect(curve[3][0] - 3, curve[3][1] - 3, 6, 6);
        });
        particles.forEach((particle) => {
            const speedBoost = 1 + curveHighlightLevels[particle.curveIndex] * 1.4;
            if (!prefersReducedMotion) {
                particle.progress += deltaSeconds * particle.speed * speedBoost;
            }
            if (particle.progress > 1) {
                particle.progress -= 1;
                particle.curveIndex = Math.random() < 0.34 ? 0 : Math.random() < 0.5 ? 1 : 2;
            }
            const particleCurve = curves[particle.curveIndex];
            const curvePoint = getBezierPoint(particleCurve, particle.progress);
            const nextCurvePoint = getBezierPoint(particleCurve, Math.min(1, particle.progress + 0.01));
            const normalX = -(nextCurvePoint[1] - curvePoint[1]);
            const normalY = nextCurvePoint[0] - curvePoint[0];
            const normalLength = Math.hypot(normalX, normalY) || 1;
            const spread = particle.lateralOffset * (0.25 + 0.75 * Math.min(1, particle.progress * 1.6)) * (1 - curveHighlightLevels[particle.curveIndex] * 0.6);
            let particleX = curvePoint[0] + (normalX / normalLength) * spread + Math.sin(elapsedSeconds * 1.3 + particle.wobblePhase) * 2;
            let particleY = curvePoint[1] + (normalY / normalLength) * spread + Math.cos(elapsedSeconds * 1.1 + particle.wobblePhase) * 2;
            const pointerDistance = Math.hypot(particleX - pointerX, particleY - pointerY);
            if (pointerDistance < 90 && hasFinePointer) {
                particleX += ((particleX - pointerX) / pointerDistance) * (90 - pointerDistance) * 0.25;
                particleY += ((particleY - pointerY) / pointerDistance) * (90 - pointerDistance) * 0.25;
            }
            const alpha = (0.18 + 0.4 * Math.sin(Math.PI * particle.progress)) * (1 + curveHighlightLevels[particle.curveIndex] * 1.4);
            context.fillStyle =
                curveHighlightLevels[particle.curveIndex] > 0.4 && particle.size > 1.6
                    ? `rgba(255,74,28,${Math.min(1, alpha).toFixed(3)})`
                    : `rgba(241,240,236,${Math.min(1, alpha).toFixed(3)})`;
            context.fillRect(particleX, particleY, particle.size, particle.size);
        });
    });

    return () => removeEventListener('resize', resizeCanvas);
}
