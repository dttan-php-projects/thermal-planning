<?php
	if(strpos($SHIP_TO,"WORLDON")===FALSE){
		$array_to_check = [
			//danh theo mail 20191225
			'SAIGON JIM', 
			'CHI HUNG',
			'HWA SEUNG',
			'Pouyuen',
			'SAMHO',
			'Lac Ty',
			'SHYANG HUNG CHENG',
			'APACHE',
			'TY XUAN',
			'JIA HSIN'

			// // 'SAIGON JIM', //@tandoan: fix theo mail
			// // 'CHI HUNG CO., LTD',
			// // 'HWA SEUNG VINA CO LTD/NHON TRACH 1 IND.Z',
			// // 'Pouyuen_Khu CT1',
			// // 'Pouyuen_Khu DS1',
			// // 'Pouyuen_Khu DS1_W',
			// // 'Pouyuen_Khu K',
			// // 'Pouyuen_Nghiep Vu khu C',
			// // 'Pouyuen_Tu Tai khu C',
			// // 'POUYUEN-DT1',
			// // 'CONG TY TNHH VIET NAM SAMHO',
			// // 'Cong ty TNHH Lac Ty II',
			// // 'CONG TY TNHH LAC TY/3-5 TEN LUA ST',
			// // 'CONG TY TNHH SHYANG HUNG CHENG',
			// // 'CONG TY TNHH GIAY APACHE VIET NAM',
			// // 'CONG TY TNHH TY XUAN/KHU CONG NGHIEP HOA',
			// // 'JIA HSIN CO.,LTD'

			// // 'POUYUEN-C',
			// // 'POUYUEN-Khu K',
			// // 'POUYUEN-DS1',
			// // 'HWA SEUNG VINA',
			// // 'CHI HUNG',
			// // 'JIA HSIN',
			// // 'TY XUAN',
			// // 'SHYANG HUNG CHENG',
			// // 'SAMHO',
			// // 'APACHE',
			// // 'SAIGON JIM',
			// // 'CI BAO',
			// // 'LAC TY',
			// // 'Lac Ty II',///tram yeu cau them vao tu vi tri nay
			// // 'PT. Parkland World Indonesia Jepara',
			// // 'ALL WELLS INTERNATIONAL CO.,LTD.',
			// // 'CAN SPORT SHOES  CO.LTD',
			// // 'CI BAO CO.,LTD',
			// // 'CONG TY GIAY UY VIET',
			// // 'CONG TY LIEN DOANH CHI HUNG',
			// // 'CONG TY TNHH GIAY WAN BANG VIETNAM',
			// // 'CONG TY TNHH POWERKNIT VIET NAM',
			// // 'HWA SEUNG VINA CO., LTD',
			// // 'I-CHENG(CAMBODIA) CORPORATION',
			// // 'JIA HSIN CO.,LTD',
			// // 'LAC TY COMPANY LTD',
			// // 'LACTY II Co, Ltd',
			// // 'MENG DA FOOTWEAR CO. LTD',
			// // 'Myanmar Pou Chen Company Limited',
			// // 'POUYUEN VIETNAM COMPANY LIMITED',
			// // 'Pouyuen_Khu DS1_W',
			// // 'POUYUEN-DT1',
			// // 'Pt Bintang Indokarya Gemilang',
			// // 'PT. GLOSTAR INDONESIA (GSI)',
			// // 'PT. GLOSTAR INDONESIA 2',
			// // 'PT. HWA SEUNG INDONESIA',
			// // 'PT. PANARUB INDUSTRY',
			// // 'PT. Parkland World Indonesia',
			// // 'SAIGON JIM BROTHER\'S CORP',
			// // 'SHYANG HUNG CHENG IND,CO.,LTD',
			// // 'Shyang Peng Cheng Co., Ltd',
			// // 'TSANG YIH COMPANY LIMITED',
			// // 'VIET NAM CHUNG JYE SHOES MANUFACTURE CO., LTD',
			// // 'VIETNAM SAMHO CO LTD',
			// // 'VIETNAM SHOE MAJESTY CO LTD',
			// // 'VINH LONG FOOTWEAR CO.,LTD',
			// // 'POUYUEN-C_Tu Tai',
			// // 'POUYUEN-Khu K',
			// // 'POUYUEN-DS1',
			// // 'CHI HUNG CO., LTD',
			// // 'CONG TY TNHH TY XUAN',
			// // 'CONG TY TNHH SHYANG HUNG CHENG',
			// // 'CONG TY TNHH VIET NAM SAMHO',
			// // 'CONG TY TNHH GIAY APACHE VIET NAM',
			// // 'CONG TY TNHH LAC TY',
			// // 'Cong ty TNHH Lac Ty II',
			// // 'SAIGON JIM BROTHER_BINH CHUAN',///A Quang gui mail cap nhat them
			// // 'Pouyuen_Tu Tai khu C',
			// // 'Pouyuen_Khu K',
			// // 'Pouyuen_Khu DS1',
			// // 'HWA SEUNG VINA CO LTD/NHON TRACH 1 IND.Z',
			// // 'CHI HUNG CO., LTD',
			// // 'JIA HSIN CO.,LTD',
			// // 'CONG TY TNHH TY XUAN/KHU CONG NGHIEP HOA',
			// // 'CONG TY TNHH SHYANG HUNG CHENG',
			// // 'CONG TY TNHH VIET NAM SAMHO',
			// // 'CONG TY TNHH GIAY APACHE VIET NAM',
			// // 'SAIGON JIM BROTHER\'S CORP',
			// // 'MS. ELY',
			// // 'CONG TY TNHH LAC TY/3-5 TEN LUA ST',
			// // 'Cong ty TNHH Lac Ty II'
		];

		//@tandoan: Cập nhật lại theo danh sách mail gửi
		foreach($array_to_check as $key => $value){
			$value = trim($value);
			if(strpos(strtoupper($SHIP_TO),strtoupper($value))!==FALSE&&(strpos($RBO,"ADIDAS")!==FALSE||strpos($RBO,"REEBOK")!==FALSE)){
				if($FORM_TYPE=='paxar'){
					echo '<h2 class="ship-af">AF</h2>';
				}elseif($FORM_TYPE=='trim'){
					echo '<h2 class="ship-af">AF</h2>';
				}elseif($FORM_TYPE=='sips'){
					echo '<h2 class="ship-af">AF</h2>';
				}
				break;
			}
		}	
	}
	
?>