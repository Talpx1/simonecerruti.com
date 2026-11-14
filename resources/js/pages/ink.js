export default function (canvas, SC) {
    const { reduce, fine, mouse } = SC;
    const context = canvas.getContext('2d');
    const hero = canvas.parentElement;
    let width = 0;
    let height = 0;
    let lines = [];
    let cursor = 0;

    const build = () => {
        lines = [];
        const column_width = Math.min(420, width * 0.3);
        const x_start = width - column_width - width * 0.05;
        let y = height * 0.12;
        while (y < height * 0.86) {
            const paragraph = 3 + Math.floor(Math.random() * 4);
            for (let i = 0; i < paragraph && y < height * 0.86; i++) {
                let x = x_start;
                const words = [];
                const end = x_start + column_width * (i === paragraph - 1 ? 0.4 + Math.random() * 0.4 : 0.92 + Math.random() * 0.08);
                while (x < end) {
                    const word_width = 14 + Math.random() * 46;
                    words.push([x, Math.min(word_width, end - x)]);
                    x += word_width + 7;
                }
                lines.push({ y, words, hot: 0 });
                y += 17;
            }
            y += 22;
        }
    };

    const size = () => {
        const box = hero.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio, 2);
        width = box.width;
        height = box.height;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;
        context.setTransform(dpr, 0, 0, dpr, 0, 0);
        build();
    };

    size();
    window.addEventListener('resize', size);

    const total = () => lines.reduce((sum, line) => sum + line.words.length, 0);

    SC.onFrame.push((now, dt) => {
        const box = hero.getBoundingClientRect();
        if (box.bottom < 0) {
            return;
        }
        const mouse_x = mouse.x - box.left;
        const mouse_y = mouse.y - box.top;
        cursor += dt * (reduce ? 1e6 : 26);
        if (cursor > total() + 60) {
            cursor = 0;
        }
        context.clearRect(0, 0, width, height);
        let count = 0;
        let caret = null;
        lines.forEach((line) => {
            const near = fine && mouse.moved && Math.abs(mouse_y - line.y) < 9 && mouse_x > line.words[0][0] - 30 && mouse_x < line.words[line.words.length - 1][0] + 80;
            line.hot = near ? 1 : line.hot * Math.pow(0.35, dt);
            line.words.forEach((word) => {
                count++;
                if (count > cursor) {
                    return;
                }
                if (line.hot > 0.02) {
                    context.fillStyle = `rgba(255,74,28,${(0.85 * line.hot).toFixed(3)})`;
                    context.fillRect(word[0] - 2, line.y - 7, word[1] + 4, 12);
                }
                context.fillStyle = line.hot > 0.4 ? '#0A0A0A' : 'rgba(241,240,236,.22)';
                context.fillRect(word[0], line.y - 3, word[1], 5);
                caret = [word[0] + word[1] + 3, line.y];
            });
        });
        if (caret && Math.floor(now / 450) % 2 === 0) {
            context.fillStyle = '#FF4A1C';
            context.fillRect(caret[0], caret[1] - 8, 3, 14);
        }
    });

    return () => window.removeEventListener('resize', size);
}
