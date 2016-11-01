<?php
	class PersonLookupModel {
/*
	Jan Osnabrugge, oktober 2016
	Project Student van Stichting Eet Mee!
*/
	private $connection = null;

	private $data = array();
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
	Retourneert array met persoonsgegevens uit object
*/
	public function get_data() {
		$data = array();
		foreach($this->data as $key => $value) {
			$data[$key] = $value;
		}
		return $data;
	}
	public function from_database($name) {
		$search_name = trim($name);
		$this->data = array();
		if (!$search_name) {
			return false;
		}
		$search_name .= "%";
		$this->connect();
		$stmt = $this->connection->prepare("select
				PERSON_ID,
				BIRTHDATE,
				TITLE,
				CALLING_NAME,
				PREFIX,
				SURNAME
			from PERSON where SURNAME like ? order by SURNAME");
		$stmt->bind_param("s", $search_name);
		$stmt->execute();
		$stmt->bind_result(
			$person_id,
			$birthdate,
			$title,
			$calling_name,
			$prefix,
			$surname);
		while ($stmt->fetch()) {
			$this->data[$person_id] = array(
				"birthdate" => $birthdate,
				"title" => $title,
				"calling_name" => $calling_name,
				"prefix" => $prefix,
				"surname" => $surname);
		}
		$stmt->close();
		return sizeof($this->data);
	}
}
?>