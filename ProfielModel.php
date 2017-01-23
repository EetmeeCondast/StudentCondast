<?php
	class ProfielModel {
/*
    Mitchell van der Woude
	Jan Osnabrugge, oktober 2016
	Project Student van Stichting Eet Mee!
	
	Gegevens personen database
*/
	private $connection = null;

	private $data = array();

	public function __construct() {
		$this->data["profile_id"] = 0;
		$this->data["birthdate"] = date('Y-m-d', strtotime('-18 years'));
		$this->data["startdate"] = "";
		$this->data["mealtype"] = "";
		$this->data["activate"] = "";
		$this->data["enddate"] = "";
		$this->data["days"] = "";
		$this->data["periodicity"] = "";
		$this->data["firstname"] = "";
		$this->data["surname"] = "";
		$this->data["type_woning"] = "";
		$this->data["verminderde_mobiliteit"] = "";
		$this->data["etage"] = "";
		$this->data["roken"] = "";
		$this->data["verminderde_mobiliteit"] = "";
		$this->data["etage"] = "";
        $this->data["huisdier"] = "Hond";
        $this->data["allergie"] = "Geen";
		$this->data["app_app_person_id"] = array();
		$this->data["contacts"] = array();
		
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
	public function profile_id() { 		return $this->data["profile_id"]; }
	public function birthdate() { 		return substr($this->data["birthdate"], 0, 10); }
	public function startdate() { 		return $this->data["startdate"]; }
	public function mealtype() { 		return $this->data["mealtype"]; }
	public function activate() { 		return $this->data["activate"]; }
	public function enddate() { 		return $this->data["enddate"]; }
	public function days() { 		    return $this->data["days"]; }
	public function periodicity() {     return $this->data["periodicity"]; }
	public function firstname() {       return $this->data["firstname"]; }
	public function surname() {         return $this->data["surname"]; }
	public function type_woning() {     return $this->data["type_woning"]; }
	public function roken() {           return $this->data["roken"]; }
	public function verminderde_mobiliteit() {     return $this->data["verminderde_mobiliteit"]; }
	public function etage() {     return $this->data["etage"]; }
	public function huisdier() {        return $this->data["huisdier"]; }
    public function allergie() {        return $this->data["allergie"]; }
	
        
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
}
?>