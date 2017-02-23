<?php
	class MailModel {
/*
	Jan Osnabrugge, december 2016
	Project Student van Stichting Eet Mee!
	
	Gegevens Mail database
*/
	private $connection = null;
	
	private $events_stmt = null;
//	private $current_event = null;

	private $data = array();

	public function __construct() {
		$this->data["email_text"] = "";
		$this->data["email_subject"] = "";
		$this->data["event"] = "";
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
	public function email_text() {
		return $this->data["email_text"];
	}
	public function email_subject() {
		return $this->data["email_subject"];
	}
	public function event() {
		return $this->data["event"];
	}
/*
	Verbind aan database.
	Constanten m.b.t. de database zijn gedefinieerd in wp-config.php.
*/
	private function connect() {
		if (!$this->connection) {
			$this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, MAIL_DB_NAME) ;
			if (mysqli_connect_errno()) {
				echo "Connect failed: " . mysqli_connect_error();
				exit();
			}
		}
	}
	public function next_mail() {
		$event = array();
		if (null === $this->events_stmt) {
			$this->connect();
			$this->events_stmt = $this->connection->prepare("
				select event, email_subject
				from  event
				order by email_subject");
			$this->events_stmt->execute();
		}
		$this->events_stmt->bind_result(
			$event["event"],
			$event["email_subject"]);
		if (!$this->events_stmt->fetch()) {
			$this->events_stmt->close();
			$event = null;
		}
		return $event;
	}
/*
	Haal gegevens uit database.
	Invoer: PERSON.PERSON_ID.
	Return: true bij succes, false bij niet gevonden.
*/	
	public function from_database($event_id) {
		$this->connect();
		$this->data["event"] = $event = trim($event_id);
		$stmt = $this->connection->prepare("
			select email_subject, email_text
			from  event
			where event = ?");
		$stmt->bind_param("s", $event);
		$stmt->execute();
		$stmt->bind_result(
			$this->data["email_subject"],
			$this->data["email_text"]);
		$stmt->fetch();
		$stmt->close();
	}
/*
	Retourneert array met mailgegevens uit object
*/
	public function get_data() {
		$data = array();
		foreach($this->data as $key => $value) {
			$data[$key] = $this->data[$key];
		}
		return $data;
	}
/*
	Vul object mailgegevens met gegevens uit invoer array.
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
	Maak nieuwe rij aan in event tabel.
	Gegevens komen uit dit object.
*/
	public function create() {
		$this->connect();
		$email_text = str_replace('\"', '"', $this->data["email_text"]);
		$stmt = $this->connection->prepare("insert into event(
				event,
				email_subject,
				email_text)
			values(
				?,
				?,
				?)");
		$stmt->bind_param("sss",
			$this->data["event"],
			$this->data["email_subject"],
			$email_text);
		$stmt->execute();
		$stmt->close();
	}
/*
	Wijzig rij in PERSON tabel.
	Gegevens komen uit dit object.
	Method retourneert aantal rijen dat gemuteerd is. Zal altijd 0 of 1 zijn.
*/
	public function update() {
		if (!$this->data["event"]) {
			return 0;
		}
		$this->connect();
		$email_text = str_replace('\"', '"', $this->data["email_text"]);
		$stmt = $this->connection->prepare("update event set 
				email_subject = ?,
				email_text = ?
			where event = ?");
		$stmt->bind_param("sss",
			$this->data["email_subject"],
			$email_text,
			$this->data["event"]);
			
		$stmt->execute();
		$stmt->close();
	}
}
?>