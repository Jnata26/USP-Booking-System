// Function to apply the selected theme
function setTheme(themeName) {
    // Remove all existing theme classes from the body
    document.body.classList.remove('theme-light-tilt', 'theme-white', 'theme-dark-tilt');
    
    // Add the newly selected theme class
    document.body.classList.add(themeName);
    
    // Save user preference to localStorage
    localStorage.setItem('usp_theme', themeName);
}

// Initialize theme on page load
document.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem('usp_theme') || 'theme-light-tilt';
    setTheme(savedTheme);

    // Sync the dropdown value if it exists on the page
    const themeSelector = document.getElementById('theme-selector');
    if (themeSelector) {
        themeSelector.value = savedTheme;
        
        // Listen for changes from the dropdown
        themeSelector.addEventListener('change', (e) => {
            setTheme(e.target.value);
        });
    }
});