export default function (canvasElement, siteRuntime) {
    const { linearInterpolate, smoothStep, prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    const canvasContext = canvasElement.getContext('2d');
    const heroElement = canvasElement.parentElement;
    const startTimestamp = performance.now();
    // seeded by the URL: every page gets its own drawing, always the same one
    let randomSeed = 7;
    for (const character of location.pathname + document.title) {
        randomSeed = (randomSeed * 31 + character.charCodeAt(0)) | 0;
    }
    const nextRandomNumber = () => {
        randomSeed = (randomSeed + 0x6d2b79f5) | 0;
        let mixedValue = Math.imul(randomSeed ^ (randomSeed >>> 15), 1 | randomSeed);
        mixedValue = (mixedValue + Math.imul(mixedValue ^ (mixedValue >>> 7), 61 | mixedValue)) ^ mixedValue;
        return ((mixedValue ^ (mixedValue >>> 14)) >>> 0) / 4294967296;
    };
    const randomInRange = (minimum, maximum) => minimum + nextRandomNumber() * (maximum - minimum);
    const randomIntegerInRange = (minimum, maximum) => Math.floor(randomInRange(minimum, maximum + 1));
    const pickRandomItem = (items) => items[Math.floor(nextRandomNumber() * items.length)];

    // ---- shape helpers (coords normalized to the device frame) ----
    const createShapeRecorder = () => {
        const shapes = [];
        return {
            shapes,
            addRectangle: (startX, startY, endX, endY, isFilled) =>
                shapes.push({ type: 'rectangle', startX, startY, endX, endY, isFilled }), // isFilled is a filled hint
            addCircle: (centerX, centerY, radiusRatio) =>
                shapes.push({ type: 'circle', centerX, centerY, radiusRatio }), // avatar, pin, donut
            addLine: (startX, startY, endX, endY) => shapes.push({ type: 'line', startX, startY, endX, endY }),
            addImagePlaceholder: (startX, startY, endX, endY) =>
                shapes.push({ type: 'placeholder', startX, startY, endX, endY }),
            addPolyline: (points) => shapes.push({ type: 'polyline', points }), // chart polyline
            addTextLine: (startX, startY, lineLength) =>
                shapes.push({ type: 'line', startX, startY, endX: startX + lineLength, endY: startY }), // a line of text
        };
    };
    const drawChrome = (recorder, sidebarSide) => {
        // header + optional sidebar; returns content box
        let contentLeft = 0.04;
        let contentRight = 0.96;
        const headerHeight = randomInRange(0.08, 0.12);
        if (sidebarSide) {
            const sidebarWidth = randomInRange(0.15, 0.22);
            const sidebarX = sidebarSide === 'l' ? 0 : 1 - sidebarWidth;
            recorder.addRectangle(sidebarX, 0, sidebarX + sidebarWidth, 1);
            sidebarSide === 'l' ? (contentLeft = sidebarWidth + 0.04) : (contentRight = 1 - sidebarWidth - 0.04);
            recorder.addCircle(sidebarX + 0.045, 0.07, 0.022);
            for (let i = 0, menuItemCount = randomIntegerInRange(4, 7); i < menuItemCount; i++) {
                recorder.addTextLine(sidebarX + 0.03, 0.16 + i * 0.07, sidebarWidth * randomInRange(0.4, 0.7));
            }
        }
        recorder.addRectangle(
            sidebarSide === 'l' ? contentLeft - 0.04 : 0,
            0,
            sidebarSide === 'r' ? contentRight + 0.04 : 1,
            headerHeight,
        );
        recorder.addCircle((sidebarSide === 'r' ? contentRight : 1) - 0.05, headerHeight / 2, headerHeight * 0.22);
        return [contentLeft, headerHeight + 0.06, contentRight, 0.94];
    };
    const DESKTOP_LAYOUTS = {
        dashboard(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem(['l', 'l', 'r'])),
                contentWidth = contentRight - contentLeft,
                cardCount = randomIntegerInRange(3, 4),
                gap = 0.025,
                cardWidth = (contentWidth - gap * (cardCount - 1)) / cardCount,
                cardHeight = randomInRange(0.12, 0.15);
            for (let i = 0; i < cardCount; i++) {
                const cardLeft = contentLeft + i * (cardWidth + gap);
                recorder.addRectangle(cardLeft, contentTop, cardLeft + cardWidth, contentTop + cardHeight);
                recorder.addTextLine(cardLeft + 0.02, contentTop + cardHeight * 0.4, cardWidth * 0.5);
                recorder.addTextLine(cardLeft + 0.02, contentTop + cardHeight * 0.7, cardWidth * 0.3);
            }
            const chartTop = contentTop + cardHeight + 0.05,
                splitX = contentLeft + contentWidth * randomInRange(0.58, 0.68),
                chartBottom = chartTop + randomInRange(0.3, 0.36);
            recorder.addRectangle(contentLeft, chartTop, splitX, chartBottom);
            const points = [];
            for (let i = 0; i <= 9; i++) {
                points.push([
                    contentLeft + 0.02 + (i * (splitX - contentLeft - 0.04)) / 9,
                    chartBottom - 0.04 - randomInRange(0.02, chartBottom - chartTop - 0.1),
                ]);
            }
            recorder.addPolyline(points);
            recorder.addRectangle(splitX + gap, chartTop, contentRight, chartBottom);
            const donutRadius = Math.min(contentRight - splitX - gap, chartBottom - chartTop) * 0.32;
            recorder.addCircle((splitX + gap + contentRight) / 2, (chartTop + chartBottom) / 2, donutRadius);
            recorder.addCircle((splitX + gap + contentRight) / 2, (chartTop + chartBottom) / 2, donutRadius * 0.6);
            for (let rowY = chartBottom + 0.05; rowY < 0.9; rowY += 0.07) {
                recorder.addCircle(contentLeft + 0.015, rowY + 0.02, 0.012);
                recorder.addTextLine(contentLeft + 0.04, rowY + 0.02, contentWidth * randomInRange(0.3, 0.6));
                recorder.addTextLine(contentRight - 0.1, rowY + 0.02, 0.08);
            }
        },
        table(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem(['l', null])),
                contentWidth = contentRight - contentLeft;
            recorder.addRectangle(
                contentLeft,
                contentTop,
                contentLeft + contentWidth * randomInRange(0.3, 0.42),
                contentTop + 0.07,
            );
            recorder.addRectangle(contentRight - contentWidth * 0.15, contentTop, contentRight, contentTop + 0.07, 1);
            const hasDetailPanel = nextRandomNumber() < 0.55,
                tableRight = hasDetailPanel ? contentLeft + contentWidth * randomInRange(0.56, 0.64) : contentRight;
            recorder.addLine(contentLeft, contentTop + 0.12, tableRight, contentTop + 0.12);
            for (let rowY = contentTop + 0.16; rowY < 0.9; rowY += 0.07) {
                recorder.addCircle(contentLeft + 0.02, rowY + 0.02, 0.014);
                recorder.addTextLine(
                    contentLeft + 0.05,
                    rowY + 0.02,
                    (tableRight - contentLeft) * randomInRange(0.2, 0.35),
                );
                recorder.addTextLine(
                    contentLeft + (tableRight - contentLeft) * 0.5,
                    rowY + 0.02,
                    (tableRight - contentLeft) * randomInRange(0.1, 0.2),
                );
                recorder.addRectangle(tableRight - 0.09, rowY + 0.005, tableRight - 0.01, rowY + 0.035);
            }
            if (hasDetailPanel) {
                recorder.addRectangle(tableRight + 0.03, contentTop + 0.12, contentRight, 0.93);
                recorder.addImagePlaceholder(tableRight + 0.05, contentTop + 0.15, contentRight - 0.02, contentTop + 0.38);
                for (let i = 0; i < 5; i++) {
                    recorder.addTextLine(
                        tableRight + 0.05,
                        contentTop + 0.45 + i * 0.06,
                        (contentRight - tableRight) * randomInRange(0.4, 0.75),
                    );
                }
            }
        },
        form(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem([null, 'l'])),
                contentWidth = contentRight - contentLeft,
                columnCount = randomIntegerInRange(1, 2),
                gap = 0.05,
                fieldWidth = (contentWidth * (columnCount === 1 ? 0.62 : 1) - gap * (columnCount - 1)) / columnCount;
            for (let columnIndex = 0; columnIndex < columnCount; columnIndex++) {
                for (let fieldY = contentTop; fieldY < 0.72; fieldY += 0.14) {
                    const fieldLeft = contentLeft + columnIndex * (fieldWidth + gap);
                    recorder.addTextLine(fieldLeft, fieldY + 0.01, fieldWidth * randomInRange(0.2, 0.4));
                    recorder.addRectangle(fieldLeft, fieldY + 0.035, fieldLeft + fieldWidth, fieldY + 0.095);
                }
            }
            for (let i = 0; i < 2; i++) {
                recorder.addRectangle(contentLeft + i * 0.18, 0.79, contentLeft + i * 0.18 + 0.06, 0.83);
                recorder.addCircle(contentLeft + i * 0.18 + 0.04, 0.81, 0.014);
                recorder.addTextLine(contentLeft + i * 0.18 + 0.08, 0.81, 0.07);
            }
            recorder.addRectangle(contentRight - contentWidth * 0.22, 0.86, contentRight, 0.94, 1);
        },
        board(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem([null, 'l'])),
                contentWidth = contentRight - contentLeft,
                columnCount = randomIntegerInRange(3, 5),
                gap = 0.02,
                columnWidth = (contentWidth - gap * (columnCount - 1)) / columnCount;
            for (let i = 0; i < columnCount; i++) {
                const columnLeft = contentLeft + i * (columnWidth + gap);
                recorder.addTextLine(columnLeft, contentTop + 0.015, columnWidth * randomInRange(0.4, 0.7));
                let cardY = contentTop + 0.05;
                for (let j = 0, cardCount = randomIntegerInRange(1, 4); j < cardCount && cardY < 0.82; j++) {
                    const cardHeight = randomInRange(0.1, 0.19);
                    recorder.addRectangle(columnLeft, cardY, columnLeft + columnWidth, Math.min(0.94, cardY + cardHeight));
                    recorder.addTextLine(columnLeft + 0.015, cardY + 0.035, columnWidth * 0.6);
                    recorder.addCircle(
                        columnLeft + columnWidth - 0.025,
                        Math.min(0.94, cardY + cardHeight) - 0.03,
                        0.012,
                    );
                    cardY += cardHeight + 0.025;
                }
            }
        },
        website(recorder) {
            recorder.addRectangle(0, 0, 1, 0.1);
            recorder.addRectangle(0.04, 0.035, 0.14, 0.065);
            for (let i = 0, linkCount = randomIntegerInRange(3, 5); i < linkCount; i++) {
                recorder.addTextLine(0.5 + i * 0.09, 0.05, 0.06);
            }
            recorder.addRectangle(0.86, 0.03, 0.96, 0.07, 1);
            const isFlipped = nextRandomNumber() < 0.5,
                textX = isFlipped ? 0.54 : 0.06,
                imageX = isFlipped ? 0.06 : 0.52;
            for (let i = 0; i < 3; i++) {
                recorder.addRectangle(textX, 0.2 + i * 0.075, textX + randomInRange(0.25, 0.4), 0.25 + i * 0.075, 1);
            }
            recorder.addTextLine(textX, 0.47, 0.3);
            recorder.addTextLine(textX, 0.51, 0.24);
            recorder.addRectangle(textX, 0.57, textX + 0.14, 0.63, 1);
            recorder.addImagePlaceholder(imageX, 0.17, imageX + 0.42, 0.64);
            const cardCount = randomIntegerInRange(3, 4),
                gap = 0.03,
                cardWidth = (0.88 - gap * (cardCount - 1)) / cardCount;
            for (let i = 0; i < cardCount; i++) {
                const cardLeft = 0.06 + i * (cardWidth + gap);
                recorder.addRectangle(cardLeft, 0.71, cardLeft + cardWidth, 0.94);
                recorder.addCircle(cardLeft + 0.04, 0.77, 0.02);
                recorder.addTextLine(cardLeft + 0.02, 0.84, cardWidth * 0.7);
                recorder.addTextLine(cardLeft + 0.02, 0.88, cardWidth * 0.5);
            }
        },
        calendar(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem(['l', null])),
                contentWidth = contentRight - contentLeft,
                cellWidth = contentWidth / 7,
                rowHeight = (0.92 - contentTop - 0.06) / 5;
            recorder.addTextLine(contentLeft, contentTop + 0.015, contentWidth * 0.25);
            for (let i = 0; i < 7; i++) {
                recorder.addTextLine(contentLeft + i * cellWidth + 0.01, contentTop + 0.055, cellWidth * 0.4);
            }
            for (let weekIndex = 0; weekIndex < 5; weekIndex++) {
                for (let dayIndex = 0; dayIndex < 7; dayIndex++) {
                    const cellLeft = contentLeft + dayIndex * cellWidth,
                        cellTop = contentTop + 0.08 + weekIndex * rowHeight;
                    recorder.addRectangle(cellLeft, cellTop, cellLeft + cellWidth, cellTop + rowHeight);
                    if (nextRandomNumber() < 0.22) {
                        recorder.addRectangle(
                            cellLeft + 0.008,
                            cellTop + rowHeight * 0.45,
                            cellLeft + cellWidth * randomInRange(0.6, 0.95),
                            cellTop + rowHeight * 0.8,
                            1,
                        );
                    }
                }
            }
        },
        chat(recorder) {
            const sidebarWidth = randomInRange(0.26, 0.34);
            recorder.addRectangle(0, 0, sidebarWidth, 1);
            for (let i = 0; i < 7; i++) {
                recorder.addCircle(0.05, 0.08 + i * 0.12, 0.025);
                recorder.addTextLine(0.09, 0.07 + i * 0.12, sidebarWidth * 0.5);
                recorder.addTextLine(0.09, 0.1 + i * 0.12, sidebarWidth * 0.35);
            }
            recorder.addRectangle(sidebarWidth, 0, 1, 0.1);
            recorder.addCircle(sidebarWidth + 0.05, 0.05, 0.025);
            let bubbleY = 0.16;
            while (bubbleY < 0.76) {
                const isOwnMessage = nextRandomNumber() < 0.5,
                    bubbleWidth = randomInRange(0.18, 0.4),
                    bubbleHeight = randomInRange(0.06, 0.11);
                isOwnMessage
                    ? recorder.addRectangle(0.96 - bubbleWidth, bubbleY, 0.96, bubbleY + bubbleHeight, 1)
                    : recorder.addRectangle(
                          sidebarWidth + 0.04,
                          bubbleY,
                          sidebarWidth + 0.04 + bubbleWidth,
                          bubbleY + bubbleHeight,
                      );
                bubbleY += bubbleHeight + 0.035;
            }
            recorder.addRectangle(sidebarWidth + 0.04, 0.84, 0.88, 0.93);
            recorder.addCircle(0.93, 0.885, 0.028);
        },
        map(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, null);
            recorder.addRectangle(contentLeft, contentTop, contentRight, 0.94);
            for (let i = 0; i < 6; i++) {
                const points = [];
                const isVertical = nextRandomNumber() < 0.5;
                for (let j = 0; j <= 5; j++) {
                    const progress = j / 5;
                    points.push(
                        isVertical
                            ? [
                                  contentLeft +
                                      randomInRange(0.1, 0.9) * (contentRight - contentLeft) * 0.2 +
                                      (contentRight - contentLeft) * (i / 6) +
                                      randomInRange(-0.03, 0.03),
                                  contentTop + progress * (0.94 - contentTop),
                              ]
                            : [
                                  contentLeft + progress * (contentRight - contentLeft),
                                  contentTop + (i / 6) * (0.94 - contentTop) + randomInRange(-0.04, 0.04),
                              ],
                    );
                }
                recorder.addPolyline(points);
            }
            for (let i = 0, pinCount = randomIntegerInRange(3, 6); i < pinCount; i++) {
                const pinX = randomInRange(contentLeft + 0.08, contentRight - 0.08),
                    pinY = randomInRange(contentTop + 0.08, 0.88);
                recorder.addCircle(pinX, pinY, 0.018);
                recorder.addCircle(pinX, pinY, 0.045);
            }
            recorder.addRectangle(contentLeft + 0.03, contentTop + 0.03, contentLeft + 0.3, contentTop + 0.3, 1);
            for (let i = 0; i < 3; i++) {
                recorder.addTextLine(contentLeft + 0.05, contentTop + 0.09 + i * 0.06, 0.18);
            }
        },
        flow(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem([null, 'l'])),
                columnCount = randomIntegerInRange(3, 4),
                contentWidth = contentRight - contentLeft,
                nodes = [];
            for (let columnIndex = 0; columnIndex < columnCount; columnIndex++) {
                const nodeCount = randomIntegerInRange(1, 3);
                for (let j = 0; j < nodeCount; j++) {
                    const nodeCenterX = contentLeft + ((columnIndex + 0.5) * contentWidth) / columnCount,
                        nodeCenterY =
                            contentTop + ((j + 0.5) * (0.9 - contentTop)) / nodeCount + randomInRange(-0.03, 0.03),
                        nodeWidth = (contentWidth / columnCount) * 0.62;
                    recorder.addRectangle(
                        nodeCenterX - nodeWidth / 2,
                        nodeCenterY - 0.05,
                        nodeCenterX + nodeWidth / 2,
                        nodeCenterY + 0.05,
                        columnIndex === columnCount - 1 ? 1 : 0,
                    );
                    nodes.push([columnIndex, nodeCenterX, nodeCenterY, nodeWidth]);
                }
            }
            nodes.forEach(([columnIndex, centerX, centerY, nodeWidth]) =>
                nodes
                    .filter((candidateNode) => candidateNode[0] === columnIndex + 1)
                    .forEach(([, nextCenterX, nextCenterY, nextNodeWidth]) => {
                        const middleX = (centerX + nodeWidth / 2 + nextCenterX - nextNodeWidth / 2) / 2;
                        recorder.addPolyline([
                            [centerX + nodeWidth / 2, centerY],
                            [middleX, centerY],
                            [middleX, nextCenterY],
                            [nextCenterX - nextNodeWidth / 2, nextCenterY],
                        ]);
                    }),
            );
        },
        shop(recorder) {
            const [contentLeft, contentTop, contentRight] = drawChrome(recorder, pickRandomItem([null, 'l'])),
                contentWidth = contentRight - contentLeft,
                columnCount = randomIntegerInRange(3, 4),
                gap = 0.025,
                cardWidth = (contentWidth - gap * (columnCount - 1)) / columnCount,
                rowCount = randomIntegerInRange(1, 2),
                rowHeight = (0.92 - contentTop) / rowCount - 0.03;
            for (let rowIndex = 0; rowIndex < rowCount; rowIndex++) {
                for (let i = 0; i < columnCount; i++) {
                    const cardLeft = contentLeft + i * (cardWidth + gap),
                        cardTop = contentTop + rowIndex * (rowHeight + 0.03);
                    recorder.addImagePlaceholder(cardLeft, cardTop, cardLeft + cardWidth, cardTop + rowHeight * 0.62);
                    recorder.addTextLine(cardLeft, cardTop + rowHeight * 0.72, cardWidth * 0.7);
                    recorder.addTextLine(cardLeft, cardTop + rowHeight * 0.8, cardWidth * 0.35);
                    recorder.addRectangle(
                        cardLeft + cardWidth * 0.6,
                        cardTop + rowHeight * 0.86,
                        cardLeft + cardWidth,
                        cardTop + rowHeight,
                        1,
                    );
                }
            }
        },
    };
    const PHONE_LAYOUTS = {
        list(recorder) {
            recorder.addRectangle(0, 0, 1, 0.09);
            for (let i = 0; i < 7; i++) {
                const itemY = 0.13 + i * 0.1;
                recorder.addCircle(0.13, itemY + 0.03, 0.05);
                recorder.addTextLine(0.25, itemY + 0.02, randomInRange(0.3, 0.55));
                recorder.addTextLine(0.25, itemY + 0.05, randomInRange(0.2, 0.4));
                recorder.addLine(0.06, itemY + 0.085, 0.94, itemY + 0.085);
            }
            recorder.addRectangle(0, 0.92, 1, 1);
        },
        chat(recorder) {
            recorder.addRectangle(0, 0, 1, 0.09);
            recorder.addCircle(0.12, 0.045, 0.04);
            let bubbleY = 0.13;
            while (bubbleY < 0.8) {
                const isOwnMessage = nextRandomNumber() < 0.5,
                    bubbleWidth = randomInRange(0.35, 0.7),
                    bubbleHeight = randomInRange(0.05, 0.09);
                isOwnMessage
                    ? recorder.addRectangle(0.94 - bubbleWidth, bubbleY, 0.94, bubbleY + bubbleHeight, 1)
                    : recorder.addRectangle(0.06, bubbleY, 0.06 + bubbleWidth, bubbleY + bubbleHeight);
                bubbleY += bubbleHeight + 0.03;
            }
            recorder.addRectangle(0.06, 0.87, 0.8, 0.94);
            recorder.addCircle(0.88, 0.905, 0.045);
        },
        card(recorder) {
            recorder.addImagePlaceholder(0, 0, 1, 0.42);
            recorder.addRectangle(0.06, 0.47, 0.8, 0.52, 1);
            recorder.addTextLine(0.06, 0.57, 0.6);
            recorder.addTextLine(0.06, 0.61, 0.45);
            for (let i = 0; i < 3; i++) {
                recorder.addCircle(0.1 + i * 0.13, 0.69, 0.045);
            }
            recorder.addRectangle(0.06, 0.86, 0.94, 0.94, 1);
        },
        form(recorder) {
            recorder.addRectangle(0, 0, 1, 0.09);
            for (let i = 0; i < 5; i++) {
                const fieldY = 0.14 + i * 0.13;
                recorder.addTextLine(0.06, fieldY, randomInRange(0.2, 0.4));
                recorder.addRectangle(0.06, fieldY + 0.025, 0.94, fieldY + 0.085);
            }
            recorder.addRectangle(0.06, 0.84, 0.94, 0.92, 1);
        },
        stats(recorder) {
            recorder.addRectangle(0, 0, 1, 0.09);
            recorder.addCircle(0.5, 0.27, 0.17);
            recorder.addCircle(0.5, 0.27, 0.11);
            for (let i = 0; i < 2; i++) {
                recorder.addRectangle(0.06 + i * 0.46, 0.5, 0.48 + i * 0.46, 0.64);
            }
            const points = [];
            for (let i = 0; i <= 6; i++) {
                points.push([0.06 + (i * 0.88) / 6, randomInRange(0.72, 0.86)]);
            }
            recorder.addPolyline(points);
            recorder.addRectangle(0, 0.92, 1, 1);
        },
    };

    // ---- composition: which devices, where ----
    const compositionIndex = randomIntegerInRange(0, 4),
        desktopLayoutName = pickRandomItem(Object.keys(DESKTOP_LAYOUTS)),
        phoneLayoutName = pickRandomItem(Object.keys(PHONE_LAYOUTS)),
        secondPhoneLayoutName = pickRandomItem(Object.keys(PHONE_LAYOUTS));
    const buildShapes = (layoutFunction) => {
        const recorder = createShapeRecorder();
        layoutFunction(recorder);
        return recorder.shapes;
    };
    let devices = [];
    const planDevices = (viewportWidth, viewportHeight) => {
        const isMobileViewport = viewportWidth < 900,
            deviceArea = isMobileViewport
                ? [viewportWidth * 0.06, viewportHeight * 0.12, viewportWidth * 0.94, viewportHeight * 0.5]
                : [viewportWidth * 0.42, viewportHeight * 0.12, viewportWidth * 0.96, viewportHeight * 0.66];
        const areaWidth = deviceArea[2] - deviceArea[0],
            areaHeight = deviceArea[3] - deviceArea[1],
            plannedDevices = [];
        const addDevice = (x, y, width, height, shapes, depth, kind) =>
            plannedDevices.push({ x, y, width, height, shapes, depth, kind });
        if (compositionIndex === 0 || isMobileViewport) {
            const deviceWidth = Math.min(areaWidth, areaHeight / 0.62),
                deviceHeight = deviceWidth * randomInRange(0.56, 0.66);
            addDevice(
                deviceArea[2] - deviceWidth,
                deviceArea[1],
                deviceWidth,
                deviceHeight,
                buildShapes(DESKTOP_LAYOUTS[desktopLayoutName]),
                4,
                'desktop',
            );
        } else if (compositionIndex === 1) {
            const deviceWidth = areaWidth * 0.82,
                deviceHeight = deviceWidth * 0.6;
            addDevice(
                deviceArea[2] - deviceWidth,
                deviceArea[1],
                deviceWidth,
                deviceHeight,
                buildShapes(DESKTOP_LAYOUTS[desktopLayoutName]),
                3,
                'desktop',
            );
            const phoneWidth = deviceWidth * 0.24;
            addDevice(
                deviceArea[2] - deviceWidth - phoneWidth * 0.35,
                deviceArea[1] + deviceHeight * 0.35,
                phoneWidth,
                phoneWidth * 2.05,
                buildShapes(PHONE_LAYOUTS[phoneLayoutName]),
                12,
                'phone',
            );
        } else if (compositionIndex === 2) {
            const phoneHeight = Math.min(areaHeight * 1.05, areaWidth * 0.9),
                phoneWidth = phoneHeight / 2.05;
            addDevice(
                deviceArea[2] - phoneWidth * 2.25,
                deviceArea[1] + areaHeight * 0.06,
                phoneWidth,
                phoneHeight,
                buildShapes(PHONE_LAYOUTS[phoneLayoutName]),
                6,
                'phone',
            );
            addDevice(
                deviceArea[2] - phoneWidth,
                deviceArea[1] - areaHeight * 0.02,
                phoneWidth,
                phoneHeight,
                buildShapes(PHONE_LAYOUTS[secondPhoneLayoutName === phoneLayoutName ? 'list' : secondPhoneLayoutName]),
                12,
                'phone',
            );
        } else if (compositionIndex === 3) {
            const deviceHeight = Math.min(areaHeight * 0.9, areaWidth * 0.68),
                deviceWidth = deviceHeight * 1.38;
            addDevice(
                deviceArea[2] - deviceWidth - 14,
                deviceArea[1] + 20,
                deviceWidth,
                deviceHeight,
                buildShapes(DESKTOP_LAYOUTS[desktopLayoutName]),
                4,
                'tablet',
            );
        } else {
            const deviceWidth = areaWidth * 0.7,
                deviceHeight = deviceWidth * 0.58;
            addDevice(
                deviceArea[0] + areaWidth * 0.02,
                deviceArea[1] + areaHeight * 0.08,
                deviceWidth,
                deviceHeight,
                buildShapes(DESKTOP_LAYOUTS[desktopLayoutName]),
                3,
                'desktop',
            );
            const phoneWidth = deviceWidth * 0.26;
            addDevice(
                deviceArea[2] - phoneWidth,
                deviceArea[1] - areaHeight * 0.02,
                phoneWidth,
                phoneWidth * 2.05,
                buildShapes(PHONE_LAYOUTS[phoneLayoutName]),
                12,
                'phone',
            );
        }
        return plannedDevices;
    };
    let canvasWidth = 0,
        canvasHeight = 0;
    const resizeCanvas = () => {
        const heroBounds = heroElement.getBoundingClientRect(),
            devicePixelRatioCapped = Math.min(devicePixelRatio, 2);
        canvasWidth = heroBounds.width;
        canvasHeight = heroBounds.height;
        canvasElement.width = canvasWidth * devicePixelRatioCapped;
        canvasElement.height = canvasHeight * devicePixelRatioCapped;
        canvasElement.style.width = canvasWidth + 'px';
        canvasElement.style.height = canvasHeight + 'px';
        canvasContext.setTransform(devicePixelRatioCapped, 0, 0, devicePixelRatioCapped, 0, 0);
        devices = planDevices(canvasWidth, canvasHeight);
    };
    // freeze the random stream for layout, so resizes keep the same drawing
    const initialRandomSeed = randomSeed;
    const resetSeedAndResizeCanvas = () => {
        randomSeed = initialRandomSeed;
        resizeCanvas();
    };
    resetSeedAndResizeCanvas();
    addEventListener('resize', resetSeedAndResizeCanvas);

    let smoothedPointerX = canvasWidth * 0.7,
        smoothedPointerY = canvasHeight * 0.4;
    const addRulerTick = (x, y, isHorizontal) => {
        canvasContext.moveTo(x, y);
        isHorizontal ? canvasContext.lineTo(x, y + 6) : canvasContext.lineTo(x + 6, y);
    };
    const drawDashed = (progress, dashLength, drawStroke) => {
        canvasContext.setLineDash([dashLength * progress, dashLength + 1]);
        drawStroke();
        canvasContext.setLineDash([]);
    };
    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = heroElement.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const elapsedSeconds = (timestamp - startTimestamp) / 1000;
        let targetPointerX = pointer.smoothX - heroBounds.left,
            targetPointerY = pointer.smoothY - heroBounds.top;
        if (!(hasFinePointer && pointer.hasMoved && targetPointerY > 0 && targetPointerY < canvasHeight)) {
            targetPointerX = canvasWidth * (0.62 + 0.22 * Math.sin(elapsedSeconds * 0.35));
            targetPointerY = canvasHeight * (0.4 + 0.2 * Math.sin(elapsedSeconds * 0.53 + 1));
        }
        smoothedPointerX = linearInterpolate(smoothedPointerX, targetPointerX, 1 - Math.pow(0.0005, deltaSeconds));
        smoothedPointerY = linearInterpolate(smoothedPointerY, targetPointerY, 1 - Math.pow(0.0005, deltaSeconds));
        canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
        canvasContext.lineWidth = 1;
        canvasContext.strokeStyle = 'rgba(241,240,236,.035)';
        canvasContext.beginPath();
        for (let x = 0; x < canvasWidth; x += 32) {
            canvasContext.moveTo(x + 0.5, 0);
            canvasContext.lineTo(x + 0.5, canvasHeight);
        }
        for (let y = 0; y < canvasHeight; y += 32) {
            canvasContext.moveTo(0, y + 0.5);
            canvasContext.lineTo(canvasWidth, y + 0.5);
        }
        canvasContext.stroke();
        canvasContext.strokeStyle = 'rgba(241,240,236,.18)';
        canvasContext.beginPath();
        for (let x = 0; x < canvasWidth; x += 8) {
            if (Math.abs(x - smoothedPointerX) < 60 || x % 64 === 0) {
                addRulerTick(x + 0.5, 0, true);
            }
        }
        for (let y = 0; y < canvasHeight; y += 8) {
            if (Math.abs(y - smoothedPointerY) < 60 || y % 64 === 0) {
                addRulerTick(0, y + 0.5, false);
            }
        }
        canvasContext.stroke();
        canvasContext.strokeStyle = 'rgba(241,240,236,.1)';
        canvasContext.setLineDash([2, 6]);
        canvasContext.beginPath();
        canvasContext.moveTo(smoothedPointerX, 0);
        canvasContext.lineTo(smoothedPointerX, canvasHeight);
        canvasContext.moveTo(0, smoothedPointerY);
        canvasContext.lineTo(canvasWidth, smoothedPointerY);
        canvasContext.stroke();
        canvasContext.setLineDash([]);
        const parallaxX = smoothedPointerX / canvasWidth - 0.5,
            parallaxY = smoothedPointerY / canvasHeight - 0.5;
        let hoveredRegion = null,
            revealOrder = 0;
        devices.forEach((device) => {
            const deviceLeft = device.x - parallaxX * device.depth,
                deviceTop = device.y - parallaxY * device.depth,
                toDeviceX = (value) => deviceLeft + value * device.width,
                toDeviceY = (value) => deviceTop + value * device.height;
            const getRevealProgress = (revealIndex) =>
                prefersReducedMotion ? 1 : smoothStep(0, 1, (elapsedSeconds * 1000 - 200 - revealIndex * 16) / 700);
            // device body: covers what is behind it, rounded for phones
            const deviceProgress = getRevealProgress(revealOrder++),
                cornerRadius = device.kind === 'phone' ? device.width * 0.12 : device.kind === 'tablet' ? 14 : 4,
                framePadding = device.kind === 'desktop' ? 0 : device.width * 0.05;
            if (deviceProgress > 0) {
                canvasContext.fillStyle = 'rgba(12,12,12,' + (0.92 * deviceProgress).toFixed(2) + ')';
                canvasContext.beginPath();
                canvasContext.roundRect(
                    deviceLeft - framePadding,
                    deviceTop - framePadding * (device.kind === 'phone' ? 2 : 1),
                    device.width + framePadding * 2,
                    device.height + framePadding * (device.kind === 'phone' ? 4 : 2),
                    cornerRadius,
                );
                canvasContext.fill();
                canvasContext.strokeStyle = 'rgba(241,240,236,.24)';
                drawDashed(deviceProgress, 2 * (device.width + device.height) + 400, () => canvasContext.stroke());
            }
            if (device.kind === 'desktop' && deviceProgress > 0) {
                canvasContext.strokeStyle = 'rgba(241,240,236,.18)';
                canvasContext.beginPath();
                canvasContext.rect(deviceLeft, deviceTop, device.width, device.height);
                canvasContext.stroke();
            }
            device.shapes.forEach((shape) => {
                const shapeProgress = getRevealProgress(revealOrder++);
                if (shapeProgress <= 0) {
                    return;
                }
                canvasContext.strokeStyle = 'rgba(241,240,236,.16)';
                canvasContext.fillStyle = 'rgba(241,240,236,.06)';
                if (shape.type === 'rectangle' || shape.type === 'placeholder') {
                    const rectX = toDeviceX(shape.startX),
                        rectY = toDeviceY(shape.startY),
                        rectWidth = toDeviceX(shape.endX) - rectX,
                        rectHeight = toDeviceY(shape.endY) - rectY;
                    if (shape.isFilled) {
                        canvasContext.globalAlpha = shapeProgress;
                        canvasContext.fillRect(rectX, rectY, rectWidth, rectHeight);
                        canvasContext.globalAlpha = 1;
                    }
                    drawDashed(shapeProgress, 2 * (rectWidth + rectHeight), () =>
                        canvasContext.strokeRect(rectX + 0.5, rectY + 0.5, rectWidth, rectHeight),
                    );
                    if (shape.type === 'placeholder') {
                        canvasContext.beginPath();
                        canvasContext.moveTo(rectX, rectY);
                        canvasContext.lineTo(rectX + rectWidth * shapeProgress, rectY + rectHeight * shapeProgress);
                        canvasContext.moveTo(rectX + rectWidth, rectY);
                        canvasContext.lineTo(
                            rectX + rectWidth - rectWidth * shapeProgress,
                            rectY + rectHeight * shapeProgress,
                        );
                        canvasContext.stroke();
                    }
                    if (
                        rectWidth > 8 &&
                        rectHeight > 6 &&
                        smoothedPointerX > rectX &&
                        smoothedPointerX < rectX + rectWidth &&
                        smoothedPointerY > rectY &&
                        smoothedPointerY < rectY + rectHeight &&
                        (!hoveredRegion || rectWidth * rectHeight < hoveredRegion.width * hoveredRegion.height)
                    ) {
                        hoveredRegion = { x: rectX, y: rectY, width: rectWidth, height: rectHeight };
                    }
                } else if (shape.type === 'circle') {
                    const circleX = toDeviceX(shape.centerX),
                        circleY = toDeviceY(shape.centerY),
                        circleRadius = shape.radiusRatio * device.width;
                    canvasContext.beginPath();
                    canvasContext.arc(
                        circleX,
                        circleY,
                        circleRadius,
                        -Math.PI / 2,
                        -Math.PI / 2 + Math.PI * 2 * shapeProgress,
                    );
                    canvasContext.stroke();
                    if (
                        circleRadius > 6 &&
                        Math.hypot(smoothedPointerX - circleX, smoothedPointerY - circleY) < circleRadius &&
                        !hoveredRegion
                    ) {
                        hoveredRegion = {
                            x: circleX - circleRadius,
                            y: circleY - circleRadius,
                            width: circleRadius * 2,
                            height: circleRadius * 2,
                        };
                    }
                } else if (shape.type === 'line') {
                    canvasContext.strokeStyle = 'rgba(241,240,236,.13)';
                    canvasContext.lineWidth = Math.max(1.5, device.height * 0.012);
                    canvasContext.beginPath();
                    canvasContext.moveTo(toDeviceX(shape.startX), toDeviceY(shape.startY));
                    canvasContext.lineTo(
                        toDeviceX(shape.startX + (shape.endX - shape.startX) * shapeProgress),
                        toDeviceY(shape.startY + (shape.endY - shape.startY) * shapeProgress),
                    );
                    canvasContext.stroke();
                    canvasContext.lineWidth = 1;
                } else if (shape.type === 'polyline') {
                    const segmentCount = shape.points.length - 1,
                        revealedSegments = shapeProgress * segmentCount;
                    canvasContext.strokeStyle = 'rgba(255,74,28,.45)';
                    canvasContext.beginPath();
                    shape.points.forEach(([pointX, pointY], pointIndex) => {
                        if (pointIndex > revealedSegments + 1) {
                            return;
                        }
                        let drawX = toDeviceX(pointX),
                            drawY = toDeviceY(pointY);
                        if (pointIndex > revealedSegments) {
                            const [previousX, previousY] = shape.points[pointIndex - 1],
                                fraction = revealedSegments - (pointIndex - 1);
                            drawX = toDeviceX(previousX + (pointX - previousX) * fraction);
                            drawY = toDeviceY(previousY + (pointY - previousY) * fraction);
                        }
                        pointIndex ? canvasContext.lineTo(drawX, drawY) : canvasContext.moveTo(drawX, drawY);
                    });
                    canvasContext.stroke();
                }
            });
        });
        if (hoveredRegion) {
            const { x: regionX, y: regionY, width: regionWidth, height: regionHeight } = hoveredRegion;
            canvasContext.fillStyle = 'rgba(255,74,28,.07)';
            canvasContext.fillRect(regionX, regionY, regionWidth, regionHeight);
            canvasContext.strokeStyle = '#FF4A1C';
            canvasContext.lineWidth = 1.2;
            canvasContext.strokeRect(regionX + 0.5, regionY + 0.5, regionWidth, regionHeight);
            canvasContext.fillStyle = '#FF4A1C';
            [
                [regionX, regionY],
                [regionX + regionWidth, regionY],
                [regionX, regionY + regionHeight],
                [regionX + regionWidth, regionY + regionHeight],
            ].forEach(([cornerX, cornerY]) => canvasContext.fillRect(cornerX - 3, cornerY - 3, 6, 6));
            canvasContext.strokeStyle = 'rgba(255,74,28,.7)';
            canvasContext.lineWidth = 1;
            canvasContext.beginPath();
            canvasContext.moveTo(regionX, regionY - 14);
            canvasContext.lineTo(regionX + regionWidth, regionY - 14);
            canvasContext.moveTo(regionX, regionY - 19);
            canvasContext.lineTo(regionX, regionY - 9);
            canvasContext.moveTo(regionX + regionWidth, regionY - 19);
            canvasContext.lineTo(regionX + regionWidth, regionY - 9);
            canvasContext.moveTo(regionX - 14, regionY);
            canvasContext.lineTo(regionX - 14, regionY + regionHeight);
            canvasContext.moveTo(regionX - 19, regionY);
            canvasContext.lineTo(regionX - 9, regionY);
            canvasContext.moveTo(regionX - 19, regionY + regionHeight);
            canvasContext.lineTo(regionX - 9, regionY + regionHeight);
            canvasContext.stroke();
        }
        canvasContext.fillStyle = '#FF4A1C';
        canvasContext.fillRect(smoothedPointerX - 2, smoothedPointerY - 2, 4, 4);
    });

    return () => removeEventListener('resize', resetSeedAndResizeCanvas);
}
