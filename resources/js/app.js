import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

function initializeScrollReveals() {
    const elements = document.querySelectorAll('[data-reveal]');

    if (!elements.length || !('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    document.documentElement.classList.add('cg-motion-ready');

    const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -32px 0px', threshold: 0.08 });

    elements.forEach(element => observer.observe(element));

    document.addEventListener('focusin', event => {
        const element = event.target.closest('[data-reveal]');
        if (element) {
            element.classList.add('is-visible');
            observer.unobserve(element);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeScrollReveals, { once: true });
} else {
    initializeScrollReveals();
}

Alpine.data('landingPage', () => ({
    darkMode: (() => {
        try {
            return localStorage.getItem('darkMode') === 'true';
        } catch (error) {
            return false;
        }
    })(),
    language: (() => {
        try {
            return localStorage.getItem('landingLanguage') === 'tl' ? 'tl' : 'en';
        } catch (error) {
            return 'en';
        }
    })(),
    copy: {
        en: {
            community: 'For the people of Barangay Maimpis',
            headline: 'Let’s take care of our barangay.',
            headlineAccent: 'Together.',
            introduction: 'Share a concern, follow its updates, and connect with our barangay officials and tanod to help keep Maimpis safe.',
            createAccount: 'Create a resident account',
            join: 'Join',
            signIn: 'Sign in',
            emergencyTitle: 'Need urgent help?',
            emergency: 'Emergency? Call 911. CivicGuard reports are not monitored in real time.',
            processIntro: 'From our kababayans, for our barangay',
            processTitle: 'Every concern has a next step.',
            reportStep: 'Share your concern',
            reportDescription: 'Add the details and location so the right team can review it.',
            staffStep: 'Barangay staff follow up',
            staffDescription: 'Our officials and tanod review and respond to reports.',
            progressStep: 'Follow the updates',
            progressDescription: 'Check the status and let us know when the concern is resolved.',
            residentsTitle: 'For residents',
            howItWorks: 'How we can help one another',
            teamwork: 'Working together helps the barangay respond more effectively.',
            residentDescription: 'Report an incident, check its progress, and receive important updates from the barangay.',
            getStarted: 'Get started',
            dashboard: 'Go to your dashboard',
            staffTitle: 'For barangay officials and tanod',
            staffDescription: 'Review reports, prioritize urgent concerns, and keep residents updated.',
            staffSignIn: 'Staff sign in',
            footerBrand: 'Barangay Maimpis Incident Management',
            footerEmergency: 'For emergencies, call 911.',
            chooseLanguage: 'Choose page language',
        },
        tl: {
            community: 'Para sa mga kababayan ng Barangay Maimpis',
            headline: 'Pangalagaan natin ang ating barangay.',
            headlineAccent: 'Sama-sama.',
            introduction: 'Ipaabot ang inyong mga alalahanin, subaybayan ang tugon, at makipag-ugnayan sa mga opisyal at tanod para sa mas ligtas na Maimpis.',
            createAccount: 'Gumawa ng account ng residente',
            join: 'Sumali',
            signIn: 'Magpatuloy',
            emergencyTitle: 'Kailangan ng agarang tulong?',
            emergency: 'May agarang panganib? Tumawag sa 911. Hindi agad natutugunan ang mga ulat sa CivicGuard.',
            processIntro: 'Mula sa mga kababayan, para sa barangay',
            processTitle: 'Bawat alalahanin ay may kasunod na hakbang.',
            reportStep: 'Ipaabot ang alalahanin',
            reportDescription: 'Ibigay ang detalye at lokasyon upang masuri ito ng kinauukulang pangkat.',
            staffStep: 'Kikilos ang barangay',
            staffDescription: 'Susuriin at aaksiyunan ng mga opisyal at tanod ang mga ulat.',
            progressStep: 'Subaybayan ang tugon',
            progressDescription: 'Tingnan ang kalagayan ng ulat at ipaalam kung nalutas na ang alalahanin.',
            residentsTitle: 'Para sa mga residente',
            howItWorks: 'Paano tayo makatutulong sa isa’t isa',
            teamwork: 'Mas maayos ang pagtugon kapag nagtutulungan ang mga residente at ang barangay.',
            residentDescription: 'Mag-ulat ng insidente, subaybayan ang pag-usad nito, at tumanggap ng mahahalagang abiso mula sa barangay.',
            getStarted: 'Magsimula',
            dashboard: 'Buksan ang pahina ng buod',
            staffTitle: 'Para sa mga opisyal at tanod',
            staffDescription: 'Suriin ang mga ulat, unahin ang mga agarang alalahanin, at ipaalam ang mga tugon sa mga residente.',
            staffSignIn: 'Pagpasok ng kawani',
            footerBrand: 'Pamamahala ng mga insidente sa Barangay Maimpis',
            footerEmergency: 'Sa oras ng kagipitan, tumawag sa 911.',
            chooseLanguage: 'Pumili ng wika',
        },
    },
    text(key) {
        return this.copy[this.language][key];
    },
    init() {
        this.$watch('darkMode', value => {
            try {
                localStorage.setItem('darkMode', value);
            } catch (error) {
                return;
            }
        });
    },
    setLanguage(language) {
        this.language = language;
        try {
            localStorage.setItem('landingLanguage', language);
        } catch (error) {
            return;
        }
    },
    toggleLanguage() {
        this.setLanguage(this.language === 'en' ? 'tl' : 'en');
    },
}));

Alpine.start();
