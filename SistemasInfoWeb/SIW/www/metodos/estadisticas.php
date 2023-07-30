<?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    include "../modelo.php";

    $conn = model_connect();

    if (isset($_GET["action"])) {
        $action = $_GET["action"];
    } else {
        $action = "calendario";
    }

    if($action === "percent_admins"){

        $consulta = "SELECT email, typeUser, idUser FROM final_usuarios WHERE typeUser = 1";
        $res = mysqli_query($conn, $consulta);    
        $num_admins = mysqli_num_rows($res);
        

        $consulta = "SELECT idUser FROM final_usuarios WHERE typeUser = 0";
        $res = mysqli_query($conn, $consulta);    
        $num_normales = mysqli_num_rows($res);
        
        $result = [
            "numAdmins" => $num_admins,
            "numNormales" => $num_normales
        ];
        echo json_encode($result);


    }

    else if($action === "visitas"){

        $consulta = "SELECT COUNT(final_estadisticas.idUsuario) num_views, final_usuarios.email FROM final_estadisticas INNER JOIN final_usuarios WHERE final_usuarios.idUser=final_estadisticas.idUsuario GROUP BY idUsuario";
        // $consulta = "SELECT * FROM final_estadisticas_disponibilidad INNER JOIN final_parkings ON final_estadisticas_disponibilidad.idParking=final_parkings.idParking WHERE final_parkings.idParking=".$parking;

        $res = mysqli_query($conn, $consulta);    
        $views = array();
        $users = array();
        while($row = mysqli_fetch_assoc($res)){

            array_push($views,$row['num_views']);
            array_push($users,$row['email']);
        }

        $result = [
            "views" => $views,
            "users" => $users
        ];
        echo json_encode($result);
    }

?>