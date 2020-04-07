<?php     

    /**
     * Traitement du formulaire de connexion et déconnexion. 
     * 
     */

    if(session_status() !== PHP_SESSION_ACTIVE) { session_start();} 
    include_once('users.php'); 
    $users = loadUsers();
    if(!empty($_POST['submit'])) {
        $action = $_POST['submit']; 
        switch($action) {
            case 'connexion' : 
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
            break;
            case 'deconnexion' : 
                session_unset(); 
                session_destroy(); 
                break;
        }
    }
    header('Location: index.php');
