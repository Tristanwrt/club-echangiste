<?php
$slug = htmlspecialchars(trim($_GET['slug'] ?? ''));
if (!$slug) { header('Location: /regions'); exit; }

$regions_raw = json_decode(file_get_contents(__DIR__ . '/data/regions_france.json'), true) ?? [];
$depts_raw   = json_decode(file_get_contents(__DIR__ . '/data/departments_france.json'), true) ?? [];
$clubs_raw   = json_decode(file_get_contents(__DIR__ . '/data/clubs.json'), true) ?? [];
$cities_raw  = json_decode(file_get_contents(__DIR__ . '/data/cities_france.json'), true) ?? [];

// Find region
$region = null;
foreach ($regions_raw as $r) {
  if ($r['slug'] === $slug) { $region = $r; break; }
}
if (!$region) { header('HTTP/1.0 404 Not Found'); require '404.php'; exit; }

// Departments in this region
$depts = array_values(array_filter($depts_raw, fn($d) => $d['region_id'] == $region['id']));

// Clubs in this region
$clubs = array_values(array_filter($clubs_raw, fn($c) => $c['region_id'] == $region['id']));

// Collect department ids for city lookup
$dept_ids = array_column($depts, 'id');

// Big cities (sample — limit to 40 distinct city names for perf)
$big_cities = [];
$seen = [];
foreach ($cities_raw as $c) {
  if (in_array($c['department_id'], $dept_ids) && !in_array($c['name'], $seen)) {
    $big_cities[] = $c;
    $seen[] = $c['name'];
    if (count($big_cities) >= 40) break;
  }
}

$page_title = 'Clubs échangistes en ' . $region['name'] . ' — Annuaire libertin';
$meta_desc  = 'Trouvez les clubs échangistes en ' . $region['name'] . '. Annuaire complet des clubs libertins par département et ville.';
$canonical  = 'http://club-echangiste.fr/region/' . $slug;

$schema_json = json_encode([
  "@context"    => "https://schema.org",
  "@type"       => "BreadcrumbList",
  "itemListElement" => [
    ["@type"=>"ListItem","position"=>1,"name"=>"Accueil","item"=>"http://club-echangiste.fr/"],
    ["@type"=>"ListItem","position"=>2,"name"=>"Régions","item"=>"http://club-echangiste.fr/regions"],
    ["@type"=>"ListItem","position"=>3,"name"=>$region['name'],"item"=>$canonical],
  ]
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
      <span><?= htmlspecialchars($region['name']) ?></span>
    </nav>
    <p class="label page-hero__label">Région</p>
    <h1 class="page-hero__title">Clubs échangistes en <?= htmlspecialchars($region['name']) ?></h1>
    <p class="page-hero__desc">
      <?= count($depts) ?> département<?= count($depts) > 1 ? 's' : '' ?> — 
      <?= count($clubs) ?> club<?= count($clubs) > 1 ? 's' : '' ?> référencé<?= count($clubs) > 1 ? 's' : '' ?>
    </p>
  </div>
</div>

<section>
  <div class="container">

    <!-- Clubs dans la région -->
    <?php if (!empty($clubs)): ?>
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.5rem">
        <i data-lucide="map-pin" width="22" height="22" color="#C0392B" style="vertical-align:middle;margin-right:.5rem"></i>
        Clubs référencés en <?= htmlspecialchars($region['name']) ?>
      </h2>
      <div class="clubs-grid">
        <?php foreach ($clubs as $club): ?>
        <a href="/ville/<?= htmlspecialchars($club['city_slug']) ?>" class="club-card">
          <img 
            src="<?= htmlspecialchars($club['image']) ?>" 
            alt="Club échangiste <?= htmlspecialchars($club['name']) ?> - <?= htmlspecialchars($club['city']) ?>"
            width="940" height="350" loading="lazy"
            class="club-card__img"
          >
          <div class="club-card__body">
            <div class="club-card__badge">Club libertin · <?= htmlspecialchars($club['city']) ?></div>
            <h3 class="club-card__title"><?= htmlspecialchars($club['name']) ?></h3>
            <div class="club-card__meta">
              <span><i data-lucide="clock" width="13" height="13"></i> <?= htmlspecialchars($club['horaires']) ?></span>
              <span><i data-lucide="euro" width="13" height="13"></i> <?= htmlspecialchars($club['tarif']) ?></span>
            </div>
            <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1rem"><?= htmlspecialchars(mb_substr($club['description'], 0, 100)) ?>...</p>
            <div class="btn btn--outline" style="font-size:.82rem;padding:.5rem 1rem">Voir la fiche →</div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Dérogation -->
    <div class="deroge-box reveal-on-scroll" style="margin-bottom:3rem">
      <div class="deroge-box__crown">🔥</div>
      <p class="deroge-box__label">Alternative en ligne</p>
      <h2 class="deroge-box__title">Des libertins <span>en <?= htmlspecialchars($region['name']) ?></span> vous attendent</h2>
      <p class="deroge-box__desc">Pas besoin de sortir pour rencontrer des couples et célibataires libertins de votre région. La plateforme #1 de rencontres libertines en France.</p>
      <div class="deroge-box__cta">
        <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold btn--lg">
          Voir les profils de <?= htmlspecialchars($region['name']) ?> →
        </a>
      </div>
      <p class="deroge-box__fine">Lien affilié · Réservé aux adultes +18 ans</p>
    </div>

    <!-- Départements -->
    <div class="reveal-on-scroll">
      <h2 style="margin-bottom:1.5rem">
        <i data-lucide="layers" width="22" height="22" color="#C0392B" style="vertical-align:middle;margin-right:.5rem"></i>
        Départements de <?= htmlspecialchars($region['name']) ?>
      </h2>
      <div class="dept-grid">
        <?php foreach ($depts as $dept): ?>
        <a href="/departement/<?= htmlspecialchars($dept['slug']) ?>" class="dept-card">
          <span class="dept-card__code"><?= htmlspecialchars($dept['code']) ?></span>
          <span class="dept-card__name"><?= htmlspecialchars($dept['name']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Villes de la région -->
    <?php if (!empty($big_cities)): ?>
    <div class="reveal-on-scroll" style="margin-top:3rem">
      <h2 style="margin-bottom:1.5rem">
        <i data-lucide="building-2" width="22" height="22" color="#C0392B" style="vertical-align:middle;margin-right:.5rem"></i>
        Villes avec clubs en <?= htmlspecialchars($region['name']) ?>
      </h2>
      <div class="cities-grid">
        <?php foreach ($big_cities as $city): ?>
        <a href="/ville/<?= htmlspecialchars($city['slug']) ?>" class="city-tag">
          <?= htmlspecialchars($city['name']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

<?php require 'footer.php'; ?>