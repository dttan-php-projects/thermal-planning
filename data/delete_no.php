<?php
header("Content-Type: application/json");
if(!empty($_POST['data'])){
	$NO = $_POST['data'];
	require_once ("../Database.php");

	$conn = _conn();
	
	$save_item  			= "DELETE FROM save_item where NUMBER_NO='$NO'";
	$save_material   		= "DELETE FROM save_material where ID_SAVE_ITEM='$NO'";
	$check_1 = $conn->query($save_item);
	$check_2 = $conn->query($save_material);

	if ($conn ) mysqli_close($conn);
	
	if($check_1&&$check_2){
		$response = [
                'status' => true,
                'mess' =>''  
            ];
	}else{
		$response = [
                'status' => false,
                'mess' =>  ''
            ];
	}
	echo json_encode($response);
}