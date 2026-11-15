const SCREEN_WORLD_WIDTH = 4.4;
const SCREEN_WORLD_HEIGHT = 2.75;
const SCREEN_UNITS_WIDTH = 44;
const SCREEN_UNITS_HEIGHT = 27.5;
const FONT = '"Archivo", "Helvetica Neue", Arial, sans-serif';
const convertUnitsToWorld = (unitX, unitY) => [
    (unitX / SCREEN_UNITS_WIDTH) * SCREEN_WORLD_WIDTH - SCREEN_WORLD_WIDTH / 2,
    SCREEN_WORLD_HEIGHT / 2 - (unitY / SCREEN_UNITS_HEIGHT) * SCREEN_WORLD_HEIGHT,
];

const UI_WIREFRAME_RECTANGLES = [[0, 0, 44, 27.5], [0, 0, 8, 27.5], [8, 0, 36, 3.4], [9.5, 4.4, 7.6, 5.4], [18.3, 4.4, 7.6, 5.4], [27.1, 4.4, 7.6, 5.4], [35.9, 4.4, 7.6, 5.4], [9.5, 11, 21.5, 15.3], [32.3, 11, 11.2, 15.3], [35.5, 0.9, 7, 1.6], [24, 0.9, 10.5, 1.6]];
const CORE_RECTANGLE = [15.5, 10.9, 13, 5.7];
const MODULE_RECTANGLES = [[3, 2.6, 9, 3.2], [17.5, 1.6, 9, 3.2], [32, 2.6, 9, 3.2], [1, 12.15, 8, 3.2, 1], [34.5, 12.15, 8.5, 3.2], [3, 21.7, 9, 3.2, 1], [17.5, 22.7, 9, 3.2], [32, 21.7, 9, 3.2]];
const CHART_BAR_VALUES = [6, 8, 5, 9, 7, 4, 3, 7, 9, 6, 10, 8, 9, 11];

function getRectangleExitPoint(rectangle, centerX, centerY, directionX, directionY) {
    const halfWidth = rectangle[2] / 2;
    const halfHeight = rectangle[3] / 2;
    const horizontalDistance = directionX ? halfWidth / Math.abs(directionX) : 1e9;
    const verticalDistance = directionY ? halfHeight / Math.abs(directionY) : 1e9;
    const exitDistance = Math.min(horizontalDistance, verticalDistance);
    return [centerX + directionX * exitDistance, centerY + directionY * exitDistance];
}

const MODULE_CONNECTIONS = MODULE_RECTANGLES.map((moduleRectangle) => {
    const moduleCenter = [moduleRectangle[0] + moduleRectangle[2] / 2, moduleRectangle[1] + moduleRectangle[3] / 2];
    const coreCenter = [CORE_RECTANGLE[0] + CORE_RECTANGLE[2] / 2, CORE_RECTANGLE[1] + CORE_RECTANGLE[3] / 2];
    const deltaX = coreCenter[0] - moduleCenter[0];
    const deltaY = coreCenter[1] - moduleCenter[1];
    const distance = Math.hypot(deltaX, deltaY);
    const startPoint = getRectangleExitPoint(moduleRectangle, moduleCenter[0], moduleCenter[1], deltaX / distance, deltaY / distance);
    const endPoint = getRectangleExitPoint(CORE_RECTANGLE, coreCenter[0], coreCenter[1], -deltaX / distance, -deltaY / distance);
    return [startPoint[0], startPoint[1], endPoint[0], endPoint[1], moduleRectangle[4] ? 1 : 0];
});

const getRectangleSegments = (rectangle) => [
    [rectangle[0], rectangle[1], rectangle[0] + rectangle[2], rectangle[1]],
    [rectangle[0] + rectangle[2], rectangle[1], rectangle[0] + rectangle[2], rectangle[1] + rectangle[3]],
    [rectangle[0] + rectangle[2], rectangle[1] + rectangle[3], rectangle[0], rectangle[1] + rectangle[3]],
    [rectangle[0], rectangle[1] + rectangle[3], rectangle[0], rectangle[1]],
];

function drawRoundedRectanglePath(context, rectangleX, rectangleY, rectangleWidth, rectangleHeight, cornerRadius) {
    context.beginPath();
    context.moveTo(rectangleX + cornerRadius, rectangleY);
    context.arcTo(rectangleX + rectangleWidth, rectangleY, rectangleX + rectangleWidth, rectangleY + rectangleHeight, cornerRadius);
    context.arcTo(rectangleX + rectangleWidth, rectangleY + rectangleHeight, rectangleX, rectangleY + rectangleHeight, cornerRadius);
    context.arcTo(rectangleX, rectangleY + rectangleHeight, rectangleX, rectangleY, cornerRadius);
    context.arcTo(rectangleX, rectangleY, rectangleX + rectangleWidth, rectangleY, cornerRadius);
    context.closePath();
}

export default function (element, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, clamp, linearInterpolate, smoothStep, queryOne, queryAll, pointer } = siteRuntime;
    const config = JSON.parse(element.dataset.config || '{}');
    const documentRoot = document.documentElement;
    const easeOutCubic = (progress) => 1 - Math.pow(1 - clamp(progress, 0, 1), 3);

    let isDisposed = false;
    let viewportWidth = innerWidth;
    let viewportHeight = innerHeight;
    let scrollPosition = scrollY;
    let isHorizontalLayout = viewportWidth > 900;
    let threeState = null;
    let marqueeOffset = 0;
    let lastChapterIndex = -1;
    const sectionBounds = {};
    const cleanupCallbacks = [];
    const addListener = (target, eventType, handler, options) => {
        target.addEventListener(eventType, handler, options);
        cleanupCallbacks.push(() => target.removeEventListener(eventType, handler, options));
    };

    const marqueeRows = queryAll('.home-marquee-track', element).map((rowElement) => ({ element: rowElement, direction: Number(rowElement.parentElement.dataset.direction) }));
    const screenElement = queryOne('.before-after-screen', element);
    const workItems = queryAll('.home-works-item', element);
    const workShots = queryAll('.home-works-shot', element);
    const workNowLabels = queryAll('.home-works-caption', element);
    const giantLetters = queryAll('.home-story-word span', element);
    const chapterElements = queryAll('.home-story-chapter', element);
    const indexButtons = queryAll('.home-story-nav button', element);
    const portraitElement = queryOne('.portrait-frame', element);
    let activeWorkIndex = -1;
    let screenTargetProgress = 1;
    let screenCurrentProgress = 1;
    let isScreenHovered = false;
    let screenWorkChangedAt = 0;

    const activateWork = (workIndex) => {
        if (workIndex === activeWorkIndex) {
            return;
        }
        activeWorkIndex = workIndex;
        workItems.forEach((workItem, index) => workItem.classList.toggle('is-active', index === workIndex));
        workShots.forEach((workShot, index) => workShot.classList.toggle('is-active', index === workIndex));
        workNowLabels.forEach((workNowLabel, index) => workNowLabel.classList.toggle('is-active', index === workIndex));
        screenCurrentProgress = 0;
        screenWorkChangedAt = performance.now();
    };

    if (screenElement) {
        addListener(screenElement, 'pointermove', (event) => {
            const screenBounds = screenElement.getBoundingClientRect();
            isScreenHovered = true;
            screenTargetProgress = clamp((event.clientX - screenBounds.left) / screenBounds.width, 0, 1);
        });
        addListener(screenElement, 'pointerleave', () => {
            isScreenHovered = false;
            screenTargetProgress = 1;
        });
    }

    workItems.forEach((workItem, workIndex) =>
        addListener(workItem, 'click', () => {
            const worksSection = sectionBounds.works;
            if (isHorizontalLayout && worksSection) {
                scrollTo({ top: worksSection.top + ((workIndex + 0.5) / workItems.length) * (worksSection.height - viewportHeight), behavior: prefersReducedMotion ? 'auto' : 'smooth' });
            } else {
                activateWork(workIndex);
            }
        }),
    );

    indexButtons.forEach((indexButton) =>
        addListener(indexButton, 'click', () => {
            const storySection = sectionBounds.story;
            if (!storySection) {
                return;
            }
            scrollTo({ top: storySection.top + ((Number(indexButton.dataset.chapterIndex) + 0.6) / 6) * (storySection.height - viewportHeight), behavior: prefersReducedMotion ? 'auto' : 'smooth' });
        }),
    );

    function updateLayout() {
        viewportWidth = innerWidth;
        viewportHeight = innerHeight;
        isHorizontalLayout = viewportWidth > 900;
        queryAll('[data-section]', element).forEach((sectionElement) => {
            const sectionRect = sectionElement.getBoundingClientRect();
            sectionBounds[sectionElement.dataset.section] = { top: sectionRect.top + scrollY, height: sectionElement.offsetHeight };
        });
        if (threeState) {
            threeState.renderer.setSize(viewportWidth, viewportHeight, false);
            threeState.camera.aspect = viewportWidth / viewportHeight;
            threeState.camera.updateProjectionMatrix();
        }
    }

    addListener(window, 'resize', updateLayout);
    addListener(window, 'load', updateLayout);
    const resizeObserver = new ResizeObserver(updateLayout);
    resizeObserver.observe(element);
    cleanupCallbacks.push(() => resizeObserver.disconnect());
    updateLayout();

    const sectionAnchors = {
        hero: { x: 0, y: 0, scale: 1, visibility: 1 },
        mq: { x: 0, y: 0, scale: 1, visibility: 0.6 },
        story: { x: 0, y: 0.15, scale: 1, visibility: 1 },
        works: { x: 0, y: 0.15, scale: 1, visibility: 0 },
        svc: { x: 0, y: 0, scale: 1, visibility: 0 },
        man: { x: 0, y: 0, scale: 1, visibility: 0 },
        about: { x: 0, y: 0, scale: 1, visibility: 0 },
        contact: { x: 0, y: 0, scale: 1, visibility: 0 },
    };

    const archLabels = config.arch?.modules || [];
    const uiConfig = config.ui || {};

    function drawArchitecture(canvas, progress) {
        const context = canvas.getContext('2d');
        const unitScale = canvas.width / SCREEN_UNITS_WIDTH;
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.lineCap = 'round';
        const getModuleProgress = (moduleIndex) => clamp((progress - 0.12 - moduleIndex * 0.075) / 0.22, 0, 1);
        context.globalAlpha = clamp(progress / 0.2, 0, 1);
        context.strokeStyle = 'rgba(241,240,236,.55)';
        context.lineWidth = 1.6;
        context.setLineDash([]);
        context.strokeRect(CORE_RECTANGLE[0] * unitScale, CORE_RECTANGLE[1] * unitScale, CORE_RECTANGLE[2] * unitScale, CORE_RECTANGLE[3] * unitScale);
        context.fillStyle = 'rgba(241,240,236,.9)';
        context.font = `800 ${0.62 * unitScale}px ${FONT}`;
        context.textAlign = 'center';
        context.fillText(config.arch?.core || '', (CORE_RECTANGLE[0] + CORE_RECTANGLE[2] / 2) * unitScale, (CORE_RECTANGLE[1] + 2.75) * unitScale);
        context.fillStyle = 'rgba(241,240,236,.4)';
        context.font = `500 ${0.42 * unitScale}px ${FONT}`;
        context.fillText(config.arch?.core_sub || '', (CORE_RECTANGLE[0] + CORE_RECTANGLE[2] / 2) * unitScale, (CORE_RECTANGLE[1] + 3.75) * unitScale);
        MODULE_RECTANGLES.forEach((moduleRectangle, moduleIndex) => {
            const moduleProgress = getModuleProgress(moduleIndex);
            if (moduleProgress <= 0) {
                return;
            }
            context.globalAlpha = Math.min(1, moduleProgress * 1.6) * 0.9;
            context.strokeStyle = 'rgba(241,240,236,.32)';
            context.lineWidth = 1.3;
            context.setLineDash([]);
            context.strokeRect(moduleRectangle[0] * unitScale, moduleRectangle[1] * unitScale, moduleRectangle[2] * unitScale, moduleRectangle[3] * unitScale);
            context.fillStyle = 'rgba(241,240,236,.7)';
            context.font = `650 ${0.46 * unitScale}px ${FONT}`;
            context.textAlign = 'center';
            context.fillText(archLabels[moduleIndex] || '', (moduleRectangle[0] + moduleRectangle[2] / 2) * unitScale, (moduleRectangle[1] + moduleRectangle[3] / 2 + 0.17) * unitScale);
            const connection = MODULE_CONNECTIONS[moduleIndex];
            const connectionProgress = clamp((moduleProgress - 0.35) / 0.65, 0, 1);
            if (connectionProgress > 0) {
                context.strokeStyle = 'rgba(241,240,236,.3)';
                context.lineWidth = 1.3;
                context.setLineDash([2, 7]);
                context.beginPath();
                context.moveTo(connection[0] * unitScale, connection[1] * unitScale);
                context.lineTo((connection[0] + (connection[2] - connection[0]) * connectionProgress) * unitScale, (connection[1] + (connection[3] - connection[1]) * connectionProgress) * unitScale);
                context.stroke();
                if (connectionProgress >= 1) {
                    context.fillStyle = 'rgba(241,240,236,.6)';
                    context.beginPath();
                    context.arc(connection[2] * unitScale, connection[3] * unitScale, 4, 0, Math.PI * 2);
                    context.fill();
                }
            }
        });
        context.setLineDash([]);
        context.globalAlpha = 1;
    }

    const formatNumber = (value, digits) =>
        value.toLocaleString(config.locale || undefined, { minimumFractionDigits: digits, maximumFractionDigits: digits });

    function drawUserInterface(canvas, aliveProgress) {
        const context = canvas.getContext('2d');
        const unitScale = canvas.width / SCREEN_UNITS_WIDTH;
        const toPixels = (units) => units * unitScale;
        const drawText = (text, unitX, unitY, size, weight, color, align) => {
            context.font = `${weight} ${toPixels(size)}px ${FONT}`;
            context.fillStyle = color;
            context.textAlign = align || 'left';
            context.fillText(text, toPixels(unitX), toPixels(unitY));
        };
        const drawSkeleton = (unitX, unitY, skeletonWidth, skeletonHeight) => {
            context.fillStyle = '#222';
            drawRoundedRectanglePath(context, toPixels(unitX), toPixels(unitY), toPixels(skeletonWidth), toPixels(skeletonHeight), toPixels(Math.min(skeletonHeight / 2, 0.3)));
            context.fill();
        };
        const dataProgress = easeOutCubic(aliveProgress / 0.42);
        const arrivedProgress = easeOutCubic((aliveProgress - 0.5) / 0.28);
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = '#0F0F0F';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.fillStyle = '#0A0A0A';
        context.fillRect(0, 0, toPixels(8), canvas.height);
        context.fillStyle = '#FF4A1C';
        context.fillRect(toPixels(1), toPixels(1.05), toPixels(0.9), toPixels(0.9));
        drawText(uiConfig.app || '', 2.3, 1.82, 0.62, 800, '#F1F0EC');
        (uiConfig.nav || []).forEach((navLabel, navIndex) => {
            const unitY = 5.2 + navIndex * 2.05;
            if (navIndex === 0) {
                context.fillStyle = '#1C1C1C';
                drawRoundedRectanglePath(context, toPixels(0.6), toPixels(unitY - 1.0), toPixels(6.8), toPixels(1.5), toPixels(0.25));
                context.fill();
                context.fillStyle = '#FF4A1C';
                context.fillRect(0, toPixels(unitY - 0.9), toPixels(0.18), toPixels(1.3));
            }
            drawText(navLabel, 1.4, unitY, 0.58, navIndex === 0 ? 750 : 550, navIndex === 0 ? '#F1F0EC' : '#8C8B86');
        });
        context.fillStyle = '#262626';
        context.beginPath();
        context.arc(toPixels(1.9), toPixels(25.6), toPixels(0.7), 0, Math.PI * 2);
        context.fill();
        drawText(uiConfig.user || '', 3, 25.8, 0.52, 650, '#BDBCB7');
        drawText(uiConfig.greeting || '', 9.5, 2.05, 0.95, 850, '#F1F0EC');
        const nameUnitX = 9.5 + context.measureText(`${uiConfig.greeting || ''} `).width / unitScale;
        if (dataProgress > 0.02) {
            context.globalAlpha = dataProgress;
            drawText(uiConfig.first_name || '', nameUnitX, 2.05, 0.95, 850, '#F1F0EC');
            context.globalAlpha = 1;
        } else {
            drawSkeleton(nameUnitX, 1.25, 4.2, 0.95);
        }
        drawText(dataProgress > 0.5 ? uiConfig.all_good : uiConfig.loading, 9.5, 2.95, 0.46, 550, '#8C8B86');
        context.strokeStyle = '#2A2A2A';
        context.lineWidth = 2;
        drawRoundedRectanglePath(context, toPixels(24), toPixels(0.9), toPixels(10.5), toPixels(1.6), toPixels(0.3));
        context.stroke();
        drawText(uiConfig.search || '', 24.6, 1.92, 0.5, 500, '#6E6D69');
        context.fillStyle = '#FF4A1C';
        drawRoundedRectanglePath(context, toPixels(35.5), toPixels(0.9), toPixels(7), toPixels(1.6), toPixels(0.3));
        context.fill();
        drawText(uiConfig.new_order || '', 39, 1.93, 0.55, 800, '#0A0A0A', 'center');
        const kpis = uiConfig.kpis || [];
        const kpiValueFormatters = [
            () => String(Math.round(128 * dataProgress) + (arrivedProgress > 0.5 ? 1 : 0)),
            () => String(Math.round(36 * dataProgress) + (arrivedProgress > 0.5 ? 1 : 0)),
            () => `${uiConfig.currency || ''} ${formatNumber(84.2 * dataProgress, 1)}k`,
            () => String(Math.round(4 * dataProgress)),
        ];
        kpis.forEach((kpi, kpiIndex) => {
            const unitX = 9.5 + kpiIndex * 8.8;
            context.fillStyle = '#161616';
            drawRoundedRectanglePath(context, toPixels(unitX), toPixels(4.4), toPixels(7.6), toPixels(5.4), toPixels(0.35));
            context.fill();
            drawText(kpi.label, unitX + 0.6, 5.6, 0.48, 600, '#8C8B86');
            if (dataProgress < 0.02) {
                drawSkeleton(unitX + 0.6, 6.6, 3.6, 1.5);
                drawSkeleton(unitX + 0.6, 8.55, 5, 0.45);
            } else {
                context.globalAlpha = Math.min(1, dataProgress * 2);
                drawText(kpiValueFormatters[kpiIndex](), unitX + 0.6, 7.95, 1.55, 850, kpiIndex === 3 ? '#FF4A1C' : '#F1F0EC');
                drawText(kpi.sub, unitX + 0.6, 9.05, 0.42, 550, kpiIndex === 3 ? '#FF4A1C' : '#6E6D69');
                context.globalAlpha = 1;
            }
        });
        context.fillStyle = '#141414';
        drawRoundedRectanglePath(context, toPixels(9.5), toPixels(11), toPixels(21.5), toPixels(15.3), toPixels(0.35));
        context.fill();
        drawText(uiConfig.chart_title || '', 10.2, 12.4, 0.55, 750, '#F1F0EC');
        CHART_BAR_VALUES.forEach((barValue, barIndex) => {
            const barProgress = easeOutCubic(clamp((dataProgress - barIndex * 0.03) / 0.6, 0, 1));
            const barHeight = Math.max(0.25, barValue * 0.85 * barProgress);
            const unitX = 10.6 + barIndex * 1.42;
            context.fillStyle = dataProgress < 0.02 ? '#202020' : barIndex === 13 ? '#FF4A1C' : 'rgba(241,240,236,.82)';
            context.fillRect(toPixels(unitX), toPixels(24.6 - barHeight), toPixels(0.82), toPixels(barHeight));
        });
        context.strokeStyle = '#2A2A2A';
        context.lineWidth = 2;
        context.beginPath();
        context.moveTo(toPixels(10.2), toPixels(24.6));
        context.lineTo(toPixels(30.3), toPixels(24.6));
        context.stroke();
        context.fillStyle = '#141414';
        drawRoundedRectanglePath(context, toPixels(32.3), toPixels(11), toPixels(11.2), toPixels(15.3), toPixels(0.35));
        context.fill();
        drawText(uiConfig.orders_title || '', 33, 12.4, 0.55, 750, '#F1F0EC');
        const rowTones = [['#2A2A2A', '#F1F0EC'], ['#1F1F1F', '#BDBCB7'], ['#1F1F1F', '#BDBCB7'], ['#1F1F1F', '#8C8B86'], ['#1F1F1F', '#8C8B86']];
        const orderRows = (uiConfig.rows || []).map((orderRow, rowIndex) => [orderRow.id, orderRow.who, orderRow.status, rowTones[rowIndex][0], rowTones[rowIndex][1]]);
        const rowShift = arrivedProgress * 2.55;
        context.save();
        context.beginPath();
        context.rect(toPixels(32.3), toPixels(13), toPixels(11.2), toPixels(13));
        context.clip();
        const drawOrderRow = (orderRow, unitY, alpha, isHighlighted) => {
            context.globalAlpha = alpha;
            if (isHighlighted) {
                context.fillStyle = 'rgba(255,74,28,.14)';
                context.fillRect(toPixels(32.6), toPixels(unitY - 1.2), toPixels(10.6), toPixels(2.2));
                context.fillStyle = '#FF4A1C';
                context.fillRect(toPixels(32.6), toPixels(unitY - 1.2), toPixels(0.14), toPixels(2.2));
            }
            drawText(orderRow[0], 33.1, unitY - 0.2, 0.46, 800, isHighlighted ? '#FF4A1C' : '#F1F0EC');
            drawText(orderRow[1], 33.1, unitY + 0.55, 0.42, 550, '#8C8B86');
            context.fillStyle = orderRow[3];
            drawRoundedRectanglePath(context, toPixels(38.6), toPixels(unitY - 0.75), toPixels(4.3), toPixels(1.05), toPixels(0.5));
            context.fill();
            drawText(orderRow[2], 40.75, unitY - 0.02, 0.36, 700, orderRow[4], 'center');
            context.globalAlpha = 1;
        };
        if (arrivedProgress > 0 && uiConfig.incoming) {
            drawOrderRow([uiConfig.incoming.id, uiConfig.incoming.who, uiConfig.incoming.status, 'rgba(255,74,28,.25)', '#FF4A1C'], 14.6 - (1 - arrivedProgress) * 2.55, arrivedProgress, true);
        }
        orderRows.forEach((orderRow, rowIndex) => {
            const unitY = 14.6 + rowIndex * 2.55 + rowShift;
            const rowAlpha = clamp((dataProgress - rowIndex * 0.08) / 0.4, 0, 1);
            if (rowAlpha <= 0) {
                drawSkeleton(33.1, unitY - 0.75, 4 + (rowIndex % 2), 0.45);
                drawSkeleton(33.1, unitY + 0.15, 2.8, 0.35);
                drawSkeleton(38.6, unitY - 0.75, 4.3, 1.05);
            } else {
                drawOrderRow(orderRow, unitY, rowAlpha, false);
            }
        });
        context.restore();
        context.strokeStyle = '#2E2E2E';
        context.lineWidth = 3;
        context.strokeRect(1.5, 1.5, canvas.width - 3, canvas.height - 3);
        context.strokeStyle = '#1F1F1F';
        context.lineWidth = 2;
        context.beginPath();
        context.moveTo(toPixels(8), 0);
        context.lineTo(toPixels(8), canvas.height);
        context.stroke();
    }

    function drawToast(canvas) {
        const context = canvas.getContext('2d');
        const canvasWidth = canvas.width;
        const canvasHeight = canvas.height;
        context.clearRect(0, 0, canvasWidth, canvasHeight);
        context.fillStyle = '#1B1B1B';
        drawRoundedRectanglePath(context, 2, 2, canvasWidth - 4, canvasHeight - 4, 22);
        context.fill();
        context.strokeStyle = '#333';
        context.lineWidth = 2;
        context.stroke();
        context.fillStyle = '#FF4A1C';
        context.beginPath();
        context.arc(70, canvasHeight / 2, 30, 0, Math.PI * 2);
        context.fill();
        context.strokeStyle = '#0A0A0A';
        context.lineWidth = 7;
        context.lineCap = 'round';
        context.beginPath();
        context.moveTo(56, canvasHeight / 2);
        context.lineTo(66, canvasHeight / 2 + 11);
        context.lineTo(86, canvasHeight / 2 - 12);
        context.stroke();
        context.fillStyle = '#F1F0EC';
        context.font = `800 38px ${FONT}`;
        context.textAlign = 'left';
        context.fillText(config.toast?.title || '', 124, canvasHeight / 2 - 8);
        context.fillStyle = '#8C8B86';
        context.font = `550 28px ${FONT}`;
        context.fillText(config.toast?.sub || '', 124, canvasHeight / 2 + 34);
    }

    function createGlyphAtlas() {
        const atlasCanvas = document.createElement('canvas');
        atlasCanvas.width = 512;
        atlasCanvas.height = 64;
        const context = atlasCanvas.getContext('2d');
        context.fillStyle = '#fff';
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        ['{', '}', '<', '/>', ';', '=', '(', ')'].forEach((glyph, glyphIndex) => {
            context.font = `700 ${glyph.length > 1 ? 34 : 46}px ${FONT}`;
            context.fillText(glyph, glyphIndex * 64 + 32, 34);
        });
        return atlasCanvas;
    }

    function initThreeScene(THREE) {
        const glCanvas = queryOne('#home-webgl-canvas', element);
        let renderer;
        try {
            renderer = new THREE.WebGLRenderer({ canvas: glCanvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
        } catch (error) {
            return null;
        }
        THREE.ColorManagement.enabled = false;
        renderer.outputColorSpace = THREE.LinearSRGBColorSpace;
        renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
        renderer.setSize(viewportWidth, viewportHeight, false);
        renderer.localClippingEnabled = true;
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(35, viewportWidth / viewportHeight, 0.1, 100);
        camera.position.set(0, 0, 9);
        const group = new THREE.Group();
        scene.add(group);
        const maxAnisotropy = renderer.capabilities.getMaxAnisotropy();
        const createCanvasTexture = (sourceCanvas) => {
            const texture = new THREE.CanvasTexture(sourceCanvas);
            texture.anisotropy = maxAnisotropy;
            texture.minFilter = THREE.LinearFilter;
            texture.generateMipmaps = false;
            return texture;
        };

        const pickClusterPoint = () => {
            const moduleRectangle = MODULE_RECTANGLES[Math.floor(Math.random() * MODULE_RECTANGLES.length)];
            return convertUnitsToWorld(moduleRectangle[0] + 0.5 + Math.random() * (moduleRectangle[2] - 1), moduleRectangle[1] + 0.5 + Math.random() * (moduleRectangle[3] - 1));
        };
        const architectureSegments = [];
        getRectangleSegments(CORE_RECTANGLE).forEach((segment) => architectureSegments.push(segment));
        MODULE_RECTANGLES.forEach((moduleRectangle) => getRectangleSegments(moduleRectangle).forEach((segment) => architectureSegments.push(segment)));
        MODULE_CONNECTIONS.forEach((connection) => architectureSegments.push(connection.slice(0, 4)));
        const createSegmentSampler = (segments) => {
            const segmentLengths = segments.map((segment) => Math.hypot(segment[2] - segment[0], segment[3] - segment[1]));
            const totalLength = segmentLengths.reduce((sum, length) => sum + length, 0);
            return () => {
                let remainingDistance = Math.random() * totalLength;
                let segmentIndex = 0;
                while (segmentIndex < segments.length - 1 && remainingDistance > segmentLengths[segmentIndex]) {
                    remainingDistance -= segmentLengths[segmentIndex];
                    segmentIndex++;
                }
                const segment = segments[segmentIndex];
                const fraction = segmentLengths[segmentIndex] ? remainingDistance / segmentLengths[segmentIndex] : 0;
                return convertUnitsToWorld(segment[0] + (segment[2] - segment[0]) * fraction, segment[1] + (segment[3] - segment[1]) * fraction);
            };
        };
        const sampleArchitecturePoint = createSegmentSampler(architectureSegments);

        const PARTICLE_COUNT = viewportWidth < 900 ? 2600 : 6000;
        const positions = new Float32Array(PARTICLE_COUNT * 3);
        const waveAttributes = new Float32Array(PARTICLE_COUNT * 4);
        const clusterTargets = new Float32Array(PARTICLE_COUNT * 3);
        const architectureTargets = new Float32Array(PARTICLE_COUNT * 3);
        const seeds = new Float32Array(PARTICLE_COUNT);
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            const radius = 2.4 + Math.pow(Math.random(), 0.7) * 6.2;
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.acos(2 * Math.random() - 1);
            positions[i * 3] = radius * Math.sin(phi) * Math.cos(theta) * 1.5;
            positions[i * 3 + 1] = radius * Math.cos(phi) * 0.85;
            positions[i * 3 + 2] = radius * Math.sin(phi) * Math.sin(theta) * 0.9;
            const particlesPerLine = Math.floor(PARTICLE_COUNT / 30);
            waveAttributes[i * 4] = ((i % particlesPerLine) / particlesPerLine) * 18 - 9 + (Math.random() - 0.5) * 0.015;
            waveAttributes[i * 4 + 1] = Math.floor(i / particlesPerLine) % 30;
            waveAttributes[i * 4 + 2] = (Math.floor(i / particlesPerLine) % 7) / 7;
            waveAttributes[i * 4 + 3] = -1;
            const clusterPoint = pickClusterPoint();
            clusterTargets[i * 3] = clusterPoint[0] + (Math.random() - 0.5) * 0.01;
            clusterTargets[i * 3 + 1] = clusterPoint[1] + (Math.random() - 0.5) * 0.01;
            clusterTargets[i * 3 + 2] = 0.02;
            const architecturePoint = sampleArchitecturePoint();
            architectureTargets[i * 3] = architecturePoint[0];
            architectureTargets[i * 3 + 1] = architecturePoint[1];
            architectureTargets[i * 3 + 2] = 0.03;
            seeds[i] = Math.random();
        }
        const particleGeometry = new THREE.BufferGeometry();
        particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        particleGeometry.setAttribute('aW', new THREE.BufferAttribute(waveAttributes, 4));
        particleGeometry.setAttribute('aR', new THREE.BufferAttribute(clusterTargets, 3));
        particleGeometry.setAttribute('aQ', new THREE.BufferAttribute(architectureTargets, 3));
        particleGeometry.setAttribute('aSeed', new THREE.BufferAttribute(seeds, 1));
        const atlasTexture = new THREE.CanvasTexture(createGlyphAtlas());
        const particleMaterial = new THREE.ShaderMaterial({
            transparent: true,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
            uniforms: {
                uTime: { value: 0 },
                uPre: { value: 0 },
                uT1: { value: 0 },
                uT2: { value: 0 },
                uFade: { value: 1 },
                uIntro: { value: 0 },
                uGlobal: { value: 1 },
                uMouse: { value: new THREE.Vector3(99, 99, 0) },
                uRepel: { value: 1 },
                uPix: { value: renderer.getPixelRatio() },
                uAcc: { value: new THREE.Color(0xff4a1c) },
                uAtlas: { value: atlasTexture },
            },
            vertexShader: `
        uniform float uTime, uPre, uT1, uT2, uFade, uIntro, uRepel, uPix, uGlobal; uniform vec3 uMouse;
        attribute vec4 aW; attribute vec3 aR; attribute vec3 aQ; attribute float aSeed;
        varying float vA; varying float vAcc; varying float vG; varying float vGi;
        void main() {
          float L = aW.y;
          float x = mod(aW.x + uTime * (0.16 + 0.12 * aW.z) + 9.0, 18.0) - 9.0;
          float amp = 0.34 + 0.2 * sin(L * 0.37);
          float y = -2.55 + L * 0.175 + amp * sin(x * 0.48 + uTime * 0.42 + L * 0.2) + 0.1 * sin(x * 1.25 - uTime * 0.3 + L);
          vec3 w = vec3(x, y, -0.5 - mod(L, 3.0) * 0.4);
          vec3 c = position;
          float a = uTime * (0.025 + 0.05 * aSeed); float ca = cos(a), sa = sin(a);
          c.xz = mat2(ca, -sa, sa, ca) * c.xz;
          c.y += sin(uTime * 0.45 + aSeed * 40.0) * 0.16;
          float h = smoothstep(aSeed * 0.3, 0.7 + aSeed * 0.3, uPre);
          vec3 p = mix(w, c, h);
          float t1 = smoothstep(aSeed * 0.45, 0.55 + aSeed * 0.45, uT1);
          p = mix(p, aR, t1);
          float t2 = smoothstep(aSeed * 0.4, 0.6 + aSeed * 0.4, uT2);
          p = mix(p, aQ, t2);
          vec4 wp = modelMatrix * vec4(p, 1.0);
          vec2 d = wp.xy - uMouse.xy; float dist = length(d);
          float hot = exp(-dist * dist * 1.6);
          wp.y += hot * 0.28 * (1.0 - h);
          float f = smoothstep(1.3, 0.0, dist) * uRepel * h;
          wp.xy += normalize(d + 0.0001) * f * 0.45; wp.z += f * 0.25;
          vec4 mv = viewMatrix * wp;
          gl_Position = projectionMatrix * mv;
          float glyph = step(0.0, aW.w) * (1.0 - t1);
          float dotSize = mix(mix(2.0 + 0.9 * aSeed, 1.2 + 2.4 * aSeed, h), 1.6 + 0.8 * aSeed, t1);
          gl_PointSize = mix(dotSize * (9.0 / -mv.z), 12.0 + 6.0 * aSeed, glyph) * uPix * (1.0 + hot * 0.6 * (1.0 - h));
          float waveA = (0.5 + 0.35 * aSeed) * (0.6 + 0.4 * sin(aW.x * 0.35 + L * 0.5 + uTime * 0.6)) * uIntro + hot * 0.7;
          float chaosA = (0.25 + 0.75 * aSeed) * (0.6 + f * 0.8);
          vA = uFade * uGlobal * mix(mix(waveA, chaosA, h), 0.85, t1);
          vAcc = (step(0.95, aSeed) + hot * (1.0 - h) * step(0.55, aSeed)) * (1.0 - t1) + f * 0.5;
          vG = glyph; vGi = aW.w;
        }`,
            fragmentShader: `
        uniform vec3 uAcc; uniform sampler2D uAtlas;
        varying float vA; varying float vAcc; varying float vG; varying float vGi;
        void main() {
          vec2 pc = gl_PointCoord;
          float dotA = smoothstep(0.5, 0.0, length(pc - 0.5));
          float glyA = texture2D(uAtlas, vec2((pc.x + max(vGi, 0.0)) / 8.0, 1.0 - pc.y)).a;
          float s = mix(dotA, glyA, step(0.5, vG));
          if (s < 0.02) discard;
          vec3 col = mix(vec3(0.95, 0.94, 0.92), uAcc, clamp(vAcc, 0.0, 1.0));
          gl_FragColor = vec4(col, vA * s);
        }`,
        });
        group.add(new THREE.Points(particleGeometry, particleMaterial));

        const fragmentGroup = new THREE.Group();
        scene.add(fragmentGroup);
        const fragmentMaterial = new THREE.LineBasicMaterial({ color: 0xf1f0ec, transparent: true, opacity: 0 });
        const shapeBuilders = {
            sheet() {
                const vertices = [];
                const shapeWidth = 1.6;
                const shapeHeight = 1.1;
                for (let i = 0; i <= 6; i++) {
                    vertices.push(-shapeWidth / 2 + (i * shapeWidth) / 6, -shapeHeight / 2, -shapeWidth / 2 + (i * shapeWidth) / 6, shapeHeight / 2);
                }
                for (let j = 0; j <= 5; j++) {
                    vertices.push(-shapeWidth / 2, -shapeHeight / 2 + (j * shapeHeight) / 5, shapeWidth / 2, -shapeHeight / 2 + (j * shapeHeight) / 5);
                }
                return vertices;
            },
            mail() {
                const shapeWidth = 1.4;
                const shapeHeight = 0.9;
                return [-shapeWidth / 2, -shapeHeight / 2, shapeWidth / 2, -shapeHeight / 2, shapeWidth / 2, -shapeHeight / 2, shapeWidth / 2, shapeHeight / 2, shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, -shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2, 0, 0, 0, 0, shapeWidth / 2, shapeHeight / 2];
            },
            doc() {
                const shapeWidth = 1.0;
                const shapeHeight = 1.3;
                const vertices = [-shapeWidth / 2, -shapeHeight / 2, shapeWidth / 2, -shapeHeight / 2, shapeWidth / 2, -shapeHeight / 2, shapeWidth / 2, shapeHeight / 2, shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, -shapeHeight / 2];
                for (let i = 0; i < 7; i++) {
                    const lineY = shapeHeight / 2 - 0.22 - i * 0.15;
                    vertices.push(-shapeWidth / 2 + 0.12, lineY, shapeWidth / 2 - 0.12 - (i % 3) * 0.15, lineY);
                }
                return vertices;
            },
            note() {
                const shapeWidth = 0.8;
                const vertices = [-shapeWidth / 2, -shapeWidth / 2, shapeWidth / 2, -shapeWidth / 2, shapeWidth / 2, -shapeWidth / 2, shapeWidth / 2, shapeWidth / 2, shapeWidth / 2, shapeWidth / 2, -shapeWidth / 2, shapeWidth / 2, -shapeWidth / 2, shapeWidth / 2, -shapeWidth / 2, -shapeWidth / 2];
                for (let i = 0; i < 3; i++) {
                    vertices.push(-0.28, 0.18 - i * 0.16, 0.24 - i * 0.08, 0.18 - i * 0.16);
                }
                return vertices;
            },
            cal() {
                const vertices = [];
                const shapeWidth = 1.2;
                const shapeHeight = 1.0;
                for (let i = 0; i <= 7; i++) {
                    vertices.push(-shapeWidth / 2 + (i * shapeWidth) / 7, -shapeHeight / 2, -shapeWidth / 2 + (i * shapeWidth) / 7, shapeHeight / 2 - 0.2);
                }
                for (let j = 0; j <= 5; j++) {
                    vertices.push(-shapeWidth / 2, -shapeHeight / 2 + (j * (shapeHeight - 0.2)) / 5, shapeWidth / 2, -shapeHeight / 2 + (j * (shapeHeight - 0.2)) / 5);
                }
                vertices.push(-shapeWidth / 2, shapeHeight / 2, shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2, -shapeWidth / 2, shapeHeight / 2 - 0.2, shapeWidth / 2, shapeHeight / 2, shapeWidth / 2, shapeHeight / 2 - 0.2);
                return vertices;
            },
        };
        const fragmentPlan = [['sheet', -3.6, 1.2, 0.2, 3, 1.6], ['mail', 3.4, 1.4, -0.4, 5, 1.4], ['doc', -3.1, -1.4, 0.3, 7, 1.0], ['note', 2.9, -1.3, 0.5, 2, 0.8], ['cal', 0.3, 2.1, -0.8, 6, 1.2], ['sheet', 1.0, -2.2, -0.2, 0, 1.6], ['doc', -1.4, 2.4, -1.0, 4, 1.0], ['note', 4.2, -0.2, 0.2, 1, 0.8]];
        const fragmentGeometries = [];
        const fragments = fragmentPlan.map((planEntry, planIndex) => {
            const vertexArray = shapeBuilders[planEntry[0]]().reduce((accumulator, value, valueIndex) => {
                accumulator.push(value);
                if (valueIndex % 2 === 1) {
                    accumulator.push(0);
                }
                return accumulator;
            }, []);
            const geometry = new THREE.BufferGeometry();
            geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertexArray, 3));
            fragmentGeometries.push(geometry);
            const fragmentMesh = new THREE.LineSegments(geometry, fragmentMaterial);
            fragmentMesh.userData = {
                home: new THREE.Vector3(planEntry[1], planEntry[2], planEntry[3]),
                spin: new THREE.Vector3(Math.random() - 0.5, Math.random() - 0.5, Math.random() - 0.5).multiplyScalar(0.5),
                phase: Math.random() * 6,
                rotation: new THREE.Euler(Math.random() * 2, Math.random() * 2, Math.random()),
                moduleIndex: planEntry[4],
                width: planEntry[5],
                index: planIndex,
            };
            fragmentGroup.add(fragmentMesh);
            return fragmentMesh;
        });

        const archCanvas = document.createElement('canvas');
        archCanvas.width = 1760;
        archCanvas.height = 1100;
        const archTexture = createCanvasTexture(archCanvas);
        const archMaterial = new THREE.MeshBasicMaterial({ map: archTexture, transparent: true, opacity: 0, depthWrite: false });
        const archMesh = new THREE.Mesh(new THREE.PlaneGeometry(SCREEN_WORLD_WIDTH, SCREEN_WORLD_HEIGHT), archMaterial);
        archMesh.position.z = 0.04;
        archMesh.renderOrder = 3;
        group.add(archMesh);

        const wireVertices = [];
        UI_WIREFRAME_RECTANGLES.forEach((rectangle) =>
            getRectangleSegments(rectangle).forEach((segment) => {
                const startPoint = convertUnitsToWorld(segment[0], segment[1]);
                const endPoint = convertUnitsToWorld(segment[2], segment[3]);
                wireVertices.push(startPoint[0], startPoint[1], 0.05, endPoint[0], endPoint[1], 0.05);
            }),
        );
        [5.2, 7.25, 9.3, 11.35, 13.4, 15.45].forEach((unitY) => {
            const startPoint = convertUnitsToWorld(1.4, unitY - 0.3);
            const endPoint = convertUnitsToWorld(6.4, unitY - 0.3);
            wireVertices.push(startPoint[0], startPoint[1], 0.05, endPoint[0], endPoint[1], 0.05);
        });
        for (let i = 0; i < 14; i++) {
            const unitX = 10.6 + i * 1.42;
            const barHeight = CHART_BAR_VALUES[i] * 0.85;
            getRectangleSegments([unitX, 24.6 - barHeight, 0.82, barHeight]).forEach((segment) => {
                const startPoint = convertUnitsToWorld(segment[0], segment[1]);
                const endPoint = convertUnitsToWorld(segment[2], segment[3]);
                wireVertices.push(startPoint[0], startPoint[1], 0.05, endPoint[0], endPoint[1], 0.05);
            });
        }
        const wireGeometry = new THREE.BufferGeometry();
        wireGeometry.setAttribute('position', new THREE.Float32BufferAttribute(wireVertices, 3));
        const wireClipPlane = new THREE.Plane(new THREE.Vector3(0, 1, 0), 50);
        const wireMaterial = new THREE.LineDashedMaterial({ color: 0xf1f0ec, transparent: true, opacity: 0, dashSize: 0.0001, gapSize: 50, depthWrite: false, clippingPlanes: [wireClipPlane] });
        const wireLines = new THREE.LineSegments(wireGeometry, wireMaterial);
        const lineDistances = new Float32Array(wireVertices.length / 3);
        for (let i = 0; i < lineDistances.length; i += 2) {
            lineDistances[i] = 0;
            lineDistances[i + 1] = Math.hypot(wireVertices[(i + 1) * 3] - wireVertices[i * 3], wireVertices[(i + 1) * 3 + 1] - wireVertices[i * 3 + 1]);
        }
        wireGeometry.setAttribute('lineDistance', new THREE.BufferAttribute(lineDistances, 1));
        wireLines.renderOrder = 4;
        group.add(wireLines);

        const uiCanvas = document.createElement('canvas');
        uiCanvas.width = 2200;
        uiCanvas.height = 1375;
        const uiTexture = createCanvasTexture(uiCanvas);
        const uiClipPlane = new THREE.Plane(new THREE.Vector3(0, -1, 0), -10);
        const uiMaterial = new THREE.MeshBasicMaterial({ map: uiTexture, transparent: true, opacity: 1, clippingPlanes: [uiClipPlane], depthWrite: false });
        const uiMesh = new THREE.Mesh(new THREE.PlaneGeometry(SCREEN_WORLD_WIDTH, SCREEN_WORLD_HEIGHT), uiMaterial);
        uiMesh.position.z = 0;
        uiMesh.renderOrder = 2;
        group.add(uiMesh);
        const scanLineMesh = new THREE.Mesh(new THREE.PlaneGeometry(SCREEN_WORLD_WIDTH * 1.06, 0.014), new THREE.MeshBasicMaterial({ color: 0xff4a1c, transparent: true, opacity: 0, depthWrite: false }));
        scanLineMesh.renderOrder = 10;
        group.add(scanLineMesh);

        const toastCanvas = document.createElement('canvas');
        toastCanvas.width = 820;
        toastCanvas.height = 160;
        drawToast(toastCanvas);
        const toastMaterial = new THREE.MeshBasicMaterial({ map: createCanvasTexture(toastCanvas), transparent: true, opacity: 0, depthWrite: false });
        const toastMesh = new THREE.Mesh(new THREE.PlaneGeometry(1.95, (1.95 * 160) / 820), toastMaterial);
        toastMesh.renderOrder = 12;
        group.add(toastMesh);

        const lastRedrawProgress = { arch: -1, ui: -1 };
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => {
                lastRedrawProgress.arch = -1;
                lastRedrawProgress.ui = -1;
                drawToast(toastCanvas);
                toastMaterial.map.needsUpdate = true;
            });
        }

        const dispose = () => {
            scene.traverse((sceneObject) => {
                sceneObject.geometry?.dispose();
                const materials = Array.isArray(sceneObject.material) ? sceneObject.material : sceneObject.material ? [sceneObject.material] : [];
                materials.forEach((material) => {
                    material.map?.dispose();
                    material.dispose();
                });
            });
            atlasTexture.dispose();
            fragmentGeometries.forEach((geometry) => geometry.dispose());
            renderer.dispose();
            renderer.forceContextLoss();
        };

        return {
            THREE,
            renderer,
            scene,
            camera,
            group,
            particleMaterial,
            fragments,
            fragmentMaterial,
            wireClipPlane,
            temporaryVector: new THREE.Vector3(),
            temporaryQuaternion: new THREE.Quaternion(),
            temporaryTargetQuaternion: new THREE.Quaternion(),
            archCanvas,
            archTexture,
            archMaterial,
            wireMaterial,
            uiCanvas,
            uiTexture,
            uiMaterial,
            uiClipPlane,
            scanLineMesh,
            toastMesh,
            toastMaterial,
            lastRedrawProgress,
            dispose,
            currentAnchor: { x: 0, y: 0, scale: 1, visibility: 1 },
            tiltRotation: { x: 0, y: 0 },
            pointerWorldPosition: new THREE.Vector3(),
            startedAt: performance.now(),
            aliveSince: null,
        };
    }

    function renderThreeScene(timestamp, deltaSeconds) {
        const THREE = threeState.THREE;
        const elapsedSeconds = (timestamp - threeState.startedAt) / 1000;
        const rotationEase = 1 - Math.pow(0.0008, deltaSeconds);
        const viewportCenter = scrollPosition + viewportHeight * 0.5;
        const normalizedPointerX = (pointer.smoothX / viewportWidth) * 2 - 1;
        const normalizedPointerY = -(pointer.smoothY / viewportHeight) * 2 + 1;
        const storySection = sectionBounds.story;
        const storyProgress = storySection ? clamp((scrollPosition - storySection.top) / Math.max(1, storySection.height - viewportHeight), 0, 1) * 6 : 6;
        const preProgress = storySection ? clamp((scrollPosition - storySection.top + viewportHeight * 0.7) / (viewportHeight * 0.75), 0, 1) : 1;

        const lastSectionKey = Object.keys(sectionBounds).reduce((bestKey, candidateKey) => (sectionBounds[candidateKey].top > (sectionBounds[bestKey]?.top ?? -1) ? candidateKey : bestKey), 'hero');
        const lastSection = sectionBounds[lastSectionKey];
        let anchor = lastSection && viewportCenter >= lastSection.top + lastSection.height ? sectionAnchors.contact : sectionAnchors.hero;
        for (const sectionKey in sectionBounds) {
            const section = sectionBounds[sectionKey];
            if (viewportCenter >= section.top && viewportCenter < section.top + section.height && sectionAnchors[sectionKey]) {
                anchor = sectionAnchors[sectionKey];
            }
        }
        const isNarrow = viewportWidth < 900;
        const anchorEase = 1 - Math.pow(0.02, deltaSeconds);
        threeState.currentAnchor.x = linearInterpolate(threeState.currentAnchor.x, anchor.x, anchorEase);
        threeState.currentAnchor.y = linearInterpolate(threeState.currentAnchor.y, anchor.y, anchorEase);
        threeState.currentAnchor.scale = linearInterpolate(threeState.currentAnchor.scale, isNarrow ? anchor.scale * 0.72 : anchor.scale, anchorEase);
        threeState.currentAnchor.visibility = linearInterpolate(threeState.currentAnchor.visibility, anchor.visibility, 1 - Math.pow(0.004, deltaSeconds));
        const globalVisibility = threeState.currentAnchor.visibility < 0.01 ? 0 : threeState.currentAnchor.visibility;
        threeState.group.position.set(threeState.currentAnchor.x, threeState.currentAnchor.y, 0);
        threeState.group.scale.setScalar(threeState.currentAnchor.scale);

        const pointerRayDirection = new THREE.Vector3(normalizedPointerX, normalizedPointerY, 0.5).unproject(threeState.camera).sub(threeState.camera.position).normalize();
        threeState.pointerWorldPosition.copy(threeState.camera.position).add(pointerRayDirection.multiplyScalar(-threeState.camera.position.z / pointerRayDirection.z));
        if (!pointer.hasMoved || !hasFinePointer) {
            threeState.pointerWorldPosition.set(99, 99, 0);
        }

        const toClusterProgress = smoothStep(0.7, 1.8, storyProgress);
        const toArchitectureProgress = smoothStep(1.8, 2.7, storyProgress);
        const architectureDrawProgress = smoothStep(1.9, 2.95, storyProgress);
        const architectureFadeOutProgress = smoothStep(2.95, 3.4, storyProgress);
        const wireFadeInProgress = smoothStep(3.05, 3.35, storyProgress);
        const wireDrawProgress = smoothStep(3.05, 3.9, storyProgress);
        const buildProgress = smoothStep(4.0, 4.9, storyProgress);
        if (storyProgress >= 5.05 && globalVisibility > 0.5) {
            if (threeState.aliveSince === null) {
                threeState.aliveSince = elapsedSeconds;
            }
        } else {
            threeState.aliveSince = null;
        }
        const aliveProgress = threeState.aliveSince === null ? 0 : clamp((elapsedSeconds - threeState.aliveSince) / 3.6, 0, 1);

        const tiltStrength = 0.35 + 0.65 * smoothStep(1.5, 5, storyProgress);
        threeState.tiltRotation.x = linearInterpolate(threeState.tiltRotation.x, (hasFinePointer ? -normalizedPointerY * 0.2 * tiltStrength : 0) + (prefersReducedMotion ? 0 : Math.sin(elapsedSeconds * 0.35) * 0.03 * tiltStrength), rotationEase);
        threeState.tiltRotation.y = linearInterpolate(threeState.tiltRotation.y, (hasFinePointer ? normalizedPointerX * 0.3 * tiltStrength : 0) + (prefersReducedMotion ? 0 : Math.sin(elapsedSeconds * 0.27) * 0.05 * tiltStrength), rotationEase);
        threeState.group.rotation.set(threeState.tiltRotation.x, threeState.tiltRotation.y, 0);

        const uniforms = threeState.particleMaterial.uniforms;
        uniforms.uTime.value = prefersReducedMotion ? 0 : elapsedSeconds;
        uniforms.uIntro.value = prefersReducedMotion ? 1 : smoothStep(0, 2.2, elapsedSeconds);
        uniforms.uPre.value = preProgress;
        uniforms.uT1.value = toClusterProgress;
        uniforms.uT2.value = toArchitectureProgress;
        uniforms.uFade.value = 1 - smoothStep(2.85, 3.3, storyProgress);
        uniforms.uRepel.value = (1 - toClusterProgress * 0.85) * (prefersReducedMotion ? 0 : 1);
        uniforms.uGlobal.value = globalVisibility;
        uniforms.uMouse.value.copy(threeState.pointerWorldPosition);

        threeState.group.updateMatrixWorld();
        const fragmentOpacity = preProgress * (1 - smoothStep(2.1, 2.7, storyProgress)) * globalVisibility;
        threeState.fragmentMaterial.opacity = fragmentOpacity * linearInterpolate(0.5, 0.75, smoothStep(0.7, 1.9, storyProgress));
        threeState.fragments.forEach((fragmentMesh) => {
            const fragmentData = fragmentMesh.userData;
            const moduleRectangle = MODULE_RECTANGLES[fragmentData.moduleIndex];
            const moduleCenter = convertUnitsToWorld(moduleRectangle[0] + moduleRectangle[2] / 2, moduleRectangle[1] + moduleRectangle[3] / 2);
            threeState.temporaryVector.set(moduleCenter[0], moduleCenter[1], 0.06);
            threeState.group.localToWorld(threeState.temporaryVector);
            const fragmentProgress = smoothStep(0.75 + fragmentData.index * 0.09, 1.45 + fragmentData.index * 0.09, storyProgress);
            const floatOffsetX = Math.sin(elapsedSeconds * 0.4 + fragmentData.phase) * 0.12 * (1 - fragmentProgress);
            const floatOffsetY = Math.cos(elapsedSeconds * 0.33 + fragmentData.phase) * 0.1 * (1 - fragmentProgress);
            fragmentMesh.position.set(linearInterpolate(fragmentData.home.x + floatOffsetX, threeState.temporaryVector.x, fragmentProgress), linearInterpolate(fragmentData.home.y + floatOffsetY, threeState.temporaryVector.y, fragmentProgress), linearInterpolate(fragmentData.home.z, threeState.temporaryVector.z, fragmentProgress));
            if (!prefersReducedMotion) {
                fragmentData.rotation.x += fragmentData.spin.x * deltaSeconds * (1 - fragmentProgress);
                fragmentData.rotation.y += fragmentData.spin.y * deltaSeconds * (1 - fragmentProgress);
                fragmentData.rotation.z += fragmentData.spin.z * deltaSeconds * 0.5 * (1 - fragmentProgress);
            }
            threeState.temporaryQuaternion.setFromEuler(fragmentData.rotation);
            threeState.temporaryTargetQuaternion.copy(threeState.group.quaternion);
            fragmentMesh.quaternion.copy(threeState.temporaryQuaternion).slerp(threeState.temporaryTargetQuaternion, fragmentProgress);
            const targetScale = (moduleRectangle[2] * 0.1 * threeState.currentAnchor.scale * 0.62) / fragmentData.width;
            fragmentMesh.scale.setScalar(Math.max(0.001, linearInterpolate(isNarrow ? 0.6 : 1, targetScale, fragmentProgress)));
            fragmentMesh.visible = fragmentOpacity > 0.002;
        });

        const architectureOpacity = smoothStep(1.9, 2.3, storyProgress) * (1 - architectureFadeOutProgress) * globalVisibility * 0.85;
        threeState.archMaterial.opacity = architectureOpacity;
        if (architectureOpacity > 0.001) {
            const quantizedArchitectureProgress = Math.round(architectureDrawProgress * 200) / 200;
            if (quantizedArchitectureProgress !== threeState.lastRedrawProgress.arch) {
                threeState.lastRedrawProgress.arch = quantizedArchitectureProgress;
                drawArchitecture(threeState.archCanvas, quantizedArchitectureProgress);
                threeState.archTexture.needsUpdate = true;
            }
        }

        threeState.wireMaterial.opacity = wireFadeInProgress * 0.85 * globalVisibility;
        threeState.wireMaterial.dashSize = 0.0001 + wireDrawProgress * 4.6;

        const localY = -SCREEN_WORLD_HEIGHT / 2 + (SCREEN_WORLD_HEIGHT + 0.04) * buildProgress;
        threeState.uiClipPlane.constant = threeState.currentAnchor.y + localY * threeState.currentAnchor.scale + (buildProgress >= 1 ? 50 : 0);
        threeState.wireClipPlane.constant = buildProgress >= 1 ? -50 : -(threeState.currentAnchor.y + localY * threeState.currentAnchor.scale) + 0.002;
        threeState.uiMaterial.visible = buildProgress > 0.001 && globalVisibility > 0.001;
        threeState.uiMaterial.opacity = globalVisibility;
        const quantizedAliveProgress = Math.round(aliveProgress * 120) / 120;
        if (threeState.uiMaterial.visible && quantizedAliveProgress !== threeState.lastRedrawProgress.ui) {
            threeState.lastRedrawProgress.ui = quantizedAliveProgress;
            drawUserInterface(threeState.uiCanvas, quantizedAliveProgress);
            threeState.uiTexture.needsUpdate = true;
        }
        threeState.scanLineMesh.position.set(0, localY, 0.12);
        threeState.scanLineMesh.material.opacity = buildProgress > 0.001 && buildProgress < 0.999 ? globalVisibility : 0;
        const toastProgress = smoothStep(0.62, 0.78, aliveProgress);
        threeState.toastMaterial.opacity = toastProgress * globalVisibility;
        threeState.toastMesh.position.set(SCREEN_WORLD_WIDTH / 2 - 0.7, -SCREEN_WORLD_HEIGHT / 2 + 0.2 - (1 - toastProgress) * 0.25, 0.45);

        if (globalVisibility > 0) {
            threeState.renderer.render(threeState.scene, threeState.camera);
        } else {
            threeState.renderer.clear();
        }
    }

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        scrollPosition = scrollY;

        if (!prefersReducedMotion) {
            marqueeOffset += deltaSeconds * 38;
            marqueeRows.forEach((marqueeRow) => {
                const halfWidth = marqueeRow.element.scrollWidth / 2 || 1;
                const offsetX = (marqueeOffset + scrollPosition * 0.45) % halfWidth;
                marqueeRow.element.style.transform = `translate3d(${marqueeRow.direction > 0 ? -offsetX : offsetX - halfWidth}px, 0, 0)`;
            });
        }

        const storySection = sectionBounds.story;
        const storyProgress = storySection ? clamp((scrollPosition - storySection.top) / Math.max(1, storySection.height - viewportHeight), 0, 1) * 6 : 6;
        const chapterIndex = Math.min(5, Math.floor(storyProgress >= 6 ? 5.99 : storyProgress));
        if (chapterIndex !== lastChapterIndex) {
            lastChapterIndex = chapterIndex;
            giantLetters.forEach((giantLetter, index) => giantLetter.classList.toggle('is-active', index === chapterIndex));
            chapterElements.forEach((chapterElement, index) => chapterElement.classList.toggle('is-active', index === chapterIndex));
            indexButtons.forEach((indexButton, index) => indexButton.classList.toggle('is-active', index === chapterIndex));
        }

        const worksSection = sectionBounds.works;
        if (worksSection && workItems.length) {
            if (isHorizontalLayout) {
                const worksProgress = clamp((scrollPosition - worksSection.top) / Math.max(1, worksSection.height - viewportHeight), 0, 0.999);
                activateWork(Math.floor(worksProgress * workItems.length));
            } else if (activeWorkIndex < 0) {
                activateWork(0);
            }
            const targetProgress = isScreenHovered ? screenTargetProgress : timestamp - screenWorkChangedAt > 250 ? 1 : 0;
            screenCurrentProgress = prefersReducedMotion ? targetProgress : linearInterpolate(screenCurrentProgress, targetProgress, 1 - Math.pow(isScreenHovered ? 0.0005 : 0.06, deltaSeconds));
            screenElement?.style.setProperty('--screen-scan-progress', screenCurrentProgress.toFixed(4));
        }

        if (portraitElement && !prefersReducedMotion) {
            const portraitTop = portraitElement.getBoundingClientRect().top;
            portraitElement.style.setProperty('--portrait-reveal-progress', clamp((viewportHeight - portraitTop) / (viewportHeight * 0.55), 0, 1).toFixed(3));
        }

        if (threeState) {
            renderThreeScene(timestamp, deltaSeconds);
        }
    });

    import('three')
        .then((THREE) => {
            if (isDisposed) {
                return;
            }
            threeState = initThreeScene(THREE);
            if (!threeState) {
                documentRoot.classList.add('is-webgl-unavailable');
            }
        })
        .catch(() => documentRoot.classList.add('is-webgl-unavailable'));

    return () => {
        isDisposed = true;
        cleanupCallbacks.forEach((cleanup) => cleanup());
        if (threeState) {
            threeState.dispose();
            threeState.THREE.ColorManagement.enabled = true;
            threeState = null;
        }
        documentRoot.classList.remove('is-webgl-unavailable');
    };
}
