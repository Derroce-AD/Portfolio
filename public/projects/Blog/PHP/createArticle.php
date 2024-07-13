<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

$headTitle = isset($_GET['id']) ? "Modifier l'article" : "Nouvel Article";

include '../includes/_connect.php';

$sql = "SELECT code, libelle, couleur FROM categorie";
$stmt = $conn->prepare($sql);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialization of variables
$message = "";
$pseudo = "";
$category = "";
$title = "";
$article = "";
$creeLe = date('Y-m-d H:i:s');
$expireLe = "";
$modifieLe = date('Y-m-d H:i:s');

// DELETE THE EXPIRED ARTICLES
$curentTime = date('Y-m-d H:i:s');
$sql = "DELETE FROM articles WHERE expire_le <= ?";
$stmtExpire = $conn->prepare($sql);
$stmtExpire->execute([$curentTime]);

// CHECK IF FORM WAS SUBMITTED
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // GET THE ARTICLE TO EDIT
    $sql = "SELECT * FROM articles WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $pseudo = $result['pseudo'];
        $category = $result['categorie_code'];
        $title = $result['titre'];
        $article = $result['article'];
        $expireLe = $result['expire_le'];
        $modifieLe = $result['modifie_le']; // GET THE MODIFICATION DATE
    } else {
        $message = "Aucun article disponible.";
    }
    $stmt->closeCursor(); // CLOSE THE PREPARED STATEMENT
}

// TREATMENT OF THE FORM
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = trim($_POST['pseudo']);
    $category = trim($_POST['category']);
    $title = trim($_POST['title']);
    $article = trim($_POST['article']);
    $expireLe = $_POST['expire_le'];
    $modifieLe = date('Y-m-d H:i:s'); // UPDATE THE MODIFICATION DATE
    
    // CHECK IF ALL FIELDS ARE FILLED
    if(empty($pseudo)){
        $message = "Veuillez remplir votre pseudo.";
    }
    if(empty($category)){
        $message = "Veuillez choisir une catégorie.";
    }
    if(empty($title)){
        $message = "Veuillez choisir un titre.";
    }
    if(empty($article)){
        $message = "Veuillez écrire votre article.";
    }
    if(empty($expireLe)){
        $message = "Veuillez choisir une date d'expiration.";
    }

    if (empty($pseudo) || empty($category) || empty($title) || empty($article) || empty($expireLe)) {
        $message = "Veuillez remplir tous les champs.";
    } elseif (strtotime($expireLe) <= strtotime($creeLe)) {
        $message = "La date de fin doit être postérieure à la date de début.";
    } elseif (strlen($title) > 80) {
        $message = "Le titre ne doit pas dépasser 80 caractères.";
    } elseif (strlen($article) > 300) {
        $message = "L'article ne doit pas dépasser 300 caractères.";
    } else {
            if (isset($_GET['id'])) {
                // EDITING AN EXISTING ARTICLE

                // CHECK IF ANOTHER ARTICLE WITH THE SAME TITLE OR CONTENT EXISTS
                $checkSql = "SELECT * FROM articles WHERE titre = ? AND id != ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute([$title, $_GET['id']]);
                $result = $checkStmt->fetchAll();

                if (count($result) > 0) {
                    $message = "Cet article existe déjà.";
                } else {
                    $sql = "UPDATE articles SET pseudo = ?, categorie_code = ?, titre = ?, article = ?, modifie_le = ?, expire_le = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$pseudo, $category, $title, $article, $modifieLe, $expireLe, $_GET['id']]);
                    $message = "L'article a été modifié avec succès.";
                }

            } else {
                // CREATION OF A NEW ARTICLE

                // CHECK IF ANOTHER ARTICLE WITH THE SAME TITLE OR CONTENT EXISTS
                $checkSql = "SELECT * FROM articles WHERE titre = ? OR article = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->execute([$title, $article]);
                $result = $checkStmt->fetchAll();

                if (count($result) > 0) {
                    $message = "Cet article existe déjà.";
                } else {
                    $sql = "INSERT INTO articles (pseudo, categorie_code, titre, article, cree_le, expire_le) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$pseudo, $category, $title, $article, $creeLe, $expireLe]);
                    $message = "Nouvel article a été créé avec succès.";
                }
            }
        }
    }


// CLOSE THE CONNECTION
$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<?php include '../includes/_head.php'; ?>
<body>
    <?php include '../includes/_header.php'; ?>

    <main>
        <form class="formulaire_article" method="POST">
            <h1><?php echo $headTitle; ?></h1><br><br>
            <label for="pseudo">Pseudo :</label><br>
            <input class="InputForm" type="text" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($pseudo); ?>"><br><br>

            <select class="InputForm" name="category" id="category">
                <option value="">Sélectionner une catégorie</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['code']); ?>" <?php echo ($category === $cat['code']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['libelle']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br /><br />

            <label for="title">Titre de l'article :</label><br>
            <input class="InputForm" type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" maxlength="50"><br><br>

            <label for="article">Article :</label><br>
            <textarea class="InputForm" id="article" name="article" maxlength="300"><?php echo htmlspecialchars($article); ?></textarea><br><br>

            <label for="expire_le">Date d'expiration :</label><br>
            <input class="InputForm" type="date" id="expire_le" name="expire_le" value="<?php echo htmlspecialchars($expireLe); ?>"><br><br>

            <input class="ValideCreate" type="submit" value="Valider"><br><br>
            <a class="CreateCancel" href="<?php echo isset($id) ? "./article.php?id=$id" : './index.php'; ?>">Annuler</a><br><br>

            <!-- SHOW MESSAGES IF NEEDED -->
            <?php if (!empty($message)): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </form>
    </main>
    

    <?php include '../includes/_footer.php'; ?>
    <script>
        // COLORS DATA FOR CATEGORIES
        var categoriesColors = {
            <?php foreach ($categories as $cat): ?>
                "<?php echo $cat['code']; ?>": "<?php echo $cat['couleur']; ?>",
            <?php endforeach; ?>
        };

        // FUNCTION TO UPDATE THE BACKGROUND COLOR OF THE SELECTED CATEGORY
        function updateCategoryColor() {
            var categorySelect = document.getElementById('category');  // Utilisation de 'category' au lieu de 'categorie'
            var selectedCategory = categorySelect.value;
            var color = categoriesColors[selectedCategory];

            // APPLY COLOR
            categorySelect.style.backgroundColor = color;
        }

        // LISTENER TO CHANGE COLOR IF THE SELECTED CATEGORY IS CHANGED
        document.getElementById('category').addEventListener('change', updateCategoryColor);

        // CALL TO UPDATE CATEGORY
        updateCategoryColor();
    </script>



</body>
</html>


