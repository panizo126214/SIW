<?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);

    set_time_limit(60*60*24);

    include "../modelo.php";

    $conn = model_connect();

    $accessToken = model_get_api_key();
    // echo $accessToken;

    $acentos = array(
        'á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ',
        'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'
    );
    $sin_acentos = array(
        'a', 'e', 'i', 'o', 'u', 'u', 'n',
        'A', 'E', 'I', 'O', 'U', 'U', 'N'
    );

    $completed = false;
    $cont = 0;
    while($cont < 10){ #Hasta no haber recogido datos de 5h no paro


        $url = 'https://openapi.emtmadrid.es/v4/citymad/places/parkings/availability/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);
        $lines_to_add = "";

        $fecha_actual = date("Y-m-d",time());
        $response_arr = $decoded_response["data"];
        for($i = 0; $i < sizeof($response_arr); $i++){

            $nombre = $decoded_response["data"][$i]["name"];
            $timestamp = date('Y-m-d H:i:s');
            $plazas_libres = $decoded_response["data"][$i]["freeParking"];
            $total_plazas = $decoded_response["data"][$i]["parkingSpaces"];
            
            if($total_plazas == null || $total_plazas == ""){
                $total_plazas = 0;
            }
            
            if($plazas_libres == null || $plazas_libres == ""){
                $plazas_libres = 0;
            }

            $consulta = "SELECT idParking, name FROM final_parkings";
            $res = mysqli_query($conn, $consulta);
            while($row = $res->fetch_assoc()){
                $name = str_replace($acentos, $sin_acentos, $row['name']);
                $nombre = str_replace($acentos, $sin_acentos, $nombre);
                
                if($nombre == $name){
                    $idParking = $row['idParking'];
                    sleep(1);
                    $consulta = "INSERT INTO final_estadisticas_disponibilidad (idParking, disponibilidad, timestamp, totalPlazas) values ('$idParking', '$plazas_libres', '$timestamp', '$total_plazas')";
                    $res = mysqli_query($conn, $consulta); 
                    break;
                }
            }
           
        }
        $cont = $cont+1;
        sleep(60*10);
    }

?>