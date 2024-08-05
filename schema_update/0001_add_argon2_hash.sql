ALTER TABLE `user` ADD COLUMN hash varchar(128);
ALTER TABLE `user` ADD COLUMN hash_type int;
UPDATE `user` SET hash_type = 0;

