CREATE TABLE `posts` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `content` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) default CHARSET=utf8mb4;