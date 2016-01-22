<?php

//item manager, not individual item
class Item{
	private $conn;
	function __construct($conn){
		$this->conn = $conn;
	}

	public function addItem($itemID, $itemName, $description, $category){
		$insertItemQuery = "INSERT INTO `items` (`itemID`, `itemName`, `description`, `category`) VALUES ('$itemID', '$itemName', '$description', '$category')";
		//echo $insertItemQuery;
		if(mysqli_query($this->conn, $insertItemQuery)){
			return true;
		} else {
			return false;
		}
	}

}


?>