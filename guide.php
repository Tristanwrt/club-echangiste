<?php
$page_title = 'Guide du libertin — Tout savoir sur les clubs échangistes en France';
$meta_desc  = 'Guide complet pour les libertins : comment fonctionne un club échangiste, dress code, tarifs, règles, conseils pour débutants. Tout ce qu\'il faut savoir.';
$canonical  = 'http://club-echangiste.fr/guide';
require 'header.php';
?>

<div class="page-hero">
  <div class="container">
    <nav class="breadcrumb">
      <a href="/">Accueil</a>
      <span class="breadcrumb__sep">/</span>
      <span>Guide du libertin</span>
    </nav>
    <p class="label page-hero__label">Guide complet</p>
    <h1 class="page-hero__title">Guide du libertin en France</h1>
    <p class="page-hero__desc">Tout ce qu'il faut savoir avant de franchir la porte d'un club échangiste.</p>
  </div>
</div>

<section>
  <div style="max-width:860px;margin:0 auto;padding:0 1.5rem">

    <!-- Intro -->
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <div class="info-box">
        <p>Ce guide est réservé aux adultes consentants. Il s'adresse aux personnes majeures souhaitant s'informer sur la vie libertine en France dans un cadre légal et respectueux.</p>
      </div>
    </div>

    <!-- Section 1 -->
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.25rem">Qu'est-ce qu'un club échangiste ?</h2>
      <p style="margin-bottom:1rem">Un club échangiste (aussi appelé club libertin ou club privé) est un établissement privé où des adultes consentants se réunissent pour partager des expériences intimes avec d'autres couples ou célibataires. Ces lieux sont légaux en France et soumis à la réglementation des établissements de nuit.</p>
      <p style="margin-bottom:1rem">Il en existe de toutes sortes : des clubs très haut de gamme avec piscine et champagne, des lieux plus modestes mais chaleureux, des clubs spécialisés (fetish, SM, nudistes...) ou encore des clubs mixtes accueillant tous les profils.</p>
      <p>En France, on estime à plus de 500 le nombre de clubs échangistes actifs, répartis dans toutes les grandes villes et parfois en zones rurales.</p>
    </div>

    <div class="divider"></div>

    <!-- Section 2 -->
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.25rem">Les règles d'or du bon libertin</h2>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <?php
        $rules = [
          ['icon'=>'heart','title'=>'Consentement absolu','text'=>'Le Non est sacré. Aucune avance non désirée, aucune insistance. C\'est LA règle fondamentale.'],
          ['icon'=>'shield','title'=>'Hygiène irréprochable','text'=>'Douche avant la soirée, dents propres. La propreté est une marque de respect envers les autres.'],
          ['icon'=>'eye-off','title'=>'Discrétion totale','text'=>'Ce que vous voyez au club reste au club. Les identités des autres membres sont confidentielles.'],
          ['icon'=>'smile','title'=>'Esprit positif','text'=>'Un refus n\'est pas une vexation. Restez souriant et bienveillant, l\'ambiance en dépend.'],
          ['icon'=>'phone-off','title'=>'Pas de photos','text'=>'L\'utilisation du téléphone est strictement interdite dans la plupart des clubs. Respectez cette règle.'],
          ['icon'=>'users','title'=>'Respectez le couple','text'=>'Si vous approchez une personne venue en couple, assurez-vous de l\'accord des deux partenaires.'],
        ];
        foreach ($rules as $r): ?>
        <div class="feature-card" style="padding:1.5rem">
          <div class="feature-card__icon">
            <i data-lucide="<?= $r['icon'] ?>" width="22" height="22" color="#C0392B"></i>
          </div>
          <h3 class="feature-card__title"><?= htmlspecialchars($r['title']) ?></h3>
          <p class="feature-card__desc"><?= htmlspecialchars($r['text']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="divider"></div>

    <!-- Section 3 : Dress code -->
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.25rem">Le dress code</h2>
      <p style="margin-bottom:1rem">Chaque club a ses propres exigences vestimentaires, mais voici les grandes catégories :</p>
      <ul style="list-style:none;display:flex;flex-direction:column;gap:.75rem">
        <?php
        $dress = [
          ['Élégant','Costume/robe de soirée. Pour les clubs haut de gamme. Pas de jeans, pas de baskets.'],
          ['Sexy/Libertin','Lingerie, corset, vinyle, dentelle... Laissez libre cours à votre séduction.'],
          ['Fetish/BDSM','Cuir, latex, uniforme, maîtresse... Pour les soirées spécialisées.'],
          ['Naturiste','Aucune tenue requise (serviette obligatoire). Pour les clubs nudistes.'],
          ['Soirée thématique','Costume imposé : années 80, blanc, rouge, masque... Vérifiez le thème avant.'],
        ];
        foreach ($dress as $d): ?>
        <li style="display:flex;gap:1rem;align-items:flex-start;background:var(--bg-card);border:1px solid var(--bg-border);padding:1rem 1.25rem;border-radius:var(--radius-sm)">
          <i data-lucide="tag" width="16" height="16" color="#C0392B" style="flex-shrink:0;margin-top:3px"></i>
          <div>
            <strong style="color:var(--text-white);font-size:.9rem"><?= htmlspecialchars($d[0]) ?> : </strong>
            <span style="color:var(--text-muted);font-size:.88rem"><?= htmlspecialchars($d[1]) ?></span>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="divider"></div>

    <!-- Section 4 : Tarifs -->
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.25rem">Les tarifs</h2>
      <p style="margin-bottom:1.25rem">Les prix varient selon le standing du club, la ville et la soirée :</p>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem">
        <?php
        $prices = [
          ['Entrée couple','30€ – 120€'],
          ['Célibataire H','40€ – 80€ (soirées spécifiques)'],
          ['Célibataire F','Souvent gratuit ou très réduit'],
          ['Cotisation annuelle','50€ – 200€/an selon club'],
          ['Consommations','Bar souvent inclus ou à prix coûtant'],
        ];
        foreach ($prices as $p): ?>
        <div style="background:var(--bg-card);border:1px solid var(--bg-border);border-radius:var(--radius-sm);padding:1rem">
          <div style="font-size:.7rem;color:var(--text-dim);letter-spacing:.1em;text-transform:uppercase;margin-bottom:.35rem"><?= htmlspecialchars($p[0]) ?></div>
          <div style="font-size:1rem;font-weight:600;color:var(--text-white)"><?= htmlspecialchars($p[1]) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="divider"></div>

    <!-- FAQ -->
    <div class="reveal-on-scroll" style="margin-bottom:3rem">
      <h2 style="margin-bottom:1.5rem">Questions fréquentes</h2>
      <?php
      $faqs = [
        ['Peut-on y aller seul ?','Certains clubs acceptent les célibataires, surtout les femmes. Les hommes seuls sont souvent admis uniquement lors de soirées spéciales. Renseignez-vous avant.'],
        ['Doit-on obligatoirement participer ?','Non. Vous pouvez venir observer, discuter au bar, profiter de l\'ambiance sans aucune obligation. Le passage à l\'acte est toujours un choix libre.'],
        ['Y a-t-il un risque pour la santé ?','Les préservatifs sont toujours mis à disposition dans les clubs sérieux. L\'usage est fortement recommandé. La plupart des membres libertins sont régulièrement testés.'],
        ['C\'est légal en France ?','Oui. Les clubs échangistes sont des établissements légaux en France, soumis à la législation sur les lieux de nuit. L\'accès est réservé aux majeurs.'],
        ['Peut-on rester anonyme ?','Oui. Vous pouvez utiliser un prénom d\'emprunt. La discrétion est une valeur fondamentale dans la communauté libertine.'],
      ];
      foreach ($faqs as $faq): ?>
      <details style="border:1px solid var(--bg-border);border-radius:var(--radius-sm);margin-bottom:.5rem;overflow:hidden">
        <summary style="padding:1.1rem 1.5rem;cursor:pointer;font-weight:600;color:var(--text-white);list-style:none;display:flex;justify-content:space-between;align-items:center">
          <?= htmlspecialchars($faq[0]) ?>
          <i data-lucide="chevron-down" width="16" height="16" color="var(--text-dim)"></i>
        </summary>
        <div style="padding:1rem 1.5rem 1.25rem;background:var(--bg-card2);color:var(--text-muted);font-size:.9rem;line-height:1.6">
          <?= htmlspecialchars($faq[1]) ?>
        </div>
      </details>
      <?php endforeach; ?>
    </div>

    <!-- DEROGE BOX -->
    <div class="deroge-box reveal-on-scroll">
      <div class="deroge-box__crown">💻</div>
      <p class="deroge-box__label">Alternative numérique</p>
      <h2 class="deroge-box__title">Pas encore prêt pour un club ? <span>Commencez en ligne</span></h2>
      <p class="deroge-box__desc">
        La meilleure façon de débuter dans le monde libertin : créez un profil sur le site #1 de rencontres libertines, 
        échangez avec des couples et célibataires de votre région, et progressez à votre rythme.
      </p>
      <div class="deroge-box__cta">
        <a href="https://www.jacquie-et-michel-elite.com/?ref=clubechangiste" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold btn--lg">
          🔥 Créer mon profil libertin gratuitement
        </a>
      </div>
      <p class="deroge-box__fine">Lien affilié · Réservé aux adultes +18 ans</p>
    </div>

  </div>
</section>

<?php require 'footer.php'; ?>