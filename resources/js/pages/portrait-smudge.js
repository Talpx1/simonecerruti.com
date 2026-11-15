const CELL_SIZE = 10;
const BRUSH_RADIUS = 96;
const DRAG_FORCE = 0.55;
const BURST_FORCE = 26;
const SPRING_STIFFNESS = 0.07;
const SPRING_DAMPING = 0.86;
const MAXIMUM_OFFSET = 70;
const REST_THRESHOLD = 0.05;

export default function (frameElement, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, smoothStep } = siteRuntime;
    const imageElement = frameElement.querySelector('img');
    if (!imageElement || prefersReducedMotion || !hasFinePointer) {
        return;
    }

    const canvasElement = document.createElement('canvas');
    canvasElement.className = 'portrait-smudge';
    canvasElement.setAttribute('aria-hidden', 'true');
    frameElement.appendChild(canvasElement);
    const canvasContext = canvasElement.getContext('2d');

    let frameWidth = 0;
    let frameHeight = 0;
    let columnCount = 0;
    let rowCount = 0;
    let offsetsX = new Float32Array(0);
    let offsetsY = new Float32Array(0);
    let velocitiesX = new Float32Array(0);
    let velocitiesY = new Float32Array(0);
    let coverScale = 1;
    let coverOffsetX = 0;
    let coverOffsetY = 0;
    let pointerX = 0;
    let pointerY = 0;
    let pointerDeltaX = 0;
    let pointerDeltaY = 0;
    let isHovering = false;
    let isDisposed = false;
    let isMoving = false;

    const buildGrid = () => {
        const bounds = frameElement.getBoundingClientRect();
        if (!imageElement.naturalWidth || !bounds.width) {
            return;
        }
        const pixelRatio = Math.min(devicePixelRatio || 1, 2);
        frameWidth = bounds.width;
        frameHeight = bounds.height;
        canvasElement.width = Math.round(frameWidth * pixelRatio);
        canvasElement.height = Math.round(frameHeight * pixelRatio);
        canvasContext.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        columnCount = Math.ceil(frameWidth / CELL_SIZE);
        rowCount = Math.ceil(frameHeight / CELL_SIZE);
        const cellTotal = columnCount * rowCount;
        offsetsX = new Float32Array(cellTotal);
        offsetsY = new Float32Array(cellTotal);
        velocitiesX = new Float32Array(cellTotal);
        velocitiesY = new Float32Array(cellTotal);
        coverScale = Math.max(frameWidth / imageElement.naturalWidth, frameHeight / imageElement.naturalHeight);
        coverOffsetX = (frameWidth - imageElement.naturalWidth * coverScale) / 2;
        coverOffsetY = (frameHeight - imageElement.naturalHeight * coverScale) / 2;
    };

    const onPointerMove = (event) => {
        const bounds = frameElement.getBoundingClientRect();
        const nextX = event.clientX - bounds.left;
        const nextY = event.clientY - bounds.top;
        if (isHovering) {
            pointerDeltaX += nextX - pointerX;
            pointerDeltaY += nextY - pointerY;
        }
        pointerX = nextX;
        pointerY = nextY;
    };
    const onPointerEnter = (event) => {
        isHovering = true;
        const bounds = frameElement.getBoundingClientRect();
        pointerX = event.clientX - bounds.left;
        pointerY = event.clientY - bounds.top;
    };
    const onPointerLeave = () => {
        isHovering = false;
    };
    const onPointerDown = (event) => {
        const bounds = frameElement.getBoundingClientRect();
        const burstX = event.clientX - bounds.left;
        const burstY = event.clientY - bounds.top;
        for (let row = 0; row < rowCount; row++) {
            for (let column = 0; column < columnCount; column++) {
                const deltaX = column * CELL_SIZE + CELL_SIZE / 2 - burstX;
                const deltaY = row * CELL_SIZE + CELL_SIZE / 2 - burstY;
                const distance = Math.hypot(deltaX, deltaY) || 1;
                const strength = smoothStep(220, 0, distance) * BURST_FORCE;
                const cellIndex = row * columnCount + column;
                velocitiesX[cellIndex] += (deltaX / distance) * strength;
                velocitiesY[cellIndex] += (deltaY / distance) * strength;
            }
        }
        isMoving = true;
    };

    frameElement.addEventListener('pointerenter', onPointerEnter);
    frameElement.addEventListener('pointermove', onPointerMove, { passive: true });
    frameElement.addEventListener('pointerleave', onPointerLeave);
    frameElement.addEventListener('pointerdown', onPointerDown);
    addEventListener('resize', buildGrid, { passive: true });
    if (imageElement.complete) {
        buildGrid();
    } else {
        imageElement.addEventListener('load', buildGrid, { once: true });
    }

    siteRuntime.frameCallbacks.push(() => {
        if (isDisposed || !columnCount) {
            return;
        }
        const hasPointerPush = isHovering && (pointerDeltaX !== 0 || pointerDeltaY !== 0);
        if (!hasPointerPush && !isMoving) {
            return;
        }

        if (hasPointerPush) {
            const minColumn = Math.max(0, Math.floor((pointerX - BRUSH_RADIUS) / CELL_SIZE));
            const maxColumn = Math.min(columnCount - 1, Math.ceil((pointerX + BRUSH_RADIUS) / CELL_SIZE));
            const minRow = Math.max(0, Math.floor((pointerY - BRUSH_RADIUS) / CELL_SIZE));
            const maxRow = Math.min(rowCount - 1, Math.ceil((pointerY + BRUSH_RADIUS) / CELL_SIZE));
            for (let row = minRow; row <= maxRow; row++) {
                for (let column = minColumn; column <= maxColumn; column++) {
                    const distance = Math.hypot(column * CELL_SIZE + CELL_SIZE / 2 - pointerX, row * CELL_SIZE + CELL_SIZE / 2 - pointerY);
                    const weight = smoothStep(BRUSH_RADIUS, 0, distance) * DRAG_FORCE;
                    const cellIndex = row * columnCount + column;
                    velocitiesX[cellIndex] += pointerDeltaX * weight;
                    velocitiesY[cellIndex] += pointerDeltaY * weight;
                }
            }
            pointerDeltaX = 0;
            pointerDeltaY = 0;
            isMoving = true;
        }

        canvasContext.clearRect(0, 0, frameWidth, frameHeight);
        let hasMovingCells = false;
        for (let row = 0; row < rowCount; row++) {
            for (let column = 0; column < columnCount; column++) {
                const cellIndex = row * columnCount + column;
                velocitiesX[cellIndex] = (velocitiesX[cellIndex] - offsetsX[cellIndex] * SPRING_STIFFNESS) * SPRING_DAMPING;
                velocitiesY[cellIndex] = (velocitiesY[cellIndex] - offsetsY[cellIndex] * SPRING_STIFFNESS) * SPRING_DAMPING;
                offsetsX[cellIndex] = Math.max(-MAXIMUM_OFFSET, Math.min(MAXIMUM_OFFSET, offsetsX[cellIndex] + velocitiesX[cellIndex]));
                offsetsY[cellIndex] = Math.max(-MAXIMUM_OFFSET, Math.min(MAXIMUM_OFFSET, offsetsY[cellIndex] + velocitiesY[cellIndex]));

                const isAtRest =
                    Math.abs(offsetsX[cellIndex]) < REST_THRESHOLD &&
                    Math.abs(offsetsY[cellIndex]) < REST_THRESHOLD &&
                    Math.abs(velocitiesX[cellIndex]) < REST_THRESHOLD &&
                    Math.abs(velocitiesY[cellIndex]) < REST_THRESHOLD;
                if (isAtRest) {
                    offsetsX[cellIndex] = 0;
                    offsetsY[cellIndex] = 0;
                    velocitiesX[cellIndex] = 0;
                    velocitiesY[cellIndex] = 0;
                    continue;
                }
                hasMovingCells = true;

                const cellX = column * CELL_SIZE;
                const cellY = row * CELL_SIZE;
                const sourceX = (cellX - offsetsX[cellIndex] - coverOffsetX) / coverScale;
                const sourceY = (cellY - offsetsY[cellIndex] - coverOffsetY) / coverScale;
                const sourceSize = CELL_SIZE / coverScale;
                const clampedX = Math.max(0, Math.min(imageElement.naturalWidth - sourceSize, sourceX));
                const clampedY = Math.max(0, Math.min(imageElement.naturalHeight - sourceSize, sourceY));
                canvasContext.drawImage(imageElement, clampedX, clampedY, sourceSize, sourceSize, cellX, cellY, CELL_SIZE + 0.6, CELL_SIZE + 0.6);
            }
        }
        isMoving = hasMovingCells;
    });

    return () => {
        isDisposed = true;
        removeEventListener('resize', buildGrid);
        canvasElement.remove();
    };
}
