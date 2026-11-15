export default function (canvasElement, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    const canvasContext = canvasElement.getContext('2d');
    const heroElement = canvasElement.parentElement;
    const CELL_SIZE = 14;
    let canvasWidth = 0;
    let canvasHeight = 0;
    let columnCount = 0;
    let rowCount = 0;
    let cellIntensities;
    let cellFreshness;
    let baseLayerCanvas = null;
    let logoPathPoints = [];

    const svgNamespace = 'http://www.w3.org/2000/svg';
    const svgHolder = document.createElementNS(svgNamespace, 'svg');
    svgHolder.setAttribute('width', '0');
    svgHolder.setAttribute('height', '0');
    svgHolder.style.position = 'absolute';
    const logoPath = document.createElementNS(svgNamespace, 'path');
    logoPath.setAttribute(
        'd',
        'M3 51.4989C24 51.4989 26.0781 44.3738 36.5 29.9988C51 9.99881 74 -6.50109 97 9.99882C74 9.99882 74 28.4987 74 29.9987C74 31.4987 74 51.4987 97 51.4987',
    );
    svgHolder.appendChild(logoPath);
    document.body.appendChild(svgHolder);

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
        columnCount = Math.ceil(canvasWidth / CELL_SIZE);
        rowCount = Math.ceil(canvasHeight / CELL_SIZE);
        cellIntensities = new Float32Array(columnCount * rowCount);
        cellFreshness = new Float32Array(columnCount * rowCount);
        baseLayerCanvas = document.createElement('canvas');
        baseLayerCanvas.width = canvasWidth * devicePixelRatioCapped;
        baseLayerCanvas.height = canvasHeight * devicePixelRatioCapped;
        const baseLayerContext = baseLayerCanvas.getContext('2d');
        baseLayerContext.setTransform(devicePixelRatioCapped, 0, 0, devicePixelRatioCapped, 0, 0);
        baseLayerContext.fillStyle = 'rgba(241,240,236,.07)';
        for (let y = 0; y < rowCount; y++) {
            for (let x = 0; x < columnCount; x++) {
                baseLayerContext.fillRect(x * CELL_SIZE + CELL_SIZE / 2 - 1, y * CELL_SIZE + CELL_SIZE / 2 - 1, 2, 2);
            }
        }
        const pathLength = logoPath.getTotalLength();
        const logoScale = Math.min((canvasWidth * 0.42) / 100, (canvasHeight * 0.62) / 54);
        const logoOriginX = canvasWidth * 0.94 - 100 * logoScale;
        const logoOriginY = canvasHeight * 0.42 - 27 * logoScale;
        logoPathPoints = [];
        for (let i = 0; i <= 260; i++) {
            const pathPoint = logoPath.getPointAtLength((pathLength * i) / 260);
            logoPathPoints.push([logoOriginX + pathPoint.x * logoScale, logoOriginY + pathPoint.y * logoScale]);
        }
    };
    resizeCanvas();
    addEventListener('resize', resizeCanvas);

    const paintCells = (centerX, centerY, brushRadius, freshness) => {
        const centerColumn = Math.floor(centerX / CELL_SIZE);
        const centerRow = Math.floor(centerY / CELL_SIZE);
        for (let j = -brushRadius; j <= brushRadius; j++) {
            for (let i = -brushRadius; i <= brushRadius; i++) {
                const column = centerColumn + i;
                const row = centerRow + j;
                if (column < 0 || row < 0 || column >= columnCount || row >= rowCount) {
                    continue;
                }
                const distance = Math.hypot(i, j);
                if (distance > brushRadius + 0.2) {
                    continue;
                }
                const cellIndex = row * columnCount + column;
                cellIntensities[cellIndex] = Math.min(
                    1,
                    cellIntensities[cellIndex] + (1 - distance / (brushRadius + 1)) * 0.9,
                );
                cellFreshness[cellIndex] = Math.max(cellFreshness[cellIndex], freshness);
            }
        }
    };

    let autoBrushProgress = 0;
    let lastPointerPosition = null;
    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = heroElement.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        if (!prefersReducedMotion) {
            autoBrushProgress += deltaSeconds * 0.3;
            const cycleProgress = autoBrushProgress % 1.35;
            if (cycleProgress < 1) {
                const brushPoint = logoPathPoints[Math.floor(cycleProgress * (logoPathPoints.length - 1))];
                if (brushPoint) {
                    paintCells(brushPoint[0], brushPoint[1], 1, 0.15);
                }
            }
        } else if (logoPathPoints.length) {
            logoPathPoints.forEach((pathPoint) => paintCells(pathPoint[0], pathPoint[1], 1, 0));
        }
        if (pointer.hasMoved && hasFinePointer) {
            const pointerX = pointer.x - heroBounds.left;
            const pointerY = pointer.y - heroBounds.top;
            if (pointerX >= 0 && pointerY >= 0 && pointerX <= canvasWidth && pointerY <= canvasHeight) {
                if (lastPointerPosition) {
                    const stepCount = Math.max(
                        1,
                        Math.ceil(
                            Math.hypot(pointerX - lastPointerPosition[0], pointerY - lastPointerPosition[1]) /
                                (CELL_SIZE / 2),
                        ),
                    );
                    for (let i = 1; i <= stepCount; i++) {
                        paintCells(
                            lastPointerPosition[0] + ((pointerX - lastPointerPosition[0]) * i) / stepCount,
                            lastPointerPosition[1] + ((pointerY - lastPointerPosition[1]) * i) / stepCount,
                            1,
                            1,
                        );
                    }
                }
                lastPointerPosition = [pointerX, pointerY];
            } else {
                lastPointerPosition = null;
            }
        }
        canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
        canvasContext.drawImage(baseLayerCanvas, 0, 0, canvasWidth, canvasHeight);
        const slowDecay = prefersReducedMotion ? 1 : Math.pow(0.8, deltaSeconds);
        const fastDecay = Math.pow(0.3, deltaSeconds);
        const freshnessDecay = Math.pow(0.25, deltaSeconds);
        for (let cellIndex = 0; cellIndex < cellIntensities.length; cellIndex++) {
            const intensity = cellIntensities[cellIndex];
            if (intensity < 0.02) {
                cellIntensities[cellIndex] = 0;
                continue;
            }
            const cellX = (cellIndex % columnCount) * CELL_SIZE;
            const cellY = Math.floor(cellIndex / columnCount) * CELL_SIZE;
            const squareSize = 3 + intensity * (CELL_SIZE - 5);
            const freshness = cellFreshness[cellIndex];
            canvasContext.fillStyle =
                freshness > 0.3
                    ? `rgba(255,74,28,${(0.35 + 0.65 * intensity).toFixed(3)})`
                    : `rgba(241,240,236,${(0.12 + 0.55 * intensity).toFixed(3)})`;
            canvasContext.fillRect(
                cellX + (CELL_SIZE - squareSize) / 2,
                cellY + (CELL_SIZE - squareSize) / 2,
                squareSize,
                squareSize,
            );
            cellIntensities[cellIndex] = intensity * (freshness > 0.3 ? fastDecay : slowDecay);
            cellFreshness[cellIndex] = freshness * freshnessDecay;
        }
    });

    return () => {
        removeEventListener('resize', resizeCanvas);
        svgHolder.remove();
    };
}
