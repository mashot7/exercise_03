<?php
define('DSN', 'mysql:host=localhost;dbname=mydata');
define('USERNAME', 'root');
define('PASSWORD', '');

class Connection
{
	public function __construct()
	{
		try {
			$this->DB = new PDO(DSN, USERNAME, PASSWORD);
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}

	public function row_count()
	{
		$sql = "SELECT * FROM tablepaginate";
		$statement = $this->DB->query($sql);
		$row_count = $statement->rowCount();
		if ($row_count) {
			return $row_count;
		} else {
			return false;
		}
	}

	public function display_page($start_record, $record_per_page)
	{
		$sql = "SELECT * FROM `tablepaginate` ORDER BY `ID` DESC LIMIT $start_record, $record_per_page";
		try {
			$result = $this->DB->query($sql);
			if ($result->rowCount()) {
				echo '<br>';
				while ($row = $result->fetch()) {
					echo $row['ID'] . '<br>';
				}
			} else {
				return false;
			}
		} catch (PDOException $e) {
			$e->getMessage();
		}
	}
}