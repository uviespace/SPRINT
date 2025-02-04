CREATE TABLE parameter_deduced_relation (
	id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
	idParameter INT UNSIGNED NOT NULL,
	idReferenceParameter INT UNSIGNED NOT NULL
);

ALTER TABLE `parameter_deduced_relation` ADD CONSTRAINT `idParameter_fk` 
	FOREIGN KEY (`idParameter`) REFERENCES `parameter`(`id`) ON DELETE CASCADE;

ALTER TABLE parameter_deduced_relation ADD CONSTRAINT idReferenceParameter_fk 
	FOREIGN KEY (`idReferenceParameter`) REFERENCES `parameter`(`id`) ON DELETE RESTRICT;

