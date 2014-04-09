<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
	</head>
	<body>
	<?php
	require_once("../class/config.php");
	include_once("head.php");

	if (!$admin->enabled) {
		__("This page is restricted to authorized staff");
		exit();
	}
	
	$json = file_get_contents("champs.json");
	$parsed_json = json_decode($json);

	echo "<form method='post' action='modifier_formulaire.php' id='form' >";
	echo "<table border='true' cellspacing='1' cellpadding='5' width=80%>";
	for($i=0; $i<=9; $i++) {
		if($parsed_json->{'champs'}[$i]->{'type'} != "radio") {
			echo "<tr><th>" . $parsed_json->{'champs'}[$i]->{'text'} . "</th><td><input type='" . $parsed_json->{'champs'}[$i]->{'type'} . "' name='" . $parsed_json->{'champs'}[$i]->{'name'} . "' valeur='$" . $parsed_json->{'champs'}[$i]->{'name'} . "'/></td></tr><br/><br/>";
		}
		else {
			echo "<tr><th>" . $parsed_json->{'champs'}[$i]->{'text'} . "</th><td>";
			for($j=0; $j<=2; $j++) {
				echo "<input type='" . $parsed_json->{'champs'}[$i]->{'type'} . "' name='" . $parsed_json->{'champs'}[$i]->{'name'} . "' valeur='$" . $parsed_json->{'champs'}[$i]->{'name'} . "'/>" . $parsed_json->{'champs'}[$i]->{'radio_opts'}[$j]->{'text'} . "<br/>";
			}
			echo"</td></tr><br/><br/>";
		}
	}
	echo "</table>";
	echo "</form>";

	?>
	
	<?php
	
	/*
	echo "<table border='true'>";
	
	$db->query("SELECT * FROM memberman WHERE uid='0'");
	while ($db->next_record()) {
		//echo $db->Record["clef"] . " / " . $db->Record["valeur"] . " / " . $db->Record["uid"] . " / " . $db->Record["type"] . " / " . $db->Record["obligatoire"] . "<br/><br/>";
		echo "	<tr>
			<th> " . $db->Record['valeur'] . "</th>
			<td><input type='text' name=" . $db->Record["clef"] . "/></td>
			</tr>
		";
	}

	echo "</table>";
	*/

	/*
	// Les délimiteurs peuvent être des tirets, points ou slash
	$date = "04/30/1973";
	list($month, $day, $year) = split('[/.-]', $date);
	echo "Mois : $month; Jour : $day; Année : $year<br />\n";
	*/


	?>
	</body>
</html>
