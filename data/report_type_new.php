<?php

require_once("../Database.php");

function getMasterItem($internal_item, $conn) 
{
	$internal_item = trim($internal_item);
	$results = array();

	$sql = "SELECT * FROM master_bom WHERE `INTERNAL_ITEM` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return '';

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) return '';

	return $results;
}

function getNHOM($internal_item, $conn ) 
{
	$internal_item = trim($internal_item);
	$results = array();

	$sql = "SELECT `NHOM` FROM master_bom WHERE `internal_item` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return '';

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) return '';

	return trim(strtoupper($results['NHOM']));
}

function getKitDetail($internal_item, $conn ) 
{

	$internal_item = trim($internal_item);
	$results = array();

	$sql = "SELECT `CHI_TIET_KIT` FROM master_bom WHERE `internal_item` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return '';

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) return '';

	return trim($results['CHI_TIET_KIT']);
}

function formatDate($value){
	return date('d-M-y',strtotime($value));
}


// load packing and attachment
function remarkPackingInstr($ORDER_NUMBER, $LINE_NUMBER, $conn2, $conn) 
{
	$results = array();

	$sql = "SELECT CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS FROM vnso WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn2, $sql);
	if (!$query ) return '';
	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) {
		$sql = "SELECT CONCAT(VIRABLE_BREAKDOWN_INSTRUCTIONS,PACKING_INSTRUCTIONS) AS PACKING_INSTRUCTIONS FROM vnso_total WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn2, $sql);
		if (!$query ) return '';
		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) {
			// get BOM data
			$sql = "SELECT REMARK_3_PACKING AS PACKING_INSTRUCTIONS  FROM oracle_download WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
			$query = mysqli_query($conn, $sql);
			if (!$query ) return '';
			$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		}
	}

	return (!empty($results) ) ? trim($results['PACKING_INSTRUCTIONS']) : '';

}

function remnarkTrimCard($packingInstr) 
{	
	$packingInstr = !empty($packingInstr) ? strtolower(trim($packingInstr) ) : '';
	
	$remark = '';
	
	$array = array(
		'trim card',
		'trimcard',
		'trim-card',
		'trim/card'
	);
	
	foreach ($array as $value ) {
		if (strpos($packingInstr, $value) !== false) {
			$remark = 'TRIM CARD';
			break;
		}
	}

	return $remark;
	
}

function getSecurityCol($masterItem) 
{
	return !empty($masterItem) ? trim($masterItem['SECURITY'] ) : '';	
}

function getAutomailData($ORDER_NUMBER, $LINE_NUMBER, $conn2 ) 
{
	$results = array();

	$sql = "SELECT * FROM vnso WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn2, $sql);
	if (!$query ) return '';
	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) {
		$sql = "SELECT * FROM vnso_total WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
		$query = mysqli_query($conn2, $sql);
		if (!$query ) return '';
		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	}

	return $results;

}

function remarkFRUIC($ORDER_TYPE_NAME)
{
	$remark = '';
	if (!empty($ORDER_TYPE_NAME) ) {
		if (stripos($ORDER_TYPE_NAME, 'BNH') !== false ) {
			$remark = "FRU IC LH";
		}
	}

	return $remark;
	
}

// @TanDoan - 20211108: Xử lý remark COMBINE THERMAL & RFID vào trong cột NOTE 2 (đơn PAXAR)
function remarkCombineUPC($masterItem, $packingInstr, $shipTo ) 
{
	$remark = '';
	$UPC = !empty($masterItem) ? $masterItem['REMARK_1_ITEM'] : '';
	if (strpos( strtoupper($UPC), "UPC") !== false) {
		
		if (strpos($packingInstr, "HANGLE") !== false ) {
			$remark = "HANGLE";
		} else {
			$remark = "COMBINE THERMAL & RFID";
		}

		// Đối với đơn hàng WORLDON thì remark không hiển thị
		if (strpos(strtoupper($shipTo), 'WORLDON') !== false) {
			$remark = '';	
		}
		
	}

	return $remark;
}


date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('max_execution_time',1200);  // set time 20 minutes
require_once("../Database.php");
$fields = 'FORM_TYPE,s_m.SO_LINE,s_m.CREATE_DATE,NUMBER_NO,REQ,PD,s_m.INTERNAL_ITEM,s_i.RBO,s_m.ITEM,ITEM_DES,s_m.QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,MATERIAL_QTY,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,SO_UPS,CREATED_BY,DATA_RECEIVED,SO_LAN,SHIP_TO,CS';
// to do process so kho if(type_worst_vertical = 100-SB1) 10,5
$FORM_TYPE = isset($_COOKIE['print_type_thermal'])?$_COOKIE['print_type_thermal']:'';
$FROM_DATE = $_GET['from_date_value'];
$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
$TO_DATE = $_GET['to_date_value'];
$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
$query = "SELECT $fields FROM save_material as s_m JOIN save_item as s_i ON s_i.NUMBER_NO = s_m.ID_SAVE_ITEM where (s_m.CREATE_DATE>='$FROM_DATE' AND s_m.CREATE_DATE<='$TO_DATE') AND FORM_TYPE='$FORM_TYPE' order by s_m.ID asc";

$_conn = _conn();
$_conn2 = _conn('au_avery');
$rowsResult = MiQuery($query, $_conn);

$filename = $FORM_TYPE."_".date("d_m_Y__H_i_s");
header('Content-Encoding: UTF-8');
header('Content-Type: text/csv; charset=utf-8');  
header("Content-type: text/csv");
header("Cache-Control: no-store, no-cache");
header("Content-Disposition: attachment; filename=$filename.csv");  
$output = fopen("php://output", "w");  
if($FORM_TYPE=='paxar'){
	$header = [
		"NGAY LAM DON HANG","STT PLANNING","SO#","Cust Req date (D-1)","Promise Date (D-1)","MA HANG - ITEM CODE","RBO","NHAN - ORDER ITEM","SO LUONG CON NHAN - QTY","MA VAT TU - ORACLE MATERIAL"," Description - MATERIAL"," SO LUONG VAT TU CAN - QTY-EA"," SO LUONG VAT TU CAN - QTY-YD"," SO LUONG VAT TU CAN - QTY-MT"," Kich thuoc nhan chieu dai"," Kich thuoc nhan chieu rong"," MA MUC - ORACLE ","Description - MUC","SO LUONG MUC CAN - QTY-MT","SO UP","CREATED BY", "SO KIT", "NOTE 1", "NOTE 2"
	];
}else{
	$header = [
		"NGAY LAM DON HANG","STT PLANNING","SO#","Cust Req date (D-1)","Promise Date (D-1)","MA HANG - ITEM CODE","RBO","NHAN - ORDER ITEM","SO LUONG CON NHAN - QTY","MA VAT TU - ORACLE MATERIAL"," Description - MATERIAL"," SO LUONG VAT TU CAN - QTY-EA"," SO LUONG VAT TU CAN - QTY-YD"," SO LUONG VAT TU CAN - QTY-MT"," Kich thuoc nhan chieu dai"," Kich thuoc nhan chieu rong"," MA MUC - ORACLE ","Description - MUC","SO LUONG MUC CAN - QTY-MT","DATA RECEIVED","STT FILE","SO UP","SHIP TO","CS NAME","CREATED BY", "SO KIT", "NOTE"
	];
}
//fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, $header);  
if(count($rowsResult)>0){ 
	if(!empty($rowsResult)){
		foreach ($rowsResult as $row){
			$NUMBER_NO = $row['NUMBER_NO'];			
			$SAVE_DATE = $row['CREATE_DATE']; 
			$SAVE_DATE = formatDate($row['CREATE_DATE']);
			//$SAVE_DATE = date('m/d/Y',strtotime($SAVE_DATE));
			$SO_LINE = $row['SO_LINE'];		
			$SO_LINE_ARR = explode('-', $SO_LINE);

			// Lấy dữ liệu từ Automail
			$ORDER_TYPE_NAME = '';
			$AutomailData = getAutomailData($SO_LINE_ARR[0], $SO_LINE_ARR[1], $_conn2 );
			if (!empty($AutomailData) ) {
				$ORDER_TYPE_NAME = $AutomailData['ORDER_TYPE_NAME'];
			}

			// remark FRU IC LH
				$remarkFRUIC = remarkFRUIC($ORDER_TYPE_NAME);

			$packingInstr = remarkPackingInstr($SO_LINE_ARR[0], $SO_LINE_ARR[1], $_conn2, $_conn );
			$TRIMCARD = remnarkTrimCard($packingInstr);
			
			$PD = $row['PD'];
			$PD = formatDate($PD);
			$REQ = $row['REQ'];
			$REQ = formatDate($REQ);
			$INTERNAL_ITEM = $row['INTERNAL_ITEM'];

			// @tandoan: 20200709 - lấy số nhóm PAXAR, IPPS, ... để remark nếu là nhóm IPPS
			$masterItem = getMasterItem($INTERNAL_ITEM, $_conn);
			$security = getSecurityCol($masterItem);
			$NHOM = getNHOM($INTERNAL_ITEM, $_conn);
			
			$NOTE_NHOM = '';
			if (strpos($NHOM,'FG') !== false ) {
				$NOTE_NHOM = 'IPPS'; // chi @Yen yêu cầu 
			} else if (strpos($NHOM,'IPPS') !== false ) {
				$NOTE_NHOM = $NHOM;
			}

			if (strpos($NHOM,'IPPS') !==false || strpos($NHOM,'FG') !== false ) {
				if (strpos(strtoupper($security),'HÀNG ĐẶC BIỆT') !==false || strpos(strtoupper($security),'HANG DAC BIET') !==false ) {
					$NOTE_NHOM .= " - HÀNG ĐẶC BIỆT ";
				}
			}

			// check remark FRU IC LH
				if (!empty($remarkFRUIC) ) {
					$NOTE_NHOM = (!empty($NOTE_NHOM) ) ? ($NOTE_NHOM . ". " . $remarkFRUIC) : $remarkFRUIC;
				}


			$RBO = $row['RBO'];
			$SHIP_TO = $row['SHIP_TO'];
			if($FORM_TYPE=='paxar'){
				$ITEM_DES = $row['ITEM_DES'];
			}else{
				$ITEM_DES = $row['ITEM'];
			}			
			$QTY = !empty($row['QTY'])?$row['QTY']:'-';
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

			// remark UPC ở cột NOTE 2
			$remarkCombineUPC = remarkCombineUPC($masterItem, $packingInstr, $SHIP_TO);
			if (!empty($TRIMCARD) ) {
				$TRIMCARD .= !empty($remarkCombineUPC) ? (" - ". $remarkCombineUPC) : '';
			} else {
				$TRIMCARD = $remarkCombineUPC;
			}

			if($FORM_TYPE=='paxar'){
				$arrayOutputTMP = [$SAVE_DATE,$NUMBER_NO,$SO_LINE,$REQ,$PD,$INTERNAL_ITEM,$RBO,$ITEM_DES,$QTY,$MATERIAL_CODE,$MATERIAL_DES,$EA_SHT,$YD,$MT,$LENGTH,$WIDTH,$INK_CODE,$INK_DES,$INK_QTY,$SO_UPS,$CREATED_BY,$QTY,$NOTE_NHOM,$TRIMCARD ];
			}else{
				if(empty($row['SO_LAN'])){
					$SO_LAN = '';
				}else{
					$SO_LAN = 'file-'.$row['SO_LAN'];
				}
				$arrayOutputTMP = [$SAVE_DATE,$NUMBER_NO,$SO_LINE,$REQ,$PD,$INTERNAL_ITEM,$RBO,$ITEM_DES,$QTY,$MATERIAL_CODE,$MATERIAL_DES,$EA_SHT,$YD,$MT,$LENGTH,$WIDTH,$INK_CODE,$INK_DES,$INK_QTY,$row['DATA_RECEIVED'],$SO_LAN,$SO_UPS,$row['SHIP_TO'],$row['CS'],$CREATED_BY,$QTY,$NOTE_NHOM];
			}
			//fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, $arrayOutputTMP);				
		}
	}
} 

if ($_conn ) mysqli_close($_conn);
if ($_conn2 ) mysqli_close($_conn2);
fclose($output);  