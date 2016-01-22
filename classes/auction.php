<?php 

//auction manager class, not individual auction
class Auction{
	private $conn;

	function __construct($conn)
	{
		$this->conn = $conn;
	}

	//datePosted`, `endDate`, `startPrice`, `resPrice`, `count`, `itemID`, `userId`, `bids
	public function addAuction($datePosted, $endDate, $startPrice, $resPrice, $itemID, $userId){
		$insertAuctionQuery = "INSERT INTO `auction` (`datePosted`, `endDate`, `startPrice`, `resPrice`, `count`, `itemID`, `userId`, `bids`) VALUES ('$datePosted', '$endDate', '$startPrice', '$resPrice', 0, '$itemID', '$userId', 0)";
		if(mysqli_query($this->conn, $insertAuctionQuery)){
			return true;
		} else {
			return false;
		}
	}

	public function addBid($auctionID, $userId, $bidPrice, $bidDate){
		$bidQuery = "INSERT INTO `bids` (`auctionID`, `userId`, `bidPrice`, `bidDate`) VALUES ('$auctionID', '$userId', '$bidPrice', '$bidDate')";
		//update auction bid count
		$updateQuery = "UPDATE `auction` SET bids = bids+1 WHERE auctionID='$auctionID'";

		if(mysqli_query($this->conn, $bidQuery) && mysqli_query($this->conn, $updateQuery)){
			return true;
		} else {
			return false;
		}
	}
}



?>