
function handleIntersection(entries, observer) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
        
            entry.target.classList.add('show');
            entry.target.classList.remove('fade-out');
        } else {
         
            entry.target.classList.add('fade-out');
            entry.target.classList.remove('show');
        }
    });
}

const options = {
    root: null, 
    rootMargin: '0px',
    threshold: 0.1 
};

const observer = new IntersectionObserver(handleIntersection, options);
document.addEventListener('DOMContentLoaded', () => {
    const animatedElements = document.querySelectorAll('.fade-up, .fade-in, .slide-in-left, .slide-in-right');
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
});
