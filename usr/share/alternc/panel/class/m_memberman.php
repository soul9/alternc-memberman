<?php

class m_memberman {

	function hook_menu() {
		$obj = array(
			'title'		=> _("Memberman"),
			'ico'		=> 'images/marsnet.png',
			'link'		=> 'toggle',
			'class'		=> 'memberman',		
			'pos'		=> 15,
			'links'		=> 
			array(
				array(
					'txt'   => _("Nouveau membre"), 
					'url'   => 'nouveau_membre.php',
					'ico'	=> '',
					'class' => 'memberman',
				),
				array(
					'txt'   => _("Liste membres"), 
					'url'   => 'liste_membres.php',
					'ico'	=> '',
					'class' => 'memberman',
				),
				array(
					'txt'   => _("Tableau forfaits"), 
					'url'   => 'tableau_forfaits.php',
					'ico'	=> '',
					'class' => 'memberman',
				),
			)
		) ;

		return $obj;
	}

}

?>
