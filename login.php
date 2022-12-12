<?php
session_start();
if(isset($_SESSION['connected']) && $_SESSION['connected'] == true)
{
    header('Location: index.php');
}
else if(!empty($_POST)){
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash(htmlspecialchars($_POST['password']), PASSWORD_BCRYPT);
    $db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php', 'root');
    $sql = "SELECT * FROM utilisateur WHERE Nom_utilisateur = :Nom_utilisateur";
    $query = $db->prepare($sql);
    $query->execute([
        'Nom_utilisateur'=> $username
    ]);
    if(password_verify(htmlspecialchars($_POST['password']), $query->fetch()['Mot_De_Passe']))
    {
        $_SESSION['connected'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="FR-fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Connexion</title>
</head>
<body>
    <form action="login.php" method="POST">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" name="username" placeholder="Nom d'utilisateur">

        <label for="password">Mot de passe</label>
        <input type="password" name="password" placeholder="Mot de passe">

        <input type="submit" value="Se connecter">
    </form>
    <a href="/forum-php/create-account.php"><h2>Vous ne possédez pas de compte ?</h2></a>
</body>
</html>