<?php

	/**
	 * Basic IDEA blocks
	 * Contains sql fields, tables, query parts, titles special for logged user's district
	 *
	 * @copyright Lumen Touch, 2012
	 */
	abstract class IDEAParts {

		/**
		 * This variable indicates that startup params have been initialized
		 *
		 * @var bool
		 */
		private static $initialized = false;

		/**
		 * Normal Student Name
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $stdname = "stdlnm  || ', ' || stdfnm || RTRIM(' ' || LTRIM(COALESCE(SUBSTRING(stdmnm FROM '[[:alnum:]]'), '') || '.', '.'), ' ')";

		/**
		 * Student Age
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $stdage = "date_part('year', age(stddob))";

		/**
		 * Student Gender Male/Female
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $stdsex = "CASE stdsex WHEN 1 THEN 'Male' WHEN 2 THEN 'Female' END";

		/**
		 * Sp Ed Period
		 * DB Table: webset.sys_teacherstudentassignment, webset.disdef_enroll_codes
		 *
		 * @var string
		 */
		protected static $spedPeriod = "dencode || '-' || dendesc || COALESCE(TO_CHAR(ts.stdenterdt, '-MM/DD/YYYY'),'') || COALESCE(TO_CHAR(ts.stdexitdt, '-MM/DD/YYYY'),'')";

		/**
		 * Student Demographics Status Check
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $stdActive = "COALESCE(stdstatus,'A') = 'A'";

		/**
		 * User Manager
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected static $schoolSrch = "std.att_wsds_refid";

		/**
		 * User Manager
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected static $residSrch = "std.res_wsds_refid";

		/**
		 * Sp Ed Status Check
		 * DB Table: webset.sys_teacherstudentassignment, webset.disdef_enroll_codes
		 *
		 * @var string
		 */
		protected static $spedActive = "";

		/**
		 * Disability Code
		 * DB Table: webset.std_disabilitymst, webset.statedef_disablingcondition
		 *
		 * @var string
		 */
		protected static $disabcode = "
				ARRAY_TO_STRING(
					ARRAY(
						SELECT dccode
						  FROM webset.std_disabilitymst sd
							   INNER JOIN webset.statedef_disablingcondition st ON st.dcrefid = sd.dcrefid
						 WHERE sd.stdrefid = tsrefid
						 ORDER BY sdtype, sdrefid DESC
					),
					', '
				)
		";

		/**
		 * Disability Code + Description
		 * DB Table: webset.std_disabilitymst, webset.statedef_disablingcondition
		 *
		 * @var string
		 */
		protected static $disability = "
				ARRAY_TO_STRING(
					ARRAY(
						SELECT COALESCE(st.dccode,'') || ' - ' || st.dcdesc
						  FROM webset.std_disabilitymst sd
							   INNER JOIN webset.statedef_disablingcondition st ON st.dcrefid = sd.dcrefid
						 WHERE sd.stdrefid = tsrefid
						 ORDER BY sdtype, sdrefid DESC
					),
					', '
				)
		";

		/**
		 * Placement Code
		 * DB Table: webset.std_placementcode, webset.statedef_disablingcondition
		 *
		 * @var string
		 */
		protected static $placecode = "
				ARRAY_TO_STRING(
					ARRAY(
						SELECT spccode
						  FROM webset.std_placementcode std
							   INNER JOIN webset.statedef_placementcategorycode plc ON std.spcrefid = plc.spcrefid
						 WHERE std.stdrefid = tsrefid
						 ORDER BY spcbeg, pcrefid DESC
					),
					', '
				)
	    ";

		/**
		 * Placement Code + Description
		 * DB Table: webset.std_placementcode, webset.statedef_disablingcondition
		 *
		 * @var string
		 */
		protected static $placement = "
				ARRAY_TO_STRING(
					ARRAY(
						SELECT COALESCE(plc.spccode,'') || ' - ' || plc.spcdesc
						  FROM webset.std_placementcode std
							   INNER JOIN webset.statedef_placementcategorycode plc ON std.spcrefid = plc.spcrefid
						 WHERE std.stdrefid = tsrefid
						 ORDER BY spcbeg, pcrefid DESC
					),
					', '
				)
	  	";

		/**
		 * Enrollment Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdenrolldt = "TO_CHAR(ts.stdenrolldt, 'MM/DD/YYYY')";

		/**
		 * IEP Meeting Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdiepmeetingdt = "TO_CHAR(ts.stdiepmeetingdt, 'MM/DD/YYYY')";

		/**
		 * IEP Projected Date of Annual Review
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdcmpltdt = "TO_CHAR(ts.stdcmpltdt, 'MM/DD/YYYY')";

		/**
		 * Current Evaluation Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdevaldt = "TO_CHAR(ts.stdevaldt, 'MM/DD/YYYY')";

		/**
		 * Triennial Due Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdtriennialdt = "TO_CHAR(ts.stdtriennialdt, 'MM/DD/YYYY')";

		/**
		 * Procedural Safeguards given to parent(s)
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdprocsafeguarddt = "TO_CHAR(ts.stdprocsafeguarddt, 'MM/DD/YYYY')";

		/**
		 * Bill of Rights given to parent(s)
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $parentrightdt = "TO_CHAR(ts.parentrightdt, 'MM/DD/YYYY')";

		/**
		 * Student DOB
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $stddob = "TO_CHAR(stddob, 'MM/DD/YYYY')";

		/**
		 * Date Entered Sp Ed Program
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdenterdt = "TO_CHAR(stdenterdt, 'MM/DD/YYYY')";

		/**
		 * Date Exited Sp Ed Program
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected static $stdexitdt = "TO_CHAR(stdexitdt, 'MM/DD/YYYY')";

		/**
		 * User Manager
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected static $username = "umlastname || ', ' || umfirstname";

		/**
		 * School Name
		 * DB Table: webset.vw_dmg_studentmst, c_manager.def_websis_schools
		 *
		 * @var string
		 */
		protected static $schoolName = "ws_rep.wsds_school_name";

		/**
		 * Resident District Name
		 * DB Table: webset.vw_dmg_studentmst, c_manager.def_websis_schools
		 *
		 * @var string
		 */
		protected static $districtName_res = "ws_rep_res.wsds_district_name";

		/**
		 * Resident School Name
		 * DB Table: webset.vw_dmg_studentmst, c_manager.def_websis_schools
		 *
		 * @var string
		 */
		protected static $schoolName_res = "ws_rep_res.wsds_school_name";

		/**
		 * School Name
		 * DB Table: webset.dmg_studentmst or webset.vw_dmg_studentmst
		 *
		 * @var string
		 */
		protected static $studentTable = "webset.vw_dmg_studentmst";

		/**
		 * Reporting School JOIN part
		 * DB Table: sys_voumst
		 *
		 * @var string
		 */
		protected static $repschJoin = "LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = std.vourefid";

		/**
		 * Grade Level JOIN part
		 * DB Table: c_manager.def_grade_levels
		 *
		 * @var string
		 */
		protected static $gradeJoin = "LEFT OUTER JOIN c_manager.def_grade_levels gl ON std.gl_refid = gl.gl_refid";

		/**
		 * Enthnic JOIN part
		 * DB Table: webset.statedef_ethniccode
		 *
		 * @var string
		 */
		protected static $ethnicJoin = "LEFT OUTER JOIN webset.statedef_ethniccode eth ON std.stdeth = eth.ethrefid";

		/**
		 * Primary Language JOIN part
		 * DB Table: webset.statedef_prim_lang
		 *
		 * @var string
		 */
		protected static $langJoin = "LEFT OUTER JOIN webset.statedef_prim_lang lang ON std.splrefid = lang.refid";
		/**
		 * Home Language JOIN part
		 * DB Table: webset.statedef_prim_lang
		 *
		 * @var string
		 */
		protected static $langHomeJoin = "LEFT OUTER JOIN webset.statedef_prim_lang langhome ON std.shlrefid = langhome.refid";

		/**
		 * Case Manager JOIN part
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected static $casemanJoin = "LEFT OUTER JOIN sys_usermst um ON ts.umrefid = um.umrefid";

		/**
		 * District Sp Ed Enrollment JOIN part
		 * DB Table: webset.disdef_enroll_codes
		 *
		 * @var string
		 */
		protected static $enrollJoin = "LEFT OUTER JOIN webset.disdef_enroll_codes en  ON ts.denrefid = en.denrefid";

		/**
		 * District Sp Ed Exit JOIN part
		 * DB Table: webset.statedef_prim_lang
		 *
		 * @var string
		 */
		protected static $exitJoin = "LEFT OUTER JOIN webset.disdef_exit_codes ex ON ts.dexrefid = ex.dexrefid";

		/**
		 * Student Demographics JOIN part
		 * DB Table: webset.vw_dmg_studentmst or webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $studentJoin = "INNER JOIN webset.vw_dmg_studentmst AS std ON ts.stdrefid = std.stdrefid";

		/**
		/**
		 * Student Demographics JOIN part
		 * DB Table: webset.vw_dmg_studentmst or webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected static $iepYearJoin = "INNER JOIN webset.std_iep_year AS iepyear ON ts.tsrefid = iepyear.stdrefid AND siymcurrentiepyearsw = 'Y'";

		/**
		 * Reporting School JOIN part
		 * DB Table: sys_voumst
		 *
		 * @var string
		 */
		protected static $repSchoolJoin = "LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = std.vourefid";

		/**
		 * Attending School JOIN part
		 * DB Table: c_manager.def_websis_schools or sys_voumst
		 *
		 * @var string
		 */
		protected static $schoolJoin = "LEFT OUTER JOIN c_manager.def_websis_schools AS ws_rep ON ws_rep.wsds_refid = std.att_wsds_refid";

		/**
		 * District Sp Ed Exit JOIN part
		 * DB Table: webset.statedef_prim_lang
		 *
		 * @var string
		 */
		protected static $residJoin = "LEFT OUTER JOIN c_manager.def_websis_schools AS ws_rep_res ON ws_rep_res.wsds_refid = std.res_wsds_refid";

		/**
		 * District Sp Ed Exit JOIN part
		 * DB Table: webset.statedef_prim_lang
		 *
		 * @var string
		 */
		protected static $resdisJoin;

		/**
		 * Baseline area
		 * DB Table: webset.std_bgb_baseline
		 *
		 * @var string
		 */
		protected static $baselineArea = "COALESCE(domain.gdsdesc || ' -> ', '') || COALESCE(gdssdesc || ' -> ','') || COALESCE(gdsksdesc,'')";


		/**
		 * Initializes properties which are depend on IDEA version
		 *
		 * @return void
		 */
		public static function init() {

			if (self::$initialized) return;
			self::$initialized = true;

			self::$spedActive = "COALESCE(stdenterdt, to_date('1000-01-01', 'YYYY-MM-DD'))<=current_date AND current_date<=COALESCE(stdexitdt, TO_DATE('3000-01-01', 'YYYY-MM-DD')) AND COALESCE(ts.denrefid,0) IN (" . self::activeSpedCodes() . ")";

			if (!IDEACore::websisHere()) {
				self::$studentTable = "webset.dmg_studentmst";
				self::$studentJoin = "INNER JOIN webset.dmg_studentmst AS std ON ts.stdrefid = std.stdrefid";
				self::$schoolName = "vou.vouname";
				self::$schoolName_res = "vou_res.vouname";
				self::$districtName_res = "vnd_res.vndname";
				self::$schoolSrch = "std.vourefid";
				self::$residSrch = "std.vourefid_res";
				self::$schoolJoin = "LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = std.vourefid";
				self::$residJoin = "LEFT OUTER JOIN sys_voumst vou_res ON vou_res.vourefid = std.vourefid_res";
				self::$resdisJoin = "LEFT OUTER JOIN public.sys_vndmst AS vnd_res ON vnd_res.vndrefid = std.vndrefid_res";
			}
		}

		/**
		 * Returns specified property value
		 *
		 * @param mixed $property
		 * @return mixed
		 */
		public static function get($property) {
			if (!self::$initialized) self::init();
			return self::$$property;
		}

		/**
		 * Returns ID numbers of Active Sp Ed Enrollment Codes to be used in student sp ed lists
		 * @return string
		 */
		public static function activeSpedCodes() {

			$filepath = SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_sped.txt';

			if (file_exists($filepath)) {
				$active_id = file_get_contents($filepath);
			} else {
				$SQL = "
	            	SELECT denrefid
	                  FROM webset.disdef_enroll_codes district
	                       INNER JOIN webset.statedef_enroll_codes state ON state.enrrefid = district.statecode_id
	                 WHERE vndrefid = VNDREFID
	                   AND (state.enddate IS NULL OR now()<state.enddate)
	                   AND sped_active = 'Y'
	            ";
				$enrs = db::execSQL($SQL);
				$active_id = '';
				while (!$enrs->EOF) {
					$active_id .= $enrs->fields['denrefid'] . ',';
					$enrs->MoveNext();
				}
				$active_id .= "0";
				file_put_contents($filepath, $active_id);
			}
			return $active_id;
		}

	}

?>
