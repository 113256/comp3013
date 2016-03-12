<?php include('includes/connect.php');?>
<!DOCTYPE html>
<?php
include('includes/head.php');
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

$listCategoryQuery = "SELECT * FROM category";
$listCateoryResult = mysqli_query($conn, $listCategoryQuery);

?>

<div class = "container-medium">
	<section>
		<div class = "jumbotron">
		<h1>Post an auction</h1>
		<p>Welcome, <?php echo $_SESSION['user']['userName']?></p>
		<a href = "dashboard.php" class = "btn btn-success">Back</a>
		</div>
	</section>


<?php 
$currentItemID = 0;
//find highest item id from table
	//$itemIDQuery = "SELECT * FROM `items` WHERE itemID=(SELECT max(itemID) FROM items )";
	//$itemIDQuery = "SELECT * FROM `items` ORDER BY itemID DESC LIMIT 1";
	$itemIDQuery = "SELECT max(itemID) FROM items ";
	$itemIDResult = mysqli_query($conn, $itemIDQuery);

	if(mysqli_num_rows($itemIDResult)==0){ 
		$currentItemID = 0;
	} else {
		$row = mysqli_fetch_all($itemIDResult);
		$highestItemID = $row[0][0];
		$currentItemID = $highestItemID + 1;
	}


	

if(isset($_POST['postAuction'])){
	$userId = $_SESSION['user']['userId'];
	//insert auction into auction table
	//insert item id here too 
	//auctionID
	$currentDate = new DateTime();
	$datePosted=$currentDate->format('Y-m-d H:i:s');

	$endDate = mysqli_real_escape_string($conn, $_POST['endDate']);
	$endTime = mysqli_real_escape_string($conn, $_POST['endTime']).":00";
	//concatenate date and time
	$endDate = $endDate." ".$endTime;
	//echo $endDate;

	//compare enddate to dateposted
	$endDT = strtotime($endDate);
	$endDT = date('Y-m-d H:i:s',$endDT);
	if($endDT < $datePosted){
		echo '
			<div class = "alert alert-success">
				End date cant be before current date!
			</div>
		';
	} else {

		$startPrice = mysqli_real_escape_string($conn, $_POST['startPrice']);
		$resPrice = mysqli_real_escape_string($conn, $_POST['resPrice']);
		//$count = 0;//number of views

		//insert item into item table
		//insert item id here too 
		$itemName = mysqli_real_escape_string($conn, $_POST['itemName']); 
		$description = mysqli_real_escape_string($conn, $_POST['description']);
		$category = mysqli_real_escape_string($conn, $_POST['category']);

		$auction = new Auction($conn);
		$item = new Item($conn);

		//need to insert item first as it is the parent table and auction table references items
		$itemAdd = $item->addItem($currentItemID, $itemName, $description, $category, $userId);
		$auctionAdd = $auction->addAuction($datePosted, $endDate, $startPrice, $resPrice, $currentItemID, $userId);

		if($itemAdd && $auctionAdd){
			echo '
				<div class = "alert alert-success">
					Auction successfully posted
				</div>
			';
		} else {
			echo '
				<div class = "alert alert-danger">
					Error posting auction
				</div>
			';
		}
	}
}

?>


	<form role = "form" method = "post" action = "postAuction.php">
		<div class = "form-group">
			<label>Item name</label>
			<input type = "text" class = "form-control" name = "itemName" placeholder = "name" required>
		</div>

		<div class = "form-group">
			<label>Starting price</label>
			<input type = "number" class = "form-control" name = "startPrice" placeholder = "10" required>
		</div>

		<div class = "alert alert-info">
			A reserve price is the lowest price that the seller is willing to accept for the item. 
			If the listing ends without any bids that meet the reserve price, the seller is not required to sell the item.
		</div>
		<div class = "form-group">
			<label>Reserve price</label>
			<input type = "number" class = "form-control" name = "resPrice" placeholder = "50" required>
		</div>

		<div class = "form-group">
			<label>Description</label>
			<input type = "text" class = "form-control" name = "description" placeholder = "sample description" required>
		</div>

		<div class = "form-group">
			<label>Category</label>
			<select name = "category" class = "form-control">
				<?php
				while($row = mysqli_fetch_array($listCateoryResult)){
					$id = $row['id'];
					$categoryName = $row['categoryName'];
					echo "<option value='$id'>$categoryName</option>";
				}
				?>
			</select>
		</div>

		<div class = "form-group">
			<label>End date and time</label>
			<input type = "date" class = "form-control" name = "endDate" required>
			<input type = "time" class = "form-control" name = "endTime" required>
		</div>


		<button class = "btn btn-success" type = "submit" name = "postAuction">Post auction</button>
	</form>

</div>





</html>