<?php include('includes/connect.php');

//session_start();
if(empty($_SESSION['user'])) 
{ 
    // If they are not, we redirect them to the login page. 
    header("Location: index.php"); 
    // Remember that this die statement is absolutely critical.  Without it, 
    // people can view your members-only content without logging in. 
    die("Redirecting to index.php"); 
}
$userID = $_SESSION['user']['userId'];
echo $_SESSION['user']['userName'];

$myBidQuery = "SELECT * FROM `bids` WHERE userId = '$userID'";
$myBidResult = mysqli_query($conn, $myBidQuery) or die(mysqli_error($conn));

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

	echo "<br> aID is ".$auctionID."<br>";
	//var_dump($auctionItemRow);

	//people who bid on the same auction your're bidding on
	$otherPeopleQuery = "SELECT * FROM `bids` WHERE `auctionID` = '$auctionID' AND `userId` != '$userID'";
	$otherPeopleResult = mysqli_query($conn, $otherPeopleQuery) or die(mysqli_error($conn));
	//$otherPeopleRow = mysqli_fetch_array($otherPeopleResult);

	while($row2 = mysqli_fetch_array($otherPeopleResult)){
		//print_r($row2);
		echo "user id is ";
		echo $row2['userId'];
		$otherID = $row2['userId'];
		$otherAuctionID = $row2['auctionID'];
		//bids these people made
		echo "<br> aID is ".$auctionID."<br>";
		$otherPeopleBidQuery = "SELECT * FROM `bids` WHERE `userId` = '$otherID' AND `auctionID` != '$auctionID'";
		$otherPeopleBidResult = mysqli_query($conn, $otherPeopleBidQuery) or die(mysqli_error($conn));
		while($row3 = mysqli_fetch_array($otherPeopleBidResult)){
			//auctions that people who bid on the same auctions as you bid on
			$temp = $row3['auctionID'];
			echo $temp."<br>";
			if($temp!=1){
				$auctionList[$row3['auctionID']]=2;
			}
		}
		echo "<br>";
	}
	

}
print_r($auctionList);

?>