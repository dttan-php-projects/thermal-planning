<?php
require_once("../Database.php");
//set content type and xml tag
header("Content-type:text/xml");
echo "<?xml version=\"1.0\"?>";     
echo("<rows>");
$cellStart = "<cell><![CDATA[";
$cellEnd = "]]></cell>";
$label_item ='';
$item ='';
if(!empty($_GET['label_item'])){
    $label_item = $_GET['label_item'];
}
if(!empty($_GET['item'])){
    $item = $_GET['item'];
}
for($i=1;$i<=30;$i++){    
    echo("<row>");
        echo $cellStart;
            echo('');    //value for price
        echo $cellEnd;  
        echo $cellStart;
            echo($label_item);    //value for price
        echo $cellEnd;
        echo $cellStart;
            echo($item);    //value for price
        echo $cellEnd;   
        echo $cellStart;
            echo('');    //value for price
        echo $cellEnd;     
    echo("</row>");
}
echo("</rows>");
?>