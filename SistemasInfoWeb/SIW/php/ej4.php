<?php 
		
	$tabla = ['Num Fila','Ej','Total'];
	$aux = 0;
		
	for($i = 0; $i < 50; $i++){
		
		$aux = $aux + $i;
		$tabla[$i] = ['Num Fila' => $i, 'Ej' => "Ej 3", 'Total' => $aux];
				
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
				<th> Total </th>
				
			</tr>
		
			<?php 
			
				$res = "";
				foreach($tabla as $item){
					
					$res .= '<tr>';
					$res .='<td>'.$item['Num Fila'].'</td>';
					$res .='<td>'.$item['Ej'].'</td>';
					$res .='<td>'.$item['Total'].'</td>';
					$res .= '</tr>';				
				}
				
				echo $res;
			?>
		
		</table>
		
	</body>

</html>
