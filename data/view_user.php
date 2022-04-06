<?php   
function formatDate($value){
	return date('d-M-y',strtotime($value));
}
require_once("../Database.php");

$conn = _conn();

$script = basename($_SERVER['PHP_SELF']);
$urlRoot = str_replace($script,'',$_SERVER['PHP_SELF']);
$urlRoot = str_replace('data/','',$urlRoot);
header("Content-type:text/xml");//set content type and xml tag
echo "<?xml version=\"1.0\"?>";
    $sql = "SELECT * FROM user"; 
    $rowsResult = MiQuery($sql, $conn);
    if ($conn ) mysqli_close($conn);
    
if(count($rowsResult)>0){ 
	echo("<rows>");
	if(!empty($rowsResult)){ 
		$cellStart = "<cell><![CDATA[";
        $cellEnd = "]]></cell>";
		foreach ($rowsResult as $row){
			$ID = $row['ID'];
			$EMAIL = $row['user'];
			$NOTE = $row['NOTE'];
			/*
			if($deleteNO){
				$link  = 'DELETE^javascript:deleteMS('.$ID.');^_self';
			}	
			*/
			echo("<row id='".$ID."'>");
				echo $cellStart;  // LENGTH
					echo(0);  //value for product name                 
				echo $cellEnd;
				echo( $cellStart);  // LENGTH
					echo($EMAIL);  //value for product name                 
				echo( $cellEnd);
				echo( $cellStart);  // LENGTH
					echo($NOTE);  //value for product name                 
				echo( $cellEnd);
			echo("</row>");
		}
	}
	echo("</rows>");
}else{
	echo("<rows></rows>");
}
?>