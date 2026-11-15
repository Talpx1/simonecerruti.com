import { initCustomScrollbar } from './custom-scrollbar';
import { initSmoothScroll } from './smooth-scroll';

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const hasFinePointer = window.matchMedia('(pointer: fine)').matches;
const clamp = (value, minimum, maximum) => Math.max(minimum, Math.min(maximum, value));
const linearInterpolate = (from, to, progress) => from + (to - from) * progress;
const smoothStep = (edgeStart, edgeEnd, value) => {
    const progress = clamp((value - edgeStart) / (edgeEnd - edgeStart), 0, 1);
    return progress * progress * (3 - 2 * progress);
};
const queryOne = (selector, root) => (root || document).querySelector(selector);
const queryAll = (selector, root) => Array.from((root || document).querySelectorAll(selector));
const pointer = {
    x: innerWidth * 0.6,
    y: innerHeight * 0.5,
    smoothX: innerWidth * 0.6,
    smoothY: innerHeight * 0.5,
    hasMoved: false,
};

const siteRuntime = (window.siteRuntime = {
    prefersReducedMotion,
    hasFinePointer,
    clamp,
    linearInterpolate,
    smoothStep,
    queryOne,
    queryAll,
    pointer,
    frameCallbacks: [],
});

const effectLoaders = import.meta.glob('./pages/*.js');

initCustomScrollbar();
initSmoothScroll();

let pageState = createEmptyPageState();
let cleanupCallbacks = [];
let lastRefreshedMain = null;
let hasLogoIntroPlayed = false;

function createEmptyPageState() {
    return {
        variableLetters: [],
        scrollLitStatements: [],
        magneticElements: [],
        scrollProgressBar: null,
        navigationCta: null,
        heroCta: null,
        isNavigationCtaShown: false,
    };
}

function resetPage() {
    cleanupCallbacks.forEach((cleanup) => {
        try {
            cleanup();
        } catch (error) {
            console.error(error);
        }
    });
    cleanupCallbacks = [];
    siteRuntime.frameCallbacks.length = 0;
    pageState = createEmptyPageState();
}

function splitVariableLetters() {
    queryAll('[data-variable-letters]').forEach((element, elementIndex) => {
        const text = element.textContent;
        element.textContent = '';
        element.setAttribute('aria-hidden', 'true');
        let letterIndex = 0;
        text.split(/(\s+)/).forEach((chunk) => {
            if (chunk === '') {
                return;
            }
            if (/^\s+$/.test(chunk)) {
                element.appendChild(document.createTextNode(' '));
                letterIndex += chunk.length;
                return;
            }
            const wordElement = document.createElement('span');
            wordElement.className = 'variable-word';
            for (const character of chunk) {
                const letterElement = document.createElement('span');
                letterElement.className = 'variable-letter';
                letterElement.textContent = character;
                letterElement.style.animationDelay = elementIndex * 0.12 + letterIndex * 0.025 + 's';
                wordElement.appendChild(letterElement);
                pageState.variableLetters.push({ element: letterElement, weight: 850, width: 100 });
                letterIndex++;
            }
            element.appendChild(wordElement);
        });
    });
}

function bindMobileMenu() {
    const burgerButton = queryOne('#mobile-menu-toggle');
    const mobileMenu = queryOne('#mobile-menu');
    if (!burgerButton || !mobileMenu) {
        return;
    }
    const setMenuOpen = (isOpen) => {
        mobileMenu.classList.toggle('is-open', isOpen);
        burgerButton.setAttribute('aria-expanded', String(isOpen));
        burgerButton.textContent = isOpen ? burgerButton.dataset.labelClose : burgerButton.dataset.labelOpen;
        document.body.style.overflow = isOpen ? 'hidden' : '';
        if (isOpen) {
            window.smoothScroller?.stop();
        } else {
            window.smoothScroller?.start();
        }
    };
    burgerButton.addEventListener('click', () => setMenuOpen(!mobileMenu.classList.contains('is-open')));
    queryAll('a', mobileMenu).forEach((link) => link.addEventListener('click', () => setMenuOpen(false)));
    const closeOnEscape = (event) => {
        if (event.key === 'Escape') {
            setMenuOpen(false);
        }
    };
    document.addEventListener('keydown', closeOnEscape);
    cleanupCallbacks.push(() => {
        document.removeEventListener('keydown', closeOnEscape);
        document.body.style.overflow = '';
    });
}

function bindCopyButtons() {
    queryAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', () => {
            const valueToCopy = button.dataset.copy;
            const originalLabel = button.textContent;
            const showFeedback = (feedbackText) => {
                button.textContent = feedbackText;
                setTimeout(() => {
                    button.textContent = originalLabel;
                }, 1800);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(valueToCopy).then(
                    () => showFeedback(button.dataset.copied || valueToCopy),
                    () => showFeedback(valueToCopy),
                );
            } else {
                showFeedback(valueToCopy);
            }
        });
    });
}

function bindScrollReveals() {
    if ('IntersectionObserver' in window && !prefersReducedMotion) {
        const revealObserver = new IntersectionObserver(
            (entries) =>
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        revealObserver.unobserve(entry.target);
                    }
                }),
            { rootMargin: '0px 0px -10% 0px' },
        );
        queryAll('.reveal').forEach((element) => revealObserver.observe(element));
        cleanupCallbacks.push(() => revealObserver.disconnect());
    } else {
        queryAll('.reveal').forEach((element) => element.classList.add('is-revealed'));
    }
}

function bindScrollLitStatements() {
    pageState.scrollLitStatements = queryAll('.scroll-lit p').map((paragraph) => {
        const words = paragraph.textContent.trim().split(/\s+/);
        paragraph.textContent = '';
        const wordElements = words.map((word, wordIndex) => {
            const wordElement = document.createElement('span');
            const isAccentWord = paragraph.dataset.accentPattern && new RegExp(paragraph.dataset.accentPattern).test(word);
            wordElement.className = 'scroll-lit-word' + (isAccentWord ? ' a' : '');
            wordElement.textContent = word;
            paragraph.appendChild(wordElement);
            if (wordIndex < words.length - 1) {
                paragraph.appendChild(document.createTextNode(' '));
            }
            return wordElement;
        });
        if (prefersReducedMotion) {
            wordElements.forEach((wordElement) => wordElement.classList.add('is-lit'));
        }
        return { paragraph, wordElements };
    });
}

function bindLogoAnimation() {
    const armLogos = () => {
        queryAll('.site-brand').forEach((logo) => {
            logo.classList.add('is-drawn');
            logo.addEventListener('mouseenter', () => logo.classList.add('is-flowing'));
            logo.addEventListener('animationend', () => logo.classList.remove('is-flowing'));
        });
    };
    if (hasLogoIntroPlayed) {
        armLogos();
        return;
    }
    hasLogoIntroPlayed = true;
    setTimeout(armLogos, 1900);
}

async function runPageEffects() {
    for (const effectElement of queryAll('[data-effect]')) {
        const loadEffect = effectLoaders[`./pages/${effectElement.dataset.effect}.js`];
        if (!loadEffect) {
            continue;
        }
        const effectModule = await loadEffect();
        if (!effectElement.isConnected) {
            continue;
        }
        const cleanup = effectModule.default(effectElement, siteRuntime);
        if (typeof cleanup === 'function') {
            cleanupCallbacks.push(cleanup);
        }
    }
}

function refreshPage() {
    const mainElement = queryOne('main');
    if (mainElement && mainElement === lastRefreshedMain) {
        return;
    }
    lastRefreshedMain = mainElement;
    resetPage();

    if (hasFinePointer && !prefersReducedMotion) {
        document.body.classList.add('has-custom-cursor');
    } else {
        queryAll('.cursor-ring, .cursor-dot').forEach((element) => (element.style.display = 'none'));
    }

    pageState.scrollProgressBar = queryOne('.scroll-progress');
    pageState.navigationCta = queryOne('.site-nav-cta');
    pageState.heroCta = queryOne('.home-hero-cta');
    pageState.magneticElements = queryAll('.magnetic-hover');

    splitVariableLetters();
    bindMobileMenu();
    bindCopyButtons();
    bindScrollReveals();
    bindScrollLitStatements();
    bindLogoAnimation();
    runPageEffects();
}

addEventListener(
    'pointermove',
    (event) => {
        pointer.x = event.clientX;
        pointer.y = event.clientY;
        pointer.hasMoved = true;
        const cursorRing = queryOne('.cursor-ring');
        if (!cursorRing) {
            return;
        }
        const cursorLabel = queryOne('span', cursorRing);
        const hoveredElement = event.target.closest ? event.target.closest('[data-cursor], a, button') : null;
        if (hoveredElement && hoveredElement.dataset.cursor) {
            cursorRing.classList.add('is-labelled');
            cursorRing.classList.remove('is-hovering');
            cursorLabel.textContent = hoveredElement.dataset.cursor;
        } else if (hoveredElement) {
            cursorRing.classList.add('is-hovering');
            cursorRing.classList.remove('is-labelled');
        } else {
            cursorRing.classList.remove('is-labelled', 'is-hovering');
        }
    },
    { passive: true },
);

let previousFrameTimestamp = performance.now();

function renderFrame(timestamp) {
    const deltaSeconds = Math.min(0.05, (timestamp - previousFrameTimestamp) / 1000);
    previousFrameTimestamp = timestamp;
    const viewportHeight = innerHeight;
    const scrollOffset = scrollY;
    const pointerSmoothing = 1 - Math.pow(0.0008, deltaSeconds);
    pointer.smoothX = linearInterpolate(pointer.smoothX, pointer.x, pointerSmoothing * 1.6);
    pointer.smoothY = linearInterpolate(pointer.smoothY, pointer.y, pointerSmoothing * 1.6);

    const cursorRing = queryOne('.cursor-ring');
    const cursorDot = queryOne('.cursor-dot');
    if (cursorRing) {
        cursorRing.style.transform = `translate3d(${pointer.smoothX}px, ${pointer.smoothY}px, 0)`;
    }
    if (cursorDot) {
        cursorDot.style.transform = `translate3d(${pointer.x}px, ${pointer.y}px, 0)`;
    }

    const scrollableHeight = document.documentElement.scrollHeight - viewportHeight;
    if (pageState.scrollProgressBar) {
        pageState.scrollProgressBar.style.transform = `scaleX(${scrollableHeight > 0 ? scrollOffset / scrollableHeight : 0})`;
    }

    if (pageState.navigationCta) {
        const shouldShow = pageState.heroCta ? pageState.heroCta.getBoundingClientRect().bottom < 0 : true;
        if (shouldShow !== pageState.isNavigationCtaShown) {
            pageState.isNavigationCtaShown = shouldShow;
            pageState.navigationCta.classList.toggle('is-visible', shouldShow);
            pageState.navigationCta.setAttribute('aria-hidden', String(!shouldShow));
            pageState.navigationCta.tabIndex = shouldShow ? 0 : -1;
        }
    }

    if (hasFinePointer && !prefersReducedMotion) {
        pageState.magneticElements.forEach((element) => {
            const bounds = element.getBoundingClientRect();
            const offsetX = pointer.x - (bounds.left + bounds.width / 2);
            const offsetY = pointer.y - (bounds.top + bounds.height / 2);
            const distance = Math.hypot(offsetX, offsetY);
            const pullStrength = distance < 140 ? 1 - distance / 140 : 0;
            element.style.transform = `translate3d(${offsetX * pullStrength * 0.35}px, ${offsetY * pullStrength * 0.35}px, 0)`;
        });
    }

    if (hasFinePointer && !prefersReducedMotion && pointer.hasMoved) {
        for (const letter of pageState.variableLetters) {
            const bounds = letter.element.getBoundingClientRect();
            if (bounds.bottom < -50 || bounds.top > viewportHeight + 50) {
                continue;
            }
            const distance = Math.hypot(
                pointer.smoothX - (bounds.left + bounds.width / 2),
                pointer.smoothY - (bounds.top + bounds.height / 2),
            );
            const proximity = smoothStep(260, 0, distance);
            const targetWeight = 850 - proximity * 650;
            const targetWidth = 100 + proximity * 25;
            if (Math.abs(targetWeight - letter.weight) > 2 || Math.abs(targetWidth - letter.width) > 0.5) {
                letter.weight = linearInterpolate(letter.weight, targetWeight, 0.25);
                letter.width = linearInterpolate(letter.width, targetWidth, 0.25);
                letter.element.style.setProperty('--letter-weight', letter.weight.toFixed(0));
                letter.element.style.setProperty('--letter-width', letter.width.toFixed(1));
            }
        }
    }

    pageState.scrollLitStatements.forEach((statement) => {
        if (prefersReducedMotion) {
            return;
        }
        const bounds = statement.paragraph.getBoundingClientRect();
        const scrollProgress = clamp((viewportHeight * 0.85 - bounds.top) / (bounds.height + viewportHeight * 0.35), 0, 1);
        const litWordCount = Math.floor(scrollProgress * statement.wordElements.length * 1.08);
        statement.wordElements.forEach((wordElement, wordIndex) =>
            wordElement.classList.toggle('is-lit', wordIndex < litWordCount),
        );
    });

    siteRuntime.frameCallbacks.forEach((callback) => callback(timestamp, deltaSeconds));
    requestAnimationFrame(renderFrame);
}

document.addEventListener('livewire:navigating', resetPage);
document.addEventListener('livewire:navigated', refreshPage);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', refreshPage);
} else {
    refreshPage();
}

requestAnimationFrame(renderFrame);
window.__revealReady = true;
