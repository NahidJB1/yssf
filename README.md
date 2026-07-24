# YS Study Focus (YSSF)

Welcome to the **YS Study Focus** project repository! This website helps international students discover, evaluate, and apply to top universities in Malaysia.

## Project Structure

This project is built using modern, lightweight HTML, CSS, and vanilla JavaScript. For maintainability and security, the repository uses a modular structure:

```text
├── index.html                   # Main landing page
├── favicon.png                  # Site branding
├── universities.json            # Structured data
├── sitemap.xml & robots.txt     # SEO configuration files
│
├── assets/                      # Global static assets
│   ├── css/                     # Global CSS (global.css) for site-wide styling & navbars
│   ├── js/                      # Global JavaScript for dynamic elements and icon injection
│   ├── images/                  # All optimized images (campuses, logos, etc.)
│   └── documents/               # Downloadable PDFs (fee structures, brochures)
│
└── pages/                       # Organized sub-pages
    ├── universities/            # Detailed info pages for specific partner universities
    └── general/                 # Utility pages (Developer Profile, Team, Requirements)
```

## Recent Updates & Security Features
- **Centralized Assets**: CSS and JS are decoupled from the HTML pages and managed centrally in the `assets/` directory.
- **Universal Navigation**: A streamlined navigation bar is injected into all sub-pages, eliminating "dead end" pages and improving UX.
- **Reverse Tabnabbing Protection**: All external links (`target="_blank"`) enforce `rel="noopener noreferrer"` to guarantee robust security against malicious redirects.

## Deployment
This is a fully static website. It can be hosted efficiently on any static hosting provider (e.g., GitHub Pages, Netlify, Vercel, or traditional shared hosting) without requiring a backend server.
