<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct()
    {
        parent::__construct(new MainLayout("Dettaglio Notizia - Accademia del Cinema"), function () {

            // 1. RECUPERO E VALIDAZIONE ID ARTICOLO
            $idArticolo = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $articolo = null;
            $correlati = [];

            if ($idArticolo > 0) {
                try {
                    // Connessione diretta nativa
                    $dsn = "mysql:host=localhost;dbname=cinema_newspaper;charset=utf8mb4";
                    $pdo = new PDO($dsn, "root", "", [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);

                    // Estrazione articolo principale
                    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
                    $stmt->execute([':id' => $idArticolo]);
                    $articolo = $stmt->fetch();

                    // Incremento visualizzazioni
                    if ($articolo) {
                        $updateViews = $pdo->prepare("UPDATE articles SET views_number = views_number + 1 WHERE id = :id");
                        $updateViews->execute([':id' => $idArticolo]);
                    }

                    // Estrazione articoli per la sidebar (Esclusi 'video' e 'rassegna stampa/press review')
                    $stmtCorrelati = $pdo->prepare("
    SELECT * FROM articles 
    WHERE id != :id 
      AND LOWER(category) != 'video' 
      AND LOWER(category) != 'press_review'
      AND LOWER(category) != 'rassegna_stampa'
    ORDER BY date DESC, id DESC 
    LIMIT 3
");
                    $stmtCorrelati->execute([':id' => $idArticolo]);
                    $correlati = $stmtCorrelati->fetchAll();

                } catch (Exception $e) {
                    echo "<div class='db-error-alert'><strong>Errore Database:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
                }
            }

            // Fallback: articolo non trovato
            if (!$articolo) {
                ?>
                <div class="main-container error-wrapper">
                    <div class="error-card">
                        <i class="fas fa-exclamation-circle error-icon"></i>
                        <h2>Articolo non disponibile</h2>
                        <p>Il contenuto cercato non è presente nel database o l'ID specificato (<strong><?= $idArticolo ?></strong>) è
                            errato.</p>
                        <a href="tuttiArticoli.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Torna a tutti gli articoli
                        </a>
                    </div>
                </div>
                <?php
                return;
            }

            // Mappatura variabili dinamiche
            $title = $articolo['title'] ?? '';
            $description = $articolo['description'] ?? '';
            $text = !empty($articolo['text']) ? $articolo['text'] : $description;
            $category = $articolo['category'] ?? 'news';
            $dateValue = $articolo['date'] ?? '';
            $author = $articolo['author'] ?? 'Redazione';
            $imageData = $articolo['image'] ?? null;

            $formattedDate = !empty($dateValue) ? date('d M Y', strtotime($dateValue)) : 'Data non specificata';
            $hasImage = (!empty($imageData) && trim($imageData) !== '');

            // Gestione classe badge dinamica
            $badgeClass = 'blue';
            if (strtolower($category) === 'video')
                $badgeClass = 'red';
            if (strtolower($category) === 'press_review' || strtolower($category) === 'rassegna_stampa')
                $badgeClass = 'green';
            ?>

            <section class="description-section">
                <div class="back-link-container">
                    <a href="tuttiArticoli.php" class="back-to-all">
                        <i class="fas fa-arrow-left"></i> TORNA A TUTTI GLI ARTICOLI
                    </a>
                </div>
                <h1>Articoli</h1>
            </section>

            <main class="container content-wrapper">
                <div class="article-layout">

                    <article class="single-article-box" style="min-width: 0;">
                        <div class="article-header-meta">
                            <span class="badge <?= $badgeClass ?>">
                                <i class="fas fa-newspaper"></i> <?= e(ucfirst(str_replace('_', ' ', $category))) ?>
                            </span>
                            <span class="date">
                                <i class="far fa-calendar-alt"></i> di <?= e($author) ?> — <?= e($formattedDate) ?>
                            </span>
                        </div>

                        <h1 class="single-title"><?= e($title) ?></h1>

                        <p class="single-summary"><?= e($description) ?></p>

                        <div class="single-image">
                            <?php if ($hasImage): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($imageData) ?>" alt="<?= e($title) ?>">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&q=80&w=1200"
                                    alt="Cover predefinita">
                            <?php endif; ?>
                        </div>

                        <div class="single-content">
                            <p><?= nl2br(e($text)) ?></p>
                        </div>

                        <div class="share-bar">
                            <span>Condividi l'articolo:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>"
                                target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>" target="_blank"><i
                                    class="fab fa-twitter"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?url=<?= urlencode(current_url()) ?>" target="_blank"><i
                                    class="fab fa-linkedin-in"></i></a>
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode(current_url()) ?>" target="_blank"><i
                                    class="fab fa-whatsapp"></i></a>
                        </div>
                    </article>

                    <aside class="sidebar-box">
                        <h3 class="sidebar-title">Ultimi Aggiornamenti</h3>
                        <?php if (!empty($correlati) && is_array($correlati)): ?>
                            <?php $counter = 1;
                            foreach ($correlati as $item): ?>
                                <div class="sidebar-card">
                                    <span class="rank"><?= $counter++ ?></span>
                                    <h4>
                                        <a href="notizia.php?id=<?= $item['id'] ?>">
                                            <?= e($item['title']) ?>
                                        </a>
                                    </h4>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-data">Nessun altro aggiornamento disponibile.</p>
                        <?php endif; ?>
                    </aside>

                </div>
            </main>

            <?php
        });
    }
};

echo $page->render();