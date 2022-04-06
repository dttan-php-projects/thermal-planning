<?php 
require_once("../Database.php");

$conn = _conn();

$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script,'',$_SERVER['PHP_SELF']);
$urlRoot = str_replace('data/','',$urlRoot);
header("Content-type:text/xml");//set content type and xml tag
echo "<?xml version=\"1.0\"?>";


$FROM_DATE = $_GET['from_date_value'];
$FROM_DATE = date('Y-m-d',strtotime($FROM_DATE));
$TO_DATE = $_GET['to_date_value'];
$TO_DATE = date('Y-m-d',strtotime($TO_DATE));
// to do process so kho if(type_worst_vertical = 100-SB1) 10,5
$FORM_TYPE = '';
if(!empty($_COOKIE["print_type_thermal"])){
	$FORM_TYPE = $_COOKIE["print_type_thermal"];
} 
//@TanDoan: 20190731
if(empty($_GET['so_search_value'])){
	if($FROM_DATE!='1970-01-01'&&$TO_DATE!='1970-01-01'){
		$sql = "SELECT distinct ID_SAVE_ITEM,SO_LINE,ITEM,INTERNAL_ITEM,CREATED_BY,PRINTED,s_i.CREATE_DATE FROM save_material as s_m join save_item as s_i on s_i.NUMBER_NO = s_m.ID_SAVE_ITEM and FORM_TYPE = '$FORM_TYPE' and (s_m.CREATE_DATE>='$FROM_DATE' AND s_m.CREATE_DATE<='$TO_DATE') order by ID_SAVE_ITEM desc,s_m.ID"; 
	}else{
		$sql = "SELECT distinct ID_SAVE_ITEM,SO_LINE,ITEM,INTERNAL_ITEM,CREATED_BY,PRINTED,s_i.CREATE_DATE FROM save_material as s_m join save_item as s_i on s_i.NUMBER_NO = s_m.ID_SAVE_ITEM and FORM_TYPE = '$FORM_TYPE' order by ID_SAVE_ITEM desc,s_m.ID ASC limit 0,500"; 
	}
	
}else{
	$sql = "SELECT ID_SAVE_ITEM,SO_LINE,ITEM,INTERNAL_ITEM,CREATED_BY,PRINTED,s_i.CREATE_DATE 
			FROM save_material as s_m join save_item as s_i 
			WHERE s_i.NUMBER_NO = s_m.ID_SAVE_ITEM AND s_m.SO_LINE = '".$_GET['so_search_value']."' ";
}


$rowsResult = MiQuery($sql, $conn);
if ($conn ) mysqli_close($conn);
// check role
$arrayRole = ['phung.le','hang.nguyenthu','tri.pham','vien.trinh','yen.thai','thitram.nguyen','trinh.truong','hoang.dang', 'tan.doan1', 'giau.duong', 'son.dang', 'duy.dang', 'jimmy.dang', 'chanh.ht.nguyen'];
$user = '';
if(!empty($_COOKIE["VNRISIntranet"])){
	$user = $_COOKIE["VNRISIntranet"];
	if(in_array($user,$arrayRole)){
		$deleteNO = 1;
	}else{
		$deleteNO = 0;
	}
}    
if(count($rowsResult)>0){ 
	$header = '<head>
					<column width="85" type="ed" align="left" sort="str">DATE</column>
					<column width="140" type="ed" align="left" sort="str">NO</column>
					<column width="140" type="ed" align="left" sort="str">SO-LINE</column>
					<column width="140" type="ed" align="left" sort="str">ITEM</column>
					<column width="140" type="ed" align="left" sort="str">INTERNAL ITEM</column>
					<column width="85" type="ed" align="left" sort="str">CREATED BY</column>
					<column width="80" type="ed" align="left" sort="str">PRINTED</column>
					<column width="80" type="link" align="left" sort="str"></column>
					<column width="*" type="link" align="left" sort="str"></column>
				</head>';
	echo("<rows>");	
	echo $header;
	if(!empty($rowsResult)){ 
		$cellStart = "<cell><![CDATA[";
        $cellEnd = "]]></cell>";
		$ID = 0;
		foreach ($rowsResult as $row){
			$ID++;
			$SAVE_DATE = $row['CREATE_DATE']; 
			$SAVE_DATE = date('d-M-y',strtotime($SAVE_DATE));
			$PRINTED = $row['PRINTED'];
			$NUMBER_NO = $row['ID_SAVE_ITEM'];
			$EMAIL = $row['CREATED_BY'];
			$SO_LINE = $row['SO_LINE'];
			$INTERNAL_ITEM = $row['INTERNAL_ITEM'];
			$ITEM = $row['ITEM'];
			if($deleteNO && ($user == $EMAIL || $user == 'tan.doan1' || $user == 'vien.trinh')){
				$link  = 'DELETE^javascript:delete_no("'.$NUMBER_NO.'");^_self';
			}	
			$linkPrint = "print.php?id=$NUMBER_NO";		
			echo("<row id='".$ID."'>");
			echo( $cellStart);  // LENGTH
				echo($SAVE_DATE);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($NUMBER_NO);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($SO_LINE);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($ITEM);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($INTERNAL_ITEM);  //value for product name                 
			echo( $cellEnd);
			echo( $cellStart);  // LENGTH
				echo($EMAIL);  //value for product name                 
			echo( $cellEnd);
			if($PRINTED=='1'){
				echo( $cellStart);
					echo("YES");  //value for product name                 
				echo( $cellEnd);
			}else{
				echo( $cellStart);
					echo("NO");  //value for product name                 
				echo( $cellEnd);
			}
			if($PRINTED=='1'){
				echo("<cell><![CDATA[<font color='red'></front>");  // LENGTH
					echo("Print NO^$linkPrint");  //value for product name                 
				echo("]]></cell>");
			}else{
				echo($cellStart);  // LENGTH
					echo("Print NO^$linkPrint");  //value for product name                 
				echo($cellEnd);
			}
			if($deleteNO && ($user == $EMAIL || $user == 'tan.doan1' || $user == 'vien.trinh')){
				echo( $cellStart);  // LENGTH
				echo $link;  //value for product name                 
				echo( $cellEnd);
			}				
			echo("</row>");
		}
	}
	echo("</rows>");
}else{
	echo("<rows></rows>");
}
?>