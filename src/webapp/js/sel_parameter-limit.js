const urlParams = new URLSearchParams(window.location.search);

const limits = {}

const edit_dialog_ids = [ "edit_limit_type", "edit_limit_lower_value",
													"edit_limit_upper_value", "edit_limit_setting" ];
const edit_properties = [ "type", "lvalue", "hvalue", "setting" ];

async function toggle_limit_visibility(param_id)
{
		const enum_container = document.getElementById("limit-" + param_id);
		const icon = document.getElementById("icon-" + param_id);

		if (enum_container.style.display == "none") {
				enum_container.style.display = "block";
				icon.classList.remove("nf-cod-triangle_right");
				icon.classList.add("nf-cod-triangle_down");
		} else {
				enum_container.style.display = "none";
				icon.classList.remove("nf-cod-triangle_down");
				icon.classList.add("nf-cod-triangle_right");
		}

		if (!limits.hasOwnProperty(param_id)) {
				const enum_end_point = `api/v2/projects/${urlParams.get("idProject")}` +
							`/standards/${urlParams.get("idStandard")}` +
							`/parameters/${param_id}/limits`;

				
				limits[param_id] = {
						id: param_id,
						table_handler: new TableHandler({
								table_id: "table_limit_" + param_id,
								template_id: "table_limit_row",
								properties: [ "id", "type", "lvalue", "hvalue", "setting" ],
								modal_id: "limit_modal",
								edit_dialog_ids: edit_dialog_ids,
								edit_properties: edit_properties,
								submit_button_id: "limit_submit_button",
								end_point: enum_end_point,
								create_button_id: "create_limit_button_id_" + param_id,
								empty_item: {}
						})
				};

				await limits[param_id].table_handler.load_items();
				
		}
}
