export default function (canvas, siteRuntime) {
    const { prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    const context = canvas.getContext('2d');
    const hero = canvas.parentElement;
    let width = 0;
    let height = 0;
    let lines = [];
    let revealCursor = 0;

    const buildLines = () => {
        lines = [];
        const columnWidth = Math.min(420, width * 0.3);
        const columnStartX = width - columnWidth - width * 0.05;
        let lineY = height * 0.12;
        while (lineY < height * 0.86) {
            const paragraphLineCount = 3 + Math.floor(Math.random() * 4);
            for (let i = 0; i < paragraphLineCount && lineY < height * 0.86; i++) {
                let wordX = columnStartX;
                const words = [];
                const lineEndX = columnStartX + columnWidth * (i === paragraphLineCount - 1 ? 0.4 + Math.random() * 0.4 : 0.92 + Math.random() * 0.08);
                while (wordX < lineEndX) {
                    const wordWidth = 14 + Math.random() * 46;
                    words.push([wordX, Math.min(wordWidth, lineEndX - wordX)]);
                    wordX += wordWidth + 7;
                }
                lines.push({ y: lineY, words, heat: 0 });
                lineY += 17;
            }
            lineY += 22;
        }
    };

    const resizeCanvas = () => {
        const heroBounds = hero.getBoundingClientRect();
        const pixelRatio = Math.min(window.devicePixelRatio, 2);
        width = heroBounds.width;
        height = heroBounds.height;
        canvas.width = width * pixelRatio;
        canvas.height = height * pixelRatio;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        buildLines();
    };

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    const countTotalWords = () => lines.reduce((sum, line) => sum + line.words.length, 0);

    siteRuntime.frameCallbacks.push((timestamp, deltaSeconds) => {
        const heroBounds = hero.getBoundingClientRect();
        if (heroBounds.bottom < 0) {
            return;
        }
        const pointerX = pointer.x - heroBounds.left;
        const pointerY = pointer.y - heroBounds.top;
        revealCursor += deltaSeconds * (prefersReducedMotion ? 1e6 : 26);
        if (revealCursor > countTotalWords() + 60) {
            revealCursor = 0;
        }
        context.clearRect(0, 0, width, height);
        let wordCount = 0;
        let caret = null;
        lines.forEach((line) => {
            const isPointerNear = hasFinePointer && pointer.hasMoved && Math.abs(pointerY - line.y) < 9 && pointerX > line.words[0][0] - 30 && pointerX < line.words[line.words.length - 1][0] + 80;
            line.heat = isPointerNear ? 1 : line.heat * Math.pow(0.35, deltaSeconds);
            line.words.forEach((word) => {
                wordCount++;
                if (wordCount > revealCursor) {
                    return;
                }
                if (line.heat > 0.02) {
                    context.fillStyle = `rgba(255,74,28,${(0.85 * line.heat).toFixed(3)})`;
                    context.fillRect(word[0] - 2, line.y - 7, word[1] + 4, 12);
                }
                context.fillStyle = line.heat > 0.4 ? '#0A0A0A' : 'rgba(241,240,236,.22)';
                context.fillRect(word[0], line.y - 3, word[1], 5);
                caret = [word[0] + word[1] + 3, line.y];
            });
        });
        if (caret && Math.floor(timestamp / 450) % 2 === 0) {
            context.fillStyle = '#FF4A1C';
            context.fillRect(caret[0], caret[1] - 8, 3, 14);
        }
    });

    return () => window.removeEventListener('resize', resizeCanvas);
}
