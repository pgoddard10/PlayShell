BEGIN TRANSACTION;
DROP TABLE IF EXISTS `user_history`;
CREATE TABLE IF NOT EXISTS `user_history` (
	`content_id`	INTEGER NOT NULL,
	`time_scanned`	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`user_id`	INTEGER NOT NULL,
	FOREIGN KEY(`content_id`) REFERENCES `content`(`content_id`),
	FOREIGN KEY(`user_id`) REFERENCES `user`(`user_id`),
	PRIMARY KEY(`content_id`,`time_scanned`,`user_id`)
);
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
	`user_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`first_name`	TEXT,
	`last_name`	TEXT,
	`email`	TEXT
);
DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
	`staff_id`	INTEGER NOT NULL,
	`username`	TEXT NOT NULL,
	`password`	TEXT NOT NULL,
	`first_name`	TEXT NOT NULL,
	`last_name`	TEXT NOT NULL,
	`email`	TEXT,
	`active`	INTEGER NOT NULL DEFAULT 1,
	PRIMARY KEY(`staff_id`)
);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (1,'pgoddard10','$2y$10$iPhtEghZVVe7lIqjusadB.divf7a4CGtR.LjaWYIafQSphymEV80a','Paul','Goddard','paul2.goddard@live.uwe.ac.uk',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (2,'sholmes','$2y$10$CnBgnliJM54qDiMdEsW8i.1cY5VpsdwwawNEO.VDcbmPaZ5Hx5SMS','Sherlock','Holmes',NULL,1);
DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
	`role_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT NOT NULL UNIQUE
);
INSERT INTO `role` (role_id,name) VALUES (1,'Staff Database Manager');
INSERT INTO `role` (role_id,name) VALUES (2,'Content Manager');
INSERT INTO `role` (role_id,name) VALUES (3,'Report Manager');
INSERT INTO `role` (role_id,name) VALUES (4,'Visitor Manager');
INSERT INTO `role` (role_id,name) VALUES (5,'Device Manager');
DROP TABLE IF EXISTS `staff_role`;
CREATE TABLE IF NOT EXISTS `staff_role` (
	`staff_id`	INTEGER NOT NULL,
	`role_id`	INTEGER NOT NULL,
	PRIMARY KEY(`staff_id`,`role_id`),
	FOREIGN KEY(`staff_id`) REFERENCES `staff`(`staff_id`),
	FOREIGN KEY(`role_id`) REFERENCES `role`(`role_id`)
);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (2,2);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,1);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,2);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,3);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,4);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,5);
DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
	`item_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`heritage_id`	TEXT,
	`name`	TEXT NOT NULL,
	`location`	TEXT,
	`last_modified`	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified_by`	INTEGER,
	`url`	TEXT,
	`active`	INTEGER NOT NULL,
	FOREIGN KEY(`modified_by`) REFERENCES `staff`(`staff_id`)
);
INSERT INTO `item` (item_id,heritage_id,name,location,last_modified,modified_by,url,active) VALUES (1,NULL,'talking head','reception','2020-03-20 16:51:04',1,NULL,1);
DROP TABLE IF EXISTS `gesture`;
CREATE TABLE IF NOT EXISTS `gesture` (
	`gesture_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`name`	INTEGER NOT NULL
);
DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
	`content_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`name`	TEXT NOT NULL,
	`tag_id`	TEXT NOT NULL,
	`tts_enabled`	INTEGER NOT NULL,
	`soundfile_location`	TEXT,
	`written_text`	TEXT,
	`next_content`	INTEGER,
	`created`	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`last_modified`	TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`modified_by`	INTEGER,
	`active`	INTEGER,
	`gesture_id`	INTEGER,
	`item_id`	INTEGER,
	FOREIGN KEY(`modified_by`) REFERENCES `staff`(`staff_id`),
	FOREIGN KEY(`gesture_id`) REFERENCES `gesture`(`gesture_id`),
	FOREIGN KEY(`item_id`) REFERENCES `item`(`item_id`)
);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (1,'some content name','JBD874',1,NULL,'This is some sample content which is to be spoken aloud by the text-to-speech system.',NULL,'2020-03-20 16:48:50','2020-03-20 17:36:50',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (2,'name2','UBIY87OI',1,NULL,'Some creative content is written here.',NULL,'2020-03-20 18:39:59','2020-03-20 18:39:59',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (14,'some name','8G7GUYSD',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:17:46','2020-03-20 19:17:46',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (15,'nom.rand.33','F1127945374',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:20:06','2020-03-20 19:20:06',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (16,'nom.rand.57','F4368880',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:20:21','2020-03-20 19:20:21',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (17,'nom.rand.31','F412733',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:20:25','2020-03-20 19:20:25',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (18,'nom.rand.3','F7332797',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:29','2020-03-20 19:23:29',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (19,'nom.rand.21','F63289521',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:38','2020-03-20 19:23:38',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (20,'nom.rand.9','F49586611',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:48','2020-03-20 19:23:48',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (21,'nom.rand.12','F97506933',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:59','2020-03-20 19:23:59',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (22,'nom.rand.59','F21105528',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:24:11','2020-03-20 19:24:11',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (23,'nom.rand.70','F1099422',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:26:40','2020-03-20 19:26:40',1,1,NULL,1);
COMMIT;