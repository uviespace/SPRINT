const urlParams = new URLSearchParams(window.location.search);


function load_data()
{
		var types_handler = new TableHandler({ table_id: "table_datatype",
																					 template_id: "table_datatype_row",
																					 properties: [ "id", "domain", "name",
																												 "nativeType", "size", "pusparamtype", 
																												 "value", "desc" ],
																					 modal_id: "datatype_modal",
																					 edit_dialog_ids: [ "edit_datatype_domain",
																															"edit_datatype_name",
																															"edit_datatype_native_type",
																															"edit_datatype_size",
																															"edit_datatype_value",
																															"edit_datatype_desc" ],
																					 edit_properties: [ "domain", "name", "nativeType",
																															"size", "value", "desc"],
																					 submit_button_id: "datatype_submit_button",
																					 create_button_id: "create_datatype_button_id",
																					 end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																					 `/standards/${urlParams.get("idStandard")}/datatypes`,
																					 empty_item: {},
																				 });

		types_handler.load_items();
}


window.onload = load_data();
