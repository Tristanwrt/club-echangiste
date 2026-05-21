<!-- ── FOOTER ──────────────────────────────────────────────── -->
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <!-- Brand -->
      <div class="footer__brand">
        <a href="/" class="nav__logo" style="font-size:1.1rem">
          <span class="nav__logo-dot"></span>
          Club<em style="color:var(--primary)">Échangiste</em>.fr
        </a>
        <p>L'annuaire de référence des clubs échangistes et libertins de France. Discret, fiable, exhaustif.</p>
        <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold" style="font-size:.82rem;padding:.6rem 1.25rem">
          🔥 Rencontres libertines en ligne
        </a>
      </div>

      <!-- Navigation -->
      <div class="footer__col">
        <p class="footer__col-title">Annuaire</p>
        <ul>
          <li><a href="/regions">Toutes les régions</a></li>
          <li><a href="/region/ile-de-france">Île-de-France</a></li>
          <li><a href="/region/auvergne-rhone-alpes">Rhône-Alpes</a></li>
          <li><a href="/region/provence-alpes-cote-d-azur">PACA</a></li>
          <li><a href="/region/occitanie">Occitanie</a></li>
          <li><a href="/region/nouvelle-aquitaine">Nouvelle-Aquitaine</a></li>
        </ul>
      </div>

      <!-- Villes populaires -->
      <div class="footer__col">
        <p class="footer__col-title">Villes populaires</p>
        <ul>
          <li><a href="/ville/paris">Clubs Paris</a></li>
          <li><a href="/ville/lyon">Clubs Lyon</a></li>
          <li><a href="/ville/marseille">Clubs Marseille</a></li>
          <li><a href="/ville/bordeaux">Clubs Bordeaux</a></li>
          <li><a href="/ville/toulouse">Clubs Toulouse</a></li>
          <li><a href="/ville/nice">Clubs Nice</a></li>
        </ul>
      </div>

      <!-- Infos -->
      <div class="footer__col">
        <p class="footer__col-title">Informations</p>
        <ul>
          <li><a href="/guide">Guide du libertin</a></li>
          <li><a href="/quiz">Quiz : quelle app pour toi ?</a></li>
          <li><a href="/ajouter-club">Ajouter un club</a></li>
          <li><a href="/mentions-legales">Mentions légales</a></li>
          <li><a href="/confidentialite">Confidentialité</a></li>
          <li><a href="/contact">Contact</a></li>
        </ul>
      </div>
    </div>

    <div class="footer__bottom">
      <p class="footer__legal">
        © <?= date('Y') ?> ClubÉchangiste.fr — Tous droits réservés.
        Ce site est réservé aux adultes consentants (+18 ans).
        <a href="/mentions-legales">Mentions légales</a> · <a href="/confidentialite">Confidentialité</a>
      </p>
      <p class="footer__legal" style="text-align:right">
        Ce site contient des liens d'affiliation marqués <em>sponsored</em>. 
        Certains clubs sont présentés à titre informatif.
      </p>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script>
// Lucide icons init
document.addEventListener('DOMContentLoaded', function() {
  if (window.lucide) lucide.createIcons();
});

// Nav scroll effect
const nav = document.getElementById('main-nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 60);
}, { passive: true });

// Mobile nav toggle
const toggle = document.getElementById('nav-toggle');
const links  = document.getElementById('nav-links');
if (toggle && links) {
  toggle.addEventListener('click', () => links.classList.toggle('open'));
}

// Scroll reveal
const revealEls = document.querySelectorAll('.reveal-on-scroll');
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('revealed');
      io.unobserve(e.target);
    }
  });
}, { threshold: 0.1 });
revealEls.forEach(el => io.observe(el));

// City search filter
const searchInput = document.getElementById('city-search');
if (searchInput) {
  searchInput.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('[data-searchable]').forEach(el => {
      const text = el.dataset.searchable.toLowerCase();
      el.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
  });
}
</script>
</body>
</html>