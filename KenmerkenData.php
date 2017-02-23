<?php
/*
Class KenmerkenData
    Doet alles wat met kenmerken te maken heeft:
        Genereert HTML t.b.v. kenmerken opvoeren/wijzigen/verwijderen
        Afhandeling van formulier t.b.v. kenmerken opvoeren/wijzigen/verwijderen
		
		
Als cardinality == 0..n, dan: 
	properties in GROUP_QUALITY_PROPERTIES
	GROUP_QUALITY.DETAILS = null
	GROUP_QUALITY.DESCRIPTION = waarde
Als cardinality == 0..1, dan: 
	Geen GROUP_QUALITY_PROPERTIES
	GROUP_QUALITY.DETAILS = waarde
	GROUP_QUALITY.DESCRIPTION = property
*/
class KenmerkenData {
	public $input = array();
	public	$data = array();
	private $data_row = array();
	private $database = null;
	
	public function __construct($db_name = APPLICATION_DB_NAME) {
        $this->connect($db_name);
	}
	public function __destruct() {
        $this->database = null;
	}
	private function connect($db_name) {
		if (!$this->database) {
			$this->database = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $db_name) ;
			if (mysqli_connect_errno()) {
				echo "Connect failed: " . mysqli_connect_error();
				exit();
			}
		}
	}
	private function profile_discriminator() {
		$query = $this->database->prepare("select discriminator from PROFILE where PROFILE_ID = ?");
		$query->bind_param("i", $this->data["profile_id"]);
                $query->execute();
		$query->bind_result($discriminator);
		$query->fetch();
		return $discriminator;
	}
	public function update_qualities() {
		$this->input["delete"] = array();
		if (StudentUtilities::post("delete")) {
			$this->input["delete"] = StudentUtilities::post("delete");
			foreach($this->input["delete"] as $group_quality_id => $value) {
				$this->delete_group_quality($group_quality_id);
			}
		}
		$this->input["quality"] = array();
		if (StudentUtilities::post("quality")) {
			$this->input["quality"] = StudentUtilities::post("quality");
			if (StudentUtilities::post("property")) {
				$properties = StudentUtilities::post("property");
				foreach($this->input["quality"] as $proto_id => $data) {
					$this->input["quality"][$proto_id]["properties"] = array();
					if (array_key_exists($proto_id, $properties)) {
						if (is_array($properties[$proto_id])) {
							$this->input["quality"][$proto_id]["properties"] = $properties[$proto_id];
						}
					}
				}
			}
			foreach($this->input["quality"] as $proto_id => $qualities) {
				foreach($qualities as $group_quality_id => $quality) {
					//	Is geen group_quality_id!
					if ("properties" === $group_quality_id) {
						continue;
					}
					//	Controleer of hij net verwijders is!
					if (array_key_exists($group_quality_id, $this->input["delete"])) {
						//	Ja, volgende!
						continue;
					}
					//	Controleer of GROUP_QUALITY_ID = 0, Zo ja, nieuwe aanmaken.
					if (!intval($group_quality_id)) {
						if ($quality) {
//							$this->new_group_quality($proto_id, Session::get("profile_id"));
							$this->new_group_quality($proto_id, StudentUtilities::post("profile_id"));
						}
					} else {
						$this->update_group_quality($proto_id, $group_quality_id);
					}
				}
			}
		}
	}
	private function new_group_quality($proto_id, $profile_id) {
        //  Controleer of er wel iets is!
        if (!$this->input["quality"][$proto_id][0]) {
            return false;
        }
		$quality_id = 0;
		$query = $this->database->prepare("
			select
				SHORTNAME,
				CARDINALITY,
				DETAILS,
				WEIGHT,
				CODE,
				DESCRIPTION_TYPE,
				PROPERTY_TYPE
			from PROTO_QUALITY 
			where ID = ?");
		$query->bind_param("i", $proto_id);
		$query->execute();
        $query->bind_result(
            $SHORTNAME,
            $CARDINALITY,
            $DETAILS,
            $WEIGHT,
            $CODE,
            $DESCRIPTION_TYPE,
            $PROPERTY_TYPE);
		$query->fetch();
		$query->close();
		switch($DESCRIPTION_TYPE) {
			case "text":
			case "radio":
			case "select":
				$details = null;
				$description = null;
				if ("[0..n]" == $CARDINALITY) {
					$description = $this->input["quality"][$proto_id][0];
				} else {
					$details = $this->input["quality"][$proto_id][0];
				}
				$insert = $this->database->prepare("
					insert into GROUP_QUALITY (
						CARDINALITY,
						DETAILS,
						WEIGHT,
						CODE,
						DESCRIPTION,
						SHORTNAME,
						OWNER_PROFILE_ID
					)
					values (
						?,
						?,
						?,
						?,
						?,
				        ?,
						?
					)");    
				$insert->bind_param("ssiissi",
					$CARDINALITY,
					$details,
					$WEIGHT,
					$CODE,
					$description,
					$SHORTNAME,
					$profile_id);
				$insert->execute();
				$quality_id = $this->database->insert_id;
				$insert->close();;
				break;
//			case "checkbox":
//				break;
		}
		switch($PROPERTY_TYPE) {
/*
			case "checkbox":
				$proto_props = $this->database->prepare("
					select ID, PROPERTIES_KEY from PROTO_QUALITY_PROPERTY
					where QUALITY_ID = ?
				");
				$proto_props->bind_param("i", $proto_id);
				$proto_props->execute();
				$proto_props->bind_result(
                    $ID,
				    $PROPERTIES_KEY
				);
				$proto_props_result = $proto_props->get_result();
				$proto_props->close();
				while($proto_prop_row = $proto_props_result->fetch_assoc()) {
					$ID = $proto_prop_row["ID"];
					$insert = $this->database->prepare("
						insert into GROUP_QUALITY_PROPERTIES (
							GROUP_QUALITY_GROUP_QUALITY_ID,
							PROPERTIES,
							PROPERTIES_KEY
						)
						values (
							?,
							?,
                            ?
						)
                        ");
					$value = "false";
					if (array_key_exists(0, $this->input["quality"][$proto_id]["properties"])) {
						if (array_key_exists($ID, $this->input["quality"][$proto_id]["properties"][0])) {
							$value = "true";
						}
					}
					$insert->bind_param("iss",
                        $quality_id,
                        $value,
                        $proto_prop_row["PROPERTIES_KEY"]
					);
					$insert->execute();
					$insert = null;
				}
				break;
*/
			case "text":
                //  Als CARDINALITY == [0..1] dan wordt de property in GROUP_QUALITY.DESCRIPTION opgeslagen!
				if ("[0..1]" == $CARDINALITY) {
					$update = $this->database->prepare("
						update GROUP_QUALITY
						set DESCRIPTION = ?
						where GROUP_QUALITY_ID = ?
					");
					$update->bind_param("si", 
						$this->input["quality"][$proto_id]["properties"][0], 
						$quality_id);
					$update->execute();
					$update->close();
				} else {
					$insert = $this->database->prepare("
						insert into GROUP_QUALITY_PROPERTIES (
							GROUP_QUALITY_GROUP_QUALITY_ID,
							PROPERTIES,
							PROPERTIES_KEY
						)
						values (
							?,
							?,
                            'TYPE'
						)
                        ");
					$value = "";
					if (array_key_exists(0, $this->input["quality"][$proto_id]["properties"])) {
							$value = $this->input["quality"][$proto_id]["properties"][0];
					}
					$insert->bind_param("is",
                        $quality_id,
                        $value
//                       $proto_prop_row["PROPERTIES_KEY"]
					);
					$insert->execute();
					$insert->close();
				}
				break;
		}
	}
	private function update_group_quality($proto_id, $group_quality_id) {
		$query = $this->database->prepare("select
                SHORTNAME,
                CARDINALITY,
                DETAILS,
                WEIGHT,
                CODE,
                DESCRIPTION_TYPE,
                PROPERTY_TYPE
            from PROTO_QUALITY where ID = ?
            ");
		$query->bind_param("i",
            $proto_id);
		$query->execute();
        $query->bind_result(
            $SHORTNAME,
            $CARDINALITY,
            $DETAILS,
            $WEIGHT,
            $CODE,
            $DESCRIPTION_TYPE,
            $PROPERTY_TYPE);
		$query->fetch();
		$query->close();
		switch($DESCRIPTION_TYPE) {
			case "radio":
			case "select":
			case "text":
				$column = null;
				$description = $this->input["quality"][$proto_id][$group_quality_id];
				if ("[0..n]" == $CARDINALITY) {
					$column = "DESCRIPTION";
				} else {
					$column = "DETAILS";
				}
				$description = $this->input["quality"][$proto_id][$group_quality_id];
				$update = $this->database->prepare("
					update GROUP_QUALITY
					set 
						" . $column . " = ?
					where GROUP_QUALITY_ID = ?
				");
				$update->bind_param("si", 
                    $description,
                    $group_quality_id);
				$update->execute();
				$update->close();
				break;
//			case "radio":
//				break;
		}
		switch($PROPERTY_TYPE) {
/*
			case "checkbox":
				$proto_props = $this->database->prepare("
					select ID, PROPERTIES_KEY from PROTO_QUALITY_PROPERTY
					where QUALITY_ID = ?
				");
				$proto_props->bind_param("i", $proto_id);
				$proto_props->execute();
                $proto_props->bind_result(
                    $ID,
                    $PROPERTIES_KEY
                );
				$proto_props_result = $proto_props->get_result();
				$proto_props->close();
				while($proto_prop_row = $proto_props_result->fetch_assoc()) {
					$ID = $proto_prop_row["ID"];
					$update = $this->database->prepare("
						update GROUP_QUALITY_PROPERTIES
						set PROPERTIES = ?
						where GROUP_QUALITY_GROUP_QUALITY_ID = ?
						and PROPERTIES_KEY = ?
					");
					$value = "false";
					if (array_key_exists($group_quality_id, $this->input["quality"][$proto_id]["properties"])) {
						if (array_key_exists($ID, $this->input["quality"][$proto_id]["properties"][$group_quality_id])) {
							$value = "true";
						}
					}
                    $update->bind_param("sis",
                        $value,
                        $group_quality_id,
                        $proto_prop_row["PROPERTIES_KEY"]
                    );
					$update->execute();
					$update = null;
				}
				break;
*/
			case "text":
				if ("[0..1]" == $CARDINALITY) {
					$update = $this->database->prepare("
						update GROUP_QUALITY
						set DESCRIPTION = ?
						where GROUP_QUALITY_ID = ?
					");
					$description = $this->input["quality"][$proto_id]["properties"][$group_quality_id];
					$update->bind_param("si",
						$description,
						$group_quality_id
					);
					$update->execute();
					$update->close();
				} else {
					$update = $this->database->prepare("
						update GROUP_QUALITY_PROPERTIES
						set PROPERTIES = ?
						where GROUP_QUALITY_GROUP_QUALITY_ID = ?
					");
					$value = "";
					if (array_key_exists($group_quality_id, $this->input["quality"][$proto_id]["properties"])) {
						$value = $this->input["quality"][$proto_id]["properties"][$group_quality_id];
					}
                    $update->bind_param("si",
                        $value,
                        $group_quality_id
                    );
					$update->execute();
					$update->close();
				}	
				break;
		}
	}
	private function delete_group_quality($group_quality_id) {
		$query = $this->database->prepare("
			delete from GROUP_QUALITY_PROPERTIES
			where GROUP_QUALITY_GROUP_QUALITY_ID = ?
		");
		$query->bind_param("i", $group_quality_id);
		$query->execute();
		$query = $this->database->prepare("
			delete from GROUP_QUALITY
			where GROUP_QUALITY_ID = ?
		");
		$query->bind_param("i", $group_quality_id);
		$query->execute();
	}
	public function show_qualities($profile_id) {

		$this->data["profile_id"] = $profile_id;
		$this->getGROUP_QUALITYs();
		$html = "";
		foreach($this->data["qualities"] as $proto_id => $qualities) {
			$html .= $this->html_quality($proto_id, $qualities);
		}
		return $html;
	}
	private function html_quality($proto_id, $qualities) {
		$delete = "";
		$input = "";
		if ("[0..n]" == $qualities["CARDINALITY"]) {
			$delete = '<span class="del_field"><input type="checkbox" name="delete[__quality_id__]" style="float: none; margin: 0 4px 0 16px;">' .
			__('DELETE', 'student') . "</span>"; 
		}
		$html = '
<fieldset name=fieldset_quality[' . $proto_id . '] class="fieldset-quality">
	<legend class="fieldset-quality-legend">'. __(sprintf("%s", $this->data["qualities"][$proto_id]["NAME"]), "student").'</legend>';
		$input .= $this->html_descriptions($proto_id, $qualities["CARDINALITY"], $delete);
		$html .= $input;
		$html .= '
</fieldset>
';
		$PROTO_QUALITY_DESCRIPTIONS = null;
		return $html;
	}
	private function html_descriptions($proto_id, $cardinality, $checkbox) {
		$qualities = $this->data["qualities"][$proto_id];
		$html = "";
		switch($qualities["DESCRIPTION_TYPE"]) {
		case "select":
			foreach($qualities["qualities"] as $quality_id => $row) {
				$html .= $this->generateSelect($proto_id, $quality_id, $qualities, $row["DESCRIPTION"], $cardinality, $checkbox);
			}
			if ("[0..n]" == $cardinality) {
				//	Extra invoerveld toevoegen
				$html .= $this->generateSelect($proto_id, 0, $qualities, "", $cardinality, "");
			} elseif (!sizeof($qualities["qualities"])) {
				$html .= $this->generateSelect($proto_id, 0, $qualities, "", $cardinality, $checkbox);
			}
			break;
		case "radio":
			foreach($qualities["qualities"] as $quality_id => $row) {
				$html .= $this->generateRadio($proto_id, $quality_id, $qualities, $row["DESCRIPTION"], $checkbox);
			}
			if ((!sizeof($qualities["qualities"])) || ("[0..n]" == $cardinality)) {
					$html .= $this->generateRadio($proto_id, 0, $qualities, "", "");
			}	
			break;
		case "text":
			foreach($qualities["qualities"] as $quality_id => $row) {
				$html .= $this->generateText($proto_id, $quality_id, $qualities, $row["DESCRIPTION"], $checkbox);
			}
			if ((!sizeof($qualities["qualities"])) || ("[0..n]" == $cardinality)) {
					$html .= $this->generateText($proto_id, 0, $qualities, "", "");
			}
			break;
		case "checkbox":
//          echo "<hr>qualities<br>";
//            var_dump($qualities);
//            echo "<br>qualities[descriptions]<br>";
//            var_dump($qualities["descriptions"]);
//            echo "<hr>";
//			foreach($qualities["descriptions"] as $quality_id => $row) {
//				$description = "";
//				if (isset($row["DESCRIPTION"])) {
//					$description = $row["DESCRIPTION"];
//				}
//			$html .= $this->generateCheckBox($proto_id, $quality_id, $qualities, $description, //$checkbox);
			$html .= $this->generateCheckBox($proto_id, $proto_id, $qualities["qualities"], $qualities["descriptions"], $checkbox);
//			}
			if ($checkbox) {
				//	Alleen als [0..n] relatie. Dan extra (leeg) invoerveld.
				$html .= $this->generateCheckBox($proto_id, 0, $qualities, "", "");
			}
			break;
		}
		return $html;
	}	
	private function generateText($proto_id, $quality_id, $qualities, $input_description, $checkbox) {
		$html = "";
		$html .= ('
		<input type="text" class="quality-text" ' . '
							name="quality[' . $proto_id . '][' . $quality_id . ']" value="' . $input_description . '">');
		if ($checkbox) {
			$checkbox = str_replace("__quality_id__", $quality_id, $checkbox);
		}
		$html .= $this->html_properties($proto_id, $quality_id) . $checkbox . "<br>";
		return $html;
	}
	private function generateSelect($proto_id, $quality_id, $qualities, $input_description, $cardinality, $checkbox) {
		$html = "";
		$html .= ('
			<select name="quality[' . $proto_id . '][' . $quality_id . ']" size="1" class="quality-select">');
//				if ("[0..n]" == $cardinality) {
					$input = $input_description;
					if (!$input_description) {
						$input = $qualities["DEFAULT_DESCRIPTION"];
					}
					foreach($qualities["proto_descriptions"] as $description_id => $description) {
						$selected = "";
						if ($description == $input) {
							$selected = " selected ";
						}
						$html .= ('
					<option value="' . $description . '"' . $selected . ' class="quality-select-option">'.
							__(sprintf("%s", $description), "student") . '
					</option>');
					}
//				}
				$html .= ('
			</select>');
		if ($checkbox) {
			$checkbox = str_replace("__quality_id__", $quality_id, $checkbox);
		}
		$html .= $this->html_properties($proto_id, $quality_id) . $checkbox . "<br>";
		return $html;
	}
	private function generateRadio($proto_id, $quality_id, $qualities, $input_description, $checkbox) {
//echo "<hr>proto_id=" . $proto_id . "qid=" . $quality_id . "<br>";
//var_dump($qualities);
//echo "<br>";
//var_dump($input_description);
//echo "<hr>";
		$html = "";
		$html .= '
			<fieldset class="fieldset-radio">';
		$input = $input_description;
		if (!$input_description) {
			$input = $qualities["DEFAULT_DESCRIPTION"];
		}
		foreach($qualities["proto_descriptions"] as $description_id => $description) {
			$checked = "";
			if ($input == $description) {
				$checked = " checked ";
			}
			$html .= ('
		<input type="radio" class="quality-radio" ' . '
							name="quality[' . $proto_id . '][' . $quality_id . ']" value="' . $description . '"' . 
					$checked . '>' . __(sprintf("%s", $description), "student"));
		}
		if ($checkbox) {
			$checkbox = str_replace("__quality_id__", $quality_id, $checkbox);
		}
		$html .= $this->html_properties($proto_id, $quality_id) . $checkbox . "<br>";
		$html .= '
			</fieldset>';
		return $html;
	}
	private function generateCheckBox($proto_id, $quality_id, $qualities, $descriptions, $checkbox) {
        
//echo "<hr><hr>proto_id = " . $proto_id . "<br>";
//echo "<br><hr>quality_id = " . $quality_id . "<br>";
//echo "<br><hr>qualities = <br>";
//var_dump($qualities);
//echo "<br><hr>descriptions = <br>";
//var_dump($descriptions);
//echo "<br><hr>checkbox = " . $checkbox . "<hr>";
		$html = "";
		$html .= '
			<fieldset class="fieldset-checkbox">';
		foreach($descriptions as $description_id => $description) {
			$checked = "";
//			if ($input_description == $description) {
//				$checked = " checked ";
//			}
			$html .= ('
		<input type="checkbox" class="quality-checkbox" ' . '
							name="quality[' . $proto_id . '][' . $quality_id . ']" value="' . $description . '"' . 
					$checked . '>' . __(sprintf("%s", $description), "student"));
		}
		if ($checkbox) {
			$checkbox = str_replace("__quality_id__", $quality_id, $checkbox);
		}
		$html .= '
			</fieldset>';
		return $html;
	}
	private function html_properties($proto_id, $quality_id) {
//echo "<hr>html_properties proto_id=" . $proto_id . "qid=" . $quality_id . "<br>";
		$qualities = $this->data["qualities"][$proto_id];
//var_dump($qualities);
//echo "<hr>";
		$html = "";
		switch($qualities["PROPERTY_TYPE"]) {
		case "checkbox":
			$html .= '
			<fieldset class="fieldset-property">';
			foreach($qualities["properties"] as $property_id => $property) {
				$checked = "";
				if (intval($quality_id)) {
					if ($qualities["qualities"][$quality_id]["properties"][$property] == "true") {
						$checked = " checked ";
					}
				}
				$html .= ('
			<input class="property-checkbox" type="checkbox" value="' .
							$property . '" .
							name="property[' . $proto_id . '][' . $quality_id . '][' . $property_id . ']"' . $checked . '>' .
							__(sprintf("%s", $property), "student"));
			}
			$html .= '
			</fieldset>';
			break;
		case "text":
//var_dump($qualities);
			$value = "";
			if (intval($quality_id)) {
				if (is_array($qualities["qualities"][$quality_id]["properties"])) {
					$value = $qualities["qualities"][$quality_id]["properties"]["TYPE"];
				} else {
					$value = $qualities["qualities"][$quality_id]["properties"];
				}
			}
			$html .= ('
		<input class="property-text" type="text" 
			name="property[' . $proto_id .'][' . $quality_id . ']" value="' . $value . '">');
			break;
		}
		return $html;
	}	
	public function to_session() {
		//	Store data in $_SESSION
		Session::set('application_data', $this->data);
	}
	public function from_session() {
		//	Get student data from $_SESSION
		$this->application_data = Session::get('application_data');
		if (is_array($this->data)) {
			return true;
		}
		else {
			$this->data = array();
			return false;
		}
	}
	public function from_input() {
		$this->data = array();
		$this->data["HOST"] = array();
		$this->data_row = array();
		$this->kenmerkenPerDiscriminator("HOST" , StudentUtilities::post("kenmerken")["HOST"]);
		$this->data["GUEST"] = array();
		$this->data_row = array();
		$this->kenmerkenPerDiscriminator("PROFILE", StudentUtilities::post("kenmerken")["PROFILE"]);
	}
	private function kenmerkenPerDiscriminator($discriminator, $input) {
		foreach($input as $row) {
			if ($this->kenmerkenRow($row)) {
				$this->data[$discriminator][] = $this->data_row;
			}
		}
	}	
	private function kenmerkenRow($input) {
		$qd_ok = false;
		$qdp_ok = true;
		if (isset($input["qd"])) {
			foreach($input["qd"] as $quality_id => $input_description) {
				$qd_ok = $this->quality_description($quality_id, $input_description);
			}
		}
		if ($qd_ok) {
			if (isset($input["qdp"])) {
				foreach($input["qdp"] as $property_id => $input_property) {
					$qdp_ok = $this->quality_property($property_id, $input_property);
				}
			}
		}
		if ($qd_ok && $qdp_ok) {
			return true;
		}
		else {
			return false;
		}
	}
	private function quality_description($quality_id, $input_description) {
		$query_proto_quality = null;
		$query_proto_quality_description = null;
		$query_proto_property = null;
		$return = true;
		$query_proto_quality = $this->database->prepare("select
                	SHORTNAME,
                	CARDINALITY,
                	DETAILS,
                	WEIGHT,
                	CODE,
                	DESCRIPTION_TYPE,
			DEFAULT_DESCRIPTION,
                	PROPERTY_TYPE
		from PROTO_QUALITY where ID = ?");
		$query_proto_quality->bind_param("i", $quality_id);
		$query_proto_quality->execute();
		$query_proto_quality->bind_result(
			$SHORTNAME,
			$CARDINALITY,
			$DETAILS,
			$WEIGHT,
			$CODE,
			$DESCRIPTION_TYPE,
			$DEFAULT_DESCRIPTION,
			$PROPERTY_TYPE);
		if ($query->fetch()) {
			switch ($DESCRIPTION_TYPE) {
			case "radio":
			case "select":
				if ("" == $input_description) {
					//	Geen keuze gemaakt
					$return = false;
				}
				$query_proto_quality_description = $this->database->prepare("
					select DESCRIPTION
					from PROTO_QUALITY_DESCRIPTION
					where QUALITY_ID = ?
					and DESCRIPTION = ?
				");
				$query_proto_quality_description->bind_param("is", 
					$quality_id,
					$input_description);
				$query_proto_quality_description->execute();
				$query_proto_quality_description->bind_result($DESCRIPTION);
				if ($query_proto_quality_description->rowCount()) {
					$query_proto_quality_description->fetch();
					$this->data_row["ID"] = $quality_id;
					$this->data_row["PROPERTY_TYPE"] = $PROPERTY_TYPE;
					$this->data_row["DESCRIPTION_TYPE"] = $DESCRIPTION_TYPE;
					$this->data_row["CARDINALITY"] = $CARDINALITY;
					$this->data_row["DETAILS"] = $DETAILS;
					$this->data_row["WEIGHT"] = $WEIGHT;
					$this->data_row["CODE"] = $CODE;
					$this->data_row["DESCRIPTION"] = $DESCRIPTION;
					$this->data_row["SHORTNAME"] = $SHORTNAME;
					$this->data_row["properties"] = array();
					switch ($proto_quality->PROPERTY_TYPE) {
					case "checkbox":
						$query_proto_property = $this->database->prepare("
							select PROPERTIES_KEY from PROTO_QUALITY_PROPERTY
							where QUALITY_ID = ?
						");
						$query_proto_property->bind_param("i", $quality_id);
						$query_proto_property->execute();
						$query_proto_property->bind_result($PROPERTIES_KEY);
						while ($query_proto_property->fetch()) {
							$this->data_row["properties"]["$PROPERTIES_KEY"] = array(
								"properties_key" => $PROPERTIES_KEY,
								"properties" => "false"
							);
						}
						break;
					case "text":
						$this->data_row["properties"]["text"] = array(
							"properties_key" => "TYPE",
							"properties" => "");
						break;
					}
				}	
				break;
			default:
				$query_proto_property = null;
				$return = false;
			}
		}
		$query_proto_quality = null;
		$query_proto_quality_description = null;
		$query_proto_property = null;
		return $return;
	}
	private function quality_property($property_id, $input_property) {
		switch($this->data_row["PROPERTY_TYPE"]) {
		case "text":
			if ("" == trim($input_property)) {
				return false;
			}
			$this->data_row["properties"]["text"]["properties"] = $input_property;
			break;
		case "checkbox":
			$this->data_row["properties"][$property_id]["properties"] = "true";
			break;
		}
		return true;
	}
	public function storeKenmerken($applicationcode, $person_id, $address_id) {
		$group_id = $this->storeGROUPING($person_id);
		if (sizeof($this->data["HOST"])) {
			$profile_id = $this->storePROFILE($applicationcode, $group_id, $address_id, "HOST");
			foreach($this->data["HOST"] as $group_quality) {
				$group_quality_id = $this->storeGROUP_QUALITY($profile_id, $group_quality);
				foreach($group_quality["properties"] as $properties) {
					$this->storeGROUP_QUALITY_PROPERTIES($group_quality_id, $properties);
				}
			}
		}
		if (sizeof($this->data["GUEST"])) {
			$profile_id = $this->storePROFILE($applicationcode, $group_id, $address_id, "GUEST");
			foreach($this->data["GUEST"] as $group_quality) {
				$group_quality_id = $this->storeGROUP_QUALITY($profile_id, $group_quality);
				foreach($group_quality["properties"] as $properties) {
					$this->storeGROUP_QUALITY_PROPERTIES($group_quality_id, $properties);
				}
			}
		}
	}
	private function storeGROUP_QUALITY($profile_id, $group_quality) {
		$query = $this->database->prepare("
			insert into GROUP_QUALITY (
				CARDINALITY,
				DETAILS,
				WEIGHT,
				CODE,
				DETAILS,
				DESCRIPTION,
				SHORTNAME,
				OWNER_profile_id
			)
			values (
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?
			)
		");
        $query->bind_param("ssiisssi",
            $group_quality["CARDINALITY"],
            $group_quality["DETAILS"],
            $group_quality["WEIGHT"],
            $group_quality["CODE"],
            $group_quality["DETAILS"],
            $group_quality["DESCRIPTION"],
            $group_quality["SHORTNAME"],
            $profile_id
        );
		$query->execute();
		$id = $this->database->insert_id;
		$query = null;
		return $id;
	}
	private function storeGROUP_QUALITY_PROPERTIES($group_quality_id, $properties) {
		$query = $this->database->prepare("
			insert into GROUP_QUALITY_PROPERTIES (
				GROUP_QUALITY_GROUP_QUALITY_ID,
				PROPERTIES,
				PROPERTIES_KEY
			)
			values (
				?,
				?,
				?
			)
		");
		$query->bind_param("iss",
            $group_quality_id,
            $properties["properties"],
            $properties["properties_key"]
        );
		$query->execute();
		$query = null;
	}
	private function getGROUP_QUALITYs() {
		$host_or_guest = "IS_GUEST_QUALITY";
		if ("HOST" == $this->profile_discriminator()) {
			$host_or_guest = "IS_HOST_QUALITY";
		}
		$protos = null;
		$group_qualities = null;
		$group_quality_properties = null;
		$this->data["qualities"] = array();
		$protos = $this->database->prepare("
			select
				ID,
				NAME,
				SHORTNAME,
				CARDINALITY,
				PROPERTY_TYPE,
				DESCRIPTION_TYPE,
				DEFAULT_DESCRIPTION
			from PROTO_QUALITY
			where " . $host_or_guest . " = 1 
 			order by SEQUENCE");
		$protos->execute();
		$protos->bind_result(
			$ID,
			$NAME,
			$SHORTNAME,
			$CARDINALITY,
			$PROPERTY_TYPE,
			$DESCRIPTION_TYPE,
			$DEFAULT_DESCRIPTION);
		$data = array();
		$protos_result = $protos->get_result();
		$protos->close();
		while ($proto_row = $protos_result->fetch_assoc()) {
			$ID = $proto_row["ID"];
			$data[$ID] = array(
				"NAME" => $proto_row["NAME"],
				"SHORTNAME" => $proto_row["SHORTNAME"],
				"CARDINALITY" => $proto_row["CARDINALITY"],
				"PROPERTY_TYPE" => $proto_row["PROPERTY_TYPE"],
				"DESCRIPTION_TYPE" => $proto_row["DESCRIPTION_TYPE"],
				"DEFAULT_DESCRIPTION" => $proto_row["DEFAULT_DESCRIPTION"],
				"proto_descriptions" => array(),
				"proto_properties" => array(),
				"qualities" => array()
			);
			$proto_descriptions = $this->database->prepare("
				select ID, DESCRIPTION from PROTO_QUALITY_DESCRIPTION
				where QUALITY_ID = ?
				order by SEQUENCE
			");
			$proto_descriptions->bind_param("i", $ID);
			$proto_descriptions->execute();
			$proto_descriptions->bind_result($ID, $DESCRIPTION);
			$data_descriptions = array();
			$proto_descriptions_result = $proto_descriptions->get_result();
			$proto_descriptions->close();
			while($proto_description_row = $proto_descriptions_result->fetch_assoc()) {
				$QID = $proto_description_row["ID"];
				$data_descriptions[$QID]= $proto_description_row["DESCRIPTION"];
			}
			$data[$ID]["proto_descriptions"] = $data_descriptions;
			$proto_properties = $this->database->prepare("
				select ID, PROPERTIES_KEY from PROTO_QUALITY_PROPERTY
				where QUALITY_ID = ?
				order by SEQUENCE
			");
			$proto_properties->bind_param("i", $ID);
			$proto_properties->execute();
			$proto_properties->bind_result($PID, $PROPERTIES_KEY);
			$data_properties = array();
			$proto_properties_result = $proto_properties->get_result();
			$proto_properties->close();
			while($proto_propertie_row = $proto_properties_result->fetch_assoc()) {
				$PID = $proto_propertie_row["ID"];
				$data_properties[$PID] = $proto_propertie_row["PROPERTIES_KEY"];
			}
			$data[$ID]["proto_properties"] = $data_properties;
		}
		foreach($data as $proto_id => &$quality) {	// N.B. BY REFERENCE!
			//	get GROUP_QUALITYs
			$group_qualities = $this->database->prepare("
				select
					GROUP_QUALITY_ID,
					DESCRIPTION,
					DETAILS
				from GROUP_QUALITY
				where OWNER_profile_id = ?
				and SHORTNAME = ?
			");
			$group_qualities->bind_param("is", 
				$this->data["profile_id"],
				$quality["SHORTNAME"]);
			$group_qualities->execute();
			$group_qualities->bind_result(
				$GROUP_QUALITY_ID,
				$DESCRIPTION,
				$DETAILS);
			$group_qualities_result = $group_qualities->get_result();
			$group_qualities->close();
			while ($group_quality_row = $group_qualities_result->fetch_assoc()) {
				$group_quality_id = $group_quality_row["GROUP_QUALITY_ID"];
				$description = $group_quality_row["DESCRIPTION"];
				$properties = null;
				if ("[0..1]" == $quality["CARDINALITY"]) {
					$properties = $group_quality_row["DESCRIPTION"];
					$description = $group_quality_row["DETAILS"];
				} else {
					$properties = array();
				}
				$quality["qualities"][$group_quality_id] = array(
					"DESCRIPTION" => $description,
					"properties" => $properties
				);
				if (is_array($properties)) {
					$group_quality_properties = $this->database->prepare("
						select PROPERTIES, PROPERTIES_KEY from GROUP_QUALITY_PROPERTIES
						where GROUP_QUALITY_GROUP_QUALITY_ID = ?
					");
					$group_quality_properties->bind_param("i", $group_quality_id);
					$group_quality_properties->execute();
					$group_quality_properties->bind_result($PROPERTIES, $PROPERTIES_KEY);
					$group_quality_properties_result = $group_quality_properties->get_result();
					$group_quality_properties->close();
					while ($group_quality_property_row = $group_quality_properties_result->fetch_assoc()) {
						$quality["qualities"][$group_quality_id]["properties"][$group_quality_property_row["PROPERTIES_KEY"]] =
							$group_quality_property_row["PROPERTIES"];
					}
				}
			}
			$this->data["qualities"][$proto_id] = $quality;
		}
	}
}
