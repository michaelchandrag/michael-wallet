CREATE TABLE `user` (
	`id` 					INT NOT NULL AUTO_INCREMENT,
	`email`					VARCHAR(200) NOT NULL,
	`phone_number` 			VARCHAR(200) NULL,
	`password`				VARCHAR(200) NOT NULL,
	`name`					VARCHAR(200) NULL,
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


/* */

CREATE TABLE `category` (
	`id` 					INT NOT NULL AUTO_INCREMENT,
	`id_user` 				INT NOT NULL,
	`type` 					VARCHAR(200) NOT NULL, /* cash_in / cash_out */
	`name` 					VARCHAR(200) NULL DEFAULT '',
	`description` 			TEXT NULL,
	`created_at`			DATETIME NULL,
	`updated_at`			DATETIME NULL,
	`deleted_at`			DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX (`id_user`),
    INDEX (`type`)
) ENGINE=InnoDB;

/* */

CREATE TABLE `transaction` (
	`id` 					INT NOT NULL AUTO_INCREMENT,
	`id_user` 				INT NOT NULL,
    `id_category` 			INT NOT NULL,
    `id_wallet` 			INT NOT NULL,
    `amount` 				INT DEFAULT 0 NOT NULL,
	`description` 			TEXT NULL,
	`created_at`			DATETIME NULL,
	`updated_at`			DATETIME NULL,
	`deleted_at`			DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX (`id_user`),
    INDEX (`id_category`),
    INDEX (`id_wallet`)
) ENGINE=InnoDB;


ALTER TABLE `wallet` ADD lifetime_cash_in_total INT DEFAULT 0;
ALTER TABLE `wallet` ADD lifetime_cash_out_total INT DEFAULT 0;
ALTER TABLE `wallet` ADD lifetime_total INT DEFAULT 0;

ALTER TABLE `category` ADD lifetime_cash_in_total INT DEFAULT 0;
ALTER TABLE `category` ADD lifetime_cash_out_total INT DEFAULT 0;
ALTER TABLE `category` ADD lifetime_total INT DEFAULT 0;

ALTER TABLE `user` ADD lifetime_cash_in_total INT DEFAULT 0;
ALTER TABLE `user` ADD lifetime_cash_out_total INT DEFAULT 0;
ALTER TABLE `user` ADD lifetime_total INT DEFAULT 0;

/* ========================================================== */

ALTER TABLE `wallet` ADD COLUMN `monthly_cash_in_total` INT NULL DEFAULT 0;
ALTER TABLE `wallet` ADD COLUMN `monthly_cash_out_total` INT NULL DEFAULT 0;
ALTER TABLE `wallet` ADD COLUMN `monthly_total` INT NULL DEFAULT 0;
ALTER TABLE `category` ADD COLUMN `monthly_cash_in_total` INT NULL DEFAULT 0;
ALTER TABLE `category` ADD COLUMN `monthly_cash_out_total` INT NULL DEFAULT 0;
ALTER TABLE `category` ADD COLUMN `monthly_total` INT NULL DEFAULT 0;
ALTER TABLE `transaction` ADD COLUMN `transaction_at` DATETIME NULL DEFAULT NOW();