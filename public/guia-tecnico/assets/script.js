// Guia Técnico - JavaScript
class TechnicalGuide {
    constructor() {
        this.init();
    }

    init() {
        this.setupNavigation();
        this.setupScrollToTop();
        this.setupCodeCopy();
        this.setupSearch();
        this.setupMobileMenu();
        this.updateActiveSection();
    }

    setupNavigation() {
        // Highlight active navigation item
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    setupScrollToTop() {
        const scrollButton = document.querySelector('.scroll-to-top');
        if (!scrollButton) return;

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
        });

        scrollButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    setupCodeCopy() {
        // Add copy button to code blocks
        document.querySelectorAll('.code-block').forEach(block => {
            const button = document.createElement('button');
            button.className = 'copy-btn';
            button.innerHTML = '📋';
            button.title = 'Copiar código';
            
            button.addEventListener('click', () => {
                const code = block.querySelector('pre').textContent;
                navigator.clipboard.writeText(code).then(() => {
                    button.innerHTML = '✅';
                    setTimeout(() => {
                        button.innerHTML = '📋';
                    }, 2000);
                });
            });

            block.style.position = 'relative';
            button.style.position = 'absolute';
            button.style.top = '10px';
            button.style.right = '10px';
            button.style.background = 'rgba(255,255,255,0.1)';
            button.style.border = 'none';
            button.style.borderRadius = '4px';
            button.style.padding = '5px 8px';
            button.style.cursor = 'pointer';
            button.style.fontSize = '12px';

            block.appendChild(button);
        });
    }

    setupSearch() {
        const searchInput = document.querySelector('#search-input');
        if (!searchInput) return;

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            this.filterContent(query);
        });
    }

    filterContent(query) {
        const sections = document.querySelectorAll('.card');
        
        sections.forEach(section => {
            const text = section.textContent.toLowerCase();
            if (text.includes(query) || query === '') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }

    setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });

            // Close sidebar when clicking outside
            document.addEventListener('click', (e) => {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });
        }
    }

    updateActiveSection() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.section-nav a');

        window.addEventListener('scroll', () => {
            let current = '';
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new TechnicalGuide();
});

// Utility functions
function highlightCode() {
    // Simple syntax highlighting for PHP code
    document.querySelectorAll('.code-block pre').forEach(block => {
        let html = block.innerHTML;
        
        // PHP tags
        html = html.replace(/(&lt;\?php|&lt;\?)/g, '<span class="syntax-php">$1</span>');
        
        // Classes
        html = html.replace(/\b(class|interface|trait)\s+(\w+)/g, '$1 <span class="syntax-class">$2</span>');
        
        // Methods
        html = html.replace(/\b(function)\s+(\w+)/g, '$1 <span class="syntax-method">$2</span>');
        
        // Variables
        html = html.replace(/(\$\w+)/g, '<span class="syntax-variable">$1</span>');
        
        // Strings
        html = html.replace(/(["'])([^"']*)\1/g, '<span class="syntax-string">$1$2$1</span>');
        
        // Comments
        html = html.replace(/(\/\/.*$|\/\*[\s\S]*?\*\/)/gm, '<span class="syntax-comment">$1</span>');
        
        block.innerHTML = html;
    });
}

// Auto-highlight code when content is loaded
document.addEventListener('DOMContentLoaded', highlightCode);