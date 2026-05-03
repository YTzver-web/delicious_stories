const dots = document.querySelectorAll('.dot');
const slides = document.querySelectorAll('.slide'); 
let slideIndex = 0;

function showSlide(n) {
    if (n < 0) {
        slideIndex = slides.length - 1;
    }
    if (n >= slides.length) {
        slideIndex = 0;
    }

    slides.forEach(slide => {
        slide.classList.remove('active');  
    });

    dots.forEach(dot => {
        dot.classList.remove('active');
    });

    slides[slideIndex].classList.add('active');  
   
    dots[slideIndex].classList.add('active');
}

function currentSlide(n) {
    slideIndex = n;
    showSlide(slideIndex);
}

dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        currentSlide(index);
    });
});

setInterval(() => {
    slideIndex++;
    if (slideIndex >= slides.length) {
        slideIndex = 0;
    }
    showSlide(slideIndex);
}, 5000); 

showSlide(slideIndex);