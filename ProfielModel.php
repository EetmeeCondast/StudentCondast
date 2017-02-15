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
			$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, STUDENT_DB_NAME) ;
			if (mysqli_connect_errno()) {
				echo "Connect failed: " . mysqli_connect_error();
				exit();
			}
		}
	}
        
/*
    Create functie om de velden te inserten in de database.
    INSERTEN in de PROFILE_AVAILABILITY TABLE
*/
     public function create() {
		$profile_profile_id = 0;
		$this->connect();
		$this->connection->query("LOCK TABLES PROFILE_AVAILABILITY WRITE");
		$query = $this->connection->query("SELECT MAX(PROFILE_PROFILE_ID) AS 'last' FROM PROFILE_AVAILABILITY");
        echo $this->connection->error;
		if ($query->num_rows) {
			$profile_profile_id = $query->fetch_assoc()["last"];
		}
		$profile_profile_id += 1;
		$stmt = $this->connection->prepare("insert into PROFILE_AVAILABILITY( 
				ACTIVATE,
                DAYS,
                ENDDATE,
                MATCHDUEDATE,
				MEALTYPE,
				MEALSPERPERIOD,
				PERIODICITY,
				STARTDATE,
				PROFILE_PROFILE_ID)
			values(
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?)");
		$stmt->bind_param("iisssiisi",
			$this->data["activate"],
            $this->data["days"],
            $this->data["enddate"],
            $this->data["matchduedate"],
            $this->data["mealtype"],
            $this->data["mealsperperiod"],
            $this->data["periodicity"],
            $this->data["startdate"],
            $profile_profile_id);
		$stmt->execute();
		$this->connection->query("UNLOCK TABLES");
		$stmt->close();
		return $this->data["profile_profile_id"];
        
        // Inserten in de PROFILE TABLE 
        $profile_id = 0;
        $this->connect();
        $this->connection->query("LOCK TABLES PROFILE WRITE");
        $query = $this->connection->query("SELECT MAX(PROFILE_ID) AS 'last' FROM PROFILE");
         if ($query->num_rows){
             $profile_id = $query->fetch_assoc()["last"];
         }
         $profile_id += 1;
         $stmt = $this->connection->prepare("insert into PROFILE(
                 PROFILE_ID,
                 discriminator,
                 CREATEDATE,
                 DIETS,
                 REMARKS,
                 SPECIALDIET,
                 FLIGHT,
                 MAXGUESTS,
                 MOBILITY,
                 SPECIALDIETALLOWED,
                 TYPEOFBUILDING)
            VALUES(
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
         $stmt->bind_param("isiisiiiiis",
            $profile_id,
            trim($this->date["discriminator"]),
            $this->data["createdate"],
            $this->data["diets"],
            trim($this->data["remarks"]),
            $this->data["specialdiet"],
            $this->data["flight"],
            $this->data["maxxguests"],
            $this->data["mobility"],
            $this->data["specialdietallowed"],
            trim($this->data["typeofbuilding"]));
        $stmt->execute();
        
                           
                           
                           
        $this->connection->query("UNLOCK TABLES");
        $stmt->close();
                          
        return $this->data["person_id"];                 
                          

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