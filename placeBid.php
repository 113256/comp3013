<!DOCTYPE html>

<?php 

include('includes/head.php');
include('includes/connect.php');
error_reporting(E_ALL);

//session_start();
if(empty($_SESSION['user'])) 
{ 
    // If they are not, we redirect them to the login page. 
    header("Location: login.php"); 
    // Remember that this die statement is absolutely critical.  Without it, 
    // people can view your members-only content without logging in. 
    die("Redirecting to login.php"); 
}
if(isset($_GET['auctionID'])){
	$auctionID = $_GET['auctionID'];

	$auctionItemQuery = "SELECT datePosted, startPrice, endDate, bids, i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID WHERE a.auctionID = '$auctionID'";
	$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));
	$auctionItemRow = mysqli_fetch_array($auctionItemResult);

	$highestBidQuery = "SELECT `bidPrice` FROM `bids` WHERE auctionID = '$auctionID' ORDER BY bidPrice DESC LIMIT 1";
	$highestBidResult = mysqli_query($conn, $highestBidQuery);
	$highestBid = mysqli_fetch_array($highestBidResult);

} 

?>

<div class = "container-medium">
	<section>
		<div class = "jumbotron">
		<h1>Bid</h1>

		<ul class="list-group">
		  <li class="list-group-item">Item name: <?php echo $auctionItemRow['itemName'];?> </li>
		  <li class="list-group-item">Start price: <?php echo $auctionItemRow['startPrice'];?></li>
		  <li class="list-group-item">Description: <?php echo $auctionItemRow['description'];?></li>
		  <li class="list-group-item">Highest bid: <?php echo $highestBid[0];?></li>
		  <li class="list-group-item">End date: <?php echo $auctionItemRow['endDate'];?></li>
		</ul>

		<a href = "dashboard.php" class = "btn btn-success">Back</a>
		</div>
	</section>


<?php 



if(isset($_POST['placeBid'])){
	$userId = $_SESSION['user']['userId'];
	$bidDate = $date = date('Y-m-d');	
	$bidPrice = $_POST['bidPrice'];

	if($bidPrice <= $auctionItemRow['startPrice']){
		echo '
			<div class = "alert alert-danger">
				Error bidding - bid price must be higher than start price!
			</div>
		';
	} else {

		//get starting price, if bidprice<starting price then show error.

		//$bidQuery = "INSERT INTO `bids` (`auctionID`, `userId`, `bidPrice`, `bidDate`) VALUES ('$auctionID', '$userId', '$bidPrice', '$bidDate')";
		//update auction bid count
		//$updateQuery = "UPDATE `auction` SET bids = bids+1 WHERE auctionID='$auctionID'";

		//auction manager
		$auction = new Auction($conn);

		if($auction->addBid($auctionID, $userId, $bidPrice, $bidDate)){
			echo '
				<div class = "alert alert-success">
					Bid successful
				</div>
			';
		} else {
			echo '
				<div class = "alert alert-danger">
					Error bidding
				</div>
			';
		}
	}


}

?>


	<form role = "form" method = "post" action = "placeBid.php?auctionID=<?php echo $auctionID;?>">

		<div class = "form-group">
			<label>Bid price</label>
			<input type = "number" class = "form-control" name = "bidPrice" placeholder = "10" required>
		</div>



		<button class = "btn btn-success" type = "submit" name = "placeBid">Post auction</button>
	</form>

</div>





</html>