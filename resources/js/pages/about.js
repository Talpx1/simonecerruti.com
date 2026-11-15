export default function (rootElement, siteRuntime) {
    const { queryOne, queryAll, prefersReducedMotion, clamp } = siteRuntime;
    const paintCanvas = queryOne('#pixel-paint-canvas', rootElement);
    const canvasContext = paintCanvas.getContext('2d');
    const portraitElement = queryOne('.portrait-frame');
    const helloText = paintCanvas.dataset.hello;
    const abortController = new AbortController();
    const { signal } = abortController;

    const resetCanvas = () => {
        canvasContext.fillStyle = '#F1F0EC';
        canvasContext.fillRect(0, 0, paintCanvas.width, paintCanvas.height);
        canvasContext.fillStyle = '#0A0A0A';
        canvasContext.font = '700 15px Archivo, Arial';
        canvasContext.fillText(helloText, 14, 26);
    };
    resetCanvas();

    let brushColor = '#0A0A0A';
    let isDrawing = false;
    let lastPoint = null;
    const colorSwatches = queryAll('.about-paint-screen-tools [data-col]', rootElement);
    colorSwatches.forEach((swatchButton) =>
        swatchButton.addEventListener(
            'click',
            () => {
                brushColor = swatchButton.dataset.col;
                colorSwatches.forEach((otherSwatch) => otherSwatch.classList.toggle('is-active', otherSwatch === swatchButton));
            },
            { signal },
        ),
    );
    queryOne('#paint-clear-button', rootElement).addEventListener('click', resetCanvas, { signal });

    const getCanvasPoint = (event) => {
        const canvasRect = paintCanvas.getBoundingClientRect();
        return [
            ((event.clientX - canvasRect.left) / canvasRect.width) * paintCanvas.width,
            ((event.clientY - canvasRect.top) / canvasRect.height) * paintCanvas.height,
        ];
    };
    const drawDot = (pointX, pointY) => {
        canvasContext.fillStyle = brushColor;
        canvasContext.fillRect(Math.round(pointX) - 2, Math.round(pointY) - 2, 4, 4);
    };
    paintCanvas.addEventListener(
        'pointerdown',
        (event) => {
            isDrawing = true;
            lastPoint = getCanvasPoint(event);
            drawDot(...lastPoint);
            paintCanvas.setPointerCapture(event.pointerId);
        },
        { signal },
    );
    paintCanvas.addEventListener(
        'pointermove',
        (event) => {
            if (!isDrawing) {
                return;
            }
            const currentPoint = getCanvasPoint(event);
            const stepCount = Math.ceil(Math.hypot(currentPoint[0] - lastPoint[0], currentPoint[1] - lastPoint[1]) / 2);
            for (let i = 0; i <= stepCount; i++) {
                drawDot(
                    lastPoint[0] + ((currentPoint[0] - lastPoint[0]) * i) / stepCount,
                    lastPoint[1] + ((currentPoint[1] - lastPoint[1]) * i) / stepCount,
                );
            }
            lastPoint = currentPoint;
        },
        { signal },
    );
    addEventListener('pointerup', () => (isDrawing = false), { signal });

    queryAll('.accordion-item').forEach((expandableRow) =>
        expandableRow.addEventListener('click', () => expandableRow.classList.toggle('is-open'), { signal }),
    );

    siteRuntime.frameCallbacks.push(() => {
        if (!prefersReducedMotion && portraitElement) {
            const portraitTop = portraitElement.getBoundingClientRect().top;
            portraitElement.style.setProperty(
                '--portrait-reveal-progress',
                clamp((innerHeight - portraitTop) / (innerHeight * 0.55), 0, 1).toFixed(3),
            );
        }
    });

    return () => abortController.abort();
}
