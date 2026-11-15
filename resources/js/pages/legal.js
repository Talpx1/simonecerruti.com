export default function (rootElement, siteRuntime) {
    const { clamp } = siteRuntime;
    const articleBody = rootElement.querySelector('.article-body');
    const progressBar = rootElement.querySelector('.article-progress i');
    const tocLinks = [...rootElement.querySelectorAll('.article-toc a')];
    const headingElements = tocLinks.map((tocLink) => document.getElementById(tocLink.getAttribute('href').slice(1)));

    siteRuntime.frameCallbacks.push(() => {
        const bodyRect = articleBody.getBoundingClientRect();
        const progress = clamp((innerHeight * 0.3 - bodyRect.top) / (bodyRect.height - innerHeight * 0.4), 0, 1);
        progressBar.style.transform = `scaleY(${progress.toFixed(4)})`;
        let activeIndex = 0;
        headingElements.forEach((headingElement, headingIndex) => {
            if (headingElement && headingElement.getBoundingClientRect().top < innerHeight * 0.35) {
                activeIndex = headingIndex;
            }
        });
        tocLinks.forEach((tocLink, linkIndex) => tocLink.classList.toggle('is-active', linkIndex === activeIndex));
    });
}
