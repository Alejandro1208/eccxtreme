class Translator {
    constructor() {
        this.translations = null;
        this.currentLang = localStorage.getItem('selectedLanguage') || 'es';
    }

    async loadTranslations() {
        try {
            const response = await fetch('/translations.json');
            this.translations = await response.json();
            this.translate(this.currentLang);
        } catch (error) {
            console.error('Error loading translations:', error);
        }
    }

    translate(lang) {
        if (!this.translations || !this.translations[lang]) return;
        
        this.currentLang = lang;
        localStorage.setItem('selectedLanguage', lang);
        document.documentElement.lang = lang;
        
        document.querySelectorAll('[data-translate]').forEach(element => {
            const keys = element.getAttribute('data-translate').split('.');
            let translation = this.translations[lang];
            
            for (const key of keys) {
                translation = translation[key];
                if (!translation) break;
            }
            
            if (translation) {
                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                    element.placeholder = translation;
                } else {
                    element.textContent = translation;
                }
            }
        });
    }
}

// Inicializar traductor
document.addEventListener('DOMContentLoaded', async () => {
    const translator = new Translator();
    await translator.loadTranslations();

    // Agregar eventos a los botones de idioma
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const lang = btn.getAttribute('title').toLowerCase().substring(0, 2);
            translator.translate(lang);
            
            // Actualizar clases active
            document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });
});