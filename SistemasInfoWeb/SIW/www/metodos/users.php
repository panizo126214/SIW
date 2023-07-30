<?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    include "../modelo.php";

    if (isset($_GET["action"])) {
        $action = $_GET["action"];
    } else {
        $action = "pass";
    }

    if($action === "modUser"){

        if (isset( $_POST["id"])) {
            $idUser =  $_POST["id"];
        } else {
            $idUser = -1;
        }
        if (isset( $_POST["emailUser"])) {
            $emailUser =  $_POST["emailUser"];
        } else {
            $emailUser = "";
        }
        if (isset( $_POST["passUser"])) {
            $passUser =  $_POST["passUser"];
        } else {
            $passUser = "";
        }
        if (isset( $_POST["nameUser"])) {
            $nameUser =  $_POST["nameUser"];
        } else {
            $nameUser = "";
        }
        if (isset( $_POST["typeUser"])) {
            if( $_POST["typeUser"] === "admin"){
                $typeUser = 1;
            }
            else{
                $typeUser = 0;
            }
        } else {
            $typeUser = "";
        }

        $conn = model_connect();

        $consulta = "UPDATE final_usuarios SET email='$emailUser', pass='$passUser', username='$nameUser', typeUser='$typeUser' WHERE idUser='$idUser'";

        $res = mysqli_query($conn, $consulta);
        echo $res;
    }

    else if($action === "getRowContent"){

        echo "";
    }

    else{
        echo "Error retrieving data";
    }

?>