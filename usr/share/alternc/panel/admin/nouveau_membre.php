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
	</head>
	<body onLoad="showRadio();">
	<?php
	require_once("../class/config.php");
	include_once("head.php");

	if (!$admin->enabled) {
		__("This page is restricted to authorized staff");
		exit();
	}

	function test_input($data) {
		$data1 = trim($data);
		$data2 = stripslashes($data1);
		$data3 = htmlspecialchars($data2);
		if($data == $data3)
			return $data;
		else
			return "";
	}

	$typeAP = $login = $mdp = $conf = $forfait = $nom = $prenom = $email = $nomAsso = $activite = $categorie = "";

	$loginErr = $mdpErr = $confErr = $forfaitErr = $nomErr = $prenomErr = $emailErr = $nomAssoErr = $activiteErr = $categorieErr = "";

	$testLogin = $testMdp = $testConf = $testForfait = $testNom = $testPrenom = $testEmail =  $testNomAsso = $testActivite = $testCategorie = $testInscription = true;

	$typeAP = $_POST['typeAP'];

	if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {

		if($typeAP == "association") {

			if (empty($_POST["nomAsso"])) {
				$nomAssoErr = "<br/>Nom Association requis";
				$testNomAsso = false;
			}
			else {
				$nomAsso = test_input($_POST["nomAsso"]);
				if (!preg_match("/^[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]*$/", $nomAsso)) {
					$nomAssoErr = "<br/>Lettres, espaces et tirets uniquement";
					$testNomAsso = false;
				}
			}

			if (!empty($_POST["activite"])) {
				$activite = test_input($_POST["activite"]);
				if (!preg_match("/^[a-zA-Z0-9àáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]*$/", $activite)) {
					$activiteErr = "<br/>Lettres, espaces et tirets uniquement";
					$testActivite = false;
				}
			}

			if (empty($_POST["categorie"])) {
				$testCategorie = false;
				$categorieErr = "<br/>Résultat annuel requis";
			}
			else
				$categorie = test_input($_POST["categorie"]);
		
		}

		if (empty($_POST["forfait"])) {
			$testForfait = false;
			$forfaitErr = "<br/>Forfait requis";
		}
		else
			$forfait = test_input($_POST["forfait"]);

		if (empty($_POST["nom"])) {
			$nomErr = "<br/>Nom requis";
			$testNom = false;
		}
		else {
			$nom = test_input($_POST["nom"]);
			if (!preg_match("/^[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]*$/", $nom)) {
				$nomErr = "<br/>Lettres, espaces et tirets uniquement";
				$testNom = false;
			}
		}

		if (empty($_POST["prenom"])) {
			$prenomErr = "<br/>Prenom requis";
			$testPrenom = false;
		}
		else {
			$prenom = test_input($_POST["prenom"]);
			if (!preg_match("/^[a-zA-Zàáâãäåçèéêëìíîïðòóôõöùúûüýÿ -]*$/", $prenom)) {
				$prenomErr = "<br/>Lettres, espaces et tirets uniquement";
				$testPrenom = false;
			}
		}

		if (empty($_POST["email"])) {
			$emailErr = "<br/>Adresse mail requise";
			$testEmail = false;
		}
		else {
			$email = test_input($_POST["email"]);
			if(!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
				$emailErr = "<br/>Adresse mail invalide";
				$testEmail = false;
			}
		}

		if (empty($_POST["login"])) {
			$loginErr = "<br/>Login requis";
			$testLogin = false;
		}
		else {
			$login = test_input($_POST["login"]);
			if (!preg_match("/^[a-z0-9]*$/", $login)) {
				$loginErr = "<br/>Minuscules et chiffres uniquement";
				$testLogin = false;
			}
		}

		if (empty($_POST["mdp"])) {
			$mdpErr = "<br/>Mot de passe requis";
			$testMdp = false;
		}
		else
			$mdp = test_input($_POST["mdp"]);

		$conf = test_input($_POST["conf"]);
		if($mdp != $conf) {
			$confErr = "<br/>Mauvaise confirmation";
			$testMdp = false;
			$testConf = false;
		}
		else if (empty($_POST["conf"])) {
			$confErr = "<br/>Confirmation requise";
			$testConf = false;
		}
		else if (!$admin->checkPolicy("mem",$login,$mdp) && !empty($_POST["login"])) {
			$error=$err->errstr();
			if (isset($error) && $error)
				echo "<p class=\"alert alert-danger\">$error</p>";
			$testMdp = false;
		}

	}

	$testInscription = ($testForfait && $testCategorie && $testNomAsso && $testActivite && $testNom && $testPrenom && $testEmail && $testLogin && $testMdp && $testConf);

	$fields = array (
		"typeAP"	=> array ($typeAP, $uid, "string", "", 1),
		"login"		=> array ($login, $uid, "string", "", 1),
		"mdp"		=> array (md5($mdp), $uid, "string", "", 1),
		"forfait"	=> array ($forfait, $uid, "string", "", 1),
		"nom"		=> array ($nom, $uid, "string", "", 1),
		"prenom"	=> array ($prenom, $uid, "string", "", 1),
		"email"		=> array ($email, $uid, "string", "", 1),
		"nomAsso"	=> array ($nomAsso, $uid, "string", "", 1),
		"activite"	=> array ($activite, $uid, "string", "", 1),
		"categorie"	=> array ($valeurCategorie, $uid, "string", "", 1),
		"statut"	=> array ("En attente", $uid, "string", "", 1),
	);

	if(isset($_POST['submit']) && $testInscription == true) {

		$pass = $mdp;
		$nmail = $email;
		$canpass = 1;
		$type = $forfait;
		$notes = '';
		$dom_to_create = false;
		$db_server_id = 1;

		if (!($u=$admin->add_mem($login, $pass, $nom, $prenom, $nmail, $canpass, $type, 12, $notes, 0, $dom_to_create, $db_server_id))) {
			$error=$err->errstr();
			if (isset($error) && $error)
				echo "<p class=\"alert alert-danger\">$error</p>";
		}
		else {
			$u=$admin->add_mem($login, $pass, $nom, $prenom, $nmail, $canpass, $type, 12, $notes, 0, $dom_to_create, $db_server_id);

			$db->query("SELECT max(m.uid) as nextid FROM membres m");
			if (!$db->next_record()) {
				$uid=2000;
			}
			else {
				$uid=$db->Record["nextid"];
				if ($uid<=2000) $uid=2000;
			}

			foreach ($fields AS $name => $options) {
				if($options[0] != "")
					$db->query("INSERT INTO memberman(clef, valeur, uid, type, defaut, obligatoire) VALUES ('$name', '$options[0]', '$uid', '$options[2]', '$options[3]', '$options[4]');");
			}

			$from = "marsnet@yopmail.fr";
			$subject = "Inscription Marsnet";
			$message = "Bonjour, vous êtes inscrit, BRAVO !";
			$message = wordwrap($message, 70);
			mail($email,$subject,$message,"From: $from\n");

			$from = "marsnet@yopmail.fr";
			$subject = "Inscription Marsnet";
			$message = "Bonjour, le membre '$login' s'est inscrit !";
			$message = wordwrap($message, 70);
			mail("herve.galvan@yopmail.fr",$subject,$message,"From: $from\n");

			$error=_("The new member has been successfully created");

			include("liste_membres.php");
			exit;
		}

	}
	?>


	<h3><?php __("New AlternC account"); ?></h3>
	<hr id="topbar"/>
	<br />


	<div id='choixType'>
	<br />
	<input type="radio" name="typeAP" value="association" id="association" onclick="showRadio();" <?php if($typeAP == "association") echo "checked"; ?> />Association
	<input type="radio" name="typeAP" value="personne" id="personne" onclick="showRadio();" <?php if($typeAP == "personne") echo "checked"; ?> />Personne
	<br /><br />
	</div>


	<div id='formcacheP'>
	<form method="post" action="nouveau_membre.php" id="form" >

		<input type="hidden" name="typeAP" value="personne" id="personne" onclick="showRadio();" <?php if($typeAP == "personne") echo "checked"; ?> />

		<table border='true' cellspacing='1' cellpadding='5' width=80%>

			<tr>
			<th width=40%>Login<span class="error"><?php echo $loginErr;?></span></th>
			<td width=60%><input type="text" name="login" class="int" value="<?php echo $login;?>" /></td>
			</tr>

			<tr>
			<th>Mot de passe<span class="error"><?php echo $mdpErr;?></span></th>
			<td><input type="password" name="mdp" class="int"/></td>
			</tr>

			<tr>
			<th>Confirmation<span class="error"><?php echo $confErr;?></span></th>
			<td><input type="password" name="conf" class="int"/></td>
			</tr>

			<tr>
			<th>Forfait<span class="error"><?php echo $forfaitErr;?></span></th>
			<td><input type="radio" name="forfait" value="petit" <?php if($forfait == "petit") echo "checked"; ?> >Petit<br />
			<input type="radio" name="forfait" value="moyen" <?php if($forfait == "moyen") echo "checked"; ?> >Moyen<br />
			<input type="radio" name="forfait" value="grand" <?php if($forfait == "grand") echo "checked"; ?> >Grand</td>
			</tr>

			<tr>
			<th>Cotisation</th>
			<td><input type="radio" checked>Personne seule, cotisation de 12&euro;<br /></td>
			</tr>

			<tr>
			<th>Nom<span class="error"><?php echo $nomErr;?></span></th>
			<td><input type="text" name="nom" class="int" value="<?php echo $nom;?>" /></td>
			</tr>

			<tr>
			<th>Prenom<span class="error"><?php echo $prenomErr;?></span></th>
			<td><input type="text" name="prenom" class="int" value="<?php echo $prenom;?>" /></td>
			</tr>

			<tr>
			<th>Adresse mail<span class="error"><?php echo $emailErr;?></span></th>
			<td><input type="email" name="email" class="int" value="<?php echo $email;?>" /></td>
			</tr>

		</table>
		<br />
		<input type="submit" class="inb ok" id="submitP" value="Valider son inscription" name="submit" />
	</form>
	</div>

	<div id='formcacheA'>
	<form method="post" action="nouveau_membre.php" id="form" >

		<input type="hidden" name="typeAP" value="association" id="association" onclick="showRadio();" <?php if($typeAP == "association") echo "checked"; ?> />

		<table border='true' cellspacing='1' cellpadding='5' width=80%>

			<tr>
			<th width=40%>Login<span class="error"><?php echo $loginErr;?></span></th>
			<td width=60%><input type="text" name="login" class="int" value="<?php echo $login;?>" /></td>
			</tr>

			<tr>
			<th>Mot de passe<span class="error"><?php echo $mdpErr;?></span></th>
			<td><input type="password" name="mdp" class="int" /></td>
			</tr>

			<tr>
			<th>Confirmation<span class="error"><?php echo $confErr;?></span></th>
			<td><input type="password" name="conf" class="int" /></td>
			</tr>

			<tr>
			<th>Forfait<span class="error"><?php echo $forfaitErr;?></span></th>
			<td><input type="radio" name="forfait" value="petit" <?php if($forfait == "petit") echo "checked"; ?> >Petit<br />
			<input type="radio" name="forfait" value="moyen" <?php if($forfait == "moyen") echo "checked"; ?> >Moyen<br />
			<input type="radio" name="forfait" value="grand" <?php if($forfait == "grand") echo "checked"; ?> >Grand</td>
			</tr>

			<tr>
			<th>Cotisation<span class="error"><?php echo $categorieErr;?></span></th>
			<td>Résultat annuel :<br/><input type="radio" name="categorie" value="petite" <?php if($categorie == "petite") echo "checked"; ?> /> Inf&eacute;rieur &agrave; 5 000&euro;, cotisation de 18&euro;<br />
			<input type="radio" name="categorie" value="moyenne" <?php if($categorie == "moyenne") echo "checked"; ?> /> Entre 5 000&euro; et 150 000&euro;, cotisation de 30&euro;<br />
			<input type="radio" name="categorie" value="grande" <?php if($categorie == "grande") echo "checked"; ?> /> Sup&eacute;rieur &agrave; 150 000&euro;, cotisation de 48&euro;</td>
			</tr>

			<tr>
			<th>Nom Association<span class="error"><?php echo $nomAssoErr;?></span></th>
			<td><input type="text" name="nomAsso" class="int" value="<?php echo $nomAsso;?>" /></td>
			</tr>

			<tr>
			<th>Activité<span class="error"><?php echo $activiteErr;?></span></th>
			<td><input type="text" name="activite" class="int" value="<?php echo $activite;?>" /></td>
			</tr>

			<tr>
			<th>Nom<span class="error"><?php echo $nomErr;?></span></th>
			<td><input type="text" name="nom" class="int" value="<?php echo $nom;?>" /></td>
			</tr>

			<tr>
			<th>Prenom<span class="error"><?php echo $prenomErr;?></span></th>
			<td><input type="text" name="prenom" class="int" value="<?php echo $prenom;?>" /></td>
			</tr>

			<tr>
			<th>Adresse mail<span class="error"><?php echo $emailErr;?></span></th>
			<td><input type="email" name="email" class="int" value="<?php echo $email;?>" /></td>
			</tr>

		</table>
		<br />
		<input type="submit" class="inb ok" id="submitA" value="Valider son inscription" name="submit" />
	</form>
	</div>

	</form>

	</body>
</html>
