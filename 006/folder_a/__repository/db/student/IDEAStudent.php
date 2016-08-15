<?php

	/**
	 * Contains basic student Demo and Sp Ed data
	 *
	 * @copyright Lumen Touch, 2012
	 */
	class IDEAStudent extends RegularClass {

		/**
		 * Sp Ed Enrollment Student Lumen ID
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var int
		 */
		protected $tsrefid;

		/**
		 * Student Lumen ID
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var int
		 */
		protected $stdrefid;

		/**
		 * Student Name
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var int
		 */
		protected $stdname;

		/**
		 * Student Name in FML format
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdnamefml;

		/**
		 * Student Last Name
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var int
		 */
		protected $stdlastname;

		/**
		 * Student First Name
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdfirstname;

		/**
		 * Student DOB
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stddob;

		/**
		 * Student Gender
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdsex;

		/**
		 * Student's Photo
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdphoto;

		/**
		 * Student's Case Manager Name
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected $cmname;

		/**
		 * Student's Case Manager Title
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected $cmtitle;

		/**
		 * Student's Case Manager Name in LF format
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected $cmnamelf;

		/**
		 * Student's Case Phone
		 * DB Table: sys_usermst
		 *
		 * @var string
		 */
		protected $cmphone;

		/**
		 * Student IEP Initiation Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $stdenrolldt;

		/**
		 * Student IEP Meeting Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $stdiepmeetingdt;

		/**
		 * Student Evaluation Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $stdevaldt;

		/**
		 * Student IEP Annual Review Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $stdcmpltdt;

		/**
		 * Student Triennial Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $stdtriennialdt;

		/**
		 * Bill of Rights given to parent(s)
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $parentrightdt;

		/**
		 * Procedural Safeguards given to parent(s)
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $stdprocsafeguarddt;

		/**
		 * Student ID Number (Ext2)
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $externalid;

		/**
		 * Student External ID Number
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdschid;

		/**
		 * Federal ID Number
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdfedidnmbr;

		/**
		 * State ID Number
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdstateidnmbr;

		/**
		 * Student Grade Level
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $grdlevel;

		/**
		 * Student Grade Level ID
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var int
		 */
		protected $grdlevel_id;

		/**
		 * Student Ethnic Code
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $ethcode;

		/**
		 * Student Race
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $ethdesc;

		/**
		 * Student Reporting(Attending) School ID
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var int
		 */
		protected $vourefid;

		/**
		 * Student  School ID
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var int
		 */
		protected $schoolid;

		/**
		 * Student Reporting(Attending) School
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $vouname;

		/**
		 * Student Reporting(Resident) School
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $vouname_res;

		/**
		 * Student Age
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdage;

		/**
		 * Student Home Phone
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdhphn;

		/**
		 * Student Mobile Phone
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdhphnmob;

		/**
		 * Student Language
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $prim_lang;

		/**
		 * Student Language Used at Home
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $home_lang;

		/**
		 * Student Address
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdhadr1;

		/**
		 * Student City
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdhcity;

		/**
		 * Student State
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdhstate;

		/**
		 * Student Zip Code
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdhzip;

		/**
		 * Full Student Address with City State Zip
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdaddress;

		/**
		 * Student EC Flag Y/N
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $ecflag;

		/**
		 * Student IEP Year ID
		 * DB Table: webset.std_iep_year
		 *
		 * @var int
		 */
		protected $stdiepyear;

		/**
		 * Student IEP Year Begin Date
		 * DB Table: webset.std_iep_year
		 *
		 * @var int
		 */
		protected $stdiepyearbgdt;

		/**
		 * Student IEP Year End Date
		 * DB Table: webset.std_iep_year
		 *
		 * @var string
		 */
		protected $stdiepyearendt;

		/**
		 * Student IEP Year title
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdiepyeartitle;

		/**
		 * Student Demographics Status Y/N
		 * DB Table: webset.dmg_studentmst
		 *
		 * @var string
		 */
		protected $stdstatus;

		/**
		 * Sp Ed Status
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var string
		 */
		protected $spedstatus;

		/**
		 * Sp Ed Enrollment Code ID
		 * DB Table: referrence to webset.disdef_enroll_codes
		 *
		 * @var int
		 */
		protected $denrefid;

		/**
		 * Sp Ed Exit Code ID
		 * DB Table: referrence to webset.disdef_exit_codes
		 *
		 * @var int
		 */
		protected $dexrefid;

		/**
		 * Sp Ed Exit Code Other
		 * DB Table: referrence to webset.disdef_exit_codes
		 *
		 * @var varchar
		 */
		protected $parcommentsd;

		/**
		 * Sp Ed enrollment Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var int
		 */
		protected $stdenterdt;

		/**
		 * Sp Ed enrollment Date
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var int
		 */
		protected $stdexitdt;

		/**
		 * Unique teacher. Students can have Case Manager.
		 *
		 * @var array
		 */
		protected $caseManager;

		/**
		 * Constructions std
		 * DB Table: webset.std_constructions
		 *
		 * @var array
		 */
		protected $constructions = array();

		/**
		 * Summary/Additional Comments/Recommendations
		 *
		 * @var array
		 */
		protected $summaryRecommendations;

		/**
		 * IEP MEETING PARTICIPANTS
		 *
		 * @var array
		 */
		protected $commetteMembers;

		/**
		 * PLEP/PLAFP data
		 *
		 * @var array
		 */
		protected $plep;

		/**
		 * BGB Goals
		 *
		 * @var array
		 */
		protected $bgbGoals;

		/**
		 * Progress Report BGB Goals
		 *
		 * @var array
		 */
		protected $progressReportBGB;

		/**
		 * Simple Progress Report BGB Goals
		 *
		 * @var array
		 */
		protected $progressReportSimpleBGB;

		/**
		 * Progress Reporting
		 *
		 * @var array
		 */
		protected $progressReporting;

		/**
		 * Initializes basic properties
		 *
		 * @param int $stdiepyear
		 * @param int $tsRefID
		 */
		public function __construct($tsRefID = 0, $stdiepyear = 0) {
			parent::__construct();

			$SQL = "
            SELECT std.stdrefid,
	               stdfnm || RTRIM(' ' || LTRIM(COALESCE(SUBSTRING(stdmnm FROM '[[:alnum:]]'), '') || '.', '.'), ' ') || ' ' || stdlnm as stdname,
	               stdfnm,
                   stdlnm,
	               " . IDEAParts::get('stddob') . " as stddob,
	               " . IDEAParts::get('stdsex') . " as stdsex,

		           stdschid,
		           stdfedidnmbr,
		           stdstateidnmbr,
		           externalid,

		           gl.gl_code,
	               gl.gl_refid,
	               ethcode,
	               ethdesc,
                   std.vourefid,
	               " . IDEAParts::get('schoolSrch') . " as schoolid,
	               " . IDEAParts::get('schoolName') . " as vouname,
	               " . IDEAParts::get('schoolName_res') . " as vouname_res,
	               " . IDEAParts::get('districtName_res') . " as vndname_res,
	               AGE(stddob) as stdage,
	               lang.adesc as prim_lang,
	               langhome.adesc as home_lang,

	               stdhphn,
                   stdhphnmob,
                   COALESCE(stdhadr1,'') as stdhadr1,
                   COALESCE(stdhcity_m, stdhcity) as stdhcity,
                   COALESCE(stdhstate_m, stdhstate) as stdhstate,
                   COALESCE(stdhzip_m, stdhzip) as stdhzip,

		           stdphoto,
                   " . IDEAParts::get('username') . " AS cmname,
                   umtitle,
		           umlastname || ' ' || umfirstname AS cmnamelf,
	               REPLACE(um.umphone||COALESCE(', ext '||um.umphoneext, ''), '() -', '') AS cmphone,
	               COALESCE(stdearlychildhoodsw, 'N') as stdearlychildhoodsw,
	               " . IDEAParts::get('stdenrolldt') . " as stdenrolldt,
		           " . IDEAParts::get('stdiepmeetingdt') . " as stdiepmeetingdt,
		           " . IDEAParts::get('stdevaldt') . " as stdevaldt,
		           " . IDEAParts::get('stdcmpltdt') . " as stdcmpltdt,
		           " . IDEAParts::get('stdtriennialdt') . " as stdtriennialdt,
		           " . IDEAParts::get('parentrightdt') . " as parentrightdt,
		           " . IDEAParts::get('stdprocsafeguarddt') . " as stdprocsafeguarddt,
	               iep.siymrefid,
	               TO_CHAR(iep.siymiepbegdate, 'MM-DD-YYYY') as siymiepbegdate,
	               TO_CHAR(iep.siymiependdate, 'MM-DD-YYYY') as siymiependdate,
	               TO_CHAR(iep.siymiepbegdate, 'MM/DD/YYYY') || ' - ' || TO_CHAR(iep.siymiependdate, 'MM/DD/YYYY') as iepyear,

				   CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
                   CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus,
                   denrefid,
                   dexrefid,
                   parcomments,
                   " . IDEAParts::get('stdenterdt') . " as stdenterdt,
		           " . IDEAParts::get('stdexitdt') . " as stdexitdt
		      FROM webset.sys_teacherstudentassignment ts
		           LEFT OUTER JOIN webset.std_iep_year iep ON ts.tsrefid = iep.stdrefid AND " . ($stdiepyear > 0 ? "iep.siymrefid = " . $stdiepyear : "siymcurrentiepyearsw = 'Y'") . "
		           " . IDEAParts::get('studentJoin') . "
		           " . IDEAParts::get('residJoin') . "
		           " . IDEAParts::get('schoolJoin') . "
		           " . IDEAParts::get('gradeJoin') . "
		           " . IDEAParts::get('casemanJoin') . "
		           " . IDEAParts::get('langJoin') . "
		           " . IDEAParts::get('langHomeJoin') . "
		           " . IDEAParts::get('ethnicJoin') . "
		           " . IDEAParts::get('resdisJoin') . "
		     WHERE tsrefid = " . $tsRefID . "
        ";

			$result = $this->execSQL($SQL);

			$this->tsrefid = $tsRefID;
			$this->stdrefid = $result->fields['stdrefid'];
			$this->stdlastname = $result->fields['stdlnm'];
			$this->stdfirstname = $result->fields['stdfnm'];
			$this->stdname = $this->stdfirstname . ' ' . $this->stdlastname;
			$this->stdnamefml = $result->fields['stdname'];
			$this->stddob = $result->fields['stddob'];
			$this->stdsex = $result->fields['stdsex'];

			$this->stdschid = $result->fields['stdschid'];
			$this->stdfedidnmbr = $result->fields['stdfedidnmbr'];
			$this->stdstateidnmbr = $result->fields['stdstateidnmbr'];
			$this->externalid = $result->fields['externalid'];

			$this->grdlevel = $result->fields['gl_code'];
			$this->grdlevel_id = $result->fields['gl_refid'];
			$this->ethcode = $result->fields['ethcode'];
			$this->ethdesc = $result->fields['ethdesc'];
			$this->vourefid = $result->fields['vourefid'];
			$this->schoolid = $result->fields['schoolid'];
			$this->vouname = $result->fields['vouname'];
			$this->vndname_res = $result->fields['vndname_res'];
			$this->vouname_res = $result->fields['vouname_res'];
			$this->stdage = substr($result->fields['stdage'], 0, strpos($result->fields['stdage'], ' '));
			$this->prim_lang = $result->fields['prim_lang'];
			$this->home_lang = $result->fields['home_lang'];

			$this->stdhphn = $result->fields['stdhphn'];
			$this->stdhphnmob = $result->fields['stdhphnmob'];
			$this->stdhadr1 = $result->fields['stdhadr1'];
			$this->stdhcity = $result->fields['stdhcity'];
			$this->stdhstate = $result->fields['stdhstate'];
			$this->stdhzip = $result->fields['stdhzip'];
			$this->stdaddress = trim($this->get('stdhadr1') . ', ' .
				$this->get('stdhcity') . ', ' .
				$this->get('stdhstate') . ' ' .
				$this->get('stdhzip'), ', ');

			$this->stdphoto = $result->fields['stdphoto'];
			$this->cmname = $result->fields['cmname'];
			$this->cmtitle = $result->fields['umtitle'];
			$this->cmnamelf = $result->fields['cmnamelf'];
			$this->cmphone = $result->fields['cmphone'];
			$this->ecflag = $result->fields['stdearlychildhoodsw'];
			$this->stdenrolldt = $result->fields['stdenrolldt'];
			$this->stdiepmeetingdt = $result->fields['stdiepmeetingdt'];
			$this->stdevaldt = $result->fields['stdevaldt'];
			$this->stdcmpltdt = $result->fields['stdcmpltdt'];
			$this->stdtriennialdt = $result->fields['stdtriennialdt'];
			$this->parentrightdt = $result->fields['parentrightdt'];
			$this->stdprocsafeguarddt = $result->fields['stdprocsafeguarddt'];

			$this->stdiepyear = $result->fields['siymrefid'];
			$this->stdiepyearbgdt = $result->fields['siymiepbegdate'];
			$this->stdiepyearendt = $result->fields['siymiependdate'];
			$this->stdiepyeartitle = $result->fields['iepyear'];

			$this->stdstatus = $result->fields['stdstatus'];
			$this->spedstatus = $result->fields['spedstatus'];
			$this->denrefid = $result->fields['denrefid'];
			$this->dexrefid = $result->fields['dexrefid'];
			$this->parcomments = $result->fields['parcomments'];
			$this->stdenterdt = $result->fields['stdenterdt'];
			$this->stdexitdt = $result->fields['stdexitdt'];
		}

		/**
		 * Returns specified property value
		 *
		 * @param mixed $property
		 * @return mixed
		 */
		public function get($property) {
			return $this->$property;
		}

		/**
		 * Returns specified mm-dd-yyyy date property in yyyy-mm-dd
		 *
		 * @param mixed $property
		 * @return mixed
		 */
		public function getDate($property) {
			if ($this->$property != '') {
				return substr($this->$property, 6) . '-' . substr($this->$property, 0, 2) . '-' . substr($this->$property, 3, 2);
			} else {
				return '';
			}
		}

		/**
		 * Returns guardian's array
		 *
		 * @return array
		 */
		public function getGuardians() {
			$SQL = "
            SELECT TRIM(gdfnm)  as gdfnm,
                   TRIM(gdlnm)  as gdlnm,
                   TRIM(gdadr1) as gdadr1,
                   TRIM(gdadr2) as gdadr2,
                   gdhphn,
                   gdadr1,
                   gdadr2,
                   gdwplace,
                   gdwphn,
                   gdemail,
                   gtdesc,
                   COALESCE(gdcitycode_m, gdcitycode) as gdcitycode,
                   COALESCE(gdcity_m, gdcity) as gdcity,
                   COALESCE(gdstate_m, gdstate) as gdstate,
                   gdmphn,
                   adesc as gdlang
              FROM webset.sys_teacherstudentassignment ts
                   INNER JOIN webset.dmg_guardianmst grd ON grd.stdrefid = ts.stdrefid
                   LEFT OUTER JOIN webset.def_guardiantype gtype ON grd.gdtype = gtype.gtrefid
                   LEFT OUTER JOIN webset.statedef_prim_lang AS lg ON lg.refid = ghlrefid
             WHERE COALESCE(gdeddecision,'Y') = 'Y'
               AND tsrefid = " . $this->tsrefid . "
             ORDER BY seqnumber, gtrank, UPPER(gdlnm), UPPER(gdfnm)
        ";

			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns sudent's disability array
		 *
		 * @param bool $selAll
		 * @return array
		 */
		public function getDisability($selAll = false) {
			if (!$selAll) {
				$SQL = "
		            SELECT state.dccode as code,
		                   state.dcdesc as disability,
		                   CASE sdtype
		                       WHEN 1 THEN 'Primary'
		                       WHEN 2 THEN 'Secondary'
		                       WHEN 3 THEN 'Other'
		                   END as type
		              FROM webset.std_disabilitymst std
		                   INNER JOIN webset.statedef_disablingcondition state ON std.dcrefid = state.dcrefid
		             WHERE stdrefid = " . $this->tsrefid . "
		             ORDER BY sdtype, state.dccode
	            ";
			} else {
				$SQL = "
		            SELECT state.dccode as code,
		                   state.dcdesc as disability,
		                   CASE sdtype
		                       WHEN 1 THEN 'Primary'
		                       WHEN 2 THEN 'Secondary'
		                       WHEN 3 THEN 'Other'
		                   END as type,
		                   std.dcrefid
		              FROM webset.statedef_disablingcondition state
		                   LEFT JOIN webset.std_disabilitymst std ON (std.dcrefid = state.dcrefid AND stdrefid = " . $this->tsrefid . ")
	                 WHERE screfid = " . VNDState::factory()->id . "
					   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
		             ORDER BY dccode
	            ";
			}

			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns placement's array
		 *
		 * @return array
		 */
		public function getPlacement() {
			$SQL = "
            SELECT spcdesc,
                   spccode,
                   spctdesc,
                   spctcode
               FROM webset.statedef_placementcategorycode code
                   INNER JOIN webset.statedef_placementcategorycodetype typ ON code.spctrefid = typ.spctrefid
                   INNER JOIN webset.std_placementcode std ON code.spcrefid = std.spcrefid
              WHERE stdrefid = " . $this->tsrefid . "
        ";

			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Return construction
		 *
		 * @param int $id
		 * @param bool $uniqueYear
		 * @return array
		 */
		public function getConstruction($id, $uniqueYear = false) {
			$where = '';

			if ($uniqueYear === true) $where .= 'AND iepyear = ' . $this->stdiepyear;

			$SQL = "
            SELECT *
              FROM webset.std_constructions
             WHERE stdrefid = " . $this->tsrefid . "
               AND constr_id = $id
            $where
            ";

			return db::execSQL($SQL)->assoc();
		}

		/**
		 * Return construction
		 *
		 * @param int $id
		 * @param bool $iepYear
		 * @param int $type
		 * @return array
		 */
		public function getConstructionData($id, $iepYear = false, $type = 1) {
			$where = '';

			if ($iepYear === true) $where .= 'AND iepyear = ' . $this->stdiepyear;

			$codVal = db::execSQL("
                SELECT values
	              FROM webset.std_constructions
	             WHERE stdrefid =  " . $this->tsrefid . "
	               AND constr_id = $id
                    $where
            ");
			if ($type == 1) {
				$res = array();
				if ($codVal->fields["values"] != "") {
					preg_match_all("/<value name=\"(.+?)\">(.+?)<\/value>/is", base64_decode($codVal->fields["values"]), $out);
					for ($i = 0; $i < count($out[1]); $i++) $res[$out[1][$i]] = $out[2][$i];
					return $res;
				}
			} else {
				return base64_decode($codVal->fields["values"]);
			}
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @param int $stdiepyear
		 * @return IDEAStudent
		 */
		public static function factory($tsRefID, $stdiepyear = 0) {
			return new IDEAStudent($tsRefID, $stdiepyear);
		}

		/**
		 * Retrun Unique Teacher for student
		 *
		 * @return array
		 */
		public function getCaseManager() {
			if (!isset($this->caseManager)) {
				$SQL = "
				SELECT usr.umfirstname || ' ' || usr.umlastname AS cmname,
		               COALESCE(vou.vouphone, CASE usr.umphone WHEN '' THEN NULL ELSE usr.umphone END || COALESCE(' Ext.: ' || usr.umphoneext, '')) AS cmphone,
	                   stdvou.vouphone,
	                   pcu.umfirstname || ' ' || pcu.umlastname AS pcname,
	                   COALESCE(pvou.vouphone, CASE pcu.umphone WHEN '' THEN NULL ELSE pcu.umphone END || COALESCE(' Ext.: ' || pcu.umphoneext, '')) AS pcphone
				  FROM webset.sys_teacherstudentassignment ts
	                   LEFT OUTER JOIN webset.dmg_studentmst dmg ON ts.stdrefid=dmg.stdrefid
				       LEFT OUTER JOIN sys_usermst usr ON usr.umrefid = ts.umrefid
	                   LEFT OUTER JOIN sys_voumst vou ON vou.vourefid = usr.vourefid
	                   LEFT OUTER JOIN sys_voumst stdvou ON stdvou.vourefid = dmg.vourefid
	                   LEFT OUTER JOIN webset.sys_proccoordassignment pca ON pca.cmrefid = ts.umrefid
	                   LEFT OUTER JOIN webset.sys_proccoordmst pcm ON pcm.pcrefid = pca.pcrefid
	                   LEFT OUTER JOIN sys_usermst pcu ON pcu.umrefid = pcm.umrefid
	                   LEFT OUTER JOIN sys_voumst pvou ON pvou.vourefid = pcu.vourefid
	             WHERE ts.tsrefid = " . $this->tsrefid . "
	            ";

				$this->caseManager = db::execSQL($SQL)->fields;
			}

			return $this->caseManager;
		}

		/**
		 * Return summary comments/recommendations
		 *
		 * @param string $area
		 * @return array
		 */
		public function getSummaryRecommendations($area) {
			if (!isset($this->summaryRecommendations)) {
				$SQL = "
				SELECT siaitext
		          FROM webset.std_additionalinfo
		         WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	               AND COALESCE(docarea, 'A') = '$area'
				 ORDER BY siairefid
				";
				$this->summaryRecommendations = db::execSQL($SQL)->assocAll();
			}
			return $this->summaryRecommendations;
		}

		/**
		 * Return commette members
		 *
		 * @param string $area
		 * @param bool $withIEP if true select participants by IEP year
		 * @return array
		 */
		public function getCommetteMembers($area = 'A', $withIEP = true) {
			if (!isset($this->commetteMembers)) {
				$iep = "";

				if ($area != null) $area = "AND COALESCE(docarea, 'A') = '$area'";
				if ($withIEP === true) $iep = "AND iep_year = " . $this->stdiepyear . "";

				$SQL = "
				SELECT participantname,
	                   participantrole,
	                   participantatttype,
	                   std_seq_num,
	                   spirefid,
	                   partcat,
	                   signature
	              FROM webset.std_iepparticipants
	             WHERE stdrefid = " . $this->tsrefid . "
	                   $iep
	                   $area
	             ORDER BY CASE WHEN SUBSTRING(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
	            ";

				$this->commetteMembers = db::execSQL($SQL)->assocAll();
			}

			return $this->commetteMembers;
		}

		/**
		 * @param bool $withIEP
		 * @return array
		 */
		public function getPLEP($withIEP = true) {
			if (!isset($this->plep)) {

				$SQL = "
					SELECT prefid,
	                       pleadstat,
	                       prsltsstateasmnts,
	                       pbaseline,
	                       paddinfo,
	                       pstrstd,
	                       pgdconcrn,
	                       pprogprev,
	                       pmtsgened,
	                       peval,
	                       pelidg,
	                       pagedstd,
	                       piep,
	                       prcntevalrslts,
	                       pstrstd,
	                       mo_dwa,
	                       mo_bench_pages,
	                       mo_bench_desc,
	                       mo_formal
		              FROM webset.std_plepmst
		             WHERE stdrefid = " . $this->tsrefid . "
                   " . ($withIEP ? "AND iepyear = " . $this->stdiepyear : "") . "
	            ";

				$this->plep = db::execSQL($SQL)->assoc();
			}

			return $this->plep;
		}

		/**
		 * Return student Baseline/Goals/Benchmarks
		 *
		 * @param string string $esy
		 * @return array|bool
		 */
		public function getBgbGoals($esy = 'N') {
			if (!isset($this->bgbGoals)) {
				$this->bgbGoals = array();
				$SQL = "
					SELECT bl.blrefid,
					       g.grefid,
					       o.brefid as orefid,
					       " . IDEAParts::get('baselineArea') . " as subject,
					       blbaseline as baseline,
					       COALESCE(g.overridetext, gsentance) as gsentance,
					       COALESCE(o.overridetext, bsentance) as bsentance,
					       bl.order_num as bl_num,
					       g.order_num as g_num,
					       o.order_num as b_num,
					       mo_domains,
	                       mo_comments,
	                       TO_CHAR(g.gdate, 'MM-DD-YYYY') AS gdate,
	                       o.in_support
					  FROM webset.std_bgb_baseline bl
					       INNER JOIN webset.std_bgb_goal g ON g.blrefid = bl.blrefid
					       LEFT OUTER JOIN webset.std_bgb_benchmark o ON o.grefid = g.grefid
					       LEFT OUTER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON bl.blksa = ksa.gdskrefid
					       LEFT OUTER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
					       LEFT OUTER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
					 WHERE bl.stdrefid = " . $this->tsrefid . "
					   AND bl.siymrefid = " . $this->stdiepyear . "
					   AND bl.esy = '" . $esy . "'
					 ORDER BY bl.order_num, bl.blrefid, g.order_num, g.grefid, o.order_num, o.brefid
	            ";
				$this->bgbGoals = $this->goalsArrayPrepare(db::execSQL($SQL)->assocAll());
			}
			return $this->bgbGoals;
		}

		/**
		 * Prepares goals array with nested objectives array
		 *
		 * @param array $data
		 * @return array
		 */
		private function goalsArrayPrepare($data) {
			$result = array();
			$blrefid = null;
			$bl_count = 0;
			$grefid = null;
			$goal = array();
			foreach ($data as $key => $record) {
				if ($blrefid != $record['blrefid']) {
					$bl_count++;
				}
				if ($grefid != $record['grefid']) {
					$goal = array();
					$goal['objectives'] = array();
					$goal['grefid'] = $record['grefid'];
					$goal['blrefid'] = $record['blrefid'];
					$goal['baseline'] = $record['baseline'];
					$goal['subject'] = $record['subject'];
					$goal['gsentance'] = $record['gsentance'];
					$goal['mo_domains'] = $record['mo_domains'];
					$goal['mo_comments'] = $record['mo_comments'];
					$goal['gdate'] = $record['gdate'];
					$goal['bl_num'] = $record['bl_num'] == '' ? $bl_count : $record['bl_num'];
					$goal['g_num'] = $goal['bl_num'] . '.' . ($record['g_num'] == '' ? (count($result) + 1) : $record['g_num']);
				}
				if ($record['orefid'] > 0) {
					$objective = array();
					$objective['orefid'] = $record['orefid'];
					$objective['bsentance'] = $record['bsentance'];
					$objective['in_support'] = $record['in_support'];
					$objective['b_num'] = $record['b_num'] == '' ? (count($goal['objectives']) + 1) : $record['b_num'];
					$objective['b_num_goal'] = $goal['g_num'] . '.' . $objective['b_num'];
					$goal['objectives'][] = $objective;
				}
				if (($grefid != $record['grefid'])) {
					$result[] = $goal;
				} else {
					$result[count($result) - 1] = $goal;
				}
				$grefid = $record['grefid'];
				$blrefid = $record['blrefid'];
			}
			return $result;
		}

		/**
		 * Returns Progress Report for BGB Goals data
		 *
		 * @param string $esy
		 * @return array
		 */
		public function getProgressReportBGB($esy = 'N') {
			if (!isset($this->progressReportBGB)) {
				$this->progressReportBGB = array();
				$goals = $this->getBgbGoals($esy);

				$progresses = $this->db->execute("
					SELECT sprnarative,
					       stdgoalrefid,
					       stdbenchmarkrefid,
					       sprmarkingprd,
					       dsyrefid,
					       epsdesc
	                  FROM webset.std_progressreportmst std
	                       INNER JOIN webset.disdef_progressrepext ext ON ext.eprefid = std.eprefid
	                 WHERE stdrefid =  " . $this->tsrefid . "
	            ")->assocAll();

				$iepyear = IDEAStudentIEPYear::factory($this->stdiepyear);
				$periods = IDEASchool::factory($this->get('vourefid'))
					->getMarkingPeriods(
						$iepyear->get(IDEAStudentIEPYear::F_BEG_DATE),
						$iepyear->get(IDEAStudentIEPYear::F_END_DATE)
					);
				foreach ($goals as $goal) {
					//Goal Line
					$line = array();
					$line['grefid'] = $goal['grefid'];
					$line['orefid'] = '';
					$line['goal'] = 'Goal ' . $goal['g_num'] . '. ' . $goal['gsentance'];
					$line['objective'] = '';
					foreach ($periods as $period) {

						$period['value'] = '';
						$period['narrative'] = '';
						foreach ($progresses as $progress) {
							if ($progress['dsyrefid'] == $period['dsyrefid'] &&
								$progress['sprmarkingprd'] == $period['bmnum'] &&
								$progress['stdgoalrefid'] == $goal['grefid'] &&
								$progress['stdbenchmarkrefid'] == ''
							) {
								$period['value'] = $progress['epsdesc'];
								$period['narrative'] = $progress['sprnarative'];
								break;
							}
						}
						$line['periods'][] = $period;
					}
					$this->progressReportBGB[] = $line;

					//Attached Objectives Lines
					foreach ($goal['objectives'] as $objective) {
						$line = array();
						$line['grefid'] = '';
						$line['brefid'] = $objective['orefid'];
						$line['goal'] = '';
						$line['objective'] = 'Objective ' . $objective['b_num_goal'] . '. ' . $objective['bsentance'];

						foreach ($periods as $period) {
							$period['value'] = '';
							$period['narrative'] = '';
							foreach ($progresses as $progress) {
								if ($progress['dsyrefid'] == $period['dsyrefid'] &&
									$progress['sprmarkingprd'] == $period['bmnum'] &&
									$progress['stdgoalrefid'] == $goal['grefid'] &&
									$progress['stdbenchmarkrefid'] == $objective['orefid']
								) {
									$period['value'] = $progress['epsdesc'];
									$period['narrative'] = $progress['sprnarative'];
									break;
								}
							}
							$line['periods'][] = $period;
						}
						$this->progressReportBGB[] = $line;
					}
				}
			}
			return $this->progressReportBGB;
		}

		/**
		 * Returns Simple Progress Report for BGB Goals data
		 *
		 * @param string $esy
		 * @param int $vourefid
		 * @return array
		 */
		public function getProgressReportSimpleBGB($esy = 'N', $vourefid = null) {
			if (!isset($this->progressReportSimpleBGB)) {
				if (!$vourefid) {
					$vourefid = $this->get('vourefid');
				}
				$this->progressReportSimpleBGB = array();
				$goals = $this->getBgbGoals($esy);
				$periods = IDEADistrict::factory(SystemCore::$VndRefID)->getMarkingPeriodsSimple($esy);
				$extents = IDEADistrict::factory(SystemCore::$VndRefID)->getProgressExtents(true);
				$options = json_decode(IDEAFormat::getIniOptions('bgb'), true);

				$extents_keyed = array();
				foreach ($extents as $extent) {
					/** @var IDEADistrictProgressExtent $extent */
					$extents_keyed[$extent->get(IDEADistrictProgressExtent::F_REFID)] = $extent->get(IDEADistrictProgressExtent::F_CODE);
				}
				foreach ($goals as $goal) {
					//Goal Line
					$line = array();
					$line['grefid'] = $goal['grefid'];
					$line['brefid'] = '';
					$line['goal'] = 'Goal ' . $goal['g_num'] . '. ' . $goal['gsentance'];
					$line['objective'] = '';
					$progress = $this->db->execute("
						SELECT spr_period_data
		                  FROM webset.std_progress_reporting
		                 WHERE sbg_grefid =  " . $goal['grefid'] . "
		            ")->getOne();
					$spr_refid = $this->db->execute("
						SELECT spr_refid
		                  FROM webset.std_progress_reporting
		                 WHERE sbg_grefid =  " . $goal['grefid'] . "
		            ")->getOne();
					$line['id'] = $spr_refid;
					$line['period_data'] = $progress;
					if ($progress) $progress = json_decode($progress, true);

					foreach ($periods as $period) {
						$column = array();
						$column['bm'] = $period['smp_period'];
						$column['smp_refid'] = $period['smp_refid'];
						$column['value'] = '';
						$column['narrative'] = '';
						$column['bmbgdt'] = '';
						$column['bmendt'] = '';
						$column['dsydesc'] = '';
						if (isset($progress[$period['smp_refid']])) {
							$column['value'] = $extents_keyed[$progress[$period['smp_refid']]['extentProgress']];
							$column['narrative'] = $progress[$period['smp_refid']]['narrative'];
						}
						$line['periods'][] = $column;
					}
					$this->progressReportSimpleBGB[] = $line;

					//Attached Objectives Lines
					foreach ($goal['objectives'] as $objective) {
						$line = array();
						$line['goal'] = '';
						$line['grefid'] = '';
						$line['brefid'] = $objective['orefid'];
						$line['objective'] = $options['benchmark'] . ' ' . $objective['b_num_goal'] . '. ' . $objective['bsentance'];

						$progress = $this->db->execute("
							SELECT spr_period_data
			                  FROM webset.std_progress_reporting
			                 WHERE sbb_brefid =  " . $objective['orefid'] . "
			            ")->getOne();
						$spr_refid = $this->db->execute("
							SELECT spr_refid
			                  FROM webset.std_progress_reporting
			                 WHERE sbb_brefid =  " . $objective['orefid'] . "
			            ")->getOne();
						$line['id'] = $spr_refid;
						$line['period_data'] = $progress;
						if ($progress) $progress = json_decode($progress, true);
						foreach ($periods as $period) {
							$column = array();
							$column['bm'] = $period['smp_period'];
							$column['value'] = '';
							$column['narrative'] = '';
							$column['bmbgdt'] = '';
							$column['bmendt'] = '';
							$column['dsydesc'] = '';
							if (isset($progress[$period['smp_refid']])) {
								$column['value'] = $extents_keyed[$progress[$period['smp_refid']]['extentProgress']];
								$column['narrative'] = $progress[$period['smp_refid']]['narrative'];
							}
							$line['periods'][] = $column;
						}
						$this->progressReportSimpleBGB[] = $line;
					}
				}
			}
			return $this->progressReportSimpleBGB;
		}

		/**
		 * Return Special Considerations: Federal and State Requirements
		 */
		public function getSpecConsiderations() {
			if (!isset($this->specialConsiderations)) {
				$questions = db::execSQL("
					SELECT quest.scmrefid,
					       quest.scmquestion,
					       quest.scmlinksw
					  FROM webset.statedef_spconsid_quest quest
					 WHERE quest.screfid = " . VNDState::factory()->id . "
	                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
					 ORDER BY quest.seqnum
	            ")->assocAll();

				$result = array();
				$i = 0;
				foreach ($questions as $qst) {
					$result[$i] = $qst;
					$answers = db::execSQL("
						SELECT answ.scanswer || COALESCE(' <i>' || std.sscmnarrative || '</i>', '') || coalesce(' <i><b>' || 'Attached ' || mfcdoctitle || '</b></i>', '') as scanswer,
			                   CASE std.scarefid > 0 WHEN TRUE THEN 'yes' END as checked,
			                   std.scarefid as std_ans_id,
			                   answ.scarefid as stt_ans_id
			              FROM webset.statedef_spconsid_answ answ
			                   LEFT JOIN webset.statedef_spconsid_quest quest ON quest.scmrefid = answ.scmrefid
			                   LEFT OUTER JOIN webset.std_spconsid std ON (std.scarefid = answ.scarefid AND std.stdrefid = " . $this->tsrefid . " AND syrefid = " . $this->stdiepyear . ")
		                       LEFT OUTER JOIN webset.statedef_forms forms ON answ.formrefid = mfcrefid AND mfcrefid in (SELECT webset.std_forms.mfcrefid
		                                                                                                                   FROM webset.std_forms
		                                                                                                                  WHERE stdrefid = " . $this->tsrefid . ")
		                 WHERE quest.screfid = " . VNDState::factory()->id . "
		                   AND quest.scmrefid = " . $qst['scmrefid'] . "
		                 ORDER BY quest.seqnum, quest.scmsdesc, order_num, scanswer
					")->assocAll();
					$result[$i]['answ'] = $answers;
					$i++;
				}
				$this->specialConsiderations = $result;
			}

			return $this->specialConsiderations;
		}

		/**
		 * Return Special Considerations: Federal and State Requirements
		 */
		public function getProgressReporting() {
			if (!isset($this->progressReporting)) {
				$result = db::execSQL("
					SELECT state.fprrefid,
						   std.sfprrefid,
						   state.fprdesc,
						   other_desc
	                  FROM webset.statedef_freqprogrep state
	                  	   LEFT JOIN webset.std_freqprogrep std ON (std.fprrefid = state.fprrefid AND std.stdrefid = " . $this->tsrefid . ")
	                 WHERE screfid = " . VNDState::factory()->id . "
	                   AND COALESCE(onlythisvnd,'" . SystemCore::$VndName . "') like '%" . SystemCore::$VndName . "%'
	                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	                 ORDER BY order_num
				")->assocAll();

				$this->progressReporting = $result;
			}

			return $this->progressReporting;
		}

	}

?>
