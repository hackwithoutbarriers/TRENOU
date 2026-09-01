document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (!menuToggle || !mobileMenu) {
        return;
    }

    const setMenuState = (isOpen) => {
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.classList.toggle('active', isOpen);
        mobileMenu.classList.toggle('hidden', !isOpen);
    };

    menuToggle.addEventListener('click', () => {
        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
        setMenuState(!isOpen);
    });

    mobileMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setMenuState(false));
    });
});
