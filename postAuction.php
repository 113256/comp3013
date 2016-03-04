<?php include('includes/connect.php');
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
include('includes/head.php');
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
	$datePosted = $date = date('Y-m-d');
	$endDate = $_POST['endDate'];	
	$startPrice = $_POST['startPrice'];
	$resPrice = $_POST['resPrice'];
	//$count = 0;//number of views

	//insert item into item table
	//insert item id here too 
	$itemName = $_POST['itemName']; 
	$description = $_POST['description'];
	$category = $_POST['category'];

	$auction = new Auction($conn);
	$item = new Item($conn);

	if($auction->addAuction($datePosted, $endDate, $startPrice, $resPrice, $currentItemID, $userId) && $item->addItem($currentItemID, $itemName, $description, $category, $userId)){
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
			<input type = "text" class = "form-control" name = "category" placeholder = "book" required>
		</div>

		<div class = "form-group">
			<label>End date</label>
			<input type = "date" class = "form-control" name = "endDate" required>
		</div>


		<button class = "btn btn-success" type = "submit" name = "postAuction">Post auction</button>
	</form>

</div>





</html>