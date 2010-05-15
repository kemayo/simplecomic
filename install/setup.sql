 CREATE TABLE `comics` (
`comicid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 255 ) NOT NULL ,
`filename` VARCHAR( 255 ) NOT NULL ,
`pub_date` INT NOT NULL ,
`chapterid` INT NOT NULL ,
INDEX `bychapter` ( `chapterid` , `pub_date` ) ,
INDEX `bydate` ( `pub_date` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

 CREATE TABLE  `comics_text` (
 `comicid` INT NOT NULL ,
 `description` MEDIUMTEXT NOT NULL ,
 `transcript` MEDIUMTEXT NOT NULL ,
 `alt_text` MEDIUMTEXT NOT NULL ,
 PRIMARY KEY (  `comicid` )
 ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

 CREATE TABLE  `chapters` (
 `chapterid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `title` VARCHAR( 255 ) NOT NULL ,
 `slug` VARCHAR( 32 ) NOT NULL ,
 `order` TINYINT NOT NULL ,
 `parentid` INT NOT NULL
 ) ENGINE = MYISAM;

CREATE TABLE  `chapters_text` (
 `chapterid` INT NOT NULL ,
 `description` MEDIUMTEXT NOT NULL ,
 PRIMARY KEY (  `chapterid` )
 ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `rants` (
`rantid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 128 ) NOT NULL ,
`pub_date` INT NOT NULL
) ENGINE = MYISAM ;

 CREATE TABLE  `rants_text` (
 `rantid` INT NOT NULL ,
 `text` MEDIUMTEXT NOT NULL ,
 PRIMARY KEY (  `rantid` )
 ) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
