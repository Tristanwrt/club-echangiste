<?php
$slug = htmlspecialchars(trim($_GET['slug'] ?? ''));
if (!$slug) { header('Location: /regions'); exit; }

$regions_raw = json_decode(file_get_contents(__DIR__ . '/data/regions_france.json'), true) ?? [];
$depts_raw   = json_decode(file_get_contents(__DIR__ . '/data/departments_france.json'), true) ?? [];
$clubs_raw   = json_decode(file_get_contents(__DIR__ . '/data/clubs.json'), true) ?? [];
$cities_raw  = json_decode(file_get_contents(__DIR__ . '/data/cities_france.json'), true) ?? [];

// Find city (by slug)
$city = null;
foreach ($cities_raw as $c) {
  if ($c['slug'] === $slug) { $city = $c; break; }
}

// If not found by exact slug, try to match by name-based slug
if (!$city) {
  // Try a fuzzy approach: maybe the slug is for a club's city_slug
  $clubs_for_slug = array_filter($clubs_raw, fn($c) => $c['city_slug'] === $slug);
  if (!empty($clubs_for_slug)) {
    $first_club = reset($clubs_for_slug);
    $city_name = $first_club['city'];
    foreach ($cities_raw as $c) {
      if (strtolower($c['name']) === strtolower($city_name)) {
        $city = $c;
        break;
      }
    }
    if (!$city) {
      // Synthetic city from club data
      $city = [
        'id' => 0,
        'name' => $city_name,
        'slug' => $slug,
        'department_id' => $first_club['department_id'],
        'region_id' => null,
        'zip' => ''
      ];
    }
  }
}

if (!$city) { header('HTTP/1.0 404 Not Found'); require '404.php'; exit; }

// Parent department
$dept = null;
foreach ($depts_raw as $d) {
  if ($d['id'] == $city['department_id']) { $dept = $d; break; }
}

// Parent region
$region = null;
if ($dept) {
  foreach ($regions_raw as $r) {
    if ($r['id'] == $dept['region_id']) { $region = $r; break; }
  }
}

// Clubs in this city
$clubs = array_values(array_filter($clubs_raw, fn($c) => $c['city_slug'] === $slug));

// Nearby cities (same dept, different, limit 12)
$nearby_cities = [];
$seen_names = [$city['name']];
foreach ($cities_raw as $nc) {
  if ($nc['department_id'] == ($city['department_id'] ?? 0) && !in_array($nc['name'], $seen_names)) {
    $nearby_cities[] = $nc;
    $seen_names[] = $nc['name'];
    if (count($nearby_cities) >= 12) break;
  }
}

$city_name   = $city['name'];
$page_title  = 'Clubs échangistes à ' . $city_name . ' — Annuaire libertin';
$meta_desc   = 'Trouvez les clubs échangistes à ' . $city_name . '. Adresses, horaires, tarifs et avis. Annuaire libertin local discret.';
$canonical   = 'http://club-echangiste.fr/ville/' . $slug;

$schema_json = json_encode([
  "@context"    => "https://schema.org",
  "@type"       => "BreadcrumbList",
  "itemListElement" => array_filter([
    ["@type"=>"ListItem","position"=>1,"name"=>"Accueil","item"=>"http://club-echangiste.fr/"],
    $region ? ["@type"=>"ListItem","position"=>2,"name"=>$region['name'],"item"=>"http://club-echangiste.fr/region/".$region['slug']] : null,
    $dept   ? ["@type"=>"ListItem","position"=>3,"name"=>$dept['name'],"item"=>"http://club-echangiste.fr/departement/".$dept['slug']] : null,
    ["@type"=>"ListItem","position"=>4,"name"=>$city_name,"item"=>$canonical],
  ])
]);

require 'header.php';
?>

<div class="page-hero">
  <div class="container">
    <nav class="breadcrumb">
      <a href="/">Accueil</a>
      <span class="breadcrumb__sep">/</span>
      <?php if ($region): ?>
      <a href="/region/<?= htmlspecialchars($region['slug']) ?>"><?= htmlspecialchars($region['name']) ?></a>
      <span class="breadcrumb__sep">/</span>
      <?php endif; ?>
      <?php if ($dept): ?>
      <a href="/departement/<?= htmlspecialchars($dept['slug']) ?>"><?= htmlspecialchars($dept['name']) ?></a>
      <span class="breadcrumb__sep">/</span>
      <?php endif; ?>
      <span><?= htmlspecialchars($city_name) ?></span>
    </nav>
    <p class="label page-hero__label">Clubs libertins locaux</p>
    <h1 class="page-hero__title">Clubs échangistes à <?= htmlspecialchars($city_name) ?></h1>
    <p class="page-hero__desc">
      <?= count($clubs) > 0 
        ? count($clubs) . ' club' . (count($clubs)>1?'s':'') . ' référencé' . (count($clubs)>1?'s':'') . ' à ' . htmlspecialchars($city_name) 
        : 'Découvrez les clubs libertins à ' . htmlspecialchars($city_name) . ' et à proximité' ?>
    </p>
  </div>
</div>

<section>
  <div class="container">
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:3rem;align-items:start">

      <!-- MAIN CONTENT -->
      <div>

        <?php if (!empty($clubs)): ?>
          <?php foreach ($clubs as $club): ?>
          <!-- CLUB FICHE -->
          <article style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:2rem" class="reveal-on-scroll">
            <img 
              src="<?= htmlspecialchars($club['image']) ?>"
              alt="Club échangiste <?= htmlspecialchars($club['name']) ?> à <?= htmlspecialchars($city_name) ?>"
              width="940" height="350"
              loading="lazy"
              style="width:100%;height:280px;object-fit:cover;filter:brightness(.8) saturate(.7)"
            >
            <div style="padding:2rem">
              <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:.75rem">
                <div>
                  <p class="label" style="margin-bottom:.4rem">Club échangiste · <?= htmlspecialchars($city_name) ?></p>
                  <h2 style="font-size:1.6rem"><?= htmlspecialchars($club['name']) ?></h2>
                </div>
                <div style="text-align:right">
                  <div style="font-size:1.5rem;font-weight:700;color:var(--gold)">★ <?= htmlspecialchars($club['rating']) ?></div>
                  <div style="font-size:.75rem;color:var(--text-dim)">Note membres</div>
                </div>
              </div>

              <p style="color:var(--text-muted);line-height:1.7;margin-bottom:1.5rem"><?= htmlspecialchars($club['description']) ?></p>

              <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem">
                <div style="background:var(--bg-card2);border-radius:var(--radius-sm);padding:.9rem">
                  <div style="font-size:.7rem;color:var(--text-dim);letter-spacing:.1em;text-transform:uppercase;margin-bottom:.4rem">Horaires</div>
                  <div style="font-size:.88rem;color:var(--text-white)"><?= htmlspecialchars($club['horaires']) ?></div>
                </div>
                <div style="background:var(--bg-card2);border-radius:var(--radius-sm);padding:.9rem">
                  <div style="font-size:.7rem;color:var(--text-dim);letter-spacing:.1em;text-transform:uppercase;margin-bottom:.4rem">Tarifs</div>
                  <div style="font-size:.88rem;color:var(--text-white)"><?= htmlspecialchars($club['tarif']) ?></div>
                </div>
                <div style="background:var(--bg-card2);border-radius:var(--radius-sm);padding:.9rem">
                  <div style="font-size:.7rem;color:var(--text-dim);letter-spacing:.1em;text-transform:uppercase;margin-bottom:.4rem">Lieu</div>
                  <div style="font-size:.88rem;color:var(--text-white)"><?= htmlspecialchars($club['address']) ?></div>
                </div>
              </div>

              <!-- Types / tags -->
              <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1.5rem">
                <?php foreach ($club['type'] as $type): ?>
                <span style="background:rgba(192,57,43,.1);border:1px solid rgba(192,57,43,.2);color:var(--primary-light);padding:.25rem .75rem;border-radius:50px;font-size:.73rem;font-weight:600">
                  <?= htmlspecialchars($type) ?>
                </span>
                <?php endforeach; ?>
              </div>

              <div class="info-box">
                <p>
                  <strong style="color:var(--text-white)">📍 Infos pratiques :</strong> 
                  Réservation conseillée le week-end. Tenue correcte ou sexy exigée. 
                  Minimum 18 ans, pièce d'identité requise à l'entrée.
                  Pour contacter ce club, cherchez leur site officiel ou leurs réseaux.
                </p>
              </div>
            </div>
          </article>
          <?php endforeach; ?>

        <?php else: ?>
          <!-- No club found, generic page -->
          <div class="info-box reveal-on-scroll" style="margin-bottom:2rem">
            <p>Aucun club échangiste n'est encore référencé directement à <?= htmlspecialchars($city_name) ?>. 
            Consultez les clubs dans les villes voisines ou explorez les rencontres libertines en ligne.</p>
          </div>
          <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-md);padding:2rem;margin-bottom:2rem" class="reveal-on-scroll">
            <h2 style="margin-bottom:1rem">Clubs libertins à proximité de <?= htmlspecialchars($city_name) ?></h2>
            <p style="margin-bottom:1.5rem">Voici les clubs référencés dans le département <?= $dept ? htmlspecialchars($dept['name']) : '' ?>, à distance raisonnable.</p>
            <?php
            $nearby_clubs = array_values(array_filter($clubs_raw, fn($c) => $c['department_id'] == ($city['department_id'] ?? 0)));
            foreach (array_slice($nearby_clubs, 0, 3) as $nc): ?>
            <a href="/ville/<?= htmlspecialchars($nc['city_slug']) ?>" style="display:flex;align-items:center;gap:1rem;padding:.85rem;background:var(--bg-card2);border-radius:var(--radius-sm);margin-bottom:.5rem;transition:all .2s" onmouseover="this.style.borderLeft='3px solid var(--primary)'" onmouseout="this.style.borderLeft='none'">
              <i data-lucide="map-pin" width="16" height="16" color="#C0392B" style="flex-shrink:0"></i>
              <div>
                <div style="font-weight:600;font-size:.9rem"><?= htmlspecialchars($nc['name']) ?></div>
                <div style="font-size:.78rem;color:var(--text-dim)"><?= htmlspecialchars($nc['city']) ?> · <?= htmlspecialchars($nc['tarif']) ?></div>
              </div>
              <i data-lucide="arrow-right" width="14" height="14" color="var(--text-dim)" style="margin-left:auto"></i>
            </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Guide libertin local -->
        <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-md);padding:2rem" class="reveal-on-scroll">
          <h2 style="margin-bottom:1rem">Vie libertine à <?= htmlspecialchars($city_name) ?></h2>
          <p style="margin-bottom:1rem">
            <?= htmlspecialchars($city_name) ?> fait partie des villes françaises où la communauté libertine est active. 
            Que vous soyez un couple en quête de nouvelles expériences ou un célibataire curieux, 
            la vie nocturne libertine offre de nombreuses possibilités dans cette région.
          </p>
          <p style="margin-bottom:1rem">
            Les clubs échangistes de <?= $dept ? htmlspecialchars($dept['name']) : 'cette zone' ?> accueillent généralement 
            des couples et parfois des célibataires selon les soirs thématiques. Le dress code est toujours de mise : 
            élégant, sexy ou fetish selon les établissements.
          </p>
          <p>
            Si vous préférez une approche plus discrète, les plateformes de rencontres libertines en ligne 
            permettent de rencontrer des partenaires dans votre secteur sans vous déplacer.
          </p>
        </div>

      </div>

      <!-- SIDEBAR -->
      <div>

        <!-- DEROGE BOX SIDEBAR -->
        <div class="deroge-box reveal-on-scroll" style="margin-bottom:1.5rem;padding:1.75rem">
          <div class="deroge-box__crown" style="font-size:2rem">🔥</div>
          <p class="deroge-box__label">Recommandé</p>
          <h3 class="deroge-box__title" style="font-size:1.2rem">
            Libertins à <span><?= htmlspecialchars($city_name) ?></span>
          </h3>
          <p class="deroge-box__desc" style="font-size:.82rem">
            Sans sortir, rencontrez des libertins près de chez vous. 
            Le site #1 de rencontres en France.
          </p>
          <div class="deroge-box__cta" style="margin-bottom:.75rem">
            <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" 
               target="_blank" rel="noopener noreferrer sponsored" 
               class="btn btn--gold" style="width:100%;justify-content:center;font-size:.85rem">
              Voir les profils →
            </a>
          </div>
          <p class="deroge-box__fine">Lien affilié · +18 ans</p>
        </div>

        <!-- Nearby cities -->
        <?php if (!empty($nearby_cities)): ?>
        <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-md);padding:1.5rem" class="reveal-on-scroll">
          <h3 style="margin-bottom:1rem;font-size:.95rem">
            <i data-lucide="map" width="16" height="16" color="#C0392B" style="vertical-align:middle;margin-right:.4rem"></i>
            Villes proches
          </h3>
          <div style="display:flex;flex-direction:column;gap:.35rem">
            <?php foreach ($nearby_cities as $nc): ?>
            <a href="/ville/<?= htmlspecialchars($nc['slug']) ?>" class="city-tag">
              <?= htmlspecialchars($nc['name']) ?>
            </a>
            <?php endforeach; ?>
          </div>
          <?php if ($dept): ?>
          <a href="/departement/<?= htmlspecialchars($dept['slug']) ?>" class="btn btn--outline" style="width:100%;justify-content:center;margin-top:1rem;font-size:.8rem">
            Tout le <?= htmlspecialchars($dept['name']) ?>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Region link -->
        <?php if ($region): ?>
        <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-md);padding:1.5rem;margin-top:1rem" class="reveal-on-scroll">
          <h3 style="margin-bottom:.75rem;font-size:.9rem">
            <i data-lucide="layers" width="16" height="16" color="#C0392B" style="vertical-align:middle;margin-right:.4rem"></i>
            Région
          </h3>
          <a href="/region/<?= htmlspecialchars($region['slug']) ?>" class="btn btn--outline" style="width:100%;justify-content:center;font-size:.82rem">
            Clubs en <?= htmlspecialchars($region['name']) ?> →
          </a>
        </div>
        <?php endif; ?>

      </div>
    </div>
  </div>
</section>

<?php require 'footer.php'; ?>