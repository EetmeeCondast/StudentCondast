<?php
	class ProfielModel {
/*
    Mitchell van der Woude
	Jan Osnabrugge, oktober 2016
	Project Student van Stichting Eet Mee!
	
	Gegevens applicatie database
*/
	private $connection = null;
	private $db_name = "";
	private $person_id = 0;
	private $data = array();

	public function __construct($db_name, $profile_id) {
		$this->db_name = $db_name;
		$this->data["PROFILE_ID"] = $profile_id;
		//	Initialize data from PROFILE table
		$this->data["discriminator"] = "";
		$this->data["CREATEDATE"] = "";
		$this->data["DIETS"] = 0;
		$this->data["REMARKS"] = "";
		$this->data["SPECIALDIET"] = 0;
		$this->data["GROUP_GROUP_ID"] = 0;
		$this->data["MATCHPREFERENCES_MATCHPREFS_ID"] = 0;
		$this->data["ADDRESSID"] = 0;
		$this->data["FLIGHT"] = 0;
		$this->data["MAXGUESTS"] = 0;
		$this->data["MOBILITY"] = "";
		$this->data["SPECIALDIETALLOWED"] = 0;
		$this->data["TYPEOFBUILDING"] = "";
/*		Initialize data from Group_MEMBERS table
		Dit is een array met als index PERSON_ID en als value een array met persoonsgegevens van de groepsleden
		$data["Group_MEMBERS"][$PERSON_ID] = array (
				"firstname" => $FIRSTNAME,
				"prefix" => $PREFIX,
				"surname" => $SURNAME,
				"PERSON.NATURE" => $NATURE,
				"PERSON.DESCRIPTION" => $DESCRIPTION,
				"PERSON.HOBBIES" => $HOBBIES,
				"PERSON.BIRTHDATE" => $BIRTHDATE,
				"Group_MEMBERS.MEMBERTYPE" => $MEMBERTYPE);
*/
		$this->data["Group_MEMBERS"] = array();
/*		Initialize data from PROFILE_AVAILABILITY table
		Dit is een array met als index een (volg)nummertje en als value een array met beschikbaarheidsgegevens.
		$data["PROFILE_AVAILABILITY"][$idx] = array (
				"startdate" => $STARTDATE,
				"mealtype" => $MEALTYPE,
				"activate" => $ACTIVATE,
				"enddate" => $ENDDATE,
				"days" => $DAYS,
				"periodicity" => $PERIODICITY,
				"MEALSPERPERIOD" => $MEALSPERPERIOD,
				"MATCHDUEDATE" => $MATCHDUEDATE);
*/
		$this->data["PROFILE_AVAILABILITY"] = array();
/*		
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
*/
/*		
$this->data["discriminator"] = "";
$this->data["CREATEDATE"] = "";
$this->data["DIETS"] = 0;
$this->data["REMARKS"] = "";
$this->data["SPECIALDIET"] = 0;
$this->data["GROUP_GROUP_ID"] = 0;
$this->data["MATCHPREFERENCES_MATCHPREFS_ID"] = 0;
$this->data["ADDRESSID"] = 0;
$this->data["FLIGHT"] = 0;
$this->data["MAXGUESTS"] = 0;
$this->data["MOBILITY"] = "";
$this->data["SPECIALDIETALLOWED"] = 0;
$this->data["TYPEOFBUILDING"] = "";
*/		
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
	public function all_data() {		return $this->data; }
	public function group_members() {	return $this->data["Group_MEMBERS"]; }
	public function availability() {	return $this->data["PROFILE_AVAILABILITY"]; }
	public function profile_id() { 		return $this->data["PROFILE_ID"]; }
	public function startdate() { 		return $this->data["startdate"]; }
	public function mealtype() { 		return $this->data["mealtype"]; }
	public function activate() { 		return $this->data["activate"]; }
	public function enddate() { 		return $this->data["enddate"]; }
	public function days() { 		    return $this->data["days"]; }
	public function periodicity() {     return $this->data["periodicity"]; }
	public function type_woning() {     return $this->data["type_woning"]; }
	public function etage() {     return $this->data["etage"]; }
	
        
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
			$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $this->db_name) ;
			if (mysqli_connect_errno()) {
				echo "Connect failed: " . mysqli_connect_error();
				exit();
			}
		}
	}
	//	Haal alle profiel-gegevens uit de database.
	public function from_database() {
		if ($this->from_profile()) {
			$this->from_group_members();
			$this->from_profile_availability();
		}
	}
	private function from_profile() {
		$return = false;
		$this->connect();
		$query = $this->connection->prepare("
			select 
				discriminator,
				CREATEDATE,
				DIETS,
				REMARKS,
				SPECIALDIET,
				GROUP_GROUP_ID,
				MATCHPREFERENCES_MATCHPREFS_ID,
				ADDRESSID,
				FLIGHT,
				MAXGUESTS,
				MOBILITY,
				SPECIALDIETALLOWED,
				TYPEOFBUILDING
			from PROFILE where PROFILE_ID = ?");
		$query->bind_param("i", $this->data["PROFILE_ID"]);
		$query->execute();
		$query->bind_result(
			$this->data["discriminator"],
			$this->data["CREATEDATE"],
			$this->data["DIETS"],
			$this->data["REMARKS"],
			$this->data["SPECIALDIET"],
			$this->data["GROUP_GROUP_ID"],
			$this->data["MATCHPREFERENCES_MATCHPREFS_ID"],
			$this->data["ADDRESSID"],
			$this->data["FLIGHT"],
			$this->data["MAXGUESTS"],
			$this->data["MOBILITY"],
			$this->data["SPECIALDIETALLOWED"],
			$this->data["TYPEOFBUILDING"]);
		if ($query->fetch()) {
			$this->data["CREATEDATE"] = substr($this->data["CREATEDATE"], 0, 10);
			$return = true;
		}
		$query->close();
		return $return;
	}
	private function from_group_members() {
		$return = false;
		$this->connect();
		$query = $this->connection->prepare("select PERSON_ID from GROUPING where GROUP_ID = ?");
		$query->bind_param("i", $this->data["GROUP_GROUP_ID"]);
		$query->execute();
		$person_id = 0;
		$query->bind_result($person_id);
		$query->fetch();
		$query->close();
		$query = $this->connection->prepare("
			select 
				PERSON_ID, 
				MEMBERTYPE
			from Group_MEMBERS 
			where Group_GROUP_ID = ? 
			and PERSON_ID != ?");
		$query->bind_param("ii", $this->data["GROUP_GROUP_ID"], $person_id);
		$query->execute();
		$query_result = $query->get_result();
		$query->close();
		while($row = $query_result->fetch_assoc()) {
			$person_id = $row["PERSON_ID"];
			require_once(STUDENT__PLUGIN_DIR . "PersonModel.php");
			$person = new PersonModel();
			$person->get_application_member_data($person_id);
			$this->data["Group_MEMBERS"][$row["PERSON_ID"]] = array(
				"firstname" => $person->firstname(),
				"prefix" => $person->prefix(),
				"surname" => $person->surname(),
				"nature" => $person->nature(),
				"description" => $person->description(),
				"hobbies" => $person->hobbies(),
				"birthdate" => $person->birthdate(),
				"membertype" => $row["MEMBERTYPE"]);
		}
	}
	private function from_profile_availability() {
		$return = false;
		$this->connect();
		$query = $this->connection->prepare("
			select 
				ACTIVATE,
				DAYS,
				ENDDATE,
				MATCHDUEDATE,
				MEALTYPE,
				MEALSPERPERIOD,
				PERIODICITY,
				STARTDATE
			from  PROFILE_AVAILABILITY
			where PROFILE_PROFILE_ID = ?");
		$query->bind_param("i", $this->data["PROFILE_ID"]);
		$query->execute();
		$query_result = $query->get_result();
		$query->close();
		$i = 1;
		while($row = $query_result->fetch_assoc()) {
			$days = $row["DAYS"];
			$days_array = array();
			if ($days & 2) $days_array[] = "MO";
			if ($days & 4) $days_array[] = "TU";
			if ($days & 8) $days_array[] = "WE";
			if ($days & 16) $days_array[] = "TH";
			if ($days & 32) $days_array[] = "FR";
			if ($days & 64) $days_array[] = "SA";
			if ($days & 128) $days_array[] = "SU";
			$this->data["PROFILE_AVAILABILITY"][$i++] = array(
				"startdate" => substr($row["STARTDATE"], 0, 10),
				"mealtype" => $row["MEALTYPE"],
				"activate" => $row["ACTIVATE"],
				"enddate" => substr($row["ENDDATE"], 0, 10),
				"days" => $days_array,
				"periodicity" => $row["PERIODICITY"],
				"mealsperperiod" => $row["MEALSPERPERIOD"],
				"matchduedate" => substr($row["MATCHDUEDATE"], 0, 10));
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