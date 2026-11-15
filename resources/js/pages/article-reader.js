export default function (rootElement, siteRuntime) {
    const { queryOne, queryAll, clamp } = siteRuntime;
    const articleBody = queryOne('.article-body', rootElement);
    const progressBar = queryOne('.article-progress i', rootElement);
    const tocLinks = queryAll('.article-toc a', rootElement);
    const headingElements = tocLinks.map((tocLink) => document.getElementById(tocLink.getAttribute('href').slice(1)));
    const timers = [];

    siteRuntime.frameCallbacks.push(() => {
        if (!articleBody) {
            return;
        }
        const bodyRect = articleBody.getBoundingClientRect();
        const progress = clamp(
            (window.innerHeight * 0.3 - bodyRect.top) / (bodyRect.height - window.innerHeight * 0.4),
            0,
            1,
        );
        if (progressBar) {
            progressBar.style.transform = `scaleY(${progress.toFixed(4)})`;
        }
        let activeIndex = 0;
        headingElements.forEach((headingElement, headingIndex) => {
            if (headingElement && headingElement.getBoundingClientRect().top < window.innerHeight * 0.35) {
                activeIndex = headingIndex;
            }
        });
        tocLinks.forEach((tocLink, linkIndex) => tocLink.classList.toggle('is-active', linkIndex === activeIndex));
    });

    const copyButtons = queryAll('[data-copy-link]', rootElement);
    const handleCopyClick = (event) => {
        const copyButton = event.currentTarget;
        const markCopied = () => {
            copyButton.classList.add('is-active');
            timers.push(setTimeout(() => copyButton.classList.remove('is-active'), 1800));
        };
        if (navigator.clipboard) {
            navigator.clipboard.writeText(copyButton.dataset.copyLink).then(markCopied, () => {});
        }
    };
    copyButtons.forEach((copyButton) => copyButton.addEventListener('click', handleCopyClick));

    return () => {
        timers.forEach(clearTimeout);
        copyButtons.forEach((copyButton) => copyButton.removeEventListener('click', handleCopyClick));
    };
}
