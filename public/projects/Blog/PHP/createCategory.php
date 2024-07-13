<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

// PAGE TITLE
$headTitle = isset($_GET['page']) && $_GET['page'] === 'edit' ? "Modifier la catégorie" : "Créer une catégorie";

// CONNEXION DB
include '../includes/_connect.php';

// GET PAGE INFO FROM THE URL
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// VAR INIT
$message = '';
$message2 = '';
$code = '';
$libelle = '';
$color = '';

// SAVE DATA IN VARIABLES AND SET THEM ON UPPER CASE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        // Conversion des caractères minuscules en majuscules
        $code = strtoupper(trim($_POST['code']));
        $libelle = strtoupper(trim($_POST['libelle']));
        $color = strtoupper(trim($_POST['color']));
        
        // CHECK IF CODE AND LIBELLE ARE EMPTY
        if (empty($code)) {
            $message = "Le code est requis.";
        } elseif (strlen($code) !== 3 || strlen($code) > 3 || !preg_match('/^[A-Z]{1,3}$/', $code)) {
            $message = "Le code doit contenir entre 1 et 3 caractères en majuscules.";
        } elseif (empty($libelle)) {
            $message = "Le libellé est requis.";
        } elseif (strlen($libelle) > 50 || !preg_match('/^[A-Z\s]{1,50}$/', $libelle)) {
            $message = "Le libellé doit contenir entre 1 et 50 caractères en majuscules.";
        } elseif (empty($color)) {
            $message = "La couleur est requise.";
        } else {
            // CHECK IF COLOR OR CODE ALREADY EXIST
            $sqlCheck = "SELECT COUNT(*) AS count FROM categorie WHERE code = ? OR couleur = ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            if ($stmtCheck) {
                $stmtCheck->execute([$code, $color]);
                $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if ($row['count'] > 0) {
                    $message = "Une catégorie avec ce code ou cette couleur existe déjà.";
                } else {
                    // INSERT NEW CATEGORY
                    $sqlInsert = "INSERT INTO categorie (code, libelle, couleur) VALUES (?, ?, ?)";
                    $stmtInsert = $conn->prepare($sqlInsert);
                    if ($stmtInsert && $stmtInsert->execute([$code, $libelle, $color])) {
                        $message = "La catégorie a bien été ajoutée.";
                    } else {
                        $message = "Erreur lors de l'ajout de la catégorie.";
                        if ($stmtInsert) {
                            $message .= " " . $stmtInsert->errorInfo()[2];
                        } else {
                            $message .= "Erreur de préparation de la requête.";
                        }
                    }
                }
            } else {
                $message = "Erreur de préparation de la requête de vérification.";
            }
        }
    } elseif (isset($_POST['confirm_delete'])) {
        $categoryCode = $_POST['category'];

        // Check if there are associated articles
        $sqlCheckArticles = "SELECT COUNT(*) AS count FROM articles WHERE categorie_code = ?";
        $stmtCheckArticles = $conn->prepare($sqlCheckArticles);
        if ($stmtCheckArticles) {
            $stmtCheckArticles->execute([$categoryCode]);
            $rowArticles = $stmtCheckArticles->fetch(PDO::FETCH_ASSOC);
            if ($rowArticles['count'] > 0) {
                $message2 = "Cette catégorie est associée à des articles. Vous ne pouvez pas la supprimer.";
            } else {
                // Delete the category if no associated articles
                $sqlDeleteCategory = "DELETE FROM categorie WHERE code = ?";
                $stmtDeleteCategory = $conn->prepare($sqlDeleteCategory);
                if ($stmtDeleteCategory && $stmtDeleteCategory->execute([$categoryCode])) {
                    $message2 = "La catégorie a été supprimée avec succès.";
                } else {
                    $message2 = "Erreur lors de la suppression de la catégorie.";
                    if ($stmtDeleteCategory) {
                        $message2 .= " " . $stmtDeleteCategory->errorInfo()[2];
                    } else {
                        $message2 .= " Erreur de préparation de la requête.";
                    }
                }
            }
        } else {
            $message2 = "Erreur de préparation de la requête de vérification des articles associés.";
        }
    } elseif (isset($_POST['update'])) {
        $updateCode = strtoupper(trim($_POST['new_code']));
        $updateLibelle = strtoupper(trim($_POST['new_libelle']));
        $updateColor = strtoupper(trim($_POST['new_color']));
        $categoryCode = $_POST['category']; // UPDATE CODE CATEGORY

        // CHECK EACH STR
        if (empty($updateCode)) {
            $message2 = "Le code est requis.";
        } elseif (!preg_match('/^[A-Z]{1,3}$/', $updateCode)) {
            $message2 = "Le code doit contenir entre 1 et 3 caractères en majuscules.";
        } elseif (empty($updateLibelle)) {
            $message2 = "Le libellé est requis.";
        } elseif (strlen($updateLibelle) > 50 || !preg_match('/^[A-Z\s]{1,50}$/', $updateLibelle)) {
            $message2 = "Le libellé doit contenir entre 1 et 50 caractères en majuscules.";
        } elseif (empty($updateColor)) {
            $message2 = "La couleur est requise.";
        } else {
            // CHECK IF DATA ALREADY EXIST
            $sqlCheck = "SELECT COUNT(*) AS count FROM categorie WHERE (code = ? OR couleur = ?) AND code <> ?";
            $stmtCheck = $conn->prepare($sqlCheck);
            if ($stmtCheck) {
                $stmtCheck->execute([$updateCode, $updateColor, $categoryCode]);
                $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                if ($row['count'] > 0) {
                    $message2 = "Une catégorie avec ce code ou cette couleur existe déjà.";
                } else {
                    // CHECK IF THERE ARE ASSOCIATED ARTICLES
                    $sqlCheckArticles = "SELECT COUNT(*) AS count FROM articles WHERE categorie_code = ?";
                    $stmtCheckArticles = $conn->prepare($sqlCheckArticles);
                    if ($stmtCheckArticles) {
                        $stmtCheckArticles->execute([$categoryCode]);
                        $rowArticles = $stmtCheckArticles->fetch(PDO::FETCH_ASSOC);
                        if ($rowArticles['count'] > 0) {
                            $message2 = "Cette catégorie est associée à des articles. Vous ne pouvez pas la modifier.";
                        } else {
                            // UPDATE CATEGORY DATA
                            $sqlUpdate = "UPDATE categorie SET code = ?, libelle = ?, couleur = ? WHERE code = ?";
                            $stmtUpdate = $conn->prepare($sqlUpdate);
                            if ($stmtUpdate && $stmtUpdate->execute([$updateCode, $updateLibelle, $updateColor, $categoryCode])) {
                                $message2 = "La catégorie a été modifiée avec succès.";
                            } else {
                                $message2 = "Erreur lors de la modification de la catégorie.";
                                if ($stmtUpdate) {
                                    $message2 .= " " . $stmtUpdate->errorInfo()[2];
                                } else {
                                    $message2 .= " Erreur de préparation de la requête.";
                                }
                            }
                        }
                    } else {
                        $message2 = "Erreur de préparation de la requête de vérification des articles associés.";
                    }
                }
            } else {
                $message2 = "Erreur de préparation de la requête de vérification.";
            }
        }
    }
}

// GET DATA TO PUT IT IN THE FORM
$categories = [];
$sql = "SELECT code, libelle, couleur FROM categorie";
$stmt = $conn->query($sql);
if ($stmt) {
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<?php include '../includes/_head.php'; ?>
<body>
    <?php include '../includes/_header.php'; ?>

    <main>
        <?php
        // SHOW FORM DEPENDING ON CLICKED LINK 
        switch ($page) {
            case 'create':
                echo "<form action='createCategory.php?page=create' method='POST'>
                        <h1>Créer une nouvelle catégorie</h1>
                        <label for='code'>Code (3 caractères max, en majuscules) :</label>
                        <input type='text' id='code' name='code' maxlength='3'>
                        <br /><br />

                        <label for='libelle'>Nom de la catégorie (50 caractères max, en majuscules) :</label>
                        <input type='text' id='libelle' name='libelle' maxlength='50'>
                        <br /><br />

                        <div class='FormColor'>
                            <label class='ColorTitle' for='color'>Couleur :</label>
                            <input type='color' id='color' name='color'>
                            <input type='hidden' id='color_hidden' name='color_hidden'>
                        </div>
                        <br /><br />

                        <a href='createCategory.php?page=edit'>Modifier une catégorie existante</a>
                        <input type='submit' name='add' value='Ajouter la catégorie'><br><br>";
                
                    if (!empty($message)) {
                    echo '<p>'.htmlspecialchars($message).'</p>';
                }

                echo "</form>";
                break;

            case 'edit':
                echo "<form action='createCategory.php?page=edit' method='POST'>
                        <h1>Modifier une catégorie</h1>";

                echo "<select name='category' id='category'>";
                foreach ($categories as $category) {
                    echo "<option value='".htmlspecialchars($category['code'])."' data-color='".htmlspecialchars($category['couleur'])."'>".htmlspecialchars($category['libelle'])."</option>";
                }
                echo "</select>
                        <br /><br />";

                echo "<label for='new_code'>Nouveau code :</label>
                        <input type='text' id='new_code' name='new_code' maxlength='3'>
                        <br /><br />";

                echo "<label for='new_libelle'>Nouveau libellé :</label>
                        <input type='text' id='new_libelle' name='new_libelle' maxlength='50'>
                        <br /><br />";

                echo "<div class='FormColor'>
                        <label class='ColorTitle' for='new_color'>Nouvelle couleur :</label>
                        <input type='color' id='new_color' name='new_color'>
                      </div><br /><br />";

                echo "<div class='EditCatButton'>
                        <input type='submit' name='delete' value='Supprimer la catégorie'>
                        <input type='submit' name='update' value='Valider la modification'>
                      </div>
                      <br /><br />";

                if (isset($_POST['delete'])) {
                    echo "<p>Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
                            <input type='submit' name='confirm_delete' value='Oui'>
                            <input type='submit' name='cancel_delete' value='Non'>
                            <input type='hidden' name='category' value='".htmlspecialchars($_POST['category'])."'>
                            <br /><br />";
                }
                // DISPLAY ERROR MESSAGES
                if (!empty($message2)) {
                    echo "<p>".htmlspecialchars($message2)."</p>";
                }

                echo "</form>";
                break;

            default:
                echo "<h1>404 Not Found</h1>";
                echo "<p>The page you are looking for does not exist.</p>";
                break;
        }
        ?>
    </main>
    
    <script>
        // Function to change the background color of the selected category
        function changeCategoryColor() {
            var categorySelect = document.getElementById('category');
            var selectedOption = categorySelect.options[categorySelect.selectedIndex];
            var color = selectedOption.getAttribute('data-color');
            categorySelect.style.backgroundColor = color;
        }

        // Add event listener for change event on category select element
        document.getElementById('category').addEventListener('change', changeCategoryColor);

        // Change the background color on page load based on selected category
        window.onload = function() {
            changeCategoryColor();
        };
    </script>

    <?php include '../includes/_footer.php'; ?>
</body>
</html>
