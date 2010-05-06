/* Base media table */
CREATE TABLE IF NOT EXISTS `#__media` (
  `media_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `directory` VARCHAR(400)  NOT NULL,
  `filename` VARCHAR(255)  NOT NULL,
  `thumbnail_file` VARCHAR(20)  NOT NULL,
  `filesize` BIGINT ,
  `filedate` DATETIME ,
  `syncable` TINYINT(1)  NOT NULL DEFAULT 0,
  `synced` TINYINT(1)  NOT NULL DEFAULT 0,
  `mimetype` VARCHAR(60)  NOT NULL DEFAULT 'application/octet-stream',
  `sizex` INTEGER UNSIGNED,
  `sizey` INTEGER UNSIGNED,
  `description` VARCHAR(255) ,
  `notes` MEDIUMTEXT ,
  `tags` MEDIUMTEXT ,
  `metadata` MEDIUMTEXT ,
  PRIMARY KEY (`media_id`)
)
CHARACTER SET utf8 COLLATE utf8_general_ci;

/* !!DELETE ME!! Temporary entry for the mockup extension */
INSERT	INTO `#__extensions`
	(`name`, `type`, `element`, `client_id`, `enabled`, `access`, `protected`)
	VALUES ('Media Manager NG Mockups','component','com_mmng_mock', 1, 1, 1, 0);
