<?php 
include("./Includes/_connect.php");
$css_version = time(); // Utilise le timestamp actuel pour forcer la mise à jour du CSS
$css = "<link rel='stylesheet' type='text/css' href='../css/contact.css?v=$css_version'>";

$title = "Contact";
include("./Includes/_head.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $mail= $_POST['mail'];
    $tel = $_POST['tel'];


    $sql = "INSERT INTO contact (message, mail, telephone) VALUES ('$message', '$mail', '$tel')";
    mysqli_query($conn, $sql);
    header("Location: contact.php");
    exit();
}
?>



<body>
<?php include("./Includes/_header.php")?>
<main>
<form action="contact.php" method="post">

<label for="mail">Adresse mail :</label>
<input name="mail" type="text" placeholder="Entrez votre adresse mail" required>
<br />

<label for="tel">Numéro de téléphone (facultatif) : </label>
<input type="text" name="tel" placeholder="Entrez votre numéro de téléphone">
<br />

<label for="message">Message :</label>
<input name="message" type="text" placeholder="Entrez votre message" required>
<br />
<input type="submit" value="Envoyer">

</form>

</main>
<?php include("./Includes/_footer.php")?>
</body>
</html>
