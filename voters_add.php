<?php
	include 'admin/includes/session.php';

	if(isset($_POST['add'])){
		
		$firstname = strip_tags($_POST['firstname']);
		$lastname = strip_tags($_POST['lastname']);
		$voter = strip_tags($_POST['votersid']);
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}
		
		$pos = strpos($firstname, "'");
		if($pos !== false) $firstname = substr($firstname, 0, $pos);
		
		$pos = strpos($lastname, "'");
		if($pos !== false) $lastname = substr($lastname, 0, $pos);
		
		$pos = strpos($voter, "'");
		if($pos !== false) $voter = substr($voter, 0, $pos);
		
		$sql = "SELECT * FROM voters WHERE voters_id = '$voter'";
		$query = $conn->query($sql);
		
		if($query->num_rows > 1){
			$_SESSION['error'] = "$voter already present, Plz select another VOTERS ID" ;
			header('location: register.php');
		}
		else{
			$sql = "INSERT INTO voters (voters_id, password, firstname, lastname, photo) VALUES ('$voter', '$password', '$firstname', '$lastname', '$filename')";
			if($conn->query($sql)){
				header('location: index.php');
			}
			else{
				$_SESSION['error'] = $conn->error;
				header('location: register.php');
			}
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
		header('location: register.php');
	}

	
?>
