<?php

	/**
	 * Contains basic district
	 *
	 * @copyright Lumen Touch, 2012
	 */
	class IDEADistrict extends RegularClass {

		/**
		 * District ID
		 * DB Table: public.sys_vndmst
		 *
		 * @var int
		 */
		protected $vndrefid;

		/**
		 * Initializes basic properties
		 *
		 * @param null $vndrefid
		 * @throws Exception
		 */
		public function __construct($vndrefid = null) {
			if ($vndrefid == null) {
				throw new Exception('District ID has not been specified.');
			}
			$this->vndrefid = $vndrefid;
		}

		/**
		 * Returns Distrcit Grades
		 *
		 * @return array
		 */
		public function getGrades() {
			$SQL = "
			    SELECT gl_refid,
			           gl_code
			      FROM c_manager.def_grade_levels
				 WHERE vndrefid = " . $this->vndrefid . "
			     ORDER BY gl_numeric_value, gl_code
            ";
			return $this->execSQL($SQL)->indexAllKeyed();
		}

		/**
		 * Returns Distrcit School Year
		 *
		 * @return array
		 */
		public function getSchoolYears() {
			$SQL = "
				SELECT dsyrefid,
					   dsydesc,
					   dsybgdt,
					   dsyendt
				  FROM webset.disdef_schoolyear
				 WHERE vndrefid = " . $this->vndrefid . "
				 ORDER BY dsybgdt
            ";

			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns list of the instances of IDEADistrictProgressExtent.
		 * Output array format:
		 *       [
		 *               <IDEADistrictProgressExtent>,
		 *               <IDEADistrictProgressExtent>,
		 *               ....
		 *       ]
		 *
		 * @return array.<IDEADistrictProgressExtent>
		 */
		public function getProgressExtents() {
			return IDEADistrictProgressExtent::createInstances(
				$this->execSQL("
                    SELECT eprefid
                      FROM webset.disdef_progressrepext
                     WHERE vndrefid = " . $this->vndrefid . "
                     ORDER BY epseq, epsdesc
                ")->indexCol(),
				$this->db
			);
		}

		/**
		 * Select Field with Sp Ed Exit Codes
		 *
		 * @return array
		 */
		public function getExitCodes() {
			$SQL = "
			    SELECT dexrefid,
                       COALESCE(dexcode || ' - ','') || dexdesc AS exitcode
                  FROM webset.disdef_exit_codes district
                       LEFT OUTER JOIN webset.statedef_exitcategories state ON state.secrefid = district.statecode_id
                 WHERE vndrefid = VNDREFID
                   AND (state.recdeactivationdt IS NULL OR now()<state.recdeactivationdt)
                   AND (district.enddate IS NULL OR now()<district.enddate)
                 ORDER BY seqnum, dexcode
            ";
			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns Simple Marking Periods
		 *
		 * @return array
		 */
		public function getMarkingPeriodsSimple($esy = 'N') {

			return $this->execSQL("
		        SELECT smp_refid,
		               smp_period
		          FROM webset.sch_marking_period
		         WHERE vndrefid = VNDREFID
		           AND COALESCE(smp_active, 'N') = 'Y'
		           AND COALESCE(esy, 'N') = '" . $esy . "'
		         ORDER BY smp_sequens, smp_period
			")->assocAll();

		}

		/**
		 * Creates an instance of this class
		 *
		 * @param null $vndrefid
		 * @return IDEADistrict
		 */
		public static function factory($vndrefid = null) {
			return new IDEADistrict($vndrefid);
		}

	}

?>
