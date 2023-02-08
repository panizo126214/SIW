

<?php 
		
	$tabla = ['Num Fila','ej'];
			
	for($i = 0; $i < 50; $i++){
				
		$tabla[$i] = ['Num Fila' => $i, 'ej' => "Ej 3",];
				
	}
		
?>

<html>

	<head>
		<title>Ejercicio 3 php</title>
	</head>
	
	<body>
		
		<table border = "1">
		
			<tr>
			
				<th> Número de fila </th>
				<th> Ejercicio </th>
			
			</tr>
		
			<?php 
			
				$res = "";
				foreach($tabla as $item){
					
					$res .= '<tr>';
					$res .='<td>'.$item['Num Fila'].'</td>';
					$res .='<td>'.$item['ej'].'</td>';
					$res .= '</tr>';				
				}
				
				echo $res;
			?>
		
		</table>
		
	</body>

</html>
