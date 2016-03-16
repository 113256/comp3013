<?php include('includes/connect.php');
error_reporting(E_ALL);
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

$myBidQuery = "SELECT * FROM `bids` WHERE userId = '$userID'";
$myBidResult = mysqli_query($conn, $myBidQuery) or die(mysqli_error($conn));


if(isset($_POST['rate'])){
	$rating = $_POST['rating'];
	if(!empty($rating)){
		$rowUserId = $_POST['rowUserId'];
		$updateRatingQuery = "UPDATE `users` SET `rating`= `rating`+'$rating', `noRating` = `noRating`+1 WHERE `userId` = '$rowUserId'";
		mysqli_query($conn, $updateRatingQuery);
	}
}
?>
<!DOCTYPE html>
<?php 
include('includes/head.php');
?>


<div class = "container-medium">
	<section>
		<div class = "jumbotron">
		<h1>My bids</h1>
		<p><?php echo $_SESSION['user']['userName']?></p>
		<a href = "dashboard.php" class = "btn btn-success">Back</a>
		</div>
	</section>



	<table class = "table table-hover table-condensed">
		<thead>
			<th>Bid date</th>
			<th>My bid price</th>
			<th>Highest bid</th>
			<th>Starting price</th>
			<th>Item name</th>
			<th>Description</th>
			<th>End date</th>
			<th>Expired?</th>
			<th>Seller Id</th>
			<th>Seller rating</th>
			<th>Rate seller</th>
		</thead>
		<tbody>
			<?php

			mysqli_data_seek($myBidResult,0);//return to 0th index
			while($row = mysqli_fetch_array($myBidResult)){
				$auctionID = $row['auctionID'];
				$auctionItemQuery = "SELECT u.*,winnerNotified, datePosted, startPrice, endDate, bids, i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID INNER JOIN `users` as u on a.userId = u.userId WHERE a.auctionID = '$auctionID'";
				$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));
				$auctionItemRow = mysqli_fetch_array($auctionItemResult);

				$highestBidQuery = "SELECT `bidPrice` FROM `bids` WHERE auctionID = '$auctionID' ORDER BY bidPrice DESC LIMIT 1";
				$highestBidResult = mysqli_query($conn, $highestBidQuery);
				$highestBid = mysqli_fetch_array($highestBidResult);

				echo '<tr>';
				echo '<td>'.$row['bidDate'].'</td>';
				echo '<td>'.$row['bidPrice'].'</td>';
				echo '<td>'.$highestBid[0];
				if($highestBid[0]>$row['bidPrice']){
					echo "-- OUTBID";
				}
				echo'</td>';
				echo '<td>'.$auctionItemRow['startPrice'].'</td>';
				echo '<td>'.$auctionItemRow['itemName'].'</td>';
				echo '<td>'.$auctionItemRow['description'].'</td>';
				echo '<td>'.$auctionItemRow['endDate'].'</td>';
				if($auctionItemRow['winnerNotified']){
					echo '<td>EXPIRED</td>';
				} else {
					echo '<td>Not yet</td>';
				}
				echo '<td>'.$auctionItemRow['userId'].'</td>';
				if($auctionItemRow['rating']!=0){
					echo '<td>'.round($auctionItemRow['rating']/$auctionItemRow['noRating'],1).'</td>';	
				} else {
					echo '<td></td>';
				}	
				echo '<td>'
				?> 
				<form action = "<?php $_SERVER['PHP_SELF'] ?>" method = "post">
					<input type="number" name="rating" min="1" max="10">
					<input type="text" name="rowUserId" value = <?php echo $auctionItemRow['userId']; ?> hidden >
					<button class = "btn btn-success" type = "submit" name = "rate">Rate</button>
				</form>
				<?php 
				echo '</td>';
				echo '</tr>';
			}
			?>
			
		</tbody>
	</table>

</div>





</html>