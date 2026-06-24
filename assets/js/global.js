// global.js
document.addEventListener("DOMContentLoaded", () => {
    // Dynamically inject Ionicons to avoid repeating script tags in every HTML file
    if (!document.querySelector('script[src*="ionicons"]')) {
        const scriptModule = document.createElement('script');
        scriptModule.type = 'module';
        scriptModule.src = 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js';
        scriptModule.setAttribute('integrity', 'sha384-sO//z//rXz/M1gG+I1xXp3P1GZ2f/R9fI/1mZ0Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z1Z'); // Example SRI, usually required but unpkg changes often. We will just use standard tags here.
        scriptModule.removeAttribute('integrity'); // Removed to avoid hash mismatches on unpkg
        document.body.appendChild(scriptModule);

        const scriptNoModule = document.createElement('script');
        scriptNoModule.setAttribute('nomodule', '');
        scriptNoModule.src = 'https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js';
        document.body.appendChild(scriptNoModule);
    }
});
