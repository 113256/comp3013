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
$userID = $_SESSION['user']['userId'];
$listCategoryQuery = "SELECT * FROM category";
$listCateoryResult = mysqli_query($conn, $listCategoryQuery);
//dont show my own auctions
if(isset($_GET['category'])){
	$filter = $_GET['category'];//category in an array
	$auctionItemQuery = "SELECT auctionID, datePosted, startPrice, endDate, bids, i.itemName, i.description, i.category,c.categoryName FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID INNER JOIN `category` as c on i.category = c.id WHERE a.userID != '$userID' AND i.category IN (";
	//where in is shorthand for where or.
	foreach ($filter as $category) {
		$auctionItemQuery.="'$category'".",";
	}
	$auctionItemQuery = rtrim($auctionItemQuery,",");
	$auctionItemQuery.=")";
	echo $auctionItemQuery;
} else {
	$auctionItemQuery = "SELECT auctionID, datePosted, startPrice, endDate, bids, i.itemName, i.description, i.category,c.categoryName FROM `auction` AS a INNER JOIN `items` as i on a.itemID = i.itemID INNER JOIN `category` as c on i.category = c.id WHERE a.userID != '$userID'";
}
$auctionItemResult = mysqli_query($conn, $auctionItemQuery) or die(mysqli_error($conn));

?>
<!DOCTYPE html>
<?php 
include('includes/head.php');
?>


<div class = "container-medium">
	<section>
		<div class = "jumbotron">
			<h1>Browse auctions</h1>
			<p><?php echo $_SESSION['user']['userName']?></p>

			<?php 
				/*if(isset($_GET['filter'])){
					$filter = $_GET['filter'];
					if(empty($filter)){
						echo "<h2>Showing all results for the all categories</h2><br>";
					} else {
						echo "<h2>Showing all results for the ".$filter." category</h2><br>";
					}
				}*/
				if(isset($_GET['category'])){
					$filter = $_GET['category'];//category is an array
					if(empty($filter)){
						echo "<h3>Showing results for the all categories</h3><br>";
					} else {
						echo "<h3>Showing results for the ";
						foreach ($filter as $cat) {
							echo "$cat, ";
						}
						echo " </h3><br>";
					}
					//print_r($filter);
					//echo '$filter';
				}
			?>

			<a href = "dashboard.php" class = "btn btn-success">Back</a>
			<form action = "browse.php" method = "get">
				<div class = "form-group">
					<!--<select name = "filter" class = "form-control">
					  <option value="" selected = "selected">All categories</option>	
					  <option value="book">Book</option>
					  <option value="game">Game</option>
					</select>-->
					<?php
					while($row = mysqli_fetch_array($listCateoryResult)){
						$id = $row['id'];
						$categoryName = $row['categoryName'];
						echo "<label class='checkbox-inline'><input name = 'category[]' type='checkbox' value='$id'>$categoryName</label>";
					}
					?>
				</div>
				<button type = "submit" class = "btn btn-default">Submit</button>	
			</form>

		</div>
	</section>



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
			mysqli_data_seek($auctionItemResult,0);//return to 0th index
			while($row = mysqli_fetch_array($auctionItemResult)){

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
			?>
			
		</tbody>
	</table>

</div>





</html>