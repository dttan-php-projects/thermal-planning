<?php
require_once("../Database.php");
function getMaterialCode($string)
{
	//init var
    $dataResults = array();
    $size = $color = $qty = $material_code = '';
	$errorCount = $check_exist = $pause = 0;
    $sizepos = $colorpos = $qtypos = $materialcodepos = $maxpos = '';

    //loại bỏ các khoảng trắng
    $string = str_replace(" ", "",$string);
    if (strpos($string, ";Total")!==false ) {

    } else if (strpos($string,"Total")!==false ) {
        $string = str_replace("Total", ";Total",$string);
	}
	
	if (strpos($string,"\t")!==false ) {
        $string = str_replace("", "\t",$string);
	}
	if (strpos($string,"\x0B")!==false ) {
        $string = str_replace("", "\x0B",$string);
	}

    //Lấy Ký tự cuối check xem phải là ký tự: ^ hay k, k phải thì trả về lỗi
    $check = substr( $string,  strlen($string)-1, 1 );
    if ($check !== '^') {$pause = 1;}

	//Tách chuỗi thành mảng, mỗi phần tử có các nội dung size, color, qty, material_code
    $string_explode = explode(";",$string);

    //Đoạn code xác định vị trí size, color, qty, material_code.
    foreach ($string_explode as $stringpos) {
        $detachedpos = explode(":",$stringpos);
        for ($i=0;$i<count($detachedpos);$i++) {
            if ( strpos(strtoupper($detachedpos[$i]),"SIZE")!==false ) { $sizepos=$i; $maxpos = count($detachedpos); }
            if (strpos(strtoupper($detachedpos[$i]),"COLOR")!==false) { $colorpos=$i;  }
            if (strpos(strtoupper($detachedpos[$i]),"QUANTITY")!==false){ $qtypos=$i;  }
            if (strpos(strtoupper($detachedpos[$i]),"MATERIAL CODE")!==false || strpos(strtoupper($detachedpos[$i]),"MATERIALCODE")!==false){ $materialcodepos=$i; }

        }
        if ($sizepos) break;
    }

	//Kiểm tra data có nhập thiếu cột dữ liệu không

	//Nếu có data và có ký tự ^ (data k bị mất). Trường hợp ngược lại không them vào
	if(!empty($string_explode) && !$pause){

        // echo "\n maxpos: " . $maxpos . "\n";
        foreach ($string_explode as $key => $value) {
            $check_exist=0;
            //get format string  detached.
            $detachedStringAll = trim($value);

            //check error. Nếu không đúng định dạng => return error
            if(substr_count($detachedStringAll,":")<3){//Trường hợp min = 4 col
                $errorCount++; continue;
            }

            //tách chuỗi thành mảng bởi ký tự :
            $detachedString = explode(":",$detachedStringAll);

            //check detachedString không đúng định dạng. Dừng
            if (count($detachedString) !=$maxpos) {$errorCount++; continue;}

            //get data
            if ( $sizepos!=$colorpos && $colorpos!=$qtypos && $qtypos!=$materialcodepos ) {
				//lấy dữ liệu //Trường hợp không lấy được cột data nào thì cho dữ liệu đó = rỗng.
				$size = isset($detachedString[$sizepos]) ? trim($detachedString[$sizepos]) : '';
				
                $color = isset($detachedString[$colorpos]) ? trim($detachedString[$colorpos]) : '';
                $qty = isset($detachedString[$qtypos]) ? $detachedString[$qtypos] : '';
				$material_code = isset($detachedString[$materialcodepos]) ? trim($detachedString[$materialcodepos]): ''; //tam thoi lay vi tri nay

				/* *** Check trường hợp OE không nhập dấu ; trước chữ Total, dấu ^, (còn thì thêm vào ...) *** */
				$character_error_arr = [
					'Total',
					'^'
				];

				//Tìm các dữ liệu thừa để tách chuỗi thành mảng từ ký tự đó và lấy ra phần tử dữ liệu đã tách.
				foreach ($character_error_arr as $key => $value) {
					if (strpos(strtoupper($size),strtoupper($value))!==false) {
						$detached_tmp = explode($value,$size);
						$size = $detached_tmp[0];
					}

					if (strpos(strtoupper($color),strtoupper($value))!==false) {
						$detached_tmp = explode($value,$color);
						$color = $detached_tmp[0];
					}

					if (strpos(strtoupper($qty),strtoupper($value))!==false) {
						$detached_tmp = explode($value,$qty);
						$qty = $detached_tmp[0];
					}

					if (strpos(strtoupper($material_code),strtoupper($value))!==false) {
						$detached_tmp = explode($value,$material_code);
						$material_code = $detached_tmp[0];
					}
				} //end for

			}
			
            if(!is_numeric($qty) ){//kiểm tra qty có phải số không
				$errorCount++;
            } else {
                //check data ton tai chua, neu ton tai => cong them vao qty
                if (!empty($dataResults)) {
                    foreach($dataResults as $key => $value){

                        if( $value['size']==$size && $value['color']==$color && $value['material_code']==$material_code ){
                            $dataResults[$key]['qty'] += $qty;//cộng thêm vào
                            $check_exist = 1;
                        }
                    }

                    //Không tồn tại thì thêm vào mảng kết quả
                    if($check_exist==0){
                        $get = [
                            'size' 			=> $size,
                            'color' 		=> $color,
                            'qty' 			=> $qty,
                            'material_code' => $material_code
                        ];
                        array_push($dataResults,$get);

                    }


                } else {//trường hợp đầu tiên
                    $get = [
                        'size' 			=> $size,
                        'color' 		=> $color,
                        'qty' 			=> $qty,
                        'material_code' => $material_code
					];
                    array_push($dataResults,$get);
                }

            }

        }

		

	}

	// //return result data
	return $dataResults;

}

function getSizeNew($ORDER_NUMBER, $LINE_NUMBER ) 
{
	$conn = _conn("au_avery"); 

	$sql = "SELECT * FROM vnso_size WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER' ORDER BY SIZE ASC ";
	$query = mysqli_query($conn, $sql);
	if (mysqli_num_rows($query) > 0 ) {
		$results = mysqli_fetch_all($query, MYSQLI_ASSOC );

		
	} else {
		$sql = "SELECT * FROM vnso_size_oe WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER' AND ID_INSERT = (SELECT  MAX(ID_INSERT) AS ID FROM vnso_size_oe WHERE ORDER_NUMBER = '$ORDER_NUMBER' AND LINE_NUMBER = '$LINE_NUMBER') ORDER BY SIZE ASC; ";
		$query = mysqli_query($conn, $sql);
		if (mysqli_num_rows($query) > 0 ) {
			$results = mysqli_fetch_all($query, MYSQLI_ASSOC );
		}
	}

	$data = array();
	if (!empty($results) ) {
		foreach ($results as $item ) {
			//// dang lam 20200617
			$size = trim($item['SIZE']);
			$color = trim($item['COLOR']);
			$qty = trim($item['QTY']);
			$qty = (int)$qty;
			$material_code = trim($item['MATERIAL']);

			if (empty($size) || empty($material_code) ) {
				break;
			} else {
				if (empty($data) ) {
					$data[] = [
						'size' 			=> $size,
						'color' 		=> $color,
						'qty' 			=> $qty,
						'material_code' => $material_code
					];
				} else {
					$exist_size = false;
					foreach ($data as $key => $value ) {
						// duplicate => sum qty
						if (strcmp($value['size'], $size) == 0 && $value['color'] == $color && $value['material_code'] == $material_code ) {
							$data[$key]['qty'] += $qty;
							$exist_size = true;
							break;
						}
					}
	
					if ($exist_size == false ) {
						$data[] = [
							'size' 			=> $size,
							'color' 		=> $color,
							'qty' 			=> $qty,
							'material_code' => $material_code
						];
					}
				}
			}

		}

	}

	if ($conn ) mysqli_close($conn);

	return $data;

}

function getIPPSMaterialQty($ITEM, $NHOM_PX_AD, $material_unit, $QTY, $BASE_ROLL ) 
{
	if ( (stripos($NHOM_PX_AD,'IPPS') !== false) || (stripos($NHOM_PX_AD,'FG') !== false)  ) {

		$UOM = getUOM($ITEM);
		if (empty($UOM) ) {
			$response = [
				'status' => false,
				'mess' =>  "ITEM: $ITEM KHÔNG LẤY ĐƯỢC UOM "
			];
			echo json_encode($response);die;

		}

		$EA_SHT = 0;
		$YD = 0;
		$MT = 0;

		// $MaterialUnitArr = array('EA', 'SHEET', 'KIT', 'RL' );

		if ($material_unit == 'EA' || $material_unit == 'SHEET' || $material_unit == 'KIT' || $material_unit == 'RL' ) {
			if ($UOM == 'KIT' || $UOM == 'RL' ) {
				$EA_SHT = ceil($QTY*$BASE_ROLL);
			} else {
				$EA_SHT = $QTY;
			}

		} else if ($material_unit == 'YD' ) {
			if ($UOM == 'KIT' ) {
				$YD = ceil($QTY*$BASE_ROLL);
			} else {
				$YD = $QTY;
			}

		} else if ($material_unit == 'MT' ) {
			if ($UOM == 'KIT' ) {
				$MT = ceil($QTY*$BASE_ROLL);
			} else {
				$MT = $QTY;
			}

		}

	} else {
		// nhóm khác hàng bán đã có tính rồi
	}

	return array('EA_SHT' => $EA_SHT, 'YD' => $YD, 'MT' => $MT );




}

function getIPPSInkQty($QTY, $RIBBON_MT_KIT ) 
{

	return ceil($QTY * $RIBBON_MT_KIT );
}


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

function getBonus5Date($REQUEST_DATE) 
{

	$dateCheck = getdate();
    $day = $dateCheck['mday'];
    $mon = $dateCheck['mon'];
    $year = $dateCheck['year'];

    $date=date_create("$year-$mon-$day");
    date_add($date,date_interval_create_from_date_string("5 days"));

	$date = date_format($date,"d.m.Y");

	// so sánh nếu PD < CRD thì cho PD = CRD
	if (abs(strtotime($date) - strtotime($REQUEST_DATE)) ) {
		$date = date('d.m.Y',strtotime($REQUEST_DATE));
	}

	return $date;
	
}

function checkUniqloFG($RBO, $NHOM ) 
{
	if ( (stripos($RBO, 'UNIQLO') !== false) && ($NHOM == 'FG') ) {
		return true;
	}

	return false;
}

// echo getUOM('25HGAPGROUP-LPN(VN)'); exit();

date_default_timezone_set('Asia/Ho_Chi_Minh');
header("Content-Type: application/json");
require_once("../Database.php");
require_once("./class/helper.php");

$conn = _conn("au_avery");
$conn2 = _conn();


$data = isset($_POST['data']) ? $_POST['data'] : '' ;
if (empty($data) ) {
	$data = isset($_GET['data']) ? $_GET['data'] : '' ;
	$ORDER_NUMBER = explode('-', $data)[0];
} else {
	$ORDER_NUMBER = trim($data[0]);
}

$ORDER_NUMBER_ORG = $ORDER_NUMBER;
$ORDER_NUMBER_TMP = $ORDER_NUMBER;
if(!empty($data)){
	
	if(!empty($ORDER_NUMBER)){
		$data_source_thermal = !empty($_COOKIE['data_source_thermal'])?$_COOKIE['data_source_thermal']:'';
		if(empty($data_source_thermal)){
			$response = [
				'status' => false,
				'mess' =>  "VUI LÒNG CHỌN AUTOMAIL HOẶC ORACLE DOWNLOAD!"
			];
			echo json_encode($response);die;
		}

		$OrderBy = " ORDER BY ORDER_NUMBER,LENGTH(LINE_NUMBER),LINE_NUMBER ASC";

		if($data_source_thermal=='auto_mail'){
			
			$sql = "SELECT ID,ORDER_NUMBER,LINE_NUMBER,QTY,ORDERED_ITEM,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,ITEM_DESC,SOLD_TO_CUSTOMER,SHIP_TO_CUSTOMER,CS,PACKING_INSTRUCTIONS,ORDER_TYPE_NAME,VIRABLE_BREAKDOWN_INSTRUCTIONS,PRODUCTION_METHOD, FLOW_STATUS_CODE FROM vnso";
			$ORDER_NUMBER = trim($ORDER_NUMBER);
			$ORDER_NUMBER = trim($ORDER_NUMBER,'-');
			if(strpos($ORDER_NUMBER,'-')!==false){
				$LINE_ARR = explode('-',$ORDER_NUMBER);
				$ORDER_NUMBER = $LINE_ARR[0];
				unset($LINE_ARR[0]);
				$LINES = implode(',',$LINE_ARR);
				if(!empty($LINES)){
					$sql.=" WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND ORDER_NUMBER='$ORDER_NUMBER'";
					$sql.=" AND LINE_NUMBER IN ($LINES) ";// LIMIT 0,1 @TanDoan:Trường hợp trùng nên get 1";
				}
			}else{
				$sql.=" WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND ORDER_NUMBER = '$ORDER_NUMBER'";
			}
			$sql.=$OrderBy;
			$res = MiQuery($sql,$conn);

			// check 
			if(empty($res)||count($res)<1){
				$response = [
					'status' => false,
					'mess' =>  "SO LINE $ORDER_NUMBER_ORG KHÔNG TỒN TẠI TRÊN AUTOMAIL!"
				];
				echo json_encode($response);die;
			}

		}elseif($data_source_thermal=='oracle_download'){
			// $OrderBy = " ORDER BY oracle_download.LINE_NUMBER ASC ";
			$sql = "SELECT oracle_download.ID,ORDER_NUMBER,LINE_NUMBER,QTY,ORDERED_ITEM,ITEM,PROMISE_DATE,REQUEST_DATE,ORDERED_DATE,ITEM_DESC,SOLD_TO_CUSTOMER,SHIP_TO_CUSTOMER,CS,PACKING_INSTRUCTIONS,ORDER_TYPE_NAME, 'Thermal' AS PRODUCTION_METHOD FROM oracle_download";
			$ORDER_NUMBER_TMP = trim($ORDER_NUMBER_TMP);
			$ORDER_NUMBER_TMP = trim($ORDER_NUMBER_TMP,'-');
			if(strpos($ORDER_NUMBER_TMP,'-')!==false){
				$LINE_ARR = explode('-',$ORDER_NUMBER_TMP);
				$ORDER_NUMBER_TMP = $LINE_ARR[0];
				unset($LINE_ARR[0]);
				$LINES = implode(',',$LINE_ARR);
				if(!empty($LINES)){
					$sql.=" WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND ORDER_NUMBER='$ORDER_NUMBER_TMP'";
					$sql.=" AND LINE_NUMBER IN ($LINES) ";//@TanDoan:Trường hợp trùng nên get 1
				}
			}else{
				$sql.=" WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND ORDER_NUMBER = '$ORDER_NUMBER_TMP'";
			}
			$sql.=$OrderBy;
			$res = MiQuery($sql,$conn2);
			// get oracle download
			if(empty($res)||count($res)<1){
				$response = [
					'status' => false,
					'mess' =>  "SO LINE $ORDER_NUMBER_ORG KHÔNG TỒN TẠI TRÊN ORACLE DOWNLOAD! X"
				];
				echo json_encode($response);die;
			}
		}

		/** -------------START:  @tandoan: Thêm đoạn code check tồn tại SOLine, nếu có báo ngưng làm lệnh ---------------------------------------- */
			foreach($res as $value_supply){
				$ORDER_NUMBER = trim($value_supply['ORDER_NUMBER']);
				$LINE_NUMBER = trim($value_supply['LINE_NUMBER']);
				$SO_LINE_DOUBLE = $ORDER_NUMBER . '-' . $LINE_NUMBER;

				$number_no_double_check = '';
				$created_date_double_check = '';
				$sql_double_check = "SELECT material_save.ID_SAVE_ITEM AS ID_SAVE_ITEM, item_save.CREATED_TIME AS  CREATED_TIME
									FROM `save_material` AS material_save INNER JOIN `save_item` AS item_save
									ON material_save.ID_SAVE_ITEM = item_save.NUMBER_NO
									WHERE material_save.SO_LINE = '$SO_LINE_DOUBLE' ORDER BY material_save.ID DESC LIMIT 0,1  ";
				if ($query_double_check = mysqli_query($conn2, $sql_double_check)) {
					if ( mysqli_num_rows($query_double_check) > 0 ) {
						$result_double_check = mysqli_fetch_array($query_double_check, MYSQLI_ASSOC);

						if (!empty($result_double_check)) {
							$number_no_double_check = !empty($result_double_check["ID_SAVE_ITEM"]) ? trim($result_double_check["ID_SAVE_ITEM"]) : '';
							$created_date_double_check = !empty($result_double_check["CREATED_TIME"]) ? $result_double_check["CREATED_TIME"] : '';

						}
						$response = [
							'status' => false,
							'mess' =>  "SOLINE: $SO_LINE_DOUBLE đã làm lệnh ngày $created_date_double_check .NUMBER NO: $number_no_double_check. Vui lòng vào VIEW NO để xem lại"
						];
						echo json_encode($response); die();

					}
				}

				break;
			}
		/** -------------END:  @tandoan: Thêm đoạn code check tồn tại SOLine, nếu có báo ngưng làm lệnh ---------------------------------------- */

		/* Start: @TanDoan: Số Scrap vật tư: -------------------------------------------------------------------------------------------------------------- */
			// Từ 2.3 thành 2.26
			// Từ 2.26 thành 2.19 (Chị @Phung Yêu cầu 20210222)
			$scrap_Material = 1.021;
		/* End: @TanDoan: Số Scrap vật tư: -------------------------------------------------------------------------------------------------------------- */

		$dataResult = [];
		foreach ($res as $row){
			/* START: Trường hợp đơn hàng lấy từ automail trạng thái ENTER thì không cho làm lệnh */
				if ($data_source_thermal == 'auto_mail' ) {
					$FLOW_STATUS_CODE = trim($row['FLOW_STATUS_CODE']);
					if (strpos(strtoupper($FLOW_STATUS_CODE),'ENTER') !== false ) {
						$response = [
							'status' => false,
							'mess' =>  "ĐƠN HÀNG ĐANG ENTERED"
						];
						echo json_encode($response);die;
					}

				}

			/* END: Trường hợp đơn hàng lấy từ automail trạng thái ENTER thì không cho làm lệnh */

			// Order infos
			if(!empty($row['PRODUCTION_METHOD'])&&strpos($row['PRODUCTION_METHOD'],'RFID')!==FALSE){
				// continue; //Fix RFID
			}
			$ID = $row['ID'];


			$REQUEST_DATE = trim($row['REQUEST_DATE']);
			if(empty($REQUEST_DATE) || (strpos($REQUEST_DATE, '1970') !== false ) ){
					$response = [
					'status' => false,
					'mess' =>  "DỮ LIỆU REQUEST DATE TRỐNG."
				];
				echo json_encode($response);die;
			}else{
				//@TanDoan: case combine RFID vs Thermal
				$REQUEST_DATE_REAL = trim($row['REQUEST_DATE']);
				$REQUEST_DATE_REAL = date("d-M-y", strtotime("$REQUEST_DATE_REAL"));

				$REQUEST_DATE = trim($row['REQUEST_DATE']);
				$REQUEST_DATE = date('d-M-y',strtotime($REQUEST_DATE));
				$REQUEST_DATE = formatDate($REQUEST_DATE);
			}


			// @TanDoan - 20210325: Nếu PD trống thì cộng 5 ngày tính từ ngày làm lệnh thực tế. mail: Re: Tools for IC FRU / Blank PD
			$PROMISE_DATE = trim($row['PROMISE_DATE']);
			if(empty($PROMISE_DATE) || (strpos($PROMISE_DATE, '1970') !== false ) ){
				$PROMISE_DATE = getBonus5Date($REQUEST_DATE); // dạng hiển thị: d/m/Y
			}else{
				$PROMISE_DATE = date('d-M-y',strtotime($PROMISE_DATE));
				$PROMISE_DATE = formatDate($PROMISE_DATE);
			}

			

			if(!empty($row['ORDERED_DATE'])){
				$ORDERED_DATE = trim($row['ORDERED_DATE']);
				$ORDERED_DATE = date('d-M-y',strtotime($ORDERED_DATE));
				$ORDERED_DATE = formatDate($ORDERED_DATE,'',0);
			}else{
				$ORDERED_DATE = '';
			}

			$ORDER_NUMBER = trim($row['ORDER_NUMBER']);
			$PACKING_INSTRUCTIONS = trim($row['PACKING_INSTRUCTIONS']);
			$ORDER_TYPE_NAME = trim($row['ORDER_TYPE_NAME']);

			
			// // // xử lý trường hợp đơn hàng BN 20201217
			// // if (stripos($ORDER_TYPE_NAME, 'BNH') !== false ) {
			// // 	$response = [
			// // 		'status' => false,
			// // 		'mess' =>  "SO# $ORDER_NUMBER LÀ ĐƠN HÀNG BẮC NINH"
			// // 	];
			// // 	echo json_encode($response);die;
			// // }
			


			$LINE_NUMBER = trim($row['LINE_NUMBER']);
			$QTY = (int)trim($row['QTY']);
			$ORDERED_ITEM = trim($row['ORDERED_ITEM']);
			$ITEM = trim($row['ITEM']);
			$ITEM_DESC = trim($row['ITEM_DESC']);
			$RBO = trim($row['SOLD_TO_CUSTOMER']);
			$SHIP_TO = trim($row['SHIP_TO_CUSTOMER']);
			$CS = trim($row['CS']);
			// Paper infos
			$MATERIAL_CODE = '';
			$MATERIAL_DES = '';
			// Need Q'ty
			$EA_SHT = 0;
			$YD = 0;
			$MT = 0;
			// Size lable
			$LENGTH = '';
			$WIDTH = '';
			// Ink infos
			$INK_CODE = '';
			$INK_DES = '';
			$INK_QTY = '';
			$UNIT = '';
			$SO_UPS	= '';
			$INTERNAL_ITEM = '';
			$REMARK_1 = '';
			$REMARK_2 = '';
			$REMARK_3 = '';
			$REMARK_4 = '';
			$REMARK_5 = '';
			$REMARK_6 = '';
			$REMARK_7 = '';
			$MATERIAL_QTY = '-';
			// Size lable
			$LENGTH = '';
			$WIDTH = '';
			// Ink infos
			$INK_CODE = '';
			$INK_DES = '';
			$INK_QTY = 0;
			$specialVT = 0;
			$DATA_MULTIPE = '';
			$SAMPLE = 0;
			$SO_UPS_TEXT = '';
			$sql_get_material = "SELECT * from master_bom where INTERNAL_ITEM='$ITEM'";
			$resMaterial = MiQuery($sql_get_material,$conn2);
			if(empty($resMaterial)||count($resMaterial)<1){
				$response = [
					'status' => false,
					'mess' =>  "SO-LINE không tồn tại INTERNAL ITEM: ".$ITEM." trên hệ thống, vui lòng cập nhật"
				];
				echo json_encode($response);die;
			}

			// check print_type
			$print_type = $_COOKIE['print_type_thermal'];
			if(!empty($resMaterial[0])){


				// GET NHOM PX OR AD
				// @tandoan: 20200707: thêm nhóm IPPS
				$NHOM_PX_AD = strtoupper(trim($resMaterial[0]['NHOM']));

				/* 
					@TanDoan - 20211117: Để cộng cho trường hợp RBO = UNIQLO && NHOM = FG
					+ Cộng thêm số lượng tại REMARK_4_SAN_XUAT cho Số lượng và tính code vật tư, mực theo trường hợp này
					+ Anh Quang Phan yêu cầu. email: 

				 */
				
					$REMARK_4_SAN_XUAT = trim($resMaterial[0]['REMARK_4_SAN_XUAT']);
					// if (checkUniqloFG($RBO, $NHOM_PX_AD) && is_numeric($REMARK_4_SAN_XUAT) ) {
					// 	$QTY += (int)$REMARK_4_SAN_XUAT;
					// }

					
				/** end @TanDoan - 20211117: Để cộng cho trường hợp RBO = UNIQLO && NHOM = FG */


				// @tandoan: 20200725 dùng để sử dụng tính công thức vật tư cho form TRIM, SIPS
				$PCS_SET = (int)trim($resMaterial[0]['PCS_SET']); 

				if(!empty($NHOM_PX_AD)){
					$print_type_show = strtoupper($print_type);
					if ($print_type=='paxar') {
						if($NHOM_PX_AD != 'PX' && $NHOM_PX_AD != 'IPPS' && $NHOM_PX_AD != 'FG' ) {
							$response = [
								'status' => false,
								'mess' =>  "NHÓM $NHOM_PX_AD, ITEM $ITEM KHÔNG THUỘC VỀ LÀM LỆNH $print_type_show, VUI LÒNG KIỂM TRA LẠI (*)"
							];
							echo json_encode($response);die;
						}
					} else {
						if($NHOM_PX_AD!='AD') {
							$response = [
								'status' => false,
								'mess' =>  "ITEM $ITEM KHÔNG THUỘC VỀ LÀM LỆNH $print_type_show , VUI LÒNG KIỂM TRA LẠI"
							];
							echo json_encode($response);die;
						}
					}

				}

				// Lấy ORDERED_ITEM từ BOM. Anh Quang yêu cầu: 20210325 (hangout)
					$ORDERED_ITEM = (!empty($resMaterial[0]['ORDERED_ITEM'])) ? trim($resMaterial[0]['ORDERED_ITEM']) : $ORDERED_ITEM;
					$ITEM_DESC = (!empty($resMaterial[0]['ITEM_DESC'])) ? trim($resMaterial[0]['ITEM_DESC']) : $ITEM_DESC;
					
				
				$MATERIAL_CODE_2 = trim($resMaterial[0]['MATERIAL_CODE_2']);
				$MATERIAL_DES_2 = trim($resMaterial[0]['MATERIAL_DES_2']);
				$MATERIAL_UOM_2 = trim($resMaterial[0]['MATERIAL_UOM_2']);
				$RIBBON_CODE_2 = trim($resMaterial[0]['RIBBON_CODE_2']);
				$RIBBON_DES_2 = trim($resMaterial[0]['RIBBON_DES_2']);
				$UNIT = strtoupper(trim($resMaterial[0]['MATERIAL_UOM']));
				$LENGTH = trim($resMaterial[0]['CHIEU_DAI']);
				$WIDTH = trim($resMaterial[0]['CHIEU_RONG']);
				$COLOR_BY_SIZE_TEXT = trim($resMaterial[0]['COLOR_BY_SIZE']);
				$GAP = !empty($resMaterial[0]['GAP']) ? trim($resMaterial[0]['GAP']) : 0;
				$GAP = (int)$GAP;

				// @tandoan: 20200707: Dùng để tính đơn hàng IPPS (hàng bán)
				$BASE_ROLL = trim($resMaterial[0]['BASE_ROLL']); // nếu đơn vị con nhãn là KIT, SL Vật tư = $qty * base_roll
				$BASE_ROLL = (int)$BASE_ROLL;
				$RIBBON_MT_KIT = trim($resMaterial[0]['RIBBON_MT_KIT']); // nếu đơn vị con nhãn là KIT, SL mực = $qty * ribbon mt kit
				$RIBBON_MT_KIT = (int)$RIBBON_MT_KIT;

				// get SAMPLE
				if($ORDER_TYPE_NAME=='VN SAM'){
					$SAMPLE = 0;
				}else{
					$sql_count_sample = "SELECT COUNT(ID) FROM vnso WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND PACKING_INSTRUCTIONS LIKE '%$ORDER_NUMBER%' AND LINE_NUMBER='$LINE_NUMBER' AND ORDER_TYPE_NAME='VN SAM' AND ITEM='$ITEM'";
					$count_sample = MiQuery($sql_count_sample,$conn);
					if($count_sample>0){
						$SAMPLE = 1;
					}else{
						$SAMPLE = 2;
					}
				}
				$SO_UPS_TEXT = trim($resMaterial[0]['UPS']);
				if($print_type=='paxar'){
					if(($COLOR_BY_SIZE_TEXT=='YES'||$COLOR_BY_SIZE_TEXT=='yes'||$COLOR_BY_SIZE_TEXT=='Yes')&&(strpos($RBO,"NIKE")!==FALSE||strpos($RBO,"UNDER ARMOUR")!==FALSE||strpos($RBO,"FANATICS")!==FALSE)){
						//echo "don hang dac biet";
						$COLOR_BY_SIZE = 1;
						if(empty($row['VIRABLE_BREAKDOWN_INSTRUCTIONS'])){
							$response = [
								'status' => false,
								'mess' =>  "KHÔNG LẤY ĐƯỢC SIZE TỪ AUTOMAIL, VUI LÒNG KIỂM TRA! (1) "
							];
							echo json_encode($response);die;
						}
						$VIRABLE_BREAKDOWN_INSTRUCTIONS = trim($row['VIRABLE_BREAKDOWN_INSTRUCTIONS']);
						// get multiple material
						
						// $MATERIAL_CODE_LIST = getSizeNew($ORDER_NUMBER, $LINE_NUMBER);
						$MATERIAL_CODE_LIST = array();

						// print_r($MATERIAL_CODE_LIST); exit();

						if (empty($MATERIAL_CODE_LIST) ) {
							$MATERIAL_CODE_LIST = getMaterialCode($VIRABLE_BREAKDOWN_INSTRUCTIONS);
						}

						if (empty($MATERIAL_CODE_LIST)) {
							$response = [
								'status' => false,
								'mess' =>  "KHÔNG LẤY ĐƯỢC SIZE TỪ AUTOMAIL, VUI LÒNG KIỂM TRA! (2)"
							];
							echo json_encode($response);die;
						}

						/** -------START: @tandoan: Check Số lượng size và số lượng automail khác nhau -------------------------------------------*/
							$qty_automail_check = (int)$QTY;
							$size_qty_total_check = 0;
							foreach ($MATERIAL_CODE_LIST as $size_item_check) {
								$size_qty_check = (int)$size_item_check['qty'];
								$size_qty_total_check += $size_qty_check;
							}

							if ($qty_automail_check !== $size_qty_total_check) {
								$response = [
									'status' => false,
									'mess' =>  " (*) SỐ LƯỢNG TRÊN ORACLE: $qty_automail_check VÀ SỐ LƯỢNG TỔNG SIZE: $size_qty_total_check KHÔNG GIỐNG NHAU!"
								];
								echo json_encode($response);die;
							}
						/** ------- END: @tandoan: Check Số lượng size và số lượng automail khác nhau -------------------------------------------*/

						if(empty($MATERIAL_CODE_LIST)){
							$response = [
								'status' => false,
								'mess' =>  "KHÔNG LẤY ĐƯỢC SIZE TỪ AUTOMAIL, VUI LÒNG KIỂM TRA LẠI ĐỊNH DẠNG SIZE! "
							];
							echo json_encode($response);die;
						}
						// Paper infos
						$MATERIAL_CODE = '';
						$MATERIAL_DES = trim($resMaterial[0]['MATERIAL_DES']);;
						$SET = trim($resMaterial[0]['PCS_SET']);
						if(empty($SET)){
							$SET = 1;
						}
						$specialVT = 1;
						// PREPARE CODE
						$SO_UPS = trim($resMaterial[0]['UPS']);
						if(empty($SO_UPS)){
							$SO_UPS = 1;
						}
						if(in_array($ITEM,['P342582A','P342588A','P246529E','1-217817-000-00'])){
							$CHIA_3 = 1;
						}else{
							$CHIA_3 = 0;
						}
						if($specialVT){
							$DATA_MULTIPE = [];
							if(!empty($MATERIAL_CODE_LIST)){
								foreach($MATERIAL_CODE_LIST as $key => $value){
									$SIZE_QTY = $value['qty'];
									$EA_SHT = 0;
									if(in_array($UNIT,['EA','SHEET'])||in_array($ITEM,['1T000051-000-00','1T000052-000-00','1T000062-000-00'])){
										$EA_SHT = $SIZE_QTY * $scrap_Material * $SET; //@tandoan: vật tư.
										$EA_SHT = ceil($EA_SHT);
									}
									if($UNIT=='YD'&&$CHIA_3==0){
										$YD = ( ($SIZE_QTY+15) * $LENGTH ) / 1000 * $scrap_Material;
									}else{
										if($UNIT=='YD'&&$CHIA_3==1){
											$YD = ( ($SIZE_QTY+15) * $LENGTH ) / 1000 * $scrap_Material / 3;
										}else{
											$YD = 0;
										}
									}
									$YD = ceil($YD*$SET/0.914);
									$MT = 0;
									if($UNIT=='MT'&&$EA_SHT==0){
										$MT = ((($SIZE_QTY*($LENGTH+$GAP))/1000)* $scrap_Material )*$SET; //@tandoan: 20200314 Tính $GAP sau
										//$MT = round($MT,3);
										$MT = ceil($MT);
									}
									if($UNIT=="EA"){
										$INK_QTY_TMP = ((($SIZE_QTY*($LENGTH+$GAP))/1000)* $scrap_Material )/$SO_UPS*$SET; //@tandoan: 20200314 Tính $GAP sau
										$INK_QTY_CHECK = ceil($INK_QTY_TMP);
									}else{
										$INK_QTY_CHECK = 0;
									}
									$DATA_MULTIPE[] = [
										'MATERIAL_CODE' => $value['material_code'],
										'MATERIAL_DES' => $MATERIAL_DES,
										'EA_SHT' => $EA_SHT,
										'YD' => $YD,
										'MT' => $MT,
										'INK_CODE' => trim($resMaterial[0]['RIBBON_CODE']),
										'INK_DES' => trim($resMaterial[0]['RIBBON_DES']),
										'INK_QTY' => $INK_QTY_CHECK,
										'COLOR' => (strpos($RBO,"NIKE")!==FALSE)?$value['color']:$ITEM_DESC,
										'SIZE_QTY' => $SIZE_QTY,
									];
								}
							}
							$DATA_MULTIPE = json_encode($DATA_MULTIPE);
						}

						$INTERNAL_ITEM = trim($resMaterial[0]['INTERNAL_ITEM']);
						// Ink infos
						$INK_CODE = trim($resMaterial[0]['RIBBON_CODE']);
						$INK_DES = trim($resMaterial[0]['RIBBON_DES']);
						$INK_QTY = (((trim($MATERIAL_CODE_LIST[0]['qty'])*($LENGTH+$GAP))/1000) * $scrap_Material )/$SO_UPS*$SET; // @tandoan: chinh $GAP,
						// Need Q'ty
						$EA_SHT = '';
						$YD = '';
						$MT = '';
						// REMARK
						if(strtoupper($resMaterial[0]['LAYOUT_PREPRESS'])=='YES'){
							$REMARK_1 = 'CHUYEN PREPRESS LAM LAYOUT';
						}else{
							$REMARK_1 = '';
						}
						// if(!empty($row['PACKING_INSTRUCTIONS'])&&strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
						// 	$REMARK_3 = trim($row['PACKING_INSTRUCTIONS']);
						// }
						$REMARK_4 = ($ITEM=='P301234')?'IN KHONG CAT - GIAO HANG DANG ROLL':'';
						$REMARK_5 = '';
					}else{
						

						$COLOR_BY_SIZE = 0;
						$SET = trim($resMaterial[0]['PCS_SET']);
						if(empty($SET)){
							$SET = 1;
						}
						if(!empty($MATERIAL_CODE_2)||!empty($RIBBON_CODE_2)){
							$specialVT = 1;
						}
						// PREPARE CODE
						if($specialVT){
							$DATA_MULTIPE = [];
							$DATA_MULTIPE['MATERIAL_CODE'] = $MATERIAL_CODE_2;
							$DATA_MULTIPE['MATERIAL_DES'] = $MATERIAL_DES_2;
							$DATA_MULTIPE['EA_SHT'] = 0; // luon luon bang 0
							$DATA_MULTIPE['YD'] = 0; // luon luon bang 0
							if($MATERIAL_UOM_2=="MT"){
								$MT_TMP = ((($QTY*$LENGTH)/1000) * $scrap_Material )*$SET; //
								$MT_TMP = ceil($MT_TMP);
								$DATA_MULTIPE['MT'] = $MT_TMP;
							}else{
								$DATA_MULTIPE['MT'] = 0;
							}
							$DATA_MULTIPE['INK_CODE'] = $RIBBON_CODE_2;
							$DATA_MULTIPE['INK_DES'] = $RIBBON_DES_2;
							$DATA_MULTIPE['INK_QTY'] = 0;
							$DATA_MULTIPE = json_encode($DATA_MULTIPE);
						}

						$INTERNAL_ITEM = trim($resMaterial[0]['INTERNAL_ITEM']);
						$SO_UPS = trim($resMaterial[0]['UPS']);
						if(empty($SO_UPS)){
							$SO_UPS = 1;
						}
						if(in_array($ITEM,['P342582A','P342588A','P246529E','1-217817-000-00'])){
							$CHIA_3 = 1;
						}else{
							$CHIA_3 = 0;
						}
						// Paper infos
						$MATERIAL_CODE = trim($resMaterial[0]['MATERIAL_CODE']);
						$MATERIAL_DES = trim($resMaterial[0]['MATERIAL_DES']);
						// Ink infos
						$INK_CODE = trim($resMaterial[0]['RIBBON_CODE']);
						$INK_DES = trim($resMaterial[0]['RIBBON_DES']);

						//tandoan: 20200707 - Xử lý đơn IPPS (hàng bán): Tính số lượng mực
						if ((stripos($NHOM_PX_AD,'IPPS') !== false) || (stripos($NHOM_PX_AD,'FG') !== false) ) {
							$INK_QTY = getIPPSInkQty($QTY, $RIBBON_MT_KIT );
						} else {

							$INK_QTY = ((($QTY*($LENGTH+$GAP))/1000)* $scrap_Material )/$SO_UPS*$SET; // @tandoan: tính $GAP

						}

						// Need Q'ty
						$EA_SHT = 0;


						//tandoan: 20200707 - Xử lý đơn IPPS (hàng bán) 
						// @tandoan: 20200707: Lấy UOM từ bảng tbl_productline_item (đơn vị con nhãn)
						// Nếu KIT thì sẽ nhân với cột BASE_ROLL để tính số lượng vật tư, cột RIBBON_MT_KIT để tính số lượng mực, hiển thị remark CHI_TIET_KIT
						if ((stripos($NHOM_PX_AD,'IPPS') !== false) || (stripos($NHOM_PX_AD,'FG') !== false) ) {

							$material_qty = getIPPSMaterialQty($INTERNAL_ITEM, $NHOM_PX_AD, $UNIT, $QTY, $BASE_ROLL );
							$EA_SHT = $material_qty['EA_SHT'];
							
							$YD = $material_qty['YD'];
							$MT = $material_qty['MT'];

							if (checkUniqloFG($RBO, $NHOM_PX_AD) && is_numeric($REMARK_4_SAN_XUAT) ) {
								if(!empty($EA_SHT) ) $EA_SHT += (int)$REMARK_4_SAN_XUAT;
								if(!empty($YD) ) $YD += (int)$REMARK_4_SAN_XUAT;
								if(!empty($MT) ) $MT += (int)$REMARK_4_SAN_XUAT;
								
							}

						} else {

							if($UNIT=='EA'||$UNIT=='SHEET'){

								$EA_SHT = $QTY * $scrap_Material * $SET;
								$EA_SHT = ceil($EA_SHT);

								//trường hợp tạm tính @tandoan
								if ( $ITEM == '1-257723-000-01') {
									$EA_SHT = $QTY*1.1;
									$EA_SHT = ceil($EA_SHT);
								}

								/*@tandoan: Tạm tính các ITEM đặc biệt */
								$ArrMain = array();
								$ArrMain []= array("ITEM" => "1-257716-000-01", "MATERIAL" => "6-700245-000-02", "SCRAP" => 1.1);
								$ArrMain []= array("ITEM" => "1-257723-000-01", "MATERIAL" => "6-700246-000-01", "SCRAP" => 1.1);
								$ArrMain []= array("ITEM" => "1-151089-001-00", "MATERIAL" => "6-700450-000-01", "SCRAP" => 1.25);
								$ArrMain []= array("ITEM" => "1-151089-002-00", "MATERIAL" => "6-700450-000-01", "SCRAP" => 1.25);
								$ArrMain []= array("ITEM" => "1-101985-000-00", "MATERIAL" => "6-700450-000-01", "SCRAP" => 1.25);
								$ArrMain []= array("ITEM" => "1-101985-000-01", "MATERIAL" => "6-700450-000-01", "SCRAP" => 1.25);
								$ArrMain []= array("ITEM" => "1-151089-002-01", "MATERIAL" => "6-700450-000-01", "SCRAP" => 1.25);
								$ArrMain []= array("ITEM" => "1-117375-000-00", "MATERIAL" => "6V001976-000-01", "SCRAP" => 2);
								$ArrMain []= array("ITEM" => "1-202474-000-00", "MATERIAL" => "6V001976-000-01", "SCRAP" => 2);
								$ArrMain []= array("ITEM" => "2-137978-000-00", "MATERIAL" => "6V001976-000-01", "SCRAP" => 2);

								foreach($ArrMain as $R) {
									if($R["ITEM"] == $ITEM && $R["MATERIAL"] == $MATERIAL_CODE) {
										$EA_SHT = $QTY*$R["SCRAP"];
										$EA_SHT = ceil($EA_SHT);
									}
								}

							}

							// tính số lượng mực đơn vị là YD

							if($UNIT=='YD'&&$CHIA_3==0){
								$YD = (($QTY+15)*$LENGTH)/1000* $scrap_Material;
							}else{
								if($UNIT=='YD'&&$CHIA_3==1){
									$YD = (($QTY+15)*$LENGTH)/1000* $scrap_Material / 3;
								}else{
									$YD = 0;
								}
							}

							$YD = $YD*$SET/0.914;
							$MT = 0;
							if($UNIT=='MT'&&$EA_SHT==0){
								$MT = ((($QTY*$LENGTH)/1000) * $scrap_Material )*$SET;
								//$MT = round($MT,3);
								$MT = ceil($MT);
							}

						}

						// REMARK
						if(strtoupper($resMaterial[0]['LAYOUT_PREPRESS'])=='YES'){
							$REMARK_1 = 'CHUYEN PREPRESS LAM LAYOUT';
						}else{
							$REMARK_1 = '';
						}
						// if(!empty($row['PACKING_INSTRUCTIONS'])&&strpos($ORDER_TYPE_NAME,'VN SAM')!==false){
						// 	$REMARK_3 = trim($row['PACKING_INSTRUCTIONS']);
						// }
						$REMARK_4 = ($ITEM=='P301234')?'IN KHONG CAT - GIAO HANG DANG ROLL':'';
						$REMARK_5 = '';
					}
				}elseif($print_type=='trim'){
					$SO_UPS = trim($resMaterial[0]['UPS']);
					if(empty($SO_UPS)){
						$SO_UPS = 1;
					}
					// get SAMPLE
					if($ORDER_TYPE_NAME=='VN SAM'){
						$SAMPLE = 0;
					}else{
						$sql_count_sample = "SELECT COUNT(ID) FROM vnso WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND PACKING_INSTRUCTIONS LIKE '%$ORDER_NUMBER%' AND LINE_NUMBER='$LINE_NUMBER' AND ORDER_TYPE_NAME='VN SAM' AND ITEM='$ITEM'";
						$count_sample = MiQuery($sql_count_sample,$conn);
						if($count_sample>0){
							$SAMPLE = 1;
						}else{
							$SAMPLE = 2;
						}
					}
					$specialVT = 0;
					$LENGTH = trim($resMaterial[0]['CHIEU_DAI']);
					$MATERIAL_CODE = trim($resMaterial[0]['MATERIAL_CODE']);
					$RIBBON_CODE_2 = trim($resMaterial[0]['RIBBON_CODE_2']);
					$MATERIAL_QTY = '';
					$INK_CODE = trim($resMaterial[0]['RIBBON_CODE']);
					$INK_QTY = '';
					if(!empty($RIBBON_CODE_2)){ // check 2 muc
						$specialVT = 1;
					}

					
					if (in_array($ITEM,['2-111696-000-00','2-340177-000-SHT','2-340183-000-SHT','2-378861-000-SHT','2-378862-000-SHT'])){//@TanDoan: 20190817 - add 2 item
						$MATERIAL_QTY = $QTY;
						$INK_QTY = ceil((($QTY*($LENGTH+$GAP)*$scrap_Material)/1000)/$SO_UPS); //@tandoan: 20200314 Tính $GAP sau, chia thêm số UPS
					} else if ($MATERIAL_CODE=='TH03572' || $MATERIAL_CODE=='TH06297' ) {//@Tandoan: Trâm yêu cầu 20190923. Material/35
						// Tính số lượng vật tư
							$MATERIAL_QTY = $QTY * $scrap_Material;
							$MATERIAL_QTY = $MATERIAL_QTY/35;
							$MATERIAL_QTY = ceil($MATERIAL_QTY);
						// Tính số lượng mực
							$INK_QTY = ceil((($QTY*($LENGTH+$GAP)* $scrap_Material )/1000)/$SO_UPS); //@tandoan: 20200314 Tính $GAP sau, chia thêm số UPS
						
					} else{
						/* START: @tandoan: 20200725: XỬ LÝ SỐ LƯỢNG CODE VẬT TƯ DỰA VÀO PCS SET */
						/* 
							|	@tandoan: 20200725: XỬ LÝ SỐ LƯỢNG CODE VẬT TƯ DỰA VÀO PCS SET
							|	Kiểm tra PCS, Tính công thức: 	
							|	- Công thức số lượng vật tư: Nếu có PCS_SET thì giữ công thức như trước và nhân thêm PCS_SET
							|	- Công thức số lượng mực: Nếu có PCS_SET thì giữ công thức như trước và nhân thêm PCS_SET
						
						*/
							// Tính số lượng vật tư
								$MATERIAL_QTY = $QTY * $scrap_Material; 
								$MATERIAL_QTY = ceil($MATERIAL_QTY);

							// Kiểm tra lại PCS để Tính số lượng mực, số lượng vật tư lại
							if (!empty($PCS_SET) || $PCS_SET != 0 ) {
								
								// Kiểm tra PCS_SET có phải số hay không, Nếu không phải thì không lấy PCS_SET tính
								if (is_int($PCS_SET) ) {
									// Lấy PCS SET nhân với số lượng vật tư
										$MATERIAL_QTY = $MATERIAL_QTY*$PCS_SET; 
									
									//@tandoan: 20200314 Tính $GAP sau, chia thêm số UPS
										$INK_QTY = ceil(((($QTY*($LENGTH+$GAP)* $scrap_Material )/1000)*$PCS_SET)/$SO_UPS); 

								} else {
									//@tandoan: 20200314 Tính $GAP sau, chia thêm số UPS
										$INK_QTY = ceil((($QTY*($LENGTH+$GAP)* $scrap_Material)/1000)/$SO_UPS); 	
								}

							} else {
								//@tandoan: 20200314 Tính $GAP sau, chia thêm số UPS
									$INK_QTY = ceil((($QTY*($LENGTH+$GAP)* $scrap_Material )/1000)/$SO_UPS); 
							}
						/* END: @tandoan: 20200725: XỬ LÝ SỐ LƯỢNG CODE VẬT TƯ DỰA VÀO PCS SET */
						
					}
					$DATA_MULTIPE = '';
					if($specialVT){
						$DATA_MULTIPE = [];
						$DATA_MULTIPE['INK_CODE'] = $RIBBON_CODE_2;
					}
					$DATA_MULTIPE = json_encode($DATA_MULTIPE);
					$INTERNAL_ITEM = trim($resMaterial[0]['INTERNAL_ITEM']);
					if($SHIP_TO=='Cong ty TNHH SUNGJIN INC VINA'){
						$REMARK_1 = 'TACH RIENG TUNG SIZE /1 COC , CAC SIZE CUNG 1 STYLE BO VAO 1 BICH, GHI SO STYLE RA NGOAI BICH';
					}
					elseif($SHIP_TO=="GRAND TWINS INT'L (CAMBODIA0 LTD"||$SHIP_TO=="CCH TOP (VN) CO LT_TAN THUAN ROAD, TANSH"){
						$REMARK_3 = 'DANH DAU TUNG THUNG TRONG DN GUI KH+ DN GUI SHIPPING';
					}
					elseif($SHIP_TO=="CONG TY TNHH TY XUAN/KHU CONG NGHIEP HOA"){
						$REMARK_4 = 'SX IN TEM THUNG VA DONG GOI THEO SO# LINE CHO KH TY XUAN/VINH LONG-DAN CON NHAN TUONG UNG CUA SO# LINE';
					}
					if(substr($MATERIAL_CODE,0,2)=='TH'){
						$REMARK_2 = 'San xuat them 15pcs lam mau';
					}
					$REMARK_5 = 'Nhãn Nike đính kèm pl trong thùng hàng với tất cả đơn hàng KH FAR EASTERN,Riêng Nike 1 up vẫn giữ qui cách đóng gói';

					///@TanDoan: add cột PACKING_INSTRUCTIONS vào remark_3 cho TRIM (trinh.nguyen, thitram.nguyen yêu cầu, không có ràng buộc gì về điều kiện)
					$REMARK_3_PACKING_INSTRUCTIONS = trim($row['PACKING_INSTRUCTIONS']);
					$REMARK_3 = $REMARK_3_PACKING_INSTRUCTIONS .".".$REMARK_3;

				}elseif($print_type=='sips'){
					// get SAMPLE
					if($ORDER_TYPE_NAME=='VN SAM'){
						$SAMPLE = 0;
					}else{
						$sql_count_sample = "SELECT COUNT(ID) FROM vnso WHERE ITEM <> 'VN FREIGHT CHARGE' AND ITEM <> 'VN FREIGHT CHARGE 1' AND PACKING_INSTRUCTIONS LIKE '%$ORDER_NUMBER%' AND LINE_NUMBER='$LINE_NUMBER' AND ORDER_TYPE_NAME='VN SAM' AND ITEM='$ITEM'";
						$count_sample = MiQuery($sql_count_sample,$conn);
						if($count_sample>0){
							$SAMPLE = 1;
						}else{
							$SAMPLE = 2;
						}
					}
					$SO_UPS = trim($resMaterial[0]['UPS']);
					if($SO_UPS==0){
						$SO_UPS = 1;
					}
					$specialVT = 0;
					$LENGTH = trim($resMaterial[0]['CHIEU_DAI']);
					$MATERIAL_CODE = trim($resMaterial[0]['MATERIAL_CODE']);
					$MATERIAL_QTY = '';
					$INK_CODE = trim($resMaterial[0]['RIBBON_CODE']);
					$INK_QTY = '';

					/* START: @tandoan: 20200725: XỬ LÝ SỐ LƯỢNG CODE VẬT TƯ DỰA VÀO PCS SET */
						/* 
							|	Kiểm tra PCS, Tính công thức: 	
							|	- Công thức số lượng vật tư: Nếu có PCS_SET thì giữ công thức như trước và nhân thêm PCS_SET
							|	- Công thức số lượng mực: Giữ như cũ KHÔNG THAY ĐỔI
						
						*/

						if (!empty($PCS_SET) || $PCS_SET != 0 ) {
								
							// Kiểm tra PCS_SET có phải số hay không, Nếu không phải thì không lấy PCS_SET tính
							if (is_int($PCS_SET) ) {
								$MATERIAL_QTY = $QTY * $scrap_Material;
								// Lấy số lượng vật tư nhân với PCS_SET
								$MATERIAL_QTY = $MATERIAL_QTY * $PCS_SET;
								$MATERIAL_QTY = round_up($MATERIAL_QTY,0);
							} else {
								$MATERIAL_QTY = round_up($QTY* $scrap_Material, 0);
							}

						} else {
							$MATERIAL_QTY = round_up($QTY* $scrap_Material,0);
						}

					// Tính số lượng mực
						$INK_QTY = ceil($QTY*($LENGTH+$GAP)*1.024/1000/$SO_UPS); //@tandoan: 20200314 Tính $GAP sau,
					
					$INTERNAL_ITEM = trim($resMaterial[0]['INTERNAL_ITEM']);
					if($INTERNAL_ITEM=='1-106507-000-00'&&(strpos($SHIP_TO,'FASHION GARMENT')!==false||strpos($SHIP_TO,'CRYSTAL')!==false)){
						$REMARK_1 = 'Sản xuất vui lòng photocopy 01 bộ packing list dán ngoài thùng hàng.';
					}
					if(strpos($SHIP_TO,"DIAMOND")!==false&&strpos($RBO,"NEW BALANCE")!==false){
						$REMARK_1 .= "Lay mau moi PO 1 pc size bat ky, bo vao bich va dan ngoai thung hang.";
					}else{
						if(strpos($SHIP_TO,"DIAMOND")==false&&strpos($RBO,"NEW BALANCE")==false){
							$REMARK_1 .= "Lay mau moi PO 1 pc size bat ky, bo vao bich va dan ngoai thung hang.";
						}
					}
					if(strpos($RBO,"SINTEX")!==false){
						$REMARK_1 .= ".ĐÓNG GÓI THEO DẠNG TỪNG BÓ 100 Pcs";
					}
					if($INTERNAL_ITEM=='1-106507-000-00'&&(strpos($SHIP_TO,'CONG TY TNHH HANSOLL VINA')!==false||strpos($SHIP_TO,'KOTOP VINA')!==false||strpos($SHIP_TO,'UNISOLL')!==false)){
						$REMARK_3 = 'TREN DN GIAO HANG VUI LONG GHI RO TRINH TU THUNG CARTON DONG HANG.';
						$REMARK_4 = 'LAY MAU MOI SIZE 5PCS LAM 5 BO TRIMCARD ROI GOI TRUC TIEP CHO KAYLATRAN CS DE GIAO DI KOREA.';
					}
				}

				$REMARK_1 = $REMARK_1 . $resMaterial[0]['REMARK_1_ITEM'];
				$REMARK_2 = $REMARK_2 . $resMaterial[0]['REMARK_2_SHIPPING'];
				// $REMARK_3 = $REMARK_3 . $resMaterial[0]['REMARK_3_PACKING'];//@tandoan: bo remark 3 trong masster file
				// $REMARK_3 = $REMARK_3;
				
				$REMARK_4 = $REMARK_4 . $REMARK_4_SAN_XUAT;

				//@TanDoan: Thermal combine với RFID: request date -1 day
				if($REMARK_4 == 'Hàng Nike Thermal combine với RFID')
				{
					$REQUEST_DATE = date("d-M-y", strtotime("$REQUEST_DATE - 1 day"));
					$REMARK_4 = 'Hàng Nike Thermal combine với RFID (Ngày CRD thực: '.$REQUEST_DATE_REAL.')';
				}
			}



			if($print_type=='paxar'){
				$dataResult[] = [
					'id' => $ID,
					'data' => [1,$ORDER_NUMBER,$LINE_NUMBER,$QTY,$ORDERED_ITEM,$ITEM,$RBO,$PROMISE_DATE,$REQUEST_DATE,$ORDERED_DATE,$ITEM_DESC,$MATERIAL_CODE,$MATERIAL_DES,$EA_SHT,ceil($YD),$MT,round($LENGTH,2),round($WIDTH,2),$INK_CODE,$INK_DES,ceil($INK_QTY),$UNIT,$SO_UPS,$SHIP_TO,$CS,$INTERNAL_ITEM,$REMARK_1,$REMARK_2,$REMARK_3,$REMARK_4,$REMARK_5,$specialVT,$DATA_MULTIPE,$SAMPLE,$SO_UPS_TEXT,$ORDER_TYPE_NAME,$PACKING_INSTRUCTIONS,$COLOR_BY_SIZE]
				];
			}elseif($print_type=='trim'){
				$dataResult[] = [
					'id' => $ID,
					'data' => [1,$ORDER_NUMBER,$LINE_NUMBER,$QTY,$ORDERED_ITEM,$ITEM,$RBO,$PROMISE_DATE,$REQUEST_DATE,$ORDERED_DATE,$MATERIAL_CODE,$MATERIAL_QTY,$INK_CODE,$INK_QTY,$SHIP_TO,$CS,$INTERNAL_ITEM,$REMARK_1,$REMARK_2,$REMARK_3,$REMARK_4,$REMARK_5,$specialVT,$DATA_MULTIPE,$SAMPLE,round($LENGTH,2),round($WIDTH,2),$SO_UPS_TEXT,$ORDER_TYPE_NAME,$PACKING_INSTRUCTIONS]
				];
			}elseif($print_type=='sips'){
				$dataResult[] = [
					'id' => $ID,
					'data' => [1,$ORDER_NUMBER,$LINE_NUMBER,$QTY,$ORDERED_ITEM,$ITEM,$RBO,$PROMISE_DATE,$REQUEST_DATE,$ORDERED_DATE,$MATERIAL_CODE,$MATERIAL_QTY,$INK_CODE,$INK_QTY,$SHIP_TO,$CS,$INTERNAL_ITEM,$REMARK_1,$REMARK_2,$REMARK_3,$REMARK_4,$REMARK_5,0,$DATA_MULTIPE,$SAMPLE,round($LENGTH,2),round($WIDTH,2),$SO_UPS_TEXT,$ORDER_TYPE_NAME,$PACKING_INSTRUCTIONS]
				];
			}
		}
		$response = [
			'status' => TRUE,
			'data' => $dataResult
		];
		echo json_encode($response);die;
	}
}
?>