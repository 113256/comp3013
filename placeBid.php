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
if(isset($_GET['auctionID'])){
	$auctionID = $_GET['auctionID'];
} 

?>

<div class = "container-medium">
	<section>
		<div class = "jumbotron">
		<h1>Bid</h1>
		<a href = "dashboard.php" class = "btn btn-success">Back</a>
		</div>
	</section>


<?php 



if(isset($_POST['placeBid'])){
	$userId = $_SESSION['user']['userId'];
	$bidDate = $date = date('Y-m-d');	
	$bidPrice = $_POST['bidPrice'];


	$bidQuery = "INSERT INTO `bids` (`auctionID`, `userId`, `bidPrice`, `bidDate`) VALUES ('$auctionID', '$userId', '$bidPrice', '$bidDate')";
	//update auction bid count
	$updateQuery = "UPDATE `auction` SET bids = bids+1 WHERE auctionID='$auctionID'";

	if(mysqli_query($conn, $bidQuery) && mysqli_query($conn, $updateQuery)){
		echo '
			<div class = "alert alert-success">
				Bid successful
			</div>
		';
	} else {
		echo '
			<div class = "alert alert-danger">
				Error bidding
			</div>
		';
	}


}

?>


	<form role = "form" method = "post" action = "placeBid.php?auctionID=<?php echo $auctionID;?>">

		<div class = "form-group">
			<label>Bid price</label>
			<input type = "number" class = "form-control" name = "bidPrice" placeholder = "10" required>
		</div>



		<button class = "btn btn-success" type = "submit" name = "placeBid">Post auction</button>
	</form>

</div>





</html>