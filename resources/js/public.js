const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const fine = window.matchMedia('(pointer: fine)').matches;
const clamp = (v, a, b) => Math.max(a, Math.min(b, v));
const lerp = (a, b, t) => a + (b - a) * t;
const smooth = (a, b, v) => {
    const t = clamp((v - a) / (b - a), 0, 1);
    return t * t * (3 - 2 * t);
};
const $ = (s, r) => (r || document).querySelector(s);
const $$ = (s, r) => Array.from((r || document).querySelectorAll(s));
const mouse = { x: innerWidth * 0.6, y: innerHeight * 0.5, sx: innerWidth * 0.6, sy: innerHeight * 0.5, moved: false };

const SC = (window.SC = { reduce, fine, clamp, lerp, smooth, $, $$, mouse, onFrame: [] });

const effects = import.meta.glob('./pages/*.js');

let page = emptyPage();
let cleanups = [];
let refreshed_main = null;
let logo_drawn = false;

function emptyPage() {
    return { letters: [], scrubs: [], mags: [], bar: null, navCta: null, heroCta: null, nav_shown: false };
}

function resetPage() {
    cleanups.forEach((fn) => {
        try {
            fn();
        } catch (e) {
            console.error(e);
        }
    });
    cleanups = [];
    SC.onFrame.length = 0;
    page = emptyPage();
}

function splitLetters() {
    $$('[data-split]').forEach((el, li) => {
        const text = el.textContent;
        el.textContent = '';
        el.setAttribute('aria-hidden', 'true');
        let i = 0;
        text.split(/(\s+)/).forEach((chunk) => {
            if (chunk === '') {
                return;
            }
            if (/^\s+$/.test(chunk)) {
                el.appendChild(document.createTextNode(' '));
                i += chunk.length;
                return;
            }
            const word = document.createElement('span');
            word.className = 'vw';
            for (const ch of chunk) {
                const s = document.createElement('span');
                s.className = 'vf';
                s.textContent = ch;
                s.style.animationDelay = li * 0.12 + i * 0.025 + 's';
                word.appendChild(s);
                page.letters.push({ el: s, w: 850, s: 100 });
                i++;
            }
            el.appendChild(word);
        });
    });
}

function bindMenu() {
    const burger = $('#burger');
    const mmenu = $('#mmenu');
    if (!burger || !mmenu) {
        return;
    }
    const set = (open) => {
        mmenu.classList.toggle('open', open);
        burger.setAttribute('aria-expanded', String(open));
        burger.textContent = open ? burger.dataset.labelClose : burger.dataset.labelOpen;
        document.body.style.overflow = open ? 'hidden' : '';
    };
    burger.addEventListener('click', () => set(!mmenu.classList.contains('open')));
    $$('a', mmenu).forEach((a) => a.addEventListener('click', () => set(false)));
    const onKey = (e) => {
        if (e.key === 'Escape') {
            set(false);
        }
    };
    document.addEventListener('keydown', onKey);
    cleanups.push(() => {
        document.removeEventListener('keydown', onKey);
        document.body.style.overflow = '';
    });
}

function bindCopyButtons() {
    $$('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const value = btn.dataset.copy;
            const label = btn.textContent;
            const done = (text) => {
                btn.textContent = text;
                setTimeout(() => {
                    btn.textContent = label;
                }, 1800);
            };
            if (navigator.clipboard) {
                navigator.clipboard.writeText(value).then(
                    () => done(btn.dataset.copied || value),
                    () => done(value),
                );
            } else {
                done(value);
            }
        });
    });
}

function bindReveals() {
    if ('IntersectionObserver' in window && !reduce) {
        const io = new IntersectionObserver(
            (entries) =>
                entries.forEach((e) => {
                    if (e.isIntersecting) {
                        e.target.classList.add('in');
                        io.unobserve(e.target);
                    }
                }),
            { rootMargin: '0px 0px -10% 0px' },
        );
        $$('.rv').forEach((el) => io.observe(el));
        cleanups.push(() => io.disconnect());
    } else {
        $$('.rv').forEach((el) => el.classList.add('in'));
    }
}

function bindScrubs() {
    page.scrubs = $$('.scrub p').map((p) => {
        const words = p.textContent.trim().split(/\s+/);
        p.textContent = '';
        const els = words.map((w, i) => {
            const s = document.createElement('span');
            s.className = 'wd' + (p.dataset.acc && new RegExp(p.dataset.acc).test(w) ? ' a' : '');
            s.textContent = w;
            p.appendChild(s);
            if (i < words.length - 1) {
                p.appendChild(document.createTextNode(' '));
            }
            return s;
        });
        if (reduce) {
            els.forEach((e) => e.classList.add('lit'));
        }
        return { p, els };
    });
}

function bindLogo() {
    const arm = () => {
        $$('.mark').forEach((m) => {
            m.classList.add('drawn');
            m.addEventListener('mouseenter', () => m.classList.add('flow'));
            m.addEventListener('animationend', () => m.classList.remove('flow'));
        });
    };
    if (logo_drawn) {
        arm();
        return;
    }
    logo_drawn = true;
    setTimeout(arm, 1900);
}

async function runEffects() {
    for (const el of $$('[data-fx]')) {
        const loader = effects[`./pages/${el.dataset.fx}.js`];
        if (!loader) {
            continue;
        }
        const module = await loader();
        if (!el.isConnected) {
            continue;
        }
        const cleanup = module.default(el, SC);
        if (typeof cleanup === 'function') {
            cleanups.push(cleanup);
        }
    }
}

function refresh() {
    const main = $('main');
    if (main && main === refreshed_main) {
        return;
    }
    refreshed_main = main;
    resetPage();

    if (fine && !reduce) {
        document.body.classList.add('has-cursor');
    } else {
        $$('.cur, .cur-dot').forEach((el) => (el.style.display = 'none'));
    }

    page.bar = $('.bar');
    page.navCta = $('.nav-cta');
    page.heroCta = $('.hero-cta');
    page.mags = $$('.mag');

    splitLetters();
    bindMenu();
    bindCopyButtons();
    bindReveals();
    bindScrubs();
    bindLogo();
    runEffects();
}

addEventListener(
    'pointermove',
    (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
        mouse.moved = true;
        const cur = $('.cur');
        if (!cur) {
            return;
        }
        const label = $('span', cur);
        const t = e.target.closest ? e.target.closest('[data-cursor], a, button') : null;
        if (t && t.dataset.cursor) {
            cur.classList.add('big');
            cur.classList.remove('hov');
            label.textContent = t.dataset.cursor;
        } else if (t) {
            cur.classList.add('hov');
            cur.classList.remove('big');
        } else {
            cur.classList.remove('big', 'hov');
        }
    },
    { passive: true },
);

let prevT = performance.now();

function frame(now) {
    const dt = Math.min(0.05, (now - prevT) / 1000);
    prevT = now;
    const vw = innerWidth;
    const vh = innerHeight;
    const sy = scrollY;
    const k = 1 - Math.pow(0.0008, dt);
    mouse.sx = lerp(mouse.sx, mouse.x, k * 1.6);
    mouse.sy = lerp(mouse.sy, mouse.y, k * 1.6);

    const cur = $('.cur');
    const dot = $('.cur-dot');
    if (cur) {
        cur.style.transform = `translate3d(${mouse.sx}px, ${mouse.sy}px, 0)`;
    }
    if (dot) {
        dot.style.transform = `translate3d(${mouse.x}px, ${mouse.y}px, 0)`;
    }

    const docH = document.documentElement.scrollHeight - vh;
    if (page.bar) {
        page.bar.style.transform = `scaleX(${docH > 0 ? sy / docH : 0})`;
    }

    if (page.navCta) {
        const show = page.heroCta ? page.heroCta.getBoundingClientRect().bottom < 0 : sy > vh * 0.6;
        if (show !== page.nav_shown) {
            page.nav_shown = show;
            page.navCta.classList.toggle('show', show);
            page.navCta.setAttribute('aria-hidden', String(!show));
            page.navCta.tabIndex = show ? 0 : -1;
        }
    }

    if (fine && !reduce) {
        page.mags.forEach((m) => {
            const b = m.getBoundingClientRect();
            const dx = mouse.x - (b.left + b.width / 2);
            const dy = mouse.y - (b.top + b.height / 2);
            const d = Math.hypot(dx, dy);
            const f = d < 140 ? 1 - d / 140 : 0;
            m.style.transform = `translate3d(${dx * f * 0.35}px, ${dy * f * 0.35}px, 0)`;
        });
    }

    if (fine && !reduce && mouse.moved) {
        for (const L of page.letters) {
            const b = L.el.getBoundingClientRect();
            if (b.bottom < -50 || b.top > vh + 50) {
                continue;
            }
            const d = Math.hypot(mouse.sx - (b.left + b.width / 2), mouse.sy - (b.top + b.height / 2));
            const f = smooth(260, 0, d);
            const tw = 850 - f * 650;
            const ts = 100 + f * 25;
            if (Math.abs(tw - L.w) > 2 || Math.abs(ts - L.s) > 0.5) {
                L.w = lerp(L.w, tw, 0.25);
                L.s = lerp(L.s, ts, 0.25);
                L.el.style.setProperty('--w', L.w.toFixed(0));
                L.el.style.setProperty('--s', L.s.toFixed(1));
            }
        }
    }

    page.scrubs.forEach((sc) => {
        if (reduce) {
            return;
        }
        const r = sc.p.getBoundingClientRect();
        const p = clamp((vh * 0.85 - r.top) / (r.height + vh * 0.35), 0, 1);
        const lit = Math.floor(p * sc.els.length * 1.08);
        sc.els.forEach((w, i) => w.classList.toggle('lit', i < lit));
    });

    SC.onFrame.forEach((fn) => fn(now, dt));
    requestAnimationFrame(frame);
}

document.addEventListener('livewire:navigating', resetPage);
document.addEventListener('livewire:navigated', refresh);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', refresh);
} else {
    refresh();
}

requestAnimationFrame(frame);
window.__revealReady = true;
