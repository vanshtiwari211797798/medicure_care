document.addEventListener('DOMContentLoaded', function () {

    // ==== Hero Slideshow ====
    let flag = 0;
    const slides = document.getElementsByClassName("slide");

    function slideshow(num) {
        if (num >= slides.length) flag = 0;
        if (num < 0) flag = slides.length - 1;

        Array.from(slides).forEach(slide => {
            slide.style.display = "none";
        });

        slides[flag].style.display = "block";
    }

    function controller(x) {
        flag += x;
        slideshow(flag);
    }

    // Initialize and auto-slide
    slideshow(flag);
    setInterval(() => {
        flag++;
        slideshow(flag);
    }, 3000);

    // ==== Horizontal Sliders ====
    function setupHorizontalScroll(containerId, backBtnId, nextBtnId, scrollAmount) {
        const container = document.getElementById(containerId);
        const backBtn = document.getElementById(backBtnId);
        const nextBtn = document.getElementById(nextBtnId);

        if (!container || !backBtn || !nextBtn) return;

        container.addEventListener("wheel", (evt) => {
            evt.preventDefault();
            container.scrollLeft += evt.deltaY;
        });

        backBtn.addEventListener("click", () => {
            container.scrollLeft -= scrollAmount;
        });

        nextBtn.addEventListener("click", () => {
            container.scrollLeft += scrollAmount;
        });
    }

    // Apply to different sections
    setupHorizontalScroll("scrollable-gallery", "backbtn", "nextbtn", 900);
    setupHorizontalScroll("scrollable-articles", "articleBackBtn", "articleNextBtn", 400);
    setupHorizontalScroll("scrollable-deals", "dealsBackBtn", "dealsNextBtn", 300);

    // ==== Category Slider ====
    const categorySlider = document.querySelector('.category-slider');
    const prevCatBtn = document.querySelector('.prev-btn');
    const nextCatBtn = document.querySelector('.next-btn');

    if (categorySlider && prevCatBtn && nextCatBtn) {
        const scrollAmount = 270; // 250 + 20 gap

        prevCatBtn.addEventListener('click', () => {
            categorySlider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextCatBtn.addEventListener('click', () => {
            categorySlider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        const handleScroll = () => {
            prevCatBtn.style.visibility = categorySlider.scrollLeft <= 0 ? 'hidden' : 'visible';
            nextCatBtn.style.visibility = categorySlider.scrollLeft >= (categorySlider.scrollWidth - categorySlider.clientWidth)
                ? 'hidden' : 'visible';
        };

        categorySlider.addEventListener('scroll', handleScroll);
        handleScroll(); // Initial check
    }

    // ==== FAQ Accordion ====
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const toggleIcon = item.querySelector('.faq-toggle i');

        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            // Close all
            faqItems.forEach(faq => {
                faq.classList.remove('active');
                const icon = faq.querySelector('.faq-toggle i');
                if (icon) icon.className = 'fas fa-plus';
            });

            // Open clicked
            if (!isActive) {
                item.classList.add('active');
                if (toggleIcon) toggleIcon.className = 'fas fa-minus';
            }
        });
    });

});
