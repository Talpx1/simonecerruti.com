export default function (rootElement, siteRuntime) {
    const { queryOne, queryAll, prefersReducedMotion } = siteRuntime;
    const config = JSON.parse(queryOne('.page-effect-config', rootElement).textContent);
    const translations = config.text;
    const abortController = new AbortController();
    const { signal } = abortController;
    const timers = [];
    let isAlive = true;

    const addListener = (target, eventType, handler, options = {}) =>
        target.addEventListener(eventType, handler, { signal, ...options });
    const createElement = (tagName, className, text) => {
        const node = document.createElement(tagName);
        if (className) {
            node.className = className;
        }
        if (text !== undefined) {
            node.textContent = text;
        }
        return node;
    };
    const waitMilliseconds = (milliseconds) =>
        new Promise((resolve) => {
            const timerId = setTimeout(resolve, milliseconds);
            timers.push(timerId);
        });

    const fitSection = queryOne('#method-fit-section', rootElement);
    const rulesContainer = queryOne('.method-rules', fitSection);
    const flowList = queryOne('#method-flow-list', fitSection);
    const fitScore = queryOne('#method-fit-score-line', fitSection);
    const statusIcons = { blocked: '✕', workaround: '↻', supported: '✓' };
    let selectedRuleIndex = 0;
    let fitMode = 'template';

    config.rules.forEach((rule, ruleIndex) => {
        const ruleButton = createElement('button', ruleIndex === 0 ? 'is-active' : '', rule.title);
        ruleButton.type = 'button';
        ruleButton.dataset.itemIndex = ruleIndex;
        rulesContainer.appendChild(ruleButton);
    });

    const renderFit = () => {
        const selectedRule = config.rules[selectedRuleIndex];
        const flowItems = fitMode === 'custom' ? selectedRule.custom_steps : selectedRule.template_steps;
        queryAll('.segmented-toggle button', fitSection).forEach((switchButton) => {
            switchButton.classList.toggle('is-active', switchButton.dataset.fitMode === fitMode);
            switchButton.classList.toggle('is-accent', switchButton.dataset.fitMode === 'custom');
        });
        queryOne('#method-fit-rule-title', fitSection).textContent = '«' + selectedRule.rule + '»';
        flowList.replaceChildren(
            ...flowItems.map((flowItem, itemIndex) => {
                const listItem = createElement('li', 'method-flow-step--' + flowItem[0]);
                listItem.style.transitionDelay = itemIndex * 90 + 'ms';
                listItem.append(
                    createElement('i', '', statusIcons[flowItem[0]]),
                    createElement('span', '', flowItem[1]),
                );
                return listItem;
            }),
        );
        requestAnimationFrame(() =>
            queryAll('#method-flow-list li', fitSection).forEach((listItem) => listItem.classList.add('is-revealed')),
        );
        const manualWorkaroundCount = flowItems.filter((flowItem) => flowItem[0] === 'workaround').length;
        const [textBefore, textAfter] = translations.workarounds.split(':count');
        const workaroundCountElement = createElement(
            'b',
            fitMode === 'custom' ? 'is-success' : '',
            fitMode === 'custom' ? '0' : String(manualWorkaroundCount),
        );
        fitScore.replaceChildren(textBefore, workaroundCountElement);
        if (textAfter) {
            fitScore.append(textAfter);
        }
        if (fitMode !== 'custom') {
            fitScore.append(' ', createElement('span', '', translations.rule_stays_outside));
        }
    };
    queryAll('.method-rules button', fitSection).forEach((ruleButton) =>
        addListener(ruleButton, 'click', () => {
            selectedRuleIndex = Number(ruleButton.dataset.itemIndex);
            queryAll('.method-rules button', fitSection).forEach((otherButton) =>
                otherButton.classList.toggle('is-active', otherButton === ruleButton),
            );
            renderFit();
        }),
    );
    queryAll('.segmented-toggle button', fitSection).forEach((switchButton) =>
        addListener(switchButton, 'click', () => {
            fitMode = switchButton.dataset.fitMode;
            renderFit();
        }),
    );
    renderFit();

    const testsSection = queryOne('#method-tests-section', rootElement);
    const testRuns = queryAll('.method-check', testsSection);
    const progressMeter = queryOne('.method-meter i', testsSection);
    let hasRunTests = false;
    const setRunState = (testRun, text) => (testRun.querySelector('b').textContent = text);
    const runTests = async () => {
        testRuns.forEach((testRun) => {
            testRun.classList.remove('is-done');
            setRunState(testRun, translations.waiting);
        });
        progressMeter.style.transform = 'scaleX(0)';
        for (let i = 0; i < testRuns.length; i++) {
            const isSecurityRun = testRuns[i].classList.contains('section');
            setRunState(testRuns[i], isSecurityRun ? translations.analysing : translations.running);
            await waitMilliseconds(prefersReducedMotion ? 0 : 480);
            if (!isAlive) {
                return;
            }
            testRuns[i].classList.add('is-done');
            setRunState(
                testRuns[i],
                i === testRuns.length - 1
                    ? translations.none
                    : isSecurityRun
                      ? translations.clean
                      : translations.passed,
            );
            progressMeter.style.transform = `scaleX(${(i + 1) / testRuns.length})`;
        }
    };
    addListener(queryOne('#rerun-tests-button', testsSection), 'click', runTests);

    const moduleNav = queryOne('#method-app-nav', rootElement);
    const moduleGrid = queryOne('#method-app-grid', rootElement);
    const moduleCountElement = queryOne('#module-count', rootElement);
    const addModuleButton = queryOne('#add-module-button', rootElement);
    let visibleModuleCount = 3;
    const createVisualization = (visualizationType) => {
        const visualizationElement = createElement(
            'b',
            'method-app-visual--' + { bar: 'bar', chart: 'chart', rows: 'rows', phone: 'phone', ai: 'ai' }[visualizationType] ||
                'dots',
        );
        const childCount = { bar: 1, chart: 5, rows: 3, phone: 0, ai: 0 }[visualizationType] ?? 5;
        for (let i = 0; i < childCount; i++) {
            const barElement = createElement('i');
            if (visualizationType === 'chart') {
                barElement.style.height = [40, 65, 50, 85, 100][i] + '%';
            }
            visualizationElement.appendChild(barElement);
        }
        if (visualizationType === 'ai') {
            visualizationElement.textContent = 'AI';
        }
        return visualizationElement;
    };
    const drawApp = (isFreshModule) => {
        const visibleModules = config.modules.slice(0, visibleModuleCount);
        moduleNav.replaceChildren(
            ...visibleModules.map((moduleData, moduleIndex) => {
                const navItem = createElement(
                    'li',
                    (moduleIndex === 0 ? 'cur' : '') +
                        (isFreshModule && moduleIndex === visibleModuleCount - 1 ? ' fresh' : ''),
                    moduleData.name,
                );
                if (isFreshModule && moduleIndex === visibleModuleCount - 1) {
                    navItem.appendChild(createElement('em', '', translations.new));
                }
                return navItem;
            }),
        );
        moduleGrid.replaceChildren(
            ...visibleModules.map((moduleData, moduleIndex) => {
                const moduleCard = createElement(
                    'div',
                    'method-app-card' + (isFreshModule && moduleIndex === visibleModuleCount - 1 ? ' fresh' : ''),
                );
                moduleCard.append(
                    createElement('span', '', moduleData.name),
                    createElement('strong', '', moduleData.value),
                    createElement('small', '', moduleData.label),
                    createVisualization(moduleData.visualization),
                );
                return moduleCard;
            }),
        );
        moduleCountElement.textContent = translations.modules.replace(':count', String(visibleModuleCount));
    };
    drawApp(false);
    addListener(addModuleButton, 'click', () => {
        if (visibleModuleCount >= config.modules.length) {
            visibleModuleCount = 3;
            drawApp(false);
        } else {
            visibleModuleCount++;
            drawApp(true);
        }
        addModuleButton.querySelector('span').textContent =
            visibleModuleCount >= config.modules.length ? translations.restart : translations.add_module;
    });

    const automationsList = queryOne('#automation-list', rootElement);
    const automationLog = queryOne('#automation-log', rootElement);
    const manualCounter = queryOne('#manual-counter', rootElement);
    const totalAutomations = config.automations.length;
    const createIdleItem = () => createElement('li', 'is-idle', translations.all_manual);
    automationsList.replaceChildren(
        ...config.automations.map((automation, automationIndex) => {
            const automationButton = createElement('button', 'method-auto');
            automationButton.type = 'button';
            automationButton.dataset.itemIndex = automationIndex;
            automationButton.setAttribute('aria-pressed', 'false');
            const textWrapper = createElement('span', 'method-auto-text');
            textWrapper.append(
                createElement('b', '', automation.name),
                createElement('span', 'method-auto-pain', automation.pain),
                createElement('span', 'method-auto-fixed', translations.done_automatically),
            );
            const switchWrapper = createElement('span', 'method-auto-switch');
            switchWrapper.appendChild(createElement('i'));
            automationButton.append(textWrapper, switchWrapper);
            return automationButton;
        }),
    );
    automationLog.replaceChildren(createIdleItem());
    let clockMinutes = 8 * 60 + 2;
    const createTimestamp = () => {
        clockMinutes += 3 + Math.floor(Math.random() * 9);
        return (
            String(Math.floor(clockMinutes / 60) % 24).padStart(2, '0') +
            ':' +
            String(clockMinutes % 60).padStart(2, '0')
        );
    };
    const pushLogEntry = (automationIndex) => {
        automationLog.querySelector('.is-idle')?.remove();
        const automation = config.automations[automationIndex];
        const logItem = createElement('li');
        logItem.append(
            createElement('time', '', createTimestamp()),
            createElement('i', '', '✓'),
            createElement('span', '', automation.events[Math.floor(Math.random() * automation.events.length)]),
        );
        automationLog.prepend(logItem);
        requestAnimationFrame(() => logItem.classList.add('is-revealed'));
        while (automationLog.children.length > 4) {
            automationLog.lastChild.remove();
        }
    };
    const getActiveAutomationIndexes = () =>
        queryAll('.method-auto', automationsList)
            .filter((automationButton) => automationButton.getAttribute('aria-pressed') === 'true')
            .map((automationButton) => Number(automationButton.dataset.itemIndex));
    const renderManualCounter = (activeCount) => {
        manualCounter.textContent = translations.by_hand
            .replace(':left', String(totalAutomations - activeCount))
            .replace(':total', String(totalAutomations));
        manualCounter.classList.toggle('is-success', activeCount === totalAutomations);
    };
    renderManualCounter(0);
    queryAll('.method-auto', automationsList).forEach((automationButton) =>
        addListener(automationButton, 'click', () => {
            const isNowActive = automationButton.getAttribute('aria-pressed') !== 'true';
            automationButton.setAttribute('aria-pressed', String(isNowActive));
            if (isNowActive) {
                pushLogEntry(Number(automationButton.dataset.itemIndex));
            }
            const activeCount = getActiveAutomationIndexes().length;
            renderManualCounter(activeCount);
            if (!activeCount) {
                automationLog.replaceChildren(createIdleItem());
            }
        }),
    );
    timers.push(
        setInterval(() => {
            const activeIndexes = getActiveAutomationIndexes();
            if (!activeIndexes.length || prefersReducedMotion) {
                return;
            }
            const automationsRect = automationsList.getBoundingClientRect();
            if (automationsRect.bottom < 0 || automationsRect.top > innerHeight) {
                return;
            }
            pushLogEntry(activeIndexes[Math.floor(Math.random() * activeIndexes.length)]);
        }, 2400),
    );

    const setupFieldValidation = (fieldSelector, isValueValid, okMessage, badMessage) => {
        const fieldElement = queryOne(fieldSelector, rootElement);
        const inputElement = fieldElement.querySelector('input');
        const messageElement = fieldElement.querySelector('.method-field-message');
        addListener(inputElement, 'input', () => {
            const trimmedValue = inputElement.value.trim();
            fieldElement.classList.remove('is-valid', 'is-invalid');
            if (!trimmedValue) {
                messageElement.textContent = translations.write_something;
                return;
            }
            const isValid = isValueValid(trimmedValue);
            fieldElement.classList.add(isValid ? 'is-valid' : 'is-invalid');
            messageElement.textContent = isValid ? okMessage : badMessage;
        });
    };
    setupFieldValidation(
        '#field-email',
        (value) => /^[^\s@]+@[^\s@]+\.[a-z]{2,}$/i.test(value),
        translations.email_ok,
        translations.email_bad,
    );
    setupFieldValidation('#field-vat-number', (value) => /^\d{11}$/.test(value), translations.vat_ok, translations.vat_bad);

    const tabButtons = queryAll('.method-ui-tabs button', rootElement);
    tabButtons.forEach((tabButton) =>
        addListener(tabButton, 'click', () => {
            tabButtons.forEach((otherTab) => otherTab.classList.toggle('is-active', otherTab === tabButton));
            queryAll('.method-ui-pane', rootElement).forEach((paneElement) =>
                paneElement.classList.toggle('is-active', paneElement.dataset.paneIndex === tabButton.dataset.tabIndex),
            );
        }),
    );

    let draggedCard = null;
    const kanbanColumns = queryAll('.method-kanban-column', rootElement);
    const placeCardInColumn = (cardElement, columnElement) => {
        columnElement.appendChild(cardElement);
        cardElement.classList.toggle('is-done', columnElement.dataset.columnIndex === '2');
        cardElement.classList.remove('is-added');
        void cardElement.offsetWidth;
        cardElement.classList.add('is-added');
    };
    queryAll('.method-kanban-card', rootElement).forEach((cardElement) => {
        addListener(cardElement, 'dragstart', (event) => {
            draggedCard = cardElement;
            cardElement.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
            try {
                event.dataTransfer.setData('text/plain', 'x');
            } catch (_) {
                /* some browsers refuse setData */
            }
        });
        addListener(cardElement, 'dragend', () => {
            cardElement.classList.remove('is-dragging');
            kanbanColumns.forEach((columnElement) => columnElement.classList.remove('is-drop-target'));
            draggedCard = null;
        });
        addListener(cardElement, 'click', () =>
            placeCardInColumn(
                cardElement,
                kanbanColumns[(Number(cardElement.parentElement.dataset.columnIndex) + 1) % 3],
            ),
        );
    });
    kanbanColumns.forEach((columnElement) => {
        addListener(columnElement, 'dragover', (event) => {
            event.preventDefault();
            columnElement.classList.add('is-drop-target');
        });
        addListener(columnElement, 'dragleave', () => columnElement.classList.remove('is-drop-target'));
        addListener(columnElement, 'drop', (event) => {
            event.preventDefault();
            columnElement.classList.remove('is-drop-target');
            if (draggedCard) {
                placeCardInColumn(draggedCard, columnElement);
            }
        });
    });

    const signatureCanvas = queryOne('.method-phone-signature', rootElement);
    const signatureContext = signatureCanvas.getContext('2d');
    const sendButton = queryOne('.method-phone-send', rootElement);
    const checkboxes = queryAll('.method-phone-check', rootElement);
    let isSigned = false;
    let isSigning = false;
    let lastSignaturePoint = null;
    const updateSendButtonState = () => {
        sendButton.disabled = !(checkboxes.every((checkbox) => checkbox.classList.contains('is-active')) && isSigned);
    };
    const clearSignature = () => {
        signatureContext.fillStyle = '#F1F0EC';
        signatureContext.fillRect(0, 0, signatureCanvas.width, signatureCanvas.height);
        signatureContext.strokeStyle = '#C9C8C3';
        signatureContext.beginPath();
        signatureContext.moveTo(20, 96);
        signatureContext.lineTo(440, 96);
        signatureContext.stroke();
    };
    clearSignature();
    const getSignaturePoint = (event) => {
        const canvasRect = signatureCanvas.getBoundingClientRect();
        return [
            ((event.clientX - canvasRect.left) / canvasRect.width) * signatureCanvas.width,
            ((event.clientY - canvasRect.top) / canvasRect.height) * signatureCanvas.height,
        ];
    };
    addListener(signatureCanvas, 'pointerdown', (event) => {
        isSigning = true;
        lastSignaturePoint = getSignaturePoint(event);
        signatureCanvas.setPointerCapture(event.pointerId);
    });
    addListener(signatureCanvas, 'pointermove', (event) => {
        if (!isSigning) {
            return;
        }
        const currentPoint = getSignaturePoint(event);
        signatureContext.strokeStyle = '#0A0A0A';
        signatureContext.lineWidth = 3;
        signatureContext.lineCap = 'round';
        signatureContext.beginPath();
        signatureContext.moveTo(lastSignaturePoint[0], lastSignaturePoint[1]);
        signatureContext.lineTo(currentPoint[0], currentPoint[1]);
        signatureContext.stroke();
        lastSignaturePoint = currentPoint;
        isSigned = true;
        updateSendButtonState();
    });
    addListener(window, 'pointerup', () => (isSigning = false));
    checkboxes.forEach((checkbox) =>
        addListener(checkbox, 'click', () => {
            checkbox.classList.toggle('is-active');
            updateSendButtonState();
        }),
    );
    addListener(sendButton, 'click', () => {
        sendButton.textContent = translations.report_sent;
        sendButton.classList.add('is-sent');
        timers.push(
            setTimeout(() => {
                sendButton.textContent = translations.close_job;
                sendButton.classList.remove('is-sent');
                checkboxes.forEach((checkbox) => checkbox.classList.remove('is-active'));
                isSigned = false;
                clearSignature();
                updateSendButtonState();
            }, 2600),
        );
    });

    const configuratorSelection = { material: 'rovere', length: '160', delivery: 'std' };
    const materialOptions = { rovere: ['#B8895A', 0], noce: ['#6B4A33', 180], nero: ['#1A1A1A', 120] };
    const lengthOptions = { 120: [0.6, 980], 160: [0.8, 1240], 200: [1, 1520] };
    const drawConfigurator = () => {
        const objectElement = queryOne('#configurator-object', rootElement);
        objectElement.style.background = materialOptions[configuratorSelection.material][0];
        objectElement.style.width = lengthOptions[configuratorSelection.length][0] * 100 + '%';
        objectElement.style.borderColor = configuratorSelection.material === 'nero' ? '#3A3A3A' : 'transparent';
        queryOne('#configurator-dimensions', rootElement).textContent = configuratorSelection.length + ' cm';
        const totalPrice =
            lengthOptions[configuratorSelection.length][1] +
            materialOptions[configuratorSelection.material][1] +
            (configuratorSelection.delivery === 'exp' ? 90 : 0);
        queryOne('#configurator-price', rootElement).textContent = '€ ' + totalPrice.toLocaleString(config.locale);
    };
    queryAll('.method-config-row', rootElement).forEach((optionRow) =>
        queryAll('button', optionRow).forEach((optionButton) =>
            addListener(optionButton, 'click', () => {
                queryAll('button', optionRow).forEach((otherButton) =>
                    otherButton.classList.toggle('is-active', otherButton === optionButton),
                );
                configuratorSelection[optionRow.dataset.optionGroup] = optionButton.dataset.optionValue;
                drawConfigurator();
            }),
        ),
    );
    drawConfigurator();

    siteRuntime.frameCallbacks.push(() => {
        if (!hasRunTests && testsSection.getBoundingClientRect().top < innerHeight * 0.7) {
            hasRunTests = true;
            runTests();
        }
    });

    return () => {
        isAlive = false;
        abortController.abort();
        timers.forEach((timerId) => {
            clearTimeout(timerId);
            clearInterval(timerId);
        });
    };
}
