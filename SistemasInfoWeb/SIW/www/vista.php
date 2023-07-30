<?php

    function vista_register(){

        $vista = file_get_contents("./paginas/registro.html");
        echo $vista;
    }

    function vista_login(){

        $vista = file_get_contents("./paginas/login.html");
        echo $vista;
    }

    function vista_landing(){

        $vista = file_get_contents("./paginas/landing.html");
        
        
        if(isset($_SESSION['email'])){
            echo "Bienvenid@: ".$_SESSION['email'];
            $vista = str_replace('##LOGOUT##','<li class="nav-item"><a class="nav-link" href="index.php?action=logout">Cerrar Sesión</a></li>',$vista);
            $vista = str_replace('##REGISTRO##','',$vista);
            $vista = str_replace('##LOGIN##','',$vista);
            if(isset($_SESSION['admin'])){
                $vista = str_replace('##ADMIN##','<li class="nav-item"><a class="nav-link" href="index.php?action=admin">Admin Workspace</a></li>',$vista);
            }
            else{
                $vista = str_replace('##ADMIN##','',$vista);
            }
        }

        else{
            $vista = file_get_contents("./paginas/landing.html");
            $vista = str_replace('##LOGOUT##','',$vista);
            $vista = str_replace('##REGISTRO##','<li class="nav-item"><a class="nav-link" href="index.php?action=register&key=1">Registrate!</a></li>',$vista);
            $vista = str_replace('##LOGIN##','<li class="nav-item"><a class="nav-link" href="index.php?action=login&key=1">Haz login!</a></li>',$vista);
            $vista = str_replace('##ADMIN##','',$vista);
        }

        echo $vista;
    }

    function vista_lista_parkings($lines_to_add){

        // $vista = file_get_contents("./parking.html");
        
        $vista = file_get_contents("./paginas/parking.html");

        $vista_actualizada = str_replace("##ELEMS_PARKING##",$lines_to_add,$vista);
        $vista_actualizada = str_replace('##GEOLOCALIZACION##','<script type="text/javascript" src="./js/search_location.js"></script>',$vista_actualizada);        
        echo $vista_actualizada;
    }

    function vista_lista_plazas($lines_to_add,$lines_to_add1,$lines_to_add2){

        $vista = file_get_contents("./paginas/plazas.html");
        $vista_actualizada = str_replace("##ELEMS_PLAZAS##",$lines_to_add,$vista);
        $vista_actualizada = str_replace("##LISTA_BICIS##",$lines_to_add1,$vista_actualizada);
        $vista_actualizada = str_replace("##LISTA_ESTACIONES##",$lines_to_add2,$vista_actualizada);

        echo $vista_actualizada;
    }

    function vista_lista_users($lines_to_add){
        $vista = file_get_contents("./paginas/users.html");
        $vista_actualizada = str_replace("##ELEMS_USERS##",$lines_to_add,$vista);
        $vista_actualizada = str_replace('##SCRIPT##','<script type="text/javascript" src="./js/admin_user_operations.js"></script>',$vista_actualizada);
        echo $vista_actualizada;
    }

    function vista_admin_workspace(){

        $vista = file_get_contents("./paginas/admin.html");
        // $vista_actualizada = str_replace('##ESTADISTICAS##','<script type="text/javascript" src="./js/admin_statistics.js"></script>',$vista);        
        //echo $lines_to_add;
        //$vista_actualizada = str_replace('##ESTADISTICAS##',$lines_to_add,$vista);        

        echo $vista;
    }

    function vista_buses(){

        $vista = file_get_contents("./paginas/buses.html");
        echo $vista;
    }

?>