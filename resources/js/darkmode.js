// Dark Mode Toggle
const DarkMode = {
    init() {
        const saved = localStorage.getItem('darkMode');
        if (saved === 'true' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            this.enable();
        }
    },
    toggle() {
        if (document.documentElement.classList.contains('dark')) {
            this.disable();
        } else {
            this.enable();
        }
    },
    enable() {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    },
    disable() {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    },
    isEnabled() {
        return document.documentElement.classList.contains('dark');
    }
};

DarkMode.init();
window.DarkMode = DarkMode;
