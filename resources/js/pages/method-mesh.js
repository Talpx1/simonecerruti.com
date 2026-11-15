export default function (canvasElement, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    const canvasContext = canvasElement.getContext('2d');
    const heroElement = canvasElement.parentElement;
    const GRID_SPACING = 30;
    let canvasWidth = 0;
    let canvasHeight = 0;
    let columnCount = 0;
    let rowCount = 0;
    let pointsX;
    let pointsY;
    let velocitiesX;
    let velocitiesY;
    let restPositionsX;
    let restPositionsY;
    let previousPointerPosition = null;
    let isPointerPressed = false;

    const resizeCanvas = () => {
        const heroBounds = heroElement.getBoundingClientRect();
        const devicePixelRatioCapped = Math.min(devicePixelRatio, 2);
        canvasWidth = heroBounds.width;
        canvasHeight = heroBounds.height;
        canvasElement.width = canvasWidth * devicePixelRatioCapped;
        canvasElement.height = canvasHeight * devicePixelRatioCapped;
        canvasElement.style.width = canvasWidth + 'px';
        canvasElement.style.height = canvasHeight + 'px';
        canvasContext.setTransform(devicePixelRatioCapped, 0, 0, devicePixelRatioCapped, 0, 0);
        columnCount = Math.ceil(canvasWidth / GRID_SPACING) + 2;
        rowCount = Math.ceil(canvasHeight / GRID_SPACING) + 2;
        const pointCount = columnCount * rowCount;
        pointsX = new Float32Array(pointCount);
        pointsY = new Float32Array(pointCount);
        velocitiesX = new Float32Array(pointCount);
        velocitiesY = new Float32Array(pointCount);
        restPositionsX = new Float32Array(pointCount);
        restPositionsY = new Float32Array(pointCount);
        for (let j = 0; j < rowCount; j++) {
            for (let i = 0; i < columnCount; i++) {
                const pointIndex = j * columnCount + i;
                restPositionsX[pointIndex] = pointsX[pointIndex] = (i - 0.5) * GRID_SPACING;
                restPositionsY[pointIndex] = pointsY[pointIndex] = (j - 0.5) * GRID_SPACING;
            }
        }
    };
    resizeCanvas();

    const handlePointerDown = (event) => {
        if (!event.target.closest('a, button, input, textarea')) {
            isPointerPressed = true;
        }
    };
    const handlePointerUp = () => {
        isPointerPressed = false;
    };
    addEventListener('resize', resizeCanvas);
    addEventListener('pointerdown', handlePointerDown);
    addEventListener('pointerup', handlePointerUp);

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = heroElement.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const pointerX = pointer.smoothX - heroBounds.left;
        const pointerY = pointer.smoothY - heroBounds.top;
        const elapsedSeconds = timestamp / 1000;
        const pointerVelocityX = previousPointerPosition ? pointerX - previousPointerPosition[0] : 0;
        const pointerVelocityY = previousPointerPosition ? pointerY - previousPointerPosition[1] : 0;
        previousPointerPosition = [pointerX, pointerY];
        const influenceRadius = isPointerPressed ? 190 : 130;
        const isPointerInside = hasFinePointer && pointer.hasMoved && pointerY > -50 && pointerY < canvasHeight + 50;
        const springStiffness = 34;
        const dampingFactor = Math.pow(0.0025, deltaSeconds);
        for (let pointIndex = 0; pointIndex < pointsX.length; pointIndex++) {
            const restX = restPositionsX[pointIndex];
            const restY =
                restPositionsY[pointIndex] +
                (prefersReducedMotion ? 0 : Math.sin(restX * 0.008 + elapsedSeconds * 0.9) * 4);
            let accelerationX = (restX - pointsX[pointIndex]) * springStiffness;
            let accelerationY = (restY - pointsY[pointIndex]) * springStiffness;
            if (isPointerInside) {
                const offsetX = pointsX[pointIndex] - pointerX;
                const offsetY = pointsY[pointIndex] - pointerY;
                const distance = Math.hypot(offsetX, offsetY);
                if (distance < influenceRadius && distance > 0.1) {
                    const proximityFactor = 1 - distance / influenceRadius;
                    accelerationX +=
                        (offsetX / distance) * proximityFactor * (isPointerPressed ? 5200 : 2600) +
                        pointerVelocityX * proximityFactor * 60;
                    accelerationY +=
                        (offsetY / distance) * proximityFactor * (isPointerPressed ? 5200 : 2600) +
                        pointerVelocityY * proximityFactor * 60;
                }
            }
            velocitiesX[pointIndex] = (velocitiesX[pointIndex] + accelerationX * deltaSeconds) * dampingFactor;
            velocitiesY[pointIndex] = (velocitiesY[pointIndex] + accelerationY * deltaSeconds) * dampingFactor;
            pointsX[pointIndex] += velocitiesX[pointIndex] * deltaSeconds;
            pointsY[pointIndex] += velocitiesY[pointIndex] * deltaSeconds;
        }
        canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
        canvasContext.lineWidth = 1;
        canvasContext.strokeStyle = 'rgba(241,240,236,.075)';
        canvasContext.beginPath();
        for (let j = 0; j < rowCount; j++) {
            for (let i = 0; i < columnCount; i++) {
                const pointIndex = j * columnCount + i;
                if (i < columnCount - 1) {
                    canvasContext.moveTo(pointsX[pointIndex], pointsY[pointIndex]);
                    canvasContext.lineTo(pointsX[pointIndex + 1], pointsY[pointIndex + 1]);
                }
                if (j < rowCount - 1) {
                    canvasContext.moveTo(pointsX[pointIndex], pointsY[pointIndex]);
                    canvasContext.lineTo(pointsX[pointIndex + columnCount], pointsY[pointIndex + columnCount]);
                }
            }
        }
        canvasContext.stroke();
        for (let pointIndex = 0; pointIndex < pointsX.length; pointIndex++) {
            const displacement = Math.hypot(
                pointsX[pointIndex] - restPositionsX[pointIndex],
                pointsY[pointIndex] - restPositionsY[pointIndex],
            );
            if (displacement < 3) {
                canvasContext.fillStyle = 'rgba(241,240,236,.22)';
                canvasContext.fillRect(pointsX[pointIndex] - 1, pointsY[pointIndex] - 1, 2, 2);
                continue;
            }
            const highlightFactor = Math.min(1, displacement / 40);
            canvasContext.fillStyle =
                highlightFactor > 0.35
                    ? `rgba(255,74,28,${(0.4 + 0.6 * highlightFactor).toFixed(3)})`
                    : `rgba(241,240,236,${(0.3 + highlightFactor).toFixed(3)})`;
            const dotSize = 2 + highlightFactor * 3;
            canvasContext.fillRect(
                pointsX[pointIndex] - dotSize / 2,
                pointsY[pointIndex] - dotSize / 2,
                dotSize,
                dotSize,
            );
        }
    });

    return () => {
        removeEventListener('resize', resizeCanvas);
        removeEventListener('pointerdown', handlePointerDown);
        removeEventListener('pointerup', handlePointerUp);
    };
}
