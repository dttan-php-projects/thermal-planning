<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
function createNO($maxValue)
{ 
    if(!empty($maxValue)){        
        $maxValue++;
        $lenMaxValue = strlen($maxValue);        
        if($maxValue<=99999){
            if($lenMaxValue===1){
                $maxValue = "0000".$maxValue;
            }else if($lenMaxValue===2){
                $maxValue = "000".$maxValue;
            }else if($lenMaxValue===3){
                $maxValue = "00".$maxValue;
            }
			else if($lenMaxValue===4){
                $maxValue = "0".$maxValue;
            }
        }
        return $maxValue;
    }else{
        return "00001";
    }
}

function duplicate($SO_LINE) 
{
	$conn = _conn();

	$sql = "SELECT * FROM `save_material` WHERE `SO_LINE` = '$SO_LINE' LIMIT 1; ";
	$query = mysqli_query($conn, $sql );
	if (!$query ) return false;

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if ($conn) mysqli_close($conn);
	return (count($results) > 0 ) ? true : false;

}

header("Content-Type: application/json");
if(empty($_COOKIE["VNRISIntranet"])){
	$response = [
		'status' => false,
		'mess' =>  'VUI LÒNG ĐĂNG NHẬP VÀO HỆ THỐNG ĐỂ TẠO LỆNH!!!'// use to debug code
	];
	echo json_encode($response);
}
$data = $_POST['data'];
// $data = '{"item":{"NO":"TP2103-38473","CREATE_DATE":"26-Mar-21","ORDER":"25-Mar-21","REQ":"06-Apr-21","PD":"31.Mar.21","SHIP_TO":"CONG TY TNHH POU HUNG VIET NAM","RBO":"PUMA AG","CS":"Tran, Anita","QTY":"52","REMARK_1":"7200pcs/roll","REMARK_2":"","REMARK_3":"","REMARK_4":"","REMARK_5":"","FORM_TYPE":"paxar","SAMPLE":"0","COLOR_BY_SIZE":"0"},"material":[{"SO_LINE":"51919191-1","ITEM":"PBS01111CN","INTERNAL_ITEM":"1-274816-000-00","ITEM_DES":"PUMA AG.PBS01111CN.......","QTY":"52","MATERIAL_CODE":"6-701130-000-00","MATERIAL_DES":"PBS01111-Basesheet., ups: 18, kho baseshet: 242mmx314.325mm","EA_SHT":"54","YD":"0","MT":"0","LENGTH":"37","WIDTH":"95","INK_CODE":"Laser Ink","INK_DES":"Laser Ink","INK_QTY":"1","MULTIPLE":"0","SAMPLE":"0","SO_UPS":"18"}]}';
if(!empty($data)){
    $formatData = json_decode($data,true);    
    // save date process after that
    if($formatData){
        // get data
        $itemData = $formatData["item"];
		/*
		echo "<pre>";
		print_r($formatData);die;
		*/
		$FORM_TYPE = $itemData['FORM_TYPE'];
		if($FORM_TYPE=='paxar'){
			$supplyData = $formatData["material"]; 
			require_once("../Database.php");

			// save item        
			$check_item = true;
			$check_supply = true;			  
			
			//@tandoan: Lấy số NUMBER NO: đã set lại NUMBER_NO, chỉ cần lấy NO này lưu vào data
			$NUMBER_NO = trim($itemData['NO']);
			$NUMBER_NO = str_replace("FR-FR","FR",$NUMBER_NO);

			$NoCurrent_arr = explode('-',$NUMBER_NO);
			$NoCurrent = $NoCurrent_arr[0];

			// save session prefix
			// $_SESSION[$FORM_TYPE]['prefix'] = $NoCurrent;
			setcookie($FORM_TYPE.'_prefix', $NoCurrent, time() + (86400 * 365), "/"); // 86400 = 1 day			

			
			$CREATE_DATE = !empty($itemData['CREATE_DATE'])?addslashes($itemData['CREATE_DATE']):'';
			$CREATE_DATE = date("Y-m-d",strtotime($CREATE_DATE));
			$SAVE_DATE_FORMAT = date("d-M-y",strtotime($CREATE_DATE));
			//setcookie('SAVE_DATE_THERMAL_'.$FORM_TYPE, $SAVE_DATE_FORMAT, time() + (86400 * 365), "/"); // 86400 = 1 day
			$ORDERED = !empty($itemData['ORDER'])?$itemData['ORDER']:'';
			if($ORDERED){
				$ORDERED = date("Y-m-d",strtotime($ORDERED));
			} 
			$REQ = !empty($itemData['REQ'])?$itemData['REQ']:'';
			if($REQ){
				$REQ = date("Y-m-d",strtotime($REQ));
			}
			$PD = !empty($itemData['PD'])?$itemData['PD']:'';     
			if (!empty($PD) ) {
				if (strpos($PD, '.') !== false ) {
					$PD = date("d.m.Y",strtotime($PD));
				} else {
					$PD = date("Y-m-d",strtotime($PD));
				}
			}
			
			// if($PD){
			// 	$PD = date("Y-m-d",strtotime($PD));
			// }
			$SHIP_TO = !empty($itemData['SHIP_TO'])?addslashes($itemData['SHIP_TO']):''; 
			$RBO = !empty($itemData['RBO'])?addslashes($itemData['RBO']):'';
			$CS = !empty($itemData['CS'])?addslashes($itemData['CS']):'';
			$QTY = !empty($itemData['QTY'])?addslashes($itemData['QTY']):0;
			$REMARK_1 = !empty($itemData['REMARK_1'])?addslashes($itemData['REMARK_1']):'';
			$REMARK_2 = !empty($itemData['REMARK_2'])?addslashes($itemData['REMARK_2']):'';
			$REMARK_3 = !empty($itemData['REMARK_3'])?addslashes($itemData['REMARK_3']):'';
			$REMARK_4 = !empty($itemData['REMARK_4'])?addslashes($itemData['REMARK_4']):'';
			$REMARK_5 = !empty($itemData['REMARK_5'])?addslashes($itemData['REMARK_5']):'';
			// call CHECK REMARK 6
			// testing
			/*
			$INTERNAL_ITEM_ARRAY[] = 'ABC';
			$RBO = 'AEO';
			$SHIP_TO = 'PHONG PHU';
			*/
			if(!empty($supplyData)){
				foreach($supplyData as $key=>$value_supply){
					if(!empty($value_supply['INTERNAL_ITEM'])){
						$INTERNAL_ITEM_ARRAY[] = trim(strtoupper($value_supply['INTERNAL_ITEM']));
					}					
				}
			}
			$sql_REQUEST = "SELECT GLID,RBO,CUSTOMER_NAME,NOTE FROM special_request";
			$rowsResultRequest = MiQuery($sql_REQUEST, $dbMi_138);
			$REMARK_6 = '';
			if(!empty($rowsResultRequest)){
				foreach ($rowsResultRequest as $rowRequest){
					$count_check = 0;
					$GLID_REQUEST = !empty($rowRequest['GLID'])?strtoupper($rowRequest['GLID']):'';
					$GLID_REQUEST = trim($GLID_REQUEST);
					$RBO_REQUEST = !empty($rowRequest['RBO'])?strtoupper($rowRequest['RBO']):'';
					$RBO_REQUEST = trim($RBO_REQUEST);
					$CUSTOMER_NAME_REQUEST = !empty($rowRequest['CUSTOMER_NAME'])?strtoupper($rowRequest['CUSTOMER_NAME']):'';
					$CUSTOMER_NAME_REQUEST = trim($CUSTOMER_NAME_REQUEST);
					$NOTE_REQUEST = !empty($rowRequest['NOTE'])?($rowRequest['NOTE']):'';
					// check					
					if(!empty($GLID_REQUEST)){
						if(in_array($GLID_REQUEST,$INTERNAL_ITEM_ARRAY)){
							$count_check++;
						}
					}else{
						$count_check++;
					}	
					if(!empty($RBO_REQUEST)){
						if(strpos(strtoupper($RBO),$RBO_REQUEST)!==false){
							$count_check++;
						}
					}else{
						$count_check++;
					}
					if(!empty($CUSTOMER_NAME_REQUEST)){
						if(strpos(strtoupper($SHIP_TO),$CUSTOMER_NAME_REQUEST)!==false){
							$count_check++;
						}
					}else{
						$count_check++;
					}
					if($count_check==3){
						$REMARK_6 = $NOTE_REQUEST;
						break;
					}					
				}
			}
			$FORM_TYPE = !empty($itemData['FORM_TYPE'])?addslashes($itemData['FORM_TYPE']):'';
			$GET_SAMPLE = !empty($itemData['SAMPLE'])?addslashes($itemData['SAMPLE']):'';
			$COLOR_BY_SIZE = !empty($itemData['COLOR_BY_SIZE'])?addslashes($itemData['COLOR_BY_SIZE']):'';
			$CREATED_BY = !empty($_COOKIE["VNRISIntranet"])?addslashes($_COOKIE["VNRISIntranet"]):''; 

			$sql_save_item="INSERT INTO `save_item` 
			(`CREATE_DATE`,`NUMBER_NO`,`ORDER`,`REQ`,`PD`,`SHIP_TO`,`RBO`,`CS`,`QTY`,`REMARK_1`,`REMARK_2`,`REMARK_3`,`REMARK_4`,`REMARK_5`,`REMARK_6`,`FORM_TYPE`,`SAMPLE`,`CREATED_BY`,`COLOR_BY_SIZE`) 
			VALUES ('$CREATE_DATE','$NUMBER_NO','$ORDERED','$REQ','$PD','$SHIP_TO','$RBO','$CS','$QTY','$REMARK_1','$REMARK_2','$REMARK_3','$REMARK_4','$REMARK_5','$REMARK_6','$FORM_TYPE','$GET_SAMPLE','$CREATED_BY','$COLOR_BY_SIZE')";
			$check_item = $dbMi_138->query($sql_save_item);    

			if($check_item){

				$insert_id = $dbMi_138->insert_id;

				// // Sau khi save xong thì kiểm tra xem trong tmp có NO này chưa, chưa thì lưu vào
				$sql_tmp = "SELECT NUMBER_NO FROM save_item_tmp where NUMBER_NO = '$NUMBER_NO' order by NUMBER_NO desc limit 0,1";
				$no_max_tmp = MiQuery($sql_tmp,$dbMi_138); 
				if (empty($no_max_tmp)) {
					$insert_tmp = "INSERT INTO `save_item_tmp` (`NUMBER_NO`, `FORM_TYPE`) VALUES ('$NUMBER_NO', '$FORM_TYPE') ";
					$check_save_tmp = $dbMi_138->query($insert_tmp);  // Không cần check
				}

				if($insert_id){
					// update material					
					if(!empty($supplyData)){						
						foreach($supplyData as $key=>$value_supply){
							$SO_LINE = !empty($value_supply['SO_LINE'])?addslashes($value_supply['SO_LINE']):'';
							$ITEM = !empty($value_supply['ITEM'])?addslashes($value_supply['ITEM']):'';
							$INTERNAL_ITEM = !empty($value_supply['INTERNAL_ITEM'])?addslashes($value_supply['INTERNAL_ITEM']):'';
							$ITEM_DES = !empty($value_supply['ITEM_DES'])?addslashes($value_supply['ITEM_DES']):'';
							$QTY = !empty($value_supply['QTY'])?addslashes($value_supply['QTY']):0;
							$MATERIAL_CODE = !empty($value_supply['MATERIAL_CODE'])?addslashes($value_supply['MATERIAL_CODE']):'';
							$MATERIAL_DES = !empty($value_supply['MATERIAL_DES'])?addslashes($value_supply['MATERIAL_DES']):'';
							$EA_SHT = !empty($value_supply['EA_SHT'])?addslashes($value_supply['EA_SHT']):0;
							$YD = !empty($value_supply['YD'])?addslashes($value_supply['YD']):0;
							$MT = !empty($value_supply['MT'])?addslashes($value_supply['MT']):0;
							$LENGTH = !empty($value_supply['LENGTH'])?addslashes($value_supply['LENGTH']):0;
							$WIDTH = !empty($value_supply['WIDTH'])?addslashes($value_supply['WIDTH']):0;
							$INK_CODE = !empty($value_supply['INK_CODE'])?addslashes($value_supply['INK_CODE']):'';
							$INK_DES = !empty($value_supply['INK_DES'])?addslashes($value_supply['INK_DES']):'';
							$INK_QTY = !empty($value_supply['INK_QTY'])?addslashes($value_supply['INK_QTY']):0;
							$MULTIPLE = !empty($value_supply['MULTIPLE'])?addslashes($value_supply['MULTIPLE']):'';
							$SAMPLE = !empty($value_supply['SAMPLE'])?addslashes($value_supply['SAMPLE']):0;
							$SO_UPS = !empty($value_supply['SO_UPS'])?addslashes($value_supply['SO_UPS']):0;
							$ID_SAVE_ITEM = $NUMBER_NO;

							$sql_save_supply = "INSERT INTO `save_material` 
							(`CREATE_DATE`,`ID_SAVE_ITEM`,`SO_LINE`, `ITEM`,`INTERNAL_ITEM`,`ITEM_DES`,`QTY`,`MATERIAL_CODE`,`MATERIAL_DES`,`EA_SHT`,`YD`,`MT`,`LENGTH`,`WIDTH`,`INK_CODE`,`INK_DES`,`INK_QTY`,`MULTIPLE`,`SAMPLE`,`SO_UPS`)
							VALUES('$CREATE_DATE','$ID_SAVE_ITEM','$SO_LINE','$ITEM','$INTERNAL_ITEM','$ITEM_DES','$QTY','$MATERIAL_CODE','$MATERIAL_DES','$EA_SHT','$YD','$MT','$LENGTH','$WIDTH','$INK_CODE','$INK_DES','$INK_QTY','$MULTIPLE','$SAMPLE','$SO_UPS')";
							$check_supply = $dbMi_138->query($sql_save_supply);                                           
						}
					} 
					if($check_supply){
						$response = [
							'status' => true,
							'mess' =>  '',// use to debug code
							'NUMBER_NO' => $NUMBER_NO
						];
					}else{
						$response = [
							'status' => false,
							'mess' =>  $dbMi_138->error// use to debug code
						];
					}                
				}            
				
			}else{
				$response = [
					'status' => false,
					'mess' =>  $dbMi_138->error// use to debug code
				];
			}
		}elseif($FORM_TYPE=='trim'){
			/*
			echo "<pre>";
			print_r($formatData);die;
			*/
			$supplyData = $formatData["material"]; 
			require_once("../Database.php");
			// save item        
			$check_item = true;
			$check_supply = true;			  
			
			//@tandoan: Lấy số NUMBER NO: đã set lại NUMBER_NO, chỉ cần lấy NO này lưu vào data
			$NUMBER_NO = trim($itemData['NO']);
			$NUMBER_NO = str_replace("FR-FR","FR",$NUMBER_NO);

			$NoCurrent_arr = explode('-',$NUMBER_NO);
			$NoCurrent = $NoCurrent_arr[0];

			// save session prefix
			$DATA_RECEIVED = !empty($itemData['DATA_RECEIVED'])?addslashes($itemData['DATA_RECEIVED']):'';
			$SO_LAN = !empty($itemData['SO_LAN'])?addslashes($itemData['SO_LAN']):'';
			setcookie($FORM_TYPE.'_prefix', $NoCurrent, time() + (86400 * 365), "/"); // 86400 = 1 day
			setcookie('trim_sips_data_received',$DATA_RECEIVED, time() + (86400 * 365), "/"); // 86400 = 1 day
			setcookie('trim_sips_so_lan',$SO_LAN, time() + (86400 * 365), "/"); // 86400 = 1 day
		
			
			$CREATE_DATE = !empty($itemData['CREATE_DATE'])?addslashes($itemData['CREATE_DATE']):'';
			$CREATE_DATE = date("Y-m-d",strtotime($CREATE_DATE)); 
			$SAVE_DATE_FORMAT = date("d-M-y",strtotime($CREATE_DATE));
			//setcookie('SAVE_DATE_THERMAL_'.$FORM_TYPE, $SAVE_DATE_FORMAT, time() + (86400 * 365), "/"); // 86400 = 1 day
			$ORDERED = !empty($itemData['ORDER'])?$itemData['ORDER']:'';
			if($ORDERED){
				$ORDERED = date("Y-m-d",strtotime($ORDERED));
			} 
			$REQ = !empty($itemData['REQ'])?$itemData['REQ']:'';
			if($REQ){
				$REQ = date("Y-m-d",strtotime($REQ));
			}

			// Thay đổi lại PD: Nếu PD trống thì PD = ngày làm lệnh thực tế + 5 (nếu chủ nhật +1)
			$PD = !empty($itemData['PD'])?$itemData['PD']:'';     
			if (!empty($PD) ) {
				if (strpos($PD, '.') !== false ) {
					$PD = date("d.m.Y",strtotime($PD));
				} else {
					$PD = date("Y-m-d",strtotime($PD));
				}
			}
			$SHIP_TO = !empty($itemData['SHIP_TO'])?addslashes($itemData['SHIP_TO']):''; 
			$RBO = !empty($itemData['RBO'])?addslashes($itemData['RBO']):'';
			$CS = !empty($itemData['CS'])?addslashes($itemData['CS']):'';
			$QTY = !empty($itemData['QTY'])?addslashes($itemData['QTY']):0;
			$REMARK_1 = !empty($itemData['REMARK_1'])?addslashes($itemData['REMARK_1']):'';
			$REMARK_2 = !empty($itemData['REMARK_2'])?addslashes($itemData['REMARK_2']):'';
			$REMARK_3 = !empty($itemData['REMARK_3'])?addslashes($itemData['REMARK_3']):'';
			$REMARK_4 = !empty($itemData['REMARK_4'])?addslashes($itemData['REMARK_4']):'';
			$REMARK_5 = !empty($itemData['REMARK_5'])?addslashes($itemData['REMARK_5']):'';
			$FORM_TYPE = !empty($itemData['FORM_TYPE'])?addslashes($itemData['FORM_TYPE']):'';
			$GET_SAMPLE = !empty($itemData['SAMPLE'])?addslashes($itemData['SAMPLE']):'';
			$CREATED_BY = !empty($_COOKIE["VNRISIntranet"])?addslashes($_COOKIE["VNRISIntranet"]):'';
			$sql_save_item="INSERT INTO `save_item` 
			(`CREATE_DATE`,`NUMBER_NO`,`ORDER`,`REQ`,`PD`,`SHIP_TO`,`RBO`,`CS`,`QTY`,`REMARK_1`,`REMARK_2`,`REMARK_3`,`REMARK_4`,`REMARK_5`,`FORM_TYPE`,`SAMPLE`,`DATA_RECEIVED`,`SO_LAN`,`CREATED_BY`) 
			VALUES ('$CREATE_DATE','$NUMBER_NO','$ORDERED','$REQ','$PD','$SHIP_TO','$RBO','$CS','$QTY','$REMARK_1','$REMARK_2','$REMARK_3','$REMARK_4','$REMARK_5','$FORM_TYPE','$GET_SAMPLE','$DATA_RECEIVED','$SO_LAN','$CREATED_BY')";
			// echo $sql_save_item;die;
			$check_item = $dbMi_138->query($sql_save_item);    
			if($check_item){
				$insert_id = $dbMi_138->insert_id;

				// // Sau khi save xong thì kiểm tra xem trong tmp có NO này chưa, chưa thì lưu vào
				$sql_tmp = "SELECT NUMBER_NO FROM save_item_tmp where NUMBER_NO = '$NUMBER_NO' order by NUMBER_NO desc limit 0,1";
				$no_max_tmp = MiQuery($sql_tmp,$dbMi_138); 
				if (empty($no_max_tmp)) {
					$insert_tmp = "INSERT INTO `save_item_tmp` (`NUMBER_NO`, `FORM_TYPE`) VALUES ('$NUMBER_NO', '$FORM_TYPE') ";
					$check_save_tmp = $dbMi_138->query($insert_tmp);  // Không cần check
				}

				if($insert_id){
					// update material					
					if(!empty($supplyData)){
						foreach($supplyData as $key=>$value_supply){
							$SO_LINE = !empty($value_supply['SO_LINE'])?addslashes($value_supply['SO_LINE']):'';
							$ITEM = !empty($value_supply['ITEM'])?addslashes($value_supply['ITEM']):'';
							$INTERNAL_ITEM = !empty($value_supply['INTERNAL_ITEM'])?addslashes($value_supply['INTERNAL_ITEM']):'';
							$QTY = !empty($value_supply['QTY'])?addslashes($value_supply['QTY']):0;
							$MATERIAL_CODE = !empty($value_supply['MATERIAL_CODE'])?addslashes($value_supply['MATERIAL_CODE']):'';
							$MATERIAL_QTY = !empty($value_supply['MATERIAL_QTY'])?addslashes($value_supply['MATERIAL_QTY']):0;
							$LENGTH = !empty($value_supply['LENGTH'])?addslashes($value_supply['LENGTH']):0;
							$WIDTH = !empty($value_supply['WIDTH'])?addslashes($value_supply['WIDTH']):0;
							$INK_CODE = !empty($value_supply['INK_CODE'])?addslashes($value_supply['INK_CODE']):'';
							$INK_QTY = !empty($value_supply['INK_QTY'])?addslashes($value_supply['INK_QTY']):0;
							$MULTIPLE = !empty($value_supply['MULTIPLE'])?addslashes($value_supply['MULTIPLE']):'';
							$SAMPLE = !empty($value_supply['SAMPLE'])?addslashes($value_supply['SAMPLE']):0;
							$SO_UPS = !empty($value_supply['SO_UPS'])?addslashes($value_supply['SO_UPS']):0;
							$ID_SAVE_ITEM = $NUMBER_NO;
							$sql_save_supply = "INSERT INTO `save_material` 
							(`CREATE_DATE`,`ID_SAVE_ITEM`,`SO_LINE`, `ITEM`,`INTERNAL_ITEM`,`QTY`,`MATERIAL_CODE`,`MATERIAL_QTY`,`LENGTH`,`WIDTH`,`INK_CODE`,`INK_QTY`,`MULTIPLE`,`SAMPLE`,`SO_UPS`)
							VALUES('$CREATE_DATE','$ID_SAVE_ITEM','$SO_LINE','$ITEM','$INTERNAL_ITEM','$QTY','$MATERIAL_CODE','$MATERIAL_QTY','$LENGTH','$WIDTH','$INK_CODE','$INK_QTY','$MULTIPLE','$SAMPLE','$SO_UPS')";
							$check_supply = $dbMi_138->query($sql_save_supply);                                           
						}
					} 
					if($check_supply){
						$response = [
							'status' => true,
							'mess' =>  '',// use to debug code
							'NUMBER_NO' => $NUMBER_NO
						];
					}else{
						$response = [
							'status' => false,
							'mess' =>  $dbMi_138->error// use to debug code
						];
					}                
				}            
				
			}else{
				$response = [
					'status' => false,
					'mess' =>  $dbMi_138->error// use to debug code
				];
			}
		}elseif($FORM_TYPE=='sips'){
			/*
			echo "<pre>";
			print_r($formatData);die;
			*/
			$supplyData = $formatData["material"]; 
			require_once("../Database.php");
			// save item        
			$check_item = true;
			$check_supply = true;			  
			
			//@tandoan: Lấy số NUMBER NO: đã set lại NUMBER_NO, chỉ cần lấy NO này lưu vào data
			$NUMBER_NO = trim($itemData['NO']);
			$NUMBER_NO = str_replace("FR-FR","FR",$NUMBER_NO);

			$NoCurrent_arr = explode('-',$NUMBER_NO);
			$NoCurrent = $NoCurrent_arr[0];

			// save session prefix
			$DATA_RECEIVED = !empty($itemData['DATA_RECEIVED'])?addslashes($itemData['DATA_RECEIVED']):'';
			$SO_LAN = !empty($itemData['SO_LAN'])?addslashes($itemData['SO_LAN']):'';
			setcookie($FORM_TYPE.'_prefix', $NoCurrent, time() + (86400 * 365), "/"); // 86400 = 1 day
			setcookie('trim_sips_data_received',$DATA_RECEIVED, time() + (86400 * 365), "/"); // 86400 = 1 day
			setcookie('trim_sips_so_lan',$SO_LAN, time() + (86400 * 365), "/"); // 86400 = 1 day

			
			$CREATE_DATE = !empty($itemData['CREATE_DATE'])?addslashes($itemData['CREATE_DATE']):'';
			$CREATE_DATE = date("Y-m-d",strtotime($CREATE_DATE)); 
			$SAVE_DATE_FORMAT = date("d-M-y",strtotime($CREATE_DATE));
			//setcookie('SAVE_DATE_THERMAL_'.$FORM_TYPE, $SAVE_DATE_FORMAT, time() + (86400 * 365), "/"); // 86400 = 1 day
			$ORDERED = !empty($itemData['ORDER'])?$itemData['ORDER']:'';
			if($ORDERED){
				$ORDERED = date("Y-m-d",strtotime($ORDERED));
			} 
			$REQ = !empty($itemData['REQ'])?$itemData['REQ']:'';
			if($REQ){
				$REQ = date("Y-m-d",strtotime($REQ));
			}

			// Thay đổi lại PD: Nếu PD trống thì PD = ngày làm lệnh thực tế + 5 (nếu chủ nhật +1)
			$PD = !empty($itemData['PD'])?$itemData['PD']:'';     
			if (!empty($PD) ) {
				if (strpos($PD, '.') !== false ) {
					$PD = date("d.m.Y",strtotime($PD));
				} else {
					$PD = date("Y-m-d",strtotime($PD));
				}
			}
			
			$SHIP_TO = !empty($itemData['SHIP_TO'])?addslashes($itemData['SHIP_TO']):''; 
			$RBO = !empty($itemData['RBO'])?addslashes($itemData['RBO']):'';
			$CS = !empty($itemData['CS'])?addslashes($itemData['CS']):'';
			$QTY = !empty($itemData['QTY'])?addslashes($itemData['QTY']):0;
			$REMARK_1 = !empty($itemData['REMARK_1'])?addslashes($itemData['REMARK_1']):'';
			$REMARK_2 = !empty($itemData['REMARK_2'])?addslashes($itemData['REMARK_2']):'';
			$REMARK_3 = !empty($itemData['REMARK_3'])?addslashes($itemData['REMARK_3']):'';
			$REMARK_4 = !empty($itemData['REMARK_4'])?addslashes($itemData['REMARK_4']):'';
			$FORM_TYPE = !empty($itemData['FORM_TYPE'])?addslashes($itemData['FORM_TYPE']):'';
			$GET_SAMPLE = !empty($itemData['SAMPLE'])?addslashes($itemData['SAMPLE']):'';
			$CREATED_BY = !empty($_COOKIE["VNRISIntranet"])?addslashes($_COOKIE["VNRISIntranet"]):'';
			$sql_save_item="INSERT INTO `save_item` 
			(`CREATE_DATE`,`NUMBER_NO`,`ORDER`,`REQ`,`PD`,`SHIP_TO`,`RBO`,`CS`,`QTY`,`REMARK_1`,`REMARK_2`,`REMARK_3`,`REMARK_4`,`FORM_TYPE`,`SAMPLE`,`DATA_RECEIVED`,`SO_LAN`,`CREATED_BY`) 
			VALUES ('$CREATE_DATE','$NUMBER_NO','$ORDERED','$REQ','$PD','$SHIP_TO','$RBO','$CS','$QTY','$REMARK_1','$REMARK_2','$REMARK_3','$REMARK_4','$FORM_TYPE','$GET_SAMPLE','$DATA_RECEIVED','$SO_LAN','$CREATED_BY')";
			// echo $sql_save_item;die;
			$check_item = $dbMi_138->query($sql_save_item);    
			if($check_item){
				$insert_id = $dbMi_138->insert_id;

				// // Sau khi save xong thì kiểm tra xem trong tmp có NO này chưa, chưa thì lưu vào
				$sql_tmp = "SELECT NUMBER_NO FROM save_item_tmp where NUMBER_NO = '$NUMBER_NO' order by NUMBER_NO desc limit 0,1";
				$no_max_tmp = MiQuery($sql_tmp,$dbMi_138); 
				if (empty($no_max_tmp)) {
					$insert_tmp = "INSERT INTO `save_item_tmp` (`NUMBER_NO`, `FORM_TYPE`) VALUES ('$NUMBER_NO', '$FORM_TYPE') ";
					$check_save_tmp = $dbMi_138->query($insert_tmp);  // Không cần check
				}
				
				if($insert_id){
					// update material					
					if(!empty($supplyData)){
						foreach($supplyData as $key=>$value_supply){
							$SO_LINE = !empty($value_supply['SO_LINE'])?addslashes($value_supply['SO_LINE']):'';
							$ITEM = !empty($value_supply['ITEM'])?addslashes($value_supply['ITEM']):'';
							$INTERNAL_ITEM = !empty($value_supply['INTERNAL_ITEM'])?addslashes($value_supply['INTERNAL_ITEM']):'';
							$QTY = !empty($value_supply['QTY'])?addslashes($value_supply['QTY']):0;
							$MATERIAL_CODE = !empty($value_supply['MATERIAL_CODE'])?addslashes($value_supply['MATERIAL_CODE']):'';
							$MATERIAL_QTY = !empty($value_supply['MATERIAL_QTY'])?addslashes($value_supply['MATERIAL_QTY']):0;
							$LENGTH = !empty($value_supply['LENGTH'])?addslashes($value_supply['LENGTH']):0;
							$WIDTH = !empty($value_supply['WIDTH'])?addslashes($value_supply['WIDTH']):0;
							$INK_CODE = !empty($value_supply['INK_CODE'])?addslashes($value_supply['INK_CODE']):'';
							$INK_QTY = !empty($value_supply['INK_QTY'])?addslashes($value_supply['INK_QTY']):0;
							$MULTIPLE = !empty($value_supply['MULTIPLE'])?addslashes($value_supply['MULTIPLE']):'';
							$SAMPLE = !empty($value_supply['SAMPLE'])?addslashes($value_supply['SAMPLE']):0;
							$SO_UPS = !empty($value_supply['SO_UPS'])?addslashes($value_supply['SO_UPS']):0;
							$ID_SAVE_ITEM = $NUMBER_NO;
							$sql_save_supply = "INSERT INTO `save_material` 
							(`CREATE_DATE`,`ID_SAVE_ITEM`,`SO_LINE`, `ITEM`,`INTERNAL_ITEM`,`QTY`,`MATERIAL_CODE`,`MATERIAL_QTY`,`LENGTH`,`WIDTH`,`INK_CODE`,`INK_QTY`,`MULTIPLE`,`SAMPLE`,`SO_UPS`)
							VALUES('$CREATE_DATE','$ID_SAVE_ITEM','$SO_LINE','$ITEM','$INTERNAL_ITEM','$QTY','$MATERIAL_CODE','$MATERIAL_QTY','$LENGTH','$WIDTH','$INK_CODE','$INK_QTY','$MULTIPLE','$SAMPLE','$SO_UPS')";
							$check_supply = $dbMi_138->query($sql_save_supply);                                           
						}
					} 
					if($check_supply){
						$response = [
							'status' => true,
							'mess' =>  '',// use to debug code
							'NUMBER_NO' => $NUMBER_NO
						];
					}else{
						$response = [
							'status' => false,
							'mess' =>  $dbMi_138->error// use to debug code
						];
					}                
				}            
				
			}else{
				$response = [
					'status' => false,
					'mess' =>  $dbMi_138->error// use to debug code
				];
			}
		}                
    }
    echo json_encode($response);
}
?>