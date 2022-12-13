<?php
if(!empty($_POST))
{
    if($_POST['password'] == $_POST['confirm'] && strlen($_POST['password']) >= 10 && strlen($_POST['username']) >= 3)
    {
        function getClientIP(){       
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
                   return  $_SERVER["HTTP_X_FORWARDED_FOR"];  
            }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
                   return $_SERVER["REMOTE_ADDR"]; 
            }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
                   return $_SERVER["HTTP_CLIENT_IP"]; 
            } 
       
            return '';
        }
        $username = htmlspecialchars($_POST['username']);
        $db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php', 'root');
        $sql2 = "SELECT * FROM utilisateur WHERE Nom_utilisateur = :Nom_utilisateur";
        $query2 = $db->prepare($sql2);
        $query2->execute([
            'Nom_utilisateur'=> $username
        ]);
        if(!$query2->fetch())
        {
            $password = password_hash(htmlspecialchars($_POST['password']), PASSWORD_BCRYPT);
            $date = date('y-m-d');
            $sql = "INSERT INTO utilisateur (Nom_utilisateur, Mot_De_Passe, Date_Inscription, IP) VALUES (:Nom_utilisateur, :Mot_De_Passe, :Date_Inscription, :IP)";
            $query = $db->prepare($sql);
            $query->execute([
                'Nom_utilisateur'=> $username,
                'Mot_De_Passe' => $password,
                'Date_Inscription' => $date,
                'IP' => getClientIP()
            ]);
            session_start();
            $_SESSION['connected'] = false;
            header('Location: login.php');
        }
        else{
            echo "<h2 style='color:red;'>";
            echo "Ce nom d'utilisateur est déjà utilisé";
            echo "</h2>";
        }
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
    <title>Créer un compte</title>
</head>
<body>
    <h1>Créez votre compte</h1>
    <form action="/forum-php/create-account.php" method="POST">

        
        <label for="username">Nom d'utilisateur</label>
        <input id="username" type="text" name="username" placeholder="Nom d'utilisateur" value="<?php if(!empty($_POST))echo htmlspecialchars($_POST['username']) ?>" onchange="verifUsername()">
        <p id="error-username" class="error">Le nom d'utilisateur doit contenir au moins 3 caractères</p>

        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password" placeholder="Mot de passe" onchange="verifPassword()">
        <p id="error-password" class="error">Le mot de passe doit contenir au moins 10 caractères</p>


        <label for="confirm">Confirmer le mot de passe</label>
        <input id="confirm-password" type="password" name="confirm" placeholder="Confirmer le mot de passe" onchange="verifPassword()">
        <p id="error-confirm" class="error">Le mot de passe doit être identique au premier</p>


        <input type="submit" value="Créer votre compte">
    </form>
    <a href="/forum-php/login.php"><h2>Vous possédez déjà un compte ?(Se connecter)</h2></a>
</body>
</html>
<script>
    function verifPassword()
    {
        if(document.getElementById('password').value === document.getElementById('confirm-password').value)
        {
            document.getElementById('password').style.border = "2px solid green";
            document.getElementById('confirm-password').style.border = "2px solid green";
            document.getElementById('error-confirm').style.display = "none";
        }
        else
        {
            document.getElementById('password').style.border = "2px solid red";
            document.getElementById('confirm-password').style.border = "2px solid red";
            document.getElementById('error-confirm').style.display = "block";
        }

        if(document.getElementById('password').value.length < 10)
        {
            document.getElementById('password').style.border = "2px solid red";
            document.getElementById('error-password').style.display = "block";
        }
        else
        {
            document.getElementById('error-password').style.display = "none";
        }
    }

    function verifUsername()
    {
        if(document.getElementById('username').value.length < 3)
        {
            document.getElementById('username').style.border = "2px solid red";
            document.getElementById('error-username').style.display = "block";
        }
        else
        {
            document.getElementById('username').style.border = "2px solid green";
            document.getElementById('error-username').style.display = "none";
        }
    }
</script>