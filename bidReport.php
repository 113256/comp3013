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
$auctionID = $_GET['auctionID'];
$bidQuery = "SELECT * FROM `bids` WHERE auctionID = '$auctionID'";
$bidResult = mysqli_query($conn, $bidQuery);


$auctionItemQuery = "SELECT datePosted, datePosted, startPrice, endDate, bids, resPrice, noViews,i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID WHERE a.auctionID = '$auctionID'";
$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));
$auctionItemRow = mysqli_fetch_array($auctionItemResult);

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
			<th>Aggregated rating</th>
			<th>Rate</th>
		</thead>
		<tbody>
			<?php
			mysqli_data_seek($bidResult,0);//return to 0th index
			while($row = mysqli_fetch_array($bidResult)){
				$rowUserId = $row['userId'];
				$userNameQuery = "SELECT * from users WHERE userId = '$rowUserId'";
				$userNameResult = mysqli_query($conn, $userNameQuery);
				$userName = mysqli_fetch_array($userNameResult);

				echo '<tr>';
				echo '<td>'.$userName[0].'</td>';
				echo '<td>'.$row['bidDate'].'</td>';
				echo '<td>'.$row['bidPrice'].'</td>';	
				if($userName['rating']!=0){
					echo '<td>'.round($userName['rating']/$userName['noRating'],1).'</td>';	
				} else {
					echo '<td></td>';
				}	
				echo '<td>'
				?> 
				<form action = "<?php $_SERVER['PHP_SELF'] ?>" method = "post">
					<input type="number" name="rating" min="1" max="10">
					<input type="text" name="rowUserId" value = <?php echo $row['userId']; ?> hidden >
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