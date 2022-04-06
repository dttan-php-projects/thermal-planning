<?php
	// load phpspreadsheet tool
		require_once $_SERVER["DOCUMENT_ROOT"]."/MVPlans/phpspreadsheet/vendor/autoload.php";

	// get phpSpreadsheet
		use \PhpOffice\PhpSpreadsheet\Reader\Xlsx;
		use \PhpOffice\PhpSpreadsheet\Writer\Csv;
	
	function getLastTime($dbMi_138, $table) 
	{
		$result = MiQuery("SELECT CREATED_TIME FROM $table LIMIT 0,1;", $dbMi_138 );
		if (!empty($result ) ) {
			return $result;
		} else {
			return '1970-01-01 01:01:01';
		}
	}

	function xlsxToCsv($xls_file) 
    {
        $reader = new Xlsx();
        $spreadsheet = $reader->load($xls_file);
		
		$productionLine = "./fileCSV/";
        $loadedSheetNames = $spreadsheet->getSheetNames();

        $writer = new Csv($spreadsheet);
		if (!empty($loadedSheetNames) ) {
			foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
				$writer->setSheetIndex($sheetIndex);
	
				$file = $productionLine . $loadedSheetName;
				$writer->save($file.'.csv');
			}

			return true;

		} else {
			return false;
		}
        
    }

	function import($dbMi_138, $table, $file ) 
	{	

		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($file);
    
		$sheetData = $spreadsheet->getActiveSheet()->toArray();
		
		if (!empty($sheetData) ) {
			// delete all table
			mysqli_query($dbMi_138, "TRUNCATE $table ;" );

			$sql = "INSERT INTO $table (`ORDER_NUMBER`,`LINE_NUMBER`,`SO_LINE`,`ORDERED_DATE`,`BILL_TO_CUSTOMER`,`SHIP_TO_CUSTOMER`,`ORDERED_ITEM`,`ITEM`,`ITEM_DESC`,`QTY`,`UNIT_SELLING_PRICE`,`EXTENDED_PRICE`,`STATUS`,`ORDERED_DATE_`,`REQUEST_DATE`,`PROMISE_DATE`,`CS`,`ORDER_TYPE_NAME`,`SOLD_TO_CUSTOMER`,`PACKING_INSTRUCTIONS`) VALUES ";
			$count = count($sheetData);

			// echo "Count: $count "; exit();
			
			for ($i=1; $i<count($sheetData); $i++) {

				$ORDER_NUMBER = !empty($sheetData[$i][0])?addslashes($sheetData[$i][0]):'';
				$LINE_NUMBER = !empty($sheetData[$i][1])?addslashes($sheetData[$i][1]):'';
				if(!empty($LINE_NUMBER)){
					$LINE_NUMBERS = explode(".",$LINE_NUMBER);
					$LINE_NUMBER = $LINE_NUMBERS[0];
				}

				$SO_LINE = !empty($sheetData[$i][2])?addslashes($sheetData[$i][2]):'';
				$ORDERED_DATE = !empty($sheetData[$i][3])?addslashes($sheetData[$i][3]):'';
				$ORDERED_DATE = !empty($ORDERED_DATE) ? date('Y-m-d H:i:s', strtotime($ORDERED_DATE) ) : '';

				$BILL_TO_CUSTOMER = !empty($sheetData[$i][4])?addslashes($sheetData[$i][4]):'';
				$SHIP_TO_CUSTOMER = !empty($sheetData[$i][5])?addslashes($sheetData[$i][5]):'';
				$ORDERED_ITEM = !empty($sheetData[$i][6])?addslashes($sheetData[$i][6]):'';
				$ITEM = !empty($sheetData[$i][7])?addslashes($sheetData[$i][7]):'';
				$ITEM_DESC = !empty($sheetData[$i][8])?addslashes($sheetData[$i][8]):'';
				$QTY = !empty($sheetData[$i][9])?addslashes($sheetData[$i][9]):'';
				$UNIT_SELLING_PRICE = !empty($sheetData[$i][10])?addslashes($sheetData[$i][10]):'';
				$EXTENDED_PRICE = !empty($sheetData[$i][11])?addslashes($sheetData[$i][11]):'';
				$STATUS = !empty($sheetData[$i][12])?addslashes($sheetData[$i][12]):'';
				
				$ORDERED_DATE_ = !empty($sheetData[$i][13])?addslashes($sheetData[$i][13]) : '';
				$ORDERED_DATE_ = !empty($ORDERED_DATE_) ? date('Y-m-d H:i:s', strtotime($ORDERED_DATE_) ) : '';

				$REQUEST_DATE = !empty($sheetData[$i][14])?addslashes($sheetData[$i][14]):0;
				$REQUEST_DATE = !empty($REQUEST_DATE) ? date('Y-m-d H:i:s', strtotime($REQUEST_DATE) ) : '';

				$PROMISE_DATE = !empty($sheetData[$i][15])?addslashes($sheetData[$i][15]):0;
				$PROMISE_DATE = !empty($PROMISE_DATE) ? date('Y-m-d H:i:s', strtotime($PROMISE_DATE) ) : '';
				
				$CS = !empty($sheetData[$i][16])?addslashes($sheetData[$i][16]):'';
				$ORDER_TYPE_NAME = !empty($sheetData[$i][17])?addslashes($sheetData[$i][17]):'';
				$RBO = !empty($sheetData[$i][18])?addslashes($sheetData[$i][18]):'';
				$PACKING_INSTRUCTIONS = !empty($sheetData[$i][20])?addslashes($sheetData[$i][20]):'';

				// join sql
				if ($i < ($count-1) ) {
					
					if ($i == 4999 || $i == 9999 ) {
						$sql .= " ('$ORDER_NUMBER','$LINE_NUMBER','$SO_LINE','$ORDERED_DATE','$BILL_TO_CUSTOMER','$SHIP_TO_CUSTOMER','$ORDERED_ITEM','$ITEM','$ITEM_DESC','$QTY','$UNIT_SELLING_PRICE','$EXTENDED_PRICE','$STATUS','$ORDERED_DATE_','$REQUEST_DATE','$PROMISE_DATE','$CS','$ORDER_TYPE_NAME','$RBO','$PACKING_INSTRUCTIONS')";
						$results = mysqli_query($dbMi_138, $sql);
						if (!$results ) {
							return mysqli_error($dbMi_138);
						};
						$sql = "INSERT INTO $table (`ORDER_NUMBER`,`LINE_NUMBER`,`SO_LINE`,`ORDERED_DATE`,`BILL_TO_CUSTOMER`,`SHIP_TO_CUSTOMER`,`ORDERED_ITEM`,`ITEM`,`ITEM_DESC`,`QTY`,`UNIT_SELLING_PRICE`,`EXTENDED_PRICE`,`STATUS`,`ORDERED_DATE_`,`REQUEST_DATE`,`PROMISE_DATE`,`CS`,`ORDER_TYPE_NAME`,`SOLD_TO_CUSTOMER`,`PACKING_INSTRUCTIONS`) VALUES ";

					} else {
						$sql .= " ('$ORDER_NUMBER','$LINE_NUMBER','$SO_LINE','$ORDERED_DATE','$BILL_TO_CUSTOMER','$SHIP_TO_CUSTOMER','$ORDERED_ITEM','$ITEM','$ITEM_DESC','$QTY','$UNIT_SELLING_PRICE','$EXTENDED_PRICE','$STATUS','$ORDERED_DATE_','$REQUEST_DATE','$PROMISE_DATE','$CS','$ORDER_TYPE_NAME','$RBO','$PACKING_INSTRUCTIONS'),";
					}
				} else {
					$sql .= " ('$ORDER_NUMBER','$LINE_NUMBER','$SO_LINE','$ORDERED_DATE','$BILL_TO_CUSTOMER','$SHIP_TO_CUSTOMER','$ORDERED_ITEM','$ITEM','$ITEM_DESC','$QTY','$UNIT_SELLING_PRICE','$EXTENDED_PRICE','$STATUS','$ORDERED_DATE_','$REQUEST_DATE','$PROMISE_DATE','$CS','$ORDER_TYPE_NAME','$RBO','$PACKING_INSTRUCTIONS')";
				} 
				

			}

			

			if (!empty($sql) ) {

				// // // delete last
				// // // $sql = rtrim($sql, " ");
				
				// // $sql = rtrim($sql, ",");
				// // if (substr($sql, (strlen($sql) -1), 1 ) == ',' ) {
				// // 	$sql = substr($sql, 0, strlen($sql) -2);
				// // }

				// query
				$results = mysqli_query($dbMi_138, $sql);
				if (!$results ) {
					return mysqli_error($dbMi_138);
				};
			}

		} else {
			return false;
		}

		// true
		return true;

	}

?>