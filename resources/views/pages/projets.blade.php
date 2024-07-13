<?php 
$css_version = time(); // Utilise le timestamp actuel pour forcer la mise à jour du CSS
$css = "<link rel='stylesheet' type='text/css' href='../css/projets.css?v=$css_version'>";


$title = "Projets";
include("./Includes/_head.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projets</title>
    <?php echo $css; ?>
</head>
<body>
    <?php include("./Includes/_header.php")?>
    <main>
        <h1 class="projetsTitle">Mes projets</h1>
        <section class="sectionProjets">
            <!-- BLOC AVEC PROJETS -->
            <article class="blockProjets">
                <a href="./PROJECTS/Fansite/PHP/index.php">
                    <img class="projetsLogo" src="../Assets/pictures/rap verse 3.png" alt="">
                    <div class="bottomBlockProjets">
                        <h3>Rap Verse</h3>
                        <p>Ce projet est un 'Fansite', le but du projet était simplement de faire un site présentant un thème qui nous plait. J'ai choisi l'univers de la musique en présentant des rappeurs que j'affectionne.</p>
                    </div>
                </a>
            </article>
            <!-- BLOC AVEC PROJETS -->
            <article class="blockProjets">
                <a href="./PROJECTS/LaPlateforme/HTML/home.html">
                    <img class="projetsLogo" src="../Assets/pictures/logo-plateforme.png" alt="">
                    <div class="bottomBlockProjets">
                        <h3>LaPlateforme</h3>
                        <p>Ce projet était un exercice qui consistait à refaire soi-même le site de <a class="webLink" href="https://laplateforme.io/">La Plateforme</a>.</p>
                    </div>
                </a>
            </article>
            <!-- BLOC AVEC PROJETS -->

            <!-- BLOC AVEC PROJETS -->
            <article class="blockProjets">
                <a href="./PROJECTS/AppFavorites/HTML/index.html    ">
                    <img class="projetsLogo" src="../Assets/pictures/App favorites.png" alt="">
                    <div class="bottomBlockProjets">
                        <h3>App favorites</h3>
                        <p>Ce site présente mes 3 applications favorites sur téléphone. Le but était aussi de faire un déscriptif rapide de chaque application.<a class="webLink" href="https://laplateforme.io/">La Plateforme</a>.</p>
                    </div>
                </a>
            </article>
            <!-- BLOC AVEC PROJETS -->

            <!-- BLOC AVEC PROJETS -->
            <article class="blockProjets">
                <a href="./PROJECTS/Blog/PHP/index.php">
                    <img class="projetsLogo" src="../Assets/pictures/ChromaBlog.jpg" alt="">
                    <div class="bottomBlockProjets">
                        <h3>ChromaBlog</h3>
                        <p>Le projet suivant est un exercice venant d'une boite d'interimaire. L'objectif était de faire un blog, L'utilisateur peut créé un article et une catégorie d'article, les mettre à jour et les supprimer.</p>
                    </div>
                </a>
            </article>
            <!-- BLOC AVEC PROJETS -->
        </section>
    </main>
    <?php include("./Includes/_footer.php")?>
</body>
</html>
