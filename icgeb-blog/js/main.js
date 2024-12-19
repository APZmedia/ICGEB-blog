document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuButton = document.createElement('button');
    menuButton.classList.add('mobile-menu-toggle', 'md:hidden', 'text-white', 'p-2');
    menuButton.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-16 6h16" />
        </svg>
    `;

    const nav = document.querySelector('nav');
    if (nav) {
        nav.parentNode.insertBefore(menuButton, nav);
        
        menuButton.addEventListener('click', () => {
            nav.classList.toggle('mobile-menu-open');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});