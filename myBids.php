<?php include('includes/connect.php');?>
<!DOCTYPE html>
<?php
include('includes/head.php');
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



$userID = $_SESSION['user']['userId'];

$myBidQuery = "SELECT * FROM `bids` WHERE userId = '$userID'";
$myBidResult = mysqli_query($conn, $myBidQuery) or die(mysqli_error($conn));




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
		</thead>
		<tbody>
			<?php

			mysqli_data_seek($myBidResult,0);//return to 0th index
			while($row = mysqli_fetch_array($myBidResult)){
				$auctionID = $row['auctionID'];
				$auctionItemQuery = "SELECT datePosted, startPrice, endDate, bids, i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID WHERE a.auctionID = '$auctionID'";
				$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));
				$auctionItemRow = mysqli_fetch_array($auctionItemResult);

				$highestBidQuery = "SELECT `bidPrice` FROM `bids` WHERE auctionID = '$auctionID' ORDER BY bidPrice DESC LIMIT 1";
				$highestBidResult = mysqli_query($conn, $highestBidQuery);
				$highestBid = mysqli_fetch_array($highestBidResult);

				echo '<tr>';
				echo '<td>'.$row['bidDate'].'</td>';
				echo '<td>'.$row['bidPrice'].'</td>';
				echo '<td>'.$highestBid[0].'</td>';
				echo '<td>'.$auctionItemRow['startPrice'].'</td>';
				echo '<td>'.$auctionItemRow['itemName'].'</td>';
				echo '<td>'.$auctionItemRow['description'].'</td>';
				echo '<td>'.$auctionItemRow['endDate'].'</td>';
				echo '</tr>';
			}
			?>
			
		</tbody>
	</table>

</div>





</html>