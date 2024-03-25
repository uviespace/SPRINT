const urlParams = new URLSearchParams(window.location.search);

const enums = {}

const edit_dialog_ids = [ "edit_enum_name", "edit_enum_value", "edit_enum_description" ];
const edit_properties = [ "name", "value", "desc" ];

async function toggle_enum_visibility(enum_id)
{
		const enum_container = document.getElementById("enum-" + enum_id);
		const icon = document.getElementById("icon-" + enum_id);

		if (enum_container.style.display == "none") {
				enum_container.style.display = "block";
				icon.classList.remove("nf-cod-triangle_right");
				icon.classList.add("nf-cod-triangle_down");
		} else {
				enum_container.style.display = "none";
				icon.classList.remove("nf-cod-triangle_down");
				icon.classList.add("nf-cod-triangle_right");
		}

		if (!enums.hasOwnProperty(enum_id)) {
				const enum_end_point = `api/v2/projects/${urlParams.get("idProject")}` +
							`/standards/${urlParams.get("idStandard")}` +
							`/datatypes/${enum_id}/enumerations`;

				
				enums[enum_id] = {
						id: enum_id,
						table_handler: new TableHandler({
								table_id: "table_enum_" + enum_id,
								template_id: "table_enum_row",
								properties: [ "id", "name", "value", "desc" ],
								modal_id: "enum_modal",
								edit_dialog_ids: edit_dialog_ids,
								edit_properties: edit_properties,
								submit_button_id: "enum_submit_button",
								end_point: enum_end_point,
								create_button_id: "create_enum_button_id_" + enum_id,
								empty_item: {}
						})
				};

				await enums[enum_id].table_handler.load_items();
				
		}
}
