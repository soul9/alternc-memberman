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

		if ( !empty($error) ) {
			echo '<p class="alert alert-danger">' , $error, '</p>';
		}

		function viderArray($fields) {
			$fields = array (
				"login"		=> "",
				"typeAP"	=> "",
				"nom"		=> "",
				"prenom"	=> "",
				"email"		=> "",
				"forfait"	=> "",
				"mdpA"		=> "",
				"nomAsso"	=> "",
				"categorie"	=> "",
				"activite"	=> "",
				"statut"	=> "",
			);
			return $fields;
		}
		
		?>
		<form method="post" action="statuer_membre.php">
		
		<?php

		$result = mysql_query("SELECT MAX(uid) AS max_uid FROM memberman");
		$row = mysql_fetch_array($result);
		$result2 = mysql_query("SELECT MIN(uid) AS min_uid FROM memberman");
		$row2 = mysql_fetch_array($result);

		?>

		<h3><?php __("AlternC account list"); ?></h3>
		<hr id="topbar"/>
		<br />

		<table border='true' cellspacing='1' cellpadding='7' width=100%>
		<tr>
			<th width=13%>Login</th>
			<th width=8%>Nom</th>
			<th width=10%>Prénom</th>
			<th width=9%>Email</th>
			<th width=9%>Forfait</th>
			<th width=9%>Nom Asso</th>
			<th width=10%>Taille Asso</th>
			<th width=12%>Activité Asso</th>
			<th width=15%>Statut</th>
		</tr>

		<?php

		$fields = array();
		for($i=2001; $i<=$row["max_uid"]; $i++) {
			$db->query("SELECT * FROM memberman WHERE uid='$i'");
			if ($db->num_rows()) {
				$fields = viderArray($fields);
				while ($db->next_record()) {
					$fields[$db->Record["clef"]] = $db->Record["valeur"];
				}
				if($fields['typeAP'] == "personne")
					echo "<tr class='lst2'>";
				else if($fields['typeAP'] == "association")
					echo "<tr class='lst1'>";
				foreach($fields as $clef => $valeur) {
					if($clef != "typeAP") {
						if($clef == "login") {
							?>
<td><input type="checkbox" name="membre[]" id="user_<?php echo $i; ?>" value="<?php echo $i; ?>" />
							<?php
							echo "<a href='modifier_membre.php?uid=$i'>$valeur</a></td>";
						}
						else if($clef == "statut" && $valeur == "En attente") {
							echo "<td><img src='/images/orange.png' />" . " " . $valeur . "</td>";
						}
						else if($clef == "statut" && $valeur == "Activé") {
							echo "<td><img src='/images/vert.png' />" . "   " . $valeur . "</td>";
						}
						else if($clef == "statut" && $valeur == "Désactivé") {
							echo "<td><img src='/images/rouge.png' />" . "   " . $valeur . "</td>";
						}
						else if($clef != "mdp" && $clef != "mdpA" && $clef != "login") {
							echo "<td>" . $valeur . "</td>";
						}
					}
				}
				echo "</tr>";
			}
		}
		echo "</table>";
		?>
		<br />
		<input type="submit" class="inb delete" name="submitDel" value="Delete" />
		<input type="submit" class="inb act" name="submitAct" value="Activate" />
		<input type="submit" class="inb att" name="submitAtt" value="Put in pending" />
		<input type="submit" class="inb desact" name="submitDes" value="Desactivate" />
		</form>
	</body>
</html>
