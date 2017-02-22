<?php
	class PersonModel {
/*
	Jan Osnabrugge, oktober 2016
	Project Student van Stichting Eet Mee!
	
	Gegevens personen database
*/
	private $connection = null;

	private $data = array();

	public function __construct() {
		$this->data["person_id"] = 0;
		$this->data["birthdate"] = date('Y-m-d', strtotime('-18 years'));
		$this->data["createdate"] = "";
		$this->data["gender"] = "gender_vrouw";	
		$this->data["title"] = "title_mevr";
		$this->data["calling_name"] = "";
		$this->data["firstname"] = "";
		$this->data["prefix"] = "";
		$this->data["surname"] = "";
		$this->data["street"] = "";
		$this->data["street_ext"] = "";
		$this->data["postcode"] = "";
		$this->data["number"] = "";
		$this->data["extension"] = "";
		$this->data["town"] = "";
		$this->data["country"] = "THE_NETHERLANDS";
		$this->data["addresses_key"] = "MAIN";
		$this->data["description"] = "";
		$this->data["nature"] = "STUDENT";
		$this->data["upas"] = "";
		$this->data["app_app_person_id"] = array();
		$this->data["contacts"] = array();
		$this->data["longtitude"] = null;
		$this->data["latitude"] = null;
	}
	public function __destruct() {
		if ($this->connection) {
			$this->connection->close();
		}
	}
/*
	Accessor methods.
	Spreken voor zichzelf.
*/
	public function person_id() { 		return $this->data["person_id"]; }
	public function birthdate() { 		return substr($this->data["birthdate"], 0, 10); }
	public function createdate() { 		return $this->data["createdate"]; }
	public function gender() { 			return $this->data["gender"]; }
	public function title() { 			return $this->data["title"]; }
	public function calling_name() { 	return $this->data["calling_name"]; }
	public function firstname() { 		return $this->data["firstname"]; }
	public function prefix() { 			return $this->data["prefix"]; }
	public function surname() { 		return $this->data["surname"]; }
	public function street() { 		return $this->data["street"]; }
	public function street_ext() { 		return $this->data["street_ext"]; }
	public function postcode() { 		return $this->data["postcode"]; }
	public function number() { 		return $this->data["number"]; }
	public function extension() { 		return $this->data["extension"]; }
	public function town() { 		return $this->data["town"]; }
	public function addresses_key() { 		return $this->data["addresses_key"]; }
	public function description() { 	return $this->data["description"]; }
	public function nature() { 			return $this->data["nature"]; }
	public function upas() { 			return $this->data["upas"]; }
	public function longtitude() { 			return $this->data["longtitude"]; }
	public function latitude() { 			return $this->data["latitude"]; }
	public function app_person_id($application_code) {
		$app_person_id = 0;
		if (array_key_exists($application_code, $this->data["app_app_person_id"])) {
			$app_person_id =  $this->data["app_app_person_id"][$application_code];
		}
		return $app_person_id; 
	}
	public function contacts() {
		return $this->data["contacts"]; 
	}
/*
	Verbind aan database.
	Constanten m.b.t. de database zijn gedefinieerd in wp-config.php.
*/
	private function connect() {
		if (!$this->connection) {
			$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, EETMEE_DB_NAME) ;
			if (mysqli_connect_errno()) {
				echo "Connect failed: " . mysqli_connect_error();
				exit();
			}
		}
	}
/*
	Haal gegevens uit database.
	Invoer: PERSON.PERSON_ID.
	Return: true bij succes, false bij niet gevonden.
*/	
	public function from_database($person_id) {
		if (!$person_id) {
			return false;
		}
		if (!preg_match("/^[0-9]+$/", $person_id)) {
			return false;
		}
		$this->connect();
		$stmt = $this->connection->prepare("select
				PERSON_ID,
				BIRTHDATE,
				CREATEDATE,
				GENDER,
				TITLE,
				CALLING_NAME,
				FIRSTNAME,
				PREFIX,
				SURNAME,
				DESCRIPTION,
				NATURE,
				UPAS
			from PERSON where PERSON_ID = ?");
		$stmt->bind_param("i", $person_id);
		$stmt->execute();
		$stmt->bind_result(
			$this->data["person_id"],
			$this->data["birthdate"],
			$this->data["createdate"],
			$this->data["gender"],
			$this->data["title"],
			$this->data["calling_name"],
			$this->data["firstname"],
			$this->data["prefix"],
			$this->data["surname"],
			$this->data["description"],
			$this->data["nature"],
			$this->data["upas"]);
		if (!$stmt->fetch()) {
			//
			//	Geen persoon gevonden. Sluit statement en return.
			$stmt->close();
			return false;
		}
		$stmt->close();
       
        /*
            Haal PERSON_ADDRESS op
        */
            $person_address_stmt = $this->connection->prepare("
			select
				EXTENSION,
				LATITUDE,
                LONGTITUDE,
                ADDRESS_ADDRESS_ID,
                NUMBER
			from PERSON_ADDRESS 
			where PERSON_PERSON_ID = ?");
		$person_address_stmt->bind_param("i", $person_id);
		$person_address_stmt->execute();
		$person_address_stmt->bind_result(
            $this->data["extension"],
			$this->data["latitude"],
			$this->data["longtitude"],
			$address_address_id,
			$this->data["number"]);
        $person_address_stmt->fetch();
         $person_address_stmt->close();
        
        /*
            Haal ADDRESS OP
        */
        $address_stmt = $this->connection->prepare("
			select
				POSTCODE,
				STREET,
                TOWN
			from ADDRESS 
			where ADDRESS_ID = ?");
		$address_stmt->bind_param("i", $address_address_id);
		$address_stmt->execute();
		$address_stmt->bind_result(
            $this->data["postcode"],
			$this->data["street"],
			$this->data["town"]);
        $address_stmt->fetch();
        $address_stmt->close();
		/*
			Haal APPLICATION_PERSONs op.
		*/
		$this->data["app_app_person_id"] = array();
		$appprs_stmt = $this->connection->prepare("
			select
				APP_PERSON_ID,
				CODE
			from APPLICATIONPERSON 
			where PERSON = ?");
		$appprs_stmt->bind_param("i", $person_id);
		$appprs_stmt->execute();
		$appprs_stmt->bind_result(
			$APP_PERSON_ID,
			$CODE);
		while($appprs = $appprs_stmt->fetch()) {
			$this->data["app_app_person_id"][$CODE] = $APP_PERSON_ID;
		}
        
		/*
			Haal per applicatie de contacten van de persoon op
		*/
		$contact_stmt = $this->connection->prepare("
			select
				APPLICATION,
				CONTACTTYPE,
				RESTRICTED,
				VALUE
			from ApplicationPerson_CONTACTS 
			where ApplicationPerson_APP_PERSON_ID = ?");
		$this->data["contacts"] = array();
		$idx = 1;
		foreach($this->data["app_app_person_id"] as $app_code => $app_person_id) {
			$contact_stmt->bind_param("i", $app_person_id);
			$contact_stmt->execute();
		$contact_stmt->bind_result(
            $app_code,
			$CONTACTTYPE,
			$RESTRICTED,
            $VALUE);
			while ($contact_stmt->fetch()) {
				$this->data["contacts"][$idx] = array(
					"app_person_id" => $app_person_id,
					"application" => $app_code,
					"contacttype" => $CONTACTTYPE,
					"restricted" => $RESTRICTED,
					"value" => $VALUE);
				$idx++;
			}
		}
		$contact_stmt->close();
		$appprs_stmt->close();
		return true;
	}
/*
	Retourneert array met persoonsgegevens uit object
*/
	public function get_data() {
		$data = array();
		foreach($this->data as $key => $value) {
			$data[$key] = $this->data[$key];
		}
		return $data;
	}
/*
	Vul object persoonsgegevens met gegevens uit invoer array.
	Invoer array kan b.v. bestaan uit $_POST of $_SESSION gegevens. 
*/
	public function set_data($data) {
		foreach($this->data as $key => $value) {
			if (isset($data[$key])) {
				$this->data[$key] = $data[$key];
			}
		}
	}
/*
	Valideer de persoonsgegevens in het object.
	Doet ook wat aan editing (trim tekst, formateer postcode en huisnummer).
	Retourneert null (alle gegevens goedgekeurd) of een array met fouten.
*/
	public function validate() {
		$valid = true;
		$validation_errors = array();
		$timestamp = strtotime($this->data["birthdate"]);
		$day = intval(date("d", $timestamp));
		$month = intval(date("m", $timestamp));
		$year = intval(date("Y", $timestamp));
		if (!checkdate ($month , $day , $year )) {
			$validation_errors["birthdate"] = "FEEDBACK_INVALID_BIRTHDATE";
			$valid = false;
		}
		else {
			$this->data["birthdate"] = date('Y-m-d', $timestamp);
			$now = time();
			$tooold = strtotime('-150 years');
			if ($timestamp < $tooold) {
				$validation_errors["birthdate"] = "FEEDBACK_INVALID_TOO_OLD";
				$valid = false;
			} else {
				$tooyoung = time();
				if ($timestamp > $tooyoung) {
					$validation_errors["birthdate"] = "FEEDBACK_INVALID_TOO_YOUNG";
					$valid = false;
				}
			}
		}
		$this->data["calling_name"] = trim($this->calling_name());
		if (!$this->calling_name()) {
			$validation_errors["calling_name"] = "FEEDBACK_CALLINGNAME_REQUIRED";
			$valid = false;
		}
		$this->data["firstname"] = trim($this->firstname());
		$this->data["prefix"] = trim($this->prefix());
		$this->data["surname"] = trim($this->surname());
		if (!$this->surname()) {
			$validation_errors["surname"] = "FEEDBACK_SURNAME_REQUIRED";
			$valid = false;
		}
        // Check postcode
        $this->data["postcode"] = $this->postcode();
        if (!$this->postcode()) {
			$validation_errors["postcode"] = "FEEDBACK_POSTCODE_REQUIRED";
			$valid = false;
		}
        // Check number
         $this->data["number"] = $this->number();
        if (!$this->postcode()) {
			$validation_errors["number"] = "FEEDBACK_NUMBER_REQUIRED";
			$valid = false;
		}
		$this->data["description"] = trim($this->description());
		if (!$this->description()) {
			$validation_errors["description"] = "FEEDBACK_DESCRIPTION_REQUIRED";
			$valid = false;
		}
		/*	Check contacts	*/
		if (!sizeof($this->data["contacts"])) {
			$validation_errors["contacts"] = "FEEDBACK_CONTACT_REQUIRED";
			$valid = false;
		}
		else {
			$contacts_errors = array();
			foreach($this->data["contacts"] as $key => $contact) {
				//	Deze gegevens moeten ook bewaard worden
				//	i.v.m. het opslaan van contactgegevens
				$this->data["app_app_person_id"][$contact["application"]] = $contact["app_person_id"];
				//
				$value = trim($contact["value"]);
				$this->data["contacts"][$key]["value"] = $value;
				if ($value) {
					if (!$contact["contacttype"]) {
						if(!isset($contacts_errors[$key])) $contacts_errors[$key] = array();
						$contacts_errors[$key]["contacttype"] = "FEEDBACK_CONTACT_NO_CONTACTTYPE";
					}
				}
				if ($contact["contacttype"]) {
					if (!$value) {
						if(!isset($contacts_errors[$key])) $contacts_errors[$key] = array();
						$contacts_errors[$key]["value"] = "FEEDBACK_CONTACT_NO_VALUE";
					}
				}
			}
			if (sizeof($contacts_errors)) {
				$validation_errors["contacts"] = json_encode($contacts_errors);
				$valid = false;
			}
		}
		if ($valid) {
			return null;
		} else {
			return $validation_errors;
		}
	}
/*
	Maak nieuwe rij aan in ADDRESS tabel.
	Gegevens komen uit dit object.
	Method retourneert nieuwe ADDRESS_ID of 0 (indien geen succes). 
*/
      private function createAddress(){
         $address_id = 0;
         $this->connect();
         $this->connection->query("LOCK TABLES ADDRESS WRITE");
		 $query = $this->connection->query("SELECT MAX(ADDRESS_ID) AS 'last' FROM ADDRESS");
		if ($query->num_rows) {
			$address_id = $query->fetch_assoc()["last"];
        }
          $this->connection->query("UNLOCK TABLES");
          $address_id += 1;
          $stmt = $this->connection->prepare("insert into ADDRESS( 
				ADDRESS_ID,
				COUNTRY,
				POSTCODE,
				STREET,
				STREET_EXT,
				TOWN)
			values(
				?,
				?,
				?,
				?,
				?,
				?)");
          	$stmt->bind_param("isssss",
			$address_id,
			$this->data["country"],
            trim($this->data["postcode"]),
            trim($this->data["street"]),
            trim($this->data["street_ext"]),
			trim($this->data["town"]));
            $stmt->execute();
            $stmt->close();
            $this->createPerson_Address($address_id,$this->person_id());
       
        }
        
        private function createPerson_Address($address_id, $person_id){
        
         $this->connect();        
         $stmt = $this->connection->prepare("insert into PERSON_ADDRESS( 
				EXTENSION,
				lATITUDE,
				LONGTITUDE,
				PERSON_PERSON_ID,
				ADDRESS_ADDRESS_ID,
				ADDRESSES_KEY,
                NUMBER)
			values(
				?,
				?,
				?,
				?,
				?,
                ?,
                ?)");
          	$stmt->bind_param("sssiisi",
			trim($this->data["extension"]),
			$this->data["latitude"],
			$this->data["longtitude"],
            $person_id,
            $address_id,
            $this->data["addresses_key"],               
			$this->data["number"]);
            $stmt->execute();
            $stmt->close();
            
        }
        
/*
	Maak nieuwe rij aan in PERSON tabel.
	Gegevens komen uit dit object.
	Method retourneert nieuwe PERSON_ID of 0 (indien geen succes). 
*/
	public function create() {
		$person_id = 0;
		$this->connect();
		$this->connection->query("LOCK TABLES PERSON, APPLICATIONPERSON WRITE");
		$query = $this->connection->query("SELECT MAX(PERSON_ID) AS 'last' FROM PERSON");
		if ($query->num_rows) {
			$person_id = $query->fetch_assoc()["last"];
		}
		$person_id += 1;
		$stmt = $this->connection->prepare("insert into PERSON( 
				PERSON_ID,
				BIRTHDATE,
				CREATEDATE,
				GENDER,
				TITLE,
				CALLING_NAME,
				FIRSTNAME,
				PREFIX,
				SURNAME,
				DESCRIPTION,
				NATURE,
				UPAS)
			values(
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?)");
		$stmt->bind_param("isssssssssss",
			$person_id,
			$this->data["birthdate"],
			date('Y-m-d H:i:s'),
			$this->data["gender"],
			$this->data["title"],
			trim($this->data["calling_name"]),
			trim($this->data["firstname"]),
			trim($this->data["prefix"]),
			trim($this->data["surname"]),
			trim($this->data["description"]),
			$this->data["nature"],
			$this->data["upas"]);
		$stmt->execute();
		if ($this->connection->affected_rows) {
			$this->data["person_id"] = $person_id;
			$this->createAddress();
            $app_person_id = $this->store_app_person($person_id);
			$this->data["app_app_person_id"][0] = $app_person_id;
			if ($app_person_id) {
				$this->store_contacts();
			}
		}
		$this->connection->query("UNLOCK TABLES");
		$stmt->close();
		
		return $this->data["person_id"];
	}
        /*
	Wijzig rij in ADDRESS tabel.
	Gegevens komen uit dit object.
	Method retourneert aantal rijen dat gemuteerd is. Zal altijd 0 of 1 zijn.
*/
        private function updateAddress($address_address_id){
            
            $this->connect();
		$stmt = $this->connection->prepare("update ADDRESS set 
				POSTCODE = ?,
				STREET = ?,
				TOWN = ?
			where ADDRESS_ID = ?");
		$stmt->bind_param("sssi",
			trim($this->data["postcode"]),
			trim($this->data["street"]),
			trim($this->data["town"]),
            $address_address_id);
		$stmt->execute();
		$stmt->close();
        }
        /*
	Wijzig rij in PERSON_ADDRESS tabel.
	Gegevens komen uit dit object.
	Method retourneert aantal rijen dat gemuteerd is. Zal altijd 0 of 1 zijn.
*/
        private function updatePerson_Address(){
            
            $this->connect();
		$stmt = $this->connection->prepare("update PERSON_ADDRESS set 
				EXTENSION = ?,
				LATITUDE = ?,
				LONGTITUDE = ?,
				NUMBER = ?
				where PERSON_PERSON_ID = ?");
		$stmt->bind_param("ssssi",
			trim($this->data["extension"]),
			trim($this->data["latitude"]),
			trim($this->data["longtitude"]),
			trim($this->data["number"]),
			$this->data["person_id"]);
		$stmt->execute();
		$stmt->close();
            
          $stmt = $this->connection->prepare("SELECT ADDRESS_ADDRESS_ID FROM
          PERSON_ADDRESS WHERE PERSON_PERSON_ID = ? limit 1"); 
            $stmt->bind_param("i",
			$this->data["person_id"]);
            $stmt->execute();
		    $stmt->bind_result($address_address_id);
            $stmt->fetch();
            $stmt->close();
            $this->updateAddress($address_address_id);
            
        }
/*
	Wijzig rij in PERSON tabel.
	Gegevens komen uit dit object.
	Method retourneert aantal rijen dat gemuteerd is. Zal altijd 0 of 1 zijn.
*/
	public function update() {
		if (!$this->data["person_id"]) {
			return 0;
		}
		$this->connect();
		$stmt = $this->connection->prepare("update PERSON set 
				BIRTHDATE = ?,
				GENDER = ?,
				TITLE = ?,
				CALLING_NAME = ?,
				FIRSTNAME = ?,
				PREFIX = ?,
				SURNAME = ?,
				DESCRIPTION = ?,
				NATURE = ?,
				UPAS = ?
			where PERSON_ID = ?");
		$stmt->bind_param("ssssssssssi",
			$this->data["birthdate"],
			$this->data["gender"],
			$this->data["title"],
			trim($this->data["calling_name"]),
			trim($this->data["firstname"]),
			trim($this->data["prefix"]),
			trim($this->data["surname"]),
			trim($this->data["description"]),
			$this->data["nature"],
			$this->data["upas"],
			$this->data["person_id"]);
		$stmt->execute();
		$num_rows =  $this->connection->affected_rows;
		$stmt->close();
        $this->updatePerson_Address();
		$num_rows += $this->store_contacts();
		return $num_rows;
	}
	private function store_app_person($person_id) {
		$apid = $this->connection->query("SELECT MAX(APP_PERSON_ID) AS 'last' FROM APPLICATIONPERSON");
		if ($apid->num_rows) {
			$app_person_id = $apid->fetch_assoc()["last"];
		}
		$app_person_id += 1;
		$app_id = 0;
		$stmt = $this->connection->prepare("insert into APPLICATIONPERSON( 
				APP_PERSON_ID,
				CODE,
				PERSON)
			values(
				?,
				?,
				?)");
		$stmt->bind_param("iii",
			$app_person_id,
			$app_id,
			$person_id);
		$stmt->execute();
		$num_rows =  $this->connection->affected_rows;
		$stmt->close();
		if ($num_rows) {
			return $app_person_id;
		} else {
			return 0;
		}
	}
	private function store_contacts() {
		$num_rows = 0;
		$stmt = $this->connection->prepare("delete from ApplicationPerson_CONTACTS
			where ApplicationPerson_APP_PERSON_ID = ?");
		foreach($this->data["app_app_person_id"] as $id) {
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$num_rows += $this->connection->affected_rows;
		}
		$stmt = $this->connection->prepare("insert into ApplicationPerson_CONTACTS (
				APPLICATION,
				CONTACTTYPE,
				RESTRICTED,
				VALUE,
				ApplicationPerson_APP_PERSON_ID)
			values (
				?,
				?,
				?,
				?,
				?)");
		foreach($this->data["contacts"] as $contact) {
			if (!$contact["contacttype"]) {
				continue;
			}
			$app_person_id = reset($this->data["app_app_person_id"]);
			if ($contact["app_person_id"]) {
				$app_person_id = $contact["app_person_id"];
			}
			$restricted = ($contact["restricted"] ? 1 : 0);
			$stmt->bind_param("isisi",
				$contact["application"],
				$contact["contacttype"],
				$restricted,
				$contact["value"],
				$app_person_id);
			$stmt->execute();
			$num_rows += $this->connection->affected_rows;
		}
		$stmt->close();
		return $num_rows;
	}
}
?>