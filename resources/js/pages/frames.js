export default function (cv, SC) {
    const { lerp, smooth, reduce, fine, mouse } = SC;
    let disposed = false;
    let renderer = null;
    let onResize = null;

    import('three').then((THREE) => {
        if (disposed || !cv.isConnected) {
            return;
        }
        let r;
        try {
            r = new THREE.WebGLRenderer({ canvas: cv, antialias: true, alpha: true });
        } catch (e) {
            r = null;
        }
        if (!r) {
            return;
        }
        renderer = r;
      r.setPixelRatio(Math.min(devicePixelRatio, 2));
      const scene = new THREE.Scene(), cam = new THREE.PerspectiveCamera(32, 1, 0.1, 100); cam.position.set(0, 0, 9);
      const group = new THREE.Group(); scene.add(group);
      // three covers in depth, each with a hint of an interface inside
      const frames = [[-0.9, 0.75, -1.2, 2.9], [0.55, -0.15, 0, 3.3], [1.55, -1.05, 1.1, 2.4]];
      const segs = [];
      frames.forEach(([x, y, z, w]) => {
        const h = w * 9 / 16, x0 = x - w / 2, y0 = y - h / 2;
        const R = (a, b, c, d) => { segs.push([a, b, c, b, z], [c, b, c, d, z], [c, d, a, d, z], [a, d, a, b, z]); };
        R(x0, y0, x0 + w, y0 + h);
        segs.push([x0, y0 + h * 0.86, x0 + w, y0 + h * 0.86, z]);
        R(x0 + w * 0.06, y0 + h * 0.12, x0 + w * 0.46, y0 + h * 0.7);
        segs.push([x0 + w * 0.55, y0 + h * 0.62, x0 + w * 0.92, y0 + h * 0.62, z], [x0 + w * 0.55, y0 + h * 0.48, x0 + w * 0.85, y0 + h * 0.48, z], [x0 + w * 0.55, y0 + h * 0.34, x0 + w * 0.78, y0 + h * 0.34, z]);
      });
      const len = segs.map(s => Math.hypot(s[2] - s[0], s[3] - s[1])), tot = len.reduce((a, b) => a + b, 0);
      const N = innerWidth < 900 ? 2200 : 4200;
      const pos = new Float32Array(N * 3), tgt = new Float32Array(N * 3), sd = new Float32Array(N);
      for (let i = 0; i < N; i++) {
        const rr = 1.5 + Math.random() * 4.5, th = Math.random() * 6.283, ph = Math.acos(2 * Math.random() - 1);
        pos[i * 3] = rr * Math.sin(ph) * Math.cos(th) * 1.4; pos[i * 3 + 1] = rr * Math.cos(ph) * 0.9; pos[i * 3 + 2] = rr * Math.sin(ph) * Math.sin(th) * 0.8;
        let d = Math.random() * tot, k = 0; while (k < segs.length - 1 && d > len[k]) { d -= len[k]; k++; }
        const s = segs[k], f = len[k] ? d / len[k] : 0;
        tgt[i * 3] = s[0] + (s[2] - s[0]) * f; tgt[i * 3 + 1] = s[1] + (s[3] - s[1]) * f; tgt[i * 3 + 2] = s[4];
        sd[i] = Math.random();
      }
      const g = new THREE.BufferGeometry();
      g.setAttribute('position', new THREE.BufferAttribute(pos, 3)); g.setAttribute('aT', new THREE.BufferAttribute(tgt, 3)); g.setAttribute('aS', new THREE.BufferAttribute(sd, 1));
      const m = new THREE.ShaderMaterial({ transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
        uniforms: { uT: { value: 0 }, uTime: { value: 0 }, uM: { value: new THREE.Vector3(99, 99, 0) }, uPix: { value: r.getPixelRatio() }, uAcc: { value: new THREE.Color(0xff4a1c) } },
        vertexShader: `uniform float uT, uTime, uPix; uniform vec3 uM; attribute vec3 aT; attribute float aS; varying float vA; varying float vH;
          void main() {
            float t = smoothstep(aS * 0.5, 0.5 + aS * 0.5, uT);
            vec3 c = position + vec3(sin(uTime * 0.4 + aS * 30.0), cos(uTime * 0.3 + aS * 20.0), 0.0) * 0.15;
            vec3 p = mix(c, aT, t);
            p.y += sin(uTime * 0.8 + aT.x * 1.2 + aT.z) * 0.03 * t;
            vec4 wp = modelMatrix * vec4(p, 1.0);
            float d = length(wp.xy - uM.xy); float hot = exp(-d * d * 2.2);
            wp.xy += normalize(wp.xy - uM.xy + 0.0001) * hot * 0.22; wp.z += hot * 0.3;
            vec4 mv = viewMatrix * wp; gl_Position = projectionMatrix * mv;
            gl_PointSize = mix(1.4 + 2.2 * aS, 1.8 + 0.6 * aS, t) * uPix * (9.0 / -mv.z) * (1.0 + hot);
            vA = mix(0.35 + 0.5 * aS, 0.9, t) * (0.7 + hot * 0.6); vH = hot * step(0.5, aS) + step(0.97, aS) * (1.0 - t);
          }`,
        fragmentShader: `uniform vec3 uAcc; varying float vA; varying float vH; void main() { float s = smoothstep(0.5, 0.0, length(gl_PointCoord - 0.5)); if (s < 0.02) discard; gl_FragColor = vec4(mix(vec3(0.95, 0.94, 0.92), uAcc, clamp(vH, 0.0, 1.0)), vA * s); }` });
      group.add(new THREE.Points(g, m));
      const size = () => { const b = cv.parentElement.getBoundingClientRect(); r.setSize(b.width, b.height, false); cam.aspect = b.width / Math.max(1, b.height); cam.updateProjectionMatrix(); };
      size(); addEventListener('resize', size);
      const t0 = performance.now(); let rx = 0, ry = 0; const mw = new THREE.Vector3();
      SC.onFrame.push((now) => {
        const t = (now - t0) / 1000, b = cv.getBoundingClientRect();
        if (b.bottom < 0) return;
        m.uniforms.uTime.value = reduce ? 0 : t;
        m.uniforms.uT.value = reduce ? 1 : smooth(0.2, 2.6, t);
        const nx = (mouse.sx - b.left) / b.width * 2 - 1, ny = -((mouse.sy - b.top) / b.height) * 2 + 1;
        rx = lerp(rx, fine ? -ny * 0.18 : 0, 0.05); ry = lerp(ry, fine ? nx * 0.28 - 0.25 : -0.25, 0.05);
        group.rotation.set(rx, ry, 0);
        const v = new THREE.Vector3(nx, ny, 0.5).unproject(cam).sub(cam.position).normalize();
        mw.copy(cam.position).add(v.multiplyScalar(-cam.position.z / v.z));
        if (!mouse.moved || !fine || Math.abs(nx) > 1.2 || Math.abs(ny) > 1.2) mw.set(99, 99, 0);
        m.uniforms.uM.value.copy(mw);
        r.render(scene, cam);
      });

        onResize = size;
    });

    return () => {
        disposed = true;
        if (onResize) {
            removeEventListener('resize', onResize);
        }
        if (renderer) {
            renderer.dispose();
        }
    };
}
