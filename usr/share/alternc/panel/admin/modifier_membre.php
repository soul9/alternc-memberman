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

	$donnees = array (
		"typeAP"	=> "",
		"login"		=> "",
		"mdp"		=> "",
		"forfait"	=> "",
		"nom"		=> "",
		"prenom"	=> "",
		"email"		=> "",
		"nomAsso"	=> "",
		"activite"	=> "",
		"categorie"	=> "",
		"statut"	=> "",
	);

	$uid = $_GET['uid'];

	$db->query("SELECT * FROM memberman WHERE uid='$uid'");
	while ($db->next_record()) {
		$donnees[$db->Record["clef"]] = $db->Record["valeur"];
	}
	
	$typeAP = $donnees['typeAP'];
	$login = $donnees['login'];
	$forfait = $donnees['forfait'];
	$nom = $donnees['nom'];
	$prenom = $donnees['prenom'];
	$email = $donnees['email'];
	$categorie = $donnees['categorie'];
	$nomAsso = $donnees['nomAsso'];
	$activite = $donnees['activite'];
	$statut = $donnees['statut'];

	$loginErr = $oldErr = $mdpErr = $confErr = $forfaitErr = $nomErr = $prenomErr = $emailErr = $statutErr = $categorieErr = $nomAssoErr = $activiteErr = "";

	$testLogin = $testOld = $testMdp = $testConf = $testForfait = $testNom = $testPrenom = $testEmail = $testStatut = $testCategorie = $testNomAsso = $testActivite = $testInscription = true;

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
			else
				$activite = "";

			if (empty($_POST["categorie"])) {
				$testCategorie = false;
				$categorieErr = "<br/>Résultat annuel requis";
			}
			else
				$categorie = test_input($_POST["categorie"]);
		
		}

		if (empty($_POST["forfait"])) {
			$testForfait = false;
			$forfait = "<br/>Forfait requis";
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

		if (!empty($_POST["old"])) {

			$old = test_input($_POST["old"]);
			$old = md5($old);
			if ($old != $donnees["mdp"]) {
				$oldErr = "<br/>Ancien mot de passe erroné";
				$testMdp = false;
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

		if (empty($_POST["statut"])) {
			$testStatut = false;
			$statutErr = "<br/>Statut requis";
		}
		else
			$statut = test_input($_POST["statut"]);

	}

	$testInscription = ($testForfait && $testCategorie && $testNomAsso && $testActivite && $testNom && $testPrenom && $testEmail && $testLogin && $testMdp && $testConf);

	$fields = array (
		"typeAP"	=> $typeAP,
		"login"		=> $login,
		"mdp"		=> md5($mdp),
		"forfait"	=> $forfait,
		"nom"		=> $nom,
		"prenom"	=> $prenom,
		"email"		=> $email,
		"nomAsso"	=> $nomAsso,
		"activite"	=> $activite,
		"categorie"	=> $categorie,
		"statut"	=> $statut,
	);

	if(isset($_POST['submit']) && $testInscription == true) {

		if($fields['nom'] != $donnees['nom']) {
			$db->query("UPDATE memberman SET valeur='$nom' WHERE uid='$uid' AND clef='nom'");
			$db->query("UPDATE local SET nom='$nom' WHERE uid='$uid'");
			$error = $error . "Le nom de " . $login . " a été modifié.<br />";
		}
		if($fields['prenom'] != $donnees['prenom']) {
			$db->query("UPDATE memberman SET valeur='$prenom' WHERE uid='$uid' AND clef='prenom'");
			$db->query("UPDATE local SET prenom='$prenom' WHERE uid='$uid'");
			$error = $error . "Le prenom de " . $login . " a été modifié.<br />";
		}
		if($fields['email'] != $donnees['email']) {
			$db->query("UPDATE memberman SET valeur='$email' WHERE uid='$uid' AND clef='email'");
			$db->query("UPDATE membres SET mail='$email' WHERE uid='$uid'");
			$error = $error . "L'adresse mail de " . $login . " a été modifiée, un email a été envoyé.<br />";
			$from = "marsnet@yopmail.fr";
			$subject = "Confirmation adresse mail";
			$message = "Bonjour, veuillez confirmer le changement d'adresse mail !";
			$message = wordwrap($message, 70);
			mail($email,$subject,$message,"From: $from\n");
		}
		if (!empty($_POST["old"])) {
			$mdp = md5($mdp);
			$db->query("UPDATE memberman SET valeur='$mdp' WHERE uid='$uid' AND clef='mdp'");
			$db->query("UPDATE membres SET pass='$mdp' WHERE uid='$uid'");
			$error = $error . "Le mot de passe de " . $login . " a été modifié.<br />";
		}
		if($fields['forfait'] != $donnees['forfait']) {
			$db->query("UPDATE memberman SET valeur='$forfait' WHERE uid='$uid' AND clef='forfait'");
			$db->query("UPDATE membres SET type='$forfait' WHERE uid='$uid'");
			$error = $error . "Le forfait de " . $login . " a été modifié.<br />";
		}
		if($fields['statut'] != $donnees['statut']) {
			$db->query("UPDATE memberman SET valeur='$statut' WHERE uid='$uid' AND clef='statut'");
			$error = $error . "Le statut de " . $login . " a été modifié.<br />";
		}
		if($typeAP == "association") {

			if($fields['nomAsso'] != $donnees['nomAsso']) {
				$db->query("UPDATE memberman SET valeur='$nomAsso' WHERE uid='$uid' AND clef='nomAsso'");
				$error = $error . "Le nom d'association de " . $login . " a été modifié.<br />";
			}

			if($fields['activite'] != $donnees['activite']) {
				$db->query("UPDATE memberman SET valeur='$activite' WHERE uid='$uid' AND clef='activite'");
				$error = $error . "L'activité de " . $login . " a été modifiée.<br />";
				if($fields['activite'] == "")
					$db->query("DELETE FROM memberman WHERE uid='$uid' AND clef='activite'");
			}

			if($fields['categorie'] != $donnees['categorie']) {
				$db->query("UPDATE memberman SET valeur='$categorie' WHERE uid='$uid' AND clef='categorie'");
				$error = $error . "La taille de " . $login . " a été modifiée.<br />";
			}

		}

		include("liste_membres.php");
		exit;

	}

	?>

	<h3><b><?php __($login); ?></b> account</h3>
	<hr id="topbar"/>
	<br />

	<div id='choixType'>
	<br />
	<input type="radio" name="typeAP" value="<?php echo $typeAP; ?>" id="<?php echo $typeAP; ?>" onclick="showRadio();" checked /><?php if($typeAP == "association") echo "Association"; else echo "Personne"; ?>
	<br /><br />
	</div>


	<div id='formcacheP'>
	<form method="post" action="modifier_membre.php?uid=<?php echo $uid; ?>" id="form" >

		<input type="hidden" name="typeAP" value="personne" id="personne" onclick="showRadio();" <?php if($typeAP == "personne") echo "checked"; ?> />

		<table border='true' cellspacing='1' cellpadding='5' width=80%>

			<tr>
			<th width=40%>Login<span class="error"><?php echo $loginErr;?></span></th>
			<td width=60%><input type="text" name="login" class="int" value="<?php echo $login;?>" readonly /></td>
			</tr>

			<tr>
			<th>Ancien mot de passe<span class="error"><?php echo $oldErr;?></span></th>
			<td><input type="password" name="old" class="int"/></td>
			</tr>

			<tr>
			<th>Nouveau mot de passe<span class="error"><?php echo $mdpErr;?></span></th>
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

			<tr>
			<th>Statut<span class="error"><?php echo $statutErr;?></span></th>
			<td><input type="radio" name="statut" value="Activé" <?php if($statut == "Activé") echo "checked"; ?> >Activé<br />
			<input type="radio" name="statut" value="En attente" <?php if($statut == "En attente") echo "checked"; ?> >En attente<br />
			<input type="radio" name="statut" value="Désactivé" <?php if($statut == "Désactivé") echo "checked"; ?> >Désactivé</td>
			</tr>

		</table>
		<br />
		<input type="submit" class="inb ok" id="submitP" value="Enregistrer" name="submit" />
	</form>
	</div>

	<div id='formcacheA'>
	<form method="post" action="modifier_membre.php?uid=<?php echo $uid; ?>" id="form" >

		<input type="hidden" name="typeAP" value="association" id="association" onclick="showRadio();" <?php if($typeAP == "association") echo "checked"; ?> />

		<table border='true' cellspacing='1' cellpadding='5' width=80%>

			<tr>
			<th width=40%>Login<span class="error"><?php echo $loginErr;?></span></th>
			<td width=60%><input type="text" name="login" class="int" value="<?php echo $login;?>" readonly /></td>
			</tr>

			<tr>
			<th>Ancien mot de passe<span class="error"><?php echo $oldErr;?></span></th>
			<td><input type="password" name="old" class="int"/></td>
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

			<tr>
			<th>Statut<span class="error"><?php echo $statutErr;?></span></th>
			<td><input type="radio" name="statut" value="Activé" <?php if($statut == "Activé") echo "checked"; ?> >Activé<br />
			<input type="radio" name="statut" value="En attente" <?php if($statut == "En attente") echo "checked"; ?> >En attente<br />
			<input type="radio" name="statut" value="Désactivé" <?php if($statut == "Désactivé") echo "checked"; ?> >Désactivé</td>
			</tr>

		</table>
		<br />
		<input type="submit" class="inb ok" id="submitA" value="Enregistrer" name="submit" />
	</form>
	</div>

	</form>

	</body>
</html>

<!-- onclick="document.location='adm_list.php' -->
