<?php include('includes/connect.php');?>
<!DOCTYPE html>
<?php
include('includes/head.php');


if(!empty($_SESSION['user'])) 
{ 
    // If they are not, we redirect them to the login page. 
    header("Location: dashboard.php"); 
     
    // Remember that this die statement is absolutely critical.  Without it, 
    // people can view your members-only content without logging in. 
    die("Redirecting to dashboard.php"); 
}

?>


<h1 style = "text-align: center">Auction</h1>


<div class = "container">
	<div class = "row">
		<div class = "col-lg-6">
			<a class = "btn btn-default" href = "register.php"><p style = "text-align: center">Register</p></a>
		</div>
		<div class = "col-lg-6">
			<a class = "btn btn-default" href = "login.php"><p style = "text-align: center">Login</p></a>
		</div>
	</div>
</div>






</html>