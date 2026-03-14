// Register GSAP ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

// Custom Cursor
const cursor = document.querySelector('.custom-cursor');
const links = document.querySelectorAll('a, button, .work-item, .btn-scroll');

document.addEventListener('mousemove', (e) => {
    if (cursor) {
        gsap.to(cursor, {
            x: e.clientX,
            y: e.clientY,
            duration: 0.03, // Decreased duration for faster response
            ease: 'power2.out'
        });
    }
});

links.forEach(link => {
    link.addEventListener('mouseenter', () => { if (cursor) cursor.classList.add('hover'); });
    link.addEventListener('mouseleave', () => { if (cursor) cursor.classList.remove('hover'); });
});

// Initial Hero Animation
const heroTimeline = gsap.timeline({ defaults: { ease: "power4.out" } });

heroTimeline.to('.hero-title .reveal-text', {
    y: "0%",
    duration: 1.5,
    stagger: 0.2,
    delay: 0.2
})
    .to('.hero-subtitle', {
        opacity: 1,
        y: 0,
        duration: 1
    }, "-=1")
    .to('.hero-action', {
        opacity: 1,
        y: 0,
        duration: 1
    }, "-=0.8");

// Text Swap Animation (Hero)
const swapTexts = ["perform", "deliver", "resonate"];
let textIndex = 0;
const textSwapEl = document.querySelector('.text-swap-1');

if (textSwapEl) {
    setInterval(() => {
        gsap.to(textSwapEl, {
            y: "-110%",
            duration: 0.5,
            ease: "power2.in",
            onComplete: () => {
                textIndex = (textIndex + 1) % swapTexts.length;
                textSwapEl.innerText = swapTexts[textIndex];
                gsap.fromTo(textSwapEl,
                    { y: "110%" },
                    { y: "0%", duration: 0.5, ease: "power2.out" }
                );
            }
        });
    }, 3000);
}

// Scroll Animations
// Titles reveal on scroll
const splitTexts = document.querySelectorAll('.reveal-text-scroll');

splitTexts.forEach(text => {
    // Basic implementation for scroll reveal text
    // We'll wrap the inner text to animate it cleanly
    const content = text.innerHTML;
    text.innerHTML = `<span style="display:inline-block; overflow:hidden;"><span class="inner-reveal" style="display:inline-block; transform:translateY(110%);">${content}</span></span>`;

    gsap.to(text.querySelector('.inner-reveal'), {
        y: "0%",
        duration: 1.2,
        ease: "power4.out",
        scrollTrigger: {
            trigger: text,
            start: "top 85%",
            toggleActions: "play none none reverse"
        }
    });
});

// Service Cards Stagger Reveal
gsap.from('.service-item', {
    y: 100,
    opacity: 0,
    duration: 1,
    stagger: 0.2,
    ease: "power3.out",
    scrollTrigger: {
        trigger: '.services-list',
        start: "top 80%",
        toggleActions: "play none none reverse"
    }
});

// Work Items parallax image scale effect
const workImages = document.querySelectorAll('.work-item-image img');
workImages.forEach(img => {
    gsap.to(img, {
        scale: 1.2,
        ease: "none",
        scrollTrigger: {
            trigger: img.parentElement,
            start: "top bottom",
            end: "bottom top",
            scrub: true
        }
    });
});

// Navbar Scrolled State
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Smooth Scroll for valid hash links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;

        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});
