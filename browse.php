<!DOCTYPE html>
<?php
include('includes/head.php');
include('includes/connect.php');
error_reporting(E_ALL);
session_start();
if(empty($_SESSION['user'])) 
{ 
    // If they are not, we redirect them to the login page. 
    header("Location: login.php"); 
    // Remember that this die statement is absolutely critical.  Without it, 
    // people can view your members-only content without logging in. 
    die("Redirecting to login.php"); 
}

$auctionItemQuery = "SELECT datePosted, startPrice, endDate, bids, i.itemName, i.description FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID";
$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));

?>


<div class = "container-medium">
	<section>
		<div class = "jumbotron">
		<h1>Browse auctions</h1>
		<p><?php echo $_SESSION['user']['userName']?></p>
		<a href = "dashboard.php" class = "btn btn-success">Back</a>
		</div>
	</section>



	<table class = "table table-hover table-condensed">
		<thead>
			<th>Item name</th>
			<th>Date posted</th>
			<th>End date</th>
			<th>Starting price</th>
			<th>Description</th>
			<th>Bids</th>
			<th>Highest bid</th>
			<th>Place a bid</th>
		</thead>
		<tbody>
			<?php
			mysqli_data_seek($auctionItemResult,0);//return to 0th index
			while($row = mysqli_fetch_array($auctionItemResult)){
				echo '<tr>';
				echo '<td>'.$row['itemName'].'</td>';
				echo '<td>'.$row['datePosted'].'</td>';
				echo '<td>'.$row['endDate'].'</td>';
				echo '<td>'.$row['startPrice'].'</td>';
				echo '<td>'.$row['description'].'</td>';
				echo '<td>'.$row['bids'].'</td>';
				echo '<td>highest bid here</td>';
				echo '<td><a class = "btn btn-success" href = "#">Bid</a></td>';
				echo '</tr>';
			}
			?>
			
		</tbody>
	</table>

</div>





</html>