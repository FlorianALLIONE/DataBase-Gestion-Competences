<?php
/**
 * Point d'entré principal du site. 
 * Si aucun utilisateur n'est connecté, ce fichier affiche un menu de connexion 
 * et tous les utilisateurs avec leurs compétences. 
 * Si un utilisateur est connecté, il voit seulement un menu de déconnexion, 
 * un lien lui permettant de gérer ses compétences, et ses compétences. 
 * 
 */

    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();} 
    include_once('users.php'); 
    include_once('templates.php');
    $users = loadUsers();
    //print_r($_POST);
    $connected = !empty($_SESSION['user']);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétences des utilisateurs</title>
</head>
<body>
    <nav class="header-nav">
        <?php 
            if(!$connected) {
                echo htmlConnection($users); 
            } else {
                echo htmlDisconnection();
            }
        ?>
    </nav>
    <div class="container">
        <?php 
        
            if(!$connected) {
                echo <<<html
                <h1>Compétences de tout les Utilisateurs</h1>
                html;
          	    echo htmlUsers($users); 
            } else {
                echo htmlUser($users, $_SESSION['user']); 
                echo "<a href='gestion.php'>Gérer ses compétences</a>";
            }
        ?>
    </div>
</body>
</html>
