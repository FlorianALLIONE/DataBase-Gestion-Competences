<?php     

    /**
     * Traitement du formulaire de connexion et déconnexion. 
     * 
     */

    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();} 
    include_once('users.php'); 
    $users = loadUsers();
    //$salt = "j'aimebienlespatesparcequec'estunplatsimpleetfacileacuisinerdoncc'estbien";
    $mdp = $_POST["mot_de_passe"];
      
    if(!empty($_POST['submit'])) {
        $action = $_POST['submit']; 
        switch($action) {
            case 'connexion' :
                if(verifPasswd($mdp, $_POST["user"])) {

                    if(!empty($_POST['user'])) {
                        
                        $user = $_POST['user']; 
                        //On vérifie que l'utilisateur existe bien : 
                        if(array_key_exists($user, $users)) {
                            $_SESSION['user'] = $user; 
                        } else {
                            //la connexion n'est pas autorisée
                            header('HTTP/1.1 403 Forbidden', true, 403);
                            echo "Utilisateur inconnu !";
                            exit;
                        }
                    }
                }
                else {
                    header('HTTP/1.1 401 Unauthorized', true, 401);
                    echo "Identification fail";
                }
            break;
            case 'deconnexion' :
                $mdp = "";
                session_unset(); 
                session_destroy(); 
                break;
        }
    }
    
    
    header('Location: index.php');
