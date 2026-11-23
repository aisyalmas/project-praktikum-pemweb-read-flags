const desktopMq = window.matchMedia('(min-width: 992px)');

function applySidebarIcon() {
    const collapsed = document.body.classList.contains('sb-collapsed');
    const icon = document.querySelector('.toggle-btn i');
    if (!icon) return;

    icon.classList.toggle('bi-chevron-right', collapsed);
    icon.classList.toggle('bi-chevron-left', !collapsed);
}

function setSidebarState(open) {
    document.body.classList.toggle('sb-collapsed', !open);
    applySidebarIcon();
}

function toggleSidebar() {
    document.body.classList.toggle('sb-collapsed');
    applySidebarIcon();
}

desktopMq.addEventListener('change', (e) => setSidebarState(e.matches));

document.addEventListener('DOMContentLoaded', () => {
    setSidebarState(desktopMq.matches);
});