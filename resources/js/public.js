import Lenis from 'lenis'
import Snap from 'lenis/snap'
import 'lenis/dist/lenis.css'
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

// LENIS
const lenis = new Lenis({autoRaf: false});
window.lenis = lenis
const snap = new Snap(lenis, {
    type: 'lock',
    distanceThreshold: '100%',
    debounce: 0,
    easing: (x) =>  x === 0
        ? 0
        : x === 1
        ? 1
        : x < 0.5 ? Math.pow(2, 20 * x - 10) / 2
        : (2 - Math.pow(2, -20 * x + 10)) / 2
})
window.snap = snap




// GSAP

gsap.registerPlugin(ScrollTrigger);

// Synchronize Lenis scrolling with GSAP's ScrollTrigger plugin
lenis.on('scroll', ScrollTrigger.update);

// Add Lenis's requestAnimationFrame (raf) method to GSAP's ticker
// This ensures Lenis's smooth scroll animation updates on each GSAP tick
gsap.ticker.add((time) => {
    lenis.raf(time * 1000); // Convert time from seconds to milliseconds
});

// Disable lag smoothing in GSAP to prevent any delay in scroll animations
gsap.ticker.lagSmoothing(0);

window.gsapScrollTrigger = ScrollTrigger
window.gsap = gsap




// DEVICE DETECTION
const DESKTOP_MIN_WIDTH = 1024
window.isDesktop = window.innerWidth >= DESKTOP_MIN_WIDTH
window.isMobile = !isDesktop

window.addEventListener('resize', (e) => {
    window.isDesktop= window.innerWidth >= DESKTOP_MIN_WIDTH
    window.isMobile= !isDesktop
})

