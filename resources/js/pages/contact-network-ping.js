export default function (canvas, siteRuntime) {
    const { pointer, hasFinePointer, prefersReducedMotion } = siteRuntime;
    const hero = canvas.parentElement;
    const label = hero.dataset.pingLabel ?? '';
    const context = canvas.getContext('2d');
    let width = 0;
    let height = 0;
    let nodePosition = [0, 0];
    let packets = [];
    let rings = [];
    let lastSentPosition = null;
    let idleSeconds = 0;
    let ringSeconds = 0;

    const resizeCanvas = () => {
        const heroBounds = hero.getBoundingClientRect();
        const pixelRatio = Math.min(devicePixelRatio, 2);
        width = heroBounds.width;
        height = heroBounds.height;
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        nodePosition = [width * 0.8, height * 0.34];
    };
    resizeCanvas();
    addEventListener('resize', resizeCanvas);

    const sendPacket = (originX, originY) => {
        const controlX = (originX + nodePosition[0]) / 2 + (Math.random() - 0.5) * 160;
        const controlY = Math.min(originY, nodePosition[1]) - 80 - Math.random() * 120;
        packets.push({ origin: [originX, originY], control: [controlX, controlY], progress: 0, speed: 0.5 + Math.random() * 0.35 });
    };

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = hero.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const pointerX = pointer.x - heroBounds.left;
        const pointerY = pointer.y - heroBounds.top;
        if (hasFinePointer && pointer.hasMoved && pointerX > 0 && pointerY > 0 && pointerX < width && pointerY < height) {
            if (!lastSentPosition || Math.hypot(pointerX - lastSentPosition[0], pointerY - lastSentPosition[1]) > 46) {
                sendPacket(pointerX, pointerY);
                lastSentPosition = [pointerX, pointerY];
            }
        }
        idleSeconds += deltaSeconds;
        if (idleSeconds > 0.7 && !prefersReducedMotion) {
            idleSeconds = 0;
            sendPacket(Math.random() < 0.5 ? -10 : Math.random() * width * 0.5, height * (0.2 + Math.random() * 0.8));
        }
        ringSeconds += deltaSeconds;
        if (ringSeconds > 2.2) {
            ringSeconds = 0;
            rings.push({ radius: 8, alpha: 1 });
        }
        context.clearRect(0, 0, width, height);
        context.fillStyle = 'rgba(241,240,236,.06)';
        for (let gridY = 16; gridY < height; gridY += 28) {
            for (let gridX = 16; gridX < width; gridX += 28) {
                context.fillRect(gridX, gridY, 1.5, 1.5);
            }
        }
        packets = packets.filter((packet) => {
            packet.progress += deltaSeconds * packet.speed * (prefersReducedMotion ? 4 : 1);
            if (packet.progress >= 1) {
                rings.push({ radius: 6, alpha: 0.7 });
                return false;
            }
            const getCurvePoint = (curveProgress) => {
                const inverseProgress = 1 - curveProgress;
                return [
                    inverseProgress * inverseProgress * packet.origin[0] + 2 * inverseProgress * curveProgress * packet.control[0] + curveProgress * curveProgress * nodePosition[0],
                    inverseProgress * inverseProgress * packet.origin[1] + 2 * inverseProgress * curveProgress * packet.control[1] + curveProgress * curveProgress * nodePosition[1],
                ];
            };
            context.strokeStyle = 'rgba(241,240,236,.12)';
            context.lineWidth = 1;
            context.beginPath();
            const trailStart = Math.max(0, packet.progress - 0.25);
            for (let trailProgress = trailStart; trailProgress <= packet.progress; trailProgress += 0.02) {
                const trailPoint = getCurvePoint(trailProgress);
                trailProgress === trailStart ? context.moveTo(trailPoint[0], trailPoint[1]) : context.lineTo(trailPoint[0], trailPoint[1]);
            }
            context.stroke();
            const headPoint = getCurvePoint(packet.progress);
            context.fillStyle = '#FF4A1C';
            context.fillRect(headPoint[0] - 2, headPoint[1] - 2, 4, 4);
            return true;
        });
        rings = rings.filter((ring) => {
            ring.radius += deltaSeconds * 90;
            ring.alpha -= deltaSeconds * 0.45;
            if (ring.alpha <= 0) {
                return false;
            }
            context.strokeStyle = `rgba(255,74,28,${ring.alpha.toFixed(3)})`;
            context.lineWidth = 1.2;
            context.beginPath();
            context.arc(nodePosition[0], nodePosition[1], ring.radius, 0, Math.PI * 2);
            context.stroke();
            return true;
        });
        context.fillStyle = '#FF4A1C';
        context.fillRect(nodePosition[0] - 6, nodePosition[1] - 6, 12, 12);
        context.font = '700 11px Archivo, Arial';
        context.fillStyle = 'rgba(241,240,236,.6)';
        context.textAlign = 'left';
        context.fillText(label, nodePosition[0] + 18, nodePosition[1] + 4);
    });

    return () => removeEventListener('resize', resizeCanvas);
}
