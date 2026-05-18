<?php
$page_title = 'Clubs échangistes par région — Annuaire libertin France';
$meta_desc  = 'Toutes les régions de France avec leurs clubs échangistes. Île-de-France, PACA, Occitanie, Nouvelle-Aquitaine... Trouvez un club libertin près de chez vous.';
$canonical  = 'http://club-echangiste.fr/regions';

$regions_raw  = json_decode(file_get_contents(__DIR__ . '/data/regions_france.json'), true) ?? [];
$depts_raw    = json_decode(file_get_contents(__DIR__ . '/data/departments_france.json'), true) ?? [];
$clubs_raw    = json_decode(file_get_contents(__DIR__ . '/data/clubs.json'), true) ?? [];

$depts_by_region = [];
foreach ($depts_raw as $d) {
  $depts_by_region[$d['region_id']] = ($depts_by_region[$d['region_id']] ?? 0) + 1;
}
$clubs_by_region = [];
foreach ($clubs_raw as $c) {
  $clubs_by_region[$c['region_id']] = ($clubs_by_region[$c['region_id']] ?? 0) + 1;
}

require 'header.php';
?>

<div class="page-hero">
  <div class="container">
    <nav class="breadcrumb">
      <a href="/">Accueil</a>
      <span class="breadcrumb__sep">/</span>
      <span>Régions</span>
    </nav>
    <p class="label page-hero__label">Annuaire</p>
    <h1 class="page-hero__title">Clubs échangistes par région</h1>
    <p class="page-hero__desc">Sélectionnez votre région pour accéder à l'annuaire complet des clubs libertins classés par département et ville.</p>

    <div class="search-wrap">
      <i class="search-wrap__icon" data-lucide="search" width="18" height="18"></i>
      <input type="text" id="city-search" placeholder="Filtrer les régions..." autocomplete="off">
    </div>
  </div>
</div>

<section>
  <div class="container">
    <div class="regions-grid">
      <?php foreach ($regions_raw as $r): 
        $nb_depts  = $depts_by_region[$r['id']] ?? 0;
        $nb_clubs  = $clubs_by_region[$r['id']] ?? 0;
      ?>
      <a href="/region/<?= htmlspecialchars($r['slug']) ?>" 
         class="region-card reveal-on-scroll"
         data-searchable="<?= htmlspecialchars($r['name']) ?>">
        <div>
          <div class="region-card__name"><?= htmlspecialchars($r['name']) ?></div>
          <div class="region-card__count">
            <?= $nb_depts ?> dép. 
            <?php if ($nb_clubs > 0): ?>
            · <span style="color:var(--primary)"><?= $nb_clubs ?> club<?= $nb_clubs > 1 ? 's' : '' ?></span>
            <?php endif; ?>
          </div>
        </div>
        <i class="region-card__arrow" data-lucide="arrow-right" width="16" height="16"></i>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- AFFILIATION BANNER -->
<div class="container">
  <div class="affiliation-banner reveal-on-scroll">
    <div class="affiliation-banner__icon">💬</div>
    <div class="affiliation-banner__content">
      <h2 class="affiliation-banner__title">Aucun club dans votre région ? <span>Pas de panique !</span></h2>
      <p class="affiliation-banner__desc">Rejoignez la plus grande communauté de libertins en ligne. Trouvez des partenaires dans votre région sans bouger de chez vous.</p>
    </div>
    <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold btn--lg" style="flex-shrink:0">
      Rencontres libertines →
    </a>
  </div>
</div>

<?php require 'footer.php'; ?>