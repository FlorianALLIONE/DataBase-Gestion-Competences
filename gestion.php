<?php 

    /**
     * Formulaire de gestion des compétences pour l'utilisateur connecté.
     */

    //vérifier que l'utilisateur est bien connecté, sinon rediriger vers la page d'acceuil
    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();} 
    if(empty($_SESSION['user'])) {
        header('Location: index.php');
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compétences des utilisateurs</title>
</head>
<body>
    <h1>Modifiez vos Compétences :</h1>
    <form action='traitement.php' method="post">
            <select name='competence' id='competence'>
                <?php
                    $logo = array();
                    $info_logo = array();
                    $database = parse_ini_file('db.ini', true);
                    
                    try {
                        // On se connecte a MySQL
                        $bdd = new PDO($database['db']['url'], $database['db']['id'], $database['db']['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                    }
                    catch(Exception $e) {
                        // En cas d'erreur on affcihe un message et on arrête tout
                        die("Erreur : " . $e -> getMessage());
                    }
                
                    try {
                
                    
                        // Requete MySQL
                        $reponse = $bdd->prepare("SELECT id_competence, libelle, logo FROM competence");
                        $reponse->execute();
                        
                        // Si les données existent dans la base de données
                        while($donnees = $reponse->fetch()) {
                            $libelle_logo = $donnees["libelle"];
                            $info_logo["id_comp"] = $donnees["id_competence"];
                            $info_logo["logo"] = $donnees["logo"];
                
                            $logo[$libelle_logo] = $info_logo;
                        }
                    }
                
                    catch(Exception $e) {
                        // En cas d'erreur on affcihe un message et on arrête tout
                        die("Erreur : " . $e -> getMessage());
                    }
                    
                    foreach(array_keys($logo) as $cpt) {
                        echo "<option value='$cpt'>$cpt</option>";
                    }
                ?>
            </select>
            <label for='level'>Niveau</label>
            <input type='number' name='level' id='level' min='1' max='5' value='1'>
            <input type='submit' name='gestion' value='ajouter'>
            <input type='submit' name='gestion' value='modifier'>
            <input type='submit' name='gestion' value='supprimer'>
    </form>
</body>
</html>