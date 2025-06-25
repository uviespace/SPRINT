START TRANSACTION;

ALTER TABLE component ADD COLUMN componentSettings JSON;

UPDATE component SET componentSettings = '[
	   { "node_name": "LaTeX", "type": "object", "children": [ { "node_name": "Enabled", "type": "bool" } ] },
	   { "node_name": "CSV", "type": "object", "children": [
	   	 { "node_name": "Enabled", "type": "bool" },
		 { "node_name": "Separator", "type": "text" },
		 { "node_name": "Delimiter", "type": "text" }
	   ] }
]' WHERE id = 1;

UPDATE component SET componentSettings = '[
	   { "node_name": "prefix", "type": "text" },
	   { "node_name": "author", "type": "text" },
	   { "node_name": "copyright", "type": "text" },
	   { "node_name": "param_attr", "type": "text" },
	   { "node_name": "var_attr", "type": "text" },
	   { "node_name": "max_line_length", "type": "number" },
	   { "node_name": "indent", "type": "number" },
	   { "node_name": "includes", "type": "array" }
]' WHERE id = 2;

UPDATE component SET componentSettings = '[
		{
				"node_name": "general",
				"type": "object",
				"children": [
						{ "node_name": "release", "type": "number" },
						{ "node_name": "issue", "type": "number" },
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "packet_prefix", "type": "text" }
				]
		},
		{
				"node_name": "txf",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "paf",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "pcf",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "pcpc",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "ccf",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "cpc",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "pid",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "prf",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "dp2",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "caf",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		},
		{
				"node_name": "cvp",
				"type": "object",
				"children": [
						{ "node_name": "preamble", "type": "text" },
						{ "node_name": "length", "type": "number" },
						{ "node_name": "offset", "type": "number" }
				]
		}	
]' WHERE id = 4;


UPDATE component SET componentSettings = '[
	   { "node_name": "prefix", "type": "text" },
	   { "node_name": "author", "type": "text" },
	   { "node_name": "copyright", "type": "text" },
	   { "node_name": "max_line_length", "type": "number" },
	   { "node_name": "indent", "type": "number" },
	   { "node_name": "struct_attr", "type": "text"},
	   { "node_name": "endian", "type": "object", "children": [{ "node_name": "swap", "type": "bool"}, { "node_name": "fnc", "type": "text" } ]},
	   { "node_name": "crc_size", "type": "number" },
	   { "node_name": "includes", "type": "array" }
]' WHERE id = 5;

UPDATE component SET componentSettings = '[
	   { "node_name": "prefix", "type": "text" },
	   { "node_name": "author", "type": "text" },
	   { "node_name": "copyright", "type": "text" },
	   { "node_name": "max_line_length", "type": "number" },
	   { "node_name": "indent", "type": "number" },
	   { "node_name": "CrFwOutFactoryMaxNOfOutCmp", "type": "number" },
	   { "node_name": "CrFwInFactoryMaxNOfOutCmd", "type": "number" },
	   { "node_name": "CrFwInFactoryMaxNOfInRep", "type": "number" },
	   { "node_name": "CrFwOutRegistryN", "type": "number" },
	   { "node_name": "includes", "type": "array" }
]' WHERE id = 6;


UPDATE component SET componentSettings = '[
	   { "node_name": "prefix", "type": "text" },
	   { "node_name": "author", "type": "text" },
	   { "node_name": "copyright", "type": "text" },
	   { "node_name": "param_attr", "type": "text" },
	   { "node_name": "var_attr", "type": "text" },
	   { "node_name": "max_line_length", "type": "number" },
	   { "node_name": "indent", "type": "number" },
	   { "node_name": "includes", "type": "array" }
]' WHERE id = 7;


COMMIT;
