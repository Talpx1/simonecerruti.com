export default function (el, SC) {
    const { clamp } = SC;
    const art = el.querySelector('.art-body');
    const bar = el.querySelector('.art-prog i');
    const links = [...el.querySelectorAll('.toc a')];
    const heads = links.map((a) => document.getElementById(a.getAttribute('href').slice(1)));

    SC.onFrame.push(() => {
        const r = art.getBoundingClientRect();
        const p = clamp((innerHeight * 0.3 - r.top) / (r.height - innerHeight * 0.4), 0, 1);
        bar.style.transform = `scaleY(${p.toFixed(4)})`;
        let on = 0;
        heads.forEach((h, i) => {
            if (h && h.getBoundingClientRect().top < innerHeight * 0.35) {
                on = i;
            }
        });
        links.forEach((a, i) => a.classList.toggle('on', i === on));
    });
}
