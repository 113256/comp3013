<?php include('includes/connect.php');?>
<?php
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

if(isset($_POST['logout'])){
	unset($_SESSION['user']);
	header("Location: index.php"); 
    die("Redirecting to index.php"); 
}

if(isset($_POST['updatePayment'])){

	$cardName = mysqli_real_escape_string($conn, $_POST['cardName']);
	$cardNumber = mysqli_real_escape_string($conn, $_POST['cardNumber']);
	$cardExpiry = mysqli_real_escape_string($conn, $_POST['cardExpiry']);

	if($user->updatePayment($cardName, $cardNumber, $cardExpiry, $_SESSION['user']['userId'])){
		//echo "Payment details updated!";
		header("Location: dashboard.php"); 
  	 	die("Redirecting to dashboard.php"); 
	} 
	
}
$userId = $_SESSION['user']['userId'];
//card details
$cardQuery = "SELECT * FROM `payment` WHERE `userId` = '$userId'";
$cardResult = mysqli_query($conn,$cardQuery);
$cardRow = mysqli_fetch_array($cardResult);

$cardNumber = $cardRow['cardNumber'];
$starLength = strlen(substr($cardNumber, 0, strlen($cardNumber)-4));
$lastFour = substr($cardNumber, -4);
$partialCardNumber = str_repeat("*", $starLength).$lastFour;


print_r($_SESSION['user']);

?>
<!DOCTYPE html>
<?php 
include('includes/head.php');
?>

<div class = "container-medium">
	<section>
			<div class = "jumbotron">
			<h1>Dashboard</h1>
			<p>Welcome, <?php echo $_SESSION['user']['userName']?></p>
			</div>
		
	</section>

	<section>

		<div class = "row">
			<div class = "col-xs-12">
					<div class = "col-xs-3">
						<a href="browse.php">
						<div class = "fontAwesome">
						<span class="fa-stack fa-3x">
	                       <i class="fa fa-circle fa-stack-2x text-primary"></i>
	                       <i class="fa fa-shopping-bag fa-stack-1x fa-inverse"></i>
	                    </span>
	                    <h4>Browse auctions</h4>
	                	</div>
	                	</a>
					</div>
					<div class = "col-xs-3">
						<a href="postAuction.php">
							<div class = "fontAwesome">
								<span class="fa-stack fa-3x">
			                       <i class="fa fa-circle fa-stack-2x text-primary"></i>
			                       <i class="fa fa-gavel fa-stack-1x fa-inverse"></i>
			                    </span>
			                    <h4>Post auction</h4>
			                </div>
						</a>
						
					</div>
					<div class = "col-xs-3">
						<a href="myAuctions.php">
						<div class = "fontAwesome">
						<span class="fa-stack fa-3x">
	                       <i class="fa fa-circle fa-stack-2x text-primary"></i>
	                       <i class="fa fa-book fa-stack-1x fa-inverse"></i>
	                    </span>
	                    <h4>My auctions</h4>
	                	</div>
	                	</a>
					</div>
					<div class = "col-xs-3">
						<a href="myBids.php">
							<div class = "fontAwesome">
								<span class="fa-stack fa-3x">
			                       <i class="fa fa-circle fa-stack-2x text-primary"></i>
			                       <i class="fa fa-check fa-stack-1x fa-inverse"></i>
			                    </span>
			                    <h4>My bids</h4>
			                </div>
						</a>
						
					</div>
			</div>
		</div>

		<div class = "row">
			<div class = "col-xs-6">
				<h4>Details </h4>
					<ul class="list-group">
						<li class = "list-group-item">Name: <?php echo $_SESSION['user']['fName']." ".$_SESSION['user']['lName']?></li>
						<li class = "list-group-item">Email: <?php echo $_SESSION['user']['email']?></li>
						<li class = "list-group-item">Card name: <?php echo $cardRow['cardName']?></li>
						<li class = "list-group-item">Card number: <?php echo $partialCardNumber; ?></li>
						<li class = "list-group-item">Card expiry date: <?php echo $cardRow['cardExpiry']?></li>
					</ul>
			</div>
			<div class = "col-xs-6">
				<form role = "form" method = "post" action = "dashboard.php">
				<h4>Update Payment details </h4>
				<div class = "form-group">
					<label>Card name</label>
					<input type = "text" class = "form-control" name = "cardName" required>
				</div>

				<div class = "form-group">
					<label>Card number (13 or 16 digits)</label>
					<input type="text" pattern="[0-9]{13,16}" inputmode="numeric"  class = "form-control" name = "cardNumber" required>
				</div>

				<div class = "form-group">
					<label>Expiry date</label>
					<input type = "date" class = "form-control" name = "cardExpiry" required>
				</div>

				<button type = "submit" class = "btn btn-default" name = "updatePayment">Update</button>
				</form>

			</div>
			
		</div>
		<?php
		$auction = new Auction($conn);
		//index = auctionID, 
		$auctionList = $auction->recommend($_SESSION['user']['userId']);
		//print_r($auctionList);
		//create new array that contains all the indexes of auctionList that has value of 2
		$auctionIdArray = [];
		
		//key->value because we need the index
		foreach ($auctionList as $key=>$val) {
			if($val==2){
				$auctionIdArray[]=$key;
			}
		}


		?>

		<h2>Recommended items</h2><br>
		<table class = "table table-hover table-condensed">
		<thead>
			<th>Item name</th>
			<th>Date posted</th>
			<th>End date</th>
			<th>Starting price</th>
			<th>Description</th>
			<th>Category</th>
			<th>Bids</th>
			<th>Highest bid</th>
			<th>Place a bid</th>
		</thead>
		<tbody>
			<?php
			foreach($auctionIdArray as $aID){
				$auctionItemQuery = "SELECT winnerNotified, auctionID, datePosted, startPrice, endDate, bids, i.itemName, i.description, i.category,c.categoryName FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID INNER JOIN `category` as c on i.category = c.id WHERE a.auctionID = '$aID'";
				$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));

			
				$row = mysqli_fetch_array($auctionItemResult);

				if(!$row['winnerNotified']){

					$auctionID = $row['auctionID'];
					$highestBidQuery = "SELECT `bidPrice` FROM `bids` WHERE auctionID = '$auctionID' ORDER BY bidPrice DESC LIMIT 1";
					$highestBidResult = mysqli_query($conn, $highestBidQuery);
					$highestBid = mysqli_fetch_array($highestBidResult);

					echo '<tr>';
					echo '<td>'.$row['itemName'].'</td>';
					echo '<td>'.$row['datePosted'].'</td>';
					echo '<td>'.$row['endDate'].'</td>';
					echo '<td>'.$row['startPrice'].'</td>';
					echo '<td>'.$row['description'].'</td>';
					echo '<td>'.$row['categoryName'].'</td>';
					echo '<td>'.$row['bids'].'</td>';
					echo '<td>'.$highestBid[0].'</td>';
					echo '<td><a class = "btn btn-success" href = "placeBid.php?auctionID='.$row['auctionID'].'">Bid</a></td>';
					echo '</tr>';
				}
			}
			?>
			
		</tbody>
	</table>
		
	</section>

	<form action = "dashboard.php" method = "post">
		<input type = "submit" name = "logout" class = "btn btn-default" value = "Logout">
	</form>

</div>











</html>