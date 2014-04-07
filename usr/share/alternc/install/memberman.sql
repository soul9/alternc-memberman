CREATE TABLE IF NOT EXISTS `memberman` (
	`num`		INT( 10 )	NOT NULL AUTO_INCREMENT,
	`clef`		VARCHAR( 255 )	NOT NULL default ' ' ,
	`valeur`	VARCHAR( 255 )	NOT NULL default ' ' ,
	`uid`		INT( 10 )	NOT NULL default '0' ,
	`type`		VARCHAR( 255 )	NOT NULL default ' ' ,
	`defaut`	VARCHAR( 255 )	NOT NULL default ' ' ,
	`obligatoire`	INT( 10 )	NOT NULL default '0' ,
	PRIMARY KEY ( `num` )
) ENGINE=MyISAM COMMENT = 'Liste des membres';
