document.addEventListener('alpine:init', () => {
    Alpine.data('layoutApp', () => ({
        open: new URLSearchParams(window.location.search).get('sidebar') === 'open',
        activeNav: null,

        init() {
            const path = window.location.pathname;
            if (path === '/' || path.includes('home')) this.activeNav = 'home';
            else if (path.includes('cart')) this.activeNav = 'cart';
            else if (path.includes('wishlist')) this.activeNav = 'wishlist';
            
            this.$watch('open', (value) => {
                if (value) {
                    this.activeNav = 'account';
                } else {
                    if (path === '/' || path.includes('home')) this.activeNav = 'home';
                    else if (path.includes('cart')) this.activeNav = 'cart';
                    else if (path.includes('wishlist')) this.activeNav = 'wishlist';
                }
            });
        }
    }));
});