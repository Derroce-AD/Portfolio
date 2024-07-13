<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$headTitle = "Accueil";

// Connect to database
include '../includes/_connect.php';
setlocale(LC_TIME, 'fr_FR.UTF-8');

// Array to translate month names in french
$monthsTranslation = array(
    'January' => 'janvier',
    'February' => 'février',
    'March' => 'mars',
    'April' => 'avril',
    'May' => 'mai',
    'June' => 'juin',
    'July' => 'juillet',
    'August' => 'août',
    'September' => 'septembre',
    'October' => 'octobre',
    'November' => 'novembre',
    'December' => 'décembre'
);

// Determine the page number and number of articles per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$articlesPerPage = 10;
$offset = ($page - 1) * $articlesPerPage;

// SQL query to retrieve articles with pagination
$sql = "SELECT articles.*, categorie.libelle AS category_libelle, categorie.couleur AS category_color 
        FROM articles 
        LEFT JOIN categorie ON articles.categorie_code = categorie.code 
        ORDER BY articles.cree_le DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $articlesPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total pages
$totalArticlesSql = "SELECT COUNT(*) as count FROM articles";
$totalResult = $conn->query($totalArticlesSql);
$totalArticles = $totalResult->fetch(PDO::FETCH_ASSOC)['count'];
$totalPages = ceil($totalArticles / $articlesPerPage);

// Determine index title based on the number of articles
$indexTitle = ($totalArticles === 1 || $totalArticles === 0) ? "Article" : "Articles";

?>

<!DOCTYPE html>
<html lang="fr">
<?php include '../includes/_head.php'; ?>
<body>
    <?php include '../includes/_header.php'; ?>

    <a id="top"></a>

    <main>
        
        <h1><?php echo $indexTitle;?></h1>

        <!-- DIV with all articles -->
        <div class="articles-list">
            <?php
            if ($result) {
                foreach ($result as $row) {
                    $titre = htmlspecialchars($row['titre']);
                    $pseudo = htmlspecialchars($row['pseudo']);
                    $created = new DateTime($row['cree_le']);
                    $createdFormatted = $created->format('d') . ' ' . $monthsTranslation[$created->format('F')] . ' ' . $created->format('Y à H:i');
                    $expiration = new DateTime($row['expire_le']);
                    $expirationFormatted = $expiration->format('d') . ' ' . $monthsTranslation[$expiration->format('F')] . ' ' . $expiration->format('Y à H:i');
                    $article = htmlspecialchars($row['article']);
                    $categoryLibelle = htmlentities($row['category_libelle'] ?? '', ENT_QUOTES, 'UTF-8');
                    $categoryColor = htmlentities($row['category_color'] ?? '', ENT_QUOTES, 'UTF-8'); // Retrieve the background color
                    $id = htmlspecialchars($row['ID']);
                    $edited = isset($row['modifie_le']) ? new DateTime($row['modifie_le']) : null;
                    $editedFormatted = $edited ? $edited->format('d') . ' ' . $monthsTranslation[$edited->format('F')] . ' ' . $edited->format('Y à H:i') : null;
                    
                    // Display each article with a background color from the database
                    echo "<section class='article' style='border: solid $categoryColor;'>";
                    echo "<div class='article-content'>";
                    echo "<div class='IndexText'>";
                    echo "<h3 class='IndexArticleTitle'>$titre</h3>";
                    echo "<p><strong>Catégorie :</strong> $categoryLibelle</p>";
                    echo "<p><strong>Auteur :</strong> $pseudo</p>";
                    echo "<p><strong>Article :</strong> \"" . substr($article, 0, 50) . "\"...</p>";
                    echo "<p><strong>Créer le :</strong> $createdFormatted</p>";
                    echo "<p><strong>Expire le :</strong> $expirationFormatted</p>";
                    if ($editedFormatted) {
                        echo "<p><strong>Modifié le :</strong> $editedFormatted</p>";
                    }
                    echo "</div>";
                    echo "<div class='buttons'>";
                    echo "<a class='IndexArticleButton' href='./createArticle.php?id=" . $id . "'><i class='fas fa-pen-to-square'></i> Modifier</a>";
                    echo "<a class='IndexArticleButton' href='./article.php?id=" . $id . "'>Voir l'article complet</a>";
                    echo "</div>";
                    echo "</div>";
                    echo "</section>";
                }
            } else {
                echo "<p>Aucun article n'a été trouvé.</p>";
            }
            ?>
        </div>

        <!-- Pages -->
        <div class="pagination">
            <div class="paginationPast">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">Page précédente</a>
                <?php endif; ?>
            </div>
            <div class="paginationNext">
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Page suivante</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <a href="#top" class="back-to-top">↑</a><br><br>
    <?php include '../includes/_footer.php'; ?>
    <button id="backToTop" title="Retour en haut">↑</button>
</body>
</html>

<?php
// Close the connection
$conn = null;
?>
