<?php

require_once ("./Database.php");

function getUOM($internal_item) 
{
	$internal_item = trim($internal_item);
	$results = array();
	$conn = _conn("au_avery");
	$conn2 = _conn("cs_avery");

	$data = '';

	$sql = "SELECT `UOMCost` as UOM FROM tbl_productline_item WHERE `Item` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn2, $sql);
	if (!$query ) return '';

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) {
		$sql = "SELECT `UOM` FROM oe_soview_text WHERE `ITEM` = '$internal_item' ORDER BY ID DESC LIMIT 1;  ";
		$query = mysqli_query($conn, $sql);
		if (!$query ) return '';
		$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
		if (empty($results) ) return '';
	}

	if ($conn) mysqli_close($conn);
	if ($conn2) mysqli_close($conn2);

	return trim(strtoupper($results['UOM']));
}

function getNHOM($internal_item) 
{
	$internal_item = trim($internal_item);
	$results = array();
	$conn = _conn();

	$sql = "SELECT `NHOM` FROM master_bom WHERE `internal_item` = '$internal_item' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return '';

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) return '';

	if ($conn) mysqli_close($conn);

	return trim(strtoupper($results['NHOM']));
}

function getBOMData($internal_item) 
{
	$internal_item = trim($internal_item);
	$results = array();
	$conn = _conn();

	if (empty($internal_item) ) return array();

	$sql = "SELECT `*` FROM master_bom WHERE `INTERNAL_ITEM` = '$internal_item' ORDER BY ID DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return array();

	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if ($conn) mysqli_close($conn);
	return $results;
}

// load packing and attachment
function remarOnlykPackingInstr($ORDER_NUMBER, $LINE_NUMBER) 
{
	$results = array();
	$conn = _conn("au_avery");
	$conn2 = _conn();

	$sql = "SELECT PACKING_INSTRUCTIONS FROM vnso WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
	$query = mysqli_query($conn, $sql);
	if (!$query ) return '';
	$results = mysqli_fetch_array($query, MYSQLI_ASSOC);
	if (empty($results) ) {
		$sql = "SELECT PACKING_INSTRUCTIONS FROM vnso_total WHERE `ORDER_NUMBER` = '$ORDER_NUMBER' AND `LINE_NUMBER` = '$LINE_NUMBER' ORDER BY id DESC LIMIT 1;  ";
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

// load packing and attachment
function remarkPackingInstr($ORDER_NUMBER, $LINE_NUMBER) 
{
	$results = array();
	$conn = _conn("au_avery");
	$conn2 = _conn();

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

function getTRIMUnit($internal_item) {
	$array = array(
		'2-378861-000-SHT',
		'2-378862-000-SHT'
	);

	foreach ($array as $item ) {
		$result = 'PCS';
		if ($internal_item == $item ) {
			$result = 'SHT';
			break;
		}
	}

	return $result;
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


$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script,'',$_SERVER['PHP_SELF']);
require_once("./Database.php");

$conn = _conn("au_avery");

if(empty($_GET['id'])){
	echo 'VUI LÒNG NHẬP LỆNH SẢN XUẤT';die;
}
$id = $_GET['id'];
if(!empty($id)){
	$sql_item = "SELECT * FROM save_item WHERE NUMBER_NO='$id'";
	$result_item = MiQuery($sql_item,$dbMi_138);	
	if(empty($result_item[0])){
		echo 'LỆNH SẢN XUẤT KHÔNG TỒN TẠI.';die;
	}
	$result_item = $result_item[0];
	$pathERP = dirname($_SERVER['SCRIPT_FILENAME']);
	$FORM_TYPE = $result_item['FORM_TYPE'];
	if(!empty($result_item)){  
		$sql_update_PRINTED = "UPDATE save_item SET `PRINTED`='1' WHERE `NUMBER_NO`='$id'";
		$dbMi_138->query($sql_update_PRINTED);
		//require_once($pathERP."/print/pro_item.php"); //  xu ly item
		//require_once($pathERP."/print/pro_supply.php"); //  xu ly supply
		if($FORM_TYPE=='paxar'){
			require_once($pathERP."/print/pro_item.php");
			require_once($pathERP."/print/pro_supply.php");
		}
		elseif($FORM_TYPE=='trim'){
			require_once($pathERP."/print/pro_item.php");
			require_once($pathERP."/print/pro_supply.php");
		}
		elseif($FORM_TYPE=='sips'){
			require_once($pathERP."/print/pro_item.php");
			require_once($pathERP."/print/pro_supply.php");
		}
	}else{
		echo 'LỆNH SẢN XUẤT KHÔNG TỒN TẠI.';die;
	}
	if($FORM_TYPE=='paxar'){
		require_once($pathERP."/print/paxar.php");
	}
	elseif($FORM_TYPE=='trim'){
		require_once($pathERP."/print/trim.php");
	}
	elseif($FORM_TYPE=='sips'){
		require_once($pathERP."/print/sips.php");
	}
}else{
	echo 'VUI LÒNG NHẬP LỆNH SẢN XUẤT';
}
?>