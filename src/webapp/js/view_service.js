const urlParams = new URLSearchParams(window.location.search);

function load_data()
{
		var service_handler = new TableHandler({ table_id: "table_service",
																						 template_id: "table_service_row",
																						 properties: ["id", "type", "name", "desc" ],
																						 open_url: "",
																						 open_url_param_name: "",
																						 modal_id: "service_modal",
																						 edit_dialog_ids: ["edit_service_type",
																															 "edit_service_name",
																															 "edit_service_desc"],
																						 edit_properties: [ "type", "name", "desc" ],
																						 submit_button_id: "service_submit_button",
																						 create_button_id: "create_service_button_id",
																						 end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																						 `/standards/${urlParams.get("idStandard")}/services`,
																						 create_item: {}
																					 });

		service_handler.add_validations({
				validations:[
						{ id: "edit_service_type", type: "max_value", param: 255, msg: "Service type must be between 1 and 255" },
						{ id: "edit_service_name", type: "max_length", param: 256, msg: "Service name cannot be longer 256" }
				],
				required_properties: [ "edit_service_type", "edit_service_name" ]
		});

		service_handler.load_items();
}

window.onload = load_data();
