<?php

	date_default_timezone_set('Asia/Ho_Chi_Minh');
	require_once("./Database.php");

	// check role
	$rolesUpdateDB = ['minh.vo','phung.le','hang.nguyenthu','tri.pham','vien.trinh','yen.thai','thitram.nguyen','tuan.vi','trinh.truong','binh.hoang'];
	$updateDB = 0;
	if(!empty($_COOKIE["VNRISIntranet"])){
		$user = $_COOKIE["VNRISIntranet"];
		if(!empty($user)){
			// DB
			if(in_array($user,$rolesUpdateDB)){
				$updateDB = 1;
			}
		}
	}
	// get print_type
	if(empty($_COOKIE['print_type_thermal'])){
		setcookie('print_type_thermal','paxar', time() + (86400 * 365), "/"); // 86400 = 1 day
	}
	// get print_type
	if(empty($_COOKIE['data_source_thermal'])){
		setcookie('data_source_thermal','auto_mail', time() + (86400 * 365), "/"); // 86400 = 1 day
	}
	// get print_type
	if(!empty($_COOKIE['print_type_thermal'])){
		$print_type = $_COOKIE['print_type_thermal'];
	}else{
		$print_type ='';
	}
	// get print_type
	if(!empty($_COOKIE['data_source_thermal'])){
		$data_source = $_COOKIE['data_source_thermal'];
	}else{
		$data_source ='';
	}
	$SQL_CREATE_DATE = "SELECT CREATEDDATE FROM autoload_log where FUNC = 'AUTOMAIL' order by ID desc limit 0,1;";
	$CREATE_DATE = MiQuery($SQL_CREATE_DATE,_conn("au_avery"));
	if(!empty($CREATE_DATE)){
		$CREATE_DATE_TEXT = 'AUTOMAIL UPDATED: '.date('d-M-y H:i',strtotime($CREATE_DATE));
	}else{
		$CREATE_DATE_TEXT = '';
	}
?>

<!DOCTYPE html>
<html>
<head>
    <title>THERMAL</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="google" content="notranslate" />
	<link rel="STYLESHEET" type="text/css" href="/dhtmlx5F/skins/skyblue/dhtmlx.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<script src="/dhtmlx5F/codebase/dhtmlx.js" type="text/javascript"></script>
	<script src="/dhtmlx5F/JS/jquery-1.10.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>
    html, body {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
		font-family: "Source Sans Pro","Helvetica Neue",Helvetica;
		background-repeat: no-repeat;
		background-size: 100%;
    }
    .formShow input,.formShow select{ 
            font-size:12px !important; 
            font-weight:bold;
    }
    @media only screen and (max-width: 1600px) {
        
    }
	.dhxtoolbar_btn_pres .dhxtoolbar_text{
		font-weight:bold!important;
	}
	.dhxwin_active .objbox td a:visited,td.hight_light_cell a{
		color:red!important;
	}
</style>
<script>
var updateDB = <?php echo $updateDB;?>;
var LayoutMain;
var MainMenu;
var ToolbarMain;
var RootPath = '<?php echo str_replace('INDEXY.PHP','',strtoupper(str_replace('index__.php','',$_SERVER['REQUEST_URI'])));?>';  
var RootDataPath = RootPath+'data/';
var LayoutForm;
var SoForm;
var SoGrid;
var MaterialGrid;
var SizeGrid;
var SoGridLoad = RootDataPath+'grid_so.php';
var MaterialGridLoad = RootDataPath+'grid_material.php';
var SizeGridLoad = RootDataPath+'grid_size.php';
var checked_SOLINE = [];
var noGrid;
var print_type = '<?php echo $print_type;?>';
var data_source = '<?php echo $data_source;?>';
if(data_source=='auto_mail'){
	data_source_text = 'AUTOMAIL';
}else if(data_source=='oracle_download'){
	data_source_text = 'ORACLE DOWNLOAD';
}
<?php
    if(!isset($_COOKIE["VNRISIntranet"])) {
        echo 'var HeaderTile = "'.$CREATE_DATE_TEXT.'<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi Guest | <a href=\"/Login.php?URL=THERMAL\">Login</a></a>";var UserVNRIS = "";';
    } else {
        echo 'var HeaderTile = "'.$CREATE_DATE_TEXT.'<a style=\'color:blue;font-style:italic;padding-left:10px\'>Hi '.$_COOKIE["VNRISIntranet"].' | <a href=\"/Data/Logout.php\">Logout</a></a>";var UserVNRIS = "'.$_COOKIE["VNRISIntranet"].'";';
    }
?>
    var widthScreen = screen.width;
    var widthSo = 690;
	var heightOrder = 590;
    if(widthScreen<=1600){
        widthSo = 388;
    }
	
	function setCookie(cname,cvalue,exdays) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires=" + d.toGMTString();
		document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return -1;
	}	
	// xxxx document.cookie = "auto_sample=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    function initLayout(){
        LayoutMain = new dhtmlXLayoutObject({
            parent: document.body,
            pattern: "3L",
            offsets: {
                top: 65
            },
            cells: [
                {id: "a", header: true, text: "LIST SO", width: widthSo}, 
				{id: "b", header: true, text: "PRODUCTION ORDER",height: heightOrder},  
				{id: "c", header: true, text: "LIST MATERIAL"},
            ]
        });
    }
    function initMenu(){
        MainMenu = new dhtmlXMenuObject({
				parent: "menuObj",
				icons_path: "/dhtmlx5F/common/imgs_Menu/",
				json: "/FileHome/Menu.json",
				top_text: HeaderTile
        });
    }	
	
	function LoadFormUA(){		
        if(!dhxWinsAddUA){
            dhxWinsAddUA= new dhtmlXWindows();// show window form to add length
        }
        if (!dhxWinsAddUA.isWindow("windowAddUA")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowAddUA = dhxWinsAddUA.createWindow("windowAddUA", 697,65,395,320);
            windowAddUA.setText("Window Add ITem");
            /*necessary to hide window instead of remove it*/
            windowAddUA.attachEvent("onClose", function(win){
                if (win.getId() == "windowAddUA") 
                    win.hide();
            });
			formData = [
				{type: "fieldset", label: "Uploader", list:[
					{type: "upload", name: "myFiles", autoStart: true, inputWidth: 330, url: "data/item_upload.php"}
				]}
			];
			addUAForm = windowAddUA.attachForm(formData, true);
			addUAForm.attachEvent("onFileAdd",function(realName){
				LayoutMain.cells("b").progressOn();	
			});
			addUAForm.attachEvent('onUploadFail', function(fileName,extra){
				alert(extra.mess);
				var myUploader = addUAForm.getUploader('myFiles');
				myUploader.clear();
				LayoutMain.cells("b").progressOff();
			});
			addUAForm.attachEvent('onUploadFile', function(state,fileName,extra){
				LayoutMain.cells("b").progressOff();
				alert(extra.mess);
				location.reload();				
			});
			
        }else{
            dhxWinsAddUA.window("windowAddUA").show(); 
        } 
    }
	
	function updateUA(){
		UAGrid.attachEvent("onEnter", function(id,ind){
			// your code here			
			var ITEM_CODE = UAGrid.cells(id,1).getValue();
			ITEM_CODE = ITEM_CODE.trim();
			var ORDER_ITEM = UAGrid.cells(id,2).getValue();
			ORDER_ITEM = ORDER_ITEM.trim();
			var ORACLE_MATERIAL = UAGrid.cells(id,3).getValue();
			ORACLE_MATERIAL = ORACLE_MATERIAL.trim();
			var DESCRIPTION_MATERIAL = UAGrid.cells(id,4).getValue();
			DESCRIPTION_MATERIAL = DESCRIPTION_MATERIAL.trim();
			var WIDTH = UAGrid.cells(id,5).getValue();
			WIDTH = WIDTH.trim();
			var HEIGHT = UAGrid.cells(id,6).getValue();
			HEIGHT = HEIGHT.trim();
			var INK_CODE = UAGrid.cells(id,7).getValue();
			INK_CODE = INK_CODE.trim();
			var INK_DESCRIPTION = UAGrid.cells(id,8).getValue();
			INK_DESCRIPTION = INK_DESCRIPTION.trim();
			var UP = UAGrid.cells(id,9).getValue();
			UP = UP.trim();
			var MATERIAL_UNIT = UAGrid.cells(id,10).getValue();
			MATERIAL_UNIT = MATERIAL_UNIT.trim();
			var SET = UAGrid.cells(id,11).getValue();
			SET = SET.trim();
			var VAT_TU_CHIA_3 = UAGrid.cells(id,12).getValue();
			VAT_TU_CHIA_3 = VAT_TU_CHIA_3.trim();
			var LAYOUT = UAGrid.cells(id,13).getValue();
			LAYOUT = LAYOUT.trim();
			var DANG_ROLL = UAGrid.cells(id,14).getValue();
			DANG_ROLL = DANG_ROLL.trim();
			var VAI = UAGrid.cells(id,15).getValue();
			VAI = VAI.trim();
			var SIPS_VT_X2 = UAGrid.cells(id,16).getValue();
			SIPS_VT_X2 = SIPS_VT_X2.trim();
			var objUA = {
				ITEM_CODE   			:  	ITEM_CODE,
				ORDER_ITEM   			:  	ORDER_ITEM,
				ORACLE_MATERIAL   		:  	ORACLE_MATERIAL,
				DESCRIPTION_MATERIAL   	:  	DESCRIPTION_MATERIAL,
				WIDTH   				:  	WIDTH,
				HEIGHT   				:  	HEIGHT,
				INK_CODE   				:  	INK_CODE,
				INK_DESCRIPTION   		:  	INK_DESCRIPTION,
				UP   					:  	UP,
				MATERIAL_UNIT   		:  	MATERIAL_UNIT,
				SET   					:  	SET,
				VAT_TU_CHIA_3   		:  	VAT_TU_CHIA_3,
				LAYOUT   				:  	LAYOUT,
				DANG_ROLL   			:  	DANG_ROLL,
				VAI   					:  	VAI,
				SIPS_VT_X2   			:  	SIPS_VT_X2,
				idUA					:	id
			};		
			var url_update = RootDataPath+'update_ua.php';
			$.ajax({
				url: url_update,
				type: "POST",
				data: {data: JSON.stringify(objUA)},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						// change ID
						if(result.id){
							UAGrid.changeRowId(id,result.id);
						}
						console.log(result.id);
						alert('Update dữ liệu thành công!!!!');
					}else{
						alert(result.mess);
					}
				}
			});
		});
	}
	
	function deleteUA(){
		if(!updateDB){
			alert("Bạn không được phân quyền để XÓA");
			return false;
		}
		var checkIDs = [];
		UAGrid.forEachRow(function(id){
			if(UAGrid.cells(id,0).getValue()==1){
				checkIDs.push(id);
			}
		});
		if(!checkIDs.length>0){
			alert("Vui lòng chọn dòng để XÓA");
			return false;
		}else{
			confirm_delete = confirm("Bạn có muốn XÓA những item đã chọn!!!");
			if(confirm_delete){
				var url_delete = RootDataPath+'delete_ua.php';
				// get all checkbox
				$.ajax({
					url: url_delete,
					type: "POST",
					data: {data: JSON.stringify(checkIDs)},
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
						x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							// reload	
							for(var i=0;i<checkIDs.length;i++){
								UAGrid.deleteRow(checkIDs[i]);
							}
						}else{
							alert(result.mess);							
						}
					}
				});					
			}
		}
	}	
	var ToolbarMaterial;		
    function initToolbar(){		
        ToolbarMain = new dhtmlXToolbarObject({
            parent: "ToolbarBottom",
            icons_path: "/dhtmlx5F/common/imgs/",
            align: "left",
        });
        // end 
        ToolbarMain.addText("", 1, "<a style='font-size:20pt;font-weight:bold'>"+''+"THERMAL <?php echo strtoupper($print_type);?></a>");
        ToolbarMain.addText("", 2, "SO");
        ToolbarMain.addInput("so",3,""); // set for test 27210890
		ToolbarMain.addText("", 4, "FROM DATE");
        ToolbarMain.addInput("from_date",5,""); // set for test 27210890
		ToolbarMain.addText("", 6, "TO DATE");
        ToolbarMain.addInput("to_date",7,""); // set for test 27210890
		var from_date_input = ToolbarMain.getInput("from_date");
		var to_date_input = ToolbarMain.getInput("to_date");		
		myCalendar = new dhtmlXCalendarObject([from_date_input,to_date_input]);
		myCalendar.setDateFormat("%d-%M-%y");
		ToolbarMain.addSpacer("to_date");
		/*
		ToolbarMain.addButtonTwoState('auto_print', 4, 'Auto Print', "print.gif", null);	
		ToolbarMain.hideItem("auto_print");
		// check state
		// set default 
		if(getCookie('auto_print')==-1){ // set default is 0 => disable auto print
			setCookie('auto_print',1,365);
		}
		*/
		/*
		if(getCookie('print_type')==-1){
			setCookie('print_type','paxar',365);
		}		
		if(getCookie('auto_sample')==-1){
			setCookie('auto_sample',1,365);
		}
		if(getCookie('auto_print')>0){
			ToolbarMain.setItemState('auto_print', true, false); 
		}else{
			ToolbarMain.setItemState('auto_print', false, false); // set default auto print
		}	
		ToolbarMain.addButtonTwoState('auto_sample', 5, 'Sample', "page_info.gif", null);
		ToolbarMain.hideItem("auto_sample");
		// check state
		if(getCookie('auto_sample')>0){
			ToolbarMain.setItemState('auto_sample', true, false); 
		}else{
			ToolbarMain.setItemState('auto_sample', false, false); // set default auto print
		}       
		ToolbarMain.addSpacer("auto_sample");	
		*/
		//ToolbarMain.addButton("insert_bom",17, "UPDATE BOM", "page_info.gif");
		ToolbarMain.addButton("view_oracle",18, "View Oracle", "page_info.gif");
		ToolbarMain.addButton("upload_oracle",19, "Upload Oracle", "page_info.gif");
		var opts = [['paxar', 'obj', 'PAXAR'],['trim', 'obj', 'TRIM'],['sips', 'obj', 'SIPS']];
		var opts_data = [['auto_mail', 'obj', 'AUTOMAIL'],['oracle_download', 'obj', 'ORACLE DOWNLOAD']];
		ToolbarMain.addButtonSelect('select_data_source', 20, 'Data Source', opts_data, "save.gif", null);
		ToolbarMain.addButtonSelect('select_save', 21, 'Save No', opts, "save.gif", null);
        ToolbarMain.addButtonSelect('select_print', 22, 'Print No', opts, "print.gif", null);
		// set selected from cookies
		if(print_type!==''){
			ToolbarMain.setListOptionSelected('select_save', print_type);
			ToolbarMain.setListOptionSelected('select_print', print_type);
		}
		if(data_source!==''){
			ToolbarMain.setListOptionSelected('select_data_source', data_source);
		}
		// end set selected from cookies
		// get
		//returns id of the selected listed option
		// var listOptionId = ToolbarMain.getListOptionSelected('select_save');
        ToolbarMain.addButton("view_no",23, "View No", "page_info.gif");
        //ToolbarMain.addButton("report_no",12, "Report", "page_info.gif");
		ToolbarMain.addButton("export",24, "Export", "xlsx.gif");
		ToolbarMain.addButton("export_trim_sip",25, "Export Trim&Sips", "xlsx.gif");
        ToolbarMain.addButton("export_all_no",26, "Export All NO", "xlsx.gif");    
        ToolbarMain.attachEvent("onClick", function(name)
        {
            if(name == "printNo")
            {
                saveDatabase(true);
            }
            else if(name == "view_no")
            {
                viewNO();
            }else if(name == "saveNo"){
                saveDatabase(false);
            }else if(name == "view_oracle")
            {
                viewOracle();
            }
            else if(name == "export"){
				var from_date_value = ToolbarMain.getValue("from_date");
				var to_date_value = ToolbarMain.getValue("to_date");
				if(!from_date_value||!to_date_value){
					alert('VUI LÒNG CHỌN KHOẢNG NGÀY ĐỂ EXPORT DỮ LIỆU');
					return false;
				}
				var url_export = RootDataPath+'report_type_new.php?print_type='+print_type+'&from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                document.location.href = url_export;
            }
            else if(name == "export_trim_sip"){
				var from_date_value = ToolbarMain.getValue("from_date");
				var to_date_value = ToolbarMain.getValue("to_date");
				if(!from_date_value||!to_date_value){
					alert('VUI LÒNG CHỌN KHOẢNG NGÀY ĐỂ EXPORT DỮ LIỆU');
					return false;
				}
                var url_export = RootDataPath+'report_trim_sips_new.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                document.location.href = url_export;
            }
            else if(name == "export_all_no"){
				var from_date_value = ToolbarMain.getValue("from_date");
				var to_date_value = ToolbarMain.getValue("to_date");
				if(!from_date_value||!to_date_value){
					alert('VUI LÒNG CHỌN KHOẢNG NGÀY ĐỂ EXPORT DỮ LIỆU');
					return false;
				}
                var url_export = RootDataPath+'report_no_all_new.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                document.location.href = url_export;
            }else if(name == "select_data_source"){
				return false;
            }
			else if(name == "select_save"){
				saveDatabase();
            }
			else if(name == "select_print"){
				saveDatabase(true);
            }else if(name == "paxar"||name == "trim"||name == "sips"){
				setCookie('print_type_thermal',name,365);
				ToolbarMain.setListOptionSelected('select_save', name);
				ToolbarMain.setListOptionSelected('select_print', name);
				location.reload();
			}else if(name == "auto_mail"||name == "oracle_download"){
				setCookie('data_source_thermal',name,365);
				ToolbarMain.setListOptionSelected('select_data_source', name);
				location.reload();
			}else if(name == "upload_oracle")
			{
				window.open(
				  'upload_oracle_download.php',
				  '_blank' // <- This is what makes it open in a new window.
				);
			}else if(name == "insert_bom")
			{
				var url_update = RootDataPath+'insert_bom.php';			
				$.ajax({
					url: url_update,
					type: "POST",
					data: '',
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							alert(result.mess);
						}else{
							alert(result.mess);
						}
					}
				});
			}
        }); 
		// change state		
		ToolbarMain.attachEvent("onStateChange", function(id, state){
			if(id == "auto_print")
            {
				if(state){
					setCookie('auto_print',1,365);
				}else{
					setCookie('auto_print',0,365);
				}								
            }else if(id == "auto_sample"){
				if(state){
					setCookie('auto_sample',1,365);
				}else{
					setCookie('auto_sample',0,365);
				}
			}
		});		
    }
    var dhxWins;
    var viewNOGrid;
    function viewNO(){
        if(!dhxWins){
            dhxWins= new dhtmlXWindows();// show window form to add length
        }   
        if (!dhxWins.isWindow("windowViewNo")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewNo = dhxWins.createWindow("windowViewNo", 697,65,975,590);
            windowViewNo.setText("Window View NO");
            /*necessary to hide window instead of remove it*/
            windowViewNo.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewNo") 
                    win.hide();
            });
            viewNOGrid = windowViewNo.attachGrid();
            viewNOGrid.enableSmartRendering(true);
			viewNOGrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");		
			viewNOGrid.enableMultiselect(true);				
            viewNOGrid.init();  
			var from_date_value = ToolbarMain.getValue("from_date");
			var to_date_value = ToolbarMain.getValue("to_date");
            //viewNOGrid.load(RootDataPath+'view_no.php');
			viewNOGrid.load(RootDataPath+'view_no.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value,function(){
				
			});			
        }else{
            dhxWins.window("windowViewNo").show(); 
        } 
    }
    var viewOracleGrid;
    function viewOracle(){
        if(!dhxWins){
            dhxWins= new dhtmlXWindows();// show window form to add length
        }   
        if (!dhxWins.isWindow("windowViewOracle")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewOracle = dhxWins.createWindow("windowViewOracle", 697,65,400,590);
            windowViewOracle.setText("Window View Oracle");
            /*necessary to hide window instead of remove it*/
            windowViewOracle.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewOracle") 
                    win.hide();
            });
            viewOracleGrid = windowViewOracle.attachGrid();
            viewOracleGrid.enableSmartRendering(true);
			viewOracleGrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");					
            viewOracleGrid.init();  
            viewOracleGrid.load(RootDataPath+'view_oracle.php');                
        }else{
            dhxWins.window("windowViewOracle").show(); 
        } 
    }

	
		
	
	var dhxWinsUA;
    var viewUAGrid;
	var UAGrid;
	var dhxWinsAddUA;
    function loadListUA(){		
        if(!dhxWinsUA){
            dhxWinsUA= new dhtmlXWindows();// show window form to add length
        }
        if (!dhxWinsUA.isWindow("windowViewUA")){
            // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
            windowViewUA = dhxWinsUA.createWindow("windowViewUA", 2,65,1830,870);
			dhxWinsUA.window("windowViewUA").progressOn();
            windowViewUA.setText("Window View ITEM");
            /*necessary to hide window instead of remove it*/
            windowViewUA.attachEvent("onClose", function(win){
                if (win.getId() == "windowViewUA") 
                    win.hide();
            });
            UAGrid= windowViewUA.attachGrid();
            UAGrid.enableSmartRendering(true);
			UAGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");	
			UAGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");	
			var delete_button = '<input type="button" id="DeleteUA" value="DELETE" onclick="deleteUA()">';
			UAGrid.setHeader(delete_button+',MA HANG - ITEM CODE,NHAN - ORDER ITEM,MA VAT TU - ORACLE MATERIAL,Description MATERIAL,Kich thuoc nhan chieu dai (DVT:mm),Kich thuoc nhan chieu rong (DVT:mm),MA MỰC ORACLE MATERIAL,Description  MỰC,SỐ UPS,DON VI VAT TU,PAXAR SET,PAXAR VT CHIA 3,PAXAR LAYOUT,PAXAR DANG_ROLL,PAXAR VAI,SIPS VT X2,UPDATED BY');
			UAGrid.setInitWidths("90,150,360,200,600,220,230,185,545,60,105,80,120,200,135,123,80,90");
			UAGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");
			UAGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ro");
			UAGrid.setColSorting("na,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");
            UAGrid.init();  
            UAGrid.load(RootDataPath+'view_ua.php',function(){
				dhxWinsUA.window('windowViewUA').maximize();
				var state=UAGrid.getStateOfView();
				dhxWinsUA.window("windowViewUA").progressOff();
				if(state[2]>0){
					UAGrid.showRow(UAGrid.getRowId(state[2]-1));
				}				
				updateUA();
				//UAGrid.setHeader("<div style='width:100%; text-align:left;'>A</div>,B,C");
			}); 
        }else{
            dhxWinsUA.window("windowViewUA").show(); 
        } 		
		//dhxWinsUA.window("windowViewUA").progressOff();
    }
	
    function saveDatabase(print){		
		if(print_type=='paxar'){			
			save = 'SAVE';
			if(print){
				save = 'PRINT';
			}
			if(checked_SOLINE.length>0){
				// stop edit cell
				gridPaxarSave.editStop();
				SoGrid.editStop();
				// BEGIN SAVE ITEM
				var NO = $('#frm_no').val();
				NO = NO.trim();
				var CREATE_DATE = $('#frm_create_date').val();
				CREATE_DATE = CREATE_DATE.trim();
				var ORDER = $('#frm_order').val();
				ORDER = ORDER.trim();
				var REQ = $('#frm_request').val();
				REQ = REQ.trim();
				var PD = $('#frm_promise').val();
				PD = PD.trim();
				var SHIP_TO = $('#frm_ship_to').val();
				SHIP_TO = SHIP_TO.trim();
				var RBO = $('#frm_rbo').val();
				RBO = RBO.trim();
				var CS = $('#frm_cs').val();
				CS = CS.trim();
				var QTY = $('#frm_qty').val();
				QTY = QTY.trim();
				QTY_CHECK = QTY;
				var REMARK_1 = $('#frm_remark_1').val();
				REMARK_1 = REMARK_1.trim();
				var REMARK_2 = $('#frm_remark_2').val();
				REMARK_2 = REMARK_2.trim();
				var REMARK_3 = $('#frm_remark_3').val();
				REMARK_3 = REMARK_3.trim();
				var REMARK_4 = $('#frm_remark_4').val();
				REMARK_4 = REMARK_4.trim();
				var REMARK_5 = $('#frm_remark_5').val();
				REMARK_5 = REMARK_5.trim();
				var FORM_TYPE = print_type;				
				// END SAVE ITEM				
				// check material 
				if(!gridPaxarSave.getRowsNum>0){
					alert("Không lấy được danh sách vật tư hoặc mực vui lòng kiểm tra lại!!!");
					return false;
				}else{
					// save vật tư, mực
					save_material = [];
					obj_material = {};
					COUNT_QTY = 0;
					for (var i=0; i<gridPaxarSave.getRowsNum();i++){
						var SO_LINE = gridPaxarSave.cellByIndex(i,0).getValue();
						SO_LINE = SO_LINE.trim();
						var ITEM = gridPaxarSave.cellByIndex(i,1).getValue();
						ITEM = ITEM.trim();
						var INTERNAL_ITEM = gridPaxarSave.cellByIndex(i,2).getValue();
						INTERNAL_ITEM = INTERNAL_ITEM.trim();
						var ITEM_DES = gridPaxarSave.cellByIndex(i,4).getValue();
						ITEM_DES = ITEM_DES.trim();
						var QTY = gridPaxarSave.cellByIndex(i,5).getValue();
						QTY = QTY.trim();
						COUNT_QTY+=Number(QTY);
						var MATERIAL_CODE = gridPaxarSave.cellByIndex(i,6).getValue();
						MATERIAL_CODE = MATERIAL_CODE.trim();
						var MATERIAL_DES = gridPaxarSave.cellByIndex(i,7).getValue();
						MATERIAL_DES = MATERIAL_DES.trim();
						var EA_SHT = gridPaxarSave.cellByIndex(i,8).getValue();
						EA_SHT = EA_SHT.trim();
						var YD = gridPaxarSave.cellByIndex(i,9).getValue();
						YD = YD.trim();
						var MT = gridPaxarSave.cellByIndex(i,10).getValue();
						MT = MT.trim();
						var LENGTH = gridPaxarSave.cellByIndex(i,11).getValue();
						LENGTH = LENGTH.trim();
						var WIDTH = gridPaxarSave.cellByIndex(i,12).getValue();
						WIDTH = WIDTH.trim();
						var INK_CODE = gridPaxarSave.cellByIndex(i,13).getValue();
						INK_CODE = INK_CODE.trim();
						var INK_DES = gridPaxarSave.cellByIndex(i,14).getValue();
						INK_DES = INK_DES.trim();
						var INK_QTY = gridPaxarSave.cellByIndex(i,15).getValue();
						INK_QTY = INK_QTY.trim();
						var MULTIPLE = gridPaxarSave.cellByIndex(i,16).getValue();
						MULTIPLE = MULTIPLE.trim();
						var SAMPLE = $('#frm_sample').val();						
						var SO_UPS = gridPaxarSave.cellByIndex(i,18).getValue();
						SO_UPS = SO_UPS.trim();
						if(!MATERIAL_CODE&&!INK_CODE){
							alert("Không lấy được danh sách vật tư hoặc mực vui lòng kiểm tra lại!!!");
							return false;
						}else{							
							obj_material = {
								SO_LINE			:	SO_LINE,
								ITEM			:	ITEM,
								INTERNAL_ITEM	:	INTERNAL_ITEM,
								ITEM_DES		:	ITEM_DES,
								QTY				:	QTY,
								MATERIAL_CODE	:	MATERIAL_CODE,
								MATERIAL_DES	:	MATERIAL_DES,
								EA_SHT			:	EA_SHT,
								YD				:	YD,
								MT				:	MT,
								LENGTH			:	LENGTH,
								WIDTH			:	WIDTH,
								INK_CODE		:	INK_CODE,
								INK_DES			:	INK_DES,
								INK_QTY			:	INK_QTY,
								MULTIPLE		:	MULTIPLE,
								SAMPLE			:	SAMPLE,
								SO_UPS			:	SO_UPS
							};
							save_material.push(obj_material);
						}               
					}
				}
				if(QTY_CHECK!=COUNT_QTY){
					console.log(QTY_CHECK);
					console.log(COUNT_QTY);
					alert("SỐ LƯỢNG TRÊN ORACLE VÀ SỐ LƯỢNG TỔNG SIZE KHÔNG GIỐNG NHAU!!!");
					return false;
				}
				var url_save = RootDataPath+'save_item.php'; 
				var COLOR_BY_SIZE = SoGrid.cellByIndex(0,37).getValue();
				save_item ={NO:NO,CREATE_DATE:CREATE_DATE,ORDER:ORDER,REQ:REQ,PD:PD,REQ:REQ,SHIP_TO:SHIP_TO,RBO:RBO,CS:CS,QTY:QTY,REMARK_1:REMARK_1,REMARK_2:REMARK_2,REMARK_3:REMARK_3,REMARK_4:REMARK_4,REMARK_5:REMARK_5,FORM_TYPE:FORM_TYPE,SAMPLE:SAMPLE,COLOR_BY_SIZE:COLOR_BY_SIZE};    
				var jsonObjects = {
					"item": save_item,
					"material":save_material
				}; 			
				$.ajax({
				url: url_save,
					type: "POST",
					data: {data: JSON.stringify(jsonObjects) },
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							if(!print||(print&&!getCookie('auto_print')=='1')){
								alert("Save DATA Thành Công!!!");
							}	
							location.reload();
							if(print){
								var wi = window.open('about:blank', '_blank');
								//$(wi.document.body).html("<p>Please wait while you are being redirected...</p>");
								wi.location.href = RootPath+'print.php?id='+result.NUMBER_NO;
							}                                
						}else{
							alert(result.mess);
						}
					}
				});
			}else{
				alert("Vui lòng chọn một SO-LINE để "+save+"!!!");
				return false;
			}
		}else if(print_type=='trim'){
			save = 'SAVE';
			if(print){
				save = 'PRINT';
			}
			if(checked_SOLINE.length>0){
				// stop edit cell
				gridTrimSave.editStop();
				SoGrid.editStop();
				// BEGIN SAVE ITEM				
				var NO = $('#frm_no').val();
				NO = NO.trim();
				var CREATE_DATE = $('#frm_create_date').val();
				CREATE_DATE = CREATE_DATE.trim();
				var ORDER = $('#frm_order').val();
				ORDER = ORDER.trim();
				var REQ = $('#frm_request').val();
				REQ = REQ.trim();
				var PD = $('#frm_promise').val();
				PD = PD.trim();
				// add data received and file name
				var DATA_RECEIVED = $('#frm_data_received').val();
				DATA_RECEIVED = DATA_RECEIVED.trim();
				var SO_LAN = $('#frm_file').val();
				// add data received and file name
				var SHIP_TO = $('#frm_ship_to').val();
				SHIP_TO = SHIP_TO.trim();
				var RBO = $('#frm_rbo').val();
				RBO = RBO.trim();
				var CS = $('#frm_cs').val();
				CS = CS.trim();
				var QTY = $('#frm_qty').val();
				QTY = QTY.trim();
				var REMARK_1 = $('#frm_remark_1').val();
				REMARK_1 = REMARK_1.trim();
				var REMARK_2 = $('#frm_remark_2').val();
				REMARK_2 = REMARK_2.trim();
				var REMARK_3 = $('#frm_remark_3').val();
				REMARK_3 = REMARK_3.trim();
				var REMARK_4 = $('#frm_remark_4').val();
				REMARK_4 = REMARK_4.trim();
				var REMARK_5 = $('#frm_remark_5').val();
				REMARK_5 = REMARK_5.trim();
				var FORM_TYPE = print_type;				 
				// END SAVE ITEM				
				// check material 
				if(!gridTrimSave.getRowsNum>0){
					alert("Không lấy được danh sách vật tư hoặc mực vui lòng kiểm tra lại!!!");
					return false;
				}else{
					// save vật tư, mực
					save_material = [];
					obj_material = {};
					for (var i=0; i<gridTrimSave.getRowsNum();i++){
						var SO_LINE = gridTrimSave.cellByIndex(i,0).getValue();
						SO_LINE = SO_LINE.trim();
						var ITEM = gridTrimSave.cellByIndex(i,1).getValue();
						ITEM = ITEM.trim();
						var INTERNAL_ITEM = gridTrimSave.cellByIndex(i,2).getValue();
						INTERNAL_ITEM = INTERNAL_ITEM.trim();
						var QTY = gridTrimSave.cellByIndex(i,3).getValue();
						QTY = QTY.trim();
						var MATERIAL_CODE = gridTrimSave.cellByIndex(i,4).getValue();
						MATERIAL_CODE = MATERIAL_CODE.trim();
						var MATERIAL_QTY = gridTrimSave.cellByIndex(i,5).getValue();
						MATERIAL_QTY = MATERIAL_QTY.trim();
						var INK_CODE = gridTrimSave.cellByIndex(i,6).getValue();
						INK_CODE = INK_CODE.trim();
						var INK_QTY = gridTrimSave.cellByIndex(i,7).getValue();
						INK_QTY = INK_QTY.trim();
						var MULTIPLE = gridTrimSave.cellByIndex(i,8).getValue();
						MULTIPLE = MULTIPLE.trim();
						var SAMPLE = $('#frm_sample').val();
						var LENGTH = gridTrimSave.cellByIndex(i,10).getValue();
						LENGTH = LENGTH.trim();
						var WIDTH = gridTrimSave.cellByIndex(i,11).getValue();
						WIDTH = WIDTH.trim();
						var SO_UPS = gridTrimSave.cellByIndex(i,12).getValue();
						SO_UPS = SO_UPS.trim();
						if(!MATERIAL_CODE&&!INK_CODE){
							alert("Không lấy được danh sách vật tư hoặc mực vui lòng kiểm tra lại!!!");
							return false;
						}else{							
							obj_material = {
								SO_LINE				:	SO_LINE,
								ITEM				:	ITEM,
								INTERNAL_ITEM		:	INTERNAL_ITEM,
								QTY					:	QTY,
								MATERIAL_CODE		:	MATERIAL_CODE,
								MATERIAL_QTY		:	MATERIAL_QTY,
								INK_CODE			:	INK_CODE,
								INK_QTY				:	INK_QTY,
								MULTIPLE			:	MULTIPLE,
								SAMPLE				:	SAMPLE,
								LENGTH				:	LENGTH,
								WIDTH				:	WIDTH,
								SO_UPS				:	SO_UPS
							};
							save_material.push(obj_material);
						}               
					}
				}
				var url_save = RootDataPath+'save_item.php'; 
				save_item ={NO:NO,CREATE_DATE:CREATE_DATE,ORDER:ORDER,REQ:REQ,PD:PD,REQ:REQ,SHIP_TO:SHIP_TO,RBO:RBO,CS:CS,QTY:QTY,REMARK_1:REMARK_1,REMARK_2:REMARK_2,REMARK_3:REMARK_3,REMARK_4:REMARK_4,REMARK_5:REMARK_5,FORM_TYPE:FORM_TYPE,SAMPLE:SAMPLE,DATA_RECEIVED:DATA_RECEIVED,SO_LAN:SO_LAN}; 
				var jsonObjects = {
					"item": save_item,
					"material":save_material
				};  
				/*
				console.log(jsonObjects);
				return false;
				*/
				$.ajax({
				url: url_save,
					type: "POST",
					data: {data: JSON.stringify(jsonObjects) },
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							if(!print||(print&&!getCookie('auto_print')=='1')){
								alert("Save DATA Thành Công!!!");
							}
							location.reload();
							if(print){
								var wi = window.open('about:blank', '_blank');
								//$(wi.document.body).html("<p>Please wait while you are being redirected...</p>");
								wi.location.href = RootPath+'print.php?id='+result.NUMBER_NO;
							}                                
						}else{
							alert(result.mess);
						}
					}
				});
				
			}else{
				alert("Vui lòng chọn một SO-LINE để "+save+"!!!");
				return false;
			}
		}else if(print_type=='sips'){
			save = 'SAVE';
			if(print){
				save = 'PRINT';
			}
			if(checked_SOLINE.length>0){
				// stop edit cell
				gridSipsSave.editStop();
				SoGrid.editStop();
				// BEGIN SAVE ITEM				
				var NO = $('#frm_no').val();
				NO = NO.trim();
				var CREATE_DATE = $('#frm_create_date').val();
				CREATE_DATE = CREATE_DATE.trim();
				var ORDER = $('#frm_order').val();
				ORDER = ORDER.trim();
				var REQ = $('#frm_request').val();
				REQ = REQ.trim();
				var PD = $('#frm_promise').val();
				PD = PD.trim();
				// add data received and file name
				var DATA_RECEIVED = $('#frm_data_received').val();
				DATA_RECEIVED = DATA_RECEIVED.trim();
				var SO_LAN = $('#frm_file').val();
				// add data received and file name
				var SHIP_TO = $('#frm_ship_to').val();
				SHIP_TO = SHIP_TO.trim();
				var RBO = $('#frm_rbo').val();
				RBO = RBO.trim();
				var CS = $('#frm_cs').val();
				CS = CS.trim();
				var QTY = $('#frm_qty').val();
				QTY = QTY.trim();
				var REMARK_1 = $('#frm_remark_1').val();
				REMARK_1 = REMARK_1.trim();
				var REMARK_2 = $('#frm_remark_2').val();
				REMARK_2 = REMARK_2.trim();
				var REMARK_3 = $('#frm_remark_3').val();
				REMARK_3 = REMARK_3.trim();
				var REMARK_4 = $('#frm_remark_4').val();
				REMARK_4 = REMARK_4.trim();
				var FORM_TYPE = print_type;				 
				// END SAVE ITEM				
				// check material 
				if(!gridSipsSave.getRowsNum>0){
					alert("Không lấy được danh sách vật tư hoặc mực vui lòng kiểm tra lại!!!");
					return false;
				}else{
					// save vật tư, mực
					save_material = [];
					obj_material = {};
					for (var i=0; i<gridSipsSave.getRowsNum();i++){
						var SO_LINE = gridSipsSave.cellByIndex(i,0).getValue();
						SO_LINE = SO_LINE.trim();
						var ITEM = gridSipsSave.cellByIndex(i,1).getValue();
						ITEM = ITEM.trim();
						var INTERNAL_ITEM = gridSipsSave.cellByIndex(i,2).getValue();
						INTERNAL_ITEM = INTERNAL_ITEM.trim();
						var QTY = gridSipsSave.cellByIndex(i,3).getValue();
						QTY = QTY.trim();
						var MATERIAL_CODE = gridSipsSave.cellByIndex(i,4).getValue();
						MATERIAL_CODE = MATERIAL_CODE.trim();
						var MATERIAL_QTY = gridSipsSave.cellByIndex(i,5).getValue();
						MATERIAL_QTY = MATERIAL_QTY.trim();
						var INK_CODE = gridSipsSave.cellByIndex(i,6).getValue();
						INK_CODE = INK_CODE.trim();
						var INK_QTY = gridSipsSave.cellByIndex(i,7).getValue();
						INK_QTY = INK_QTY.trim();
						var MULTIPLE = gridSipsSave.cellByIndex(i,8).getValue();
						MULTIPLE = MULTIPLE.trim();
						var SAMPLE = $('#frm_sample').val();
						var LENGTH = gridSipsSave.cellByIndex(i,10).getValue();
						LENGTH = LENGTH.trim();
						var WIDTH = gridSipsSave.cellByIndex(i,11).getValue();
						WIDTH = WIDTH.trim();
						var SO_UPS = gridSipsSave.cellByIndex(i,12).getValue();
						SO_UPS = SO_UPS.trim();
						if(!MATERIAL_CODE&&!INK_CODE){
							alert("Không lấy được danh sách vật tư hoặc mực vui lòng kiểm tra lại!!!");
							return false;
						}else{							
							obj_material = {
								SO_LINE				:	SO_LINE,
								ITEM				:	ITEM,
								INTERNAL_ITEM		:	INTERNAL_ITEM,
								QTY					:	QTY,
								MATERIAL_CODE		:	MATERIAL_CODE,
								MATERIAL_QTY		:	MATERIAL_QTY,
								INK_CODE			:	INK_CODE,
								INK_QTY				:	INK_QTY,
								MULTIPLE			:	MULTIPLE,
								SAMPLE				:	SAMPLE,
								LENGTH				:	LENGTH,
								WIDTH				:	WIDTH,
								SO_UPS				:	SO_UPS
							};
							save_material.push(obj_material);
						}               
					}
				}
				var url_save = RootDataPath+'save_item.php'; 
				save_item ={NO:NO,CREATE_DATE:CREATE_DATE,ORDER:ORDER,REQ:REQ,PD:PD,REQ:REQ,SHIP_TO:SHIP_TO,RBO:RBO,CS:CS,QTY:QTY,REMARK_1:REMARK_1,REMARK_2:REMARK_2,REMARK_3:REMARK_3,REMARK_4:REMARK_4,FORM_TYPE:FORM_TYPE,SAMPLE:SAMPLE,DATA_RECEIVED:DATA_RECEIVED,SO_LAN:SO_LAN}; 
				var jsonObjects = {
					"item": save_item,
					"material":save_material
				};  
				/*
				console.log(jsonObjects);
				return false;
				*/
				$.ajax({
				url: url_save,
					type: "POST",
					data: {data: JSON.stringify(jsonObjects) },
					dataType: "json",
					beforeSend: function(x) {
						if (x && x.overrideMimeType) {
							x.overrideMimeType("application/j-son;charset=UTF-8");
						}
					},
					success: function(result) {
						if(result.status){
							if(!print||(print&&!getCookie('auto_print')=='1')){
								alert("Save DATA Thành Công!!!");
							}
							location.reload();
							if(print){
								var wi = window.open('about:blank', '_blank');
								//$(wi.document.body).html("<p>Please wait while you are being redirected...</p>");
								wi.location.href = RootPath+'print.php?id='+result.NUMBER_NO;
							}                                
						}else{
							alert(result.mess);
						}
					}
				});
				
			}else{
				alert("Vui lòng chọn một SO-LINE để "+save+"!!!");
				return false;
			}
		}
    }	
	
    function initNO(){			
		// get first checked_SOLINE
		/// check			
		var idCheck = checked_SOLINE[0]['grid_id'];
		if(print_type=='paxar'){
			if(idCheck){	
				var order = SoGrid.cells(idCheck,9).getValue();
				var req = SoGrid.cells(idCheck,8).getValue();
				var pd = SoGrid.cells(idCheck,7).getValue();
				var ship_to = SoGrid.cells(idCheck,23).getValue(); // ship_to
				var rbo = SoGrid.cells(idCheck,6).getValue(); // ship_to
				var cs = SoGrid.cells(idCheck,24).getValue(); // ship_to
				//var qty = SoGrid.cells(idCheck,3).getValue(); // ship_to
				var total_qty = 0;
				checked_SOLINE.forEach(function(element) {
					var id = element.grid_id;
					var qty = SoGrid.cells(id,3).getValue();
					total_qty+=Number(qty);
				});				
				var remark_1 = '',remark_2 = '',remark_3 = '',remark_4 = '',remark_5 = '',remark_6 = '',remark_7 = '';
				remark_1 = SoGrid.cells(idCheck,26).getValue(); // ship_to
				var have_sample = 0;
				for(var index = 0;index<SoGrid.getRowsNum();index++){
					if(SoGrid.cellByIndex(index,0).getValue()==1){
						if(SoGrid.cellByIndex(index,28).getValue()){
							remark_3 = SoGrid.cellByIndex(index,28).getValue();
							break;
						}
					}
				}
				// check sample
				var sample_check = SoGrid.cellByIndex(0,33).getValue();
				if(sample_check==1){
					remark_2 = 'CO MAU'; // ship_to
					remark_3 = '';					
				}else{
					if(sample_check==0){
						remark_3 = SoGrid.cellByIndex(0,36).getValue();
					}
				}				
				// update sample				
				remark_4 = SoGrid.cells(idCheck,29).getValue(); // ship_to
				remark_5 = SoGrid.cells(idCheck,30).getValue(); // ship_to
				var ORDER_TYPE_NAME = SoGrid.cells(idCheck,35).getValue(); // ship_to
				//var multiple = SoGrid.cells(idCheck,31).getValue();
				//var multiple_data = SoGrid.cells(idCheck,32).getValue();			
				objForm = {order:order,req:req,pd:pd,ship_to:ship_to,rbo:rbo,cs:cs,qty:total_qty,remark_1:remark_1,remark_2:remark_2,remark_3:remark_3,remark_4:remark_4,remark_5:remark_5,remark_6:remark_6,remark_7:remark_7,ORDER_TYPE_NAME:ORDER_TYPE_NAME};		
				//jsonString = objForm.multiple_data;
				//objForm.multiple_data = JSON.parse(jsonString);
				//console.log(objForm);
				LayoutMain.cells("b").attachURL(RootDataPath+'frm_no.php',true,objForm);
				//LayoutMain.cells("b").cell.style.overflow = "hidden";
				// init grid to save	
				LayoutMain.attachEvent("onContentLoaded", function(id){					
					$("#frm_sample").val(sample_check);
					if(id=='b'){
						initGridPaxarToSave();
					}					
					// frm_no id loaded, your code here	
					// check auto print to print
					// auto print
					if(getCookie('auto_print')=='1'){
						saveDatabase(true);
					}
					$('#frm_sample').change(function() {
						var order_type = $(this).val();
						if(order_type==0){ // don mau
							$("#frm_remark_3").val(SoGrid.cellByIndex(0,36).getValue());							
						}else if(order_type==1){
							$("#frm_remark_2").val('CO MAU.');
							$("#frm_remark_3").val('');
						}else if(order_type==2){
							$("#frm_remark_2").val('');
							$("#frm_remark_3").val('');
						}
					});
					// set read only
					$("#frm_create_date").prop("readonly", true);
				});					
			}
		}else if(print_type=='trim'){
			if(idCheck){
				var sample_check = SoGrid.cellByIndex(0,24).getValue();				
				var order = SoGrid.cells(idCheck,9).getValue();
				var req = SoGrid.cells(idCheck,8).getValue();
				var pd = SoGrid.cells(idCheck,7).getValue();
				var ship_to = SoGrid.cells(idCheck,14).getValue(); // ship_to
				var rbo = SoGrid.cells(idCheck,6).getValue(); // ship_to
				var cs = SoGrid.cells(idCheck,15).getValue(); // ship_to
				//var qty = SoGrid.cells(idCheck,3).getValue(); // ship_to
				var total_qty = 0;
				checked_SOLINE.forEach(function(element) {
					var id = element.grid_id;
					var qty = SoGrid.cells(id,3).getValue();
					total_qty+=Number(qty);
				});	
				var remark_1 = '',remark_2 = '',remark_3 = '',remark_4 = '',remark_5 = '',remark_6 = '',remark_7 = '';
				remark_1 = SoGrid.cells(idCheck,17).getValue(); // remark_1
				remark_2 = SoGrid.cells(idCheck,18).getValue(); // ship_to
				remark_3 = SoGrid.cells(idCheck,19).getValue(); // ship_to
				remark_4 = SoGrid.cells(idCheck,20).getValue(); // ship_to
				remark_5 = SoGrid.cells(idCheck,21).getValue(); // ship_to		
				var ORDER_TYPE_NAME = SoGrid.cells(idCheck,28).getValue(); // ship_to
				objForm = {order:order,req:req,pd:pd,ship_to:ship_to,rbo:rbo,cs:cs,qty:total_qty,remark_1:remark_1,remark_2:remark_2,remark_3:remark_3,remark_4:remark_4,remark_5:remark_5,remark_6:remark_6,remark_7:remark_7,ORDER_TYPE_NAME:ORDER_TYPE_NAME};		
				//jsonString = objForm.multiple_data;
				//objForm.multiple_data = JSON.parse(jsonString);
				//console.log(objForm);
				LayoutMain.cells("b").attachURL(RootDataPath+'frm_no.php',true,objForm);
				//LayoutMain.cells("b").cell.style.overflow = "hidden";
				// init grid to save	
				LayoutMain.attachEvent("onContentLoaded", function(id){
					if(id=='b'){
						$("#frm_sample").val(sample_check);
						initGridTrimToSave();
						// check auto print to print
						// auto print
						if(getCookie('auto_print')=='1'){
							saveDatabase(true);
						}
					}
					// frm_no id loaded, your code here	
					// set read only
					$("#frm_create_date").prop("readonly", true);
				});
			}
		}else if(print_type=='sips'){
			if(idCheck){
				var sample_check = SoGrid.cellByIndex(0,24).getValue();				
				var order = SoGrid.cells(idCheck,9).getValue();
				var req = SoGrid.cells(idCheck,8).getValue();
				var pd = SoGrid.cells(idCheck,7).getValue();
				var ship_to = SoGrid.cells(idCheck,14).getValue(); // ship_to
				var rbo = SoGrid.cells(idCheck,6).getValue(); // ship_to
				var cs = SoGrid.cells(idCheck,15).getValue(); // ship_to
				//var qty = SoGrid.cells(idCheck,3).getValue(); // ship_to
				var total_qty = 0;
				checked_SOLINE.forEach(function(element) {
					var id = element.grid_id;
					var qty = SoGrid.cells(id,3).getValue();
					total_qty+=Number(qty);
				});	
				var remark_1 = '',remark_2 = '',remark_3 = '',remark_4 = '',remark_5 = '',remark_6 = '',remark_7 = '';
				if(sample_check==1){ // 
					remark_2 = 'CO MAU.'; // ship_to
				}else if(sample_check==0){
					remark_2 = 'Day la don hang MAU. Lấy 10 PCS/size.';
				}
				if(ship_to == "ELEGANCE INDUSTRIAL CO.,LTD-SHIP VIETNAM" ){
					remark_2+=" 3 pcs làm trim card, 7 pcs làm mẫu";					
				}
				remark_1 = SoGrid.cells(idCheck,17).getValue(); // remark_1
				remark_3 = SoGrid.cells(idCheck,19).getValue(); // ship_to
				remark_4 = SoGrid.cells(idCheck,20).getValue(); // ship_to					
				objForm = {order:order,req:req,pd:pd,ship_to:ship_to,rbo:rbo,cs:cs,qty:total_qty,remark_1:remark_1,remark_2:remark_2,remark_3:remark_3,remark_4:remark_4,remark_5:remark_5,remark_6:remark_6,remark_7:remark_7,ORDER_TYPE_NAME:ORDER_TYPE_NAME};		
				//jsonString = objForm.multiple_data;
				//objForm.multiple_data = JSON.parse(jsonString);
				//console.log(objForm);
				LayoutMain.cells("b").attachURL(RootDataPath+'frm_no.php',true,objForm);
				//LayoutMain.cells("b").cell.style.overflow = "hidden";
				// init grid to save	
				LayoutMain.attachEvent("onContentLoaded", function(id){
					if(id=='b'){
						$("#frm_sample").val(sample_check);
						initGridSipsToSave();
						// check auto print to print
						// auto print
						if(getCookie('auto_print')=='1'){
							saveDatabase(true);
						}
						$('#frm_sample').change(function() {
							remark_2 = '';
							var order_type = $(this).val();
							if(order_type==0){ // don mau
								$("#frm_remark_2").val('Day la don hang MAU. Lấy 10 PCS/size.');								
							}else if(order_type==1){
								$("#frm_remark_2").val('CO MAU.');
							}else if(order_type==2){
								$("#frm_remark_2").val('');
							}
							if(ship_to == "ELEGANCE INDUSTRIAL CO.,LTD-SHIP VIETNAM" ){
								remark_2 = $("#frm_remark_2").val();
								$("#frm_remark_2").val(remark_2+" 3 pcs làm trim card, 7 pcs làm mẫu");					
							}
						});
						// set read only
						$("#frm_create_date").prop("readonly", true);
					}
					// frm_no id loaded, your code here						
				});
			}
		}		
    }
	
	
	var gridPaxarSave;
	function initGridPaxarToSave(){
		if(SoGrid.cellByIndex(0,37).getValue()=='1'){ // color by size
			gridPaxarSave = LayoutMain.cells("c").attachGrid();
			gridPaxarSave.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
			if(SoGrid.cellByIndex(0,6).getValue().indexOf("NIKE") !==-1){ // color by size			
				gridPaxarSave.setHeader("SO#,ITEM,INTERNAL ITEM,RBO,COLOR,QTY,MATERIAL CODE,MATERIAL DES,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DES,QTY,MULTIPLE,SAMPLE,SO UPS");   //sets the headers of columns
			}else{
				gridPaxarSave.setHeader("SO#,ITEM,INTERNAL ITEM,RBO,ITEM DES,QTY,MATERIAL CODE,MATERIAL DES,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DES,QTY,MULTIPLE,SAMPLE,SO UPS");   //sets the headers of columns
			}
			//gridPaxarSave.setColumnIds("SO#,ITEM,INTERNAL ITEM,RBO,ITEM DES,QTY,MATERIAL CODE,MATERIAL DES,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DES,QTY,MULTIPLE,SAMPLE,SO UPS");         //sets the columns' ids
			gridPaxarSave.setInitWidths("80,70,115,60,80,50,110,105,60,40,50,70,60,100,60,50,70,65,60");   //sets the initial widths of columns
			gridPaxarSave.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
			gridPaxarSave.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
			gridPaxarSave.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
			gridPaxarSave.init();
			// load data from list SO		
			if(SoGrid.getRowsNum()>0){
				// forEach
				for(var index = 0;index<SoGrid.getRowsNum();index++){
					if(SoGrid.cellByIndex(index,0).getValue()==1){
						if(MULTIPLE!="0"){
							var MULTIPLE_DATA = SoGrid.cellByIndex(index,32).getValue();
							if(MULTIPLE_DATA){
								MULTIPLE_DATA = JSON.parse(MULTIPLE_DATA);
							}
							if(MULTIPLE_DATA.length){
								for(var k=0;k<MULTIPLE_DATA.length;k++){
									QTY = 0;
									MATERIAL_CODE = '';
									MATERIAL_DES = '';
									EA_SHT = 0;
									YD = 0;
									MT = 0;
									INK_CODE = '';
									INK_DES = '';
									INK_QTY = '';
									var uniqueID = gridPaxarSave.uid();
									var SO = SoGrid.cellByIndex(index,1).getValue();
									SO = SO.trim();
									var LINE = SoGrid.cellByIndex(index,2).getValue();
									LINE = LINE.trim();
									var SO_LINE = SO+"-"+LINE;
									var ITEM = SoGrid.cellByIndex(index,4).getValue();
									var INTERNAL_ITEM = SoGrid.cellByIndex(index,5).getValue();
									var RBO = SoGrid.cellByIndex(index,6).getValue();
									var ITEM_DES = MULTIPLE_DATA[k]['COLOR'];
									var QTY = MULTIPLE_DATA[k]['SIZE_QTY'];
									var MATERIAL_CODE = MULTIPLE_DATA[k]['MATERIAL_CODE'];
									var MATERIAL_DES = MULTIPLE_DATA[k]['MATERIAL_DES'];
									var EA_SHT = MULTIPLE_DATA[k]['EA_SHT'];
									var YD = MULTIPLE_DATA[k]['YD'];
									var MT = MULTIPLE_DATA[k]['MT'];
									var LENGTH = SoGrid.cellByIndex(index,16).getValue();
									var WIDTH = SoGrid.cellByIndex(index,17).getValue();
									var INK_CODE = SoGrid.cellByIndex(index,18).getValue();
									var INK_DES = SoGrid.cellByIndex(index,19).getValue();
									var INK_QTY = MULTIPLE_DATA[k]['INK_QTY'];
									var MULTIPLE = SoGrid.cellByIndex(index,31).getValue();
									var SAMPLE = SoGrid.cellByIndex(index,33).getValue();	
									var SO_UPS = SoGrid.cellByIndex(index,34).getValue();				
									var data_add = [SO_LINE,ITEM,INTERNAL_ITEM,RBO,ITEM_DES,QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,MULTIPLE,SAMPLE,SO_UPS];
									// console.log(data_add);
									gridPaxarSave.addRow(uniqueID,data_add);
								}								
							}			
						}
					}
				}			
			}
		}else{
			gridPaxarSave = LayoutMain.cells("c").attachGrid();
			gridPaxarSave.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
			gridPaxarSave.setHeader("SO#,ITEM,INTERNAL ITEM,RBO,ITEM DES,QTY,MATERIAL CODE,MATERIAL DES,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DES,QTY,MULTIPLE,SAMPLE,SO UPS");   //sets the headers of columns
			gridPaxarSave.setColumnIds("SO#,ITEM,INTERNAL ITEM,RBO,ITEM DES,QTY,MATERIAL CODE,MATERIAL DES,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DES,QTY,MULTIPLE,SAMPLE,SO UPS");         //sets the columns' ids
			gridPaxarSave.setInitWidths("80,70,115,60,80,50,110,105,60,40,50,70,60,100,60,50,70,65,60");   //sets the initial widths of columns
			gridPaxarSave.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
			gridPaxarSave.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
			gridPaxarSave.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
			gridPaxarSave.init();
			// load data from list SO		
			if(SoGrid.getRowsNum()>0){
				// forEach
				for(var index = 0;index<SoGrid.getRowsNum();index++){
					if(SoGrid.cellByIndex(index,0).getValue()==1){				
						var uniqueID = gridPaxarSave.uid();
						var SO = SoGrid.cellByIndex(index,1).getValue();
						SO = SO.trim();
						var LINE = SoGrid.cellByIndex(index,2).getValue();
						LINE = LINE.trim();
						var SO_LINE = SO+"-"+LINE;
						var ITEM = SoGrid.cellByIndex(index,4).getValue();
						var INTERNAL_ITEM = SoGrid.cellByIndex(index,5).getValue();
						var RBO = SoGrid.cellByIndex(index,6).getValue();
						var ITEM_DES = SoGrid.cellByIndex(index,10).getValue();
						var QTY = SoGrid.cellByIndex(index,3).getValue();
						var MATERIAL_CODE = SoGrid.cellByIndex(index,11).getValue();
						var MATERIAL_DES = SoGrid.cellByIndex(index,12).getValue();
						var EA_SHT = SoGrid.cellByIndex(index,13).getValue();
						var YD = SoGrid.cellByIndex(index,14).getValue();
						var MT = SoGrid.cellByIndex(index,15).getValue();
						var LENGTH = SoGrid.cellByIndex(index,16).getValue();
						var WIDTH = SoGrid.cellByIndex(index,17).getValue();
						var INK_CODE = SoGrid.cellByIndex(index,18).getValue();
						var INK_DES = SoGrid.cellByIndex(index,19).getValue();
						var INK_QTY = SoGrid.cellByIndex(index,20).getValue();
						var MULTIPLE = SoGrid.cellByIndex(index,31).getValue();
						var SAMPLE = SoGrid.cellByIndex(index,33).getValue();	
						var SO_UPS = SoGrid.cellByIndex(index,34).getValue();				
						var data_add = [SO_LINE,ITEM,INTERNAL_ITEM,RBO,ITEM_DES,QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,MULTIPLE,SAMPLE,SO_UPS];
						// console.log(data_add);
						gridPaxarSave.addRow(uniqueID,data_add);				
						// CHECK TO ADD MULTIPE
						if(MULTIPLE!="0"){
							var MULTIPLE_DATA = SoGrid.cellByIndex(index,32).getValue();
							if(MULTIPLE_DATA){
								MULTIPLE_DATA = JSON.parse(MULTIPLE_DATA);
							}					
							QTY = 0;
							MATERIAL_CODE = '';
							MATERIAL_DES = '';
							EA_SHT = 0;
							YD = 0;
							MT = 0;
							INK_CODE = '';
							INK_DES = '';
							INK_QTY = '';
							if(MULTIPLE_DATA){
								MATERIAL_CODE = MULTIPLE_DATA.MATERIAL_CODE;
								MATERIAL_DES = MULTIPLE_DATA.MATERIAL_DES;
								EA_SHT = MULTIPLE_DATA.EA_SHT;
								YD = MULTIPLE_DATA.YD;
								MT = MULTIPLE_DATA.MT;
								INK_CODE = MULTIPLE_DATA.INK_CODE;
								INK_DES = MULTIPLE_DATA.INK_DES;
								INK_QTY = MULTIPLE_DATA.INK_QTY;
							}
							var data_add_multiple = [SO_LINE,ITEM,INTERNAL_ITEM,RBO,ITEM_DES,QTY,MATERIAL_CODE,MATERIAL_DES,EA_SHT,YD,MT,LENGTH,WIDTH,INK_CODE,INK_DES,INK_QTY,0,SAMPLE,SO_UPS];
							var uniqueID = gridPaxarSave.uid();
							gridPaxarSave.addRow(uniqueID,data_add_multiple);
						}
					}
				}			
			}
		}			
	}
	
	var gridTrimSave;
	function initGridTrimToSave(){
		gridTrimSave = LayoutMain.cells("c").attachGrid();
		gridTrimSave.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
		gridTrimSave.setHeader("SO#,ITEM,INTERNAL ITEM,QTY,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,MULTIPLE,SAMPLE,LENGTH,WIDTH,SO_UPS");   //sets the headers of columns
        gridTrimSave.setColumnIds("SO#,ITEM,INTERNAL ITEM,QTY,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,MULTIPLE,SAMPLE,LENGTH,WIDTH,SO_UPS");         //sets the columns' ids
        gridTrimSave.setInitWidths("80,110,120,70,130,120,120,90,100,75,70,60,70");   //sets the initial widths of columns
        gridTrimSave.setColAlign("left,left,left,left,left,left,left,lef,left,left,left,left,lef");     //sets the alignment of columns
        gridTrimSave.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
        gridTrimSave.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na");
        gridTrimSave.init();
		// load data from list SO
		var index = 0;
		if(SoGrid.getRowsNum()>0){
			// forEach
			for(var index = 0;index<SoGrid.getRowsNum();index++){
				if(SoGrid.cellByIndex(index,0).getValue()==1){
					var uniqueID = gridTrimSave.uid();
					var SO = SoGrid.cellByIndex(index,1).getValue();
					SO = SO.trim();
					var LINE = SoGrid.cellByIndex(index,2).getValue();
					LINE = LINE.trim();
					var SO_LINE = SO+"-"+LINE;
					var ITEM = SoGrid.cellByIndex(index,4).getValue();
					var INTERNAL_ITEM = SoGrid.cellByIndex(index,5).getValue();				
					var QTY = SoGrid.cellByIndex(index,3).getValue();
					var MATERIAL_CODE = SoGrid.cellByIndex(index,10).getValue();
					var MATERIAL_QTY = SoGrid.cellByIndex(index,11).getValue();
					var INK_CODE = SoGrid.cellByIndex(index,12).getValue();
					var INK_QTY = SoGrid.cellByIndex(index,13).getValue();
					var MULTIPLE = SoGrid.cellByIndex(index,22).getValue();
					var SAMPLE = SoGrid.cellByIndex(index,24).getValue();	
					var LENGTH = SoGrid.cellByIndex(index,25).getValue();
					var WIDTH = SoGrid.cellByIndex(index,26).getValue();
					var SO_UPS = SoGrid.cellByIndex(index,27).getValue();
					var data_add = [SO_LINE,ITEM,INTERNAL_ITEM,QTY,MATERIAL_CODE,MATERIAL_QTY,INK_CODE,INK_QTY,MULTIPLE,SAMPLE,LENGTH,WIDTH,SO_UPS];
					// console.log(data_add);
					gridTrimSave.addRow(uniqueID,data_add);				
					// CHECK TO ADD MULTIPE
					if(MULTIPLE!="0"){
						var MULTIPLE_DATA = SoGrid.cellByIndex(index,23).getValue();
						if(MULTIPLE_DATA){
							MULTIPLE_DATA = JSON.parse(MULTIPLE_DATA);
						}					
						QTY = 0;
						INK_CODE = '';
						MATERIAL_QTY = 0;
						if(MULTIPLE_DATA){
							INK_CODE = MULTIPLE_DATA.INK_CODE;
						}
						var data_add_multiple = [SO_LINE,ITEM,INTERNAL_ITEM,QTY,MATERIAL_CODE,MATERIAL_QTY,INK_CODE,INK_QTY,0,SAMPLE,LENGTH,WIDTH,SO_UPS];
						var uniqueID = gridTrimSave.uid();
						gridTrimSave.addRow(uniqueID,data_add_multiple);
					}
				}
			}		
		}	
	}
	
	var gridSipsSave;
	function initGridSipsToSave(){
		gridSipsSave = LayoutMain.cells("c").attachGrid();
		gridSipsSave.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
		gridSipsSave.setHeader("SO#,ITEM,INTERNAL ITEM,QTY,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,MULTIPLE,SAMPLE,LENGTH,WIDTH,SO_UPS");   //sets the headers of columns
        gridSipsSave.setColumnIds("SO#,ITEM,INTERNAL ITEM,QTY,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,MULTIPLE,SAMPLE,LENGTH,WIDTH,SO_UPS");         //sets the columns' ids
        gridSipsSave.setInitWidths("80,110,120,70,130,120,120,90,100,75,70,60,70");   //sets the initial widths of columns
        gridSipsSave.setColAlign("left,left,left,left,left,left,left,lef,left,left,left,left,lef");     //sets the alignment of columns
        gridSipsSave.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
        gridSipsSave.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na");
        gridSipsSave.init();
		// load data from list SO
		var index = 0;
		if(SoGrid.getRowsNum()>0){
			// forEach
			for(var index = 0;index<SoGrid.getRowsNum();index++){
				if(SoGrid.cellByIndex(index,0).getValue()==1){
					var uniqueID = gridSipsSave.uid();
					var SO = SoGrid.cellByIndex(index,1).getValue();
					SO = SO.trim();
					var LINE = SoGrid.cellByIndex(index,2).getValue();
					LINE = LINE.trim();
					var SO_LINE = SO+"-"+LINE;
					var ITEM = SoGrid.cellByIndex(index,4).getValue();
					var INTERNAL_ITEM = SoGrid.cellByIndex(index,5).getValue();				
					var QTY = SoGrid.cellByIndex(index,3).getValue();
					var MATERIAL_CODE = SoGrid.cellByIndex(index,10).getValue();
					var MATERIAL_QTY = SoGrid.cellByIndex(index,11).getValue();
					var INK_CODE = SoGrid.cellByIndex(index,12).getValue();
					var INK_QTY = SoGrid.cellByIndex(index,13).getValue();
					var MULTIPLE = SoGrid.cellByIndex(index,22).getValue();
					var SAMPLE = SoGrid.cellByIndex(index,24).getValue();	
					var LENGTH = SoGrid.cellByIndex(index,25).getValue();
					var WIDTH = SoGrid.cellByIndex(index,26).getValue();
					var SO_UPS = SoGrid.cellByIndex(index,27).getValue();
					var data_add = [SO_LINE,ITEM,INTERNAL_ITEM,QTY,MATERIAL_CODE,MATERIAL_QTY,INK_CODE,INK_QTY,MULTIPLE,SAMPLE,LENGTH,WIDTH,SO_UPS];
					// console.log(data_add);
					gridSipsSave.addRow(uniqueID,data_add);				
					// CHECK TO ADD MULTIPE
					if(MULTIPLE!="0"){
						var MULTIPLE_DATA = SoGrid.cellByIndex(index,23).getValue();
						if(MULTIPLE_DATA){
							MULTIPLE_DATA = JSON.parse(MULTIPLE_DATA);
						}					
						QTY = 0;
						INK_CODE = '';
						MATERIAL_QTY = 0;
						if(MULTIPLE_DATA){
							INK_CODE = MULTIPLE_DATA.INK_CODE;
						}
						var data_add_multiple = [SO_LINE,ITEM,INTERNAL_ITEM,QTY,MATERIAL_CODE,MATERIAL_QTY,INK_CODE,INK_QTY,0,SAMPLE,LENGTH,WIDTH,SO_UPS];
						var uniqueID = gridSipsSave.uid();
						gridSipsSave.addRow(uniqueID,data_add_multiple);
					}
				}
			}		
		}	
	}
	
    function initSoGrid(){
        SoGrid = LayoutMain.cells("a").attachGrid();
        SoGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
		// check init 
		if(print_type == 'paxar'){
			SoGrid.setHeader(",SO,LINE,QTY,ITEM,INTERNAL ITEM,RBO,PD,REQ,ORDER,ITEM DESCRIPTION,MATERIAL CODE,MATERIAL DESCRIPTION,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DESCRIPTION,INK QTY,UNIT,UPS,SHIP_TO,CS,ITEM,REMARK 1,REMARK 2,REMARK 3,REMARK 4,REMARK 5,MULTIPLE,DATA,SAMPLE,SO UPS,ORDER TYPE NAME,PACKING INSTR,COLOR BY SIZE");   //sets the headers of columns
			// SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
			SoGrid.setColumnIds(",SO,LINE,QTY,ITEM,INTERNAL ITEM,RBO,PD,REQ,ORDER,ITEM DESCRIPTION,MATERIAL CODE,MATERIAL DESCRIPTION,EA-SHT,YD,MT,LENGTH,WIDTH,INK CODE,INK DESCRIPTION,INK QTY,UNIT,UPS,SHIP_TO,CS,ITEM,REMARK 1,REMARK 2,REMARK 3,REMARK 4,REMARK 5,MULTIPLE,DATA,SAMPLE,SO UPS,ORDER TYPE NAM,PACKING INSTR,COLOR BY SIZE");         //sets the columns' ids
			SoGrid.setInitWidths("30,65,45,50,105,110,195,90,80,90,180,130,280,120,50,50,70,70,75,130,120,70,70,325,145,145,145,145,145,145,145,70,145,70,80,150,150,150");   //sets the initial widths of columns
			SoGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
			SoGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
			SoGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na"); 
		}else if(print_type == 'trim'){
			SoGrid.setHeader(",SO,LINE,QTY,ITEM,INTERNAL ITEM,RBO,PD,REQ,ORDER,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,SHIP_TO,CS,ITEM,REMARK 1,REMARK 2,REMARK 3,REMARK 4,REMARK 5,MULTIPLE,DATA,SAMPLE,LENGTH,WIDTH,SO UPS,ORDER TYPE NAME,PACKING INSTR");   //sets the headers of columns
			// SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
			SoGrid.setColumnIds(",SO,LINE,QTY,ITEM,INTERNAL ITEM,RBO,PD,REQ,ORDER,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,SHIP_TO,CS,ITEM,REMARK 1,REMARK 2,REMARK 3,REMARK 4,REMARK 5,MULTIPLE,DATA,SAMPLE,LENGTH,WIDTH,SO UPS,ORDER TYPE NAME,PACKING INSTR");         //sets the columns' ids
			SoGrid.setInitWidths("30,65,45,50,105,110,90,80,114,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,80,150,150");   //sets the initial widths of columns
			SoGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
			SoGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
			SoGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
		}else{
			SoGrid.setHeader(",SO,LINE,QTY,ITEM,INTERNAL ITEM,RBO,PD,REQ,ORDER,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,SHIP_TO,CS,ITEM,REMARK 1,REMARK 2,REMARK 3,REMARK 4,REMARK 5,MULTIPLE,DATA,SAMPLE,LENGTH,WIDTH,SO UPS,ORDER TYPE NAME,PACKING INSTR");   //sets the headers of columns
			// SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
			SoGrid.setColumnIds(",SO,LINE,QTY,ITEM,INTERNAL ITEM,RBO,PD,REQ,ORDER,MATERIAL CODE,MATERIAL QTY,INK CODE,INK QTY,SHIP_TO,CS,ITEM,REMARK 1,REMARK 2,REMARK 3,REMARK 4,REMARK 5,MULTIPLE,DATA,SAMPLE,LENGTH,WIDTH,SO UPS,ORDER TYPE NAME,PACKING INSTR");         //sets the columns' ids
			SoGrid.setInitWidths("30,65,45,50,105,110,90,80,114,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,130,80,150,150");   //sets the initial widths of columns
			SoGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
			SoGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
			SoGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");
		}
        //sets the sorting types of columns
        //SoGrid.enableSmartRendering(true);
        SoGrid.init();
		/*
        var columnNum = SoGrid.getColumnsNum();        
        for(var j=8;j<columnNum;j++){
            SoGrid.setColumnHidden(j,true);
        }         
		*/
		/*
        SoGrid.load(SoGridLoad, function(){ //takes the path to your data feed      
			LayoutMain.cells("a").progressOff();
        });  
		*/
        SoGrid.attachEvent("onRowSelect", function(id,ind){ // Fire When user click on row in grid            
            console.log(id);
			return false;
        });     
        SoGrid.attachEvent("onCheck", function(rId,cInd,state){// fires after the state of a checkbox has been changed     
            processCheckSo(rId,cInd,state);
        });       
    }

    function delete_no(no){
		confirm_delete = confirm("Bạn có muốn XÓA "+no);
		if(confirm_delete){
			var url_delete = RootDataPath+'delete_no.php';
			$.ajax({
			url: url_delete,
				type: "POST",
				data: {data: no},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){						
						// reload
						location.reload();
						/*
						viewNOGrid.forEachRow(function(id){							
							if(viewNOGrid.cells(id,0).getValue()===no){
								viewNOGrid.deleteRow(id);
							}
						});
						*/
					}else{
						alert('Có Lỗi trong quá trình XÓA '+no);
					}
				}
			});
		}else{
			
		}
	}

    function initMaterialGrid(){
        MaterialGrid = LayoutMain.cells("c").attachGrid();
        MaterialGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
        MaterialGrid.setHeader("MATERIAL,DESCRIPTION,QTY");   //sets the headers of columns
        // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
        MaterialGrid.setColumnIds("MATERIAL,DESCRIPTION,QTY");         //sets the columns' ids
        MaterialGrid.setInitWidths("130,*,80");   //sets the initial widths of columns
        MaterialGrid.setColAlign("left,left,left");     //sets the alignment of columns
        MaterialGrid.setColTypes("ed,ed,ed");    //sets the types of columns
        MaterialGrid.setColSorting("str,str,str");  //sets the sorting types of columns
        //MaterialGrid.enableSmartRendering(true);
        MaterialGrid.init();
        MaterialGrid.attachEvent("onRowSelect", function(id,ind){ // Fire When user click on row in grid            
            console.log(id);
        });   
    }
	
	function loadMaterial(){
		var item = '';
        if(checkID){
            item = SoGrid.cells(checkID,5).getValue();
        }     
        MaterialGridLoadTmp = MaterialGridLoad;   
        MaterialGridLoad = RootDataPath+'grid_material.php?item='+item;
        MaterialGrid.load(MaterialGridLoad, function(){ //takes the path to your data feed  
            updateMaterial(); 
            // auto sorting following qty  
            MaterialGrid.sortRows(2,"str", "desc"); // sorts grid
            MaterialGrid.setSortImgState(true, 2, "desc"); // sets icon to sort arrow
            // init Form              
        });      
        MaterialGridLoad = MaterialGridLoadTmp;
	}
	
	
    var total_qty_material = 0;
    function updateMaterial(){        
        var size_check = SizeGrid.cellByIndex(0,0).getValue();
        var material_display = [];
        if(size_check){
            MaterialGrid.forEachRow(function(idMaterial){
                var material_code = MaterialGrid.cells(idMaterial,0).getValue();                  
                //material_code = material_code.trim();         
                SizeGrid.forEachRow(function(id){
                    var size_check_tmp = SizeGrid.cells(id,0).getValue();
                    if(size_check_tmp){
                        var base_roll = SizeGrid.cells(id,2).getValue();    
                        //console.log('material_code:'+material_code);
                        //console.log('base_roll:'+base_roll);
                        //base_roll = base_roll.trim(); 
                        if(base_roll==material_code){
                            //console.log('update');
                            var qty = SizeGrid.cells(id,3).getValue(); 
                            if(!qty){
                                qty = 0;
                            }
                            var qty_material = Number(qty*1.014);   
                            total_qty_material+=qty_material;     
                            qty_material_round = Math.round(qty_material);               
                            MaterialGrid.cells(idMaterial,2).setValue(qty_material_round);
                            material_display.push(idMaterial);
                        }
                    }                        
                });
            });
            // delete 
            MaterialGrid.forEachRow(function(idMaterial){
                var material_qty = MaterialGrid.cells(idMaterial,2).getValue();
                if(!material_qty){
                    MaterialGrid.deleteRow(idMaterial)
                }
            });
        }    
        if(total_qty_material){            
            total_qty_material_round = Math.round(total_qty_material);
        }else{
            total_qty_material_round = 0;
        }
        // update d126
        var frm_width = SoGrid.cells(checkID,13).getValue();
        if(!frm_width){
            frm_width = 0;
        }
        var frm_height = SoGrid.cells(checkID,14).getValue();
        var frm_gap = SoGrid.cells(checkID,15).getValue();
        if(!frm_gap){
            frm_gap = 0;
        }
        frm_width = Number(frm_width);
        frm_gap = Number(frm_gap);
        var frm_d126 = total_qty_material_round*(frm_width+frm_gap)/1000;         
        frm_d126_round = Math.round(frm_d126);
        // update to form
        $("#frm_d126").val(frm_d126_round);
        // add 9 row
        countSize = MaterialGrid.getRowsNum();
        var rowID = countSize;
        for(var h=1;h<=8;h++){ // 20 SO
            //add row  
            tmp_array = ['','',''];
            MaterialGrid.addRow(rowID+h+300,tmp_array);
        }
    }
	var itemMain,poMain,rboMain;
    function processCheckSo(rId,cInd,state){ 
        if(state){
			var so_line,grid_id;
			var so = SoGrid.cells(rId,1).getValue();
			so = so.trim();
			var line = SoGrid.cells(rId,2).getValue();
			so_line = so.trim()+"-"+line.trim();
			grid_id = rId;
			var obj = {so_line:so_line,grid_id:grid_id};
			checked_SOLINE.push(obj);
        }else{
			var indexSO = 0;
			checked_SOLINE.forEach(function(element) {
				if(element.grid_id===rId){
					checked_SOLINE.splice(indexSO, 1);
				}
				indexSO++;
			});
        }
        if(checked_SOLINE.length){                
            initNO();     
        }else{
            // reset 
           resetSO();
        }
    }

    function resetSO(){
		// reset form NO
        LayoutMain.cells("b").detachObject(); // reset
		if(print_type=='paxar'){
			gridPaxarSave.clearAll();
		}else if(print_type=='trim'){
			gridTrimSave.clearAll();
		}
		else if(print_type=='sips'){
			gridSave.clearAll();
		}
    }
	
	var input_so='';	
	function loadGridSO(SO_LINE){		
		var dataSo={rows:[]};
		// call ajax
		var url_load_grid = SoGridLoad;
		$.ajax({
			url: url_load_grid,
			async: false,
			type: "POST",
			data: {data: [SO_LINE]},
			dataType: "json",
			beforeSend: function(x) {				
				if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");					
				}
			},
			success: function(result){
				if(result.status){
					var length = result.data.length;
					for(var i = 0;i<length;i++){
						dataSo.rows.push(result.data[i]);
					}
					SoGrid.parse(dataSo,"json");
					LayoutMain.cells("a").progressOff();
				}else{		
					LayoutMain.cells("a").progressOff();
					alert(result.mess);
					location.reload();
				}				
			}
		});
	}
	
    function filterSO(){
        input_so = ToolbarMain.getInput("so");
        input_so.focus(); // set focus
        input_so.value=""; // set default  28088982-1 28088982-1 28140957-1: PAXAR - FR 
        input_so.onkeypress = function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){ // enter on input text				
				if(!updateDB){
					alert('Vui lòng đăng nhập vào hệ thống!');
					return false;
				}
				LayoutMain.cells("a").progressOn();
				checked_SOLINE = []; // reset checked_SOLINE when filter
                var text = $(this).val();  
                text = text.trim();        
                SoGrid.clearAll(); //remove all data
                //save query string in global variable (see step 5 for details)				
				loadGridSO(text);				
                var index = 0;		
				for (var i=0; i<SoGrid.getRowsNum(); i++){						
					if(print_type=='paxar'){
						var internal_item = SoGrid.cellByIndex(index,25).getValue(); // trim max = 23
						var length = SoGrid.cellByIndex(index,16).getValue(); // trim max = 23
						var unit = SoGrid.cellByIndex(index,21).getValue(); // trim max = 23
						var ups = SoGrid.cellByIndex(index,22).getValue(); // trim max = 23
						if(!length||length<1||isNaN(length)!=false){
							alert("CHIỀU DÀI KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT!");
							location.reload();
							return false;
						}else if(!unit){
							alert("MATERIAL UOM KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT!");
							location.reload();
							return;
						}else if(!ups||ups<1){
							alert("SỐ UPS KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT!");
							location.reload();
							return false;
						}
					}else if(print_type=='trim'){
						var internal_item = SoGrid.cellByIndex(index,16).getValue(); // trim max = 23
						var length = SoGrid.cellByIndex(index,25).getValue(); // trim max = 23
						if(!length||length<1||isNaN(length)!=false){
							alert("CHIỀU DÀI KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT!");
							location.reload();
							return false;
						}
					}
					else if(print_type=='sips'){
						var internal_item = SoGrid.cellByIndex(index,16).getValue(); // trim max = 23	
						var length = SoGrid.cellByIndex(index,25).getValue(); // trim max = 23
						var ups = SoGrid.cellByIndex(index,27).getValue(); // trim max = 23
						if(!length||length<1||isNaN(length)!=false){
							alert("CHIỀU DÀI KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT!");
							location.reload();
							return false;
						}else if(!ups||ups<1){
							alert("SỐ UPS KHÔNG TỒN TẠI, VUI LÒNG CẬP NHẬT!");
							location.reload();
							return false;
						}
					}						
					internal_item = internal_item.trim();	
					internal_item_so = SoGrid.cellByIndex(index,5).getValue();		
					internal_item_so = internal_item_so.trim();
					if(!internal_item){
						alert("SO-LINE không tồn tại INTERNAL ITEM: "+internal_item_so+" trên hệ thống, vui lòng cập nhật");
						//location.reload();
						return false;														
					}else{
						// add 
						var so_line,grid_id;
						var so = SoGrid.cellByIndex(index,1).getValue();
						so = so.trim();
						if(so){
							var line = SoGrid.cellByIndex(index,2).getValue();
							so_line = so.trim()+"-"+line.trim();
							grid_id = SoGrid.getRowId(index);
							var obj = {so_line:so_line,grid_id:grid_id};
							checked_SOLINE.push(obj);
							//SoGrid.cellByIndex(index,0).setValue(1);
						}							
					}		
					index++;
				};	
				if(checked_SOLINE.length){                
					initNO();     
				} 				
                event.stopPropagation();
            }        
        }
    }
	
	String.prototype.capitalize = function() {
		return this.charAt(0).toUpperCase() + this.slice(1);
	}
	
    $(document).ready(function(){		
        initLayout();
        initMenu();
        initToolbar();
		/*
		if(print_type==-1){
			alert("Vui lòng chọn kiểu in: PAXAR, TRIM, SIPS");
			return false;
		}
		*/
        initSoGrid();
        filterSO();		
    });    
</script>
</head>
<body>
    <div style="height: 30px;background:#205670;font-weight:bold">
		<div id="menuObj"></div>
    </div>
    <div style="position:absolute;width:100%;top:35;background:white">
		<div id="ToolbarBottom" ></div>
    </div>
</body>
</html>