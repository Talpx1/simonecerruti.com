export default function (el, SC) {
    const labels = JSON.parse(el.dataset.labels || '{}');
    const zoom_selector = 'img:not(.gallery img)';
    let overlay = null;
    let items = [];
    let current = 0;
    let last_focus = null;
    let touch_start = null;

    const describe = (img) => {
        const caption = img.closest('figure')?.querySelector('figcaption');
        return (caption ? caption.textContent : img.getAttribute('alt')) || '';
    };

    const show = (index) => {
        current = (index + items.length) % items.length;
        const img = items[current];
        const view = overlay.querySelector('img');
        view.src = img.currentSrc || img.src;
        view.alt = img.alt;
        overlay.querySelector('.lb-c').textContent = describe(img);
        overlay.querySelector('.lb-k').textContent = items.length > 1 ? `${current + 1} / ${items.length}` : '';
    };

    const close = () => {
        if (!overlay) {
            return;
        }
        document.removeEventListener('keydown', on_key);
        overlay.remove();
        overlay = null;
        document.body.style.overflow = '';
        if (last_focus) {
            last_focus.focus();
        }
    };

    function on_key(event) {
        if (event.key === 'Escape') {
            close();
        } else if (event.key === 'ArrowLeft' && items.length > 1) {
            show(current - 1);
        } else if (event.key === 'ArrowRight' && items.length > 1) {
            show(current + 1);
        }
    }

    const open = (group, index) => {
        close();
        items = group;
        last_focus = document.activeElement;
        overlay = document.createElement('div');
        overlay.className = items.length > 1 ? 'lb' : 'lb single';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', labels.gallery || '');
        overlay.innerHTML =
            '<button type="button" class="lb-x"></button>' +
            '<button type="button" class="lb-n lb-p">←</button>' +
            '<figure><img alt=""><figcaption><span class="lb-c"></span><b class="lb-k"></b></figcaption></figure>' +
            '<button type="button" class="lb-n lb-nx">→</button>';
        overlay.querySelector('.lb-x').textContent = '✕';
        overlay.querySelector('.lb-x').setAttribute('aria-label', labels.close || '');
        overlay.querySelector('.lb-p').setAttribute('aria-label', labels.previous || '');
        overlay.querySelector('.lb-nx').setAttribute('aria-label', labels.next || '');
        overlay.querySelector('.lb-x').addEventListener('click', close);
        overlay.querySelector('.lb-p').addEventListener('click', () => show(current - 1));
        overlay.querySelector('.lb-nx').addEventListener('click', () => show(current + 1));
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                close();
            }
        });
        overlay.addEventListener('touchstart', (event) => {
            touch_start = event.touches[0].clientX;
        });
        overlay.addEventListener('touchend', (event) => {
            if (touch_start === null || items.length < 2) {
                return;
            }
            const delta = event.changedTouches[0].clientX - touch_start;
            if (Math.abs(delta) > 40) {
                show(current + (delta < 0 ? 1 : -1));
            }
            touch_start = null;
        });
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', on_key);
        show(index);
        overlay.querySelector('.lb-x').focus();
    };

    const open_from = (target) => {
        const gallery_figure = target.closest('.gallery figure');
        if (gallery_figure) {
            const group = SC.$$('img', gallery_figure.parentElement);
            open(group, group.indexOf(gallery_figure.querySelector('img')));
            return true;
        }
        const img = target.closest(zoom_selector);
        if (img && el.contains(img)) {
            open([img], 0);
            return true;
        }
        return false;
    };

    const on_click = (event) => {
        if (event.target.closest('a')) {
            return;
        }
        open_from(event.target);
    };

    const on_figure_key = (event) => {
        if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('.gallery figure')) {
            event.preventDefault();
            open_from(event.target);
        }
    };

    SC.$$('img', el).forEach((img) => {
        if (!img.closest('.gallery')) {
            img.classList.add('lb-zoom');
        }
    });
    SC.$$('.gallery figure', el).forEach((figure) => {
        figure.tabIndex = 0;
        figure.setAttribute('role', 'button');
        figure.setAttribute('aria-label', labels.open || '');
    });

    el.addEventListener('click', on_click);
    el.addEventListener('keydown', on_figure_key);

    return () => {
        el.removeEventListener('click', on_click);
        el.removeEventListener('keydown', on_figure_key);
        close();
    };
}
