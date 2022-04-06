<style type="text/css">
	.form-style-10{
		width:90%;
		margin:15px auto 10px auto;
		font: 15px Arial, Helvetica, sans-serif;
	}
	.form-style-10 .inner-wrap{
		padding: 30px;
		border-radius: 6px;
		margin-bottom: 15px;
	}
	.form-style-10 label{
		color: #888;
		margin-bottom: 15px;
		float:left;
		margin-right:2%;
		width:30%;
	}
	.form-style-10 input[type="text"],.form-style-10 label select{
		display: block;
		box-sizing: border-box;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		width: 100%;
		padding: 8px;
		font-size:15px;
		font-weight:bold;
	}

	.form-style-10 .section{
		color: #2A88AD;
		margin-bottom: 5px;
		text-align:center;
		font-weight:bold;
	}
</style>
<?php

require_once("../Database.php");

function CreateNO_New($no_prefix, $form_type,$dbMi_138, $FR) {
	// 1. Lấy trong table tmp ra giá trị NO lớn nhất
	$sql_tmp = "SELECT NUMBER_NO FROM save_item_tmp where NUMBER_NO like '$no_prefix%' and FORM_TYPE='$form_type' order by NUMBER_NO desc limit 0,1";
	$no_max_tmp = MiQuery($sql_tmp,$dbMi_138); 
	// Trường hợp có dữ liệu, lấy NO tăng lên 1 save vào
	if (!empty($no_max_tmp)) {
		$NUMBER_NO_CURR = $no_max_tmp;
	} else { // Trường hợp ngược lại, lấy trong save_item, lấy NO tăng 1 save vào bảng tmp
		$sql = "SELECT NUMBER_NO FROM save_item where NUMBER_NO like '$no_prefix%' and FORM_TYPE='$form_type' order by NUMBER_NO desc limit 0,1";
		$no_max = MiQuery($sql,$dbMi_138); 
		if (!empty($no_max)) {
			$NUMBER_NO_CURR = $no_max;
		} else { // Đây là trường hợp không tìm thấy: Có thể là qua tháng mới hoặc chưa có dữ liệu. => gán cho hậu tố bắt đầu từ 1
			$NUMBER_NO_CURR = $no_prefix . '-00001';
		}
	}

	// tách chuỗi thành mảng
	$NUMBER_NO_CURR_ARR = explode('-', $NUMBER_NO_CURR);
	$NO_PREFIX_NEW = $NUMBER_NO_CURR_ARR[0]; // Lấy tiền tố
	$NO_SUFFIX_CUR = (int)$NUMBER_NO_CURR_ARR[1];
	// Tăng lên 1 đơn vị
	$NO_SUFFIX_CUR_ADD = $NO_SUFFIX_CUR + 1;
	
	$length_no = strlen($NO_SUFFIX_CUR_ADD);        
    if($NO_SUFFIX_CUR_ADD<=99999){
		if($length_no===1){
			$NO_SUFFIX_NEW = "0000".$NO_SUFFIX_CUR_ADD;
		}else if($length_no===2){
			$NO_SUFFIX_NEW = "000".$NO_SUFFIX_CUR_ADD;
		}else if($length_no===3){
			$NO_SUFFIX_NEW = "00".$NO_SUFFIX_CUR_ADD;
		}
		else if($length_no===4){
			$NO_SUFFIX_NEW = "0".$NO_SUFFIX_CUR_ADD;
		} else { // = 5
			$NO_SUFFIX_NEW = $NO_SUFFIX_CUR_ADD;
		}
	}

	// trả về NO
	$NO_NEW = $NO_PREFIX_NEW . '-' . $NO_SUFFIX_NEW . $FR;
	if (strpos($NO_NEW, '-FR-FR')!==false) {
		$NO_NEW = str_replace('-FR-FR','-FR',$NO_NEW);
	}

	// Lưu vào save_item_tmp để user khác làm lệnh không bị trùng lặp
	$insert_tmp = "INSERT INTO `save_item_tmp` (`NUMBER_NO`, `FORM_TYPE`) VALUES ('$NO_NEW', '$form_type') ";
	$check_save_tmp = $dbMi_138->query($insert_tmp);  // Không cần check

	return $NO_NEW;
}

$print_type 		= $_COOKIE['print_type_thermal'];
$yearMonth 			= date('ym');

if($print_type=='paxar'){
	$print_type_text 		= 'PAXAR';
	$prefix_first = "TP";
} else if($print_type=='trim'){
	$print_type_text 		= 'TRIM';
	$prefix_first = "TRIM";
} else {
	$print_type_text 		= 'SIPS';
	$prefix_first = "SIPS";
}

$prefix_time = $prefix_first . $yearMonth;

// gán tiền tố theo thời gian hiện tại
$prefix = $prefix_time;

// Truy vấn trong db lấy ra số NO lớn nhất (đối với fomr đang làm lệnh)
$sql_check = "SELECT NUMBER_NO FROM save_item where FORM_TYPE='$print_type' order by NUMBER_NO desc limit 0,1";
$no_max_check = MiQuery($sql_check,$dbMi_138);
if (!empty($no_max_check)) {
	$number_no_arr_check = explode('-',$no_max_check);
	$prefix_cur = 	$number_no_arr_check[0];
	
	// Nếu có cả 2 thì so sánh, cái nào lớn thì lấy
	if (strcmp($prefix_time, $prefix_cur) < 0 ) {
		$prefix = $prefix_cur;
	} //trường hợp ngược lại thì mặc định tiền tố theo lấy tháng hiện tại (ở trên)

}

$print_type_text = "THERMAL ".$print_type_text;


$date_create 		= date('d-M-y');
$order 				= !empty($_POST['order'])?$_POST['order']:'';
$req 				= !empty($_POST['req'])?$_POST['req']:'';
$pd 				= !empty($_POST['pd'])?$_POST['pd']:'';
$ship_to 			= !empty($_POST['ship_to'])?$_POST['ship_to']:'';
$rbo 				= !empty($_POST['rbo'])?$_POST['rbo']:'';
$cs 				= !empty($_POST['cs'])?$_POST['cs']:'';
$qty 				= !empty($_POST['qty'])?$_POST['qty']:'';
$remark_1 			= !empty($_POST['remark_1'])?$_POST['remark_1']:'';
$remark_2 			= !empty($_POST['remark_2'])?$_POST['remark_2']:'';
$remark_3 			= !empty($_POST['remark_3'])?$_POST['remark_3']:'';
$remark_4 			= !empty($_POST['remark_4'])?$_POST['remark_4']:'';
$remark_5 			= !empty($_POST['remark_5'])?$_POST['remark_5']:'';
$ORDER_TYPE_NAME 	= !empty($_POST['ORDER_TYPE_NAME'])?$_POST['ORDER_TYPE_NAME']:'';
$FR = '';
if(strpos($ORDER_TYPE_NAME,'VN QR')!==false){
	$FR = '-FR';
	//$NUMBER_NO_OK=$NUMBER_NO_OK.$FR;
}elseif(strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
	if(!empty($remark_3)){
		$remark_packings = explode("MAU CUA SO#",$remark_3);
		if(!empty($remark_packings[1])){
			$SO_TO_CHECK = trim($remark_packings[1]);
			if(strlen($SO_TO_CHECK)==8){
				$conn = _conn("au_avery");
				$sql_count_fr = "SELECT count(ID) FROM vnso WHERE ORDER_NUMBER='$SO_TO_CHECK' and ORDER_TYPE_NAME='VN QR';";   
				$RESULT_COUNT_FR = MiQuery($sql_count_fr,$conn);
				if ($conn ) mysqli_close($conn);
				if(!empty($RESULT_COUNT_FR)){
					$FR = '-FR';
					
				}
			}
		}
	}
}

/** ----------- GET NUMBER NO -------------------- */
$NUMBER_NO_OK = CreateNO_New($prefix, $print_type, $dbMi_138, $FR);
/** ---------------------------------------------- */
/*
echo "<pre>";
print_r($_POST);
*/
?>
<div class="form-style-10">
<form>
    <?php
		if($print_type=='paxar' || $print_type=='trim'|| $print_type=='sips'){
			require_once("frm_paxar.php");//connect to database
		}		
	?>
</form>