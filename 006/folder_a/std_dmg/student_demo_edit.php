<?php

	Security::init(PHP_NOTICE_ON);
	
	$RefID = io::get('RefID');
		
	$strSQL = "SELECT staterefid,
				   state,
				   statename
			  FROM webset.glb_statemst, public.sys_vndmst
			 WHERE public.sys_vndmst.vndstate = webset.glb_statemst.state
			   AND public.sys_vndmst.vndrefid = " . $_SESSION["s_VndRefID"];
	
	$rs = db::execSQL($strSQL);
	if(!$rs->EOF) {
		$staterefid = $rs->fields['staterefid'];
	}
	$vndstate = 'MO';
	
	$strSQL = "SELECT TO_CHAR(stddtenrol, 'MM-DD-YYYY') AS stddtenrol,
					  enrollcode, seccode || ' - ' || secdesc AS secdesc,
					  rt_code, rt_name,
					  TO_CHAR(std_withdr_date, 'MM-DD-YYYY') AS std_withdr_date,
					  trim(wcode) || ' - ' || trim(wdesc) AS std_withdr_cod, vourefid,stdphoto
				 FROM webset.vw_dmg_studentmst AS s
					  LEFT OUTER JOIN c_manager.def_resident_type AS rt ON s.resident_type = rt_code
					  LEFT OUTER JOIN c_manager.def_enrollcode ON enrollcode = secrefid
					  LEFT OUTER JOIN c_manager.def_withdraw ON wrefid = std_withdr_cod
				WHERE stdrefid = $RefID";
	$rs = db::execSQL($strSQL);
	if(!$rs->EOF) {
		$stdphoto = $rs->fields['stdphoto'];
		if($rs->fields['rt_code'] == '') {
			$resident_sql = "SELECT NULL,'Not Selected'";
			$resident_type = "Not Selected";
		} else {
			$resident_sql = "SELECT '".$rs->fields['rt_code']."','".$rs->fields['rt_name']."'";
			$resident_type = $rs->fields['rt_code']." - ".$rs->fields['rt_name'];
		}
	}
	
	$imgheight = 90;
	
	$key = readKey('websis', 'demographics', 'dmg_photo_path', 1);
	if ($key!="-1") {
		$img_folder = current($key);

		if (strpos($img_folder, "sec_disk")){
			$pathSec = $_SESSION['s_secDisk'].(str_replace("/sec_disk", "", $img_folder));
			$img_folder = $pathSec;

			$imgpath = $img_folder."/".$stdphoto;
		} else {
			$imgpath = $g_physicalRoot.$img_folder."/".$stdphoto;
		}

	}
/*
	if (file_exists($imgpath) && isset($stdphoto)) {
		list($width, $height) = getimagesize($imgpath);
		if ($width != 0 && $height != 0) {
			$percent 	= ($imgheight / $height)*100;
			$newwidth   = ($width * $percent) / 100;
			$newheight  = ($height * $percent) / 100;
			$key = readKey('sdsupport', 'sdsupport_websis_parameter', 'smallViewType', 1);
			$imgViewType = $key!=-1?current($key):'a';
			$key = readKey('sdsupport', 'sdsupport_websis_parameter', 'smallViewX', 1);
			$imgX = $key!=-1?current($key):'empty';
			$key = readKey('sdsupport', 'sdsupport_websis_parameter', 'smallViewY', 1);
			$imgY = $key!=-1?current($key):'empty';
			if($imgX == 'empty'){$imageView = 'a';}
			if($imgY == 'empty' && $imageView == 'px'){$imageView = 'a';}

			switch($imgViewType){
				case 'px':
					$img_tag = '<img  id="stdImage" width='.$imgX.' height='.$imgY.' src="' . $g_virtualRoot . '/applications/webset/system/loadStdImage.php?RefID=' . $RefID . '&imgsize='.$imgX.' " />';
					break;
				case 'pr':
					$height = $height/100*$imgX;
					//print("<script>alert($height)</script>")  ;
					$img_tag = '<img id="stdImage" src="' . $g_virtualRoot . '/applications/webset/system/loadStdImage.php?RefID=' . $RefID . '&imgsize='.$height.'" />';
					break;
				default:
					$img_tag = '<img id="stdImage" src="' . $g_virtualRoot . '/applications/webset/system/loadStdImage.php?RefID=' . $RefID . '&imgsize=90" />';
			}
		} else {
			$img_tag = '<img id="stdImage" src="' . $g_virtualRoot . '/applications/webset/system/loadStdImage.php?RefID=' . $RefID . '" />';
		}
	} else {
		$img_tag = '<img id="stdImage" src="' . $g_virtualRoot . '/applications/webset/system/loadStdImage.php?RefID=' . $RefID . '" />';
	}
*/
	$img_tag = FFStudentPhoto::factory()
				->setStudent($RefID)
				->caption('')
				->sizeTo(80,90)
				->toHTML();

	$img_html = '<TABLE cellpadding=0 cellspacing=0 border=0>
					 <tr>
					  <td>
					   <div id=stanart_div style="cursor:pointer; border: 1px solid silver; margin: 2px; padding: 3px 3px;" class="zText">
						  ' . $img_tag . '
					   </div>
					   </td>
					   </tr>
				   </TABLE>';
	
	$edit = new EditClass('edit1', $RefID);
	$edit->title = 'Add/Edit Student';
	
	$edit->SQL = "SELECT  stdfnm, 			/* field0 */
							  stdmnm,
							  stdlnm,
							  stdnm_suffix,
							  stdnickname,
							  gl_refid,
							  stdstatus,
							  enrollment_status,
							  stddob,
							  stdsex,
							  stdeth,

							  hispanic_sw,
							  asian_sw,
							  american_indian_sw,
							  black_sw,
							  native_hawaiian_sw,
							  white_sw,

							  externalid,
							  stdschid,
							  stdfedidnmbr,
							  stdstateidnmbr,
							  stdmedicatenum, 	/* field13 */
							  birth_certificate_sw,
							  stdbcnum,

							  std_hometeach,
							  std_homeroom,
							  resident_type,
							  stdcounty,
							  vourefid,
							  vourefid_res,
							  stdregion,

							  stddtenrol,
							  enrollcode,
							  syd_refid_graduation, /* THIS HAS TO BE CHANGED*/

							  stdsped,

							  student504,
							  giftedprogram,
							  stdtitle_i,
							  stdcareer,
							  cte_cluster,
							  nontrad_student,
							  single_parent,
							  displ_homemaker,
							  std_homeless_sw,
							  splrefid,
							  shlrefid,
							  elprefid,
							  stdprim_par, 		/* field32 */
							  pprefid,

							  std_withdr_date, /* 44 */
							  std_withdr_cod,
							  graduation_date,
							  graduation_date_sw,
							  null AS not_save_field_0,
							  surpar_need,

							  stdphmob,
							  stdmail,
							  stdhphn, 			/* field39 */

							  stdhadr1,

							  stdhcity_m,
							  stdhstate_m,
							  stdhzip_m,

							  NULL AS not_save_field_1, /* This field will be deleted in Finaly Save SQL */

							  stdhadr2,

							  stdhcity,
							  stdhstate,
							  stdhzip,

							  NULL AS not_save_field_2, /* This field will be deleted in Finaly Save SQL */
							  NULL AS not_save_field_3, /* This field will be deleted in Finaly Save SQL */

							  car_make,
							  car_model,
							  car_license_state,
							  car_license_num,
							  parking_tag_num,
							  NULL AS not_save_field_4, /* This field will be deleted in Finaly Save SQL */

							  ell_country,
							  birth_state,
							  birth_city,
							  birth_county,
							  birth_mother_lnm,
							  birth_mother_fnm,
							  ell_imgr,
							  ell_migrant_sw,
							  /*ell_usayear,*/
							  ell_usa_entered,
							  ell_fenr_dt,
							  std_first_enrolled,
							  /*ell_months,*/
							  ell_monitor,
							  ell_lep,
							  ell_title3,
							  ell_primlang,

							  federal_lands,
							  std_neglected_sw,
							  std_privatschool_sw,
							  std_schooled_sw,
							  std_foreign_exchange_sw,
							  lastuser,
							  lastupdate,

							  vndrefid,
							  vndrefid_res,
							  splrefid as splrefid2,
							  stdeth as stdeth2,
							  stdcounty as stdcounty2,		/* field52 */
							  std_withdr_date as std_withdr_date2
					FROM webset.dmg_studentmst

					WHERE stdRefID=$RefID ";

	class photo extends FormField{
		public function __construct() {
			parent::__construct();
		}
		
		public function toSQLCondition($db = null, $dbType = '') {
			return '';
		}
		
		public function toHTML($db=null) {
			return $this->createHTMLElement("span", $tmp=array());
		}
	}
	$edit->addTab('Student');
	$edit->addControl("Student Legal First Name", "EDIT")->sqlField('stdfnm')->req(true);
	$edit->addControl("Student Legal Middle Name", "EDIT")->sqlField('stdmnm');
	$edit->addControl("Student Legal Last Name", "EDIT")->sqlField('stdlnm')->req(true);
	$edit->addControl("Name Suffix:", "EDIT")->sqlField('stdnm_suffix');
	$edit->addControl(new photo())->caption('Student Photo')->append($img_html);
	$edit->addControl("Grade Level: ", "SELECT")->sqlField('gl_refid')->sql("
			SELECT gl_refid, gl_code, gl_numeric_value
			  FROM c_manager.def_grade_levels
			 WHERE vndrefid = " .  $_SESSION["s_VndRefID"] . "
			   AND gl_refid = (SELECT v.gl_refid FROM webset.vw_dmg_studentmst As v WHERE v.stdrefid=".$_GET['RefID'].")
		")
		->req(true);
	$edit->addControl("Student Status: ", "SELECT_RADIO")->sqlField('stdstatus')->sql("
	  SELECT validvalueid, validvalue
		FROM webset.glb_validvalues
	   WHERE valuename = 'UKTStatus'
		");
	$edit->addControl("Enrollment Status", "SELECT_RADIO")->sqlField('enrollment_status')->sql("
		SELECT validvalueid, validvalue
		  FROM webset.glb_validvalues
		 WHERE valuename = 'Enrollment_Status'
		");
	$edit->addControl("Date of Birth: ", "DATE");
	$edit->addControl("Gender: ", "SELECT")->sqlField('stdsex')->sql("
		SELECT NULL, 'not selected'
		UNION ALL
		SELECT validvalueid, validvalue
		  FROM webset.glb_validvalues
		  WHERE valueName = 'UKTGender'
		")->req(true);
		
	$edit->addControl("Race/ Ethnicity", "SELECT")->sqlField('stdeth')->sql("
		SELECT NULL, ' None Selected', '00' UNION
							  SELECT ethrefid,
									 TRIM(TRIM(COALESCE(ethcode, '')) || '/' || COALESCE(replace(ethdesc,'/',' '), ''), '/'),
									 ethcode
								FROM public.sys_vndmst
									 INNER JOIN webset.glb_statemst ON CAST(public.sys_vndmst.vndstate as varchar) = CAST(webset.glb_statemst.state as VARCHAR)
									 INNER JOIN webset.statedef_ethniccode ON webset.glb_statemst.staterefid = webset.statedef_ethniccode.scdrefid
							   WHERE vndrefid=" .  $_SESSION["s_VndRefID"] . "
							   ORDER BY 3
		");
	$edit->addControl("Hispanic:", "SELECT_RADIO")->sqlField('hispanic_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Asian", "SELECT_RADIO")->sqlField('asian_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("American Indian or Alaska Native", "SELECT_RADIO")->sqlField('american_indian_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Black or African American", "SELECT_RADIO")->sqlField('black_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Native Hawaiian or Pacific Islander", "SELECT_RADIO")->sqlField('native_hawaiian_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("White", "SELECT_RADIO")->sqlField('white_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
			
	$edit->addControl("External ID Number")->sqlField('externalid');
	$edit->addControl("Student ID Number (Ext2)")->sqlField('stdschid');
	$edit->addControl("Federal ID Number", "EDIT")->sqlField('stdfedidnmbr');
	$edit->addControl("State ID Number", "EDIT")->sqlField('stdstateidnmbr');
	$edit->addControl("Medicaid Number", "EDIT")->sqlField('stdmedicatenum');
	$edit->addControl("Birth Certificate on File", "SELECT_RADIO")->sqlField('birth_certificate_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Birth Certificate Number", "EDIT")->sqlField('stdbcnum');
	$edit->addControl("Homeroom Instructor", "EDIT")->sqlField('std_hometeach');
	$edit->addControl("Homeroom #", "EDIT")->sqlField('std_homeroom');
	$edit->addControl("Resident Type", "PROTECTED")->sqlField('resident_type');
	$edit->addControl("Region: ", "SELECT")->sqlField('stdregion')->sql("
			SELECT NULL, ' None Selected' UNION
			SELECT rrefid, region
			  FROM webset.disdef_regions
			 WHERE vndRefID = " .  $_SESSION["s_VndRefID"] . "
			 ORDER by 2
	");
		
	$edit->addTab('General');
	$edit->addControl("Enrollment Date", "PROTECTED")->sqlField('stddtenrol');
	$edit->addControl("Enrollment Code", "PROTECTED")->sqlField('enrollcode');
	$edit->addControl("Anticipated Graduation School Year", "SELECT")->sqlField('syd_refid_graduation')->sql("
			SELECT NULL, '-- not selected --' UNION ALL SELECT * FROM (SELECT syd_refid, syd_desc FROM c_manager.def_school_years_dis WHERE vndrefid = " .  $_SESSION["s_VndRefID"] . " ORDER BY syd_begdate) as main_tab
	");
	$edit->addControl("Sp Ed Student", "PROTECTED")->sqlField('stdsped');
	$edit->addControl("504 Student", "SELECT_RADIO")->sqlField('student504')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Gifted Program", "SELECT_RADIO")->sqlField('giftedprogram')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Title I", "SELECT_RADIO")->sqlField('stdtitle_i')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Career Education: ", "SELECT")->sqlField('stdcareer')->sql("
		SELECT NULL, ' -- not selected --'
		  UNION ALL
		  (SELECT hce_refid,
				 hce_code
			FROM c_manager.def_hs_career_ed
		   WHERE vndrefid = " . $_SESSION["s_VndRefID"] . "
		   ORDER BY hce_rank)
		");
	$edit->addControl("Career Cluster: ", "SELECT")->sqlField('cte_cluster')->sql("
		SELECT '', 'Not Set' UNION ALL SELECT * FROM (SELECT sc_code, sc_name FROM c_manager_statedef.mo_sc_cte_cluster ORDER BY sc_rank) as t01
	");
	$edit->addControl("Nontraditional Student", "SELECT_RADIO")->sqlField('nontrad_student')->data(
			array(
				array('' , 'Not Set'),
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Single Parent", "SELECT_RADIO")->sqlField('single_parent')->data(
			array(
				array('' , 'Not Set'),
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Displaced Homemaker", "SELECT_RADIO")->sqlField('displ_homemaker')->data(
			array(
				array('' , 'Not Set'),
				array('Y', 'Yes'),
				array('N', 'No')
			));
	$edit->addControl("Homeless", "SELECT_RADIO")->sqlField('std_homeless_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));
			
	$edit->addControl("Primary Language or Comm. Mode(s)", "SELECT")->sqlField('splrefid')->sql("
		SELECT refid,
					   CASE WHEN LOWER(adesc) in ('english','spanish','spanish; castilian','french','chinese','japanese','sign Language') THEN '<b>' ELSE '' END ||
							COALESCE(eden_code || ' - ','') || adesc,
					   CASE LOWER(adesc)
					   WHEN 'english'  then '000010'
					   WHEN 'spanish'  then '000020'
					   WHEN 'spanish; castilian'  then '000020'
					   WHEN 'french' then '000030'
					   WHEN 'chinese'  then '000040'
					   WHEN 'japanese' then '000050'
					   WHEN 'sign Language' then '000060'
					   else eden_code END
				  FROM webset.statedef_prim_lang
				 WHERE screfid = $staterefid and
					   (recactivationdt is null or recactivationdt <= now()) and
					   (recdeactivationdt is null or recdeactivationdt  >= now())
				 ORDER BY 3
				 LIMIT 20
		");
	$edit->addControl("Language Used at Home", "SELECT")->sqlField('shlrefid')->sql("
		SELECT refid,
					   CASE WHEN LOWER(adesc) in ('english','spanish','spanish; castilian','french','chinese','japanese','sign Language') THEN '<b>' ELSE '' END ||
							COALESCE(eden_code || ' - ','') || adesc,
					   CASE LOWER(adesc)
					   WHEN 'english'  then '000010'
					   WHEN 'spanish'  then '000020'
					   WHEN 'spanish; castilian'  then '000020'
					   WHEN 'french' then '000030'
					   WHEN 'chinese'  then '000040'
					   WHEN 'japanese' then '000050'
					   WHEN 'sign Language' then '000060'
					   else eden_code END
				  FROM webset.statedef_prim_lang
				 WHERE screfid = $staterefid and
					   (recactivationdt is null or recactivationdt <= now()) and
					   (recdeactivationdt is null or recdeactivationdt  >= now())
				 ORDER BY 3
				 LIMIT 20
		");
	$edit->addControl("English Language Proficiency", "SELECT")->sqlField('elprefid')->sql("
		SELECT NULL, ' None Selected' UNION
		  SELECT  elprefid, elpdesc FROM        webset.disdef_english_lang_prof
		  WHERE     (vndrefid = " . $_SESSION["s_VndRefID"] . ")
		  ORDER BY 2
		");
		
	$edit->addControl("Language of Parent", "SELECT")->sqlField('stdprim_par')->sql("
		SELECT refid,
					   CASE WHEN LOWER(adesc) in ('english','spanish','spanish; castilian','french','chinese','japanese','sign Language') THEN '<b>' ELSE '' END ||
							COALESCE(eden_code || ' - ','') || adesc,
					   CASE LOWER(adesc)
					   WHEN 'english'  then '000010'
					   WHEN 'spanish'  then '000020'
					   WHEN 'spanish; castilian'  then '000020'
					   WHEN 'french' then '000030'
					   WHEN 'chinese'  then '000040'
					   WHEN 'japanese' then '000050'
					   WHEN 'sign Language' then '000060'
					   else eden_code END
				  FROM webset.statedef_prim_lang
				 WHERE screfid = $staterefid and
					   (recactivationdt is null or recactivationdt <= now()) and
					   (recdeactivationdt is null or recdeactivationdt  >= now())
				 ORDER BY 3
				 LIMIT 20
		");
	$edit->addControl("Parental Placement: ", "SELECT")->sqlField('pprefid')->sql("
		SELECT NULL, ' None Selected', '_'
		  UNION
		  SELECT  pprefid, ppcode || ' - ' || ppdesc, ppcode || ' - ' || ppdesc
		  FROM    webset.statedef_parental_placement
		  WHERE   (screfid = $staterefid)
		  ORDER BY 3
		");
	$edit->addControl("Withdrawal Date", "PROTECTED")->sqlField("std_withdr_date");
	$edit->addControl("Withdrawal Code", "PROTECTED")->sqlField("std_withdr_cod");    
	$edit->addControl("Graduation Date", "DATE")->sqlField('graduation_date');

	$edit->addTab('Contact Info');
	$edit->addControl("Student Mobile Phone", "EDIT")->sqlField('stdphmob');
	$edit->addControl("Student Email Address", "EDIT")->sqlField('stdmail');
	$edit->addControl("Home Phone", "EDIT")->sqlField('stdhphn');
	
	$edit->addControl("Mailing Address", "EDIT")->sqlField('stdhadr1');
	
	$html_mail = '<div style="display:inline-block;vertical-align:middle">
					<div style="text-align:center;">[CAPTION]</div>
					<div>[FIELD]</div>
				 </div>';
	
	$edit->addControl("", "PROTECTED")
		->append(FFInput::factory()->caption('City')->htmlWrap($html_mail)->sqlField('stdhcity_m'))
		->append(FFSelect::factory()->caption('State')->htmlWrap($html_mail)->sqlField('stdhstate_m')->sql("
			SELECT state, state FROM public.def_statemst ORDER BY 2
		"))		
		->append(FFInput::factory()->caption('Zip Code')->htmlWrap($html_mail)->sqlField('stdhzip_m'));
	
	$edit->addTab('NCLB');
	$edit->addControl("Country of Origin", "SELECT")->sqlField('ell_country')->sql("
		SELECT screfid,
			   CASE defloption WHEN 'Y' THEN '<b>' else '' END || code || ' - ' || country,
			   CASE defloption WHEN 'Y' THEN seq-1000 else seq END
		  FROM c_manager.def_countries_st
		 WHERE state = 'MO'
		 ORDER BY 3
		 LIMIT 30
		");
	$edit->addControl("Birth State\Province", "SELECT")->sqlField('birth_state')->sql("
		SELECT statename, statename FROM public.def_statemst
					  WHERE statename != '--'
					  ORDER BY statename
		");
	$edit->addControl("Birth City", "EDIT")->sqlField('birth_city');
	$edit->addControl("Birth County", "EDIT")->sqlField('birth_county');
	$edit->addControl("Birth Mothers Last Name", "EDIT")->sqlField('birth_mother_lnm');
	$edit->addControl("Birth Mothers First Name", "EDIT")->sqlField('birth_mother_fnm');
	$edit->addControl("Immigrant", "SELECT_RADIO")->sqlField('ell_imgr')
		->sql("
		SELECT sc_code, sc_name FROM c_manager_statedef.sc_immigrant
		WHERE sc_statecode = '$vndstate'
		AND  sc_default_sw = 'Y'
		UNION ALL
		SELECT * FROM (
		SELECT sc_code, sc_name FROM c_manager_statedef.sc_immigrant
		WHERE sc_statecode = '$vndstate'
		AND  sc_default_sw != 'Y'
		ORDER BY 2
		) as t01
		");
	$edit->addControl("Migrant", "SELECT_RADIO")->sqlField('ell_migrant_sw')->data(
			array(
				array('Y', 'Yes'),
				array('N', 'No')
			));

//========== GUARDIANS ================			
	$edit->addTab('Guardians');
	$edit->addIFrame(CoreUtils::getURL('student_demo_guardians_list.php',array('stdRefID'=>$RefID)))
		->id('student_demo_guardians')
		->height(600);

//========== Groupings ================			
	$edit->addTab('Grouping');
	$edit->addIFrame(CoreUtils::getURL('student_demo_groupings_list.php',array('stdRefID'=>$RefID)))
		->id('student_demo_groupings')
		->height(600);
//========== Groupings ================			
	$edit->addTab('Emergency');
	$edit->addIFrame(CoreUtils::getURL('student_demo_emerg_list.php',array('stdRefID'=>$RefID)))
		->id('student_demo_emerg')
		->height(600);
//========== Enrollment ================			
	$edit->addTab('Enrollment');
	$edit->addIFrame(CoreUtils::getURL('student_demo_enrollment_list.php',array('stdRefID'=>$RefID)))
		->id('student_demo_enrollment')
		->height(600);
		
		
	$edit->saveURL = 'student_demo_list.php';
	$edit->cancelURL = 'student_demo_list.php';
	
	# hide Save&Add button
	$edit->getButton(EditClassButton::SAVE_AND_ADD)->hide(true);

	$edit->printEdit();	
	/*
							 
							  ell_usa_entered,
							  ell_fenr_dt,
							  std_first_enrolled,
							  ell_monitor,
							  ell_lep,
							  ell_title3,
							  ell_primlang,

							  federal_lands,
							  std_neglected_sw,
							  std_privatschool_sw,
							  std_schooled_sw,
							  std_foreign_exchange_sw,
							  lastuser,
							  lastupdate,

							  ".implode(",\n", $_customFieldsSQL).(empty($_customFieldsSQL)?'':',')."

							  vndrefid,
							  vndrefid_res,
							  splrefid as splrefid2,
							  stdeth as stdeth2,
							  stdcounty as stdcounty2,		
							  std_withdr_date as std_withdr_date2
	*/
?>