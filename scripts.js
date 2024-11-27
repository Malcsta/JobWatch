function confirmSignOut() {
    return confirm("Are you sure you want to sign out?");
}

// Saving the scroll position before the page reloads
window.addEventListener('beforeunload', () => {
    localStorage.setItem('scrollPosition', window.scrollY);
});

// Restore scroll after page reload
window.addEventListener('load', () => {
    const scrollPosition = localStorage.getItem('scrollPosition');
    if (scrollPosition) {
        window.scrollTo(0, parseInt(scrollPosition, 10));
        localStorage.removeItem('scrollPosition'); // Clean up after use
    }
});