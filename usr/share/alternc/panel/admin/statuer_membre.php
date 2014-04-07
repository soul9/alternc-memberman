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
			__("this page is restricted to authorized staff");
			exit();
		}
		
		if(isset($_POST['submitAct'])) {

			$membres = $_POST['membre'];

			foreach ($membres as $membre) {
				if (!($u=$admin->get($membre))) {
					$error=sprintf(_("Member '%s' does not exist"),$membre)."<br />";
				}
				else {
					$db->query("UPDATE memberman SET valeur='Activé' WHERE clef='statut' AND uid='$membre'");
					$error=sprintf(_("Member %s successfully activated"),$u["login"])."<br />";
				}
			}
			include ("liste_membres.php");
			exit;

		}

		if(isset($_POST['submitAtt'])) {

			$membres = $_POST['membre'];

			foreach ($membres as $membre) {
				if (!($u=$admin->get($membre))) {
					$error=sprintf(_("Member '%s' does not exist"),$membre)."<br />";
				}
				else {
					$db->query("UPDATE memberman SET valeur='En attente' WHERE clef='statut' AND uid='$membre'");
					$error=sprintf(_("Member %s successfully put in pending"),$u["login"])."<br />";
				}
			}
			include ("liste_membres.php");
			exit;

		}

		if(isset($_POST['submitDes'])) {

			$membres = $_POST['membre'];

			foreach ($membres as $membre) {
				if (!($u=$admin->get($membre))) {
					$error=sprintf(_("Member '%s' does not exist"),$membre)."<br />";
				}
				else {
					$db->query("UPDATE memberman SET valeur='Désactivé' WHERE clef='statut' AND uid='$membre'");
					$error=sprintf(_("Member %s successfully desactivated"),$u["login"])."<br />";
				}
			}
			include ("liste_membres.php");
			exit;

		}

		if(isset($_POST['submitDel'])) {

			$membres = $_POST['membre'];

			foreach ($membres as $membre) {
				if (!($u=$admin->get($membre)) || !$admin->del_mem($membre)) {
					$error=sprintf(_("Member '%s' does not exist"),$membre)."<br />";
				}
				else {
					$db->query("DELETE FROM memberman WHERE uid='$membre'");
					$error=sprintf(_("Member %s successfully deleted"),$u["login"])."<br />";
				}
			}
			include ("liste_membres.php");
			exit;

		}

		?>
	</body>
</html>
