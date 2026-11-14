export default function (el, SC) {
    const { $, $$, clamp } = SC;
    const body = $('.art-body', el);
    const bar = $('.art-prog i', el);
    const links = $$('.toc a', el);
    const headings = links.map((link) => document.getElementById(link.getAttribute('href').slice(1)));
    const timers = [];

    SC.onFrame.push(() => {
        if (!body) {
            return;
        }
        const rect = body.getBoundingClientRect();
        const progress = clamp((window.innerHeight * 0.3 - rect.top) / (rect.height - window.innerHeight * 0.4), 0, 1);
        if (bar) {
            bar.style.transform = `scaleY(${progress.toFixed(4)})`;
        }
        let active = 0;
        headings.forEach((heading, index) => {
            if (heading && heading.getBoundingClientRect().top < window.innerHeight * 0.35) {
                active = index;
            }
        });
        links.forEach((link, index) => link.classList.toggle('on', index === active));
    });

    const copy_buttons = $$('[data-copy-link]', el);
    const on_copy = (event) => {
        const button = event.currentTarget;
        const done = () => {
            button.classList.add('on');
            timers.push(setTimeout(() => button.classList.remove('on'), 1800));
        };
        if (navigator.clipboard) {
            navigator.clipboard.writeText(button.dataset.copyLink).then(done, () => {});
        }
    };
    copy_buttons.forEach((button) => button.addEventListener('click', on_copy));

    return () => {
        timers.forEach(clearTimeout);
        copy_buttons.forEach((button) => button.removeEventListener('click', on_copy));
    };
}
