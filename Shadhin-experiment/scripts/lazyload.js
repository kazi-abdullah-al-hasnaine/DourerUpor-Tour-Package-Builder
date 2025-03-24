document.addEventListener("DOMContentLoaded", function() {
        let lazyImages = document.querySelectorAll(".lazyload");
        let lazyBackgrounds = document.querySelectorAll(".lazy-bg");

        let observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (entry.target.tagName === "IMG") {
                        let img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove("lazyload");
                    } else {
                        let bgElement = entry.target;
                        bgElement.style.backgroundImage = "url(" + bgElement.dataset.bg + ")";
                        bgElement.classList.remove("lazy-bg");
                    }
                    observer.unobserve(entry.target);
                }
            });
        });

        lazyImages.forEach(img => observer.observe(img));
        lazyBackgrounds.forEach(bg => observer.observe(bg));
    });
