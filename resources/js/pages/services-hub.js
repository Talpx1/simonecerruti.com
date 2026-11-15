export default function (root, siteRuntime) {
    const { queryOne, queryAll, clamp, prefersReducedMotion } = siteRuntime;
    const config = JSON.parse(root.dataset.config || '{}');
    const routes = queryOne('#services-routes-canvas');
    const verdict = queryOne('#services-verdict', root);
    const verdictLink = queryOne('#services-verdict-link', root);
    const cards = queryAll('.services-card', root);
    const flip = queryOne('#services-compare', root);
    const switches = queryAll('.segmented-toggle button', root);
    const removeListenerCallbacks = [];
    let isAutoSwitchEnabled = true;
    let switchTimer = null;

    const addTrackedListener = (target, eventType, handler) => {
        target.addEventListener(eventType, handler);
        removeListenerCallbacks.push(() => target.removeEventListener(eventType, handler));
    };

    const escapeHtml = (text) =>
        text.replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);

    const goals = queryAll('.services-goal', root);
    goals.forEach((goal) =>
        addTrackedListener(goal, 'click', () => {
            goals.forEach((otherGoal) => otherGoal.classList.toggle('is-active', otherGoal === goal));
            const targetArea = goal.dataset.to;
            const goalLabel = goal.querySelector('span').textContent;
            verdict.innerHTML = config.verdict
                .replace(':goal', escapeHtml(goalLabel))
                .replace(':service', escapeHtml(config.names[targetArea]));
            verdictLink.hidden = false;
            verdictLink.href = '#area-' + targetArea;
            cards.forEach((card) => card.classList.toggle('is-picked', card.id === 'area-' + targetArea));
            if (routes) {
                routes.dataset.route = String(config.channels[targetArea]);
            }
        }),
    );

    siteRuntime.frameCallbacks.push(() => {
        if (prefersReducedMotion || innerWidth < 900) {
            return;
        }
        cards.forEach((card, cardIndex) => {
            const nextCard = cards[cardIndex + 1];
            if (!nextCard) {
                return;
            }
            const coverProgress = clamp(1 - (nextCard.getBoundingClientRect().top - 96) / (innerHeight * 0.8), 0, 1);
            card.style.transform = `scale(${(1 - coverProgress * 0.06).toFixed(4)})`;
            card.style.filter = `brightness(${(1 - coverProgress * 0.5).toFixed(3)})`;
        });
    });

    const setMode = (mode) => {
        flip.dataset.mode = mode;
        switches.forEach((switchButton) => {
            switchButton.classList.toggle('is-active', switchButton.dataset.mode === mode);
            switchButton.classList.toggle('is-accent', switchButton.dataset.mode === 'custom');
        });
    };
    switches.forEach((switchButton) =>
        addTrackedListener(switchButton, 'click', () => {
            isAutoSwitchEnabled = false;
            setMode(switchButton.dataset.mode);
        }),
    );
    siteRuntime.frameCallbacks.push(() => {
        if (!isAutoSwitchEnabled) {
            return;
        }
        if (flip.getBoundingClientRect().top < innerHeight * 0.35) {
            isAutoSwitchEnabled = false;
            switchTimer = setTimeout(() => setMode('custom'), 500);
        }
    });

    return () => {
        removeListenerCallbacks.forEach((removeListener) => removeListener());
        clearTimeout(switchTimer);
    };
}
