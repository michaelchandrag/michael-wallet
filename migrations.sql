CREATE TABLE `user` (
	`id` 					INT NOT NULL AUTO_INCREMENT,
	`email`					VARCHAR(200) NOT NULL,
	`phone_number` 			VARCHAR(200) NOT NULL,
	`password`				VARCHAR(200) NOT NULL,
	`first_name`			VARCHAR(200) NULL,
	`last_name`				VARCHAR(200) NULL,
	`created_at`			DATETIME NULL,
	`updated_at` 			DATETIME NULL,
	`deleted_at`			DATETIME NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY unique_email (`email`),
	UNIQUE KEY unique_phone_number (`phone_number`)
) ENGINE=InnoDB;

/* */

CREATE TABLE `wallet` (
	`id` 				INT NOT NULL AUTO_INCREMENT,
	`id_user` 			INT NOT NULL,
	`name`				VARCHAR(200) NULL DEFAULT '',		
	`description` 		TEXT NULL,
	`created_at`		DATETIME NULL,
	`updated_at`		DATETIME NULL,
	`deleted_at`	 	DATETIME NULL,
    PRIMARY KEY (`id`),
	INDEX (`id_user`) 
) ENGINE=InnoDB;