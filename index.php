<?php
session_start();
if(!isset($_SESSION['connected']) || $_SESSION['connected'] == false)
{
    $_SESSION['connected'] = false;
    header('Location: login.php');
}
$db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php', 'root');
if (!empty($_POST)){
    $sujet = htmlspecialchars($_POST['sujet']);
    $sql = "INSERT INTO sujet (Libelle, Identifiant_Utilisateur) VALUES (:sujet, :id)";
    $query = $db->prepare($sql);
    $query->execute([
        'sujet' => $sujet,
        'id'  => 1
]);
}
?>
<!DOCTYPE html>
<html lang="FR-fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Sujet</title>
</head>
<body>
    <h1>Bienvenue <?php echo $_SESSION['username']; ?></h1>
    <form action="" method="POST">
        <input type="text" name="sujet" id="sujetInput">
        <input type="submit" name="Creer" value="Créer sujet">
    </form>

    <div>
        <?php 
        $sql2 = $db->prepare('SELECT * FROM sujet');
        $result = $sql2->execute();
        $data = $sql2->fetchAll();
        foreach($data as $row){
            $sujet2 = $row['Libelle'];
            $idSujet = $row['Identifiant_Sujet'];

            echo '<tr>';
                echo '<td>';
                echo '<a href=';
                echo 'sujet.php?id='.$idSujet.'>';
                echo htmlspecialchars($sujet2);
                echo '</a>';
                echo '<br/>';
                echo '</td>';
            echo '</tr>';
        };
        ?>
    </div>
    <a href="logout.php"><h2>Se déconnecter</h2></a>
</body>
</html>
