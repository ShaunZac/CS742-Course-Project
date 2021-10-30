<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['vote'])){
		if(count($_POST) == 1){
			$_SESSION['error'][] = 'Please vote atleast one candidate';
		}
		else{
			$_SESSION['post'] = $_POST;
			$sql = "SELECT * FROM positions";
			$query = $conn->query($sql);
			$error = false;
			$sql_array = array();
			while($row = $query->fetch_assoc()){
				$position = slugify($row['description']);
				$pos_id = $row['id'];
				if(isset($_POST[$position])){
					if($row['max_vote'] > 1){
						if(count($_POST[$position]) > $row['max_vote']){
							$error = true;
							$_SESSION['error'][] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
						}
						else{
							$updat = "UPDATE voters SET voted = 1 WHERE id = '".$voter['id']."'";
							$que = $conn->query($updat);
							foreach($_POST[$position] as $key => $values){
								$sql_array[] = "INSERT INTO votes (candidate_id, position_id) VALUES ('$values', '$pos_id')";
							}

						}
						
					}
					else{
					    $updat = "UPDATE voters SET voted = 1 WHERE id = '".$voter['id']."'";
					    $que = $conn->query($updat);
						$candidate = $_POST[$position];
						$vId = $voter['id'];
						
						$sql = "SELECT id from CANDIDATES where position_id = $pos_id";
						$que = $conn->query($sql);
						
						while($candidate_row = $que->fetch_assoc()){
							$output = NULL;
							$cdId = $candidate_row['id'];
							if($cdId == $candidate){
								exec("C:\Python310\python homomorphic.py 1", $output, $ret_code);
							}
							else{
								exec("C:\Python310\python homomorphic.py 0", $output, $ret_code);
							}	
							
							$cipher = $output[0];
							$out1 = $cipher;
							$sql_array[] = "UPDATE en_votes SET ciphertext = $cipher where candidate_id = $cdId and position_id = $pos_id and voter_id = $vId";	
						}	
					}

				}
				
			}

			if(!$error){
				foreach($sql_array as $sql_row){
					$conn->query($sql_row);
				}

				unset($_SESSION['post']);
				$_SESSION['success'] = 'Ballot Submitted';

			}

		}

	}
	else{
		$_SESSION['error'][] = 'Select candidates to vote first';
	}

	header('location: home.php');

?>
