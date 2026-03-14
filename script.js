document.addEventListener('DOMContentLoaded', function() {
    initApartmentExpand();
    initSlider();
    initContactForm();
    initSmoothScroll();
});

function initApartmentExpand() {
    const apartmentRows = document.querySelectorAll('.apartment-row');

    apartmentRows.forEach(row => {
        const basicRow = row.querySelector('.row-basic');
        const expandBtn = row.querySelector('.expand-btn');

        basicRow.addEventListener('click', function() {
            toggleRow(row);
        });

        expandBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleRow(row);
        });
    });
}

function toggleRow(row) {
    const isExpanded = row.classList.contains('expanded');

    document.querySelectorAll('.apartment-row').forEach(r => {
        r.classList.remove('expanded');
    });

    if (!isExpanded) {
        row.classList.add('expanded');
    }
}

function initSlider() {
    const track = document.querySelector('.slider-track');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
    const slides = document.querySelectorAll('.slide');

    if (!track || !prevBtn || !nextBtn || slides.length === 0) return;

    let currentIndex = 0;
    const totalSlides = slides.length;

    function updateSlider() {
        const offset = -currentIndex * 100;
        track.style.transform = `translateX(${offset}%)`;
    }

    prevBtn.addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlider();
    });

    nextBtn.addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
    });

    setInterval(function() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
    }, 5000);
}

function initContactForm() {
    const form = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        var isEnglish = document.documentElement.lang === 'en';

        try {
            const response = await fetch('contact.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                formMessage.textContent = isEnglish
                    ? 'Thank you! Your request has been sent successfully. We will contact you soon.'
                    : 'Hvala! Vaš zahtev je uspešno poslat. Kontaktiraćemo vas uskoro.';
                formMessage.className = 'form-message success';
                form.reset();
            } else {
                formMessage.textContent = isEnglish
                    ? 'An error occurred. Please try again.'
                    : 'Došlo je do greške. Molimo pokušajte ponovo.';
                formMessage.className = 'form-message error';
            }
        } catch (error) {
            formMessage.textContent = isEnglish
                ? 'An error occurred. Please try again.'
                : 'Došlo je do greške. Molimo pokušajte ponovo.';
            formMessage.className = 'form-message error';
        }

        setTimeout(function() {
            formMessage.style.display = 'none';
        }, 5000);
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}
