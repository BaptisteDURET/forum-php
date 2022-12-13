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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Topic</title>
</head>
<body>
    <div class="formulaire">
        <form method="POST">
            <textarea name="commenter" id="comment" cols="30" rows="5" placeholder="Message" required="required"></textarea>
            <input type="submit" name="ajouter" value="ajouter" id="ajouter">
        </form>
        <button onclick="rtn()" class="bouton">Retour</button>
        <script>
            function rtn() {
                window.location.href= 'index.php';
            }
        </script>
    </div>    
    <table>
        <?php
            $sql2 = $db->prepare('SELECT * FROM `message`
                                INNER JOIN utilisateur ON message.Identifiant_Utilisateur = utilisateur.Identifiant_Utilisateur
                                WHERE Identifiant_Sujet = :id');
            $result = $sql2->execute([
                "id" => $_GET['id']
            ]);
            $data = $sql2->fetchAll();
            foreach($data as $row){
                $name = $row['Nom_utilisateur'];
                $contenue = $row['Contenue'];
                $date = $row['Date_Creation'];

                echo '<tr height="50px">';
                    echo '<td>';
                        echo $name;
                    echo '</td>';
                    echo '<td>';
                        echo htmlspecialchars($contenue);
                    echo '</td>';
                    echo '<td class="date">';
                        echo $date;
                    echo '</td>';
                echo '</tr>';
            };
        ?>
    </table>
</body>
</html>