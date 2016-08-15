<?php

	/**
	 * Contains basic student Demo and Sp Ed data specific for state of TX
	 * @author Nick Ignatushko <nick@lumentouch.com>
	 *
	 * @copyright Lumen Touch, 2013.
	 * Updated 16-01-2014 by Ganchar Danila
	 */
	class IDEAStudentTX extends IDEAStudent {

		/**
		 * Sp Ed Enrollment Student Lumen ID
		 * DB Table: webset.sys_teacherstudentassignment
		 *
		 * @var array
		 */
		protected $languages;

		/**
		 * Possible purposes(id, title). Get from db.
		 *
		 * @var array
		 */
		protected $meetPurposes;

		/**
		 * Selected purposes for current student
		 *
		 * @var array
		 */
		protected $meetPurposesSelected;

		public function __construct($tsRefID = 0, $stdiepyear = 0) {
			parent::__construct($tsRefID, $stdiepyear);
		}

		/**
		 * Returns Instructional Arrangements (placement's)
		 *
		 * @return array
		 */
		public function getPlacement() {
			$SQL = "
				SELECT period_dt,
				       COALESCE(vouname, school_camp) AS campus,
					   spccode,
					   spcdesc,
					   camp_attend,
					   camp_attend_no,
					   camp_close,
					   camp_close_no,
					   instruct_day,
					   instruct_day_no,
					   crtdesc,
					   spc.validvalue as speechcode,
					   ppcd.validvalue as ppcdcode
				  FROM webset_tx.std_instruct_arrange std
					   LEFT OUTER JOIN webset.statedef_placementcategorycode ON spcrefid = placement
					   LEFT OUTER JOIN sys_voumst ON vourefid = campus_id
					   LEFT OUTER JOIN webset.disdef_location ON crtrefid = location
					   LEFT OUTER JOIN webset.glb_validvalues ppcd ON ppcd.refid = std.ppcdind
					   LEFT OUTER JOIN webset.glb_validvalues spc ON spc.refid = speechind
				 WHERE std_refid = " . $this->tsrefid . "
				 ORDER BY COALESCE(std.period_dt,std.lastupdate)
            ";
			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns Basic Sp Ed Dates
		 *
		 * @return array
		 */
		public function getRad() {
			$SQL = "
				SELECT stdiepmeetingdt,
                       stdenrolldt,
                       stdcmpltdt,
                       stdevaldt,
                       stdtriennialdt,
                       stddraftiepcopydt,
                       stdiepcopydt,
                       longard,
                       briefard,
                       dts.amendment,
                       inituni,
                       assistive,
                       fba,
                       fve,
                       relatedasm,
                       related,
                       speach,
                       transition,
                       other_desc,
                       other,
                       addcomments,
                       seccode,
                       stdexitdt
                  FROM webset.sys_teacherstudentassignment ts
                       LEFT OUTER JOIN webset_tx.std_dates dts ON tsrefid = dts.stdrefid AND iepyear = " . $this->stdiepyear . "
                       LEFT OUTER JOIN webset.statedef_exitcategories edef on edef.secrefid = exitrefid
		         WHERE tsRefID =  " . $this->tsrefid . "
            ";
			return $this->execSQL($SQL)->assoc();
		}

		/**
		 * Returns Meetings Purposes
		 *
		 * @return array
		 */
		public function getMeetPurposes() {
			if (!isset($this->meetPurposes)) {
				$SQL = "
					SELECT refid,
	                       adesc
		              FROM webset_tx.def_meetpurpose
		             WHERE (enddate IS NULL or now()< enddate)
		             ORDER BY seqnum, adesc
	            ";

				$this->meetPurposes = db::execSQL($SQL)->assocAll();
			}

			return $this->meetPurposes;
		}

		/**
		 * Returns Selected Purposes|Purpose for current student
		 *
		 * @param null|string $key
		 * @return mixed
		 */
		public function getMeetPurposesSelected($key = null) {
			if (!isset($this->meetPurposesSelected)) {
				$SQL = "
					SELECT type_report,
		                   type_iep,
		                   type_iep_other
	                  FROM webset_tx.std_meet_purpose
	                 WHERE stdrefid = " . $this->tsrefid . "
	                   AND iepyear = " . $this->stdiepyear . "
	            ";

				$this->meetPurposesSelected = db::execSQL($SQL)->assoc();
			}

			if ($key == null) {
				return $this->meetPurposesSelected;
			} else {
				return $this->meetPurposesSelected[$key];
			}
		}

		/**
		 * Returns Related Services
		 *
		 * @return array
		 */
		public function getRelatedServices() {
			$SQL = "
				SELECT refid,
					   CASE WHEN service like 'Other%' THEN COALESCE(serv_other, '') ELSE service END AS service,
					   CASE WHEN frequency like 'Other%' THEN COALESCE(freq_other, '') ELSE frequency END || ' ' ||
					   CASE WHEN dur.duration like 'Other%' THEN COALESCE(duration_oth, '') ELSE dur.duration END AS frequency,
					   CASE WHEN location like 'Other%' THEN COALESCE(loc_other, '') ELSE location END AS location
				  FROM webset_tx.std_srv_related std
					   INNER JOIN webset_tx.def_srv_related rel ON rrefid = srefid
					   INNER JOIN webset_tx.def_srv_frequency freq ON freq.frefid = std.freq
					   INNER JOIN webset_tx.def_srv_duration dur ON dur.drefid = std.duration
					   INNER JOIN webset_tx.def_srv_locations loc ON loc.lrefid = std.loc
				 WHERE stdrefid =  " . $this->tsrefid . "
				   AND iep_year = " . $this->stdiepyear . "
				 ORDER BY 1, rel.seqnum
            ";
			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns Programm Interventions
		 *
		 * @param int $id
		 * @return array
		 */
		public function getProgramInterventions($id = 0) {
			$SQL = "
				SELECT sub_mod_refid,
					   mod_desc,
					   sub_mod_desc,
					   mst.seqnum,
					   dtl.seqnum,
					   'S' as mode
				  FROM webset_tx.def_pi_modifications_mst mst
					   INNER JOIN webset_tx.def_pi_modifications_dtl dtl ON dtl.mod_refid = mst.mod_refid
				 WHERE (mst.end_date>now() OR mst.end_date IS NULL)
				   AND area_id = " . $id . "
				 ORDER BY 4, 5, 3
            ";
			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns Programm Intervention Subjects
		 *
		 * @return array
		 */
		public function getProgramInterventSubjects() {
			$SQL = "
				SELECT sub_refid AS srefid,
                       COALESCE(sub_print, sub_desc) AS sname
                  FROM webset_tx.def_pi_subjects
                 WHERE (end_date>now() OR end_date IS NULL)
                   AND COALESCE(vndrefid, VNDREFID) = VNDREFID
                 ORDER BY seqnum
            ";
			return $this->execSQL($SQL)->assocAll();
		}

		/**
		 * Returns Programm Intervention Area
		 *
		 * @param int $id
		 * @return array
		 */
		public function getProgramInterventArea($id = 0) {
			$SQL = "
				SELECT *
				  FROM webset_tx.def_pi_modifications_area
  			     WHERE arefid = " . $id . "
            ";
			return $this->execSQL($SQL)->assoc();
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @param int $stdiepyear
		 * @return IDEAStudentTX
		 */
		public static function factory($tsRefID, $stdiepyear = 0) {
			return new IDEAStudentTX($tsRefID, $stdiepyear);
		}

		/**
		 * Return information about languages student
		 *
		 * @param string $name
		 * @return array
		 */
		public function getLanguages($name) {
			if (!$this->languages) {
				$SQL = "
				SELECT teachername,
	                   dominant_language,
	                   interpreter_used,
	                   interpreter_mode,
	                   writing_translate,
	                   audio_tape
	              FROM webset_tx.std_meet_language
	             WHERE stdrefid = " . $this->tsrefid . "
		           AND iepyear = " . $this->stdiepyear . "
			";

			$this->languages = db::execSQL($SQL)->assoc();
			}

			return $this->languages[$name];
		}

	}

?>
