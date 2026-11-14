export default function (el, SC) {
    const { reduce, clamp } = SC;
    const items = Array.from(el.querySelectorAll('span'));

    SC.onFrame.push(() => {
        const r = el.getBoundingClientRect();
        const p = clamp((innerHeight * 0.9 - r.top) / (r.height + innerHeight * 0.3), 0, 1);
        const n = Math.floor(p * items.length * 1.1);
        items.forEach((s, i) => s.classList.toggle('lit', reduce || i < n));
    });
}
