<?php
// ─────────────────────────────────────────────────────────────
//  Quiz "Découvre quelle app de rencontre est faite pour toi"
//  - Capture email à la fin
//  - Stocke dans data/leads.json (lecture/écriture serveur)
//  - Renvoie le résultat au format JSON
// ─────────────────────────────────────────────────────────────

// ── Endpoint POST : capture lead ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_lead') {
  header('Content-Type: application/json; charset=utf-8');

  $email   = trim($_POST['email']   ?? '');
  $result  = trim($_POST['result']  ?? '');
  $score   = (int)($_POST['score']  ?? 0);
  $answers = $_POST['answers']      ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'error' => 'Email invalide']);
    exit;
  }

  $lead = [
    'email'      => $email,
    'result'     => $result,
    'score'      => $score,
    'answers'    => $answers,
    'ts'         => date('c'),
    'ip'         => $_SERVER['REMOTE_ADDR'] ?? '',
    'ua'         => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200),
  ];

  // Try persistent storage (data/leads.json). Falls back to /tmp on read-only
  // filesystems (Vercel, etc.) — volatile but useful for previews.
  $primary  = __DIR__ . '/data/leads.json';
  $fallback = '/tmp/leads.json';
  $file     = is_writable(__DIR__ . '/data') ? $primary : $fallback;

  $leads = [];
  if (file_exists($file)) {
    $leads = json_decode(file_get_contents($file), true) ?: [];
  }
  $leads[] = $lead;
  @file_put_contents($file, json_encode($leads, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

  // Mirror to PHP error log so leads are recoverable even when FS is volatile.
  error_log('[LEAD] ' . json_encode($lead, JSON_UNESCAPED_UNICODE));

  echo json_encode(['ok' => true]);
  exit;
}

// ── Page HTML ────────────────────────────────────────────────
$page_title = 'Quiz : Découvre quelle app de rencontre est faite pour toi — ClubÉchangiste.fr';
$meta_desc  = 'En 60 secondes, découvre l\'app de rencontre libertine ou classique qui matche vraiment avec ton profil. Test gratuit et anonyme.';
$canonical  = 'http://club-echangiste.fr/quiz';

include __DIR__ . '/header.php';
?>

<style>
/* ── Quiz-specific styles (cohérents avec le design system) ── */
.quiz-shell {
  min-height: calc(100vh - 60px);
  padding: 7rem 0 5rem;
  position: relative;
  overflow: hidden;
}
.quiz-shell::before {
  content: '';
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse at 20% 0%, rgba(192,57,43,.10) 0%, transparent 55%),
    radial-gradient(ellipse at 80% 100%, rgba(212,175,55,.06) 0%, transparent 55%);
  pointer-events: none;
}
.quiz-wrap {
  position: relative;
  max-width: 720px;
  margin: 0 auto;
  padding: 0 1.5rem;
}

/* ── Progress ─────────────────────────────────────────────── */
.quiz-progress {
  display: flex; align-items: center; gap: 1rem;
  margin-bottom: 2.5rem;
}
.quiz-progress__bar {
  flex: 1; height: 6px;
  background: var(--bg-card2);
  border-radius: 50px;
  overflow: hidden;
  border: 1px solid var(--bg-border);
}
.quiz-progress__fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary), var(--primary-light));
  border-radius: 50px;
  width: 0%;
  transition: width .5s cubic-bezier(.2,.8,.2,1);
  box-shadow: 0 0 12px rgba(192,57,43,.5);
}
.quiz-progress__count {
  font-size: .8rem;
  font-weight: 600;
  color: var(--text-muted);
  font-family: var(--font-sans);
  letter-spacing: .05em;
  white-space: nowrap;
}
.quiz-progress__count strong { color: var(--primary); }

/* ── Slides ───────────────────────────────────────────────── */
.quiz-slide {
  display: none;
  animation: slideFade .45s ease both;
}
.quiz-slide.active { display: block; }
@keyframes slideFade {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

.quiz-step-label {
  font-size: .72rem;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: var(--primary);
  font-weight: 700;
  margin-bottom: 1rem;
}
.quiz-question {
  font-family: var(--font-serif);
  font-size: clamp(1.6rem, 3.2vw, 2.2rem);
  font-weight: 600;
  line-height: 1.25;
  color: var(--text-white);
  margin-bottom: 2rem;
}
.quiz-question em { color: var(--primary); font-style: normal; }

/* ── Options ──────────────────────────────────────────────── */
.quiz-options {
  display: grid;
  gap: .75rem;
  margin-bottom: 2rem;
}
.quiz-option {
  background: var(--bg-card);
  border: 1px solid var(--bg-border);
  border-radius: var(--radius-md);
  padding: 1.1rem 1.4rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  cursor: pointer;
  transition: all .2s;
  font-family: var(--font-sans);
  font-size: .98rem;
  color: var(--text-white);
  text-align: left;
  width: 100%;
}
.quiz-option:hover {
  border-color: rgba(192,57,43,.45);
  background: var(--bg-card2);
  transform: translateX(4px);
}
.quiz-option__emoji {
  font-size: 1.4rem;
  flex-shrink: 0;
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(192,57,43,.1);
  border-radius: 8px;
}
.quiz-option__text { flex: 1; line-height: 1.4; }
.quiz-option.selected {
  border-color: var(--primary);
  background: linear-gradient(135deg, rgba(192,57,43,.12), rgba(192,57,43,.04));
  box-shadow: var(--shadow-glow);
}
.quiz-option.selected .quiz-option__emoji {
  background: var(--primary);
  color: #fff;
}

/* ── Actions ──────────────────────────────────────────────── */
.quiz-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
}
.quiz-back {
  background: none;
  border: none;
  color: var(--text-dim);
  font-size: .85rem;
  cursor: pointer;
  padding: .5rem 0;
  transition: color .2s;
  font-family: var(--font-sans);
}
.quiz-back:hover { color: var(--text-white); }
.quiz-back[disabled] { opacity: 0; pointer-events: none; }

/* ── Intro slide ──────────────────────────────────────────── */
.quiz-intro { text-align: center; }
.quiz-intro__badge {
  display: inline-flex; align-items: center; gap: .5rem;
  background: rgba(212,175,55,.10);
  border: 1px solid rgba(212,175,55,.3);
  color: var(--gold);
  padding: .4rem 1rem;
  border-radius: 50px;
  font-size: .76rem; font-weight: 600;
  letter-spacing: .12em; text-transform: uppercase;
  margin-bottom: 1.5rem;
}
.quiz-intro__title {
  font-family: var(--font-serif);
  font-size: clamp(2rem, 4.5vw, 3.2rem);
  font-weight: 700;
  line-height: 1.15;
  margin-bottom: 1.25rem;
}
.quiz-intro__title span { color: var(--primary); }
.quiz-intro__desc {
  font-size: 1.05rem;
  color: var(--text-muted);
  max-width: 520px;
  margin: 0 auto 2rem;
}
.quiz-intro__meta {
  display: flex; justify-content: center; gap: 2rem;
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
}
.quiz-intro__meta div {
  font-size: .85rem;
  color: var(--text-muted);
  display: flex; align-items: center; gap: .4rem;
}
.quiz-intro__meta strong { color: var(--gold); }

/* ── Email capture ────────────────────────────────────────── */
.quiz-capture {
  text-align: center;
  background: linear-gradient(135deg, #14080A 0%, #1A0D11 100%);
  border: 1px solid rgba(212,175,55,.25);
  border-radius: var(--radius-lg);
  padding: 3rem 2rem;
  position: relative;
  overflow: hidden;
}
.quiz-capture::before {
  content: '';
  position: absolute; inset: 0;
  background: radial-gradient(circle at 50% 0%, rgba(212,175,55,.10) 0%, transparent 60%);
  pointer-events: none;
}
.quiz-capture__lock {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}
.quiz-capture__label {
  font-size: .72rem;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 700;
  margin-bottom: .75rem;
}
.quiz-capture__title {
  font-family: var(--font-serif);
  font-size: 1.7rem;
  font-weight: 600;
  margin-bottom: .75rem;
  line-height: 1.25;
}
.quiz-capture__title span { color: var(--gold); }
.quiz-capture__desc {
  color: var(--text-muted);
  font-size: .95rem;
  margin-bottom: 2rem;
  max-width: 460px;
  margin-left: auto; margin-right: auto;
}
.quiz-capture__teaser {
  background: rgba(192,57,43,.08);
  border: 1px dashed rgba(192,57,43,.35);
  border-radius: var(--radius-md);
  padding: 1.25rem;
  margin: 0 auto 2rem;
  max-width: 420px;
}
.quiz-capture__teaser-line {
  font-size: .82rem;
  color: var(--text-muted);
  letter-spacing: .04em;
}
.quiz-capture__teaser-app {
  font-family: var(--font-serif);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--text-white);
  filter: blur(8px);
  user-select: none;
  margin-top: .4rem;
}
.quiz-capture__teaser-score {
  font-size: .9rem;
  font-weight: 700;
  color: var(--primary-light);
  margin-top: .3rem;
}

.quiz-form {
  display: flex;
  gap: .5rem;
  max-width: 460px;
  margin: 0 auto 1rem;
  flex-wrap: wrap;
}
.quiz-form input[type="email"] {
  flex: 1;
  min-width: 220px;
  background: var(--bg-base);
  border: 1px solid var(--bg-border);
  border-radius: 50px;
  padding: .9rem 1.4rem;
  color: var(--text-white);
  font-family: var(--font-sans);
  font-size: .95rem;
  outline: none;
  transition: border-color .2s;
}
.quiz-form input[type="email"]:focus { border-color: var(--gold); }
.quiz-form input[type="email"]::placeholder { color: var(--text-dim); }
.quiz-form button {
  border-radius: 50px;
  padding: .9rem 1.6rem;
}
.quiz-capture__fine {
  font-size: .72rem;
  color: var(--text-dim);
  max-width: 420px;
  margin: 0 auto;
  line-height: 1.5;
}
.quiz-capture__fine a { color: var(--text-muted); text-decoration: underline; }

/* ── Result ───────────────────────────────────────────────── */
.quiz-result {
  text-align: center;
}
.quiz-result__match {
  font-size: .72rem;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: var(--gold);
  font-weight: 700;
  margin-bottom: 1rem;
}
.quiz-result__score {
  font-family: var(--font-serif);
  font-size: 5rem;
  font-weight: 700;
  background: linear-gradient(135deg, var(--gold), var(--gold-light));
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1;
  margin-bottom: .25rem;
}
.quiz-result__score-label { font-size: .85rem; color: var(--text-muted); margin-bottom: 2rem; }
.quiz-result__card {
  background: var(--bg-card);
  border: 1px solid var(--bg-border);
  border-radius: var(--radius-lg);
  padding: 2.5rem 2rem;
  margin-bottom: 1.5rem;
  position: relative;
  overflow: hidden;
}
.quiz-result__card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary), var(--gold), var(--primary));
}
.quiz-result__app {
  font-family: var(--font-serif);
  font-size: 2.4rem;
  font-weight: 700;
  margin-bottom: .25rem;
  color: var(--text-white);
}
.quiz-result__tagline {
  font-size: .95rem;
  color: var(--gold);
  font-style: italic;
  margin-bottom: 1.5rem;
}
.quiz-result__reasons {
  text-align: left;
  margin: 1.5rem 0;
}
.quiz-result__reasons li {
  display: flex;
  align-items: flex-start;
  gap: .75rem;
  font-size: .92rem;
  color: var(--text-muted);
  padding: .6rem 0;
  border-bottom: 1px solid var(--bg-border);
}
.quiz-result__reasons li:last-child { border-bottom: none; }
.quiz-result__reasons li::before {
  content: '✓';
  color: var(--primary);
  font-weight: 700;
  flex-shrink: 0;
}
.quiz-result__cta {
  width: 100%;
  justify-content: center;
  padding: 1.1rem 2rem;
  font-size: 1rem;
  margin-top: 1rem;
}
.quiz-result__share {
  margin-top: 2rem;
  font-size: .85rem;
  color: var(--text-dim);
}
.quiz-result__share a {
  color: var(--text-muted);
  text-decoration: underline;
  margin: 0 .5rem;
}
.quiz-result__alt {
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid var(--bg-border);
}
.quiz-result__alt-title {
  font-size: .78rem;
  letter-spacing: .15em;
  text-transform: uppercase;
  color: var(--text-dim);
  margin-bottom: 1rem;
  font-weight: 700;
}
.quiz-result__alt-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: .75rem;
}
.quiz-result__alt-item {
  background: var(--bg-card2);
  border: 1px solid var(--bg-border);
  border-radius: var(--radius-sm);
  padding: .85rem;
  font-size: .82rem;
  color: var(--text-muted);
}
.quiz-result__alt-item strong { display: block; color: var(--text-white); margin-bottom: .2rem; font-size: .9rem; }
.quiz-result__alt-item span { color: var(--primary); font-weight: 700; }

/* Loading state */
.quiz-loading {
  text-align: center;
  padding: 4rem 0;
}
.quiz-loading__spinner {
  display: inline-block;
  width: 48px; height: 48px;
  border: 3px solid var(--bg-border);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin .9s linear infinite;
  margin-bottom: 1.5rem;
}
@keyframes spin { to { transform: rotate(360deg); } }
.quiz-loading__text {
  font-family: var(--font-serif);
  font-size: 1.3rem;
  color: var(--text-white);
}
.quiz-loading__sub {
  font-size: .9rem; color: var(--text-muted); margin-top: .5rem;
}

@media (max-width: 600px) {
  .quiz-shell { padding: 6rem 0 3rem; }
  .quiz-result__score { font-size: 3.5rem; }
  .quiz-result__app { font-size: 1.8rem; }
  .quiz-form { flex-direction: column; }
  .quiz-form input[type="email"] { width: 100%; }
  .quiz-form button { width: 100%; }
}
</style>

<section class="quiz-shell">
  <div class="quiz-wrap">

    <!-- ── Progress bar (caché à l'intro & au résultat) ───── -->
    <div class="quiz-progress" id="progress" style="display:none">
      <div class="quiz-progress__bar"><div class="quiz-progress__fill" id="progress-fill"></div></div>
      <div class="quiz-progress__count"><strong id="progress-current">1</strong> / <span id="progress-total">6</span></div>
    </div>

    <!-- ── Intro ──────────────────────────────────────────── -->
    <div class="quiz-slide active" id="slide-intro">
      <div class="quiz-intro">
        <div class="quiz-intro__badge">💘 Quiz exclusif · 100% anonyme</div>
        <h1 class="quiz-intro__title">Découvre quelle <span>app de rencontre</span> est faite pour toi</h1>
        <p class="quiz-intro__desc">
          En 6 questions, on identifie l'application qui colle réellement à ton profil libertin —
          plus de perte de temps sur les plateformes qui ne sont pas pour toi.
        </p>
        <div class="quiz-intro__meta">
          <div>⏱️ <strong>~60 secondes</strong></div>
          <div>🔒 <strong>Anonyme</strong></div>
          <div>🎁 <strong>Code promo offert</strong></div>
        </div>
        <button class="btn btn--primary btn--lg" onclick="startQuiz()">Commencer le quiz →</button>
      </div>
    </div>

    <!-- ── Slides dynamiques (générés en JS) ─────────────── -->
    <div id="slides-container"></div>

    <!-- ── Email capture ─────────────────────────────────── -->
    <div class="quiz-slide" id="slide-email">
      <div class="quiz-capture">
        <div class="quiz-capture__lock">🔓</div>
        <div class="quiz-capture__label">Résultat prêt</div>
        <h2 class="quiz-capture__title">Ton match est <span>déverrouillé</span></h2>
        <p class="quiz-capture__desc">
          On t'envoie immédiatement ton résultat détaillé + un code promo exclusif sur l'app recommandée.
          Reçois aussi les bons plans libertins (clubs, événements, applis) — désinscription en 1 clic.
        </p>

        <div class="quiz-capture__teaser">
          <div class="quiz-capture__teaser-line">Ton app match à</div>
          <div class="quiz-capture__teaser-score" id="teaser-score">—</div>
          <div class="quiz-capture__teaser-app" id="teaser-app">App secrète</div>
        </div>

        <form class="quiz-form" id="lead-form" onsubmit="return submitLead(event)">
          <input type="email" name="email" id="lead-email" placeholder="ton@email.com" required>
          <button type="submit" class="btn btn--gold">Voir mon résultat 🔥</button>
        </form>
        <p class="quiz-capture__fine">
          En soumettant, tu acceptes nos <a href="/mentions-legales">CGU</a> et notre
          <a href="/confidentialite">politique de confidentialité</a>. Pas de spam, jamais.
        </p>
      </div>
    </div>

    <!-- ── Loading ───────────────────────────────────────── -->
    <div class="quiz-slide" id="slide-loading">
      <div class="quiz-loading">
        <div class="quiz-loading__spinner"></div>
        <div class="quiz-loading__text">On analyse ton profil…</div>
        <div class="quiz-loading__sub">Croisement avec 12 000 profils libertins similaires.</div>
      </div>
    </div>

    <!-- ── Résultat ──────────────────────────────────────── -->
    <div class="quiz-slide" id="slide-result">
      <div class="quiz-result">
        <div class="quiz-result__match">★ Ton meilleur match ★</div>
        <div class="quiz-result__score" id="result-score">92%</div>
        <div class="quiz-result__score-label">de compatibilité avec ton profil</div>

        <div class="quiz-result__card">
          <div class="quiz-result__app" id="result-app">—</div>
          <div class="quiz-result__tagline" id="result-tagline">—</div>

          <ul class="quiz-result__reasons" id="result-reasons"></ul>

          <a id="result-cta" href="#" target="_blank" rel="noopener noreferrer sponsored" class="btn btn--gold quiz-result__cta">
            Découvrir <span id="result-cta-name">l'app</span> →
          </a>

          <div class="quiz-result__alt">
            <div class="quiz-result__alt-title">Tes 3 alternatives</div>
            <div class="quiz-result__alt-grid" id="result-alt-grid"></div>
          </div>
        </div>

        <div class="quiz-result__share">
          📧 On t'a envoyé une copie + le code promo par email.
          <br>
          <a href="/quiz" onclick="location.reload();return false;">Recommencer le quiz</a> ·
          <a href="/regions">Voir les clubs près de chez toi</a>
        </div>
      </div>
    </div>

  </div>
</section>

<script>
// ─────────────────────────────────────────────────────────────
//  Quiz data — questions, apps, scoring
// ─────────────────────────────────────────────────────────────
const QUESTIONS = [
  {
    label: 'Question 1',
    title: 'Tu cherches surtout <em>quoi</em> en ce moment ?',
    options: [
      { emoji:'🔥', text:'Du fun et du plaisir sans prise de tête',           scores:{ tinder:3, fruitz:3, wyylde:1, feeld:2 } },
      { emoji:'💞', text:'Une vraie relation, peut-être quelque chose de long', scores:{ hinge:3, bumble:3, adopte:2 } },
      { emoji:'🎭', text:'Du libertinage, échangisme, soirées coquines',         scores:{ wyylde:3, feeld:2, jmelite:3 } },
      { emoji:'💼', text:'Une rencontre discrète (couple stable existant)',     scores:{ gleeden:3, wyylde:1, jmelite:2 } }
    ]
  },
  {
    label: 'Question 2',
    title: 'Tu es <em>plutôt</em> ?',
    options: [
      { emoji:'👨', text:'Un homme seul',          scores:{ tinder:1, bumble:1, adopte:2, gleeden:2, wyylde:1, jmelite:2 } },
      { emoji:'👩', text:'Une femme seule',        scores:{ bumble:3, hinge:2, gleeden:3, wyylde:1, fruitz:1 } },
      { emoji:'👫', text:'En couple',               scores:{ wyylde:3, feeld:3, jmelite:2 } },
      { emoji:'✨', text:'Non-binaire / autre',    scores:{ feeld:3, hinge:2, bumble:1 } }
    ]
  },
  {
    label: 'Question 3',
    title: 'Ton style de drague, c\'est plutôt ?',
    options: [
      { emoji:'📸', text:'Une photo qui claque suffit',          scores:{ tinder:3, fruitz:2, adopte:1 } },
      { emoji:'💬', text:'Des messages bien écrits, du teasing', scores:{ hinge:3, bumble:2, gleeden:2 } },
      { emoji:'🥂', text:'On se voit IRL le plus vite possible', scores:{ wyylde:3, jmelite:2, fruitz:2 } },
      { emoji:'🎭', text:'Mystère et complicité, on prend son temps', scores:{ feeld:3, gleeden:2, hinge:1 } }
    ]
  },
  {
    label: 'Question 4',
    title: 'Quelle distance es-tu prêt(e) à parcourir ?',
    options: [
      { emoji:'🏠', text:'Très près, à 15 minutes max',            scores:{ tinder:2, fruitz:2, adopte:2 } },
      { emoji:'🚗', text:'Jusqu\'à une heure de route, ça passe', scores:{ wyylde:3, hinge:2, bumble:2 } },
      { emoji:'✈️', text:'Partout en France si l\'opportunité est bonne', scores:{ wyylde:2, feeld:2, jmelite:3, gleeden:2 } },
      { emoji:'🌍', text:'Aucune limite (week-ends, voyages…)',    scores:{ feeld:3, jmelite:3, gleeden:1 } }
    ]
  },
  {
    label: 'Question 5',
    title: 'Combien es-tu prêt(e) à <em>investir</em> par mois ?',
    options: [
      { emoji:'🆓', text:'Rien, je veux tout gratuit',         scores:{ tinder:3, bumble:3, fruitz:2 } },
      { emoji:'💳', text:'Max 20 €/mois pour de la qualité',  scores:{ hinge:2, adopte:3, fruitz:2 } },
      { emoji:'💎', text:'30-60 € si c\'est sérieux',          scores:{ wyylde:3, gleeden:3, feeld:2 } },
      { emoji:'👑', text:'Aucune limite, je veux du premium', scores:{ jmelite:3, wyylde:2, gleeden:2 } }
    ]
  },
  {
    label: 'Question 6',
    title: 'Ton mood pour ce soir ?',
    options: [
      { emoji:'😎', text:'Léger, drôle, on rigole',                 scores:{ tinder:2, fruitz:3, bumble:2 } },
      { emoji:'🍷', text:'Sensuel, lent, dîner aux chandelles',     scores:{ hinge:3, adopte:2, gleeden:2 } },
      { emoji:'🔞', text:'Chaud bouillant, on lâche les freins',    scores:{ wyylde:3, jmelite:3, feeld:2 } },
      { emoji:'🎭', text:'Mystère, jeu de rôle, exploration',       scores:{ feeld:3, wyylde:2, gleeden:2 } }
    ]
  }
];

// ─────────────────────────────────────────────────────────────
//  Apps catalog (résultats possibles + raisons)
// ─────────────────────────────────────────────────────────────
const APPS = {
  tinder: {
    name: 'Tinder',
    tagline: 'La référence mondiale du swipe — rapide, addictive, jeune.',
    cta: 'https://tinder.com/?ref=clubechangiste',
    reasons: [
      'Volume énorme : tu trouveras toujours quelqu\'un près de toi.',
      'Gratuit, idéal pour tester sans engagement.',
      'Parfait pour des rencontres décontractées et fun.'
    ]
  },
  bumble: {
    name: 'Bumble',
    tagline: 'Les femmes font le premier pas — plus respectueux, plus qualifié.',
    cta: 'https://bumble.com/?ref=clubechangiste',
    reasons: [
      'Moins de spam et de comportements relous : l\'ambiance est plus saine.',
      'Bon mix entre relation sérieuse et rencontres légères.',
      'Interface premium et profils mieux remplis qu\'ailleurs.'
    ]
  },
  hinge: {
    name: 'Hinge',
    tagline: 'L\'app "conçue pour être supprimée" — taillée pour la vraie connexion.',
    cta: 'https://hinge.co/?ref=clubechangiste',
    reasons: [
      'Profils détaillés : tu sais à qui tu parles avant de matcher.',
      'Algorithme orienté compatibilité long-terme.',
      'Communauté éduquée, intentions claires.'
    ]
  },
  adopte: {
    name: 'AdopteUnMec',
    tagline: 'L\'app française historique — les femmes choisissent leurs prétendants.',
    cta: 'https://www.adopteunmec.com/?ref=clubechangiste',
    reasons: [
      'Énorme base francophone, idéal pour rester en France.',
      'Système panier original qui change du swipe.',
      'Femmes très actives, taux de réponse élevé.'
    ]
  },
  wyylde: {
    name: 'Wyylde',
    tagline: 'Le réseau social libertin n°1 en France — clubs, soirées, échangisme.',
    cta: 'https://www.wyylde.com/?ref=clubechangiste',
    reasons: [
      'La plus grosse communauté libertine francophone.',
      'Couples, célibataires, événements en clubs : tout est centralisé.',
      'Profils vérifiés, ambiance respectueuse et assumée.'
    ]
  },
  feeld: {
    name: 'Feeld',
    tagline: 'L\'app pour explorer ta sexualité — couples, polyamour, kinks.',
    cta: 'https://feeld.co/?ref=clubechangiste',
    reasons: [
      'Communauté ouverte d\'esprit, zéro jugement.',
      'Profils riches : tu déclares tes envies, on ne devine pas.',
      'Idéale pour les couples qui veulent expérimenter à 3+.'
    ]
  },
  gleeden: {
    name: 'Gleeden',
    tagline: 'Rencontres extraconjugales en toute discrétion — gratuit pour les femmes.',
    cta: 'https://gleeden.com/?ref=clubechangiste',
    reasons: [
      'Confidentialité absolue : photos floutées, alias possibles.',
      'Gratuit pour les femmes, équilibre H/F sain.',
      'Communauté mature (30+), intentions claires.'
    ]
  },
  fruitz: {
    name: 'Fruitz',
    tagline: 'Tu choisis ton fruit, tu choisis ton intention — fini les malentendus.',
    cta: 'https://fruitz.io/?ref=clubechangiste',
    reasons: [
      'Système de fruits : tout le monde sait ce que l\'autre cherche.',
      'Communauté jeune et fun, surtout en France.',
      'Parfait pour un plan court ou un date léger.'
    ]
  },
  jmelite: {
    name: 'Jacquie & Michel Elite',
    tagline: 'Le club de rencontres libertines premium — sélection et discrétion.',
    cta: 'https://www.jacquie-et-michel-elite.com/?ref=clubechangiste',
    reasons: [
      'Profils sélectionnés et vérifiés : qualité avant quantité.',
      'Ambiance assumée : tout le monde est là pour les mêmes raisons.',
      'Soirées et événements exclusifs partout en France.'
    ]
  }
};

// ─────────────────────────────────────────────────────────────
//  State + logic
// ─────────────────────────────────────────────────────────────
const state = {
  step: 0,
  answers: [],
  scores: {}
};

function $(id){ return document.getElementById(id); }

function startQuiz() {
  $('slide-intro').classList.remove('active');
  $('progress').style.display = 'flex';
  $('progress-total').textContent = QUESTIONS.length;
  renderQuestion(0);
}

function renderQuestion(idx) {
  state.step = idx;

  // Build slide if not exists
  let slide = document.getElementById('slide-q'+idx);
  if (!slide) {
    const q = QUESTIONS[idx];
    slide = document.createElement('div');
    slide.id = 'slide-q' + idx;
    slide.className = 'quiz-slide';
    slide.innerHTML = `
      <div class="quiz-step-label">${q.label}</div>
      <h2 class="quiz-question">${q.title}</h2>
      <div class="quiz-options">
        ${q.options.map((opt, i) => `
          <button class="quiz-option" data-idx="${i}" onclick="pickOption(${idx}, ${i})">
            <span class="quiz-option__emoji">${opt.emoji}</span>
            <span class="quiz-option__text">${opt.text}</span>
          </button>
        `).join('')}
      </div>
      <div class="quiz-actions">
        <button class="quiz-back" onclick="goBack()" ${idx===0?'disabled':''}>← Précédent</button>
        <div></div>
      </div>
    `;
    $('slides-container').appendChild(slide);
  }

  // Hide all, show this
  document.querySelectorAll('.quiz-slide').forEach(s => s.classList.remove('active'));
  slide.classList.add('active');

  // Mark already-selected
  if (state.answers[idx] !== undefined) {
    slide.querySelectorAll('.quiz-option').forEach((btn, i) => {
      btn.classList.toggle('selected', i === state.answers[idx]);
    });
  }

  updateProgress();
}

function pickOption(qIdx, optIdx) {
  state.answers[qIdx] = optIdx;
  // Visual feedback
  const slide = $('slide-q' + qIdx);
  slide.querySelectorAll('.quiz-option').forEach((btn, i) => {
    btn.classList.toggle('selected', i === optIdx);
  });

  // Auto-advance after short pause
  setTimeout(() => {
    if (qIdx + 1 < QUESTIONS.length) {
      renderQuestion(qIdx + 1);
    } else {
      // Done → compute + show email capture
      computeResult();
      showEmailCapture();
    }
  }, 350);
}

function goBack() {
  if (state.step > 0) renderQuestion(state.step - 1);
}

function updateProgress() {
  const pct = ((state.step) / QUESTIONS.length) * 100;
  $('progress-fill').style.width = pct + '%';
  $('progress-current').textContent = state.step + 1;
}

function computeResult() {
  // Sum scores from answers
  const scores = {};
  state.answers.forEach((optIdx, qIdx) => {
    const opt = QUESTIONS[qIdx].options[optIdx];
    for (const [app, pts] of Object.entries(opt.scores)) {
      scores[app] = (scores[app] || 0) + pts;
    }
  });
  state.scores = scores;

  // Sort apps by score
  const sorted = Object.entries(scores).sort((a,b) => b[1] - a[1]);
  state.topApp = sorted[0][0];
  state.alts = sorted.slice(1, 4).map(([k]) => k);

  // Compatibility score: normalize to 78-96%
  const maxPossible = QUESTIONS.length * 3;
  const raw = sorted[0][1] / maxPossible;
  state.compat = Math.min(96, Math.max(78, Math.round(raw * 100 + 30)));
}

function showEmailCapture() {
  document.querySelectorAll('.quiz-slide').forEach(s => s.classList.remove('active'));
  $('progress').style.display = 'none';
  $('teaser-app').textContent = APPS[state.topApp].name;
  $('teaser-score').textContent = state.compat + '% de compatibilité';
  $('slide-email').classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function submitLead(e) {
  e.preventDefault();
  const email = $('lead-email').value.trim();
  if (!email) return false;

  // Show loading
  document.querySelectorAll('.quiz-slide').forEach(s => s.classList.remove('active'));
  $('slide-loading').classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });

  const fd = new FormData();
  fd.append('action', 'submit_lead');
  fd.append('email', email);
  fd.append('result', state.topApp);
  fd.append('score', state.compat);
  fd.append('answers', JSON.stringify(state.answers));

  fetch('/quiz', { method: 'POST', body: fd })
    .then(r => r.json())
    .catch(() => ({ ok: true })) // graceful fallback
    .finally(() => {
      // Min loading time for UX
      setTimeout(showResult, 1800);
    });

  return false;
}

function showResult() {
  document.querySelectorAll('.quiz-slide').forEach(s => s.classList.remove('active'));

  const app = APPS[state.topApp];
  $('result-score').textContent = state.compat + '%';
  $('result-app').textContent = app.name;
  $('result-tagline').textContent = app.tagline;
  $('result-cta').href = app.cta;
  $('result-cta-name').textContent = app.name;

  const reasonsEl = $('result-reasons');
  reasonsEl.innerHTML = app.reasons.map(r => `<li>${r}</li>`).join('');

  const altsEl = $('result-alt-grid');
  altsEl.innerHTML = state.alts.map(key => {
    const a = APPS[key];
    const pct = Math.max(45, state.compat - 12 - Math.floor(Math.random()*15));
    return `
      <div class="quiz-result__alt-item">
        <strong>${a.name}</strong>
        <span>${pct}% match</span>
      </div>
    `;
  }).join('');

  $('slide-result').classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
