<?php 

    /**
     * Fichier responsable du traitement du formulaire de gestion des compétences 
     * pour l'utilisateur connecté. 
     */

    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();} 

    include_once('users.php'); 

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


    $users = loadUsers();
    //Traitement du formulaire de gestion des compétences
    //On vérifie que l'utilisateur est bien connecté, sinon on interrompt le traitement
    
    if(empty($_SESSION['user'])) {
        // // header('Location: index.php');
        header('HTTP/1.1 401 Unauthorized', true, 401);
        echo "Utilisateur non connecté !";
        exit;
    }
    $action = $_POST['gestion']; 
    if(!empty($_POST['level']) && !empty($_POST['competence'])) {
        $level = $_POST['level'];
        $cpt = $_POST['competence'];
        $user = $_SESSION['user']; 
        $i = 3;
        switch($action) {
            case 'ajouter' : //l'ajout est la modification sont gérées de la même façon
                    $i = 2;
                    //On vérifie que la compétence existe bien : 
                    if(array_key_exists($cpt, $logo)) {
                        $users[$user][$cpt] = $level; 
                    } else {
                        //renvoyer un code d'erreur
                        header('HTTP/1.1 400 Bad Request', true, 400); 
                        echo "Compétence inconnue !";
                        exit;
                    }
                
                break;
            case 'modifier' : 
                    $i = 1;
                    //On vérifie que la compétence existe bien : 
                    if(array_key_exists($cpt, $logo)) {
                        $users[$user][$cpt] = $level; 
                    } else {
                        //renvoyer un code d'erreur
                        header('HTTP/1.1 400 Bad Request', true, 400); 
                        echo "Compétence inconnue !";
                        exit;
                    }
                
                break;
            case 'supprimer' :
                $i = 0;
                print_r($users[$user][$cpt]);
            break;
        }
        saveUsers($users, $cpt, $i);
    }
    header('Location: index.php');
?>