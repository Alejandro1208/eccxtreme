let lastScroll = 0;
const header = document.querySelector('.main-header');
const headerHeight = header.offsetHeight;

function handleScroll() {
    const currentScroll = window.pageYOffset;

    // Scrolling down
    if (currentScroll > lastScroll && currentScroll > headerHeight) {
        header.style.transform = `translateY(-${headerHeight}px)`;
    } 
    // Scrolling up
    else {
        header.style.transform = 'translateY(0)';
    }

    lastScroll = currentScroll;
}

window.addEventListener('scroll', handleScroll);