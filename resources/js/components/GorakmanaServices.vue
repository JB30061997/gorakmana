<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const services = [
  { id: 'sites-web', number: '01', label: 'PRÉSENCE DIGITALE', title: 'Création de sites web', description: 'Un site web qui reflète votre identité, inspire confiance et transforme vos visiteurs en clients. Nous créons des expériences modernes, rapides et adaptées à tous les écrans.', features: ['Sites vitrines & institutionnels', 'Boutiques e-commerce', 'Design responsive & UX', 'Optimisation des performances'], image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1400&q=85', alt: 'Écran présentant un site web professionnel' },
  { id: 'applications-web', number: '02', label: 'SOLUTIONS MÉTIER', title: 'Développement d’applications web', description: 'Digitalisez vos processus avec des applications web sur mesure : outils de gestion, plateformes SaaS et interfaces métiers conçues autour de vos besoins.', features: ['ERP & CRM sur mesure', 'Plateformes SaaS', 'Automatisation des processus', 'Tableaux de bord & reporting'], image: 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1400&q=85', alt: 'Tableau de bord analytique sur ordinateur' },
  { id: 'applications-mobiles', number: '03', label: 'MOBILITÉ', title: 'Développement d’applications mobiles', description: 'Restez connecté à vos clients et à vos équipes grâce à des applications mobiles intuitives, performantes et connectées à vos outils de gestion.', features: ['Applications Android & iOS', 'Synchronisation avec le web', 'Notifications & suivi en temps réel', 'Interfaces mobiles intuitives'], image: 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9?auto=format&fit=crop&w=1400&q=85', alt: 'Smartphone avec interface mobile' },
  { id: 'gps', number: '04', label: 'GÉOLOCALISATION', title: 'Vente & intégration GPS', description: 'Équipez et pilotez votre flotte avec des solutions de géolocalisation adaptées au transport, à la logistique et à la gestion de véhicules.', features: ['Traceurs GPS & intégration', 'Suivi des véhicules', 'Historique des trajets', 'Alertes & rapports de flotte'], image: 'https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=1400&q=85', alt: 'Carte et navigation pour la géolocalisation' },
  { id: 'vps', number: '05', label: 'CLOUD & HÉBERGEMENT', title: 'Serveurs VPS & hébergement', description: 'Une infrastructure fiable pour héberger vos sites, applications et données. Nous vous accompagnons dans la configuration, le déploiement et le suivi de vos serveurs.', features: ['Mise en place de VPS', 'Déploiement d’applications', 'Sauvegardes & sécurisation', 'Administration & supervision'], image: 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1400&q=85', alt: 'Infrastructure serveur en centre de données' },
  { id: 'maintenance', number: '06', label: 'ACCOMPAGNEMENT', title: 'Support & maintenance applicative', description: 'Gardez vos applications disponibles, sécurisées et évolutives. Nous intervenons pour corriger les anomalies, assurer les mises à jour et accompagner vos utilisateurs.', features: ['Maintenance corrective', 'Maintenance évolutive', 'Assistance technique', 'Mises à jour & suivi'], image: 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=1400&q=85', alt: 'Équipe technique en réunion de support' },
  { id: 'seo', number: '07', label: 'VISIBILITÉ DIGITALE', title: 'Référencement naturel SEO', description: 'Améliorez votre visibilité sur les moteurs de recherche avec une stratégie SEO durable, une structure technique saine et des contenus adaptés à vos clients.', features: ['Audit SEO technique', 'Optimisation des pages', 'Recherche de mots-clés', 'Référencement local & suivi'], image: 'https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?auto=format&fit=crop&w=1400&q=85', alt: 'Analyse de performances et marketing digital' },
];

const root = ref(null);
let observer;
onMounted(() => {
  if (typeof IntersectionObserver === 'undefined') return;
  observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });
  root.value?.querySelectorAll('.gs-reveal').forEach(el => observer.observe(el));
});
onUnmounted(() => observer?.disconnect());
</script>

<template>
  <section id="services" ref="root" class="gs-services">
    <div class="gs-container">
      <div class="gs-heading gs-reveal">
        <div class="gs-kicker"><span class="gs-kicker-line"></span> CE QUE NOUS FAISONS</div>
        <h2>Des expertises pour <span>accélérer votre croissance.</span></h2>
        <p>De votre première présence en ligne à la gestion complète de votre infrastructure digitale, GORAKMANA vous accompagne à chaque étape.</p>
      </div>
      <article v-for="(service, index) in services" :id="service.id" :key="service.id" class="gs-service gs-reveal" :class="{ 'gs-reverse': index % 2 === 1 }">
        <div class="gs-visual">
          <div class="gs-image-wrap">
            <img :src="service.image" :alt="service.alt" loading="lazy" decoding="async" />
            <div class="gs-image-overlay"></div>
          </div>
          <span class="gs-image-index">{{ service.number }} / 07</span>
          <div class="gs-corner gs-corner-top"></div><div class="gs-corner gs-corner-bottom"></div>
        </div>
        <div class="gs-copy">
          <div class="gs-label"><span class="gs-dot"></span> {{ service.label }}</div>
          <div class="gs-number">{{ service.number }}</div>
          <h3>{{ service.title }}</h3>
          <p class="gs-description">{{ service.description }}</p>
          <ul class="gs-features"><li v-for="feature in service.features" :key="feature"><span>✓</span>{{ feature }}</li></ul>
          <a :href="`mailto:contact@gorakmana.com?subject=${encodeURIComponent('Demande de devis — ' + service.title)}`" class="gs-cta">Parlons de votre projet <span aria-hidden="true">↗</span></a>
        </div>
      </article>
      <div class="gs-end gs-reveal"><p>Une idée ? Un projet ? <strong>Construisons-le ensemble.</strong></p><a href="#contact">Discuter de mon projet <span>↗</span></a></div>
    </div>
  </section>
</template>

<style scoped>
.gs-services{--gs-red:#c8102e;--gs-text:#f5f5f6;--gs-muted:#a4a4ac;background:#080809;color:var(--gs-text);padding:110px 24px 95px;position:relative;overflow:hidden;font-family:Inter,ui-sans-serif,system-ui,sans-serif}
.gs-services::before{content:'';position:absolute;inset:0;pointer-events:none;background:radial-gradient(ellipse at 0% 20%,rgba(200,16,46,.055),transparent 40%),radial-gradient(ellipse at 100% 70%,rgba(200,16,46,.05),transparent 45%)}
.gs-container{max-width:1190px;margin:0 auto;position:relative}.gs-heading{text-align:center;max-width:810px;margin:0 auto 100px}.gs-kicker{display:flex;align-items:center;justify-content:center;gap:13px;font-size:11px;font-weight:800;letter-spacing:2.8px;color:#f33a54}.gs-kicker-line{height:1px;width:34px;background:var(--gs-red)}.gs-heading h2{font-size:clamp(36px,5vw,65px);line-height:1.13;letter-spacing:-.055em;margin:22px 0 23px;font-weight:850}.gs-heading h2 span{color:#ef1738}.gs-heading p{color:var(--gs-muted);font-size:16px;line-height:1.9;margin:0 auto;max-width:670px}
.gs-service{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:clamp(45px,7vw,110px);align-items:center;margin-bottom:145px;scroll-margin-top:110px}.gs-service.gs-reverse .gs-visual{order:2}.gs-service.gs-reverse .gs-copy{order:1}.gs-visual{height:420px;position:relative;isolation:isolate}.gs-image-wrap{position:absolute;inset:15px;border-radius:17px;overflow:hidden;border:1px solid rgba(255,255,255,.12);background:#17171a;box-shadow:0 25px 85px rgba(0,0,0,.45),0 0 55px rgba(200,16,46,.09)}.gs-image-wrap img{height:100%;width:100%;object-fit:cover;display:block;filter:saturate(.85) brightness(.72);transition:transform .9s cubic-bezier(.2,.7,.2,1),filter .7s}.gs-service:hover .gs-image-wrap img{transform:scale(1.075);filter:saturate(1) brightness(.88)}.gs-image-overlay{position:absolute;inset:0;background:linear-gradient(145deg,rgba(200,16,46,.22),transparent 52%,rgba(0,0,0,.36));pointer-events:none}.gs-image-index{position:absolute;bottom:33px;left:35px;font-size:12px;letter-spacing:2px;color:#fff;font-weight:750;text-shadow:0 2px 8px #000}.gs-corner{position:absolute;width:80px;height:80px;z-index:2;pointer-events:none}.gs-corner-top{left:0;top:0;border-top:2px solid var(--gs-red);border-left:2px solid var(--gs-red)}.gs-corner-bottom{right:0;bottom:0;border-right:2px solid var(--gs-red);border-bottom:2px solid var(--gs-red)}.gs-copy{position:relative;padding:18px 0}.gs-label{display:flex;align-items:center;gap:11px;color:#f43b55;font-size:11px;letter-spacing:2px;font-weight:800}.gs-dot{width:8px;height:8px;border-radius:50%;background:#f32a47;box-shadow:0 0 15px rgba(243,42,71,.8);animation:gs-pulse 2.5s infinite}.gs-number{position:absolute;right:0;top:-40px;font-size:clamp(85px,10vw,145px);font-weight:900;letter-spacing:-.09em;line-height:1;color:rgba(255,255,255,.035);pointer-events:none}.gs-copy h3{font-size:clamp(32px,3.7vw,49px);font-weight:830;line-height:1.15;letter-spacing:-.045em;margin:22px 0 20px;position:relative}.gs-description{color:var(--gs-muted);font-size:15px;line-height:1.95;margin:0 0 25px;max-width:530px}.gs-features{list-style:none;padding:0;margin:0 0 35px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 18px}.gs-features li{font-size:12.5px;color:#d7d7dd;display:flex;align-items:flex-start;gap:10px;line-height:1.5}.gs-features li span{color:#f43b55;font-weight:900}.gs-cta{display:inline-flex;align-items:center;gap:28px;background:var(--gs-red);color:#fff;text-decoration:none;border-radius:9px;padding:17px 23px;font-size:13px;font-weight:750;transition:transform .25s,box-shadow .25s}.gs-cta:hover{transform:translateY(-4px);box-shadow:0 15px 38px rgba(200,16,46,.32)}.gs-cta span{font-size:18px}.gs-end{text-align:center;border-top:1px solid rgba(255,255,255,.1);padding:55px 10px 0}.gs-end p{font-size:clamp(20px,3vw,30px);margin:0 0 24px;color:#c2c2c8}.gs-end p strong{color:#fff}.gs-end a{display:inline-flex;gap:20px;align-items:center;text-decoration:none;color:#f34860;font-weight:800;font-size:14px}.gs-reveal{opacity:0;transform:translateY(38px);transition:opacity .8s ease,transform .8s cubic-bezier(.2,.7,.2,1)}.gs-reveal.is-visible{opacity:1;transform:translateY(0)}@keyframes gs-pulse{50%{opacity:.45;box-shadow:0 0 6px rgba(243,42,71,.3)}}
@media(max-width:900px){.gs-service{gap:35px;margin-bottom:105px}.gs-visual{height:340px}.gs-features{grid-template-columns:1fr}.gs-copy h3{font-size:32px}}
@media(max-width:700px){.gs-services{padding:80px 18px}.gs-heading{margin-bottom:65px}.gs-heading p{font-size:14px}.gs-service{grid-template-columns:1fr;gap:30px;margin-bottom:85px}.gs-service.gs-reverse .gs-visual,.gs-service.gs-reverse .gs-copy{order:initial}.gs-visual{height:290px}.gs-copy{padding:0 5px}.gs-copy h3{font-size:34px}.gs-features{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.gs-number{top:-20px;font-size:100px}}
@media(max-width:420px){.gs-visual{height:245px}.gs-features{grid-template-columns:1fr}.gs-heading h2{font-size:35px}}
@media(prefers-reduced-motion:reduce){.gs-reveal{opacity:1;transform:none;transition:none}.gs-dot{animation:none}.gs-image-wrap img{transition:none}}
</style>
