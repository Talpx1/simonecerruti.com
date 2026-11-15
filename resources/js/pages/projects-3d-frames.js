export default function (canvas, siteRuntime) {
    const { linearInterpolate, smoothStep, prefersReducedMotion, hasFinePointer, pointer } = siteRuntime;
    let isDisposed = false;
    let renderer = null;
    let onResize = null;

    import('three').then((THREE) => {
        if (isDisposed || !canvas.isConnected) {
            return;
        }
        let webglRenderer;
        try {
            webglRenderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
        } catch (error) {
            webglRenderer = null;
        }
        if (!webglRenderer) {
            return;
        }
        renderer = webglRenderer;
        webglRenderer.setPixelRatio(Math.min(devicePixelRatio, 2));
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(32, 1, 0.1, 100);
        camera.position.set(0, 0, 9);
        const frameGroup = new THREE.Group();
        scene.add(frameGroup);
        // three covers in depth, each with a hint of an interface inside
        const frameLayouts = [[-0.9, 0.75, -1.2, 2.9], [0.55, -0.15, 0, 3.3], [1.55, -1.05, 1.1, 2.4]];
        const lineSegments = [];
        frameLayouts.forEach(([frameCenterX, frameCenterY, frameDepth, frameWidth]) => {
            const frameHeight = frameWidth * 9 / 16;
            const frameLeft = frameCenterX - frameWidth / 2;
            const frameBottom = frameCenterY - frameHeight / 2;
            const addRectangleSegments = (left, bottom, right, top) => {
                lineSegments.push([left, bottom, right, bottom, frameDepth], [right, bottom, right, top, frameDepth], [right, top, left, top, frameDepth], [left, top, left, bottom, frameDepth]);
            };
            addRectangleSegments(frameLeft, frameBottom, frameLeft + frameWidth, frameBottom + frameHeight);
            lineSegments.push([frameLeft, frameBottom + frameHeight * 0.86, frameLeft + frameWidth, frameBottom + frameHeight * 0.86, frameDepth]);
            addRectangleSegments(frameLeft + frameWidth * 0.06, frameBottom + frameHeight * 0.12, frameLeft + frameWidth * 0.46, frameBottom + frameHeight * 0.7);
            lineSegments.push([frameLeft + frameWidth * 0.55, frameBottom + frameHeight * 0.62, frameLeft + frameWidth * 0.92, frameBottom + frameHeight * 0.62, frameDepth], [frameLeft + frameWidth * 0.55, frameBottom + frameHeight * 0.48, frameLeft + frameWidth * 0.85, frameBottom + frameHeight * 0.48, frameDepth], [frameLeft + frameWidth * 0.55, frameBottom + frameHeight * 0.34, frameLeft + frameWidth * 0.78, frameBottom + frameHeight * 0.34, frameDepth]);
        });
        const segmentLengths = lineSegments.map((segment) => Math.hypot(segment[2] - segment[0], segment[3] - segment[1]));
        const totalLength = segmentLengths.reduce((sum, length) => sum + length, 0);
        const PARTICLE_COUNT = innerWidth < 900 ? 2200 : 4200;
        const positions = new Float32Array(PARTICLE_COUNT * 3);
        const targetPositions = new Float32Array(PARTICLE_COUNT * 3);
        const seeds = new Float32Array(PARTICLE_COUNT);
        for (let i = 0; i < PARTICLE_COUNT; i++) {
            const scatterRadius = 1.5 + Math.random() * 4.5;
            const azimuth = Math.random() * 6.283;
            const polar = Math.acos(2 * Math.random() - 1);
            positions[i * 3] = scatterRadius * Math.sin(polar) * Math.cos(azimuth) * 1.4;
            positions[i * 3 + 1] = scatterRadius * Math.cos(polar) * 0.9;
            positions[i * 3 + 2] = scatterRadius * Math.sin(polar) * Math.sin(azimuth) * 0.8;
            let remainingDistance = Math.random() * totalLength;
            let segmentIndex = 0;
            while (segmentIndex < lineSegments.length - 1 && remainingDistance > segmentLengths[segmentIndex]) {
                remainingDistance -= segmentLengths[segmentIndex];
                segmentIndex++;
            }
            const segment = lineSegments[segmentIndex];
            const segmentFraction = segmentLengths[segmentIndex] ? remainingDistance / segmentLengths[segmentIndex] : 0;
            targetPositions[i * 3] = segment[0] + (segment[2] - segment[0]) * segmentFraction;
            targetPositions[i * 3 + 1] = segment[1] + (segment[3] - segment[1]) * segmentFraction;
            targetPositions[i * 3 + 2] = segment[4];
            seeds[i] = Math.random();
        }
        const geometry = new THREE.BufferGeometry();
        geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geometry.setAttribute('aT', new THREE.BufferAttribute(targetPositions, 3));
        geometry.setAttribute('aS', new THREE.BufferAttribute(seeds, 1));
        const material = new THREE.ShaderMaterial({ transparent: true, depthWrite: false, blending: THREE.AdditiveBlending,
            uniforms: { uT: { value: 0 }, uTime: { value: 0 }, uM: { value: new THREE.Vector3(99, 99, 0) }, uPix: { value: webglRenderer.getPixelRatio() }, uAcc: { value: new THREE.Color(0xff4a1c) } },
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
        frameGroup.add(new THREE.Points(geometry, material));
        const resizeRenderer = () => {
            const parentBounds = canvas.parentElement.getBoundingClientRect();
            webglRenderer.setSize(parentBounds.width, parentBounds.height, false);
            camera.aspect = parentBounds.width / Math.max(1, parentBounds.height);
            camera.updateProjectionMatrix();
        };
        resizeRenderer();
        addEventListener('resize', resizeRenderer);
        const startTimestamp = performance.now();
        let rotationX = 0;
        let rotationY = 0;
        const mouseWorldPosition = new THREE.Vector3();
        siteRuntime.frameCallbacks.push((timestamp) => {
            const elapsedSeconds = (timestamp - startTimestamp) / 1000;
            const canvasBounds = canvas.getBoundingClientRect();
            if (canvasBounds.bottom < 0) {
                return;
            }
            material.uniforms.uTime.value = prefersReducedMotion ? 0 : elapsedSeconds;
            material.uniforms.uT.value = prefersReducedMotion ? 1 : smoothStep(0.2, 2.6, elapsedSeconds);
            const normalizedX = (pointer.smoothX - canvasBounds.left) / canvasBounds.width * 2 - 1;
            const normalizedY = -((pointer.smoothY - canvasBounds.top) / canvasBounds.height) * 2 + 1;
            rotationX = linearInterpolate(rotationX, hasFinePointer ? -normalizedY * 0.18 : 0, 0.05);
            rotationY = linearInterpolate(rotationY, hasFinePointer ? normalizedX * 0.28 - 0.25 : -0.25, 0.05);
            frameGroup.rotation.set(rotationX, rotationY, 0);
            const rayDirection = new THREE.Vector3(normalizedX, normalizedY, 0.5).unproject(camera).sub(camera.position).normalize();
            mouseWorldPosition.copy(camera.position).add(rayDirection.multiplyScalar(-camera.position.z / rayDirection.z));
            if (!pointer.hasMoved || !hasFinePointer || Math.abs(normalizedX) > 1.2 || Math.abs(normalizedY) > 1.2) {
                mouseWorldPosition.set(99, 99, 0);
            }
            material.uniforms.uM.value.copy(mouseWorldPosition);
            webglRenderer.render(scene, camera);
        });

        onResize = resizeRenderer;
    });

    return () => {
        isDisposed = true;
        if (onResize) {
            removeEventListener('resize', onResize);
        }
        if (renderer) {
            renderer.dispose();
        }
    };
}
