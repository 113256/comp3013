<?php include('includes/connect.php');
require('lib/password.php');

if(!empty($_SESSION['user'])) 
{ 
    // If they are not, we redirect them to the login page. 
    header("Location: dashboard.php"); 
     
    // Remember that this die statement is absolutely critical.  Without it, 
    // people can view your members-only content without logging in. 
    die("Redirecting to dashboard.php"); 
}

if(isset($_POST['login'])){


	$userName = mysqli_real_escape_string($conn, $_POST['userName']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);

	$selectQuery = "SELECT * FROM `users` WHERE `userName`='$userName'";
	$userResult = mysqli_query($conn, $selectQuery);
	$row = mysqli_fetch_array($userResult);
	//echo $row['password'];
	//echo password_verify("jo65tt", '$2y$10$WVSty3ywE3muf');
	if($user->login($userName, $password)){
		//session_start();
		//this works because $row has $row['userName'] = $userName, $row['fName'] etc... since $row is an array.
		//so this sets $_SESSION['user']['userName'] = $userName and so on 
		$_SESSION['user'] = $row; 

		header("Location: dashboard.php"); 
	    die("Redirecting to: dashboard.php"); 
	} else {
		//header("Location: index.php"); 
	    //die("Redirecting to: index.php"); 
	    echo "Invalid login details!";
	}
	
}


if(isset($_POST['register'])){
	$userName = mysqli_real_escape_string($conn,$_POST['userName']);
	$fName = mysqli_real_escape_string($conn, $_POST['fName']);
	$lName = mysqli_real_escape_string($conn, $_POST['lName']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);

	$user = new User($conn);
	$user->register($userName, $fName, $lName, $password, $email);

	//header("Location: index.php"); 
	//die("Redirecting to: index.php"); 
	echo "Successfully registered";

	//auto login.
	$selectQuery = "SELECT * FROM `users` WHERE `userName`='$userName'";
	$userResult = mysqli_query($conn, $selectQuery);
	$row = mysqli_fetch_array($userResult);
	//echo $row['password'];
	//echo password_verify("jo65tt", '$2y$10$WVSty3ywE3muf');
	if($user->login($userName, $password)){
		$_SESSION['user'] = $row; 

		header("Location: dashboard.php"); 
	    die("Redirecting to: dashboard.php"); 
}
}

?>
<!DOCTYPE html>
<?php 
include('includes/head.php');
?>


<h1 style = "text-align: center">Auction</h1>


<div class = "container">
	<div class = "row">
		<div class = "col-lg-6">
			<?php
				if(isset($_POST['register'])){
					echo "<p>Registered!</p>";
				}
			 ?>
			 <h3>Register</h3>
			<form role = "form" method = "post" action = "index.php">

				<div class = "form-group">
					<label>Username</label>
					<input type = "text" class = "form-control" name = "userName" required>
				</div>

				<div class = "form-group">
					<label>First name</label>
					<input type = "text" class = "form-control" name = "fName" required>
				</div>

				<div class = "form-group">
					<label>Last name</label>
					<input type = "text" class = "form-control" name = "lName" required>
				</div>

				<div class = "form-group">
					<label>Password</label>
					<input type = "password" class = "form-control" name = "password" required>
				</div>

				<div class = "form-group">
					<label>Email</label>
					<input type = "email" class = "form-control" name = "email" required>
				</div>

				

				<button type = "submit" class = "btn btn-default" name = "register">Submit</button>

			</form>
		</div>
		<div class = "col-lg-6">
			 <h3>Login</h3>
			<form role = "form" method = "post" action = "index.php">
				<div class = "form-group">
					<label>Username</label>
					<input type = "text" class = "form-control" name = "userName" required>
				</div>
				<div class = "form-group">
					<label>Password</label>
					<input type = "password" class = "form-control" name = "password" required>
				</div>
			

				<button type = "submit" class = "btn btn-default" name = "login">Submit</button>

			</form>
		</div>
	</div>
</div>






</html>