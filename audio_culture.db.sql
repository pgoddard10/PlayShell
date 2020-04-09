BEGIN TRANSACTION;
DROP TABLE IF EXISTS `visitor_history`;
CREATE TABLE IF NOT EXISTS `visitor_history` (
	`content_id`	INTEGER NOT NULL,
	`time_scanned`	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`visitor_id`	INTEGER NOT NULL,
	PRIMARY KEY(`content_id`,`time_scanned`,`visitor_id`),
	FOREIGN KEY(`content_id`) REFERENCES `content`(`content_id`)
);
DROP TABLE IF EXISTS `visitor`;
CREATE TABLE IF NOT EXISTS `visitor` (
	`visitor_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`first_name`	TEXT,
	`last_name`	TEXT,
	`email`	TEXT,
	`address_1`	TEXT,
	`address_2`	TEXT,
	`address_3`	TEXT,
	`address_4`	TEXT,
	`address_postcode`	TEXT
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
DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
	`role_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT NOT NULL UNIQUE
);
DROP TABLE IF EXISTS `staff_role`;
CREATE TABLE IF NOT EXISTS `staff_role` (
	`staff_id`	INTEGER NOT NULL,
	`role_id`	INTEGER NOT NULL,
	FOREIGN KEY(`staff_id`) REFERENCES `staff`(`staff_id`),
	FOREIGN KEY(`role_id`) REFERENCES `role`(`role_id`),
	PRIMARY KEY(`staff_id`,`role_id`)
);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (1,'pgoddard10','$2y$10$iPhtEghZVVe7lIqjusadB.divf7a4CGtR.LjaWYIafQSphymEV80a','Paul','Goddard','paul2.goddard@live.uwe.ac.uk',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (2,'sholmes','$2y$10$KL0fvV9vv2AYBF7lOlJLees/Yd7fz0dpdSOY.PvT0yc2d./tNoq/G','Sherlock','Holmes','',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (3,'jsmith','$2y$10$UozzO6LiErGPUqj9Gb06CeHSASTbLLqxH.ohASZK09/NqlX8BnrDa','Jim','Smith','jsmith@gmail.com',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (4,'wriker','$2y$10$8gvujP4TxftVZh8Rnr1AHuUtSvjfjjGN3q.1wNOR34IZf0JeOAGme','Will','Riker','wriker@gmail.com',0);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (5,'mappleby','$2y$10$8gvujP4TxftVZh8Rnr1AHuUtSvjfjjGN3q.1wNOR34IZf0JeOAGme','Mike','Appleby','',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (6,'kparis','$2y$10$8gvujP4TxftVZh8Rnr1AHuUtSvjfjjGN3q.1wNOR34IZf0JeOAGme','Katherine','Paris','',0);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (7,'rparker','$2y$10$8gvujP4TxftVZh8Rnr1AHuUtSvjfjjGN3q.1wNOR34IZf0JeOAGme','Ruby','Parker','',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (8,'jjefferies','$2y$10$8gvujP4TxftVZh8Rnr1AHuUtSvjfjjGN3q.1wNOR34IZf0JeOAGme','Julie','Jefferies','',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (9,'jhoward','$2y$10$8gvujP4TxftVZh8Rnr1AHuUtSvjfjjGN3q.1wNOR34IZf0JeOAGme','Jackie','Howard','',1);
INSERT INTO `staff` (staff_id,username,password,first_name,last_name,email,active) VALUES (10,'snewton','$2y$10$CtRdKvoApMCoshCGHY/lCenZO7nO7HwUn9K7LobwFJ6vquN1a6QYW','Sandra','Newton','',1);
INSERT INTO `role` (role_id,name) VALUES (1,'Staff Database Manager');
INSERT INTO `role` (role_id,name) VALUES (2,'Content Manager');
INSERT INTO `role` (role_id,name) VALUES (3,'Report Manager');
INSERT INTO `role` (role_id,name) VALUES (4,'Visitor Manager');
INSERT INTO `role` (role_id,name) VALUES (5,'Device Manager');
INSERT INTO `staff_role` (staff_id,role_id) VALUES (3,2);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,1);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,2);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,3);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,4);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (1,5);
INSERT INTO `staff_role` (staff_id,role_id) VALUES (2,2);
DROP TABLE IF EXISTS `gesture`;
CREATE TABLE IF NOT EXISTS `gesture` (
	`gesture_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`name`	INTEGER NOT NULL
);
DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
	`item_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`heritage_id`	TEXT,
	`name`	TEXT NOT NULL,
	`location`	TEXT,
	`created`	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`last_modified`	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`modified_by`	INTEGER,
	`url`	TEXT,
	`active`	INTEGER NOT NULL
);
DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
	`content_id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	`name`	TEXT NOT NULL,
	`tag_id`	TEXT,
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
	FOREIGN KEY(`item_id`) REFERENCES `item`(`item_id`),
	FOREIGN KEY(`gesture_id`) REFERENCES `gesture`(`gesture_id`)
);
INSERT INTO `item` (item_id,heritage_id,name,location,created,last_modified,modified_by,url,active) VALUES (1,NULL,'Glenside','website','2020-03-20 16:51:04','2020-03-20 16:51:04',1,NULL,1);
INSERT INTO `item` (item_id,heritage_id,name,location,created,last_modified,modified_by,url,active) VALUES (2,NULL,'Test data 1',NULL,'2020-03-20 16:51:04','2020-04-07 08:10:04',22,NULL,1);
INSERT INTO `item` (item_id,heritage_id,name,location,created,last_modified,modified_by,url,active) VALUES (3,NULL,'Test data 2',NULL,'2020-03-20 16:51:04','2020-04-07 08:10:28',1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (1,'Final Voice','JBD874',1,NULL,'Once people did not know how to mend a leg. The Thomas Splint was invented in the late 1800’s and was put into use in the latter half of WW1 reducing the mortality rate from leg injuries in WW1 from 80% to 8%.’
We are still at the very beginnings of knowing what to do to help people when brain function is impaired, but we are gaining rapidly more knowledge and understanding.
This final voice concludes our journey on how life was organised and the tensions that occur when caring for people who are mentally ill.
The interviewee was asked about the long corridors at Glenside and people wandering. 
‘That’s the state of your mind, because I think that is what you do, you wander.
You don’t know where you are going and you don’t know where you’re coming from.
Yeah, I think you do wander about, just aimlessly really…
You’re not capable of thinking, suppose you think, but you don’t you think I’m ill, and that’s why you need caring for…',NULL,'2020-03-20 16:48:50','2020-03-20 17:36:50',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (2,'Treatment','UBIY87OI',1,NULL,'Glenside Hospital catered for a patient’s basic needs. When it was first built in 1861 it may have seemed to some patients, who were not too traumatised by their illness, like a haven. It was a hospital for the poor and from 1861-1900, the hospital would have been so different to their daily experience. In a world without the welfare state, and National Health Service.
A patient would be given a bed, clean sheets and three meals a day.  At home people would have been used to sharing beds with relatives, living in one room with many people, and often having to work long hours to ensure they and their family did not starve.
Other patients may have been too ill to notice their surroundings while some,used to a more refined life may have found the long corridors and stark interiors a bleak place.
100 years on in 1961, and just over 15 years into the National Health Service, things had to change. Apart from anything else the hospital was full.  It was increasingly important, having provided people with a safe place to be looked after, to provide more support to help them return home.
If you have experience of institutions, such as a boarding school, care home, or the military you may recognise some aspects of life described. However, this institution had the added element, like any hospital, where the people being served were vulnerable because they were ill. Patients in a mental hospital are especially vulnerable because their illness is the least understood and unlike a physical illness it is hidden within the skull and even now often difficult to define.
In 1994 Glenside Hospital was closed. These large institutions were closing across the country as they were deemed as redundant. With more sophisticated drugs, Care in the Community became a possibility. Many more people could be cared for in their own homes, with smaller hospitals and houses taking over the role of nursing those who needed more attention and support.
This next piece collects some thoughts from both patients and staff on the benefits of a large hospital and ‘Care in the Community’.
There is tension between providing the right level of care and giving people their independence so that they do not harm themselves or become dependent.
How can we help people with mental illness? It is a big question and not easy to answer.
These extracts summarise the dilemma.',NULL,'2020-03-20 18:39:59','2020-03-20 18:39:59',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (14,'Inside Glenside','8G7GUYSD',1,NULL,'One of the things I found strange, people that had past us in the grounds had toothbrushes sticking out of their pockets where people normally had pens. The toothbrush was precious. It said something about how life must be organised. That it was so important to carry your toothbrush at all times.',NULL,'2020-03-20 19:17:46','2020-03-20 19:17:46',1,1,NULL,1);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (15,'nom.rand.33','F125374',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:20:06','2020-03-20 19:20:06',1,1,NULL,2);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (16,'nom.rand.57','F4368880',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:20:21','2020-03-20 19:20:21',22,1,NULL,3);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (17,'nom.rand.31','F412733',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:20:25','2020-03-20 19:20:25',1,1,NULL,2);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (18,'nom.rand.3','F7332797',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:29','2020-03-20 19:23:29',34,1,NULL,2);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (19,'nom.rand.21','F63289521',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:38','2020-03-20 19:23:38',1,1,NULL,3);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (20,'nom.rand.9','F49586611',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:48','2020-03-20 19:23:48',45,1,NULL,3);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (21,'nom.rand.12','F97506933',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:23:59','2020-03-20 19:23:59',1,1,NULL,3);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (22,'nom.rand.59','F21105528',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:24:11','2020-03-20 19:24:11',22,1,NULL,3);
INSERT INTO `content` (content_id,name,tag_id,tts_enabled,soundfile_location,written_text,next_content,created,last_modified,modified_by,active,gesture_id,item_id) VALUES (23,'nom.rand.70','F1099422',1,NULL,'This is some lovely text',NULL,'2020-03-20 19:26:40','2020-03-20 19:26:40',1,1,NULL,3);
COMMIT;
