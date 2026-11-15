export default function (canvasElement, siteRuntime) {
    const { pointer, hasFinePointer, prefersReducedMotion } = siteRuntime;
    const heroElement = canvasElement.parentElement;
    const canvasContext = canvasElement.getContext('2d');
    const errorCode = canvasElement.dataset.code ?? '404';
    let canvasWidth = 0;
    let canvasHeight = 0;
    let particles = [];
    let isPointerPressed = false;

    const buildParticles = () => {
        const heroBounds = heroElement.getBoundingClientRect();
        const devicePixelRatioCapped = Math.min(devicePixelRatio, 2);
        canvasWidth = heroBounds.width;
        canvasHeight = heroBounds.height;
        canvasElement.width = canvasWidth * devicePixelRatioCapped;
        canvasElement.height = canvasHeight * devicePixelRatioCapped;
        canvasElement.style.width = canvasWidth + 'px';
        canvasElement.style.height = canvasHeight + 'px';
        canvasContext.setTransform(devicePixelRatioCapped, 0, 0, devicePixelRatioCapped, 0, 0);
        const isMobileViewport = canvasWidth < 900;
        const fontSize = isMobileViewport ? canvasWidth * 0.42 : Math.min(canvasWidth * 0.25, 380);
        const sampleStep = isMobileViewport ? 7 : 9;
        const offscreenCanvas = document.createElement('canvas');
        const offscreenWidth = Math.ceil(fontSize * 2.4);
        const offscreenHeight = Math.ceil(fontSize * 1.05);
        offscreenCanvas.width = offscreenWidth;
        offscreenCanvas.height = offscreenHeight;
        const offscreenContext = offscreenCanvas.getContext('2d');
        offscreenContext.fillStyle = '#fff';
        offscreenContext.font = `900 ${fontSize}px Archivo, Arial Black, sans-serif`;
        offscreenContext.textBaseline = 'middle';
        offscreenContext.textAlign = 'center';
        offscreenContext.fillText(errorCode, offscreenWidth / 2, offscreenHeight / 2);
        const pixelData = offscreenContext.getImageData(0, 0, offscreenWidth, offscreenHeight).data;
        const originX = isMobileViewport ? (canvasWidth - offscreenWidth) / 2 : canvasWidth - offscreenWidth - canvasWidth * 0.04;
        const originY = isMobileViewport ? canvasHeight * 0.1 : canvasHeight * 0.07;
        const previousParticles = particles;
        particles = [];
        for (let y = 0; y < offscreenHeight; y += sampleStep) {
            for (let x = 0; x < offscreenWidth; x += sampleStep) {
                if (pixelData[(y * offscreenWidth + x) * 4 + 3] < 128) {
                    continue;
                }
                const previousParticle = previousParticles[particles.length];
                particles.push({
                    homeX: originX + x,
                    homeY: originY + y,
                    x: previousParticle ? previousParticle.x : originX + x + (Math.random() - 0.5) * canvasWidth,
                    y: previousParticle ? previousParticle.y : -Math.random() * canvasHeight,
                    velocityX: 0,
                    velocityY: 0,
                    isAccent: Math.random() < 0.07,
                    size: sampleStep - 3,
                });
            }
        }
    };

    const handlePointerDown = (event) => {
        if (!event.target.closest('a, button')) {
            isPointerPressed = true;
        }
    };
    const handlePointerUp = () => {
        isPointerPressed = false;
    };

    buildParticles();
    addEventListener('resize', buildParticles);
    addEventListener('pointerdown', handlePointerDown);
    addEventListener('pointerup', handlePointerUp);
    document.fonts && document.fonts.ready.then(buildParticles);

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = heroElement.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const pointerX = pointer.smoothX - heroBounds.left;
        const pointerY = pointer.smoothY - heroBounds.top;
        const isPointerLive = hasFinePointer && pointer.hasMoved;
        const influenceRadius = isPointerPressed ? 220 : 110;
        const dampingFactor = Math.pow(0.004, deltaSeconds);
        canvasContext.clearRect(0, 0, canvasWidth, canvasHeight);
        for (const particle of particles) {
            let accelerationX = (particle.homeX - particle.x) * 30;
            let accelerationY = (particle.homeY - particle.y) * 30;
            if (isPointerLive) {
                const offsetX = particle.x - pointerX;
                const offsetY = particle.y - pointerY;
                const distance = Math.hypot(offsetX, offsetY);
                if (distance < influenceRadius && distance > 0.1) {
                    const proximityFactor = 1 - distance / influenceRadius;
                    accelerationX += (offsetX / distance) * proximityFactor * (isPointerPressed ? 26000 : 9000);
                    accelerationY +=
                        (offsetY / distance) * proximityFactor * (isPointerPressed ? 26000 : 9000) +
                        proximityFactor * 3000;
                }
            }
            particle.velocityX = (particle.velocityX + accelerationX * deltaSeconds) * dampingFactor;
            particle.velocityY = (particle.velocityY + accelerationY * deltaSeconds) * dampingFactor;
            if (prefersReducedMotion) {
                particle.x = particle.homeX;
                particle.y = particle.homeY;
            } else {
                particle.x += particle.velocityX * deltaSeconds;
                particle.y += particle.velocityY * deltaSeconds;
            }
            const displacementFactor = Math.min(
                1,
                Math.hypot(particle.x - particle.homeX, particle.y - particle.homeY) / 30,
            );
            canvasContext.fillStyle =
                particle.isAccent || displacementFactor > 0.5
                    ? '#FF4A1C'
                    : `rgba(241,240,236,${(0.9 - displacementFactor * 0.4).toFixed(2)})`;
            canvasContext.fillRect(particle.x, particle.y, particle.size, particle.size);
        }
    });

    return () => {
        removeEventListener('resize', buildParticles);
        removeEventListener('pointerdown', handlePointerDown);
        removeEventListener('pointerup', handlePointerUp);
    };
}
