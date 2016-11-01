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
		$this->data["description"] = "";
		$this->data["nature"] = "STUDENT";
		$this->data["upas"] = "";
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
	public function description() { 	return $this->data["description"]; }
	public function nature() { 			return $this->data["nature"]; }
	public function upas() { 			return $this->data["upas"]; }
/*
	Verbind aan database.
	Constanten m.b.t. de database zijn gedefinieerd in wp-config.php.
*/
	private function connect() {
		if (!$this->connection) {
			$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, STUDENT_DB_NAME) ;
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
			return false;
		}
		$stmt->close();
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
		$this->data["description"] = trim($this->description());
		if (!$this->description()) {
			$validation_errors["description"] = "FEEDBACK_DESCRIPTION_REQUIRED";
			$valid = false;
		}
		if ($valid) {
			return null;
		} else {
			return $validation_errors;
		}
	}
/*
	Maak nieuwe rij aan in PERSON tabel.
	Gegevens komen uit dit object.
	Method retourneert nieuwe PERSON_ID of 0 (indien geen succes). 
*/
	public function create() {
		$person_id = 0;
		$this->connect();
		$this->connection->query("LOCK TABLES PERSON WRITE");
		$pid = $this->connection->query("SELECT MAX(PERSON_ID) AS 'last' FROM PERSON");
		if ($pid->num_rows) {
			$person_id = $pid->fetch_assoc()["last"];
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
		}
		$this->connection->query("UNLOCK TABLES");
		$stmt->close();
		return $this->data["person_id"];
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
		return $num_rows;
	}
}
?>