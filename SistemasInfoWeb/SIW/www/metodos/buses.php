<?php

    error_reporting(E_ALL);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    set_time_limit(2*60);

    include "../modelo.php";

    if (isset($_GET["action"])) {
        $action = $_GET["action"];
    } else {
        $action = "calendario";
    }

    $today = date("Ymd");
    $accessToken = model_get_api_key();

    if($action === "calendario"){

        $start_date = date("Ym01");
        $month = date("m");
        $end_date = date("Ymd", mktime(0, 0, 0, $month+1,0,date("Y")));
        $url = "https://openapi.emtmadrid.es/v1/transport/busemtmad/calendar/"."$start_date".'/'."$end_date".'/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);
        
        $response_arr = $decoded_response["data"];  
        
        $result = "";
        for($i = 0; $i < sizeof($response_arr); $i++){

            $date = $decoded_response["data"][$i]["date"];
            $date = explode('/',$date);
            $dayType = $decoded_response["data"][$i]["dayType"];

            $result .= '<li class='.$dayType.'>'.$date[0].'</li>';
            
        }
        echo $result;
        
    }

    else if($action === "lineas"){

                
        $conn =  model_connect();
        $consulta = "SELECT * FROM final_lineas";
        $res = mysqli_query($conn, $consulta);    

        $response = $res->fetch_assoc();

        $result = "<div class=container>";
        $num_rows = mysqli_num_rows($res);
        $cont_rows = 0;

        while($num_rows > $cont_rows){
            $result .= '<div class=linea onclick='."obtener_paradas(".$response["idLinea"].")"."><h1>".$response["idLinea"]."</h1></div>";
            $cont_rows = $cont_rows+1;
            $response = $res->fetch_assoc();

        }
        $result .= "</div></div>";

        echo $result;
    }

    else if($action === "paradas"){

        if (isset($_GET["lineId"])) {
            $lineId = $_GET["lineId"];
        } else {
            $lineId = 0;
        }    

        $direction = 1;
        $url = "https://openapi.emtmadrid.es/v1/transport/busemtmad/lines/"."$lineId".'/'.'stops/'."$direction".'/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $decoded_response = json_decode($response,true);
        $response_arr = $decoded_response["data"];  
        

        $result = "<div class=container><div class=row>"; #Creo el contenedor
        $result .= "<div class=col>"; #Creo la primera columna para las paradas de ida

        if(isset($decoded_response["data"][0]["timeTable"][0]["typeOfDays"])){

            $start_time = $decoded_response["data"][0]["timeTable"][0]["typeOfDays"][0]["Direction1"]["StartTime"];
            $stop_time = $decoded_response["data"][0]["timeTable"][0]["typeOfDays"][0]["Direction1"]["StopTime"];
            $frequency = $decoded_response["data"][0]["timeTable"][0]["typeOfDays"][0]["Direction1"]["MinimunFrequency"];
            $result .= "<div class=row><div class=cabeceraParada><h1>Recorrido de ida</h1><p>Hora de inicio: ".$start_time."</br>Hora de fin: ".$stop_time."</br>Frecuencia de paso: ".$frequency." mins.</p></div></div>";    
        }
        else{
            $result .= "<div class=row><div class=cabeceraParada><h1>Recorrido de ida</h1><p>Hora de inicio desconocida</br>Hora de fin desconocida</br>Frecuencia de paso desconocida</p></div></div>";
        }


       
        for($i = 0; $i < sizeof($response_arr); $i++){ 

            $stops = $decoded_response["data"][$i]["stops"];

            for($j = 0; $j < sizeof($stops); $j++){

                $stop = $stops[$j]["stop"];
                $name = $decoded_response["data"][$i]["stops"][$j]["name"];

                $url = "https://openapi.emtmadrid.es/v2/transport/busemtmad/stops/"."$stop".'/'."arrives/"."$lineId".'/';
                $data = array(
                    'stopId' => $stop,
                    'lineArrive' => $lineId,
                    'cultureInfo' => 'ES',
                    'Text_StopRequired_YN' => 'Y',
                    'Text_EstimationsRequired_YN' => 'Y',
                    'Text_IncidencesRequired_YN' => 'Y',
                    'DateTime_Referenced_Incidencies_YYYYMMDD' => $today
                );
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n" .
                                     "accessToken: $accessToken\r\n",
                        'method'  => 'POST',
                        'content' => json_encode($data)
                    )
                );
                $context_tiempo_llegada  = stream_context_create($options);
                $result_tiempo_llegada = file_get_contents($url, false, $context_tiempo_llegada);
                // echo '<pre>'.$result_tiempo_llegada.'</pre></br>';
                $decoded_info = json_decode($result_tiempo_llegada,true);

                if($decoded_info["code"] === "00" && isset($decoded_info["data"][0]["Arrive"][0]["estimateArrive"])){
                    $tiempo_llegada = $decoded_info["data"][0]["Arrive"][0]["estimateArrive"];
                    $tiempo_llegada = intval($tiempo_llegada/60);
                    $result .= '<div class=row><div class=parada><h1>'.$stop.'</h1><p>'.$name.'</p><p>Tiempo de llegada: '.$tiempo_llegada.' minutos</p></div></div>'; #Añado una fila
                }
                else{
                    $result .= '<div class=row><div class=parada><h1>'.$stop.'</h1><p>'.$name.'</p><p>Tiempo de llegada desconocido</p></div></div>'; #Añado una fila
                }
                // echo $tiempo_llegada;
            }
            
        }

        $result .= "</div>"; #Cierro la columna
        
        
        $direction = 2;
        $url = "https://openapi.emtmadrid.es/v1/transport/busemtmad/lines/"."$lineId".'/'.'stops/'."$direction".'/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $decoded_response = json_decode($response,true);
        $response_arr = $decoded_response["data"];
        

        $result .= "<div class=col>"; #Creo la segunda columna para las paradas de vuelta
        
        $start_time = $decoded_response["data"][0]["timeTable"][0]["typeOfDays"][0]["Direction1"]["StartTime"];
        $stop_time = $decoded_response["data"][0]["timeTable"][0]["typeOfDays"][0]["Direction1"]["StopTime"];
        $frequency = $decoded_response["data"][0]["timeTable"][0]["typeOfDays"][0]["Direction1"]["MinimunFrequency"];
        $result .= "<div class=row><div class=cabeceraParada><h1>Recorrido de vuelta</h1><p>Hora de inicio: ".$start_time."</br>Hora de fin: ".$stop_time."</br>Frecuencia de paso: ".$frequency." mins.</p></div></div>";

        for($i = 0; $i < sizeof($response_arr); $i++){ 

            $stops = $decoded_response["data"][$i]["stops"];

            for($j = 0; $j < sizeof($stops); $j++){
                
                $stop = $stops[$j]["stop"];
                $name = $decoded_response["data"][$i]["stops"][$j]["name"];

                $url = "https://openapi.emtmadrid.es/v2/transport/busemtmad/stops/"."$stop".'/'."arrives/"."$lineId".'/';
                $data = array(
                    'stopId' => $stop,
                    'lineArrive' => $lineId,
                    'cultureInfo' => 'ES',
                    'Text_StopRequired_YN' => 'Y',
                    'Text_EstimationsRequired_YN' => 'Y',
                    'Text_IncidencesRequired_YN' => 'Y',
                    'DateTime_Referenced_Incidencies_YYYYMMDD' => $today
                );
                
                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n" .
                                     "accessToken: $accessToken\r\n",
                        'method'  => 'POST',
                        'content' => json_encode($data)
                    )
                );

                $context_tiempo_llegada  = stream_context_create($options);
                $result_tiempo_llegada = file_get_contents($url, false, $context_tiempo_llegada);
                $decoded_info = json_decode($result_tiempo_llegada,true);

                if($decoded_info["code"] === "00" && isset($decoded_info["data"][0]["Arrive"][0]["estimateArrive"])){
                    $tiempo_llegada = $decoded_info["data"][0]["Arrive"][0]["estimateArrive"];
                    $tiempo_llegada = intval($tiempo_llegada/60);
                    
                    $result .= '<div class=row><div class=parada><h1>'.$stop.'</h1><p>'.$name.'</p><p>Tiempo de llegada: '.$tiempo_llegada.' minutos</p></div></div>'; #Añado una fila
                }
                else{
                    $result .= '<div class=row><div class=parada><h1>'.$stop.'</h1><p>'.$name.'</p><p>Tiempo de llegada desconocido</p></div></div>'; #Añado una fila
                }
            }
            
        }

        $result .= "</div>"; #Cierro la columna


        $result .= "</div></div>"; #Cierro el contenedor
        
        
        echo $result;

    }

    else{

        echo "Ha ocurrido un error al recuperar la información";
    }

?>