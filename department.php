<?php
$slug = htmlspecialchars(trim($_GET['slug'] ?? ''));
if (!$slug) { header('Location: /regions'); exit; }

$regions_raw = json_decode(file_get_contents(__DIR__ . '/data/regions_france.json'), true) ?? [];
$depts_raw   = json_decode(file_get_contents(__DIR__ . '/data/departments_france.json'), true) ?? [];
$clubs_raw   = json_decode(file_get_contents(__DIR__ . '/data/clubs.json'), true) ?? [];
$cities_raw  = json_decode(file_get_contents(__DIR__ . '/data/cities_france.json'), true) ?? [];

// Find department
$dept = null;
foreach ($depts_raw as $d) {
  if ($d['slug'] === $slug) { $dept = $d; break; }
}
if (!$dept) { header('HTTP/1.0 404 Not Found'); require '404.php'; exit; }

// Parent region
$region = null;
foreach ($regions_raw as $r) {
  if ($r['id'] == $dept['region_id']) { $region = $r; break; }
}

// Clubs
$clubs = array_values(array_filter($clubs_raw, fn($c) => $c['department_id'] == $dept['id']));

// Cities (limit 60 for perf)
$cities = [];
$seen = [];
foreach ($cities_raw as $c) {
  if ($c['department_id'] == $dept['id'] && !in_array($c['name'], $seen)) {
    $cities[] = $c;
    $seen[] = $c['name'];
    if (count($cities) >= 60) break;
  }
}

// Nearby departments (same region, different)
$nearby_depts = array_values(array_filter($depts_raw, fn($d) => $d['region_id'] == $dept['region_id'] && $d['id'] != $dept['id']));

$page_title = 'Clubs échangistes en ' . $dept['name'] . ' (' . $dept['code'] . ') — Annuaire libertin';
$meta_desc  = 'Tous les clubs échangistes du ' . $dept['name'] . ' (' . $dept['code'] . '). Annuaire libertin complet avec adresses, horaires et tarifs.';
$canonical  = 'http://club-echangiste.fr/departement/' . $slug;

$schema_json = json_encode([
  "@context"    => "https://schema.org",
  "@type"       => "BreadcrumbList",
  "itemListElement" => array_filter([
    ["@type"=>"ListItem","position"=>1,"name"=>"Accueil","item"=>"http://club-echangiste.fr/"],
    ["@type"=>"ListItem","position"=>2,"name"=>"Régions","item"=>"http://club-echangiste.fr/regions"],
    $region ? ["@type"=>"ListItem","position"=>3,"name"=>$region['name'],"item"=>"http://club-echangiste.fr/region/".$region['slug']] : null,
    ["@type"=>"ListItem","position"=>4,"name"=>$dept['name'],"item"=>$canonical],
  ])
]);

require 'header.php';
?>

<div class="page-hero">
  <div class="container">
    <nav class="breadcrumb">
      <a href="/">Accueil</a>
      <span class="breadcrumb__sep">/</span>
      <a href="/regions">Régions</a>
      <span class="breadcrumb__sep">/</span>
      <?php if ($region): ?>
      <a href="/region/<?= htmlspecialchars($region['slug']) ?>"><?= htmlspecialchars($region['name']) ?></a>
      <span class="breadcrumb__sep">/</span>
      <?php endif; ?>
      <span><?= htmlspecialchars($dept['name']) ?></span>
    </nav>
    <p class="label page-hero__label">Département <?= htmlspecialchars($dept['code']) ?></p>
    <h1 class="page-hero__title">Clubs échangistes en <?= htmlspecialchars($dept['name']) ?></h1>
    <p class="page-hero__desc">
      <?= count($clubs) ?> club<?= count($clubs) > 1 ? 's' : '' ?> référencé<?= count($clubs) > 1 ? 's' : '' ?> · 
      <?= count($cities) ?>+ villes couvertes
    </p>
  </div>
</div>

<section>
  <div class="container">

    <!-- Clubs -->
    <?php if (!empty($clubs)): ?>
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.5rem">Clubs libertins dans le <?= htmlspecialchars($dept['name']) ?></h2>
      <div class="clubs-grid">
        <?php foreach ($clubs as $club): ?>
        <a href="/ville/<?= htmlspecialchars($club['city_slug']) ?>" class="club-card">
          <img src="<?= htmlspecialchars($club['image']) ?>" 
               alt="Club échangiste <?= htmlspecialchars($club['city']) ?>"
               width="940" height="350" loading="lazy" class="club-card__img">
          <div class="club-card__body">
            <div class="club-card__badge">Club libertin · <?= htmlspecialchars($club['city']) ?></div>
            <h3 class="club-card__title"><?= htmlspecialchars($club['name']) ?></h3>
            <div class="club-card__meta">
              <span><i data-lucide="clock" width="13" height="13"></i> <?= htmlspecialchars($club['horaires']) ?></span>
              <span><i data-lucide="euro" width="13" height="13"></i> <?= htmlspecialchars($club['tarif']) ?></span>
            </div>
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1rem"><?= htmlspecialchars(mb_substr($club['description'], 0, 100)) ?>...</p>
            <span class="btn btn--outline" style="font-size:.82rem;padding:.5rem 1rem">Voir la fiche →</span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="info-box reveal-on-scroll">
      <p>Aucun club n'est encore référencé dans ce département. <a href="/ajouter-club" style="color:var(--primary)">Proposez un ajout</a> ou découvrez les rencontres libertines en ligne ci-dessous.</p>
    </div>
    <?php endif; ?>

    <!-- Deroge box -->
    <div class="deroge-box reveal-on-scroll" style="margin-bottom:3rem">
      <div class="deroge-box__crown">💫</div>
      <p class="deroge-box__label">Pas de déplacement requis</p>
      <h2 class="deroge-box__title">Des libertins dans le <span><?= htmlspecialchars($dept['name']) ?></span></h2>
      <p class="deroge-box__desc">
        Des centaines de profils vérifiés dans votre département vous attendent en ligne. 
        Rencontres discrètes, échangisme, plans cul... tout est possible.
      </p>
      <div class="deroge-box__cta">
        <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold btn--lg">
          🔥 Voir les libertins du <?= htmlspecialchars($dept['code']) ?>
        </a>
      </div>
      <p class="deroge-box__fine">Lien affilié · +18 ans uniquement</p>
    </div>

    <!-- Cities -->
    <?php if (!empty($cities)): ?>
    <div class="reveal-on-scroll">
      <h2 style="margin-bottom:1.5rem">
        <i data-lucide="list" width="20" height="20" color="#C0392B" style="vertical-align:middle;margin-right:.5rem"></i>
        Toutes les villes du <?= htmlspecialchars($dept['name']) ?>
      </h2>
      <div class="search-wrap" style="margin:0 0 1.5rem;max-width:100%">
        <i class="search-wrap__icon" data-lucide="search" width="18" height="18"></i>
        <input type="text" id="city-search" placeholder="Rechercher une ville..." autocomplete="off">
      </div>
      <div class="cities-grid">
        <?php foreach ($cities as $city): ?>
        <a href="/ville/<?= htmlspecialchars($city['slug']) ?>" class="city-tag" data-searchable="<?= htmlspecialchars($city['name']) ?>">
          <?= htmlspecialchars($city['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Nearby departments -->
    <?php if (!empty($nearby_depts)): ?>
    <div class="reveal-on-scroll" style="margin-top:3rem">
      <h2 style="margin-bottom:1.25rem">Départements voisins</h2>
      <div class="dept-grid">
        <?php foreach (array_slice($nearby_depts, 0, 8) as $nd): ?>
        <a href="/departement/<?= htmlspecialchars($nd['slug']) ?>" class="dept-card">
          <span class="dept-card__code"><?= htmlspecialchars($nd['code']) ?></span>
          <span class="dept-card__name"><?= htmlspecialchars($nd['name']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

<?php require 'footer.php'; ?>