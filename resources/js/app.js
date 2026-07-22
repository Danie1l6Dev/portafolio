document.addEventListener('alpine:init', () => {
    window.Alpine.data('portfolioNavigation', (sectionIds = [], initialSection = null) => ({
        open: false,
        active: initialSection,
        sections: [],
        activeFrame: null,
        syncActiveHandler: null,

        init() {
            this.sections = sectionIds
                .map((id) => document.getElementById(id))
                .filter(Boolean);

            if (this.sections.length === 0) {
                return;
            }

            this.syncActiveHandler = () => this.scheduleActiveSync();

            window.addEventListener('scroll', this.syncActiveHandler, { passive: true });
            window.addEventListener('resize', this.syncActiveHandler);
            window.addEventListener('hashchange', this.syncActiveHandler);
            window.addEventListener('load', this.syncActiveHandler);

            this.scheduleActiveSync();
        },

        scheduleActiveSync() {
            if (this.activeFrame !== null) {
                return;
            }

            this.activeFrame = window.requestAnimationFrame(() => {
                this.activeFrame = null;
                this.syncActiveSection();
            });
        },

        syncActiveSection() {
            const marker = this.$root.getBoundingClientRect().bottom + 12;
            let current = this.sections[0];

            for (const section of this.sections) {
                if (section.getBoundingClientRect().top > marker) {
                    break;
                }

                current = section;
            }

            if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2) {
                current = this.sections.at(-1);
            }

            this.active = current.id;
        },

        destroy() {
            if (this.syncActiveHandler) {
                window.removeEventListener('scroll', this.syncActiveHandler);
                window.removeEventListener('resize', this.syncActiveHandler);
                window.removeEventListener('hashchange', this.syncActiveHandler);
                window.removeEventListener('load', this.syncActiveHandler);
            }

            if (this.activeFrame !== null) {
                window.cancelAnimationFrame(this.activeFrame);
            }
        },

        toggleMenu() {
            this.open = !this.open;
        },

        closeMenu() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        },
    }));

    window.Alpine.data('featuredProjectCarousel', (total = 0) => ({
        total,
        activeIndex: 0,
        ready: false,
        touchStartX: null,
        resizeObserver: null,
        resizeHandler: null,
        layout: {
            cardWidth: 640,
            step: 360,
            visibleSide: 2,
            trackHeight: 660,
            navTop: 300,
        },

        init() {
            this.updateLayout();

            if ('ResizeObserver' in window) {
                this.resizeObserver = new ResizeObserver(() => this.updateLayout());
                this.resizeObserver.observe(this.$root);
            } else {
                this.resizeHandler = () => this.updateLayout();
                window.addEventListener('resize', this.resizeHandler);
            }

            this.$nextTick(() => {
                this.updateLayout();
                this.ready = true;
            });
        },

        destroy() {
            this.resizeObserver?.disconnect();

            if (this.resizeHandler) {
                window.removeEventListener('resize', this.resizeHandler);
            }
        },

        fittedSideStep(width, cardWidth, preferredStep) {
            if (width < 560) {
                return preferredStep;
            }

            const edgePadding = 16;
            const maximumStep = (width - cardWidth * 0.8) / 2 - edgePadding;

            return Math.max(0, Math.min(preferredStep, maximumStep));
        },

        updateLayout() {
            const width = Math.max(this.$root?.clientWidth ?? window.innerWidth, 280);

            if (width < 480) {
                const cardWidth = Math.max(240, width * 0.82);

                this.layout = {
                    cardWidth,
                    step: this.fittedSideStep(width, cardWidth, Math.max(150, width * 0.48)),
                    visibleSide: 1,
                    trackHeight: 560,
                    navTop: 255,
                };

                return;
            }

            if (width < 640) {
                const cardWidth = width * 0.78;

                this.layout = {
                    cardWidth,
                    step: this.fittedSideStep(width, cardWidth, width * 0.48),
                    visibleSide: 1,
                    trackHeight: 580,
                    navTop: 265,
                };

                return;
            }

            if (width < 768) {
                const cardWidth = Math.min(512, width * 0.78);

                this.layout = {
                    cardWidth,
                    step: this.fittedSideStep(width, cardWidth, width * 0.46),
                    visibleSide: 1,
                    trackHeight: 620,
                    navTop: 280,
                };

                return;
            }

            if (width < 1024) {
                const cardWidth = Math.min(544, width * 0.66);

                this.layout = {
                    cardWidth,
                    step: this.fittedSideStep(width, cardWidth, Math.min(300, width * 0.38)),
                    visibleSide: 2,
                    trackHeight: 635,
                    navTop: 290,
                };

                return;
            }

            const cardWidth = Math.min(640, width * 0.58);

            this.layout = {
                cardWidth,
                step: this.fittedSideStep(width, cardWidth, Math.min(360, width * 0.3)),
                visibleSide: 2,
                trackHeight: 660,
                navTop: 300,
            };
        },

        circularOffset(index) {
            let offset = index - this.activeIndex;
            const half = Math.floor(this.total / 2);

            if (offset > half) {
                offset -= this.total;
            }

            if (offset < -half) {
                offset += this.total;
            }

            return offset;
        },

        isActive(index) {
            return index === this.activeIndex;
        },

        isVisible(index) {
            return Math.abs(this.circularOffset(index)) <= this.layout.visibleSide;
        },

        slideStyle(index) {
            if (! this.ready) {
                return {};
            }

            const offset = this.circularOffset(index);
            const distance = Math.abs(offset);
            const scale = distance === 0 ? 1 : distance === 1 ? 0.8 : 0.63;
            const opacity = distance === 0 ? 1 : distance === 1 ? 0.72 : 0.42;

            return {
                width: `${this.layout.cardWidth}px`,
                transform: `translateX(calc(-50% + ${offset * this.layout.step}px)) scale(${scale})`,
                opacity,
                zIndex: 10 - distance * 3,
            };
        },

        goTo(index) {
            if (this.total === 0) {
                return;
            }

            this.activeIndex = (index + this.total) % this.total;
        },

        previous() {
            this.goTo(this.activeIndex - 1);
        },

        next() {
            this.goTo(this.activeIndex + 1);
        },

        handleArrowNavigation(direction, event) {
            const focusFollowsSlide = event.target.closest?.('.featured-project-carousel__link') !== null;

            this.goTo(this.activeIndex + direction);

            if (! focusFollowsSlide) {
                return;
            }

            this.$nextTick(() => {
                this.$root
                    .querySelector('[data-active="true"] .featured-project-carousel__link')
                    ?.focus();
            });
        },

        handleSlideClick(event, index) {
            if (this.isActive(index)) {
                return;
            }

            event.preventDefault();
            this.goTo(index);
        },

        handleSlideAction(event, index) {
            if (this.isActive(index)) {
                window.location.assign(event.currentTarget.href);

                return;
            }

            this.goTo(index);
        },

        startSwipe(event) {
            this.touchStartX = event.touches?.[0]?.clientX ?? null;
        },

        endSwipe(event) {
            if (this.touchStartX === null) {
                return;
            }

            const endX = event.changedTouches?.[0]?.clientX;

            if (typeof endX !== 'number') {
                this.touchStartX = null;

                return;
            }

            const distance = endX - this.touchStartX;

            if (distance < -50) {
                this.next();
            } else if (distance > 50) {
                this.previous();
            }

            this.touchStartX = null;
        },
    }));
});

const initializeRevealAnimations = () => {
    const elements = [...document.querySelectorAll('[data-reveal]:not([data-reveal-ready])')];

    if (elements.length === 0) {
        return;
    }

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        elements.forEach((element) => {
            element.dataset.revealReady = 'true';
            element.dataset.revealed = 'true';
        });

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) {
                    return;
                }

                entry.target.dataset.revealed = 'true';
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.12 },
    );

    elements.forEach((element, index) => {
        element.dataset.revealReady = 'true';
        element.style.transitionDelay = `${Math.min(index % 4, 3) * 55}ms`;
        observer.observe(element);
    });
};

document.addEventListener('DOMContentLoaded', initializeRevealAnimations);
document.addEventListener('livewire:navigated', initializeRevealAnimations);
