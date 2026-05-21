<?php
// ── Header partial ──────────────────────────────────────────
// Variables attendues : $page_title, $meta_desc, $canonical, $og_image (optionnel)
$site_name    = 'Club Échangiste France';
$site_url     = 'http://club-echangiste.fr';
$og_image_def = 'https://images.pexels.com/photos/4818319/pexels-photo-4818319.jpeg?auto=compress&cs=tinysrgb&h=627&w=1200';
$og_img       = isset($og_image) ? $og_image : $og_image_def;
$canonical    = isset($canonical) ? $canonical : $site_url . $_SERVER['REQUEST_URI'];
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? $site_name) ?></title>
  <meta name="description" content="<?= htmlspecialchars($meta_desc ?? 'Annuaire complet des clubs échangistes de France, classés par région et département. Trouvez le club libertin près de chez vous.') ?>">
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:title"       content="<?= htmlspecialchars($page_title ?? $site_name) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($meta_desc ?? 'Annuaire clubs échangistes France') ?>">
  <meta property="og:image"       content="<?= htmlspecialchars($og_img) ?>">
  <meta property="og:url"         content="<?= htmlspecialchars($canonical) ?>">
  <meta property="og:site_name"   content="<?= htmlspecialchars($site_name) ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= htmlspecialchars($page_title ?? $site_name) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($meta_desc ?? 'Annuaire clubs échangistes France') ?>">
  <meta name="twitter:image"       content="<?= htmlspecialchars($og_img) ?>">

  <!-- Favicon SVG -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>♦</text></svg>">

  <!-- Styles -->
  <link rel="stylesheet" href="/styles.css">

  <!-- Schema.org -->
  <?php if (isset($schema_json)): ?>
  <script type="application/ld+json"><?= $schema_json ?></script>
  <?php endif; ?>

  <!-- Lucide icons -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
</head>
<body>

<!-- Age disclaimer -->
<div class="age-banner">
  <strong>⚠ Contenu réservé aux adultes (+18 ans)</strong> — En accédant à ce site, vous confirmez avoir l'âge légal requis dans votre pays de résidence.
</div>

<!-- Navigation -->
<nav class="nav" id="main-nav">
  <div class="container">
    <div class="nav__inner">
      <a href="/" class="nav__logo">
        <span class="nav__logo-dot"></span>
        Club<em style="color:var(--primary)">Échangiste</em>.fr
      </a>
      <div class="nav__links" id="nav-links">
        <a href="/">Accueil</a>
        <a href="/regions">Régions</a>
        <a href="/quiz">Quiz</a>
        <a href="/guide">Guide</a>
        <a href="/ajouter-club">Ajouter un club</a>
      </div>
      <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--primary nav__cta">
        Rencontres libertines
      </a>
      <button class="nav__toggle" id="nav-toggle" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>