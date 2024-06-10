function load_data()
{
		var project_handler = new TableHandler({
				table_id: "table_project",
				template_id: "table_project_row",
				properties: ["id", "name", "desc", "owner", "isPublic" ],
				modal_id: "project_modal",
				edit_dialog_ids: [ "edit_project_name", "edit_project_desc",
													 "edit_project_owner", "edit_project_public" ],
				edit_properties: [ "name", "desc", "user_id", "isPublic" ],
				submit_button_id: "project_submit_button",
				create_button_id: "create_project_button_id",
				end_point: "api/v2/projects",
				create_item: {}
		});

		project_handler.load_items();
}


window.onload = load_data();
