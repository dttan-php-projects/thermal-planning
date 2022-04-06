<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('max_execution_time',300);  // set time 5 minutes
require_once("../Database.php");

$fields = 'FORM_TYPE,s_m.SO_LINE,s_m.CREATE_DATE,NUMBER_NO,REQ,PD,s_m.INTERNAL_ITEM,s_i.RBO,s_m.ITEM,ITEM_DES,s_m.QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,MATERIAL_QTY,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,SO_UPS,FORM_TYPE,CREATED_BY,s_i.REMARK_4, s_i.SHIP_TO';
$FROM_DATE = $_GET['from_date_value'];
$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
$TO_DATE = $_GET['to_date_value'];
$TO_DATE = date('Y-m-d',strtotime($TO_DATE));

$conn = _conn();
$query = "SELECT $fields FROM save_material as s_m JOIN save_item as s_i ON s_i.NUMBER_NO = s_m.ID_SAVE_ITEM where (s_m.CREATE_DATE>='$FROM_DATE' AND s_m.CREATE_DATE<='$TO_DATE') order by s_m.ID asc";
//echo $query;      die;  
$rowsResult = MiQuery($query, $conn);
//output data in XML format 
function formatDate($value){
	return date('d-M-y',strtotime($value));
}


// load packing and attachment
function remarkPackingInstr($ORDER_NUMBER, $LINE_NUMBER) 
{
	$results = array();
	$conn = conn("au_avery");
	$conn2 = conn();

	$sql = "SELECT CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS FROM vnso WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return '';
	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) {
		$sql = "SELECT CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS FROM vnso_total WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn, $sql);
		if (!$query ) return '';
		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) {
			// get BOM data
			$sql = "SELECT REMARK_3_PACKING AS PACKING_INSTRUCTIONS  FROM oracle_download WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
			$query = mysqli_query($conn2, $sql);
			if (!$query ) return '';
			$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		}
	}

	if ($conn) mysqli_close($conn);
	if ($conn2) mysqli_close($conn2);

	return (!empty($results) ) ? trim($results['PACKING_INSTRUCTIONS']) : '';

}


$filename = "all_form_type_".date("d_m_Y__H_i_s");
header('Content-Encoding: UTF-8');
header('Content-Type: text/csv; charset=utf-8');  
header("Content-type: text/csv");
header("Cache-Control: no-store, no-cache");
header("Content-Disposition: attachment; filename=$filename.csv");  
$output = fopen("php://output", "w");  
$header = [
	"NGAY LAM DON HANG","STT PLANNING","SO#","Cust Req date (D-1)","Promise Date (D-1)","MA HANG - ITEM CODE","RBO","NHAN - ORDER ITEM","SO LUONG CON NHAN - QTY","MA VAT TU - ORACLE MATERIAL"," Description - MATERIAL"," SO LUONG VAT TU CAN - QTY-EA"," SO LUONG VAT TU CAN - QTY-YD"," SO LUONG VAT TU CAN - QTY-MT"," Kich thuoc nhan chieu dai"," Kich thuoc nhan chieu rong"," MA MUC - ORACLE ","Description - MUC","SO LUONG MUC CAN - QTY-MT","SO UP","CREATED BY","","COMBINE"
];
//fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, $header);  
if(count($rowsResult)>0){ 
	if(!empty($rowsResult)){
		foreach ($rowsResult as $row){
			$FORM_TYPE = !empty($row['FORM_TYPE'])?$row['FORM_TYPE']:'';
			$NUMBER_NO = $row['NUMBER_NO'];			
			$INTERNAL_ITEM = $row['INTERNAL_ITEM'];
			$SAVE_DATE = $row['CREATE_DATE']; 
			$SAVE_DATE = formatDate($row['CREATE_DATE']);

			//Lấy thông tin remark_4
			// $REMARK_4 = $row['REMARK_4'];
			$SO_LINE = $row['SO_LINE'];  
			$SHIP_TO = $row['SHIP_TO'];  

			// @tandoan:  Lấy thông tin UPC và PACKING INSTRUCT để kiểm tra combine RFID
			$SO_LINE_ARR = explode("-",$SO_LINE);
			$UPC = MiQuery("SELECT REMARK_1_ITEM FROM master_bom WHERE INTERNAL_ITEM = '" . $INTERNAL_ITEM . "' LIMIT 1",$conn);
			if ($conn ) mysqli_close($conn);
			$PACKING_INSTRUCTIONS = remarkPackingInstr($SO_LINE_ARR[0], $SO_LINE_ARR[1]);
			
			$REMARK_4 = '';
			if(!empty($UPC)  && !empty($PACKING_INSTRUCTIONS)) {
				if(strpos($UPC,"UPC") !== false ){ 
					if (strpos($PACKING_INSTRUCTIONS,"HANGLE") !== false) {
						$REMARK_4 = "HANGLE";
					} else {
						$REMARK_4 = "Hàng Nike Thermal Combine với RFID";	
					}	

					// Đối với đơn hàng WORLDON thì remark không hiển thị
						if (strpos(strtoupper($SHIP_TO), 'WORLDON') !== false) {
							$REMARK_4 = '';
						}

					
				}
				
			}

			if ($INTERNAL_ITEM == '1-272128-000-00' || $INTERNAL_ITEM == '1-272831-000-00' || $INTERNAL_ITEM == '1-273932-000-00' ) {
				$REMARK_4 = 'COMBINED OFFSET & THERMAL';
				
			} else if ($INTERNAL_ITEM == '4-226640-238-00') {
				$REMARK_4 = 'COMBINE RFID & HANGTAG';
			}


			$PD = $row['PD'];
			$FORM_TYPE = $row['FORM_TYPE'];
			$PD = formatDate($PD);
			$REQ = $row['REQ'];
			$REQ = formatDate($REQ);
			
			$RBO = $row['RBO'];
			if($FORM_TYPE=='paxar'){
				$ITEM_DES = $row['ITEM_DES'];
			}else{
				$ITEM_DES = $row['ITEM'];
			}		
			$QTY = !empty($row['QTY'])?$row['QTY']:'-';$QTY = !empty($row['QTY'])?$row['QTY']:'-';			
			if($QTY!=='-'){
				$QTY = number_format($QTY);
			}
			$MATERIAL_CODE = $row['MATERIAL_CODE'];
			$MATERIAL_DES = $row['MATERIAL_DES'];
			$EA_SHT = !empty($row['EA_SHT'])?$row['EA_SHT']:'-';
			if($EA_SHT!=='-'){
				$EA_SHT = number_format($EA_SHT);
			}
			$YD = !empty($row['YD'])?$row['YD']:'-';
			if($YD!=='-'){
				$YD = number_format($YD);
			}
			$MT = !empty($row['MT'])?$row['MT']:'-';
			if($MT!=='-'){
				$MT = number_format($MT);
			}
			$MATERIAL_QTY = !empty($row['MATERIAL_QTY'])?$row['MATERIAL_QTY']:'-';
			if($MATERIAL_QTY!=='-'){
				$MATERIAL_QTY = number_format($MATERIAL_QTY);
			}
			if($FORM_TYPE=='sips'||$FORM_TYPE=='trim'){
				if($EA_SHT=='-'){
					$EA_SHT = $MATERIAL_QTY;
				}
			}
			$LENGTH = $row['LENGTH'];
			$WIDTH = $row['WIDTH'];
			$INK_CODE = $row['INK_CODE'];
			$INK_DES = $row['INK_DES'];
			$INK_QTY = !empty($row['INK_QTY'])?$row['INK_QTY']:'-';
			if($INK_QTY!=='-'){
				$INK_QTY = number_format($INK_QTY);
			}
			$SO_UPS = $row['SO_UPS'];
			$CREATED_BY = $row['CREATED_BY'];
			
			$arrayOutputTMP = [$SAVE_DATE,$NUMBER_NO,$SO_LINE,$REQ,$PD,$INTERNAL_ITEM,$RBO,$ITEM_DES,$QTY,$MATERIAL_CODE,$MATERIAL_DES,$EA_SHT,$YD,$MT,$LENGTH,$WIDTH,$INK_CODE,$INK_DES,$INK_QTY,$SO_UPS,$CREATED_BY,"",$REMARK_4];
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, $arrayOutputTMP);				
		}
	}
} 
fclose($output);  