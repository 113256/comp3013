<?php 
include('includes/connect.php');
//•	cron job- notify winner (if winnerNotified ==0) and set winnerNotified = 1. Also change the item ownership (userID) to the winners’s id. 
//.  IF PRICE > RESERVE UPDATE WNNER NOTIFED BUT DONT CANGE OWNERSHIP 
//also notify person who put auction

$currentDate = new DateTime();
$currentDate=$currentDate->format('Y-m-d H:i:s');
echo $currentDate;
echo "<br>";
echo "<br>";
echo "<br>";


$auctionQuery = "SELECT * FROM `auction` as a INNER JOIN `items` as i on a.itemId = i.itemId WHERE a.winnerNotified = 0";

$auctionResult = mysqli_query($conn,$auctionQuery);
while($row = mysqli_fetch_array($auctionResult)){
	$auctionID = $row['auctionID'];
	$bidQuery = "SELECT b.*,u.* FROM `bids` as b INNER JOIN users as u on b.userId = u.userId WHERE auctionID = '$auctionID' ORDER BY bidPrice DESC LIMIT 1"; 
	$bidResult = mysqli_query($conn,$bidQuery);
	$bidRow = mysqli_fetch_array($bidResult);

	print_r($row);
	echo "-----------";
	print_r($bidRow);
	echo "<br>";

	//expired
	//string to dateTime
	$endDate = strtotime($row['endDate']);
	$endDate = date('Y-m-d H:i:s',$endDate);
	$expired = $currentDate > $endDate;
	if($expired){
		
	}

	if($expired){
		echo "expired!";
		//highest bid > reserve price
		$highestBid = $bidRow['bidPrice'];
		$reservePrice = $row['resPrice'];
		$itemWon = $highestBid > $reservePrice;
		$expiredId = $row['auctionID'];
		if($itemWon){
			echo "item won! ";
			//notify user and change ownership of item!
			mailUser($bidRow['email'], $row['description'], $row['itemName'], "Auction won notification", $itemWon);
			//update ownership
			$winnerId = $bidRow['userId'];
			$itemWonId = $row['itemID'];

			$ownerQuery = "UPDATE `items` SET `userId`='$winnerId' WHERE itemId = '$itemWonId'";
			echo $ownerQuery;
			mysqli_query($conn, $ownerQuery) or die(mysqli_error($conn));
			//update winner notified
			$updateWinnerQuery = "UPDATE `auction` SET `winnerNotified`= 1 WHERE auctionID = '$expiredId'";
			mysqli_query($conn, $updateWinnerQuery) or die(mysqli_error($conn));
		} else {
			echo "not won!, bid is $highestBid, reserve is $reservePrice";
			//notify user
			mailUser($bidRow['email'], $row['description'], $row['itemName'], "Auction not won notification", $itemWon);
			//update winner notified
			$updateWinnerQuery = "UPDATE `auction` SET `winnerNotified`= 1 WHERE auctionID = '$expiredId'";
			mysqli_query($conn, $updateWinnerQuery) or die(mysqli_error($conn));
		}


	} else {
		echo "not expired";
	}



	echo "<br>";
	echo "<br>";
	echo "<br>";

}


function mailUser($to, $itemDesc, $itemName, $subject,$itemWon){
	if($itemWon){
		$message = "You have won the auction on ".$itemName." with the description: ".$itemDesc;
	} else {
		$message = "You have the highest bid but it was not higher than the reserve price. Hence you didn not win the auction on ".$itemName." with the description: ".$itemDesc;
	}
	$source = 'From: auctionServer@example.com';
	mail($to, $subject, $message,$source);
}


?>

