const urlParams = new URLSearchParams(window.location.search);


const pus_data = [
    { "type": 0, "PTC": 1, "PFC": 0, "name":"PTC 1 / PFC 0", "size": 1, "desc":"Unsigned Integer; 1 bit; boolean parameter" },
    { "type": 1, "PTC": 2, "PFC": -1, "name":"PTC 2 / 0 < PFC < 33", "size": -1, "min_size": 1, "max_size": 32, "desc":"Unsigned Integer; PFC bits; enumerated parameter" },

	{ "type": 2, "PTC": 3, "PFC": 0, "name":"PTC 3 / PFC 0", "size":"4", "desc":"Unsigned Integer; 4 bits; unsigned integer parameter" },
    { "type": 3, "PTC": 3, "PFC": 1, "name":"PTC 3 / PFC 1", "size":"5", "desc":"Unsigned Integer; 5 bits; unsigned integer parameter" },
    { "type": 4, "PTC": 3, "PFC": 2, "name":"PTC 3 / PFC 2", "size":"6", "desc":"Unsigned Integer; 6 bits; unsigned integer parameter" },
    { "type": 5, "PTC": 3, "PFC": 3, "name":"PTC 3 / PFC 3", "size":"7", "desc":"Unsigned Integer; 7 bits; unsigned integer parameter" },
    { "type": 6, "PTC": 3, "PFC": 4, "name":"PTC 3 / PFC 4", "size":"8", "desc":"Unsigned Integer; 8 bits; unsigned integer parameter" },
    { "type": 7, "PTC": 3, "PFC": 5, "name":"PTC 3 / PFC 5", "size":"9", "desc":"Unsigned Integer; 9 bits; unsigned integer parameter" },
    { "type": 8, "PTC": 3, "PFC": 6, "name":"PTC 3 / PFC 6", "size":"10", "desc":"Unsigned Integer; 10 bits; unsigned integer parameter" },
    { "type": 9 , "PTC": 3, "PFC": 7, "name":"PTC 3 / PFC 7", "size":"11", "desc":"Unsigned Integer; 11 bits; unsigned integer parameter" },
    { "type": 10, "PTC": 3, "PFC": 8, "name":"PTC 3 / PFC 8", "size":"12", "desc":"Unsigned Integer; 12 bits; unsigned integer parameter" },
    { "type": 11, "PTC": 3, "PFC": 9, "name":"PTC 3 / PFC 9", "size":"13", "desc":"Unsigned Integer; 13 bits; unsigned integer parameter" },
    { "type": 12, "PTC": 3, "PFC": 10, "name":"PTC 3 / PFC 10", "size":"14", "desc":"Unsigned Integer; 14 bits; unsigned integer parameter" },
    { "type": 13, "PTC": 3, "PFC": 11, "name":"PTC 3 / PFC 11", "size":"15", "desc":"Unsigned Integer; 15 bits; unsigned integer parameter" },
    { "type": 14, "PTC": 3, "PFC": 12, "name":"PTC 3 / PFC 12", "size":"16", "desc":"Unsigned Integer; 16 bits; unsigned integer parameter" },
    { "type": 15, "PTC": 3, "PFC": 13, "name":"PTC 3 / PFC 13", "size":"24", "desc":"Unsigned Integer; 24 bits; unsigned integer parameter" },
    { "type": 16, "PTC": 3, "PFC": 14, "name":"PTC 3 / PFC 14", "size":"32", "desc":"Unsigned Integer; 32 bits; unsigned integer parameter" },
    { "type": 17, "PTC": 3, "PFC": 15, "name":"PTC 3 / PFC 15", "size":"48", "desc":"Unsigned Integer; 48 bits; unsigned integer parameter (not supported by SCOS2000)" },
    { "type": 18, "PTC": 3, "PFC": 16, "name":"PTC 3 / PFC 16", "size":"64", "desc":"Unsigned Integer; 64 bits; unsigned integer parameter (not supported by SCOS2000)" },

	{ "type": 19, "PTC": 4, "PFC": 0, "name":"PTC 4 / PFC 0", "size":"4", "desc":"Signed Integer; 4 bits; signed integer parameter" },
    { "type": 20, "PTC": 4, "PFC": 1, "name":"PTC 4 / PFC 1", "size":"5", "desc":"Signed Integer; 5 bits; signed integer parameter" },
    { "type": 21, "PTC": 4, "PFC": 2, "name":"PTC 4 / PFC 2", "size":"6", "desc":"Signed Integer; 6 bits; signed integer parameter" },
    { "type": 22, "PTC": 4, "PFC": 3, "name":"PTC 4 / PFC 3", "size":"7", "desc":"Signed Integer; 7 bits; signed integer parameter" },
    { "type":"23", "PTC": 4, "PFC": 4, "name":"PTC 4 / PFC 4", "size":"8", "desc":"Signed Integer; 8 bits; signed integer parameter" },
    { "type":"24", "PTC": 4, "PFC": 5, "name":"PTC 4 / PFC 5", "size":"9", "desc":"Signed Integer; 9 bits; signed integer parameter" },
    { "type":"25", "PTC": 4, "PFC": 6, "name":"PTC 4 / PFC 6", "size":"10", "desc":"Signed Integer; 10 bits; signed integer parameter" },
    { "type":"26", "PTC": 4, "PFC": 7, "name":"PTC 4 / PFC 7", "size":"11", "desc":"Signed Integer; 11 bits; signed integer parameter" },
    { "type":"27", "PTC": 4, "PFC": 8, "name":"PTC 4 / PFC 8", "size":"12", "desc":"Signed Integer; 12 bits; signed integer parameter" },
    { "type":"28", "PTC": 4, "PFC": 9, "name":"PTC 4 / PFC 9", "size":"13", "desc":"Signed Integer; 13 bits; signed integer parameter" },
    { "type":"29", "PTC": 4, "PFC": 10, "name":"PTC 4 / PFC 10", "size":"14", "desc":"Signed Integer; 14 bits; signed integer parameter" },
    { "type":"30", "PTC": 4, "PFC": 11, "name":"PTC 4 / PFC 11", "size":"15", "desc":"Signed Integer; 15 bits; signed integer parameter" },
    { "type":"31", "PTC": 4, "PFC": 12, "name":"PTC 4 / PFC 12", "size":"16", "desc":"Signed Integer; 16 bits; signed integer parameter" },
    { "type":"32", "PTC": 4, "PFC": 13, "name":"PTC 4 / PFC 13", "size":"24", "desc":"Signed Integer; 24 bits; signed integer parameter" },
    { "type":"33", "PTC": 4, "PFC": 14, "name":"PTC 4 / PFC 14", "size":"32", "desc":"Signed Integer; 32 bits; signed integer parameter" },
    { "type":"34", "PTC": 4, "PFC": 15, "name":"PTC 4 / PFC 15", "size":"48", "desc":"Signed Integer; 48 bits; signed integer parameter (not supported by SCOS2000)" },
    { "type":"35", "PTC": 4, "PFC": 16, "name":"PTC 4 / PFC 16", "size":"64", "desc":"Signed Integer; 64 bits; signed integer parameter (not supported by SCOS2000)" },

	{
		"type":"36", "PTC": 5, "PFC": 1, "name":"PTC 5 / PFC 1", "size":"32",
		"desc":"Simple precision real; 32 bits; simple precision real parameter"
	},
    {
		"type":"37", "PTC": 5, "PFC": 2, "name":"PTC 5 / PFC 2", "size":"64",
		"desc":"Double precision real; 64 bits; double precision real parameter"
	},
    {
		"type":"38", "PTC": 5, "PFC": 3, "name":"PTC 5 / PFC 3", "size":"32",
		"desc":"Simple precision real (MIL std); 32 bits; Referred to as PTC=5, PFC=2 in ESA missions Parameter Types definitions"
	},

	{ "type":"39", "PTC": 6, "PFC": 0, "name":"PTC 6 / PFC 0", "size":"0", "desc":"Bit string; variable; variable-length bit-string (not supported by SCOS2000)" },
    { "type":"40", "PTC": 6, "PFC": -1, "name":"PTC 6 / 0 < PFC < 33", "size":"1:32", "desc":"Unsigned integer; PFC bits; PUS bit-string parameter" },

	{ "type":"41", "PTC": 7, "PFC": 0, "name":"PTC 7 / PFC 0", "size":"0", "desc":"Octet string; variable; variable-length octet string" },
    { "type":"42", "PTC": 7, "PFC": -1, "name":"PTC 7 / PFC > 0", "size":"0", "desc":"Octet string; PFC octets; fixed-length octet string" },

	{ "type":"43", "PTC": 8, "PFC": 0, "name":"PTC 8 / PFC 0", "size":"0", "desc":"ASCII string; variable; variable-length character string" },
    { "type":"44", "PTC": 8, "PFC": -1, "name":"PTC 8 / PFC > 0", "size":"0", "desc":"ASCII string; PFC octets; fixed-length character string" },

	{ "type":"45", "PTC": 9, "PFC": 0, "name":"PTC 9 / PFC 0", "size":"0", "desc":"Absolute time; variable; absolute time based on its p-field (not supported by SCOS2000)" },
    { "type":"46", "PTC": 9, "PFC": 1, "name":"PTC 9 / PFC 1", "size":"48", "desc":"Absolute time; 6 octets; absolute time CDS format without microseconds" },
    { "type":"47", "PTC": 9, "PFC": 2, "name":"PTC 9 / PFC 2", "size":"64", "desc":"Absolute time; 8 octets; absolute time CDS format with microseconds" },
    { "type":"48", "PTC": 9, "PFC": 3, "name":"PTC 9 / PFC 3", "size":"8", "desc":"Absolute time; 1 octet; absolute time CUC format (1 Byte coarse time)" },
    { "type":"49", "PTC": 9, "PFC": 4, "name":"PTC 9 / PFC 4", "size":"16", "desc":"Absolute time; 2 octets; absolute time CUC format (1 Byte coarse time)" },
    { "type":"50", "PTC": 9, "PFC": 5, "name":"PTC 9 / PFC 5", "size":"24", "desc":"Absolute time; 3 octets; absolute time CUC format (1 Byte coarse time)" },
    { "type":"51", "PTC": 9, "PFC": 6, "name":"PTC 9 / PFC 6", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (1 Byte coarse time)" },
    { "type":"52", "PTC": 9, "PFC": 7, "name":"PTC 9 / PFC 7", "size":"16", "desc":"Absolute time; 2 octets; absolute time CUC format (2 Bytes coarse time)" },
    { "type":"53", "PTC": 9, "PFC": 8, "name":"PTC 9 / PFC 8", "size":"24", "desc":"Absolute time; 3 octets; absolute time CUC format (2 Bytes coarse time)" },
    { "type":"54", "PTC": 9, "PFC": 9, "name":"PTC 9 / PFC 9", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (2 Bytes coarse time)" },
    { "type":"55", "PTC": 9, "PFC": 10, "name":"PTC 9 / PFC 10", "size":"40", "desc":"Absolute time; 5 octets; absolute time CUC format (2 Bytes coarse time)" },
    { "type":"56", "PTC": 9, "PFC": 11, "name":"PTC 9 / PFC 11", "size":"24", "desc":"Absolute time; 3 octets; absolute time CUC format (3 Bytes coarse time)" },
    { "type":"57", "PTC": 9, "PFC": 12, "name":"PTC 9 / PFC 12", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (3 Bytes coarse time)" },
    { "type":"58", "PTC": 9, "PFC": 13, "name":"PTC 9 / PFC 13", "size":"40", "desc":"Absolute time; 5 octets; absolute time CUC format (3 Bytes coarse time)" },
    { "type":"59", "PTC": 9, "PFC": 14, "name":"PTC 9 / PFC 14", "size":"48", "desc":"Absolute time; 6 octets; absolute time CUC format (3 Bytes coarse time)" },
    { "type":"60", "PTC": 9, "PFC": 15, "name":"PTC 9 / PFC 15", "size":"32", "desc":"Absolute time; 4 octet; absolute time CUC format (4 Bytes coarse time)" },
    { "type":"61", "PTC": 9, "PFC": 16, "name":"PTC 9 / PFC 16", "size":"40", "desc":"Absolute time; 5 octets; absolute time CUC format (4 Bytes coarse time)" },
    { "type":"62", "PTC": 9, "PFC": 17, "name":"PTC 9 / PFC 17", "size":"48", "desc":"Absolute time; 6 octets; absolute time CUC format (4 Bytes coarse time)" },
    { "type":"63", "PTC": 9, "PFC": 18, "name":"PTC 9 / PFC 18", "size":"56", "desc":"Absolute time; 7 octets; absolute time CUC format (4 Bytes coarse time)" },

	{ "type":"64", "PTC": 10, "PFC": 3, "name":"PTC 10 / PFC 3", "size":"8", "desc":"Relative time; 1 octet; relative time CUC format (1 Byte coarse time)" },
    { "type":"65", "PTC": 10, "PFC": 4, "name":"PTC 10 / PFC 4", "size":"16", "desc":"Relative time; 2 octets; relative time CUC format (1 Byte coarse time)" },
    { "type":"66", "PTC": 10, "PFC": 5, "name":"PTC 10 / PFC 5", "size":"24", "desc":"Relative time; 3 octets; relative time CUC format (1 Byte coarse time)" },
    { "type":"67", "PTC": 10, "PFC": 6, "name":"PTC 10 / PFC 6", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (1 Byte coarse time)" },
    { "type":"68", "PTC": 10, "PFC": 7, "name":"PTC 10 / PFC 7", "size":"16", "desc":"Relative time; 2 octets; relative time CUC format (2 Bytes coarse time)" },
    { "type":"69", "PTC": 10, "PFC": 8, "name":"PTC 10 / PFC 8", "size":"24", "desc":"Relative time; 3 octets; relative time CUC format (2 Bytes coarse time)" },
    { "type":"70", "PTC": 10, "PFC": 9, "name":"PTC 10 / PFC 9", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (2 Bytes coarse time)" },
    { "type":"71", "PTC": 10, "PFC": 10, "name":"PTC 10 / PFC 10", "size":"40", "desc":"Relative time; 5 octets; relative time CUC format (2 Bytes coarse time)" },
    { "type":"72", "PTC": 10, "PFC": 11, "name":"PTC 10 / PFC 11", "size":"24", "desc":"Relative time; 3 octets; relative time CUC format (3 Bytes coarse time)" },
    { "type":"73", "PTC": 10, "PFC": 12, "name":"PTC 10 / PFC 12", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (3 Bytes coarse time)" },
    { "type":"74", "PTC": 10, "PFC": 13, "name":"PTC 10 / PFC 13", "size":"40", "desc":"Relative time; 5 octets; relative time CUC format (3 Bytes coarse time)" },
    { "type":"75", "PTC": 10, "PFC": 14, "name":"PTC 10 / PFC 14", "size":"48", "desc":"Relative time; 6 octets; relative time CUC format (3 Bytes coarse time)" },
    { "type":"76", "PTC": 10, "PFC": 15, "name":"PTC 10 / PFC 15", "size":"32", "desc":"Relative time; 4 octet; relative time CUC format (4 Bytes coarse time)" },
    { "type":"77", "PTC": 10, "PFC": 16, "name":"PTC 10 / PFC 16", "size":"40", "desc":"Relative time; 5 octets; relative time CUC format (4 Bytes coarse time)" },
    { "type":"78", "PTC": 10, "PFC": 17, "name":"PTC 10 / PFC 17", "size":"48", "desc":"Relative time; 6 octets; relative time CUC format (4 Bytes coarse time)" },
    { "type":"79", "PTC": 10, "PFC": 18, "name":"PTC 10 / PFC 18", "size":"56", "desc":"Relative time; 7 octets; relative time CUC format (4 Bytes coarse time)" }
];


const edit_dialog_ids = [ "edit_datatype_domain",
						  "edit_datatype_name",
						  "edit_datatype_native_type",
						  "edit_datatype_size",
						  "edit_datatype_pustype",
						  "edit_datatype_value",
						  "edit_datatype_desc" ];

const edit_properties = [ "domain", "name", "nativeType",
						  "size", "setting", "value", "desc"];



function load_data()
{
	var types_handler = new TableHandler({
		table_id: "table_datatype",
		template_id: "table_datatype_row",
		properties: [ "id", "domain", "name",
					  "nativeType", "size", "pusparamtype", 
					  "value", "desc" ],
		modal_id: "datatype_modal",
		edit_dialog_ids: edit_dialog_ids,
		edit_properties: edit_properties,
		submit_button_id: "datatype_submit_button",
		create_button_id: "create_datatype_button_id",
		end_point: `api/v2/projects/${urlParams.get("idProject")}` +
				   `/standards/${urlParams.get("idStandard")}/datatypes`,
		empty_item: {},
		modal_filled_fn: update_pus_datatypes,
		create_item_from_modal_fn: create_item_from_modal
	});

	types_handler.load_items();
}


window.onload = load_data();


function create_item_from_modal(edit_item)
{
	for(let i = 0; i < edit_dialog_ids.length; i++)
		edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;

	const pustype = JSON.parse(edit_item.setting);
	edit_item.pusparamtype = `PTC/PFC: ${pustype.PUS.ptc}/${pustype.PUS.pfc}`;
}


// this whole thing is very hacky because it has to work with the previous system where
// pus datatypes are stored as json in setting column
// TODO: data model should be updated with proper table for pus data type
function update_pus_datatypes(item)
{
	const size_input = document.getElementById("edit_datatype_size");
	const pus_select = document.getElementById("edit_datatype_pustype");

	const size = size_input.value;

	for (let i = 0; i < pus_data.length; i++) {
		// size of pus datatype is the same as size of c datatype or pus data type is variable 
		if (size == pus_data[i].size || pus_data[i].size == -1) {
			// TODO: check boundaries of variable pus datatype
			const pfc = pus_data[i].PFC == -1 ? size : pus_data[i].PFC;
			const type = pus_data[i].type + "_" + pus_data[i].PTC + "_" + pfc;
			
			const opt = document.createElement("option");
			opt.text = pus_data[i].name + " (" + pus_data[i].desc + ")";
			opt.value = `{"PUS": {"type": "${type}", "ptc": ${pus_data[i].PTC}, "pfc": ${pfc}}}`

			pus_select.appendChild(opt);
		}
	}

	pus_select.value = item.setting;
}

