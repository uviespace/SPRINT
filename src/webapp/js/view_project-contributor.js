const urlParams = new URLSearchParams(window.location.search);

const edit_dialog_ids = [ "edit_contributor_email", "edit_contributor_role" ];
const edit_properties = [ "idUser", "idRole" ];

function load_data()
{
		var contributor_hdl = new TableHandler(
				{
						table_id: "table_contributor",
						template_id: "table_contributor_row",
						properties: [ "id", "email", "role_name", ],
						modal_id: "contributor_modal",
						edit_dialog_ids: edit_dialog_ids,
						edit_properties: edit_properties,
						submit_button_id: "contributor_submit_button",
						create_button_id: "create_contributor_button_id",
						end_point: `api/v2/projects/${urlParams.get("idProject")}/contributors`,
						empty_item: {},
						create_item_from_modal_fn: create_item
				}
		);


		contributor_hdl.load_items();
}


window.onload = load_data();


function create_item(edit_item)
{
		for (var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		edit_item["email"] = document.getElementById(edit_dialog_ids[0]).selectedOptions[0].text;
		edit_item["role_name"] = document.getElementById(edit_dialog_ids[1]).selectedOptions[0].text;
}
