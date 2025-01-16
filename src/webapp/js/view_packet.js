const urlParams = new URLSearchParams(window.location.search);

const edit_dialog_ids = [ "edit_packet_apid", "edit_packet_kind", "edit_packet_type",
			  "edit_packet_subtype", "edit_packet_domain", "edit_packet_name",
			  "edit_packet_short_desc", "edit_packet_desc", "edit_packet_param_desc",
			  "edit_packet_dest_desc", "edit_packet_code" ];

const edit_properties = [ "idProcess", "kind", "type", "subtype", "domain", "name",
			   "shortDesc", "desc", "descParam", "descDest", "code" ];



function load_data()
{
	var packet_handler = new TableHandler({
		table_id: "table_packet",
		template_id: "table_packet_row",
		properties: [ "id", "kind", "type", "subtype",
			      "discriminant", "domain", "name",
			      "shortDesc"],
		modal_id: "packet_modal",
		edit_dialog_ids: edit_dialog_ids,
		edit_properties: edit_properties,
		submit_button_id: "packet_submit_button",
		create_button_id: "create_packet_button_id",
		end_point: `api/v2/projects/${urlParams.get("idProject")}` +
		`/standards/${urlParams.get("idStandard")}/packets`,
		create_item: {}
	});

	packet_handler.load_items();
}


window.onload = load_data();
