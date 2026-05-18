<?php
$page_title = 'Club Échangiste France — Annuaire des clubs libertins par région';
$meta_desc  = 'Trouvez le club échangiste près de chez vous. Annuaire complet des clubs libertins de France classés par région, département et ville. 100% discret.';
$canonical  = 'http://club-echangiste.fr/';
$og_image   = 'https://images.pexels.com/photos/4818319/pexels-photo-4818319.jpeg?auto=compress&cs=tinysrgb&h=627&w=1200';

$schema_json = json_encode([
  "@context"  => "https://schema.org",
  "@type"     => "WebSite",
  "name"      => "Club Échangiste France",
  "url"       => "http://club-echangiste.fr",
  "description" => "Annuaire des clubs échangistes et libertins de France",
  "potentialAction" => [
    "@type"       => "SearchAction",
    "target"      => "http://club-echangiste.fr/recherche?q={search_term_string}",
    "query-input" => "required name=search_term_string"
  ]
]);

// Load regions
$regions_raw = json_decode(file_get_contents(__DIR__ . '/data/regions_france.json'), true) ?? [];
// Keep only mainland + DOM (exclude ids we don't need) — show all 18
$regions = $regions_raw;

// Load departments count per region
$depts_raw = json_decode(file_get_contents(__DIR__ . '/data/departments_france.json'), true) ?? [];
$depts_by_region = [];
foreach ($depts_raw as $d) {
  $depts_by_region[$d['region_id']] = ($depts_by_region[$d['region_id']] ?? 0) + 1;
}

require 'header.php';
?>

<!-- ── HERO ─────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero__bg"></div>
  <div class="container">
    <div class="hero__content">
      <div class="hero__badge">
        <span>♦</span> Annuaire #1 des clubs libertins
      </div>
      <h1 class="hero__title">
        Tous les clubs <em>échangistes</em><br>de France en un clic
      </h1>
      <p class="hero__desc">
        L'annuaire de référence pour trouver le club libertin le plus proche. 
        Classés par région, département et ville. Mis à jour régulièrement.
      </p>
      <div class="hero__actions">
        <a href="/regions" class="btn btn--primary btn--lg">
          <i data-lucide="map-pin" width="18" height="18"></i>
          Chercher par région
        </a>
        <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold btn--lg">
          🔥 Rencontres en ligne
        </a>
      </div>
    </div>
    <div class="hero__stats">
      <div>
        <div class="hero__stat-num">+<span>300</span></div>
        <div class="hero__stat-label">Clubs référencés</div>
      </div>
      <div>
        <div class="hero__stat-num"><span>13</span></div>
        <div class="hero__stat-label">Régions couvertes</div>
      </div>
      <div>
        <div class="hero__stat-num"><span>96</span></div>
        <div class="hero__stat-label">Départements</div>
      </div>
      <div>
        <div class="hero__stat-num"><span>100<span style="font-size:1rem">%</span></span></div>
        <div class="hero__stat-label">Discret &amp; gratuit</div>
      </div>
    </div>
  </div>
</section>

<!-- ── AFFILIATION BANNER ────────────────────────────────────── -->
<div class="container">
  <div class="affiliation-banner reveal-on-scroll">
    <div class="affiliation-banner__icon">🔥</div>
    <div class="affiliation-banner__content">
      <h2 class="affiliation-banner__title">
        Pas envie de vous déplacer&nbsp;?<br>
        <span>Rencontrez des libertins en ligne dès ce soir</span>
      </h2>
      <p class="affiliation-banner__desc">
        Des milliers de couples et célibataires libertins vous attendent sur le meilleur site de rencontres du moment. 
        Inscription gratuite, profils vérifiés, 100% discret.
      </p>
    </div>
    <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold btn--lg" style="flex-shrink:0">
      Accéder maintenant →
    </a>
  </div>
</div>

<!-- ── REGIONS ───────────────────────────────────────────────── -->
<section>
  <div class="container">
    <div class="section-header reveal-on-scroll">
      <p class="label">Annuaire géographique</p>
      <h2>Clubs échangistes par région</h2>
      <p>Sélectionnez votre région pour découvrir tous les clubs libertins à proximité, classés par département et ville.</p>
    </div>

    <div class="search-wrap reveal-on-scroll">
      <i class="search-wrap__icon" data-lucide="search" width="18" height="18"></i>
      <input type="text" id="city-search" placeholder="Rechercher une région, une ville..." autocomplete="off">
    </div>

    <div class="regions-grid" style="margin-top:2rem">
      <?php foreach ($regions as $r): 
        $nb_depts = $depts_by_region[$r['id']] ?? 0;
      ?>
      <a href="/region/<?= htmlspecialchars($r['slug']) ?>" 
         class="region-card reveal-on-scroll"
         data-searchable="<?= htmlspecialchars($r['name']) ?>">
        <div>
          <div class="region-card__name"><?= htmlspecialchars($r['name']) ?></div>
          <div class="region-card__count"><?= $nb_depts ?> département<?= $nb_depts > 1 ? 's' : '' ?></div>
        </div>
        <i class="region-card__arrow" data-lucide="arrow-right" width="16" height="16"></i>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="container"><div class="divider"></div></div>

<!-- ── POURQUOI NOUS ─────────────────────────────────────────── -->
<section class="reveal-on-scroll">
  <div class="container">
    <div class="section-header">
      <p class="label">Notre mission</p>
      <h2>L'annuaire libertin le plus complet de France</h2>
      <p>Nous référençons et vérifions chaque club échangiste pour vous offrir une expérience de recherche fiable et discrète.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card reveal-on-scroll">
        <div class="feature-card__icon">
          <i data-lucide="map-pin" width="24" height="24" color="#C0392B"></i>
        </div>
        <h3 class="feature-card__title">Géolocalisation précise</h3>
        <p class="feature-card__desc">Trouvez les clubs à proximité par région, département ou ville. Navigation intuitive et rapide.</p>
      </div>
      <div class="feature-card reveal-on-scroll">
        <div class="feature-card__icon">
          <i data-lucide="shield-check" width="24" height="24" color="#C0392B"></i>
        </div>
        <h3 class="feature-card__title">Informations vérifiées</h3>
        <p class="feature-card__desc">Chaque fiche club est vérifiée et mise à jour régulièrement. Horaires, tarifs, ambiance — tout y est.</p>
      </div>
      <div class="feature-card reveal-on-scroll">
        <div class="feature-card__icon">
          <i data-lucide="eye-off" width="24" height="24" color="#C0392B"></i>
        </div>
        <h3 class="feature-card__title">100% discret</h3>
        <p class="feature-card__desc">Aucune inscription requise. Votre vie privée est respectée. Aucune donnée personnelle collectée.</p>
      </div>
      <div class="feature-card reveal-on-scroll">
        <div class="feature-card__icon">
          <i data-lucide="zap" width="24" height="24" color="#C0392B"></i>
        </div>
        <h3 class="feature-card__title">Alternatives en ligne</h3>
        <p class="feature-card__desc">Pas de club dans votre ville ? Découvrez les meilleures plateformes de rencontres libertines en ligne.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── DEROGE BOX ─────────────────────────────────────────────── -->
<section>
  <div class="container--sm">
    <div class="deroge-box reveal-on-scroll">
      <div class="deroge-box__crown">👑</div>
      <p class="deroge-box__label">Recommandation partenaire</p>
      <h2 class="deroge-box__title">
        Le meilleur site de rencontres libertines<br>
        <span>en ce moment</span>
      </h2>
      <p class="deroge-box__desc">
        Des milliers de libertins actifs dans votre région. Interface moderne, profils vérifiés, 
        messagerie privée et bien plus. L'alternative parfaite si vous préférez rester chez vous.
      </p>
      <div class="deroge-box__cta">
        <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" 
           target="_blank" rel="noopener noreferrer sponsored" 
           class="btn btn--gold btn--lg">
          🔥 Essayer gratuitement — Inscription en 2 minutes
        </a>
      </div>
      <p class="deroge-box__fine">Lien affilié — Site réservé aux adultes (+18 ans)</p>
    </div>
  </div>
</section>

<!-- ── GUIDE RAPIDE ──────────────────────────────────────────── -->
<section class="reveal-on-scroll">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center">
      <div>
        <p class="label" style="margin-bottom:.75rem">Guide du débutant</p>
        <h2 style="margin-bottom:1.25rem">Votre première visite en club échangiste</h2>
        <p style="margin-bottom:1.5rem">Vous êtes curieux mais ne savez pas par où commencer ? Voici les bases pour une première expérience réussie en club libertin.</p>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:.85rem">
          <?php 
          $tips = [
            ['icon'=>'check-circle','text'=>'Venez en couple ou solo selon les clubs — renseignez-vous avant'],
            ['icon'=>'check-circle','text'=>'Tenue correcte exigée : élégant ou sexy, jamais négligé'],
            ['icon'=>'check-circle','text'=>'Le respect et le consentement sont les règles d\'or absolues'],
            ['icon'=>'check-circle','text'=>'Réservation souvent conseillée, surtout le week-end'],
            ['icon'=>'check-circle','text'=>'Les tarifs varient : de 20€ à 80€ par couple selon le club'],
          ];
          foreach ($tips as $tip): ?>
          <li style="display:flex;gap:.75rem;align-items:flex-start">
            <i data-lucide="<?= $tip['icon'] ?>" width="18" height="18" color="#C0392B" style="flex-shrink:0;margin-top:3px"></i>
            <span style="color:var(--text-muted);font-size:.9rem"><?= htmlspecialchars($tip['text']) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <a href="/guide" class="btn btn--outline" style="margin-top:1.75rem">Lire le guide complet</a>
      </div>
      <div>
        <img 
          src="https://images.pexels.com/photos/19343364/pexels-photo-19343364.jpeg?auto=compress&cs=tinysrgb&h=650&w=940"
          alt="Ambiance feutrée d'un club libertin élégant"
          width="940" height="650"
          loading="lazy"
          style="border-radius:var(--radius-lg);object-fit:cover;height:400px;width:100%;filter:brightness(.7) saturate(.7)"
        >
      </div>
    </div>
  </div>
</section>

<?php require 'footer.php'; ?>