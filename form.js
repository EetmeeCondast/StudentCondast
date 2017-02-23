function MarkValidationErrors(errors) {
	if (!errors) {
		return;
	}
	for (var key in errors) {
		var message = errors[key];
		var json_obj;
		try {
			json_obj = JSON.parse(message);
		} catch (e) {
			json_obj = null;
		}
		if (!json_obj) {
			item = document.getElementsByName(key)[0];
			if (item) {
				item.className = "studenterror"
			}
		} else {
			for(var idx in json_obj) {
				field = json_obj[idx];
				for (var name in field) {
					itemname = key + "[" + idx + "][" + name + "]";
					item = document.getElementsByName(itemname)[0];
					item.className = "studenterror";
				}
			}
		}
	}
}
// adres ophalen door middel van postcode huisnummer
function getAdres() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        var jsonData = JSON.parse(this.responseText);
        var city = jsonData["_embedded"]["addresses"][0]["city"]["label"];
        var street = jsonData["_embedded"]["addresses"][0]["street"];
        var long = jsonData["_embedded"]["addresses"][0]["geo"]["center"]["wgs84"]["coordinates"][0] ;
        var lat = jsonData["_embedded"]["addresses"][0]["geo"]["center"]["wgs84"]["coordinates"][1] ;
       document.getElementById("town").value = city;
       document.getElementById("street").value = street;
       document.getElementById("longtitude").value = long;
       document.getElementById("latitude").value = lat;
       //console.log(jsonData["_embedded"]["addresses"][0]["geo"]["center"]["wgs84"]["coordinates"]);
       
    }
  }
 var pc = document.getElementById("postcode").value;
 var hnr = document.getElementById("number").value;
  xhttp.open("GET", "https://postcode-api.apiwise.nl/v2/addresses?postcode=" + pc + "&number=" + hnr, true);
  xhttp.setRequestHeader("X-Api-Key", "aABHjjWGDR6mNCw907V046HnTBitIzWc7z91yPuu");
  xhttp.send();
}


function append_contacts(contacts) {
	var tbody = document.getElementById("contacts_tbody");
	var src_row = document.getElementById("contact_row");
	//
	//	json-array contacts verwerken.
	for (idx in contacts) {
		var contact = contacts[idx];
		//
		//	Geen data. Verlaat loop.
		if ("object" != typeof contact) {
			continue;
		}
		//	Geen data. Verlaat loop.
		if (!contact.hasOwnProperty('app_person_id')) {
			continue;
		}
		//
		//	Clone source <tr>
		var new_row = src_row.cloneNode(true);
		new_row.setAttribute("id", undefined);
		//
		//	Eerste <td> in nieuwe <tr>
		var td = new_row.firstElementChild;
		//
		//	Eerst <input type='hidden'> met APPLICATIONPERSON.APP_PERSON_ID
		var inp = td.firstElementChild;
		inp.setAttribute("name", "contacts[" + idx + "][app_person_id]");
		inp.value = contact["app_person_id"];
		//
		//	Ten tweede <input type='hidden'> met ApplicationPerson_CONTACTS.APPLICATION
		var inp = inp.nextElementSibling;
		inp.setAttribute("name", "contacts[" + idx + "][application]");
		inp.value = contact["application"];
		//
		//	Ten derde <select> met contact type
		inp = inp.nextElementSibling;
		inp.setAttribute("name", "contacts[" + idx + "][contacttype]");
		for(var i = 0; i < inp.options.length; i++) {
			if (contact["contacttype"] == inp.options[i].getAttribute("value")) {
				inp.selectedIndex = i;
				break;
			}
		}
		//
		//	Volgende <td> in nieuwe <tr>
		td = td.nextElementSibling;
		//
		//	Eerste <input type='text'> met contact waarde
		inp = td.firstElementChild;
		inp.setAttribute("name", "contacts[" + idx + "][value]");
		inp.value = contact["value"];
		//
		//	Volgende <td> in nieuwe <tr>
		td = td.nextElementSibling;
		//
		//	Eerste <input type='checkbox'> met contact beperkt
		inp = td.firstElementChild;
		inp.setAttribute("name", "contacts[" + idx + "][restricted]");
		if (contact["restricted"]) {
			inp.checked = true;
		}
		//
		//	Voeg nieuwe <tr> toe vóór source row.
		tbody.insertBefore(new_row, src_row);
		idx++;
	}
}
var maxid = 0;
var groups = [];
function append_data(group, data) {
	//	Bepaal te kopiëren node en zijn parent. 
	//	<table id='group_node' ...>
	nodeid = group + "_node";
	var src_node = document.getElementById(nodeid);
	groups.push(src_node.cloneNode(true));
	var parentnode = src_node.parentNode;
	//
	//	json-array met gegevens verwerken.
	//	idx dient als index voor de name.
	//
	for (var idx in data) {
		//	Clone source node en geef id "undefined" aan de nieuwe node.
		var new_node = src_node.cloneNode(true);
		new_node.id = "node_" + maxid;
		maxid++;
		//	Verwerk 1 data rij.
		//
		var rij = data[idx];
		for(var key in rij) {
			var value = rij[key];
			//
			//	Zoek binnen de nieuwe node per item de/het bijbehorende invoerveld(en).
			//	<input class='group_xxx' type='yyy' name='group[0][xxx]'>
			//
			var input_objects = new_node.getElementsByClassName(group + "_" + key);
			for (var i = 0; i < input_objects.length; i++) {
				input_object = input_objects[i];
				input_object.name = group + "[" + idx + "][" + key + "]";
				switch(input_object.type) {
					case "select-one":
						for (var j = 0; j < input_object.length; j++) {
							if (input_object[j].value == value) {
								input_object.selectedIndex = j;
								break;
							}
						}
						break;
					case "checkbox":
						if (Array.isArray(value)) {
							if (value.indexOf(input_object.value) >= 0) {
								input_object.checked = true;
							}
						} else {
							//	Dit is een switch. Kan alleen maar 0 of 1 zijn.
							if (value) {
								input_object.checked = true;
							}
						}
						break;
					default:
						input_object.value = value;
				}
			}
		}
		var button_objects = new_node.getElementsByClassName("add_delete_button");
		if (button_objects.length) {
			button_object = button_objects[0];
			button_object.setAttribute("onClick", 'javascript: delete_node("' + new_node.id + '"); return false;');
			button_object.textContent = "DEL";
		}
		parentnode.insertBefore(new_node, src_node);
	}
}
function add_node(id) {
	var src_node = document.getElementById(id);
	var parentnode = src_node.parentNode;	//	<form...
	src_node.id = "node_" + maxid;
	maxid++;
//	var new_node = src_node.cloneNode(true);
//	var original_node = null;
//	new_node.id = "node_" + maxid;
//	var group_index = 0;
	for (var group_index = 0; group_index < groups.length; group_index++) {
		var group = groups[group_index];
		if (group.id == id) {
			original_node = group.cloneNode(true);
			break;
		}
	}
	var button_objects = src_node.getElementsByClassName("add_delete_button");
	if (button_objects.length) {
		button_object = button_objects[0];
		button_object.setAttribute("onClick", 'javascript: delete_node("' + src_node.id + '"); return false;');
		button_object.textContent = "DEL";
	}
//	parentnode.insertBefore(original_node, src_node);
//	parentnode.removeChild(src_node);
//	src_selects = src_node.getElementsByTagName("select");
//	new_selects = new_node.getElementsByTagName("select");
//	for (var j = 0; j < src_selects.length; j++) {
//		select = src_selects[j];
//		for (var k = 0; k < select.length; k++) {
//			option = select[k];
//			if (option.value == select.value) {
//				new_selects[j].selectedIndex = k;
//			}
//		}
//	}
	parentnode.appendChild(original_node);
}
function delete_node(id) {
	var src_node = document.getElementById(id);
	var parentnode = src_node.parentNode;
	parentnode.removeChild(src_node);
}
