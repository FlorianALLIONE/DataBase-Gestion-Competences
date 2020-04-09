<?php 
/**
 * Permet la génération de snippets HTML
 */

/**
 * Utilisé globalement par la fonction htmlLogo pour l'affichage des logo de chaque compétence
 */
    

/**
 * htmlLogo : renvoie une chaîne de caractère contenant une balise HTML img
 *
 * @param  int $comp : l'id de la compétence dont on veut afficher le logo
 * @return string
 */
function htmlLogo($comp) { 

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

    return <<<html
    <img class="logo" src="{$logo[$comp]['logo']}" alt="{$comp}" width="25" height="25" />
    html;
}

/**
 * htmlUsers : renvoie le code HTML contenant tous les utilisateurs et leurs compétences. 
 *
 * @param  mixed $users
 * @return string
 */
function htmlUsers($users) {
    $html = "";
    foreach(array_keys($users) as $user) {
        $html .= htmlUser($users, $user); 
    }  
    return $html;
}

function htmlUser($users, $nom) {
    $competences = $users[$nom]; 
    $cHtml = ""; 
    foreach($competences as $c => $v) {
        $cHtml .= htmlUserCompetence($c, $v); 
    }

    return <<<html
    <div class="card">
        <div class="card-header user">$nom :</div>
        <div class="card-body">
            $cHtml
        </div>
        <br/>
    </div>
    html; 
}

function htmlUserCompetence($comp, $value) {
    $logo = htmlLogo($comp); 
    return <<<html
    <div class="competence">
        $logo
        <span>$comp : $value</span>
    </div>
    html;
}

function htmlConnection($users) {
    $options = "";
    foreach(array_keys($users) as $username) {
        $options .= "<option value='$username'>$username</option>\n";
    }

    return <<<html
    <div class="menu">
        <h1>Se Connecter :</h1>
        <form action="connection.php" method="POST" name="connection">
            <label for="user">Choix utilisateur : </label><br/>
            <select name="user" id="user">
                $options
            </select><br/>
            <label for="mdp">Mot de passe : </label><br/>
            <input type="password" name="mot_de_passe" id="mdp" placeholder="ex: Bernadettepassword" required/>
            <input type="submit" name="submit" value="connexion">
        </form>
    </div>
    html;

}

function htmlDisconnection() {
    $user = $_SESSION['user']; 

    return <<<html
    <div class="menu">
        <span>Bonjour $user</span>
        <form action="connection.php" method="POST" name="disconnection">
            <input type="submit" name="submit" value="deconnexion">
        </form>
    </div>
    html;
}

?>
