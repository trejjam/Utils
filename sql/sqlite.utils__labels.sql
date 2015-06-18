CREATE TABLE IF NOT EXISTS `utils__labels` (
	`id`        INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
	`namespace` VARCHAR(20) NOT NULL DEFAULT 'default',
	`name`      VARCHAR(20) NOT NULL,
	`value`     TEXT        NOT NULL
);
