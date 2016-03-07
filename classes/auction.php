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

		if($resPrice <= $startPrice){
			return false;
		}

		$insertAuctionQuery = "INSERT INTO `auction` (`datePosted`, `endDate`, `startPrice`, `resPrice`, `noViews`, `itemID`, `userId`, `bids`) VALUES ('$datePosted', '$endDate', '$startPrice', '$resPrice', 0, '$itemID', '$userId', 0)";
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
		$query = "SELECT email, c.description,c.itemName FROM `users` as u INNER JOIN (SELECT b.userId,b.auctionID,i.description,i.itemName FROM `bids` as b INNER JOIN `auction` as a on b.auctionID=a.auctionID INNER JOIN `items` as i on a.itemID = i.itemID WHERE b.auctionID = '$auctionID') as c on u.userId = c.userId WHERE c.userId != '$userId'";
		$result = mysqli_query($this->conn, $query) or die(mysqli_error($this->conn));
		while($row = mysqli_fetch_array($result)){
			//echo $row['email']."<br>";
			//send email
			$to = "113256@gmail.com";
			$itemDesc = $row['description'];
			$itemName = $row['itemName'];
			$subject = "outbid notification";
			$message = "You have been outbid on ".$itemName." with the description: ".$itemDesc;

			//need a mail server
			//mail($to, $subject, $message);
		}
	}

	public function recommend($userId){
		$userID=$userId;
		$myBidQuery = "SELECT * FROM `bids` WHERE userId = '$userID'";
		$myBidResult = mysqli_query($this->conn, $myBidQuery) or die(mysqli_error($this->conn));

		//keep list of auctions to avoid repetition
		//value 0 default
		//value=1 , auctions i bid on
		//value =2 auctions other people (who bid on the same auctions as me) bid on
		$auctionList = [];

		while($row = mysqli_fetch_array($myBidResult)){
			$auctionID = $row['auctionID'];
			$auctionList[$auctionID]=1;
			/*$auctionItemQuery = "SELECT datePosted, startPrice, endDate, bids, i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID WHERE a.auctionID = '$auctionID'";
			$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));
			$auctionItemRow = mysqli_fetch_array($auctionItemResult);*/

			/*$highestBidQuery = "SELECT `bidPrice` FROM `bids` WHERE auctionID = '$auctionID' ORDER BY bidPrice DESC LIMIT 1";
			$highestBidResult = mysqli_query($conn, $highestBidQuery);
			$highestBid = mysqli_fetch_array($highestBidResult);*/

			//echo "<br> aID is ".$auctionID."<br>";
			//var_dump($auctionItemRow);

			//people who bid on the same auction your're bidding on
			$otherPeopleQuery = "SELECT * FROM `bids` WHERE `auctionID` = '$auctionID' AND `userId` != '$userID'";
			$otherPeopleResult = mysqli_query($this->conn, $otherPeopleQuery) or die(mysqli_error($this->conn));
			//$otherPeopleRow = mysqli_fetch_array($otherPeopleResult);

			while($row2 = mysqli_fetch_array($otherPeopleResult)){
				//print_r($row2);
				//echo "user id is ";
				//echo $row2['userId'];
				$otherID = $row2['userId'];
				$otherAuctionID = $row2['auctionID'];
				//bids these people made
				//echo "<br> aID is ".$auctionID."<br>";
				$otherPeopleBidQuery = "SELECT * FROM `bids` WHERE `userId` = '$otherID' AND `auctionID` != '$auctionID'";
				$otherPeopleBidResult = mysqli_query($this->conn, $otherPeopleBidQuery) or die(mysqli_error($this->conn));
				while($row3 = mysqli_fetch_array($otherPeopleBidResult)){
					//auctions that people who bid on the same auctions as you bid on
					$temp = $row3['auctionID'];
					//echo $temp."<br>";
					if($temp!=1){
						$auctionList[$row3['auctionID']]=2;
					}
				}
				//echo "<br>";
			}
			

		}
		return($auctionList);
	}
}



?>