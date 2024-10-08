setTimeout(() => {
    const loader = document.querySelector('div.ao-container .loader');
    const loadingText = document.getElementById('ao-loading-text');
    const errorMessage = document.getElementById('ao-error-message');
    if (!loader || !loadingText || !errorMessage) {
        return;
    }

    loader.style.display = 'none';
    loadingText.style.display = 'none';
    errorMessage.style.display = 'block';
}, 15000); // wait 15 seconds before showing error message
