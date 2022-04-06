<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
ini_set('max_execution_time',300);  // set time 5 minutes
require_once("../Database.php");

$conn = _conn();

$fields = 'FORM_TYPE,s_m.SO_LINE,s_m.CREATE_DATE,NUMBER_NO,REQ,PD,s_m.INTERNAL_ITEM,s_i.RBO,s_m.ITEM,ITEM_DES,s_m.QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,MATERIAL_QTY,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,SO_UPS,CREATED_BY,DATA_RECEIVED,SO_LAN,SHIP_TO,CS';
$FROM_DATE = $_GET['from_date_value'];
$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
$TO_DATE = $_GET['to_date_value'];
$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
$query = "SELECT $fields FROM save_material as s_m JOIN save_item as s_i ON s_i.NUMBER_NO = s_m.ID_SAVE_ITEM where (s_m.CREATE_DATE>='$FROM_DATE' AND s_m.CREATE_DATE<='$TO_DATE') AND (FORM_TYPE='trim' OR FORM_TYPE='sips') order by s_m.ID asc";
$rowsResult = MiQuery($query, $conn);
if ($conn ) mysqli_close($conn);

//output data in XML format 
function formatDate($value){
	return date('d-M-y',strtotime($value));
}
$filename = "trim_sips_".date("d_m_Y__H_i_s");
header('Content-Encoding: UTF-8');
header('Content-Type: text/csv; charset=utf-8');  
header("Content-type: text/csv");
header("Cache-Control: no-store, no-cache");
header("Content-Disposition: attachment; filename=$filename.csv");  
$output = fopen("php://output", "w");  
$header = [
	"NGAY LAM DON HANG","STT PLANNING","SO#","Cust Req date (D-1)","Promise Date (D-1)","MA HANG - ITEM CODE","RBO","NHAN - ORDER ITEM","SO LUONG CON NHAN - QTY","MA VAT TU - ORACLE MATERIAL"," Description - MATERIAL"," SO LUONG VAT TU CAN - QTY-EA"," SO LUONG VAT TU CAN - QTY-YD"," SO LUONG VAT TU CAN - QTY-MT"," Kich thuoc nhan chieu dai"," Kich thuoc nhan chieu rong"," MA MUC - ORACLE ","Description - MUC","SO LUONG MUC CAN - QTY-MT","DATA RECEIVED","STT FILE","SO UP","SHIP TO","CS NAME","CREATED BY"
];
//fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, $header);  
if(count($rowsResult)>0){ 
	if(!empty($rowsResult)){
		foreach ($rowsResult as $row){
			$NUMBER_NO = $row['NUMBER_NO'];			
			$SAVE_DATE = $row['CREATE_DATE']; 
			$SAVE_DATE = formatDate($row['CREATE_DATE']);
			$SO_LINE = $row['SO_LINE'];  
			$FORM_TYPE = $row['FORM_TYPE'];  
			$PD = $row['PD'];
			$PD = formatDate($PD);
			$REQ = $row['REQ'];
			$REQ = formatDate($REQ);
			$INTERNAL_ITEM = $row['INTERNAL_ITEM'];
			$RBO = $row['RBO'];
			$ITEM_DES = $row['ITEM'];			
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
			if(empty($row['SO_LAN'])){
				$SO_LAN = '';
			}else{
				$SO_LAN = 'file-'.$row['SO_LAN'];
			}
			$arrayOutputTMP = [$SAVE_DATE,$NUMBER_NO,$SO_LINE,$REQ,$PD,$INTERNAL_ITEM,$RBO,$ITEM_DES,$QTY,$MATERIAL_CODE,$MATERIAL_DES,$EA_SHT,$YD,$MT,$LENGTH,$WIDTH,$INK_CODE,$INK_DES,$INK_QTY,$row['DATA_RECEIVED'],$SO_LAN,$SO_UPS,$row['SHIP_TO'],$row['CS'],$CREATED_BY];
			fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
			fputcsv($output, $arrayOutputTMP);				
		}
	}
} 
fclose($output);  