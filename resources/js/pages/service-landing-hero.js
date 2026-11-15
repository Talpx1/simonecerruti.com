export default function (canvasElement, siteRuntime) {
    const { clamp, linearInterpolate, prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    const canvasContext = canvasElement.getContext('2d');
    const heroElement = canvasElement.parentElement;
    const animationMode = canvasElement.dataset.mode;
    const startTimestamp = performance.now();
    let canvasWidth = 0;
    let canvasHeight = 0;
    let items = [];
    let smoothedPointerX = -999;
    let smoothedPointerY = -999;

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
        items = [];
        if (animationMode === 'cells') {
            const cellWidth = canvasWidth < 900 ? 46 : 62;
            const cellHeight = cellWidth * 0.42;
            const originX = canvasWidth < 900 ? canvasWidth * 0.04 : canvasWidth * 0.44;
            const originY = canvasHeight * 0.16;
            const columnCount = Math.floor((canvasWidth * 0.96 - originX) / cellWidth);
            const rowCount = Math.floor((canvasHeight * 0.42) / cellHeight);
            for (let row = 0; row < rowCount; row++) {
                for (let column = 0; column < columnCount; column++) {
                    items.push({
                        homeX: originX + column * cellWidth,
                        homeY: originY + row * cellHeight,
                        width: cellWidth,
                        height: cellHeight,
                        chaosOffsetX: (Math.random() - 0.5) * 70,
                        chaosOffsetY: (Math.random() - 0.5) * 46,
                        chaosRotation: (Math.random() - 0.5) * 0.6,
                        alignment: 0,
                        isFilled: Math.random() < 0.14,
                        wobbleSeed: Math.random() * 6.28,
                    });
                }
            }
        } else if (animationMode === 'flow') {
            const cursorCount = canvasWidth < 900 ? 16 : 30;
            for (let i = 0; i < cursorCount; i++) {
                items.push({
                    x: canvasWidth * (0.3 + Math.random() * 0.68),
                    y: canvasHeight * (0.14 + Math.random() * 0.5),
                    angle: Math.random() * 6.28,
                    speed: 18 + Math.random() * 26,
                    phase: 0,
                    phaseTimer: 0,
                    opacity: 0,
                });
            }
            items.button = {
                x: canvasWidth < 900 ? canvasWidth * 0.5 : canvasWidth * 0.74,
                y: canvasHeight * 0.33,
                width: 168,
                height: 54,
                pulse: 0,
            };
            items.ripples = [];
        } else {
            const rowCount = 7;
            const originX = canvasWidth < 900 ? canvasWidth * 0.06 : canvasWidth * 0.5;
            const rowWidth = canvasWidth < 900 ? canvasWidth * 0.88 : canvasWidth * 0.44;
            for (let i = 0; i < rowCount; i++) {
                items.push({
                    x: originX,
                    width: rowWidth,
                    y: 0,
                    score: Math.random(),
                    isOwnRow: i === rowCount - 1,
                    titleLengthRatio: 0.35 + Math.random() * 0.5,
                    subtitleLengthRatio: 0.2 + Math.random() * 0.4,
                });
            }
            items[rowCount - 1].score = -1;
        }
    };

    const drawCursor = (x, y, scale, color, alpha) => {
        canvasContext.save();
        canvasContext.translate(x, y);
        canvasContext.scale(scale, scale);
        canvasContext.beginPath();
        canvasContext.moveTo(0, 0);
        canvasContext.lineTo(0, 15);
        canvasContext.lineTo(4, 11.5);
        canvasContext.lineTo(7, 17.5);
        canvasContext.lineTo(9.6, 16.4);
        canvasContext.lineTo(6.8, 10.6);
        canvasContext.lineTo(11.6, 10.6);
        canvasContext.closePath();
        canvasContext.globalAlpha = alpha;
        canvasContext.fillStyle = color;
        canvasContext.fill();
        canvasContext.globalAlpha = 1;
        canvasContext.restore();
    };

    const drawCells = (elapsedSeconds, deltaSeconds) => {
        for (const cell of items) {
            const distance = Math.hypot(
                cell.homeX + cell.width / 2 - smoothedPointerX,
                cell.homeY + cell.height / 2 - smoothedPointerY,
            );
            const targetAlignment = prefersReducedMotion ? 1 : clamp(1.25 - distance / 240, 0, 1);
            cell.alignment = linearInterpolate(cell.alignment, targetAlignment, 1 - Math.pow(0.02, deltaSeconds));
            const chaosFactor = 1 - cell.alignment;
            const wobbleOffset = Math.sin(elapsedSeconds * 0.6 + cell.wobbleSeed) * 6 * chaosFactor;
            canvasContext.save();
            canvasContext.translate(
                cell.homeX + cell.width / 2 + cell.chaosOffsetX * chaosFactor + wobbleOffset,
                cell.homeY + cell.height / 2 + cell.chaosOffsetY * chaosFactor,
            );
            canvasContext.rotate(cell.chaosRotation * chaosFactor);
            canvasContext.strokeStyle = `rgba(241,240,236,${(0.045 + cell.alignment * 0.22).toFixed(3)})`;
            canvasContext.strokeRect(-cell.width / 2 + 0.5, -cell.height / 2 + 0.5, cell.width - 1, cell.height - 1);
            if (cell.isFilled) {
                canvasContext.fillStyle =
                    cell.alignment > 0.6
                        ? `rgba(255,74,28,${(0.15 + cell.alignment * 0.45).toFixed(3)})`
                        : 'rgba(241,240,236,.07)';
                canvasContext.fillRect(
                    -cell.width / 2 + 6,
                    -1.5,
                    (cell.width - 12) * (0.4 + 0.5 * ((cell.wobbleSeed * 7) % 1)),
                    3,
                );
            }
            canvasContext.restore();
        }
    };

    const drawFlow = (deltaSeconds) => {
        const button = items.button;
        button.pulse = Math.max(0, button.pulse - deltaSeconds * 2.5);
        const pulseScale = 1 + button.pulse * 0.06;
        const scaledButtonWidth = button.width * pulseScale;
        const scaledButtonHeight = button.height * pulseScale;
        canvasContext.fillStyle = '#FF4A1C';
        canvasContext.fillRect(
            button.x - scaledButtonWidth / 2,
            button.y - scaledButtonHeight / 2,
            scaledButtonWidth,
            scaledButtonHeight,
        );
        canvasContext.save();
        canvasContext.translate(button.x - 12, button.y - 10);
        canvasContext.strokeStyle = '#0A0A0A';
        canvasContext.lineWidth = 2.2;
        canvasContext.lineJoin = 'round';
        canvasContext.beginPath();
        canvasContext.moveTo(-2, 0);
        canvasContext.lineTo(3, 0);
        canvasContext.lineTo(7, 13);
        canvasContext.lineTo(20, 13);
        canvasContext.lineTo(23, 4);
        canvasContext.lineTo(5, 4);
        canvasContext.stroke();
        canvasContext.fillStyle = '#0A0A0A';
        canvasContext.beginPath();
        canvasContext.arc(9, 18, 2.2, 0, 6.3);
        canvasContext.arc(18.5, 18, 2.2, 0, 6.3);
        canvasContext.fill();
        canvasContext.restore();
        canvasContext.lineWidth = 1;
        items.ripples = items.ripples.filter((ripple) => (ripple.age += deltaSeconds) < 0.9);
        items.ripples.forEach((ripple) => {
            canvasContext.strokeStyle = `rgba(255,74,28,${(1 - ripple.age / 0.9) * 0.8})`;
            canvasContext.beginPath();
            canvasContext.arc(ripple.x, ripple.y, 4 + ripple.age * 46, 0, 6.3);
            canvasContext.stroke();
        });
        for (const cursor of items) {
            if (cursor.phase === 0) {
                cursor.angle += (Math.random() - 0.5) * deltaSeconds * 2;
                cursor.x += Math.cos(cursor.angle) * cursor.speed * deltaSeconds;
                cursor.y += Math.sin(cursor.angle) * cursor.speed * deltaSeconds;
                if (cursor.x < canvasWidth * 0.28 || cursor.x > canvasWidth - 20) {
                    cursor.angle = Math.PI - cursor.angle;
                }
                if (cursor.y < canvasHeight * 0.13 || cursor.y > canvasHeight * 0.66) {
                    cursor.angle = -cursor.angle;
                    cursor.y = clamp(cursor.y, canvasHeight * 0.13, canvasHeight * 0.66);
                }
                if (cursor.x < canvasWidth * 0.28 || cursor.x > canvasWidth - 20) {
                    cursor.x = clamp(cursor.x, canvasWidth * 0.28, canvasWidth - 20);
                }
                cursor.opacity = Math.min(1, cursor.opacity + deltaSeconds);
                const isNearPointer = Math.hypot(cursor.x - smoothedPointerX, cursor.y - smoothedPointerY) < 120;
                if ((isNearPointer && !prefersReducedMotion) || Math.random() < deltaSeconds * 0.05) {
                    cursor.phase = 1;
                }
            } else if (cursor.phase === 1) {
                const offsetX = button.x + 20 - cursor.x;
                const offsetY = button.y + 6 - cursor.y;
                const distance = Math.hypot(offsetX, offsetY);
                const stepDistance = Math.min(distance, 380 * deltaSeconds);
                if (distance > 3) {
                    cursor.x += (offsetX / distance) * stepDistance;
                    cursor.y += (offsetY / distance) * stepDistance;
                } else {
                    cursor.phase = 2;
                    cursor.phaseTimer = 0;
                    button.pulse = 1;
                    items.ripples.push({ x: cursor.x, y: cursor.y, age: 0 });
                }
            } else {
                cursor.phaseTimer += deltaSeconds;
                cursor.opacity = Math.max(0, 1 - cursor.phaseTimer * 2);
                if (cursor.phaseTimer > 0.8) {
                    cursor.phase = 0;
                    cursor.opacity = 0;
                    cursor.x = canvasWidth * (0.3 + Math.random() * 0.7);
                    cursor.y = canvasHeight * (0.14 + Math.random() * 0.5);
                }
            }
            drawCursor(
                cursor.x,
                cursor.y,
                1,
                cursor.phase ? '#FF4A1C' : '#F1F0EC',
                cursor.opacity * (cursor.phase ? 1 : 0.45),
            );
        }
    };

    const drawRank = (elapsedSeconds, deltaSeconds) => {
        const cycleProgress = (elapsedSeconds % 14) / 14;
        items.forEach((row, rowIndex) => {
            row.score = row.isOwnRow
                ? cycleProgress < 0.75
                    ? -0.3 + cycleProgress * 1.9
                    : 1.2
                : 0.5 + 0.45 * Math.sin(elapsedSeconds * row.subtitleLengthRatio + rowIndex * 1.7);
        });
        const rankedRows = [...items].sort((rowA, rowB) => rowB.score - rowA.score);
        const rowHeight = Math.min(52, (canvasHeight * 0.44) / items.length);
        rankedRows.forEach((row, rankIndex) => {
            const targetY = canvasHeight * 0.13 + rankIndex * rowHeight;
            row.y = row.y ? linearInterpolate(row.y, targetY, 1 - Math.pow(0.01, deltaSeconds)) : targetY;
        });
        items.forEach((row) => {
            const isHovered =
                smoothedPointerX > row.x &&
                smoothedPointerX < row.x + row.width &&
                smoothedPointerY > row.y &&
                smoothedPointerY < row.y + rowHeight - 12;
            const alpha = row.isOwnRow ? 1 : isHovered ? 0.5 : 0.22;
            canvasContext.strokeStyle = row.isOwnRow ? 'rgba(255,74,28,.9)' : `rgba(241,240,236,${alpha})`;
            canvasContext.fillStyle = canvasContext.strokeStyle;
            canvasContext.strokeRect(row.x + 0.5, row.y + 4.5, 14, 14);
            canvasContext.fillRect(row.x + 26, row.y + 6, row.width * row.titleLengthRatio, 4);
            canvasContext.globalAlpha = 0.55;
            canvasContext.fillRect(row.x + 26, row.y + 18, row.width * row.subtitleLengthRatio * 0.8, 2);
            canvasContext.globalAlpha = 1;
            if (row.isOwnRow) {
                canvasContext.beginPath();
                canvasContext.moveTo(row.x - 18, row.y + 11);
                canvasContext.lineTo(row.x - 8, row.y + 11);
                canvasContext.moveTo(row.x - 12, row.y + 7);
                canvasContext.lineTo(row.x - 8, row.y + 11);
                canvasContext.lineTo(row.x - 12, row.y + 15);
                canvasContext.stroke();
            }
        });
    };

    resizeCanvas();
    addEventListener('resize', resizeCanvas);

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = heroElement.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const elapsedSeconds = (timestamp - startTimestamp) / 1000;
        const isPointerLive =
            hasFinePointer &&
            pointer.hasMoved &&
            pointer.smoothY > heroBounds.top &&
            pointer.smoothY < heroBounds.bottom;
        const targetPointerX = isPointerLive
            ? pointer.smoothX - heroBounds.left
            : canvasWidth * (0.66 + 0.2 * Math.sin(elapsedSeconds * 0.4));
        const targetPointerY = isPointerLive
            ? pointer.smoothY - heroBounds.top
            : canvasHeight * (0.36 + 0.16 * Math.sin(elapsedSeconds * 0.57 + 1));
        smoothedPointerX =
            smoothedPointerX < -900
                ? targetPointerX
                : linearInterpolate(smoothedPointerX, targetPointerX, 1 - Math.pow(0.001, deltaSeconds));
        smoothedPointerY =
            smoothedPointerY < -900
                ? targetPointerY
                : linearInterpolate(smoothedPointerY, targetPointerY, 1 - Math.pow(0.001, deltaSeconds));
        canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
        canvasContext.lineWidth = 1;
        if (animationMode === 'cells') {
            drawCells(elapsedSeconds, deltaSeconds);
        } else if (animationMode === 'flow') {
            drawFlow(deltaSeconds);
        } else {
            drawRank(elapsedSeconds, deltaSeconds);
        }
    });

    return () => removeEventListener('resize', resizeCanvas);
}
