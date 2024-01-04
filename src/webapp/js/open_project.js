const urlParams = new URLSearchParams(window.location.search);

function load_data()
{
		var application_handler = new TableHandler({ table_id: "table_applications", template_id: "table_applications_row",
																								 properties: ["id", "name", "desc" ],
																								 open_url: "open_application.php?idProject=" + urlParams.get("id"),
																								 open_url_param_name: "idApplication",
																								 modal_id: "applications_modal",
																								 edit_dialog_ids: [ "edit_application_name", "edit_application_description" ],
																								 edit_properties: ["name", "desc"],
																								 submit_button_id: "application_submit_button",
																								 end_point: `api/v2/projects/${urlParams.get("id")}/applications`,
																								 create_button_id: "create_application_button_id",
																								 empty_item: { idProject: urlParams.get("id") } });
		
		var standards_handler = new TableHandler({ table_id: "table_standards", template_id: "table_standards_row",
																							 properties: ["id", "name", "desc"],
																							 open_url: "open_standard.php?idProject=" + urlParams.get("id"),
																							 open_url_param_name: "idStandard",
																							 modal_id: "standards_modal",
																							 edit_dialog_ids: ["edit_standard_name", "edit_standard_description"],
																							 edit_properties: ["name", "desc"],
																							 submit_button_id: "standards_submit_button",
																							 end_point: `api/v2/projects/${urlParams.get("id")}/standards`,
																							 create_button_id: "create_standard_button_id",
																							 empty_item: { idProject: urlParams.get("id") } });
		application_handler.load_items();
		standards_handler.load_items();
		
}

window.onload = load_data();
