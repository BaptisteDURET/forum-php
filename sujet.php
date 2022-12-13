<?php
session_start();
if(empty($_SESSION) || !isset($_SESSION['connected']) || $_SESSION['connected'] == false)
{
    header('Location: login.php');
}
$db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php', 'root');
if (!empty($_POST)){
    $message = htmlspecialchars($_POST['commenter']);
    $sql = "INSERT INTO `message` (Contenue, Identifiant_Utilisateur, Date_Creation, Identifiant_Sujet) VALUES (:contenue, :id, :dateCreation, :identifiantSujet)";
    $query = $db->prepare($sql);
    $query->execute([
        'contenue' => $message,
        'id' => $_SESSION['id'],
        'dateCreation' => date('y-m-d'),
        'identifiantSujet' => $_GET['id']
    ]);
}

$title = $db-> prepare('SELECT * FROM `sujet`
                        WHERE Identifiant_Sujet = :id');
$title->execute([
    "id" => $_GET['id']
]);
$TitleSujet = $title->fetch();


if(!empty($_GET['idmessage']) && $_SESSION['admin'] == 1)
{
    $idmessage = $_GET['idmessage'];
    DeleteMessage($idmessage);
}
else if(!empty($_GET['idmessage']) && $_SESSION['admin'] == 0)
{

    echo '<h2 style="color:red; font-size:50px;">Arrête de faire n\'importe quoi !<h2>';
    header( "refresh:5;url=logout.php" );

    
}

function DeleteMessage($idmsg)
{
    $db = new PDO('mysql:host=localhost;port=3307;dbname=forum-php', 'root');
    $sqldel = "DELETE FROM message WHERE Identifiant_MSG = :idmsg";
    $querydel = $db->prepare($sqldel);
    $querydel->execute([
        'idmsg' => $idmsg
    ]);
    header('Location: sujet.php?id='.$_GET['id']);
}
?>


<!DOCTYPE html>
<html lang="FR-fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Topic</title>
</head>
<body>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        tinymce.init({
            selector: 'textarea#commenter',
            toolbar: 'undo redo | formatpainter casechange blocks | bold italic backcolor | ',
            valid_elements : 'em,strong'
        });
    </script>

    <?php
    echo '<h1>'.$TitleSujet["Libelle"].'</h1>';
    ?> 
    
    <div class="formulaire">
        <form method="POST">
            <textarea name="commenter" id="commenter" cols="30" rows="5" placeholder="Message"></textarea>
            <input type="submit" name="ajouter" value="ajouter" id="ajouter">
        </form>
        <button onclick="rtn()" class="bouton">Retour</button>
        <script>
            function  rtn() {
                window.location.href= 'index.php';
            }
        </script>
    </div>    
    <table>
        <?php
            $sql2 = $db->prepare('SELECT * FROM `message`
                                INNER JOIN utilisateur ON message.Identifiant_Utilisateur = utilisateur.Identifiant_Utilisateur
                                WHERE Identifiant_Sujet = :id ORDER BY Date_Creation DESC');
            $result = $sql2->execute([
                "id" => $_GET['id']
            ]);
            $data = $sql2->fetchAll();
            foreach($data as $row){
                $name = $row['Nom_utilisateur'];
                $contenue = $row['Contenue'];
                $date = $row['Date_Creation'];
                if($row['Image_Profil'] != NULL)
                {
                    $ImageProfil = $row['Image_Profil'];
                }
                echo '<tr height="50px">';
                    echo '<td>';
                    if($row['Image_Profil'] != NULL)
                    {
                        echo '<img src="'.$ImageProfil.'" alt="Image de profil" width="50px" height="50px">';
                    }   
                        echo $name;
                    echo '</td>';
                    echo '<td>';
                        echo html_entity_decode($contenue);
                    echo '</td>';
                    echo '<td class="date">';
                        echo $date;
                    echo '</td>';
                    if($_SESSION['admin'] == 1)
                    {
                        echo '<td>';
                            echo '<button style="margin-left: 10px;" onclick="deleteMessage('.$row['Identifiant_MSG'].')">Supprimer</button>';
                        echo '</td>';
                    }
                echo '</tr>';
            };
        ?>
    </table>
</body>
</html>
<script>
    
    function deleteMessage(id) {
        window.location.href=  window.location.href + '&idmessage=' + id;
    }
</script>