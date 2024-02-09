const urlParams = new URLSearchParams(window.location.search);


function load_data()
{
		var apid_handler = new TableHandler({ table_id: "table_apid",
																					template_id: "table_apid_row",
																					properties: ["id", "address", "name", "desc"],
																					open_url: "",
																					open_url_param_name: "",
																					modal_id: "apid_modal",
																					edit_dialog_ids: ["edit_apid_address",
																														"edit_apid_name",
																														"edit_apid_desc"],
																					edit_properties: ["address", "name", "desc" ],
																					submit_button_id: "apid_submit_button",
																					create_button_id: "create_apid_button_id",
																					end_point: `api/v2/projects/${urlParams.get("idProject")}/apids`,
																					create_item: {}
																				});

		apid_handler.load_items();
}




window.onload = load_data();
