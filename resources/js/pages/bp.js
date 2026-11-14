export default function (bp, SC) {
    const { lerp, smooth, reduce, fine, mouse } = SC;
    const cx = bp.getContext('2d');
    const hero = bp.parentElement;
    const born = performance.now();
    // seeded by the URL: every page gets its own drawing, always the same one
    let seed = 7; for (const ch of location.pathname + document.title) seed = (seed * 31 + ch.charCodeAt(0)) | 0;
    const rnd = () => { seed = (seed + 0x6D2B79F5) | 0; let t = Math.imul(seed ^ (seed >>> 15), 1 | seed); t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t; return ((t ^ (t >>> 14)) >>> 0) / 4294967296; };
    const R = (a, b) => a + rnd() * (b - a), I = (a, b) => Math.floor(R(a, b + 1)), pick = (a) => a[Math.floor(rnd() * a.length)];

    // ---- shape helpers (coords normalized to the device frame) ----
    const mk = () => { const S = []; return {
      S, r: (a, b, c, d, f) => S.push({ t: 'r', a, b, c, d, f }), // rect (f = filled hint)
      c: (x, y, r) => S.push({ t: 'c', x, y, r }),               // circle (avatar, pin, donut)
      l: (a, b, c, d) => S.push({ t: 'l', a, b, c, d }),         // line
      x: (a, b, c, d) => S.push({ t: 'x', a, b, c, d }),         // image placeholder
      p: (pts) => S.push({ t: 'p', pts }),                       // chart polyline
      txt: (a, b, w) => S.push({ t: 'l', a, b, c: a + w, d: b }) // a line of text
    }; };
    const chrome = (g, side) => { // header + optional sidebar; returns content box
      let x0 = .04, x1 = .96; const hh = R(.08, .12);
      if (side) { const sw = R(.15, .22), sx = side === 'l' ? 0 : 1 - sw; g.r(sx, 0, sx + sw, 1); side === 'l' ? x0 = sw + .04 : x1 = 1 - sw - .04;
        g.c(sx + .045, .07, .022); for (let i = 0, n = I(4, 7); i < n; i++) g.txt(sx + .03, .16 + i * .07, sw * R(.4, .7)); }
      g.r(side === 'l' ? x0 - .04 : 0, 0, side === 'r' ? x1 + .04 : 1, hh); g.c((side === 'r' ? x1 : 1) - .05, hh / 2, hh * .22);
      return [x0, hh + .06, x1, .94];
    };
    const DESK = {
      dashboard(g) { const [x0, y0, x1] = chrome(g, pick(['l', 'l', 'r'])), W2 = x1 - x0, k = I(3, 4), gp = .025, kw = (W2 - gp * (k - 1)) / k, kh = R(.12, .15);
        for (let i = 0; i < k; i++) { const a = x0 + i * (kw + gp); g.r(a, y0, a + kw, y0 + kh); g.txt(a + .02, y0 + kh * .4, kw * .5); g.txt(a + .02, y0 + kh * .7, kw * .3); }
        const y2 = y0 + kh + .05, sp = x0 + W2 * R(.58, .68), cb = y2 + R(.3, .36); g.r(x0, y2, sp, cb);
        const pts = []; for (let i = 0; i <= 9; i++) pts.push([x0 + .02 + i * (sp - x0 - .04) / 9, cb - .04 - R(.02, cb - y2 - .1)]); g.p(pts);
        g.r(sp + gp, y2, x1, cb); const dr = Math.min(x1 - sp - gp, cb - y2) * .32; g.c((sp + gp + x1) / 2, (y2 + cb) / 2, dr); g.c((sp + gp + x1) / 2, (y2 + cb) / 2, dr * .6);
        for (let y = cb + .05; y < .9; y += .07) { g.c(x0 + .015, y + .02, .012); g.txt(x0 + .04, y + .02, W2 * R(.3, .6)); g.txt(x1 - .1, y + .02, .08); } },
      table(g) { const [x0, y0, x1] = chrome(g, pick(['l', null])), W2 = x1 - x0; g.r(x0, y0, x0 + W2 * R(.3, .42), y0 + .07); g.r(x1 - W2 * .15, y0, x1, y0 + .07, 1);
        const det = rnd() < .55, xr = det ? x0 + W2 * R(.56, .64) : x1; g.l(x0, y0 + .12, xr, y0 + .12);
        for (let y = y0 + .16; y < .9; y += .07) { g.c(x0 + .02, y + .02, .014); g.txt(x0 + .05, y + .02, (xr - x0) * R(.2, .35)); g.txt(x0 + (xr - x0) * .5, y + .02, (xr - x0) * R(.1, .2)); g.r(xr - .09, y + .005, xr - .01, y + .035); }
        if (det) { g.r(xr + .03, y0 + .12, x1, .93); g.x(xr + .05, y0 + .15, x1 - .02, y0 + .38); for (let i = 0; i < 5; i++) g.txt(xr + .05, y0 + .45 + i * .06, (x1 - xr) * R(.4, .75)); } },
      form(g) { const [x0, y0, x1] = chrome(g, pick([null, 'l'])), W2 = x1 - x0, cols = I(1, 2), gp = .05, cw = (W2 * (cols === 1 ? .62 : 1) - gp * (cols - 1)) / cols;
        for (let c = 0; c < cols; c++) for (let y = y0; y < .72; y += .14) { const a = x0 + c * (cw + gp); g.txt(a, y + .01, cw * R(.2, .4)); g.r(a, y + .035, a + cw, y + .095); }
        for (let i = 0; i < 2; i++) { g.r(x0 + i * .18, .79, x0 + i * .18 + .06, .83); g.c(x0 + i * .18 + .04, .81, .014); g.txt(x0 + i * .18 + .08, .81, .07); }
        g.r(x1 - W2 * .22, .86, x1, .94, 1); },
      board(g) { const [x0, y0, x1] = chrome(g, pick([null, 'l'])), W2 = x1 - x0, k = I(3, 5), gp = .02, cw = (W2 - gp * (k - 1)) / k;
        for (let i = 0; i < k; i++) { const a = x0 + i * (cw + gp); g.txt(a, y0 + .015, cw * R(.4, .7)); let y = y0 + .05;
          for (let j = 0, n = I(1, 4); j < n && y < .82; j++) { const h = R(.1, .19); g.r(a, y, a + cw, Math.min(.94, y + h)); g.txt(a + .015, y + .035, cw * .6); g.c(a + cw - .025, Math.min(.94, y + h) - .03, .012); y += h + .025; } } },
      website(g) { g.r(0, 0, 1, .1); g.r(.04, .035, .14, .065); for (let i = 0, n = I(3, 5); i < n; i++) g.txt(.5 + i * .09, .05, .06); g.r(.86, .03, .96, .07, 1);
        const flip = rnd() < .5, tx = flip ? .54 : .06, ix = flip ? .06 : .52; for (let i = 0; i < 3; i++) g.r(tx, .2 + i * .075, tx + R(.25, .4), .25 + i * .075, 1);
        g.txt(tx, .47, .3); g.txt(tx, .51, .24); g.r(tx, .57, tx + .14, .63, 1); g.x(ix, .17, ix + .42, .64);
        const k = I(3, 4), gp = .03, kw = (.88 - gp * (k - 1)) / k; for (let i = 0; i < k; i++) { const a = .06 + i * (kw + gp); g.r(a, .71, a + kw, .94); g.c(a + .04, .77, .02); g.txt(a + .02, .84, kw * .7); g.txt(a + .02, .88, kw * .5); } },
      calendar(g) { const [x0, y0, x1] = chrome(g, pick(['l', null])), W2 = x1 - x0, cw = W2 / 7, rh = (.92 - y0 - .06) / 5;
        g.txt(x0, y0 + .015, W2 * .25); for (let i = 0; i < 7; i++) g.txt(x0 + i * cw + .01, y0 + .055, cw * .4);
        for (let r = 0; r < 5; r++) for (let c = 0; c < 7; c++) { const a = x0 + c * cw, b = y0 + .08 + r * rh; g.r(a, b, a + cw, b + rh); if (rnd() < .22) g.r(a + .008, b + rh * .45, a + cw * R(.6, .95), b + rh * .8, 1); } },
      chat(g) { const sw = R(.26, .34); g.r(0, 0, sw, 1); for (let i = 0; i < 7; i++) { g.c(.05, .08 + i * .12, .025); g.txt(.09, .07 + i * .12, sw * .5); g.txt(.09, .1 + i * .12, sw * .35); }
        g.r(sw, 0, 1, .1); g.c(sw + .05, .05, .025); let y = .16; while (y < .76) { const me = rnd() < .5, w = R(.18, .4), h = R(.06, .11); me ? g.r(.96 - w, y, .96, y + h, 1) : g.r(sw + .04, y, sw + .04 + w, y + h); y += h + .035; }
        g.r(sw + .04, .84, .88, .93); g.c(.93, .885, .028); },
      map(g) { const [x0, y0, x1] = chrome(g, null); g.r(x0, y0, x1, .94);
        for (let i = 0; i < 6; i++) { const pts = []; const vert = rnd() < .5; for (let j = 0; j <= 5; j++) { const t = j / 5; pts.push(vert ? [x0 + R(.1, .9) * (x1 - x0) * .2 + (x1 - x0) * (i / 6) + R(-.03, .03), y0 + t * (.94 - y0)] : [x0 + t * (x1 - x0), y0 + (i / 6) * (.94 - y0) + R(-.04, .04)]); } g.p(pts); }
        for (let i = 0, n = I(3, 6); i < n; i++) { const px = R(x0 + .08, x1 - .08), py = R(y0 + .08, .88); g.c(px, py, .018); g.c(px, py, .045); }
        g.r(x0 + .03, y0 + .03, x0 + .3, y0 + .3, 1); for (let i = 0; i < 3; i++) g.txt(x0 + .05, y0 + .09 + i * .06, .18); },
      flow(g) { const [x0, y0, x1] = chrome(g, pick([null, 'l'])), cols = I(3, 4), W2 = x1 - x0, nodes = [];
        for (let c = 0; c < cols; c++) { const n = I(1, 3); for (let j = 0; j < n; j++) { const cxn = x0 + (c + .5) * W2 / cols, cyn = y0 + (j + .5) * (.9 - y0) / n + R(-.03, .03), w = W2 / cols * .62; g.r(cxn - w / 2, cyn - .05, cxn + w / 2, cyn + .05, c === cols - 1 ? 1 : 0); nodes.push([c, cxn, cyn, w]); } }
        nodes.forEach(([c, a, b, w]) => nodes.filter(n => n[0] === c + 1).forEach(([, a2, b2, w2]) => { const m = (a + w / 2 + a2 - w2 / 2) / 2; g.p([[a + w / 2, b], [m, b], [m, b2], [a2 - w2 / 2, b2]]); })); },
      shop(g) { const [x0, y0, x1] = chrome(g, pick([null, 'l'])), W2 = x1 - x0, k = I(3, 4), gp = .025, cw = (W2 - gp * (k - 1)) / k, rows = I(1, 2), rh = (.92 - y0) / rows - .03;
        for (let r = 0; r < rows; r++) for (let i = 0; i < k; i++) { const a = x0 + i * (cw + gp), b = y0 + r * (rh + .03); g.x(a, b, a + cw, b + rh * .62); g.txt(a, b + rh * .72, cw * .7); g.txt(a, b + rh * .8, cw * .35); g.r(a + cw * .6, b + rh * .86, a + cw, b + rh, 1); } }
    };
    const PHONE = {
      list(g) { g.r(0, 0, 1, .09); for (let i = 0; i < 7; i++) { const y = .13 + i * .1; g.c(.13, y + .03, .05); g.txt(.25, y + .02, R(.3, .55)); g.txt(.25, y + .05, R(.2, .4)); g.l(.06, y + .085, .94, y + .085); } g.r(0, .92, 1, 1); },
      chat(g) { g.r(0, 0, 1, .09); g.c(.12, .045, .04); let y = .13; while (y < .8) { const me = rnd() < .5, w = R(.35, .7), h = R(.05, .09); me ? g.r(.94 - w, y, .94, y + h, 1) : g.r(.06, y, .06 + w, y + h); y += h + .03; } g.r(.06, .87, .8, .94); g.c(.88, .905, .045); },
      card(g) { g.x(0, 0, 1, .42); g.r(.06, .47, .8, .52, 1); g.txt(.06, .57, .6); g.txt(.06, .61, .45); for (let i = 0; i < 3; i++) g.c(.1 + i * .13, .69, .045); g.r(.06, .86, .94, .94, 1); },
      form(g) { g.r(0, 0, 1, .09); for (let i = 0; i < 5; i++) { const y = .14 + i * .13; g.txt(.06, y, R(.2, .4)); g.r(.06, y + .025, .94, y + .085); } g.r(.06, .84, .94, .92, 1); },
      stats(g) { g.r(0, 0, 1, .09); g.c(.5, .27, .17); g.c(.5, .27, .11); for (let i = 0; i < 2; i++) g.r(.06 + i * .46, .5, .48 + i * .46, .64); const pts = []; for (let i = 0; i <= 6; i++) pts.push([.06 + i * .88 / 6, R(.72, .86)]); g.p(pts); g.r(0, .92, 1, 1); }
    };

    // ---- composition: which devices, where ----
    const comp = I(0, 4), desk = pick(Object.keys(DESK)), ph = pick(Object.keys(PHONE)), ph2 = pick(Object.keys(PHONE));
    const build = (fn) => { const g = mk(); fn(g); return g.S; };
    let devices = [];
    const plan = (W, H) => {
      const mob = W < 900, area = mob ? [W * .06, H * .12, W * .94, H * .5] : [W * .42, H * .12, W * .96, H * .66];
      const aw = area[2] - area[0], ah = area[3] - area[1], out = [];
      const dev = (x, y, w, h, shapes, z, kind) => out.push({ x, y, w, h, S: shapes, z, kind });
      if (comp === 0 || mob) { const w = Math.min(aw, ah / .62), h = w * R(.56, .66); dev(area[2] - w, area[1], w, h, build(DESK[desk]), 4, 'd'); }
      else if (comp === 1) { const w = aw * .82, h = w * .6; dev(area[2] - w, area[1], w, h, build(DESK[desk]), 3, 'd'); const pw = w * .24; dev(area[2] - w - pw * .35, area[1] + h * .35, pw, pw * 2.05, build(PHONE[ph]), 12, 'p'); }
      else if (comp === 2) { const ph0 = Math.min(ah * 1.05, aw * .9), pw = ph0 / 2.05; dev(area[2] - pw * 2.25, area[1] + ah * .06, pw, ph0, build(PHONE[ph]), 6, 'p'); dev(area[2] - pw, area[1] - ah * .02, pw, ph0, build(PHONE[ph2 === ph ? 'list' : ph2]), 12, 'p'); }
      else if (comp === 3) { const h = Math.min(ah * .9, aw * .68), w = h * 1.38; dev(area[2] - w - 14, area[1] + 20, w, h, build(DESK[desk]), 4, 't'); }
      else { const w = aw * .7, h = w * .58; dev(area[0] + aw * .02, area[1] + ah * .08, w, h, build(DESK[desk]), 3, 'd'); const pw = w * .26; dev(area[2] - pw, area[1] - ah * .02, pw, pw * 2.05, build(PHONE[ph]), 12, 'p'); }
      return out;
    };
    let W = 0, H = 0;
    const size = () => { const b = hero.getBoundingClientRect(), dpr = Math.min(devicePixelRatio, 2);
      W = b.width; H = b.height; bp.width = W * dpr; bp.height = H * dpr; bp.style.width = W + 'px'; bp.style.height = H + 'px'; cx.setTransform(dpr, 0, 0, dpr, 0, 0);
      seed = seed; devices = plan(W, H); };
    // freeze the random stream for layout, so resizes keep the same drawing
    const seed0 = seed; const size2 = () => { seed = seed0; size(); };
    size2(); addEventListener('resize', size2);

    let px = W * .7, py = H * .4;
    const tick = (x, y, horiz) => { cx.moveTo(x, y); horiz ? cx.lineTo(x, y + 6) : cx.lineTo(x + 6, y); };
    const dashDraw = (p, len, draw) => { cx.setLineDash([len * p, len + 1]); draw(); cx.setLineDash([]); };
    SC.onFrame.push((now, dt) => {
      const b = hero.getBoundingClientRect(); if (b.bottom < 0) return;
      const t = (now - born) / 1000;
      let mx = mouse.sx - b.left, my = mouse.sy - b.top;
      if (!(fine && mouse.moved && my > 0 && my < H)) { mx = W * (.62 + .22 * Math.sin(t * .35)); my = H * (.4 + .2 * Math.sin(t * .53 + 1)); }
      px = lerp(px, mx, 1 - Math.pow(.0005, dt)); py = lerp(py, my, 1 - Math.pow(.0005, dt));
      cx.clearRect(0, 0, W, H); cx.lineWidth = 1;
      cx.strokeStyle = 'rgba(241,240,236,.035)'; cx.beginPath();
      for (let x = 0; x < W; x += 32) { cx.moveTo(x + .5, 0); cx.lineTo(x + .5, H); }
      for (let y = 0; y < H; y += 32) { cx.moveTo(0, y + .5); cx.lineTo(W, y + .5); }
      cx.stroke();
      cx.strokeStyle = 'rgba(241,240,236,.18)'; cx.beginPath();
      for (let x = 0; x < W; x += 8) if (Math.abs(x - px) < 60 || x % 64 === 0) tick(x + .5, 0, true);
      for (let y = 0; y < H; y += 8) if (Math.abs(y - py) < 60 || y % 64 === 0) tick(0, y + .5, false);
      cx.stroke();
      cx.strokeStyle = 'rgba(241,240,236,.1)'; cx.setLineDash([2, 6]); cx.beginPath(); cx.moveTo(px, 0); cx.lineTo(px, H); cx.moveTo(0, py); cx.lineTo(W, py); cx.stroke(); cx.setLineDash([]);
      const ox = px / W - .5, oy = py / H - .5; let hit = null, order = 0;
      devices.forEach((d) => {
        const X = d.x - ox * d.z, Y = d.y - oy * d.z, fx = (v) => X + v * d.w, fy = (v) => Y + v * d.h;
        const prog = (k) => reduce ? 1 : smooth(0, 1, (t * 1000 - 200 - k * 16) / 700);
        // device body: covers what is behind it, rounded for phones
        const p0 = prog(order++), rad = d.kind === 'p' ? d.w * .12 : d.kind === 't' ? 14 : 4, pad = d.kind === 'd' ? 0 : d.w * .05;
        if (p0 > 0) { cx.fillStyle = 'rgba(12,12,12,' + (.92 * p0).toFixed(2) + ')'; cx.beginPath(); cx.roundRect(X - pad, Y - pad * (d.kind === 'p' ? 2 : 1), d.w + pad * 2, d.h + pad * (d.kind === 'p' ? 4 : 2), rad); cx.fill();
          cx.strokeStyle = 'rgba(241,240,236,.24)'; dashDraw(p0, 2 * (d.w + d.h) + 400, () => cx.stroke()); }
        if (d.kind === 'd' && p0 > 0) { cx.strokeStyle = 'rgba(241,240,236,.18)'; cx.beginPath(); cx.rect(X, Y, d.w, d.h); cx.stroke(); }
        d.S.forEach((s) => {
          const p = prog(order++); if (p <= 0) return;
          cx.strokeStyle = 'rgba(241,240,236,.16)'; cx.fillStyle = 'rgba(241,240,236,.06)';
          if (s.t === 'r' || s.t === 'x') {
            const x = fx(s.a), y = fy(s.b), w = fx(s.c) - x, h = fy(s.d) - y;
            if (s.f) { cx.globalAlpha = p; cx.fillRect(x, y, w, h); cx.globalAlpha = 1; }
            dashDraw(p, 2 * (w + h), () => cx.strokeRect(x + .5, y + .5, w, h));
            if (s.t === 'x') { cx.beginPath(); cx.moveTo(x, y); cx.lineTo(x + w * p, y + h * p); cx.moveTo(x + w, y); cx.lineTo(x + w - w * p, y + h * p); cx.stroke(); }
            if (w > 8 && h > 6 && px > x && px < x + w && py > y && py < y + h && (!hit || w * h < hit.w * hit.h)) hit = { x, y, w, h };
          } else if (s.t === 'c') {
            const x = fx(s.x), y = fy(s.y), r = s.r * d.w; cx.beginPath(); cx.arc(x, y, r, -Math.PI / 2, -Math.PI / 2 + Math.PI * 2 * p); cx.stroke();
            if (r > 6 && Math.hypot(px - x, py - y) < r && !hit) hit = { x: x - r, y: y - r, w: r * 2, h: r * 2 };
          } else if (s.t === 'l') {
            cx.strokeStyle = 'rgba(241,240,236,.13)'; cx.lineWidth = Math.max(1.5, d.h * .012); cx.beginPath(); cx.moveTo(fx(s.a), fy(s.b)); cx.lineTo(fx(s.a + (s.c - s.a) * p), fy(s.b + (s.d - s.b) * p)); cx.stroke(); cx.lineWidth = 1;
          } else if (s.t === 'p') {
            const n = s.pts.length - 1, upto = p * n; cx.strokeStyle = 'rgba(255,74,28,.45)'; cx.beginPath();
            s.pts.forEach(([a, c], i) => { if (i > upto + 1) return; let xx = fx(a), yy = fy(c); if (i > upto) { const [a0, c0] = s.pts[i - 1], f = upto - (i - 1); xx = fx(a0 + (a - a0) * f); yy = fy(c0 + (c - c0) * f); } i ? cx.lineTo(xx, yy) : cx.moveTo(xx, yy); });
            cx.stroke();
          }
        });
      });
      if (hit) {
        const { x, y, w, h } = hit;
        cx.fillStyle = 'rgba(255,74,28,.07)'; cx.fillRect(x, y, w, h);
        cx.strokeStyle = '#FF4A1C'; cx.lineWidth = 1.2; cx.strokeRect(x + .5, y + .5, w, h); cx.fillStyle = '#FF4A1C';
        [[x, y], [x + w, y], [x, y + h], [x + w, y + h]].forEach(([a, c]) => cx.fillRect(a - 3, c - 3, 6, 6));
        cx.strokeStyle = 'rgba(255,74,28,.7)'; cx.lineWidth = 1; cx.beginPath();
        cx.moveTo(x, y - 14); cx.lineTo(x + w, y - 14); cx.moveTo(x, y - 19); cx.lineTo(x, y - 9); cx.moveTo(x + w, y - 19); cx.lineTo(x + w, y - 9);
        cx.moveTo(x - 14, y); cx.lineTo(x - 14, y + h); cx.moveTo(x - 19, y); cx.lineTo(x - 9, y); cx.moveTo(x - 19, y + h); cx.lineTo(x - 9, y + h);
        cx.stroke();
      }
      cx.fillStyle = '#FF4A1C'; cx.fillRect(px - 2, py - 2, 4, 4);
    });

    return () => removeEventListener('resize', size2);
}
