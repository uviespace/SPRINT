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
				name: "packet",
				//table_id: "table_packet",
				//template_id: "table_packet_row",
				properties: [ "id", "kind", "type", "subtype",
											"discriminant", "domain", "name",
											"shortDesc"],
				//modal_id: "packet_modal",
				edit_dialog_ids: edit_dialog_ids,
				edit_properties: edit_properties,
				//submit_button_id: "packet_submit_button",
				//create_button_id: "create_packet_button_id",
				end_point: `api/v2/projects/${urlParams.get("idProject")}` +
						`/standards/${urlParams.get("idStandard")}/packets`,
				empty_item: { idProcess: null }
		});

		packet_handler.add_validations({
				validations: [
						{ id: "edit_packet_subtype", type: "max_value", param: 255, msg: "Sub-service types must be between 1 and 255" },
						{ id: "edit_packet_domain", type: "max_length", param: 256, msg: "Domain names cannot be longer than 256" },
						{ id: "edit_packet_name", type: "max_length", param: 256, msg: "Packet names cannot be longer than 256"},
						{ id: "edit_packet_code", type: "max_length", param: 128, msg: "Codes cannot be longer than 128"}
				],
				required_properties: [ "edit_packet_apid", "edit_packet_kind", "edit_packet_type", "edit_packet_subtype", "edit_packet_name"  ]
		})

		packet_handler.load_items();
}


window.onload = load_data();
