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

if(!empty($_GET['idtopic']) && $_SESSION['admin'] == 1)
{
    $idtopic = $_GET['idtopic'];
    DeleteTopic($idtopic);
}
else if(!empty($_GET['idtopic']) && $_SESSION['admin'] == 0)
{

    echo '<h2 style="color:red; font-size:50px;">Arrête de faire n\'importe quoi !<h2>';
    header( "refresh:5;url=logout.php" );

    
}
function DeleteTopic($idtopic)
{
    $db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php', 'root');
    $sqldel = "DELETE FROM sujet WHERE Identifiant_Sujet = :idtopic";
    $querydel = $db->prepare($sqldel);
    $querydel->execute([
        'idtopic' => $idtopic
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
        <table>
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
                    echo '</td>';
                    if($_SESSION['admin'] == 1)
                    {
                        echo '<td>';
                            echo '<button style="margin-left: 10px;" onclick="deleteTopic('.$idSujet.')">Supprimer</button>';
                        echo '</td>';
                    }
                    echo '<br/>';                    
                echo '</tr>';
            };
            ?>
        </table>
    </div>
    <a href="logout.php" id="logout"><h2>Se déconnecter</h2></a>
</body>
</html>
<script>
    function deleteTopic(idtopic)
    {
        let yes = confirm("Voulez-vous vraiment supprimer ce sujet ?");
        if(yes == true)
        {
            console.log(idtopic);
            window.location.href = "index.php?idtopic=" + idtopic;            
        }
    }
</script>