<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// INITIATE VARIABLES
$titre = ""; 
$pseudo = "";
$created = "";
$createdFormatted = "";
$article = "";
$category = "";
$color = ""; // 
$edited = null;
$editedFormatted = null;
$id = "";
$expiration = "";
$expirationFormatted = "";
$message = "";

// TRANSLATED MONTHS
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

// Check if a deletion confirmation was submitted
if (isset($_POST['confirm_delete']) && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);

    if ($deleteId > 0) {
        include '../includes/_connect.php';

        $sql = "DELETE FROM articles WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$deleteId]);

        // Close the connection
        $conn = null;

        // Redirect to index.php after deletion
        header("Location: ./index.php");
        exit();
    } else {
        $message = "Article invalide.";
    }
}

// Fetch article details if ID is provided in the URL
if (isset($_GET['id'])) {
    $articleId = intval($_GET['id']);

    if ($articleId > 0) {
        include '../includes/_connect.php';

        // Prepare and execute SQL query with PDO
        $sql = "SELECT articles.*, categorie.libelle AS category_name, categorie.couleur AS category_color 
                FROM articles 
                JOIN categorie ON articles.categorie_code = categorie.code 
                WHERE articles.ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$articleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Article found, update variables
            $titre = htmlspecialchars($result['titre']);
            $pseudo = htmlspecialchars($result['pseudo']);
            $created = new DateTime($result['cree_le']);
            $createdFormatted = $created->format('d') . ' ' . $monthsTranslation[$created->format('F')] . ' ' . $created->format('Y à H:i');
            $article = nl2br(htmlspecialchars($result['article']));
            $category = htmlspecialchars($result['category_name']);
            $color = htmlspecialchars($result['category_color']);
            $edited = isset($result['modifie_le']) ? new DateTime($result['modifie_le']) : null;
            $editedFormatted = $edited ? $edited->format('d') . ' ' . $monthsTranslation[$edited->format('F')] . ' ' . $edited->format('Y à H:i') : null;
            $id = intval($result['ID']);
            $expiration = new DateTime($result['expire_le']);
            $expirationFormatted = $expiration->format('d') . ' ' . $monthsTranslation[$expiration->format('F')] . ' ' . $expiration->format('Y à H:i');

            // Define $headTitle with the article title
            $headTitle = $titre;
        } else {
            // No article found
            $headTitle = 'Aucun article';
            $message = "Aucun article trouvé";
        }

        // Close the connection
        $conn = null;
    } else {
        $headTitle = 'Aucun article';
        $message = "ID invalide";
    }
} else {
    $headTitle = 'Aucun article';
    $message = "ID invalide";
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php include '../includes/_head.php'; ?>
<body>

    <?php include '../includes/_header.php'; ?>

    <main class="container">

        <!-- ARTICLE DETAILS DIV -->
        <div class="articleDetails" style="border: solid <?php echo $color; ?>;">
            <?php
            if (isset($result) && $result) {
                echo "<div class='LeftSideArticle'>";
                echo "<h1>" . htmlspecialchars($titre) . "</h1>";
                echo "<hr style='border-color: $color;'>";
                echo "<p class='CategoryArt' style='text-decoration:underline; text-decoration-color: $color; text-decoration-thickness: 0.2vw; '><strong>Catégorie :</strong> " . htmlspecialchars($category) . "</p>";
                echo "<p class='PseudoArt'><strong>De " . htmlspecialchars($pseudo) . " :</strong></p>";
                echo "<p class='Art'>\"" . $article . "\"</p>";
                echo "<div class='DateLineArt'>";
                echo "<p><strong>Créer le :</strong> " . htmlspecialchars($createdFormatted) . "</p>";
                echo "<p class='ExpireArt'><strong>Expire le :</strong> " . htmlspecialchars($expirationFormatted) . "</p>";
                echo "</div>";
                if ($editedFormatted) {
                    echo "<p class='EditArt'><strong>Dernière modification :</strong> " . htmlspecialchars($editedFormatted) . "</p>";
                }
                echo "</div><br>";
                echo "<div class='RightSideArt'>";

                // Show delete and modify links
                ?>
                <form class="SupprimerArticle" action="" method="post" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?php echo $id; ?>">
                    <button class="ButtonSuppr" type="submit" name="show_confirmation">Supprimer</button>&nbsp&nbsp&nbsp&nbsp
                </form><br><br>
                <?php echo "<a class='ModifArt' href='./createArticle.php?id=" . $id . "'>Modifier</a>"; ?>
                </div>

                <?php
                // Show confirmation form if "Delete" button was clicked
                if (isset($_POST['show_confirmation']) && $_POST['delete_id'] == $id) {
                    ?>
                    <div id="confirmation-form">
                        <p>Voulez-vous vraiment supprimer cet article ?</p><br>
                        <form class="ConfirmSuppr" action="" method="post">
                            <input type="hidden" name="delete_id" value="<?php echo $id; ?>">
                            <button class="SupprYes" type="submit" name="confirm_delete">Oui</button>
                            <button class="SupprNo" type="submit" name="cancel_delete">Non</button>
                        </form>
                    </div>
                    <?php
                }

            } else {
                echo "<h1>" . htmlspecialchars($headTitle) . "</h1>";
                echo htmlspecialchars($message);
            }
            ?>
        </div>
    </main>
    <?php include '../includes/_footer.php'; ?>
</body>
</html>
