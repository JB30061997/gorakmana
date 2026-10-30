<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const menuOpen = ref(false);
const year = new Date().getFullYear();
const image = (name) =>
    `/images/gorakmana/${name}-transparent.${name === 'hero' ? 'png' : 'webp'}`;
const links = [
    { href: '#accueil', text: 'Accueil' },
    { href: '#services', text: 'Services' },
    { href: '#solutions', text: 'Solutions' },
    { href: '#apropos', text: 'À propos' },
    { href: '#contact', text: 'Contact' },
];
const socialLinks = [
    {
        label: 'LinkedIn',
        href: 'https://www.linkedin.com/company/gorakmana',
    },
    {
        label: 'Facebook',
        href: 'https://www.facebook.com/gorakmana',
    },
    {
        label: 'Instagram',
        href: 'https://www.instagram.com/gorakmana',
    },
];
const services = [
    {
        id: 'sites-web',
        no: '01',
        title: 'Création de sites web',
        desc: 'Des sites modernes, rapides et adaptés à votre image de marque. Sites vitrines, e-commerce et institutionnels, pensés pour convertir vos visiteurs en clients.',
        features: [
            'Design moderne et responsive',
            'Sites vitrines, e-commerce et institutionnels',
            'Performance et expérience utilisateur',
            'Formation et accompagnement',
        ],
        img: 'sites-web',
    },
    {
        id: 'applications-web',
        no: '02',
        title: 'Développement d’applications web',
        desc: 'Applications métiers, ERP, CRM et plateformes SaaS sur mesure pour digitaliser vos processus et améliorer la productivité de vos équipes.',
        features: [
            'Solutions 100 % sur mesure',
            'Interfaces modernes et intuitives',
            'Sécurité et performances élevées',
            'Intégration avec vos outils existants',
        ],
        img: 'applications-web',
    },
    {
        id: 'applications-mobiles',
        no: '03',
        title: 'Développement d’applications mobiles',
        desc: 'Applications Android et iOS performantes pour accompagner votre activité partout, connectées à vos systèmes et adaptées à vos utilisateurs.',
        features: [
            'Applications natives ou hybrides',
            'Synchronisation avec vos systèmes',
            'Notifications et géolocalisation',
            'Déploiement Android et iOS',
        ],
        img: 'applications-mobiles',
    },
    {
        id: 'gps',
        no: '04',
        title: 'Intégration et vente de solutions GPS',
        desc: 'Solutions de géolocalisation pour la gestion de flotte, le suivi de véhicules et la sécurité de vos biens, avec installation et intégration.',
        features: [
            'Vente de traceurs GPS',
            'Installation et configuration',
            'Suivi en temps réel',
            'Rapports et alertes détaillés',
        ],
        img: 'gps',
    },
    {
        id: 'vps',
        no: '05',
        title: 'Serveurs VPS & hébergement',
        desc: 'Des infrastructures puissantes et sécurisées pour héberger vos applications, garantir leur disponibilité et accompagner votre croissance.',
        features: [
            'VPS haute performance',
            'Configuration et déploiement',
            'Sauvegardes et sécurité',
            'Supervision et accompagnement',
        ],
        img: 'vps',
    },
    {
        id: 'maintenance',
        no: '06',
        title: 'Support & maintenance applicative',
        desc: 'Un accompagnement continu pour maintenir la stabilité, la sécurité et les performances de vos applications au quotidien.',
        features: [
            'Maintenance corrective et évolutive',
            'Assistance technique réactive',
            'Mises à jour et optimisation',
            'Suivi et supervision',
        ],
        img: 'maintenance',
    },
    {
        id: 'seo',
        no: '07',
        title: 'Référencement naturel SEO',
        desc: 'Améliorez votre visibilité sur Google et attirez davantage de clients grâce à une stratégie de référencement naturel adaptée à votre activité.',
        features: [
            'Audit SEO technique et éditorial',
            'Optimisation du contenu',
            'SEO local et performances',
            'Suivi des positions et rapports',
        ],
        img: 'seo',
    },
];
const solutions = [
    {
        no: '01',
        name: 'TransPilot',
        image: 'solution-transpilot-3d.webp',
        text: 'Gestion du transport touristique et des opérations.',
    },
    {
        no: '02',
        name: 'RentFlow',
        image: 'solution-rentflow-3d.webp',
        text: 'Location automobile, contrats et flotte.',
    },
    {
        no: '03',
        name: 'UsinePilot',
        image: 'solution-usinepilot-3d.webp',
        text: 'Gestion industrielle et production.',
    },
    {
        no: '04',
        name: 'WorkPilot',
        image: 'solution-workpilot-3d.webp',
        text: 'Organisation des équipes et des projets.',
    },
];
let observer;
function closeMenu() {
    menuOpen.value = false;
}
function moveArtwork(event) {
    if (
        !window.matchMedia('(hover: hover) and (pointer: fine)').matches ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        return;
    }

    const bounds = event.currentTarget.getBoundingClientRect();
    const horizontalPosition =
        (event.clientX - bounds.left) / bounds.width - 0.5;
    const verticalPosition = (event.clientY - bounds.top) / bounds.height - 0.5;

    event.currentTarget.style.setProperty(
        '--art-rotate-x',
        `${verticalPosition * -5}deg`,
    );
    event.currentTarget.style.setProperty(
        '--art-rotate-y',
        `${horizontalPosition * 7}deg`,
    );
    event.currentTarget.style.setProperty(
        '--art-shift-x',
        `${horizontalPosition * 8}px`,
    );
    event.currentTarget.style.setProperty(
        '--art-shift-y',
        `${verticalPosition * 6}px`,
    );
}
function resetArtwork(event) {
    event.currentTarget.style.removeProperty('--art-rotate-x');
    event.currentTarget.style.removeProperty('--art-rotate-y');
    event.currentTarget.style.removeProperty('--art-shift-x');
    event.currentTarget.style.removeProperty('--art-shift-y');
}
onMounted(() => {
    if (typeof IntersectionObserver !== 'undefined') {
        observer = new IntersectionObserver(
            (entries) =>
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                }),
            { threshold: 0.08 },
        );
        document
            .querySelectorAll('.g-reveal')
            .forEach((el) => observer.observe(el));
    } else
        document
            .querySelectorAll('.g-reveal')
            .forEach((el) => el.classList.add('visible'));
});
onUnmounted(() => observer?.disconnect());
</script>

<template>
    <Head title="GORAKMANA | Solutions digitales" />
    <div class="g-site" data-theme="light">
        <header class="g-header">
            <div class="g-container g-nav">
                <a class="g-logo" href="#accueil" @click="closeMenu">
                    <img
                        src="/images/gorakmana/logo.png"
                        alt="GoRakmana Digital Solutions"
                    />
                </a>
                <nav class="g-links" aria-label="Navigation principale">
                    <a
                        v-for="link in links"
                        :key="link.href"
                        :href="link.href"
                        >{{ link.text }}</a
                    >
                </nav>
                <div class="g-actions">
                    <div class="g-socials" aria-label="Réseaux sociaux">
                        <a
                            v-for="social in socialLinks"
                            :key="social.label"
                            :href="social.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            :aria-label="social.label"
                        >
                            <svg
                                v-if="social.label === 'LinkedIn'"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M5.3 7.4H1.8V22h3.5V7.4ZM3.55 2A2.05 2.05 0 1 0 3.55 6.1 2.05 2.05 0 0 0 3.55 2ZM22 13.6c0-4.4-2.35-6.45-5.48-6.45a4.72 4.72 0 0 0-4.25 2.34V7.4H8.78V22h3.49v-7.23c0-1.9.36-3.75 2.72-3.75 2.33 0 2.36 2.18 2.36 3.88V22H22v-8.4Z" />
                            </svg>
                            <svg
                                v-else-if="social.label === 'Facebook'"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path d="M14 8.5V6.7c0-.78.52-.96.89-.96H17V2.14L14.1 2C10.88 2 9.68 3.92 9.68 6.45V8.5H7v4h2.68V22H14v-9.5h2.86l.38-4H14Z" />
                            </svg>
                            <svg
                                v-else
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path fill-rule="evenodd" d="M7.2 2h9.6A5.2 5.2 0 0 1 22 7.2v9.6a5.2 5.2 0 0 1-5.2 5.2H7.2A5.2 5.2 0 0 1 2 16.8V7.2A5.2 5.2 0 0 1 7.2 2Zm-.18 2.1A2.92 2.92 0 0 0 4.1 7.02v9.96a2.92 2.92 0 0 0 2.92 2.92h9.96a2.92 2.92 0 0 0 2.92-2.92V7.02a2.92 2.92 0 0 0-2.92-2.92H7.02ZM12 7.4a4.6 4.6 0 1 1 0 9.2 4.6 4.6 0 0 1 0-9.2Zm0 2.1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Zm5.82-3.02a1.08 1.08 0 1 1-2.16 0 1.08 1.08 0 0 1 2.16 0Z" />
                            </svg>
                        </a>
                    </div>
                    >
                    <button
                        class="g-burger"
                        type="button"
                        :aria-expanded="menuOpen"
                        aria-label="Ouvrir le menu"
                        @click="menuOpen = !menuOpen"
                    >
                        {{ menuOpen ? '✕' : '☰' }}
                    </button>
                </div>
            </div>
            <nav
                v-if="menuOpen"
                class="g-mobile-nav"
                aria-label="Navigation mobile"
            >
                <a
                    v-for="link in links"
                    :key="link.href"
                    :href="link.href"
                    @click="closeMenu"
                    >{{ link.text }}</a
                >
            </nav>
        </header>

        <main>
            <section id="accueil" class="g-hero">
                <div class="g-circuit" aria-hidden="true"></div>
                <div class="g-container g-hero-grid">
                    <div class="g-hero-copy">
                        <p class="g-kicker">
                            <span class="g-dot"></span> VOTRE PARTENAIRE DIGITAL
                        </p>
                        <h1>
                            Des solutions digitales
                            <em>pour faire grandir votre entreprise.</em>
                        </h1>
                        <p class="g-intro">
                            Nous concevons des sites web, applications,
                            solutions SaaS et infrastructures qui transforment
                            vos idées en véritables leviers de croissance.
                        </p>
                        <div class="g-hero-buttons">
                            <a class="g-btn g-btn-ghost" href="#services"
                                >Découvrir nos services <span>→</span></a
                            >
                        </div>
                        <div class="g-stats">
                            <div>
                                <strong>Web</strong
                                ><span>Sites & e-commerce</span>
                            </div>
                            <div>
                                <strong>SaaS</strong
                                ><span>Solutions métiers</span>
                            </div>
                            <div>
                                <strong>GPS</strong><span>Suivi de flotte</span>
                            </div>
                            <div>
                                <strong>VPS</strong
                                ><span>Cloud & hébergement</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="g-hero-art"
                        aria-label="Illustration de plateformes digitales sur ordinateur et mobile"
                    >
                        <div
                            class="g-art-stage"
                            @pointermove="moveArtwork"
                            @pointerleave="resetArtwork"
                        >
                            <div class="g-art-glow"></div>
                            <img
                                :src="image('hero')"
                                alt="Ordinateur et smartphone présentant des solutions digitales GORAKMANA"
                                width="1536"
                                height="1024"
                                fetchpriority="high"
                                decoding="async"
                            />
                        </div>
                    </div>
                </div>
                <a
                    class="g-scroll"
                    href="#services"
                    aria-label="Découvrir les services"
                    ><span></span
                ></a>
            </section>

            <section id="services" class="g-services">
                <div class="g-container">
                    <div class="g-heading g-reveal">
                        <span class="g-kicker">NOS SERVICES</span>
                        <h2>
                            Des services complets pour<br /><em
                                >répondre à tous vos besoins.</em
                            >
                        </h2>
                        <p>
                            De la création de votre plateforme à son hébergement
                            et à sa visibilité, nous vous accompagnons à chaque
                            étape.
                        </p>
                    </div>
                </div>
                <article
                    v-for="(service, index) in services"
                    :id="service.id"
                    :key="service.id"
                    class="g-service"
                    :class="{ 'g-reverse': index % 2 === 1 }"
                >
                    <div class="g-container g-service-grid">
                        <div class="g-service-copy g-reveal">
                            <div class="g-service-heading">
                                <span class="g-service-no">{{
                                    service.no
                                }}</span>
                                <div>
                                    <span class="g-mini"
                                        >EXPERTISE / {{ service.no }}</span
                                    >
                                    <h3>{{ service.title }}</h3>
                                </div>
                            </div>
                            <p>{{ service.desc }}</p>
                            <ul>
                                <li
                                    v-for="feature in service.features"
                                    :key="feature"
                                >
                                    <span>✓</span>{{ feature }}
                                </li>
                            </ul>
                        </div>
                        <div class="g-service-art g-reveal">
                            <div
                                class="g-art-stage"
                                @pointermove="moveArtwork"
                                @pointerleave="resetArtwork"
                            >
                                <div class="g-image-glow"></div>
                                <img
                                    :src="image(service.img)"
                                    :alt="`Illustration du service : ${service.title}`"
                                    width="1880"
                                    height="840"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <span class="g-art-label"
                                >GORAKMANA / {{ service.no }}</span
                            >
                        </div>
                    </div>
                </article>
                <div class="g-container g-services-cta g-reveal">
                    <a
                        class="g-btn g-btn-red"
                        href="mailto:contact@gorakmana.com?subject=Demande%20de%20devis"
                        >Demander un devis <span>↗</span></a
                    >
                </div>
            </section>

            <section id="solutions" class="g-section g-solutions">
                <div class="g-container">
                    <div class="g-heading g-reveal">
                        <span class="g-kicker">NOS SOLUTIONS</span>
                        <h2>
                            Des logiciels métier pour
                            <em>booster votre activité.</em>
                        </h2>
                        <p>
                            Découvrez notre écosystème de solutions SaaS pensées
                            pour répondre aux besoins de chaque secteur.
                        </p>
                    </div>
                    <div class="g-solutions-grid">
                        <article
                            v-for="s in solutions"
                            :key="s.name"
                            class="g-solution g-reveal"
                        >
                            <div
                                class="g-solution-visual g-art-stage"
                                @pointermove="moveArtwork"
                                @pointerleave="resetArtwork"
                            >
                                <span class="g-solution-no">{{ s.no }}</span>
                                <span class="g-solution-glow"></span>
                                <img
                                    :src="`/images/gorakmana/${s.image}`"
                                    :alt="`Icône 3D de la solution ${s.name}`"
                                    width="512"
                                    height="512"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                            <div class="g-solution-content">
                                <span class="g-mini">SOLUTION SAAS</span>
                                <h3>{{ s.name }}</h3>
                                <p>{{ s.text }}</p>
                                <a href="#contact"
                                    >Découvrir la solution <span>↗</span></a
                                >
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <section id="apropos" class="g-section g-about">
                <div class="g-container g-about-grid">
                    <div class="g-reveal">
                        <span class="g-kicker">À PROPOS DE GORAKMANA</span>
                        <h2>
                            Un partenaire de confiance pour votre
                            <em>transformation digitale.</em>
                        </h2>
                        <p>
                            Nous accompagnons les entreprises dans la
                            réalisation de leurs projets digitaux avec des
                            solutions innovantes, fiables et évolutives. Notre
                            objectif : créer des outils utiles, performants et
                            adaptés à votre métier.
                        </p>
                    </div>
                    <div class="g-about-tiles g-reveal">
                        <div>
                            <strong>7</strong><span>Domaines d’expertise</span>
                        </div>
                        <div><strong>4</strong><span>Solutions SaaS</span></div>
                        <div>
                            <strong>360°</strong
                            ><span>Accompagnement digital</span>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact" class="g-contact">
                <div class="g-container g-contact-grid g-reveal">
                    <div>
                        <span class="g-kicker">CONTACT</span>
                        <h2>Votre projet <em>commence ici.</em></h2>
                        <p>
                            Vous avez une idée ? Discutons-en et construisons
                            ensemble la solution adaptée à vos besoins.
                        </p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="g-footer">
            <div class="g-container g-footer-inner">
                <a class="g-logo" href="#accueil">
                    <img
                        src="/images/gorakmana/logo.png"
                        alt="GoRakmana Digital Solutions"
                    />
                </a>
                <nav aria-label="Navigation de pied de page">
                    <a
                        v-for="link in links"
                        :key="link.href"
                        :href="link.href"
                        >{{ link.text }}</a
                    >
                </nav>
                <small>© {{ year }} GORAKMANA. Tous droits réservés.</small>
            </div>
        </footer>
    </div>
</template>

<style scoped>
:global(html) {
    scroll-behavior: smooth;
}
:global(body) {
    margin: 0;
}
.g-site {
    --bg: #070709;
    --bg2: #0e0e12;
    --surface: #15151a;
    --text: #f8f8fa;
    --muted: #a1a1ae;
    --border: rgba(255, 255, 255, 0.11);
    --red: #ec123c;
    --red2: #ff3b5d;
    --header: rgba(7, 7, 9, 0.94);
    font-family:
        Inter,
        ui-sans-serif,
        system-ui,
        -apple-system,
        sans-serif;
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    overflow-x: hidden;
    transition:
        background 0.35s,
        color 0.35s;
}
.g-site[data-theme='light'] {
    --bg: #fffafa;
    --bg2: #f8f1f2;
    --surface: #fff;
    --text: #17171d;
    --muted: #62626c;
    --border: rgba(25, 18, 25, 0.13);
    --red: #c8102e;
    --red2: #df1740;
    --header: rgba(255, 250, 250, 0.96);
}
.g-site * {
    box-sizing: border-box;
}
.g-site a {
    text-decoration: none;
    color: inherit;
}
.g-site button {
    font: inherit;
}
.g-container {
    width: min(1160px, calc(100% - 48px));
    margin: 0 auto;
}
.g-red,
.g-site em {
    color: var(--red);
}
.g-site em {
    font-style: normal;
}
.g-header {
    height: 76px;
    background: var(--header);
    border-bottom: 1px solid var(--border);
    position: relative;
    z-index: 30;
    backdrop-filter: blur(18px);
}
.g-nav {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}
.g-logo {
    display: inline-flex;
    align-items: center;
    width: 205px;
    height: 58px;
    padding: 4px 8px;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
}
.g-logo img {
    display: block;
    width: 100%;
    height: auto;
    object-fit: contain;
}
.g-mark {
    width: 34px;
    height: 34px;
    display: grid;
    place-items: center;
    color: var(--red);
    font-size: 22px;
    font-weight: 900;
    border: 2px solid var(--red);
    border-radius: 9px;
    transform: rotate(-9deg);
    margin-right: 3px;
}
.g-links,
.g-actions {
    display: flex;
    align-items: center;
    gap: 25px;
}
.g-links a {
    font-size: 13px;
    color: var(--muted);
    transition: color 0.2s;
}
.g-links a:hover {
    color: var(--red);
}
.g-actions {
    gap: 10px;
}
.g-socials {
    display: flex;
    align-items: center;
    gap: 6px;
}
.g-socials a {
    display: grid;
    width: 32px;
    height: 32px;
    place-items: center;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--surface);
    color: var(--muted);
    transition:
        color 0.2s,
        border-color 0.2s,
        transform 0.2s;
}
.g-socials a:hover {
    color: var(--red);
    border-color: var(--red);
    transform: translateY(-2px);
}
.g-socials svg {
    width: 15px;
    height: 15px;
    fill: currentColor;
}
.g-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding: 15px 22px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 750;
    white-space: nowrap;
    transition:
        transform 0.25s,
        box-shadow 0.25s,
        border-color 0.25s;
}
.g-btn:hover {
    transform: translateY(-3px);
}
.g-btn-red {
    background: var(--red);
    color: #fff !important;
    box-shadow: 0 5px 25px rgba(226, 11, 50, 0.13);
}
.g-btn-red:hover {
    box-shadow: 0 10px 35px rgba(226, 11, 50, 0.4);
}
.g-services-cta {
    display: flex;
    justify-content: center;
    padding-top: 20px;
    padding-bottom: 90px;
}
.g-btn-ghost {
    border: 1px solid var(--border);
    background: var(--surface);
}
.g-btn-ghost:hover {
    border-color: var(--red);
}
.g-burger {
    display: none;
    background: none;
    border: 0;
    color: var(--text);
    font-size: 24px;
    cursor: pointer;
}
.g-mobile-nav {
    display: none;
}
.g-hero {
    position: relative;
    min-height: 675px;
    display: flex;
    align-items: center;
    overflow: hidden;
    isolation: isolate;
}
.g-hero:before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(
            ellipse at 85% 43%,
            rgba(240, 13, 58, 0.21),
            transparent 52%
        ),
        linear-gradient(90deg, transparent 48%, rgba(220, 12, 50, 0.045) 100%);
    pointer-events: none;
}
.g-circuit {
    position: absolute;
    inset: 0;
    opacity: 0.4;
    background-image:
        linear-gradient(rgba(240, 13, 58, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(240, 13, 58, 0.1) 1px, transparent 1px),
        repeating-linear-gradient(
            150deg,
            transparent 0,
            transparent 100px,
            rgba(240, 13, 58, 0.17) 101px,
            transparent 103px
        );
    background-size:
        90px 90px,
        90px 90px,
        100% 100%;
    animation: g-bg 20s linear infinite;
}
.g-hero-grid {
    position: relative;
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 22px;
    padding: 85px 0 100px;
}
.g-hero-copy {
    position: relative;
    z-index: 2;
    animation: g-rise 0.9s both;
}
.g-kicker {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: var(--red);
    font-size: 11px;
    font-weight: 850;
    letter-spacing: 2.1px;
    text-transform: uppercase;
}
.g-dot {
    height: 7px;
    width: 7px;
    background: var(--red);
    border-radius: 50%;
    box-shadow: 0 0 13px var(--red);
    animation: g-blink 2s infinite;
}
.g-hero h1 {
    font-size: clamp(44px, 5vw, 70px);
    line-height: 1.08;
    letter-spacing: -0.065em;
    margin: 22px 0 23px;
    font-weight: 850;
}
.g-intro {
    color: var(--muted);
    font-size: 15px;
    line-height: 1.9;
    max-width: 540px;
}
.g-hero-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 30px;
}
.g-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    border-top: 1px solid var(--border);
    margin-top: 58px;
    padding-top: 22px;
}
.g-stats strong {
    display: block;
    color: var(--red);
    font-size: 22px;
}
.g-stats span {
    font-size: 10px;
    color: var(--muted);
    display: block;
    margin-top: 6px;
}
.g-hero-art {
    position: relative;
    min-height: 470px;
    display: flex;
    align-items: center;
    justify-content: center;
    perspective: 1100px;
}
.g-art-stage {
    --art-rotate-x: 0deg;
    --art-rotate-y: 0deg;
    --art-shift-x: 0px;
    --art-shift-y: 0px;
    position: relative;
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    transform: translate3d(var(--art-shift-x), var(--art-shift-y), 0)
        rotateX(var(--art-rotate-x)) rotateY(var(--art-rotate-y));
    transform-style: preserve-3d;
    transition: transform 0.22s ease-out;
}
.g-art-glow,
.g-image-glow {
    position: absolute;
    inset: 10%;
    background: radial-gradient(
        ellipse,
        rgba(240, 12, 54, 0.4),
        transparent 65%
    );
    filter: blur(35px);
}
.g-hero-art img {
    position: relative;
    z-index: 1;
    width: min(112%, 680px);
    max-height: 520px;
    object-fit: contain;
    filter: drop-shadow(0 28px 42px rgba(0, 0, 0, 0.42))
        drop-shadow(0 8px 30px rgba(231, 10, 49, 0.24));
    animation: g-object-float 6s ease-in-out infinite;
}
.g-site[data-theme='light'] .g-hero-art img {
    filter: drop-shadow(0 24px 30px rgba(40, 20, 25, 0.2))
        drop-shadow(0 8px 24px rgba(190, 10, 50, 0.12));
}
.g-scroll {
    position: absolute;
    bottom: 25px;
    left: 50%;
    height: 32px;
    width: 20px;
    border: 1px solid var(--muted);
    border-radius: 16px;
    display: flex;
    justify-content: center;
    padding: 5px;
}
.g-scroll span {
    height: 8px;
    width: 3px;
    background: var(--red);
    border-radius: 3px;
    animation: g-scroll 2s infinite;
}
.g-services {
    padding-top: 85px;
}
.g-heading {
    text-align: center;
    max-width: 760px;
    margin: 0 auto 55px;
}
.g-heading h2 {
    font-size: clamp(32px, 4.2vw, 49px);
    line-height: 1.16;
    letter-spacing: -0.05em;
    margin: 16px 0;
}
.g-heading p {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.85;
}
.g-service {
    position: relative;
    overflow: hidden;
    border-top: 1px solid var(--border);
    background: var(--bg);
}
.g-service:nth-of-type(even) {
    background: var(--bg2);
}
.g-service:after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--red), transparent);
    opacity: 0.32;
}
.g-service-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 55px;
    min-height: 385px;
    padding-top: 40px;
    padding-bottom: 40px;
}
.g-service-copy {
    position: relative;
    z-index: 2;
}
.g-service-heading {
    display: flex;
    align-items: center;
    gap: 20px;
}
.g-service-no {
    font-size: clamp(60px, 7vw, 90px);
    font-weight: 900;
    letter-spacing: -0.07em;
    color: var(--red);
    line-height: 1;
    opacity: 0.85;
}
.g-mini {
    font-size: 10px;
    color: var(--red);
    font-weight: 800;
    letter-spacing: 1.5px;
}
.g-service h3 {
    font-size: clamp(23px, 2.6vw, 34px);
    line-height: 1.17;
    letter-spacing: -0.035em;
    margin: 8px 0;
}
.g-service-copy > p {
    font-size: 14px;
    line-height: 1.85;
    color: var(--muted);
    max-width: 510px;
    margin: 15px 0;
}
.g-service ul {
    list-style: none;
    margin: 18px 0 23px;
    padding: 0;
}
.g-service li {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 9px 0;
    font-size: 12px;
}
.g-service li span {
    display: grid;
    place-items: center;
    background: var(--red);
    color: #fff;
    border-radius: 50%;
    width: 17px;
    height: 17px;
    font-size: 10px;
    flex-shrink: 0;
}
.g-service-art {
    height: 315px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: visible;
    perspective: 1000px;
}
.g-service-art img {
    width: min(100%, 620px);
    height: min(100%, 300px);
    object-fit: contain;
    position: relative;
    z-index: 1;
    filter: drop-shadow(0 22px 28px rgba(0, 0, 0, 0.34))
        drop-shadow(0 5px 24px rgba(237, 13, 52, 0.2));
    animation: g-object-float 6.5s ease-in-out infinite;
}
.g-service:nth-of-type(even) .g-service-art img {
    animation-delay: -2.2s;
}
.g-site[data-theme='light'] .g-service-art img {
    filter: drop-shadow(0 20px 24px rgba(42, 22, 26, 0.18))
        drop-shadow(0 5px 20px rgba(210, 15, 52, 0.12));
}
.g-art-label {
    position: absolute;
    bottom: 5px;
    right: 15px;
    font-size: 9px;
    color: var(--red);
    letter-spacing: 2px;
}
.g-reverse .g-service-copy {
    order: 2;
}
.g-reverse .g-service-art {
    order: 1;
}
.g-section {
    padding: 100px 0;
}
.g-solutions {
    background: var(--bg2);
}
.g-solutions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
}
.g-solution {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0;
    min-height: 390px;
    transition:
        transform 0.3s,
        border-color 0.3s,
        box-shadow 0.3s,
        background 0.35s;
}
.g-solution::before {
    content: '';
    position: absolute;
    inset: 0;
    z-index: -1;
    background: radial-gradient(
        circle at 50% 10%,
        rgba(200, 16, 46, 0.15),
        transparent 40%
    );
    opacity: 0;
    transition: opacity 0.3s;
}
.g-solution:hover {
    transform: translateY(-8px);
    border-color: var(--red);
    box-shadow: 0 22px 50px rgba(0, 0, 0, 0.2);
}
.g-solution:hover::before {
    opacity: 1;
}
.g-solution-visual {
    width: 100%;
    height: 205px;
    pointer-events: auto;
    border-bottom: 1px solid var(--border);
    background:
        linear-gradient(145deg, rgba(200, 16, 46, 0.08), transparent 55%),
        var(--bg2);
}
.g-solution-visual img {
    position: relative;
    z-index: 1;
    width: 205px;
    height: 205px;
    object-fit: contain;
    filter: drop-shadow(0 16px 18px rgba(0, 0, 0, 0.38))
        drop-shadow(0 4px 16px rgba(200, 16, 46, 0.24));
    animation: g-solution-float 5.8s ease-in-out infinite;
}
.g-solution:nth-child(2n) .g-solution-visual img {
    animation-delay: -1.7s;
}
.g-solution-glow {
    position: absolute;
    width: 165px;
    height: 70px;
    border-radius: 50%;
    background: rgba(200, 16, 46, 0.23);
    filter: blur(24px);
    transform: translateY(48px);
}
.g-solution-no {
    position: absolute;
    top: 16px;
    right: 17px;
    z-index: 2;
    color: var(--red);
    font-size: 10px;
    font-weight: 850;
    letter-spacing: 1.5px;
    opacity: 0.7;
}
.g-solution-content {
    padding: 24px 24px 26px;
}
.g-site[data-theme='light'] .g-solution-visual img {
    filter: drop-shadow(0 15px 16px rgba(50, 28, 32, 0.2))
        drop-shadow(0 4px 14px rgba(200, 16, 46, 0.14));
}
.g-site[data-theme='light'] .g-solution:hover {
    box-shadow: 0 22px 48px rgba(83, 25, 37, 0.12);
}
.g-solution h3 {
    font-size: 22px;
    letter-spacing: -0.025em;
    margin: 7px 0 9px;
}
.g-solution p {
    font-size: 13px;
    line-height: 1.7;
    color: var(--muted);
    min-height: 45px;
    margin: 0 0 18px;
}
.g-solution a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--red);
    font-size: 12px;
    font-weight: 800;
}
.g-solution a span {
    transition: transform 0.2s;
}
.g-solution a:hover span {
    transform: translate(2px, -2px);
}
.g-about-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    align-items: center;
    gap: 80px;
}
.g-about h2 {
    font-size: clamp(30px, 4vw, 49px);
    letter-spacing: -0.05em;
    line-height: 1.17;
    margin: 15px 0;
}
.g-about p {
    font-size: 14px;
    line-height: 1.9;
    color: var(--muted);
}
.g-about-tiles {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.g-about-tiles > div {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 22px 15px;
}
.g-about-tiles strong {
    font-size: 29px;
    color: var(--red);
    display: block;
}
.g-about-tiles span {
    font-size: 11px;
    color: var(--muted);
    line-height: 1.6;
}
.g-contact {
    padding: 80px 0;
    background:
        radial-gradient(
            ellipse at 65% 100%,
            rgba(227, 15, 51, 0.18),
            transparent 65%
        ),
        var(--bg2);
    border-top: 1px solid var(--border);
}
.g-contact-grid {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 30px;
}
.g-contact h2 {
    font-size: clamp(32px, 4vw, 50px);
    margin: 15px 0;
    letter-spacing: -0.05em;
}
.g-contact p {
    font-size: 14px;
    color: var(--muted);
    line-height: 1.8;
}
.g-footer {
    border-top: 1px solid var(--border);
    padding: 28px 0;
}
.g-footer-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
}
.g-footer nav {
    display: flex;
    gap: 18px;
}
.g-footer nav a,
.g-footer small {
    font-size: 11px;
    color: var(--muted);
}
.g-reveal {
    opacity: 0;
    transform: translateY(32px);
    transition:
        opacity 0.75s ease,
        transform 0.75s ease;
}
.g-reveal.visible {
    opacity: 1;
    transform: translateY(0);
}
@keyframes g-rise {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: none;
    }
}
@keyframes g-object-float {
    50% {
        transform: translateY(-10px);
    }
}
@keyframes g-solution-float {
    50% {
        transform: translateY(-7px) rotate(1deg);
    }
}
@keyframes g-blink {
    50% {
        opacity: 0.3;
    }
}
@keyframes g-scroll {
    to {
        transform: translateY(11px);
        opacity: 0;
    }
}
@keyframes g-bg {
    to {
        background-position:
            90px 90px,
            90px 90px,
            0 0;
    }
}
@media (max-width: 1050px) {
    .g-links {
        gap: 12px;
    }
    .g-hero h1 {
        font-size: clamp(40px, 5vw, 57px);
    }
    .g-service-grid {
        gap: 22px;
    }
    .g-service-no {
        font-size: 60px;
    }
}
@media (max-width: 820px) {
    .g-links,
    .g-nav-cta {
        display: none;
    }
    .g-burger {
        display: block;
    }
    .g-mobile-nav {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 76px;
        left: 0;
        right: 0;
        background: var(--header);
        padding: 15px 24px 22px;
        border-bottom: 1px solid var(--border);
    }
    .g-mobile-nav a {
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }
    .g-hero-grid {
        grid-template-columns: 1fr;
        text-align: center;
        padding: 70px 0 80px;
    }
    .g-intro {
        margin-left: auto;
        margin-right: auto;
    }
    .g-hero-buttons {
        justify-content: center;
    }
    .g-hero-art {
        min-height: 350px;
        max-width: 560px;
        width: 100%;
        margin: auto;
    }
    .g-stats {
        max-width: 600px;
        margin: 40px auto 0;
    }
    .g-service-grid {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 40px 0;
    }
    .g-service-copy,
    .g-reverse .g-service-copy {
        order: 1;
    }
    .g-service-art,
    .g-reverse .g-service-art {
        order: 2;
    }
    .g-service-art {
        height: 310px;
    }
    .g-solutions-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .g-about-grid {
        grid-template-columns: 1fr;
        gap: 35px;
    }
    .g-contact-grid {
        align-items: start;
        flex-direction: column;
    }
    .g-footer-inner {
        flex-direction: column;
    }
}
@media (max-width: 520px) {
    .g-container {
        width: calc(100% - 32px);
    }
    .g-logo {
        width: 165px;
        height: 50px;
        padding: 3px 6px;
    }
    .g-hero h1 {
        font-size: clamp(38px, 10vw, 53px);
    }
    .g-hero-art {
        min-height: 240px;
    }
    .g-hero-buttons {
        flex-direction: column;
    }
    .g-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    .g-heading h2 {
        font-size: 32px;
    }
    .g-service-heading {
        gap: 12px;
    }
    .g-service-no {
        font-size: 53px;
    }
    .g-service h3 {
        font-size: 24px;
    }
    .g-service-art {
        height: 240px;
    }
    .g-solutions-grid {
        grid-template-columns: 1fr;
    }
    .g-about-tiles {
        grid-template-columns: 1fr;
    }
    .g-footer nav {
        flex-wrap: wrap;
        justify-content: center;
    }
    .g-section {
        padding: 70px 0;
    }
}
@media (prefers-reduced-motion: reduce) {
    .g-site * {
        animation: none !important;
        transition: none !important;
    }
    .g-reveal {
        opacity: 1;
        transform: none;
    }
}
</style>
