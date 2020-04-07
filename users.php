<?php 


/**
 * loadUsers : Charge les utilisateurs et leurs compétences 
 * depuis le fichier csv dont le nom est donné en paramètres. 
 *
 * @param  string $userFilename
 * @return mixed Tableau des utilisateurs et de leur compétences
 */
function loadUsers() {
	// Déclarartion variables
    $users = array();
    $info = array();
    $username = "Je suis un dindon";

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
        $reponse = $bdd->prepare("SELECT prenom, libelle, niveau FROM utilisateur INNER JOIN competence_utilisateur ON utilisateur.id_utilisateur = competence_utilisateur.id_utilisateur INNER JOIN competence ON competence_utilisateur.id_competence = competence.id_competence ORDER BY utilisateur.prenom ASC");
        $reponse->execute();


        // Si les données existent dans la base de données
        while($donnees = $reponse->fetch()) {
            if ($username != $donnees["prenom"]) {
                $username = $donnees["prenom"];
                unset($info);
            }
            $nom_comp = $donnees["libelle"];
            $info[$nom_comp] = $donnees["niveau"];

            $users[$username] = $info;
        }
    }
    catch(Exception $e) {
        // En cas d'erreur on affcihe un message et on arrête tout
        die("Erreur : " . $e -> getMessage());
    }

    return $users;
    
}


/**
 * saveUsers : sauve le liste des utilisateurs et leurs compétences 
 * dans la base de données
 */
function saveUsers($users, $cpt, $i) {
    $username = $_SESSION['user'];

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
    
        // Requete MySQL : On récupère l'identifiant utilisateur et de LA compétence qu'on veut modifier ou supprimer
        $reponse = $bdd->prepare("SELECT prenom, utilisateur.id_utilisateur, competence.id_competence, competence.libelle FROM utilisateur INNER JOIN competence_utilisateur ON utilisateur.id_utilisateur = competence_utilisateur.id_utilisateur INNER JOIN competence ON competence_utilisateur.id_competence = competence.id_competence WHERE utilisateur.prenom = :prenom AND competence.libelle = :langage");
        $reponse->execute(array('prenom' => $username, 'langage' => $cpt));

        // Si les données existent dans la base de données
        while($donnees = $reponse->fetch()) {
            $id_user = $donnees["id_utilisateur"];
            $id_comp = $donnees["id_competence"];
        }
    }
    catch(Exception $e) {
        // En cas d'erreur on affcihe un message et on arrête tout
        die("Erreur : " . $e -> getMessage());
    }


    // On modifie une compétence
    if ($i == 1) {
        try {
            // Requete MySQL
            $reponse = $bdd->prepare("UPDATE competence_utilisateur SET niveau = :lvl WHERE id_utilisateur = :idUser AND id_competence = :idComp");
            $reponse->execute(array('lvl' => $users[$username][$cpt], 'idUser' => $id_user, 'idComp' => $id_comp));
        }
        catch(Exception $e) {
            // En cas d'erreur on affcihe un message et on arrête tout
            die("Erreur : " . $e -> getMessage());
        }
    }
    // On supprime la compétence
    elseif ($i == 0) {
        echo "WWWWWEEEEEEEEEEEESSSSSSSSSHHHHHHHHH";
        try {
            echo "HEEEEEEELLLLLLLLLLOOOOOOOOOO";
            // Requete MySQL
            $reponse = $bdd->prepare("DELETE FROM competence_utilisateur WHERE id_utilisateur = :idUser AND id_competence = :idComp");
            $reponse->execute(array('idUser' => $id_user, 'idComp' => $id_comp));
        }
        catch(Exception $e) {
            // En cas d'erreur on affcihe un message et on arrête tout
            die("Erreur : " . $e -> getMessage());
        }
    }

    
    // Bouton ajouter compétence
    elseif($i = 2) { 
        try {
    
            // Requete MySQL : On récupère le identifiant de la compétence qu'on veut ajouter
            $reponse = $bdd->prepare("SELECT id_competence FROM competence WHERE libelle = :langage");
            $reponse->execute(array('langage' => $cpt));
    
            // Si les données existent dans la base de données
            while($donnees = $reponse->fetch()) {
                $id_comp_add = $donnees["id_competence"];
            }
        }
        catch(Exception $e) {
            // En cas d'erreur on affcihe un message et on arrête tout
            die("Erreur : " . $e -> getMessage());
        }

        try {
    
            // Requete MySQL : On récupère le identifiant de la compétence qu'on veut ajouter
            $reponse = $bdd->prepare("SELECT id_utilisateur FROM utilisateur WHERE prenom = :user");
            $reponse->execute(array('user' => $username));
    
            // Si les données existent dans la base de données
            while($donnees = $reponse->fetch()) {
                $id_user_add = $donnees["id_utilisateur"];
            }
        }
        catch(Exception $e) {
            // En cas d'erreur on affcihe un message et on arrête tout
            die("Erreur : " . $e -> getMessage());
        }

        // On ajoute une compétence
        try {
            print_r($id_user);
            // Requete MySQL
            $reponse = $bdd->prepare("INSERT INTO competence_utilisateur (id_utilisateur, id_competence, niveau) VALUES (:idUser, :idComp,:lvl)");
            $reponse->execute(array('idUser' => $id_user_add, 'idComp' => $id_comp_add, 'lvl' => $users[$username][$cpt]));
        }
        catch(Exception $e) {
            // En cas d'erreur on affcihe un message et on arrête tout
            die("Erreur : " . $e -> getMessage());
        }
    }
    
}
?>
