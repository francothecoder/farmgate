

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.homeImageSlider = (slides) => ({
    slides,
    activeIndex: 0,
    isPlaying: !window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    timer: null,

    init() {
        this.startTimer();
    },

    destroy() {
        this.stopTimer();
    },

    next() {
        this.activeIndex = (this.activeIndex + 1) % this.slides.length;
    },

    previous() {
        this.activeIndex = (this.activeIndex - 1 + this.slides.length) % this.slides.length;
    },

    show(index) {
        this.activeIndex = index;
    },

    pause() {
        this.isPlaying = false;
        this.stopTimer();
    },

    play() {
        this.isPlaying = true;
        this.startTimer();
    },

    startTimer() {
        this.stopTimer();

        if (this.isPlaying && this.slides.length > 1) {
            this.timer = window.setInterval(() => this.next(), 6000);
        }
    },

    stopTimer() {
        if (this.timer !== null) {
            window.clearInterval(this.timer);
            this.timer = null;
        }
    },
});

Alpine.start();
