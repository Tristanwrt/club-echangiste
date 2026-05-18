<?php
$page_title = 'Ajouter un club échangiste — ClubÉchangiste.fr';
$meta_desc  = 'Proposez l\'ajout de votre club échangiste dans notre annuaire libertin. Visibilité gratuite pour les clubs français référencés.';
$canonical  = 'http://club-echangiste.fr/ajouter-club';
require 'header.php';
?>

<div class="page-hero">
  <div class="container">
    <nav class="breadcrumb">
      <a href="/">Accueil</a>
      <span class="breadcrumb__sep">/</span>
      <span>Ajouter un club</span>
    </nav>
    <p class="label page-hero__label">Référencement</p>
    <h1 class="page-hero__title">Référencer votre club</h1>
    <p class="page-hero__desc">Vous gérez un club échangiste ? Rejoignez l'annuaire de référence et gagnez en visibilité auprès de milliers de libertins.</p>
  </div>
</div>

<section>
  <div class="container--sm">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:3rem;align-items:start;margin-bottom:3rem">
      <div class="reveal-on-scroll">
        <h2 style="margin-bottom:1rem">Pourquoi nous rejoindre ?</h2>
        <ul style="list-style:none;display:flex;flex-direction:column;gap:.75rem">
          <?php
          $avantages = [
            ['check','Visibilité auprès de milliers de libertins français'],
            ['check','Fiche complète : horaires, tarifs, photos, description'],
            ['check','Référencement SEO optimisé par ville et région'],
            ['check','Lien vers votre site officiel ou réseaux sociaux'],
            ['check','Mise à jour possible à tout moment'],
          ];
          foreach ($avantages as $a): ?>
          <li style="display:flex;gap:.75rem;align-items:center">
            <i data-lucide="<?= $a[0] ?>-circle" width="18" height="18" color="#C0392B"></i>
            <span style="font-size:.9rem;color:var(--text-muted)"><?= htmlspecialchars($a[1]) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="reveal-on-scroll">
        <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-md);padding:1.5rem">
          <h3 style="margin-bottom:.5rem">Tarifs de référencement</h3>
          <div style="display:flex;flex-direction:column;gap:.5rem">
            <div style="display:flex;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid var(--bg-border)">
              <span style="color:var(--text-muted);font-size:.9rem">Listing basique</span>
              <span style="font-weight:700;color:var(--gold)">Gratuit</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.75rem 0;border-bottom:1px solid var(--bg-border)">
              <span style="color:var(--text-muted);font-size:.9rem">Fiche complète (photos + détails)</span>
              <span style="font-weight:700;color:var(--text-white)">29€/mois</span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:.75rem 0">
              <span style="color:var(--text-muted);font-size:.9rem">Mise en avant + priorité SEO</span>
              <span style="font-weight:700;color:var(--text-white)">59€/mois</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form -->
    <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-lg);padding:2.5rem" class="reveal-on-scroll">
      <h2 style="margin-bottom:1.5rem">Formulaire de proposition</h2>
      <form action="#" method="post" style="display:flex;flex-direction:column;gap:1.25rem">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div>
            <label style="display:block;font-size:.82rem;color:var(--text-dim);margin-bottom:.4rem">Nom du club *</label>
            <input type="text" name="club_name" placeholder="Ex: Le Club Privé" required style="width:100%;background:var(--bg-card2);border:1px solid var(--bg-border);border-radius:var(--radius-sm);padding:.75rem 1rem;color:var(--text-white);font-family:var(--font-sans);outline:none;font-size:.9rem;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bg-border)'">
          </div>
          <div>
            <label style="display:block;font-size:.82rem;color:var(--text-dim);margin-bottom:.4rem">Ville *</label>
            <input type="text" name="city" placeholder="Ex: Paris" required style="width:100%;background:var(--bg-card2);border:1px solid var(--bg-border);border-radius:var(--radius-sm);padding:.75rem 1rem;color:var(--text-white);font-family:var(--font-sans);outline:none;font-size:.9rem;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bg-border)'">
          </div>
        </div>
        <div>
          <label style="display:block;font-size:.82rem;color:var(--text-dim);margin-bottom:.4rem">Description courte *</label>
          <textarea name="description" rows="3" placeholder="Décrivez votre club en quelques mots..." required style="width:100%;background:var(--bg-card2);border:1px solid var(--bg-border);border-radius:var(--radius-sm);padding:.75rem 1rem;color:var(--text-white);font-family:var(--font-sans);outline:none;font-size:.9rem;resize:vertical;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bg-border)'"></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div>
            <label style="display:block;font-size:.82rem;color:var(--text-dim);margin-bottom:.4rem">Email de contact *</label>
            <input type="email" name="email" placeholder="contact@monclub.fr" required style="width:100%;background:var(--bg-card2);border:1px solid var(--bg-border);border-radius:var(--radius-sm);padding:.75rem 1rem;color:var(--text-white);font-family:var(--font-sans);outline:none;font-size:.9rem;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bg-border)'">
          </div>
          <div>
            <label style="display:block;font-size:.82rem;color:var(--text-dim);margin-bottom:.4rem">Site web</label>
            <input type="url" name="website" placeholder="https://monclub.fr" style="width:100%;background:var(--bg-card2);border:1px solid var(--bg-border);border-radius:var(--radius-sm);padding:.75rem 1rem;color:var(--text-white);font-family:var(--font-sans);outline:none;font-size:.9rem;transition:border-color .2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--bg-border)'">
          </div>
        </div>
        <button type="submit" class="btn btn--primary btn--lg" style="align-self:flex-start">
          Envoyer ma demande →
        </button>
      </form>
    </div>

  </div>
</section>

<?php require 'footer.php'; ?>