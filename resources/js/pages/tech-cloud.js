export default function (element, siteRuntime) {
    const { prefersReducedMotion, clamp } = siteRuntime;
    const items = Array.from(element.querySelectorAll('span'));

    siteRuntime.frameCallbacks.push(() => {
        const bounds = element.getBoundingClientRect();
        const scrollProgress = clamp((innerHeight * 0.9 - bounds.top) / (bounds.height + innerHeight * 0.3), 0, 1);
        const litCount = Math.floor(scrollProgress * items.length * 1.1);
        items.forEach((item, itemIndex) => item.classList.toggle('is-lit', prefersReducedMotion || itemIndex < litCount));
    });
}
