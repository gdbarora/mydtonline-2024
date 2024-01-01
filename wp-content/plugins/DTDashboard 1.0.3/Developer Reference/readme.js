// JavaScript to toggle class
document.addEventListener('DOMContentLoaded', function () {
    const contentSections = document.querySelectorAll('section h2');
    contentSections.forEach(section => {
        const div = section.parentElement.querySelector('.content');// Add the "closed" class by default
        section.addEventListener('click', function () {
            // Toggle the "closed" class on the div within the clicked section
            div.classList.toggle('closed');
        });
    });
});