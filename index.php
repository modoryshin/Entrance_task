<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css"/>
	<title>Test Table</title>
</head>
<body>
	<div class="content">
		<?php
		//Класс для работы с данными
		class Contestant{
			public $id, $name, $city, $car, $results, $sum;

			function __construct($id, $name, $city, $car){
				$this->id = $id;
				$this->name = $name;
				$this->city = $city;
				$this->car = $car;
				$this->results = [];
			}
		}

		//Функция вывода разметки таблицы
		function MakeTable($arr){
			echo '<table>';
			echo '<tr><th>ФИО</th><th>Город</th><th>Машина</th>';
			for($i=0; $i<(count($arr[0]->results)); $i++){
				echo '<th>Результат '.($i+1).'</th>';
			}
			echo '<th>Сумма результатов</th></tr>';
			foreach($arr as $item){
				echo '<tr>';
				echo '<td>'.$item->name.'</td>'.'<td>'.$item->city.'</td>'.'<td>'.$item->car.'</td>';
				foreach($item->results as $elem){
					echo '<td>'.($elem).'</td>';
				}
				echo '<td>'.$item->sum.'</td>';
				echo '</tr>';
			}
			echo '</table>';
		}

		//Функция получения суммарного значения очков по всем попыткам
		function GetSum($arr){
			foreach($arr as $item){
				foreach($item->results as $val){
					$item->sum = $item->sum + $val;
				}
			}
			return $arr;
		}

		//Сортировка списка участников по суммарному значению очков
		function SortArrayBySum($arr){
			for($i = 0; $i < count($arr); $i++){
				for($j = 0; $j < count($arr)-1; $j++){
					if($arr[$j]->sum < $arr[$j+1]->sum){
						$temp = $arr[$j+1];
						$arr[$j+1] = $arr[$j];
						$arr[$j] = $temp;
					}
				}
			}
			return $arr;
		}

		//Сортировка списка участников по значениям конкретного заезда
		function SortArrayByResult($arr, $param){
			for($i = 0; $i < count($arr); $i++){
				for($j = 0; $j < count($arr)-1; $j++){
					if($arr[$j]->results[$param-1] < $arr[$j+1]->results[$param-1]){
						$temp = $arr[$j+1];
						$arr[$j+1] = $arr[$j];
						$arr[$j] = $temp;
					}
				}
			}
			return $arr;
		}

		//Получение числа заездов и создание выпдающего списка для выбора метода сортировки
		function GetDropDownList($arr){
			for($i=0;$i<count($arr[0]->results);$i++){
				echo '<option value="'.($i+1).'">'.($i+1).'</option>';
			}
			echo '<option value="all">Все заезды</option>';
		}

		$contestants = [];
		$file = file_get_contents("data/data_cars.json");
		$data = json_decode($file, true);
		foreach($data as $key=>$value){
			$contestant = new Contestant($data[$key]["id"], $data[$key]["name"], $data[$key]["city"], $data[$key]["car"]);
			$contestants[] = $contestant;
		}

		$file = file_get_contents("data/data_attempts.json");
		$data = json_decode($file, true);
		foreach($data as $key=>$value){
			foreach($contestants as $item){
				if($data[$key]["id"] == $item->id){
					$item->results[] = $data[$key]["result"];
					continue;
				}
			}
		}
		?>

		<p class="labelHeader">По какому заезду подвести итоги:</p>
		<form method="post" action="index.php">
			<select id="resNumber" name="resNumber"><?php GetDropDownList($contestants) ?></select>
			<input type="submit" value="Загрузить" class="loadButton"/>
		</form>

		<?php
		error_reporting(E_ALL ^ E_WARNING);
		GetSum($contestants);
		$selectOption = $_POST['resNumber'];
		if($selectOption){
			if($selectOption == "all"){
				$contestants = SortArrayBySum($contestants);
				MakeTable($contestants);
			}
			else{
				$param = intval($selectOption);
				$contestants = SortArrayByResult($contestants, $param);
				MakeTable($contestants);
			}
		}

		?>
	</div>
</body>
</html>