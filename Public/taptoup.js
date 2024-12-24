const toTopBtn = document.getElementById('toTopBtn');

window.addEventListener('scroll', () => {
    if (window.scrollY > 200) {
        toTopBtn.classList.add('opacity-100', 'visible');
        toTopBtn.classList.remove('opacity-0', 'invisible');
    } else {
        toTopBtn.classList.add('opacity-0', 'invisible');
        toTopBtn.classList.remove('opacity-100', 'visible');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    });
}
