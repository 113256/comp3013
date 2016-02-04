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

	//if bid already exists just update that persons bid
	//need to make both (userId, auctionID) unique
	public function addBid($auctionID, $userId, $bidPrice, $bidDate){
		$bidQuery = "INSERT INTO `bids` (`auctionID`, `userId`, `bidPrice`, `bidDate`) VALUES ('$auctionID', '$userId', '$bidPrice', '$bidDate') ON DUPLICATE KEY UPDATE `bidDate` = '$bidDate', `bidPrice`='$bidPrice'";
		//update auction bid count
		$updateQuery = "UPDATE `auction` SET bids = bids+1 WHERE auctionID='$auctionID'";

		if(mysqli_query($this->conn, $bidQuery) && mysqli_query($this->conn, $updateQuery)){
			return true;
		} else {
			return false;
		}
	}

	//notify bidders of the auction that theyve been outbid, by sending an email but do not notify $currentUserId as thats the person who made the bid
	public function notifyBidders($auctionID, $bidPrice, $bidDate, $userId){
		//$query = "SELECT email FROM users u, bids b WHERE u.userId = b.userId";
		//select email from users from bids with the same auction id as the auction thats being bid on
		//$query = "SELECT email FROM `users` as u INNER JOIN `auction` as a on u.userId = a.userId INNER JOIN `bids` as b on a.auctionID = b.auctionID";
		$query = "SELECT email FROM `users` as u INNER JOIN (SELECT b.userId,b.auctionID FROM `bids` as b INNER JOIN `auction` as a on b.auctionID=a.auctionID WHERE b.auctionID = '$auctionID') as c on u.userId = c.userId WHERE c.userId != '$userId'";
		$result = mysqli_query($this->conn, $query) or die(mysqli_error($this->conn));
		while($row = mysqli_fetch_array($result)){
			echo $row['email']."<br>";
		}
	}
}



?>