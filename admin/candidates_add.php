<?php
	include 'includes/session.php';
	include '../includes/keys.php';

	if(isset($_POST['add'])){
		$firstname = strip_tags($_POST['firstname']);
		$lastname = strip_tags($_POST['lastname']);
		$position = $_POST['position'];
		$platform = strip_tags($_POST['platform']);
		$filename = $_FILES['photo']['name'];
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}
		$sql = "INSERT INTO candidates (position_id, firstname, lastname, photo, platform) VALUES ('$position', '$firstname', '$lastname', '$filename', '$platform')";
		if($conn->query($sql)){
		
			$keys = $keys. " 0";

			$sql = "SELECT id from candidates where firstname = '$firstname' and lastname = '$lastname'";
			$query = $conn->query($sql);
			$row = $query->fetch_assoc();
			$cdId = $row['id']; 
			
			$sql_array = array();
			$sql = "SELECT id from voters";
			$query = $conn->query($sql);
			while($row = $query->fetch_assoc()){
				$vId = $row['id'];
				$output = NULL;
				exec("C:\Python310\python ..\paillier.py 1 ".$keys, $output, $ret_code);
				$cipher = $output[0];
				
				$sql_array[] = "INSERT INTO en_votes (voter_id, candidate_id, position_id, ciphertext) VALUES ($vId, $cdId, $position, '$cipher')";		
			}
			
			foreach($sql_array as $sql_row){
				$conn->query($sql_row);
			}
			
			$_SESSION['success'] = 'Candidate added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	}
	else{
		//$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: candidates.php');
?>
