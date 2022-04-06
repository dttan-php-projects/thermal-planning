<?php
	function isweekend($date){
		$date = strtotime($date);
		$date = date("l", $date);
		$date = strtolower($date);
		if($date == "saturday" || $date == "sunday") {
			return true;
		} else {
			return false;
		}
	}

	function remarkShortLT($start_date, $end_date ) 
	{
		$remark = '';
		$count = 0;
		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));

		$mid_date = $start_date;
		$datediff = floor(abs(strtotime($start_date) - strtotime($end_date) ) / (60*60*24) );
		if ($start_date == $end_date) {
			$remark = '<div style="font-weight:bold;font-size:35px;border:2px solid blue;padding:1px;background:yellow;color:blue;">D=0</div>';
		} else if ($datediff <=2 ) {
			$remark = '<div style="font-weight:bold;font-size:20px;border:2px solid blue;padding:1px;background:yellow;color:blue;">SHORT LEADTIME</div>';
		} else {

			// Trường hợp nếu như ngày làm đơn = CRD > 2: Kiểm tra xem khoảng thời gian từ ngày làm đơn đến ngày request date có thứ 7, cn không
			// Nếu có thì trừ hết tất cả ngày thứ 7, cn ra, nếu còn <=2 thì vẫn là short LT
			
			for ($i=1;$i<=$datediff;$i++) {
				$mid_date = date('Y-m-d', strtotime($mid_date . ' +1 day'));
				$date_check = isweekend($mid_date);
				if ($date_check == true) {
					$count++;
				}
			}

			if ( ($datediff - $count) <=2 ) {
				$remark = '<span style="color:blue;font-weigth:bold;font-size:20px;">SHORT LEADTIME</span>';
			}
		}

		// result
			return $remark;
	}

	function shortLT($start_date, $end_date ) 
	{
		$short_lt = false;
		$count = 0;
		$mid_date = $start_date;
		$datediff = floor(abs(strtotime($start_date) - strtotime($end_date) ) / (60*60*24) );
		if ($datediff <=2 ) {
			$short_lt = true;
		} else {
			
			for ($i=1;$i<=$datediff;$i++) {
				$mid_date = date('Y-m-d', strtotime($mid_date . ' +1 day'));
				$date_check = isweekend($mid_date);
				if ($date_check == true) {
					$count++;
				}
			}

			if ( ($datediff - $count) <=2 ) {
				$short_lt = true;
			}
		}

		return $short_lt;

	}

	

	function remarkCCO($supplyList, $ship_to_customer ) 
	{
		
		$remarkCOO = '';
		
		$itemList = array(
			'25HADIASAUS (VN)',
			'25HADIASSMCST (VN)',
			'25HADIBCST9 (VN)',
			'25HADIOSD1(VN)-01',
			'25HCASELABEL-WR (VN)'
		);

		$ship_to_customer_check = 'CONG TY TNHH TUNTEX SOC TRANG VIET NAM';
		if ($ship_to_customer == $ship_to_customer_check ) {
			foreach ($supplyList as $supply_check ) {
				$itemCheck = !empty($supply_check['INTERNAL_ITEM']) ? $supply_check['INTERNAL_ITEM'] : '';
				foreach ($itemList as $item ) {
					if ($itemCheck == $item ) {
						$remarkCOO = 'DÁN COO "MADE IN KOREA" TRÊN RIBBON';
						break;
					}
				}

				if (!empty($remarkCOO ) ) break;
				
			}
		}

		return $remarkCOO;
		
	}


	$sql_supply = "SELECT * FROM save_material WHERE ID_SAVE_ITEM='$id' ORDER BY LENGTH(SO_LINE), SO_LINE ASC;";
	$result_supply = MiQuery($sql_supply,$dbMi_138);
	$result_supply_makalot = MiQuery($sql_supply,$dbMi_138);

	$arr_supply = [];
	$arr_so_line = [];
	// // $machineShow = array();
	if(!empty($result_supply)){
		$count_data=0;
		foreach ($result_supply as $key => $supply) {  
			$count_data++;    
			$arr_supply[$key]['SO_LINE'] 							=  !empty($supply['SO_LINE'])?$supply['SO_LINE']:'';	
			$arr_so_line[]  										=  !empty($supply['SO_LINE'])?$supply['SO_LINE']:'';		
			$arr_supply[$key]['ITEM'] 								=  !empty($supply['ITEM'])?$supply['ITEM']:'';	
			$arr_supply[$key]['INTERNAL_ITEM'] 	 					=  !empty($supply['INTERNAL_ITEM'])?$supply['INTERNAL_ITEM']:'';	
			$arr_supply[$key]['ITEM_DES'] 							=  !empty($supply['ITEM_DES'])?$supply['ITEM_DES']:'';	
			$arr_supply[$key]['QTY'] 								=  !empty($supply['QTY'])?$supply['QTY']:0;	
			$arr_supply[$key]['MATERIAL_CODE'] 						=  !empty($supply['MATERIAL_CODE'])?$supply['MATERIAL_CODE']:'';	
			$arr_supply[$key]['MATERIAL_DES'] 						=  !empty($supply['MATERIAL_DES'])?$supply['MATERIAL_DES']:'';	
			$arr_supply[$key]['EA_SHT'] 							=  !empty($supply['EA_SHT'])?$supply['EA_SHT']:0;	
			$arr_supply[$key]['YD'] 								=  !empty($supply['YD'])?$supply['YD']:0;	
			$arr_supply[$key]['MT'] 								=  !empty($supply['MT'])?$supply['MT']:0;	
			$arr_supply[$key]['MATERIAL_QTY'] 						=  !empty($supply['MATERIAL_QTY'])?$supply['MATERIAL_QTY']:0;	
			$arr_supply[$key]['LENGTH'] 							=  !empty($supply['LENGTH'])?$supply['LENGTH']:0;	
			$arr_supply[$key]['WIDTH'] 								=  !empty($supply['WIDTH'])?$supply['WIDTH']:0;	
			$arr_supply[$key]['INK_CODE'] 							=  !empty($supply['INK_CODE'])?$supply['INK_CODE']:'';	
			$arr_supply[$key]['INK_DES'] 							=  !empty($supply['INK_DES'])?$supply['INK_DES']:'';	
			$arr_supply[$key]['INK_QTY'] 							=  !empty($supply['INK_QTY'])?$supply['INK_QTY']:0;	
			$arr_supply[$key]['MULTIPLE'] 							=  !empty($supply['MULTIPLE'])?$supply['MULTIPLE']:'';	
			$arr_supply[$key]['SAMPLE'] 							=  !empty($supply['SAMPLE'])?$supply['SAMPLE']:'';	
			$arr_supply[$key]['SO_UPS'] 							=  !empty($supply['SO_UPS'])?$supply['SO_UPS']:'';	
			
		}
	}
	$count_material = count($arr_supply);
	$SO_LINE_TEXT = '';
	if(count($arr_so_line)==1){
		$SO_LINE_TEXT = $arr_so_line[0];
	}else{
		$SO_LINE_TEXT = $arr_so_line[0];
		$SO_LINE_TEXT = substr($SO_LINE_TEXT,0,8);
	}
	//echo $SO_LINE_TEXT;die;
	if($FORM_TYPE=='paxar'){
		$BARCODE = '<img style="height:85%" src="barcode.php?text='.$SO_LINE_TEXT.'" />';
	}else{
		$BARCODE = '<img style="width:85%" src="barcode.php?text='.$SO_LINE_TEXT.'" />';//@TanDoan: 60% -> 85%
	}


	//@tandoan: MAKALOT  
	$REMARK_MAKALOT = '';
	$REMARK_SAMPLE = '';
	$REMARK_SHORT_LT = '';
	$REMARK_FR = '';
	$result_makalot = '';
	$remarkFRUIC = '';

	$SO_LINE_MAKALOT = $result_supply_makalot[0]['SO_LINE'];
	$SO_LINE_MAKALOT_ARR = explode('-',$SO_LINE_MAKALOT);
	if (!empty($SO_LINE_MAKALOT_ARR[0])) {
		$sql_makalot = "SELECT BILL_TO_CUSTOMER, ORDER_TYPE_NAME, REQUEST_DATE FROM vnso WHERE ORDER_NUMBER = '$SO_LINE_MAKALOT_ARR[0]' ORDER BY ID DESC LIMIT 1 ";
		$query_makalot = mysqli_query($conn, $sql_makalot); 
		if (mysqli_num_rows($query_makalot ) > 0 ) {
			$result_makalot = mysqli_fetch_array($query_makalot, MYSQLI_ASSOC);
		} else {
			$sql_makalot = "SELECT BILL_TO_CUSTOMER, ORDER_TYPE_NAME, REQUEST_DATE FROM vnso_total WHERE ORDER_NUMBER = '$SO_LINE_MAKALOT_ARR[0]' ORDER BY ID DESC LIMIT 1 ";

			$query_makalot = mysqli_query($conn, $sql_makalot);
			if (mysqli_num_rows($query_makalot) > 0 ) {
				$result_makalot = mysqli_fetch_array($query_makalot, MYSQLI_ASSOC);
			}
			
		}

		if (!empty($result_makalot) ) {
			$BILL_TO_MAKALOT = !empty($result_makalot['BILL_TO_CUSTOMER'])?strtoupper($result_makalot['BILL_TO_CUSTOMER']):'';
			
			// Lấy Order type name, neu bang la VN SAM, thi hien thi remark: SAMPLE
			$ORDER_TYPE_NAME = !empty($result_makalot['ORDER_TYPE_NAME'])?strtoupper($result_makalot['ORDER_TYPE_NAME']):'';
			
			if (strpos($BILL_TO_MAKALOT,'MAKALOT IND')!==false) {
				$REMARK_MAKALOT = 'Dùng form trim card của KH';
			}

			if (strpos($ORDER_TYPE_NAME,'VN SAM')!==false) {
				$REMARK_SAMPLE = 'SAMPLE';
			} else if  (strpos($ORDER_TYPE_NAME,'QR')!==false) {
				//$REMARK_FR = 'FR';
				$REMARK_FR = '<div style="text-shadow: 5px 2px 4px grey;font-weight:bold;font-size:50px;border-radius:8%;border:2px solid blue;padding:1px;background:yellow;color:blue;">FR</div>';
			}

			// Cập nhật remark FRU IC LONG HAU
				$remarkFRUIC = remarkFRUIC($ORDER_TYPE_NAME);
			
			// SHORT LT: Lay ngay Request - ngay lam lenh:
			// Cập nhật thêm D0 nếu ngày làm lệnh = CRD = 0. <=2: SHORT LT

			// $ShortLT = shortLT($SAVE_DATE, $result_makalot['REQUEST_DATE']);
			// if ($ShortLT == true ) {
			// 	$REMARK_SHORT_LT = '<span style="color:blue;font-weigth:bold;font-size:20px;">SHORT LEADTIME</span>';
			// } else {
			// 	// $REMARK_SHORT_LT = $SAVE_DATE_LT;
			// }

			$REMARK_SHORT_LT = remarkShortLT($SAVE_DATE, $REQ);
		}
	}


	// Lay cot SECURITY
	$SECURITY_SHOW = '';
	$sql_master_bom = "SELECT `SECURITY` FROM `master_bom` WHERE INTERNAL_ITEM = '" . $arr_supply[0]['INTERNAL_ITEM'] . "' LIMIT 1";
	$SECURITY = MiQuery($sql_master_bom,$dbMi_138);

	if (strtoupper(trim($SECURITY)) == 'YES' || strtoupper(trim($SECURITY)) == 'NO' || empty($SECURITY) ) {
		$SECURITY_SHOW = '';
	} else {
		$SECURITY_SHOW = strtoupper($SECURITY);
	}

	// @quang.phan:Remark: DÁN COO "MADE IN KOREA" TRÊN RIBBON
	$remarkCCO = remarkCCO($result_supply_makalot, $SHIP_TO);


?>