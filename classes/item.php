<?php

//item manager, not individual item
class Item{
	private $conn;
	function __construct($conn){
		$this->conn = $conn;
	}
	
	public function addItem($itemID, $itemName, $description, $category, $userId){
		$insertItemQuery = "INSERT INTO `items` (`itemID`, `itemName`, `description`, `category`, `userId`) VALUES ('$itemID', '$itemName', '$description', '$category', '$userId')";
		//echo $insertItemQuery;
		if(mysqli_query($this->conn, $insertItemQuery)){
			return true;
		} else {
			return false;
		}
	}

}


?>