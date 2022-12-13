<?php
session_start();
$db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php','root');
if(empty($_SESSION) || !isset($_SESSION['connected']) || $_SESSION['connected'] == false)
{
    header('Location: login.php');
}
if (!file_exists('images-profiles')) {
    mkdir('images-profiles', 0777, true);
}

$uploaddir = 'images-profiles/';
if(!empty($_FILES['avatar']['name'])) 
{

    $allowedTypes = [
        'image/png' => 'png',
        'image/jpeg' => 'jpg'
    ];
    $filetype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $_FILES['avatar']['tmp_name']);
    if(in_array($filetype, array_keys($allowedTypes)))
    {
        
        $imgName = $uploaddir.''.$_SESSION['id'] . "-" .htmlspecialchars($_FILES['avatar']['name']);
        $uploadfile = $uploaddir . basename($imgName);

        move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadfile);

        $sql = "UPDATE utilisateur SET Image_Profil = :Avatar WHERE Identifiant_Utilisateur = :Identifiant_Utilisateur";
        $query = $db->prepare($sql);
        $query->execute([
            'Avatar' => $imgName,
            'Identifiant_Utilisateur' => $_SESSION['id']
        ]);
    }
}
$sql2 = "SELECT Image_Profil FROM utilisateur WHERE Identifiant_Utilisateur = :Identifiant_Utilisateur";
$query2 = $db->prepare($sql2);
$query2->execute([
    'Identifiant_Utilisateur' => $_SESSION['id']
]);
$avatar = $query2->fetch()['Image_Profil'];
?> 
<!DOCTYPE html>
<html lang="FR-fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Profil</title>
</head>
<body>
    <h1>Profil</h1>
    <a href="index.php" style="text-align:right"><h2>Retour à l'accueil</h2></a>
    <br/>
    <?php
        if(!empty($avatar))
        {
            echo "<img src='$avatar' alt='Photo de profil' class='ppProfil'>";
        }
        else
        {
            echo "<p>Vous n'avez pas désigné de photo de profil</p>";
        }
    ?>
    <h2>Choisissez votre photo de profil</h2>
    <form enctype="multipart/form-data" action="profil.php" method="POST" class="form-imgprofil">
        <input type="file" id="avatar" name="avatar" accept="image/png, image/jpeg">
        <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
        <input type="submit" value="Valider">
    </form>
</body>
</html>