<?php
function formatDate($value,$format='d-M-Y',$remove=1){
    $display = '';
    if(!empty($value)){
        $dateFormat = explode(" ",$value);
        if(!empty($dateFormat[0])){
            $dateArray = explode("-",$dateFormat[0]);
            $date = $dateArray[0];
            $month = $dateArray[1];
            $year = $dateArray[2];            
            if(strlen($date)===1){
                $date="0".$date;
            }
            if(strlen($month)===1){
                $month="0".$month;
            }
            $day = $date."-".$month."-".$year;
            $dayTime = strtotime($date."-".$month."-".$year);
            if($format==='dd-mm-YYYY'){
                if($remove){
                    // -2 if monday else -1                
                    if(date('w',$dayTime)==1){
                        $display = date('d-m-Y', strtotime("-3 day", $dayTime));
                    }else{
                        $display = date('d-m-Y', strtotime("-2 day", $dayTime));
                    }
                }else{
                    $display = date('d-m-Y', $dayTime);
                }
                
            }elseif($format==='dd.mm.YYYY'){
                // -2 if monday else -1     
                if($remove){
                    if(date('w',$dayTime)==1){
                        $display = date('d.m.Y', strtotime("-3 day", $dayTime));
                    }else{
                        $display = date('d.m.Y', strtotime("-2 day", $dayTime));
                    }
                }else{
                    $display = date('d.m.Y', $dayTime);
                }          
                
            }else{
                // 3-Nov-18	
                if($remove){
                    if(date('w',$dayTime)==1){
                        $display = date('d-M-y', strtotime("-3 day", $dayTime));
                    }else{
                        $display = date('d-M-y', strtotime("-2 day", $dayTime));
                    }
                }else{
                    $display = date('d-M-y', $dayTime);
                }                
            }
            return $display;
        }
    }
    return "";    
}
function getFileExcel($fileName){
	//Nh??ng file PHPExcel
	
	require_once ("../../PHPExcel/IOFactory.php");
	//Ti???n h??nh x??c th???c file
	$objFile = PHPExcel_IOFactory::identify($fileName);
	$objData = PHPExcel_IOFactory::createReader($objFile);
	//Ch??? ?????c d??? li???u
	$objData->setReadDataOnly(true);
	// Load d??? li???u sang d???ng ?????i t?????ng
	$objPHPExcel = $objData->load($fileName);
	//Ch???n trang c???n truy xu???t
	$sheet = $objPHPExcel->setActiveSheetIndex(0);
	//L???y ra s??? d??ng cu???i c??ng
	$Totalrow = $sheet->getHighestRow();	
	//L???y ra t??n c???t cu???i c??ng
	$LastColumn = $sheet->getHighestColumn();
	//Chuy???n ?????i t??n c???t ???? v??? v??? tr?? th???, VD: C l?? 3,D l?? 4
	$TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);
	//T???o m???ng ch???a d??? li???u
	$data = [];
	//Ti???n h??nh l???p qua t???ng ?? d??? li???u
	//----L???p d??ng, V?? d??ng ?????u l?? ti??u ????? c???t n??n ch??ng ta s??? l???p gi?? tr??? t??? d??ng 2 , n????u co?? Ti??u ?????? , 1 kh??ng co?? ti??u ??????
	for ($i = 2; $i <= $Totalrow; $i++) {
    //----L???p c???t
		for ($j = 0; $j < $TotalCol; $j++) {
			// Ti???n h??nh l???y gi?? tr??? c???a t???ng ?? ????? v??o m???ng
			$dataValue = $sheet->getCellByColumnAndRow($j, $i)->getValue();
			$dataValue = trim($dataValue);
			$data[$i - 1][$j] = $dataValue;
		}	
	}
	//Hi???n th??? m???ng d??? li???u
	return $data;
}
function round_up($value,$pre){
	return ceil($value*pow(10,$pre)) / pow(10,$pre);
}
function upload_oracle_download($dbMi_138){
	ini_set('memory_limit','-1'); // enabled the full memory available.
	ini_set('max_execution_time',99999999999);  // set time 10 minutes
	$data_oracle = getFileExcel('\\FSPDVN06\GroupDisk-Plan\Planning\Planning Thermal\update oracle for SO# WEB\data oracle download.xlsx');
	// get data	
	// process
	$count = 0;
	$check = true;
	$sql_delete = "TRUNCATE au_avery_thermal.oracle_download";	
	$dbMi_138->query($sql_delete);
	foreach ($data_oracle as $key => $value){	
		$ORDER_NUMBER = !empty($value[0])?addslashes($value[0]):'';
		$LINE_NUMBER = !empty($value[1])?addslashes($value[1]):'';
		if(!empty($LINE_NUMBER)){
			$LINE_NUMBERS = explode(".",$LINE_NUMBER);
			$LINE_NUMBER = $LINE_NUMBERS[0];
		}
		$SO_LINE = !empty($value[2])?addslashes($value[2]):'';
		$ORDERED_DATE = !empty($value[3])?addslashes($value[3]):0;
		if(!empty($ORDERED_DATE)){
			$ORDERED_DATE = PHPExcel_Style_NumberFormat::toFormattedString($ORDERED_DATE,'yyyy-mm-dd h:mm:ss');
		}
		$BILL_TO_CUSTOMER = !empty($value[4])?addslashes($value[4]):'';
		$SHIP_TO_CUSTOMER = !empty($value[5])?addslashes($value[5]):'';
		$ORDERED_ITEM = !empty($value[6])?addslashes($value[6]):'';
		$ITEM = !empty($value[7])?addslashes($value[7]):'';
		$ITEM_DESC = !empty($value[8])?addslashes($value[8]):'';
		$QTY = !empty($value[9])?addslashes($value[9]):'';
		$UNIT_SELLING_PRICE = !empty($value[10])?addslashes($value[10]):'';
		$EXTENDED_PRICE = !empty($value[11])?addslashes($value[11]):'';
		$STATUS = !empty($value[12])?addslashes($value[12]):'';
		$ORDERED_DATE_ = !empty($value[13])?addslashes($value[13]):'';
		if(!empty($ORDERED_DATE_)){
			$ORDERED_DATE_ = PHPExcel_Style_NumberFormat::toFormattedString($ORDERED_DATE_,'yyyy-mm-dd h:mm:ss');
		}
		$REQUEST_DATE = !empty($value[14])?addslashes($value[14]):'';
		if(!empty($REQUEST_DATE)){
			$REQUEST_DATE = PHPExcel_Style_NumberFormat::toFormattedString($REQUEST_DATE,'yyyy-mm-dd h:mm:ss');
		}
		$PROMISE_DATE = !empty($value[15])?addslashes($value[15]):'';
		if(!empty($PROMISE_DATE)){
			$PROMISE_DATE = PHPExcel_Style_NumberFormat::toFormattedString($PROMISE_DATE,'yyyy-mm-dd h:mm:ss');
		}
		$CS = !empty($value[16])?addslashes($value[16]):'';
		$ORDER_TYPE_NAME = !empty($value[17])?addslashes($value[17]):'';
		$RBO = !empty($value[18])?addslashes($value[18]):'';
		$PACKING_INSTRUCTIONS = !empty($value[20])?addslashes($value[20]):'';
		require_once("../..Database.php");
		
		// save database
		if(!empty($ORDER_NUMBER)){
			$sql = "INSERT INTO `avery_thermal`.`oracle_download` 
			(`ORDER_NUMBER`,`LINE_NUMBER`,`SO_LINE`,`ORDERED_DATE`,`BILL_TO_CUSTOMER`,`SHIP_TO_CUSTOMER`,`ORDERED_ITEM`,`ITEM`,`ITEM_DESC`,`QTY`,`UNIT_SELLING_PRICE`,`EXTENDED_PRICE`,`STATUS`,`ORDERED_DATE_`,`REQUEST_DATE`,`PROMISE_DATE`,`CS`,`ORDER_TYPE_NAME`,`SOLD_TO_CUSTOMER`,`PACKING_INSTRUCTIONS`) VALUES ('$ORDER_NUMBER','$LINE_NUMBER','$SO_LINE','$ORDERED_DATE','$BILL_TO_CUSTOMER','$SHIP_TO_CUSTOMER','$ORDERED_ITEM','$ITEM','$ITEM_DESC','$QTY','$UNIT_SELLING_PRICE','$EXTENDED_PRICE','$STATUS','$ORDERED_DATE_','$REQUEST_DATE','$PROMISE_DATE','$CS','$ORDER_TYPE_NAME','$RBO','$PACKING_INSTRUCTIONS')";			
			$check = $dbMi_138->query($sql);
			if(!$check){
				//echo $dbMi_138->error." AT INDEX:$key SQL:$sql";
				break;  
			}else{
				$count++;
			}
		}		
	}
	if($check){
        //echo "Affected rows: " . $count;
        //$dbMi_138->close();
		return '';
    }else{
        return  $dbMi_138->error." AT INDEX:$key SQL:$sql";
    }
}
?>