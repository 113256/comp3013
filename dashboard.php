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

if(isset($_POST['logout'])){
	unset($_SESSION['user']);
	header("Location: login.php"); 
    die("Redirecting to login.php"); 
}
print_r($_SESSION['user']);
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
			<div class = "col-xs-3">
				<div class = "panel panel-default">
					<div class = "panel-body">
						<table class = "table table-hover table-condensed">
							<tbody>
								<tr>
									<td>Name</td>
									<td><?php echo $_SESSION['user']['fName']." ".$_SESSION['user']['lName']?></td>
								</tr>
								<tr>
									<td>Email</td>
									<td><?php echo $_SESSION['user']['email']?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				
			</div>
			<div class = "col-xs-9">
				<div class = "row">
					<div class = "col-xs-6">
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
					<div class = "col-xs-6">
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
				</div>
				<div class = "row">
					<div class = "col-xs-6">
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
					<div class = "col-xs-6">
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
				$auctionItemQuery = "SELECT auctionID, datePosted, startPrice, endDate, bids, i.itemName, i.description, i.category FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID WHERE a.auctionID = '$aID'";
				$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));

			
				$row = mysqli_fetch_array($auctionItemResult);

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
				echo '<td>'.$row['category'].'</td>';
				echo '<td>'.$row['bids'].'</td>';
				echo '<td>'.$highestBid[0].'</td>';
				echo '<td><a class = "btn btn-success" href = "placeBid.php?auctionID='.$row['auctionID'].'">Bid</a></td>';
				echo '</tr>';
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