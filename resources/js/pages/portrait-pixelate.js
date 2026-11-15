const CELL_SIZE = 12;
const BRUSH_RADIUS_CELLS = 5;
const DECAY_PER_FRAME = 0.9;

export default function (frameElement, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, smoothStep } = siteRuntime;
    const imageElement = frameElement.querySelector('img');
    if (!imageElement || prefersReducedMotion || !hasFinePointer) {
        return;
    }

    const canvasElement = document.createElement('canvas');
    canvasElement.className = 'portrait-pixelate';
    canvasElement.setAttribute('aria-hidden', 'true');
    frameElement.appendChild(canvasElement);
    const canvasContext = canvasElement.getContext('2d');
    const sampleCanvas = document.createElement('canvas');
    const sampleContext = sampleCanvas.getContext('2d', { willReadFrequently: true });

    let columnCount = 0;
    let rowCount = 0;
    let cellIntensities = new Float32Array(0);
    let cellColors = new Uint8ClampedArray(0);
    let pointerColumn = -100;
    let pointerRow = -100;
    let isHovering = false;
    let isDisposed = false;
    let hasActiveCells = false;

    const buildSamples = () => {
        const bounds = frameElement.getBoundingClientRect();
        if (!imageElement.naturalWidth || !bounds.width) {
            return;
        }
        columnCount = Math.ceil(bounds.width / CELL_SIZE);
        rowCount = Math.ceil(bounds.height / CELL_SIZE);
        canvasElement.width = columnCount * CELL_SIZE;
        canvasElement.height = rowCount * CELL_SIZE;
        sampleCanvas.width = columnCount;
        sampleCanvas.height = rowCount;
        const imageRatio = imageElement.naturalWidth / imageElement.naturalHeight;
        const frameRatio = columnCount / rowCount;
        const sourceWidth = imageRatio > frameRatio ? imageElement.naturalHeight * frameRatio : imageElement.naturalWidth;
        const sourceHeight = imageRatio > frameRatio ? imageElement.naturalHeight : imageElement.naturalWidth / frameRatio;
        sampleContext.drawImage(
            imageElement,
            (imageElement.naturalWidth - sourceWidth) / 2,
            (imageElement.naturalHeight - sourceHeight) / 2,
            sourceWidth,
            sourceHeight,
            0,
            0,
            columnCount,
            rowCount,
        );
        cellColors = sampleContext.getImageData(0, 0, columnCount, rowCount).data;
        cellIntensities = new Float32Array(columnCount * rowCount);
    };

    const updatePointerCell = (event) => {
        const bounds = frameElement.getBoundingClientRect();
        pointerColumn = (event.clientX - bounds.left) / CELL_SIZE;
        pointerRow = (event.clientY - bounds.top) / CELL_SIZE;
    };

    const onPointerEnter = (event) => {
        isHovering = true;
        updatePointerCell(event);
    };
    const onPointerLeave = () => {
        isHovering = false;
    };

    frameElement.addEventListener('pointerenter', onPointerEnter);
    frameElement.addEventListener('pointermove', updatePointerCell, { passive: true });
    frameElement.addEventListener('pointerleave', onPointerLeave);
    addEventListener('resize', buildSamples, { passive: true });
    if (imageElement.complete) {
        buildSamples();
    } else {
        imageElement.addEventListener('load', buildSamples, { once: true });
    }

    siteRuntime.frameCallbacks.push(() => {
        if (isDisposed || !columnCount || (!isHovering && !hasActiveCells)) {
            return;
        }
        if (isHovering) {
            const minColumn = Math.max(0, Math.floor(pointerColumn - BRUSH_RADIUS_CELLS));
            const maxColumn = Math.min(columnCount - 1, Math.ceil(pointerColumn + BRUSH_RADIUS_CELLS));
            const minRow = Math.max(0, Math.floor(pointerRow - BRUSH_RADIUS_CELLS));
            const maxRow = Math.min(rowCount - 1, Math.ceil(pointerRow + BRUSH_RADIUS_CELLS));
            for (let row = minRow; row <= maxRow; row++) {
                for (let column = minColumn; column <= maxColumn; column++) {
                    const distance = Math.hypot(column + 0.5 - pointerColumn, row + 0.5 - pointerRow);
                    const strength = smoothStep(BRUSH_RADIUS_CELLS, BRUSH_RADIUS_CELLS * 0.35, distance);
                    const cellIndex = row * columnCount + column;
                    cellIntensities[cellIndex] = Math.max(cellIntensities[cellIndex], strength);
                }
            }
        }

        canvasContext.clearRect(0, 0, canvasElement.width, canvasElement.height);
        hasActiveCells = false;
        for (let cellIndex = 0; cellIndex < cellIntensities.length; cellIndex++) {
            const intensity = cellIntensities[cellIndex];
            if (intensity < 0.04) {
                cellIntensities[cellIndex] = 0;
                continue;
            }
            hasActiveCells = true;
            const colorOffset = cellIndex * 4;
            const luminance = Math.round(
                cellColors[colorOffset] * 0.3 + cellColors[colorOffset + 1] * 0.59 + cellColors[colorOffset + 2] * 0.11,
            );
            const isEdge = intensity < 0.45;
            canvasContext.fillStyle = isEdge
                ? `rgba(255,74,28,${(0.25 + intensity * 1.2).toFixed(3)})`
                : `rgb(${luminance},${luminance},${luminance})`;
            const cellX = (cellIndex % columnCount) * CELL_SIZE;
            const cellY = Math.floor(cellIndex / columnCount) * CELL_SIZE;
            const inset = isEdge ? 2 : 0;
            canvasContext.fillRect(cellX + inset, cellY + inset, CELL_SIZE - inset * 2, CELL_SIZE - inset * 2);
            cellIntensities[cellIndex] = intensity * DECAY_PER_FRAME;
        }
    });

    return () => {
        isDisposed = true;
        removeEventListener('resize', buildSamples);
        canvasElement.remove();
    };
}
