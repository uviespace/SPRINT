-- add foreign keys to all tables

START TRANSACTION;

ALTER TABLE application ADD CONSTRAINT fk_application_project FOREIGN KEY(idProject) REFERENCES project(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE applicationcomponent ADD CONSTRAINT fk_applicationcomponent_application FOREIGN KEY(idApplication) REFERENCES application(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE applicationcomponent ADD CONSTRAINT fk_applicationcomponent_component FOREIGN KEY(idComponent) REFERENCES component(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE applicationstandard ADD CONSTRAINT fk_applicationstandard_application FOREIGN KEY(idApplication) REFERENCES application(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE applicationstandard ADD CONSTRAINT fk_applicationstandard_standard FOREIGN KEY(idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE calibration ADD CONSTRAINT fk_calibration_standard FOREIGN KEY (idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE constants ADD CONSTRAINT fk_constants_standard FOREIGN KEY (idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE datapoolidentifier ADD CONSTRAINT fk_datapoolidentifier_parameter FOREIGN KEY (idParameter) REFERENCES `parameter`(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE datapoolidentifier ADD CONSTRAINT fk_datapoolidentifier_project FOREIGN KEY (idProject) REFERENCES project(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE enumeration ADD CONSTRAINT `fk_enumeration_type` FOREIGN KEY (idType) REFERENCES `type`(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `limit` ADD CONSTRAINT `fk_limit_parameter` FOREIGN KEY (idParameter) REFERENCES parameter(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE packet ADD CONSTRAINT fk_packet_standard FOREIGN KEY (idStandard) REFERENCES  standard(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE packet ADD CONSTRAINT fk_packet_parent_packet FOREIGN KEY (idParent) REFERENCES packet(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE packet ADD CONSTRAINT fk_packet_process FOREIGN KEY (idProcess) REFERENCES process(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `parameter` ADD CONSTRAINT fk_parameter_standard FOREIGN KEY (idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE `parameter` ADD CONSTRAINT fk_parameter_type FOREIGN KEY (idType) REFERENCES `type`(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE parameter_deduced_relation ADD CONSTRAINT fk_parameter_deduced_relation_parameter FOREIGN KEY (idParameter) REFERENCES `parameter`(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE parameter_deduced_relation ADD CONSTRAINT fk_paremeter_deduced_relation_ref_parameter FOREIGN KEY (idParameter) REFERENCES `parameter`(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE parametersequence ADD CONSTRAINT fk_parametersequence_standard FOREIGN KEY (idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE parametersequence ADD CONSTRAINT fk_parametersequence_parameter FOREIGN KEY (idParameter) REFERENCES `parameter`(id) ON UPDATE RESTRICT ON DELETE RESTRICT;
ALTER TABLE parametersequence ADD CONSTRAINT fk_parametersequence_packet FOREIGN KEY (idPacket) REFERENCES packet(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE process ADD CONSTRAINT fk_process_project FOREIGN KEY (idProject) REFERENCES project(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE service ADD CONSTRAINT fk_service_standard FOREIGN KEY (idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE standard ADD CONSTRAINT fk_standard_project FOREIGN KEY (idProject) REFERENCES project(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE standardstandard ADD CONSTRAINT fk_standardstandard_parent FOREIGN KEY (idStandardParent) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE standardstandard ADD CONSTRAINT fk_standardstandard_child FOREIGN KEY (idStandardChild) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE `type` ADD CONSTRAINT fk_type_standard FOREIGN KEY (idStandard) REFERENCES standard(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE userproject ADD CONSTRAINT fk_userproject_user FOREIGN KEY (idUser) REFERENCES user(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE userproject ADD CONSTRAINT fk_userproject_project FOREIGN KEY (idProject) REFERENCES project(id) ON UPDATE RESTRICT ON DELETE CASCADE;
ALTER TABLE userproject ADD CONSTRAINT fk_userproject_role FOREIGN KEY (idRole) REFERENCES `role`(id) ON UPDATE RESTRICT ON DELETE RESTRICT;


COMMIT;
	
	
	
	
	

