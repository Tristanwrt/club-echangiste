<?php
http_response_code(404);
$page_title = 'Page introuvable — ClubÉchangiste.fr';
$meta_desc  = 'Cette page n\'existe pas.';
require 'header.php';
?>

<section style="min-height:70vh;display:flex;align-items:center;text-align:center">
  <div class="container">
    <div style="font-size:5rem;margin-bottom:1rem">404</div>
    <h1 style="font-family:var(--font-serif);font-size:2rem;margin-bottom:1rem">Page introuvable</h1>
    <p style="color:var(--text-muted);margin-bottom:2rem">Cette adresse n'existe pas dans notre annuaire.</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
      <a href="/" class="btn btn--primary">Retour à l'accueil</a>
      <a href="/regions" class="btn btn--outline">Voir les régions</a>
    </div>
  </div>
</section>

<?php require 'footer.php'; ?>