<?php

    set_time_limit(60*5);
    function model_connect(){

    	$servername = "dbserver";
	    $username = "grupo37";
	    // $password (No vale) = "up7AhW8aep";
        $password = "ielahWo3uk";
    	$dbname = "db_grupo37";

        // $servername = "localhost";
        // $username = "diego";
        // $password = "diego";
        // $dbname = "movmad";
    
        $conn = mysqli_connect($servername, $username, $password, $dbname);
    
        return $conn;
    }


    function model_existe_usuario($conn, $email, $name){

        $consulta = "SELECT email FROM final_usuarios WHERE email = '$email' OR username = '$name'";
        $res = mysqli_query($conn, $consulta);    
        $num_rows = mysqli_num_rows($res);
        if($num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    function model_register_user($conn){
        
        
        $username = $_POST['username'];
        $email = $_POST["email"];
        $password = $_POST["password"];
        if(!model_existe_usuario($conn, $email, $username)){
            $consulta = "INSERT INTO final_usuarios (username, email, pass, typeUser) values ('$username', '$email', '$password', 0)";
            mysqli_query($conn, $consulta);

            $consulta = "SELECT email, typeUser, idUser FROM final_usuarios WHERE email = '$email' AND pass = '$password'";
            $res = mysqli_query($conn, $consulta);    
            $response = $res->fetch_assoc();    
            $_SESSION['dateTimeStart'] = date('Y-m-d H:i:s');
            $_SESSION['idUser'] = $response['idUser'];
            $_SESSION['email'] = $email;

            return 1;
        }   
        else{
            echo "El email o nombre introducido ya ha sido usado!";
            return 0;
        }     
    }

    function model_login($conn){
	
        $email = $_POST["email"];
        $password = $_POST["password"];

        $consulta = "SELECT email, typeUser, idUser FROM final_usuarios WHERE email = '$email' AND pass = '$password'";
        $res = mysqli_query($conn, $consulta);    

        $response = $res->fetch_assoc();

        $num_rows = mysqli_num_rows($res);
        if($num_rows > 0){
            
            // session_start();
            $_SESSION['dateTimeStart'] = date('Y-m-d H:i:s');
            $_SESSION['idUser'] = $response['idUser'];
            $_SESSION['email'] = $email;

            if($response["typeUser"] == 1){
                return 2;
            }
            else{
                return 1;
            }
        }
        else{
            return 0;
        }
    }

    function model_logout($conn,$dateTimeStart,$idUser){

        $dateTimeEnd = date('Y-m-d H:i:s');
        $consulta = "INSERT INTO final_estadisticas (fechaInicioVisita, idUsuario, fechaFinVisita) values ('$dateTimeStart', '$idUser', '$dateTimeEnd')";
        $res = mysqli_query($conn, $consulta);
    }

    function model_get_api_key(){
        
        $email = 'diegopanizog@gmail.com';
        $password = 'Dragonera555!';
        $url = 'https://openapi.emtmadrid.es/v2/mobilitylabs/user/login/';

        $options = array(
            'http' => array(
                'header' => "email: $email\r\n" . 
                            "password: $password\r\n"
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
       
        $decoded_response = json_decode($response,true);
        $accessToken = $decoded_response["data"]["0"]["accessToken"];

        return $accessToken;
    }

    function model_set_parkings($accessToken, $conn){

        // $consulta = "DELETE FROM final_parkings";
        // $res = mysqli_query($conn, $consulta);

        $url = 'https://openapi.emtmadrid.es/v3/citymad/places/parkings/1/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);

        $response_arr = $decoded_response["data"][0]["data"];
        for($i = 0; $i < sizeof($response_arr); $i++){

            $id =  $decoded_response["data"][0]["data"][$i]["id"];
            $name = $decoded_response["data"][0]["data"][$i]["name"];
            $latitude = $decoded_response["data"][0]["data"][$i]["geometry"]["coordinates"][1];
            $longitude = $decoded_response["data"][0]["data"][$i]["geometry"]["coordinates"][0];
            $direccion = $decoded_response["data"][0]["data"][$i]["address"];

            $url2 = 'https://openapi.emtmadrid.es/v1/citymad/places/parking/'.$id.'/'.'1/';
            $options2 = array(
                'http' => array(
                    'header' => "accessToken: $accessToken\r\n"
                )
            );
            $context2 = stream_context_create($options2);
            $response2  = file_get_contents($url2, false, $context2);
            $decoded_response2  = json_decode($response2,true);

            if(isset($decoded_response2["data"][0]["rates"][0]["rate"])){
                $precio = floatval($decoded_response2["data"][0]["rates"][0]["rate"]);
            }
            else{
                $precio = 0;
            }
            
            if(isset($decoded_response2["data"][0]["schedule"])){
                $horario = $decoded_response2["data"][0]["schedule"];
            }
            else{
                $horario = "Desconocido";
            }
            //$consulta = "INSERT INTO final_parkings (name, address, latitude, longitude, precio, horario) values ('$name', '$direccion', '$latitude', '$longitude', '$precio', '$horario')";
            $consulta = "UPDATE final_parkings SET name = '$name', address = '$direccion', latitude = '$latitude', longitude = '$longitude', precio = '$precio', horario = '$horario' WHERE name = '$name'";

            $res = mysqli_query($conn, $consulta);
    
        }
    }

    function model_get_parkings($conn){

        $consulta = "SELECT idParking, name, address, latitude, longitude FROM final_parkings";
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
        else{
            echo "La lista de parkings no está disponible";
        }
        return $lines_to_add;
    }

    function model_get_plazas($accessToken){

        // echo $accessToken;
        
        $url = 'https://openapi.emtmadrid.es/v1/citymad/places/parkings/availability/';
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
            $fecha = substr($decoded_response["data"][$i]["lastUpd"],0,10);
            $direccion = $decoded_response["data"][$i]["address"];
            $plazas_libres = $decoded_response["data"][$i]["freeParking"];
            if($fecha == $fecha_actual && $plazas_libres > 0){        
                $lines_to_add .= '<tr class="plaza_tr_libre">'.'<td>'.$nombre.'</td><td>'.$direccion.'</td><td>'.$plazas_libres.'</td></tr>';
            }
            else if($fecha == $fecha_actual && $plazas_libres <= 0){
                $lines_to_add .= '<tr class="plaza_tr_llena">'.'<td>'.$nombre.'</td><td>'.$direccion.'</td><td>'.$plazas_libres.'</td></tr>';
            }
            else{
                $lines_to_add .= '<tr class="plaza_tr_desactualizada">'.'<td>'.$nombre.'</td><td>'.$direccion.'</td><td>'.'?'.'</td></tr>';
            }
        }
        return $lines_to_add;
    }

    function model_get_bicis($accessToken){
        $url = 'https://openapi.emtmadrid.es/v3/transport/bicimadgo/bikes/availability/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);
        $lines_to_add = "";

        $response_arr = $decoded_response["data"];

        for($i = 0; $i < sizeof($response_arr); $i++){
            $id_bici = $decoded_response["data"][$i]["DeviceName"];
            $bateria = $decoded_response["data"][$i]["porcBattery"];
            $estacion = $decoded_response["data"][$i]["bike_in_station"];
            $lines_to_add .= '<tr>'.'<td>'.$id_bici.'</td><td>'.$bateria.'</td><td>'.$estacion.'</td></tr>';
        }

        return $lines_to_add;
    }

    function model_set_estaciones($accessToken, $conn){

        $url = 'https://openapi.emtmadrid.es/v1/transport/bicipark/stations/';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);

        $response_arr = $decoded_response["data"];
        for($i = 0; $i < sizeof($response_arr); $i++){

            $id = $decoded_response["data"][$i]["stationId"];
            $name = $decoded_response["data"][$i]["stationName"];
            $direccion = $decoded_response["data"][$i]["address"];
            $consulta = "INSERT INTO final_estaciones (idEstacion, name, address) values ('$id', '$name', '$direccion')";
            $res = mysqli_query($conn, $consulta);
        }
    }

    function model_get_estaciones($accessToken, $conn){
        
        $consulta = "SELECT idEstacion, name, address FROM final_estaciones";
        $res = mysqli_query($conn, $consulta);

        $cont_rows = 0;
        $lines_to_add = "";
        if($res->num_rows > 0){
            while($cont_rows < $res->num_rows){
                $row = $res->fetch_assoc();
                $lines_to_add .= '<tr>'.'<td>'.$row["idEstacion"].'</td><td>'.$row["name"].'</td><td>'.$row["address"].'</td></tr>';
                $cont_rows = $cont_rows+1;
            }
        }
        else{
            echo "La lista de estaciones no está disponible";
        }
        return $lines_to_add;
            
    }

    function model_activar_geolocalizacion(){

        return "<script>
        
        var x = document.getElementById('textLocation');
        navigator.geolocation.getCurrentPosition(showPosition);
        
        function showPosition(position) {
          x.innerHTML = 'Latitude: ' + position.coords.latitude +
          '<br>Longitude: ' + position.coords.longitude;
        }
        
        </script>";

    }

    function model_get_users($conn){

        $consulta = "SELECT idUser, email, pass, username, typeUser FROM final_usuarios";
        $res = mysqli_query($conn, $consulta);

        $cont_rows = 0;
        $lines_to_add = "";
        if($res->num_rows > 0){
            while($cont_rows < $res->num_rows){
                $row = $res->fetch_assoc();
                if($row["typeUser"] == 1){
                    $tipo = "Admin";
                }
                else{
                    $tipo = "Normal";
                }
                $lines_to_add .= '<tr id=user'.$row["idUser"].' onclick=getRowContent('.$row["idUser"].') class="user_tr">'.'<td id=idUser'.$row["idUser"].'>'.$row["idUser"].'</td><td id=typeUser'.$row["idUser"].'>'.$tipo.'</td><td id=nameUser'.$row["idUser"].'>'.$row["username"].'</td><td id=emailUser'.$row["idUser"].'>'.$row["email"].'</td></tr>';
                $cont_rows = $cont_rows+1;
            }
        } 
        else{
            echo "La lista de parkings no está disponible";
        }
        return $lines_to_add;
    }

    function model_delete_user($conn,$idUserToDelete){

        $consulta = "DELETE FROM final_usuarios WHERE idUser=$idUserToDelete";
        $res = mysqli_query($conn, $consulta);
    }
    
    function model_add_user($conn){
        
        $username = $_POST["inputNameUser"];
        $email = $_POST["inputEmailUser"];
        $password = $_POST["inputPassUser"];
        $type = $_POST["inputTypeUser"];

        if($type === "admin"){
            $typeUser = 1;
        }

        else{
            $typeUser = 0;
        }
        if(!model_existe_usuario($conn, $email, $username)){
            $consulta = "INSERT INTO final_usuarios (username, email, pass, typeUser) values ('$username', '$email', '$password', '$typeUser')";
            mysqli_query($conn, $consulta);    
        }

        else{
            echo "El nombre o email introducido ya ha sido usado!";
        }
    }

    function model_get_num_normal_users($conn){
        
        $consulta = "SELECT idUser FROM final_usuarios WHERE typeUser = 0";
        $res = mysqli_query($conn, $consulta);    
        $num_rows = mysqli_num_rows($res);
        return $num_rows;

    }

    function model_get_num_admin_users($conn){

        $consulta = "SELECT email, typeUser, idUser FROM final_usuarios WHERE typeUser = 1";
        $res = mysqli_query($conn, $consulta);    
        $num_rows = mysqli_num_rows($res);
        return $num_rows;
    }

    function model_plot_num_users($num_normales,$num_admins){

        return "<script type='text/javascript'>

            var data = [{
                values: ['$num_normales', '$num_admins'],
                labels: ['Admins', 'Normal'],
                type: 'pie'
            }];
            
            var layout = {
                height: 400,
                width: 500
            };
            
            Plotly.newPlot('divNumUsers', data, layout);
                
        </script>";

    }

    function model_get_num_views($conn){

        $consulta = "SELECT COUNT(idUsuario) num_views FROM final_estadisticas GROUP BY idUsuario";
        $res = mysqli_query($conn, $consulta);    
        $cont = 0;
        $views = '[';
        while($row = mysqli_fetch_assoc($res)){

            $views .= $row['num_views'];
            if($cont < $res->num_rows-1){
                $views .= ',';
            }
            $cont = $cont+1;
        }
        $views .= ']';

        return $views;
    }

    function model_get_users_views($conn){
        
        $consulta = "SELECT DISTINCT idUsuario FROM final_estadisticas";
        $res = mysqli_query($conn, $consulta);        
        $cont = 0;
        $users = '[';
        while($row = mysqli_fetch_assoc($res)){
            
            $users .= (string)$row['idUsuario'];
            if($cont < $res->num_rows-1){
                $users .= ',';
            }
            $cont = $cont+1;
        }
        $users .= ']';
        return $users;
    }

    function model_plot_num_visits($views, $users){

        return "<script type='text/javascript'>

            var data = [{
                x: $users,
                y: $views,
                type: 'bar'
            }];
                    
            Plotly.newPlot('divNumViews', data);
            
        </script>";
    }

    function model_plot_statistics($conn){

        $num_admins = model_get_num_admin_users($conn);
        $num_normales = model_get_num_normal_users($conn);
        $lines_to_add = model_plot_num_users($num_normales,$num_admins);
        $lines_to_add = "";
        $views = model_get_num_views($conn);
        $users = model_get_users_views($conn);
        $lines_to_add .= model_plot_num_visits($views, $users);

        return $lines_to_add;
    }

    function model_set_paradas($accessToken, $conn){
        $url = 'https://openapi.emtmadrid.es/v1/transport/busemtmad/stops/list/';
        $params = [
            'accessToken' => $accessToken,
        ];
        $postParams = array(
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded'."\r\n".'accessToken: '.$params['accessToken']."\r\n",
                'content' => http_build_query($params),
            ]
        );
        $context = stream_context_create($postParams);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);
        
        $response_arr = $decoded_response["data"];  
        for($i = 0; $i < sizeof($response_arr); $i++){

            $id = $decoded_response["data"][$i]["node"];

            $latitude = $decoded_response["data"][$i]["geometry"]["coordinates"][1];
            $longitude = $decoded_response["data"][$i]["geometry"]["coordinates"][0];
            $wifi = $decoded_response["data"][$i]["wifi"];
            $name = $decoded_response["data"][$i]["name"];
            $name = str_replace('\'', '', $name);
            $consulta = "INSERT INTO final_paradas (idParada, latitud, longitud, wifi, nombre) values ('$id', '$latitude', '$longitude', '$wifi', '$name')";
            $res = mysqli_query($conn, $consulta);
            // echo $id." ".$latitude." ".$longitude." ".$wifi." ".$name.'<br>';
            
        }
    }

    function model_set_lineas($accessToken, $conn){

        $url = 'https://openapi.emtmadrid.es/v2/transport/busemtmad/lines/info/20230503';
        $options = array(
            'http' => array(
                'header' => "accessToken: $accessToken\r\n"
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        $decoded_response = json_decode($response,true);
        
        $response_arr = $decoded_response["data"];  
        for($i = 0; $i < sizeof($response_arr); $i++){

            $id = $decoded_response["data"][$i]["line"];
            $nameA = $decoded_response["data"][$i]["nameA"];
            $nameB = $decoded_response["data"][$i]["nameB"];
            $nameA = str_replace('\'', '', $nameA);
            $nameB = str_replace('\'', '', $nameB);
            $group = $decoded_response["data"][$i]["group"];

            $consulta = "INSERT INTO final_lineas (idLinea, nameA, nameB, grupo) values ('$id', '$nameA', '$nameB', '$group')";
            $res = mysqli_query($conn, $consulta);
            
        }
    }


?>
















