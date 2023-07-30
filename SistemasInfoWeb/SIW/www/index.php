

<?php

    session_start();
    $random = rand(0, 9999999); 
    setcookie("UserCookie",$random,time() + (86400)); //Seteo una cookie de prueba valida durante 1 día

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    include "modelo.php";
    include "vista.php";

	$conn = model_connect();
    $accessToken = model_get_api_key();

    if (isset($_GET["action"])) {
        $action = $_GET["action"];
    } else {
        if (isset($_POST["action"])) {
            $action = $_POST["action"];
        } else {
            $action = "inicio";
        }
    }
    if (isset($_GET["key"])) {
        $key = $_GET["key"];
    } else {
        if (isset($_POST["key"])) {
            $key = $_POST["key"];
        } else {
            $key = 1;
        }
    }
	
    if($action === "register"){

        if($key == 1){
            vista_register();
        }
        else{
            if(model_register_user($conn) == 1){
                vista_landing();
            }
            else{
                vista_register();
            }    
        }
    }

    else if($action === "login"){
        
        if($key == 1){
            vista_login();
        }
        else{
            if(model_login($conn) == 1){
                vista_landing();    
            }
            else if(model_login($conn) == 2){
                // $lines_to_add = model_plot_statistics($conn);
                $_SESSION['admin'] = 1;
                vista_admin_workspace();
            }
            else{
                vista_login();
            }
        }
    }

    else if($action === "logout"){

        model_logout($conn,$_SESSION['dateTimeStart'],$_SESSION['idUser']);
        session_unset();
        session_destroy();
        vista_landing();
    }

    else if($action == "inicio"){
        vista_landing();
    }

    else if($action === "parking"){

        if($key == 1){
            $lines_to_add = model_get_parkings($conn);
            vista_lista_parkings($lines_to_add);    
        }
        else if($key == 2){
            model_set_parkings($accessToken, $conn);
            $num_admins = model_get_num_admin_users($conn);
            $num_normales = model_get_num_normal_users($conn);
            $lines_to_add = model_plot_num_users($num_normales,$num_admins);
            vista_admin_workspace($lines_to_add);        }
    }

    else if($action === "plaza"){

        if($key == 1){
            $lines_to_add = model_get_plazas($accessToken);
            $lines_to_add1 = model_get_bicis($accessToken);
            $lines_to_add2 = model_get_estaciones($accessToken,$conn);
            vista_lista_plazas($lines_to_add,$lines_to_add1,$lines_to_add2);
        }

        else if($key == 2){
            
            $lines_to_add = model_activar_geolocalizacion();
            vista_lista_parkings_georef($lines_to_add);
        }
    }

    else if($action === "bus"){

        if($key == 1){
            if(isset($_SESSION['email'])){
                vista_buses();
            }
            else{
                echo "Desbloquea este servicio iniciando sesión!";
                vista_login();
            }
        }

        else if($key == 2){
            model_set_paradas($accessToken, $conn);
            model_set_lineas($accessToken, $conn);
            vista_landing();
        }

        else{
            vista_landing();
        }
    }

    else if($action === "bici"){

        if($key == 1){
            // model_set_estaciones($accessToken,$conn);
            $lines_to_add1 = model_get_bicis($accessToken);
            $lines_to_add2 = model_get_estaciones($accessToken,$conn);
            vista_bicis($lines_to_add1,$lines_to_add2);
        }
    }

    else if($action === "usuario"){

        if($key == 1){
            
            $lines_to_add = model_get_users($conn);
            vista_lista_users($lines_to_add);
        }

        else if($key == 2){

            if (isset($_POST["inputIdUser"])) {
                $idUserToDelete = $_POST["inputIdUser"];
            } else {
                $idUserToDelete = -1;
            }

            model_delete_user($conn,$idUserToDelete);
            $lines_to_add = model_get_users($conn);
            vista_lista_users($lines_to_add);
        }

        else if($key == 3){
            model_add_user($conn);
            $lines_to_add = model_get_users($conn);
            vista_lista_users($lines_to_add);            
        }
    }

    else if($action === "admin"){
        
        $num_admins = model_get_num_admin_users($conn);
        $num_normales = model_get_num_normal_users($conn);
        $lines_to_add = model_plot_num_users($num_normales,$num_admins);
        vista_admin_workspace($lines_to_add);
    }

?>

