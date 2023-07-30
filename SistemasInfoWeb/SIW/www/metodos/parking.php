<?php 

    include '../modelo.php';
    
    session_start();

    if(isset($_GET['action'])){
        $action = $_GET['action'];
    }
    else{
        $action = "stats";
    }

    if($action === "stats"){

        if(isset($_GET["parking"])){
            $parking = $_GET["parking"];
        }
        else{
            $parking = -1;
        }

        $conn =  model_connect();
        $consulta = "SELECT * FROM final_estadisticas_disponibilidad INNER JOIN final_parkings ON final_estadisticas_disponibilidad.idParking=final_parkings.idParking WHERE final_parkings.idParking=".$parking;
        $res = mysqli_query($conn, $consulta);    

        $num_rows = mysqli_num_rows($res);
        $cont_rows = 0;

        $response_array = array();
        $disponibilidad_array = array();
        $ts_array = array();
        $total_plazas = 0;

        while($num_rows > $cont_rows){
            $cont_rows = $cont_rows+1;
            $response = $res->fetch_assoc();

            array_push($disponibilidad_array,intval($response['disponibilidad']));
            array_push($ts_array,$response['timestamp']);
        }
        $total_plazas = $response['totalPlazas'];
        $precio = $response['precio'];
        $horario = $response['horario'];
        array_push($response_array,$disponibilidad_array);
        array_push($response_array,$ts_array);
        array_push($response_array,$total_plazas);
        array_push($response_array,$precio);
        array_push($response_array,$horario);
        echo json_encode($response_array);
    }

    else if($action === "csv_download"){
        if(isset($_SESSION['email'])){

            $conn = model_connect();
            $consulta = "SELECT * FROM final_parkings";
            $result = $conn->query($consulta);
            
            $num_rows = mysqli_num_rows($result);
    
            $data = array();
            $headers = array();
            
            array_push($headers,"idParking","address","latitude","longitude","name"); #Creo las cabeceras del csv
            array_push($data,$headers); #Añado las cabeceras del csv
            
            while($row = $result->fetch_assoc()){
                $values = array();            
                array_push($values,$row["idParking"],$row["address"],$row["latitude"],$row["longitude"],$row["name"]);
                array_push($data,$values);
            }
            
            echo json_encode($data);    
        }
        else{
            echo json_encode(-1);
        }

    }


    else if($action === "search_parkings"){

        $conn = model_connect();
        $parking_name = $_GET['parkingName'];
        

        $consulta = 'SELECT idParking, name, address, latitude, longitude FROM final_parkings WHERE name LIKE "%'.strval($parking_name).'%"';
        $res = mysqli_query($conn, $consulta);

        $cont_rows = 0;
        $lines_to_add = "";
        if($res->num_rows > 0){
            while($cont_rows < $res->num_rows){
                $row = $res->fetch_assoc();
                $lines_to_add .= '<tr class="parking_tr" id="'.$row["idParking"].'">'.'<td>'.$row["name"].'</td><td>'.$row["address"].'</td><td style="display:none;">'.$row["latitude"].'</td><td style="display:none;">'.$row["longitude"].'</td></tr>';
                $cont_rows = $cont_rows+1;
            }
        }

        echo $lines_to_add;

    }

    else{
        echo "Algo ha ido mal al intentar recuperar los datos.";
    }

?>