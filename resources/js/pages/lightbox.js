export default function (rootElement, siteRuntime) {
    const labels = JSON.parse(rootElement.dataset.labels || '{}');
    const zoomSelector = 'img:not(.article-gallery img)';
    let overlayElement = null;
    let lightboxImages = [];
    let currentIndex = 0;
    let previouslyFocusedElement = null;
    let touchStartX = null;

    const describeImage = (image) => {
        const captionElement = image.closest('figure')?.querySelector('figcaption');
        return (captionElement ? captionElement.textContent : image.getAttribute('alt')) || '';
    };

    const showImage = (index) => {
        currentIndex = (index + lightboxImages.length) % lightboxImages.length;
        const currentImage = lightboxImages[currentIndex];
        const viewImage = overlayElement.querySelector('img');
        viewImage.src = currentImage.currentSrc || currentImage.src;
        viewImage.alt = currentImage.alt;
        overlayElement.querySelector('.lightbox-caption').textContent = describeImage(currentImage);
        overlayElement.querySelector('.lightbox-counter').textContent =
            lightboxImages.length > 1 ? `${currentIndex + 1} / ${lightboxImages.length}` : '';
    };

    const closeLightbox = () => {
        if (!overlayElement) {
            return;
        }
        document.removeEventListener('keydown', handleKeydown);
        overlayElement.remove();
        overlayElement = null;
        document.body.style.overflow = '';
        if (previouslyFocusedElement) {
            previouslyFocusedElement.focus();
        }
    };

    function handleKeydown(event) {
        if (event.key === 'Escape') {
            closeLightbox();
        } else if (event.key === 'ArrowLeft' && lightboxImages.length > 1) {
            showImage(currentIndex - 1);
        } else if (event.key === 'ArrowRight' && lightboxImages.length > 1) {
            showImage(currentIndex + 1);
        }
    }

    const openLightbox = (imageGroup, startIndex) => {
        closeLightbox();
        lightboxImages = imageGroup;
        previouslyFocusedElement = document.activeElement;
        overlayElement = document.createElement('div');
        overlayElement.className = lightboxImages.length > 1 ? 'lightbox' : 'lightbox lightbox--single';
        overlayElement.setAttribute('role', 'dialog');
        overlayElement.setAttribute('aria-modal', 'true');
        overlayElement.setAttribute('aria-label', labels.gallery || '');
        overlayElement.innerHTML =
            '<button type="button" class="lightbox-close"></button>' +
            '<button type="button" class="lightbox-navigation lightbox-previous">←</button>' +
            '<figure><img alt=""><figcaption><span class="lightbox-caption"></span><b class="lightbox-counter"></b></figcaption></figure>' +
            '<button type="button" class="lightbox-navigation lightbox-next">→</button>';
        overlayElement.querySelector('.lightbox-close').textContent = '✕';
        overlayElement.querySelector('.lightbox-close').setAttribute('aria-label', labels.close || '');
        overlayElement.querySelector('.lightbox-previous').setAttribute('aria-label', labels.previous || '');
        overlayElement.querySelector('.lightbox-next').setAttribute('aria-label', labels.next || '');
        overlayElement.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        overlayElement.querySelector('.lightbox-previous').addEventListener('click', () => showImage(currentIndex - 1));
        overlayElement.querySelector('.lightbox-next').addEventListener('click', () => showImage(currentIndex + 1));
        overlayElement.addEventListener('click', (event) => {
            if (event.target === overlayElement) {
                closeLightbox();
            }
        });
        overlayElement.addEventListener('touchstart', (event) => {
            touchStartX = event.touches[0].clientX;
        });
        overlayElement.addEventListener('touchend', (event) => {
            if (touchStartX === null || lightboxImages.length < 2) {
                return;
            }
            const horizontalDelta = event.changedTouches[0].clientX - touchStartX;
            if (Math.abs(horizontalDelta) > 40) {
                showImage(currentIndex + (horizontalDelta < 0 ? 1 : -1));
            }
            touchStartX = null;
        });
        document.body.appendChild(overlayElement);
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', handleKeydown);
        showImage(startIndex);
        overlayElement.querySelector('.lightbox-close').focus();
    };

    const openFromTarget = (targetElement) => {
        const galleryFigure = targetElement.closest('.article-gallery figure');
        if (galleryFigure) {
            const galleryImages = siteRuntime.queryAll('img', galleryFigure.parentElement);
            openLightbox(galleryImages, galleryImages.indexOf(galleryFigure.querySelector('img')));
            return true;
        }
        const zoomableImage = targetElement.closest(zoomSelector);
        if (zoomableImage && rootElement.contains(zoomableImage)) {
            openLightbox([zoomableImage], 0);
            return true;
        }
        return false;
    };

    const handleClick = (event) => {
        if (event.target.closest('a')) {
            return;
        }
        openFromTarget(event.target);
    };

    const handleFigureKeydown = (event) => {
        if ((event.key === 'Enter' || event.key === ' ') && event.target.matches('.article-gallery figure')) {
            event.preventDefault();
            openFromTarget(event.target);
        }
    };

    siteRuntime.queryAll('img', rootElement).forEach((image) => {
        if (!image.closest('.article-gallery')) {
            image.classList.add('lightbox-zoomable');
        }
    });
    siteRuntime.queryAll('.article-gallery figure', rootElement).forEach((figureElement) => {
        figureElement.tabIndex = 0;
        figureElement.setAttribute('role', 'button');
        figureElement.setAttribute('aria-label', labels.open || '');
    });

    rootElement.addEventListener('click', handleClick);
    rootElement.addEventListener('keydown', handleFigureKeydown);

    return () => {
        rootElement.removeEventListener('click', handleClick);
        rootElement.removeEventListener('keydown', handleFigureKeydown);
        closeLightbox();
    };
}
