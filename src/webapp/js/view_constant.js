const urlParams = new URLSearchParams(window.location.search);


function load_data()
{
		var constant_handler = new TableHandler({ table_id: "table_constant",
																							template_id: "table_constant_row",
																							properties: [ "id", "domain", "name",
																														"value", "desc" ],
																							modal_id: "constant_modal",
																							edit_dialog_ids: [ "edit_constant_domain",
																																 "edit_constant_name",
																																 "edit_constant_value",
																																 "edit_constant_desc" ],
																							edit_properties: [ "domain", "name",
																																 "value", "desc" ],
																							submit_button_id: "constant_submit_button",
																							create_button_id: "create_constant_button_id",
																							end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																							`/standards/${urlParams.get("idStandard")}/constants`,
																							create_item: {}
																						});

		constant_handler.load_items();
}


window.onload = load_data();
