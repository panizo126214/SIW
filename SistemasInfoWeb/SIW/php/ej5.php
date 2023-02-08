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
			
				$color1 = "red";
				$color2 = "gray";
				$num_row = 0;
				
				$res = "";
				foreach($tabla as $item){
					
					if($num_row%2 == 0){
						
						$res .= '<tr>';
						$res .='<td bgcolor = '.$color1.'>'.$item['Num Fila'].'</td>';
						$res .='<td bgcolor = '.$color1.'>'.$item['Ej'].'</td>';
						$res .='<td bgcolor = '.$color1.'>'.$item['Total'].'</td>';
						$res .= '</tr>';
					}
					
					else{
						
						$res .= '<tr>';
						$res .='<td bgcolor = '.$color2.'>'.$item['Num Fila'].'</td>';
						$res .='<td bgcolor = '.$color2.'>'.$item['Ej'].'</td>';
						$res .='<td bgcolor = '.$color2.'>'.$item['Total'].'</td>';
						$res .= '</tr>';
					}
					
					$num_row = $num_row + 1;				
				}
				
				echo $res;
			?>
		
		</table>
		
	</body>

</html>
