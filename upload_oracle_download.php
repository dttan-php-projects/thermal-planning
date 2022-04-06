<?php
	// get time system
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		ini_set('max_execution_time',99999999999);  // set time 10 minutes
		ini_set('memory_limit', '512M');
	
		require_once ("./Database.php");
		require_once ("./data/class/import.php");

	// get file info
		$file_name = addslashes("\\FSPDVN06\GroupDisk-Plan\Planning\Planning Thermal\update oracle for SO# WEB\data oracle download.xlsx");
	
	// check
		if(!file_exists($file_name)){
			echo "FILE data oracle download.xlsx KHÔNG TỒN TẠI TRONG THƯ MỤC: \\FSPDVN06\GroupDisk-Plan\Planning\Planning Thermal\update oracle for SO# WEB!!";die;
		}

	// Get the last time the file content was modified
		$time_oracle_download = filemtime($file_name);

	// table
	$table = "oracle_download";
	
	$error = 0;
	$message_error = '';
	$message_success = '';
	
	if (!xlsxToCsv($file_name) ) {
		$message_error = "FILE KHÔNG CÓ DỮ LIỆU";
		$error = 1;
	} else {

		// file location
		$fileCSV = "./fileCSV/Sheet1.csv";

		// get last time
			$CREATED_TIME = getLastTime($dbMi_138, $table );
		
		// check
		if(!empty($CREATED_TIME)){
			if(strtotime($CREATED_TIME)>$time_oracle_download){
				$error = 1; 
				$message_error = 'DỮ LIỆU KHÔNG ĐƯỢC CẬP NHẬT DO thời gian cập nhật hệ thống > thời gian oracle tải xuống';
			}else{
				

				$results = import($dbMi_138, $table, $fileCSV);
				if ($results == true ) {
					$CREATED_TIME = getLastTime($dbMi_138, $table );
					$message_success = 'DỮ LIỆU CẬP NHẬT THÀNH CÔNG';
				} else {
					$error = 1;
					$message_error = "CÓ LỖI TRONG QUÁ TRÌNH IMPORT 1";
				}

			}
		}else{
			// upload
			$results = import($dbMi_138, $table, $fileCSV );
			if ($results == true ) {
				$CREATED_TIME = getLastTime($dbMi_138, $table );
				$message_success = 'DỮ LIỆU CẬP NHẬT THÀNH CÔNG';
			} else {
				$error = 1;
				$message_error = "CÓ LỖI TRONG QUÁ TRÌNH IMPORT 2";
			}
				
		}

	}


	// results

		echo "<p style='font-size:20pt;font-weight:bold;'>Thời gian cập nhật hệ thống: ".date("d-M-y H:i:s",strtotime($CREATED_TIME))."<p>";
		echo "<p style='font-size:20pt;font-weight:bold;'>Thời gian oracle tải xuống: ".date("d-M-y H:i:s",$time_oracle_download)."<p>";
		
		if($error){
			echo "<p class='error' style='font-size:20pt;font-weight:bold;color:red;'>$message_error<p>";
		}else{
			echo "<p class='sucess' style='font-size:20pt;font-weight:bold;color:blue;'>$message_success<p>";
		}
?>

<title>THERMAL</title>