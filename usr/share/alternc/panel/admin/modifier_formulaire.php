<html>
	<head>
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
		<script type="text/javascript">
		function showRadio() {
			if(document.getElementById('association').checked == true) {
				document.getElementById('formcacheA').style.display = "block";
				document.getElementById('formcacheP').style.display = "none";
			}
			else if(document.getElementById('personne').checked == true) {
				document.getElementById('formcacheP').style.display = "block";
				document.getElementById('formcacheA').style.display = "none";
			}
		  }
		</script>
		<script type="text/javascript">
		function surligne(champ, erreur) { // simple fonction de style
			if(erreur)
				champ.style.backgroundColor = "#fba";
			else
				champ.style.backgroundColor = "";
		}
		function veriftext(champ) { // fonction vérifiant les champs : login, nom, prenom etc.
			if(champ.value == "") {
				surligne(champ, true);
				return false;
			}
			else {
				surligne(champ, false);
				return true;
			}
		}
		function verifemail(champ) { // fonction vérifiant les champs : email
			var regex = /^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
			if(!regex.test(champ.value)) {
				surligne(champ, true);
				return false;
			}
			else {
				surligne(champ, false);
				return true;
			}
		}

		function verifForm(f) { // vérifie que toutes les fonctions (veriftext, verifemail etc.) renvoient true avant d'envoyer le formulaire
			
		}
		</script>
	</head>
	<body onLoad="showRadio();">
	<?php
	require_once("../class/config.php");
	include_once("head.php");
	if (!$admin->enabled) {
		__("This page is restricted to authorized staff");
		exit();
	}

	$typeAP = $_POST['typeAP'];
	
	$json = file_get_contents("champs.json");
	$parsed_json = json_decode($json);
	
	?>

	<h3><?php __("New AlternC account"); ?></h3>
	<hr id="topbar"/>
	<br />

	<?php
	$taille = count($parsed_json->{'champs'});
	$cptPers = $cptAsso = $cptAny = 0;
	for($i=0; $i<$taille; $i++) {
		if($parsed_json->{'champs'}[$i]->{'class'} == "any") {
			$cptAny++;
		}
	}
	for($i=0; $i<$taille; $i++) {
		if($parsed_json->{'champs'}[$i]->{'class'} == "pers") {
			$cptPers++;
		}
	}
	for($i=0; $i<$taille; $i++) {
		if($parsed_json->{'champs'}[$i]->{'class'} == "asso") {
			$cptAsso++;
		}
	}

	echo "Nombre de champs dans la base : " . $taille . "<br/>";
	echo "Nombre de champs any : " . $cptAny . "<br/>";
	echo "Nombre de champs asso : " . $cptAsso . "<br/>";
	echo "Nombre de champs pers : " . $cptPers . "<br/>";
	
	var_dump($_POST);

	?>

	<div id='choixType'>
	<br />
	<input type="radio" name="typeAP" value="association" id="association" onclick="showRadio();" <?php if($typeAP == "association") echo "checked"; ?> />Association
	<input type="radio" name="typeAP" value="personne" id="personne" onclick="showRadio();" <?php if($typeAP == "personne") echo "checked"; ?> />Personne
	<br /><br />
	</div>
	
<!-- ------------------------------------------------------------------------------ -->

	<div id='formcacheP'>
	<form method='post' action='modifier_formulaire.php' id='formulaire' name='formulaire' onsubmit="return verifForm(this)" >

	<input type="hidden" name="typeAP" value="personne" id="personne" onclick="showRadio();" <?php if($typeAP == "personne") echo "checked"; ?> />

	<table border='true' cellspacing='1' cellpadding='5' width=80%>
	
	<?php
	$taille = count($parsed_json->{'champs'});
	for($i=0; $i<$taille; $i++) {
		if($parsed_json->{'champs'}[$i]->{'class'} == "any" || $parsed_json->{'champs'}[$i]->{'class'} == "pers") {
		if($parsed_json->{'champs'}[$i]->{'type'} == "text" || $parsed_json->{'champs'}[$i]->{'type'} == "password" || $parsed_json->{'champs'}[$i]->{'type'} == "email") { ?>
<tr>
			<th><?php echo $parsed_json->{'champs'}[$i]->{'text'}; ?></th>
			<td>
				<input type="<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>" name="<?php echo $parsed_json->{'champs'}[$i]->{'name'}; ?>" value="<?php echo $_POST[$parsed_json->{'champs'}[$i]->{'name'}]; ?>" class="int" onblur="verif<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>(this)"/>
			</td>
		</tr>
		<?php
		}
		else if ($parsed_json->{'champs'}[$i]->{'type'} == "radio") { ?>
<tr>
			<th><?php echo $parsed_json->{'champs'}[$i]->{'text'}; ?></th>
			<td>
			<?php
			$nombreOpts = count($parsed_json->{'champs'}[$i]->{'options'});
			for($j=0; $j<$nombreOpts; $j++) { ?>
				<input type="<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>" name="<?php echo $parsed_json->{'champs'}[$i]->{'name'}; ?>" value="<?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}; ?>" <?php if($_POST[$parsed_json->{'champs'}[$i]->{'name'}] == $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}) echo 'checked'; ?> class="int"/><?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'text'}; ?><br/>
			<?php } ?></td>
		</tr>
		<?php }
		else if ($parsed_json->{'champs'}[$i]->{'type'} == "checkbox") { ?>
<tr>
			<th><?php echo $parsed_json->{'champs'}[$i]->{'text'}; ?></th>
			<td>
			<?php
			$nombreOpts = count($parsed_json->{'champs'}[$i]->{'options'});
			$tab = $_POST[$parsed_json->{'champs'}[$i]->{'name'} . "[]"];
			for($j=0; $j<$nombreOpts; $j++) { ?>
				<input type="<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>" name="<?php echo $parsed_json->{'champs'}[$i]->{'name'} . '[]'; ?>" value="<?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}; ?>" <?php for($x=0; $x<$nombreOpts; $x++) { if($_POST[$parsed_json->{'champs'}[$i]->{'name'}][$x] == $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}) echo 'checked'; } ?> class="int"/><?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'text'}; ?><br/>
			<?php } ?></td>
		</tr>
		<?php }
		}
	}?>
	</table>
	<input type="submit" class="inb ok" id="submitP" value="Valider" name="submit" />
	</form>
	</div>

<!-- ------------------------------------------------------------------------------ -->

	<div id='formcacheA'>
	<form method='post' action='modifier_formulaire.php' id='form' name='form' onsubmit="return verifForm(this)">

	<input type="hidden" name="typeAP" value="association" id="association" onclick="showRadio();" <?php if($typeAP == "association") echo "checked"; ?> />

	<table border='true' cellspacing='1' cellpadding='5' width=80%>
	
	<?php
	$taille = count($parsed_json->{'champs'});
	for($i=0; $i<$taille; $i++) {
		if($parsed_json->{'champs'}[$i]->{'class'} == "any" || $parsed_json->{'champs'}[$i]->{'class'} == "asso") {
		if($parsed_json->{'champs'}[$i]->{'type'} == "text" || $parsed_json->{'champs'}[$i]->{'type'} == "password" || $parsed_json->{'champs'}[$i]->{'type'} == "email") { ?>
<tr>
			<th><?php echo $parsed_json->{'champs'}[$i]->{'text'}; ?></th>
			<td>
				<input type="<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>" name="<?php echo $parsed_json->{'champs'}[$i]->{'name'}; ?>" value="<?php echo $_POST[$parsed_json->{'champs'}[$i]->{'name'}]; ?>" class="int" onblur="verif<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>(this)"/>
			</td>
		</tr>
		<?php
		}
		else if ($parsed_json->{'champs'}[$i]->{'type'} == "radio") { ?>
<tr>
			<th><?php echo $parsed_json->{'champs'}[$i]->{'text'}; ?></th>
			<td>
			<?php
			$nombreOpts = count($parsed_json->{'champs'}[$i]->{'options'});
			for($j=0; $j<$nombreOpts; $j++) { ?>
				<input type="<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>" name="<?php echo $parsed_json->{'champs'}[$i]->{'name'}; ?>" value="<?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}; ?>" <?php if($_POST[$parsed_json->{'champs'}[$i]->{'name'}] == $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}) echo 'checked'; ?> class="int"/><?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'text'}; ?><br/>
			<?php } ?></td>
		</tr>
		<?php }
		else if ($parsed_json->{'champs'}[$i]->{'type'} == "checkbox") { ?>
<tr>
			<th><?php echo $parsed_json->{'champs'}[$i]->{'text'}; ?></th>
			<td>
			<?php
			$nombreOpts = count($parsed_json->{'champs'}[$i]->{'options'});
			$tab = $_POST[$parsed_json->{'champs'}[$i]->{'name'} . "[]"];
			for($j=0; $j<$nombreOpts; $j++) { ?>
				<input type="<?php echo $parsed_json->{'champs'}[$i]->{'type'}; ?>" name="<?php echo $parsed_json->{'champs'}[$i]->{'name'} . '[]'; ?>" value="<?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}; ?>" <?php for($x=0; $x<$nombreOpts; $x++) { if($_POST[$parsed_json->{'champs'}[$i]->{'name'}][$x] == $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'name'}) echo 'checked'; } ?> class="int"/><?php echo $parsed_json->{'champs'}[$i]->{'options'}[$j]->{'text'}; ?><br/>
			<?php } ?></td>
		</tr>
		<?php }
		}
	}?>
	</table>
	<input type="submit" class="inb ok" id="submitA" value="Valider" name="submit" />
	</form>
	</div>

	</body>
</html>
