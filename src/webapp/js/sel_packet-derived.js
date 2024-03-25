const urlParams = new URLSearchParams(window.location.search);

const packets = {}

const edit_dialog_ids = [ "edit_packet_discriminant", "edit_packet_name", "edit_packet_short_desc",
												  "edit_packet_description", "edit_packet_param_desc", "edit_packet_destination_desc",
												  "edit_packet_code" ];

const edit_properties = [ "discriminant", "name", "shortDesc", "desc", "descParam",
												  "descDest", "code" ];

async function toggle_derivation_visibility(packet_id)
{
		const packet_container = document.getElementById("packet-" + packet_id);
		const icon = document.getElementById("icon-" + packet_id);

		if (packet_container.style.display == "none") {
				packet_container.style.display = "block";
				icon.classList.remove("nf-cod-triangle_right");
				icon.classList.add("nf-cod-triangle_down");
		} else {
				packet_container.style.display = "none";
				icon.classList.remove("nf-cod-triangle_down");
				icon.classList.add("nf-cod-triangle_right");
		}

		if (!packets.hasOwnProperty(packet_id)) {
				const packet_end_point = `api/v2/projects/${urlParams.get("idProject")}` +
							`/standards/${urlParams.get("idStandard")}` +
							`/packets/${packet_id}/derived_packets`;

				packets[packet_id] = {
						id: packet_id,
						table_handler: new TableHandler({
								table_id: "table_packet_" + packet_id,
								template_id: "table_packet_row",
								open_url: `view_packet-params-derived.php?idProject=${urlParams.get("idProject")}` +
										`&idStandard=${urlParams.get("idStandard")}` +
										`&idParent=${packet_id}`,
								open_url_param_name: "idPacket",
								properties: [ "id", "discriminant", "name", "shortDesc", "desc",
														  "descParam", "descDest", "code", "param_count" ],
								modal_id: "packet_modal",
								edit_dialog_ids: edit_dialog_ids,
								edit_properties: edit_properties,
								submit_button_id: "packet_submit_button",
								end_point: packet_end_point,
								create_button_id: "create_derivation_button_id_" + packet_id,
								empty_item: {}
						})
				};

				await packets[packet_id].table_handler.load_items();
		}
}


