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
$auctionID = $_GET['auctionID'];
$bidQuery = "SELECT * FROM `bids` WHERE auctionID = '$auctionID'";
$bidResult = mysqli_query($conn, $bidQuery);


$auctionItemQuery = "SELECT datePosted, datePosted, startPrice, endDate, bids, resPrice, noViews,i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID WHERE a.auctionID = '$auctionID'";
$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));
$auctionItemRow = mysqli_fetch_array($auctionItemResult);

?>


<div class = "container-medium">
	<section>
		<div class = "jumbotron">
		<h1>Bids for auction</h1>
		<p><?php echo $_SESSION['user']['userName']?></p>
		<a href = "dashboard.php" class = "btn btn-success">Back</a><br><br>
		

		<ul class="list-group">
		  <li class="list-group-item">Item name: <?php echo $auctionItemRow['itemName'];?> </li>
		  <li class="list-group-item">Start price: <?php echo $auctionItemRow['startPrice'];?></li>
		  <li class="list-group-item">Reserve price: <?php echo $auctionItemRow['resPrice'];?></li>
		  <li class="list-group-item">Description: <?php echo $auctionItemRow['description'];?></li>
		  <li class="list-group-item">Start date: <?php echo $auctionItemRow['datePosted'];?></li>
		  <li class="list-group-item">End date: <?php echo $auctionItemRow['endDate'];?></li>
		  <li class="list-group-item">Bids: <?php echo $auctionItemRow['bids'];?></li>
		  <li class="list-group-item">Number of views: <?php echo $auctionItemRow['noViews'];?></li>
		</ul>

		</div>
	</section>

	<div class="alert alert-danger">
	  <strong>Note!</strong> Only the highest bid is shown for each user.
	</div>

	<table class = "table table-hover table-condensed">
		<thead>
			<th>Bidder</th>
			<th>Bid date</th>
			<th>Bid price</th>

		</thead>
		<tbody>
			<?php
			mysqli_data_seek($bidResult,0);//return to 0th index
			while($row = mysqli_fetch_array($bidResult)){
				$rowUserId = $row['userId'];
				$userNameQuery = "SELECT userName from users WHERE userId = '$rowUserId'";
				$userNameResult = mysqli_query($conn, $userNameQuery);
				$userName = mysqli_fetch_array($userNameResult);

				echo '<tr>';
				echo '<td>'.$userName[0].'</td>';
				echo '<td>'.$row['bidDate'].'</td>';
				echo '<td>'.$row['bidPrice'].'</td>';		
				echo '</tr>';
			}
			?>
			
		</tbody>
	</table>

</div>





</html>