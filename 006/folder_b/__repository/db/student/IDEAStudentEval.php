<?php

	/**
	 * Include SQL-queries for Evaluation
	 *
	 * @author Alex Kalevich
	 * Created 19-03-2015
	 */
	class IDEAStudentEval extends IDEAStudent {

		/**
		 * Eval Process ID
		 * DB Table: webset.es_std_evalproc
		 *
		 * @var int
		 */
		protected $evalproc_id;

		/**
		 * Eval Process Type
		 * DB Table: webset.es_std_evalproc
		 *
		 * @var string
		 */
		protected $evalproc_type;

		/**
		 * Eval Process Start Date
		 * DB Table: webset.es_std_evalproc
		 *
		 * @var varchar
		 */
		protected $evalproc_date_start;

		/**
		 * webset.es_std_join ID
		 *
		 * @var string
		 */
		protected $esrefid;

		/**
		 * RED Summary
		 *
		 * @var array
		 */
		protected $red_summary;

		/**
		 * ER Procedures
		 *
		 * @var array
		 */
		protected $er_procedures;

		/**
		 * ER Results
		 *
		 * @var array
		 */
		protected $er_results;

		/**
		 * ER Observation
		 *
		 * @var array
		 */
		protected $er_observation;

		/**
		 * General Information
		 *
		 * @var array
		 */
		protected $gen_info;

		/**
		 * Case History
		 *
		 * @var array
		 */
		protected $case_hist;

		/**
		 * Team Conclusions and Decisions
		 *
		 * @var array
		 */
		protected $team_concl;

		/**
		 * Participants
		 *
		 * @var array
		 */
		protected $participants;

		/**
		 * SLD Member
		 *
		 * @var array
		 */
		protected $sld_member;

		/**
		 * Provider Copy
		 *
		 * @var array
		 */
		protected $prov_copy;

		public function __construct($tsRefID = 0, $evalproc_id = 0) {
			parent::__construct($tsRefID);
			$ep_info = $this->execSQL("
				SELECT eprefid,
					   date_start,
					   essrtdescription
				  FROM webset.es_std_evalproc AS ep
					   INNER JOIN webset.es_statedef_reporttype ON essrtrefid = ev_type
				 WHERE stdrefid = " . $tsRefID . "
				   AND " . ($evalproc_id > 0 ? "ep.eprefid = " . $evalproc_id : "ep_current_sw = 'Y'") . "
				")->assoc();
			$this->evalproc_id = $ep_info['eprefid'];
			$this->evalproc_date_start = $ep_info['date_start'];
			$this->evalproc_type = $ep_info['essrtdescription'];
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEAStudentEval
		 */
		public static function factory($tsRefID, $evalproc_id = 0) {
			return new IDEAStudentEval($tsRefID, $evalproc_id);
		}

		/**
		 * Return construction
		 *
		 * @param int $id
		 * @param bool $filter_evalproc
		 * @param int $type
		 * @return array
		 */
		public function getConstructionData($id, $filter_evalproc = true, $type = 1) {
			$where = '';

			if ($filter_evalproc === true) $where .= 'AND evalproc_id = ' . $this->evalproc_id;

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
		 * Return group of constructions
		 *
		 * @param int $group_id
		 * @param bool $filter_evalproc
		 * @param int $type
		 * @return array
		 */
		public function getConstructionGroupData($group_id, $filter_evalproc = true, $type = 1) {
			$where = '';

			if ($filter_evalproc === true) $where .= 'AND evalproc_id = ' . $this->evalproc_id;

			$data = db::execSQL("
                SELECT values,
                       std.constr_id
                  FROM webset.std_constructions AS std
                       INNER JOIN webset.sped_constructions AS def ON std.constr_id = def.cnrefid
                 WHERE stdrefid = " . $this->tsrefid . "
                   AND group_id = $group_id
                    $where
                 ORDER BY order_num
            ")->assocAll();
			foreach ($data as $key => $record) {
				if ($type == 1) {
					$res = array();
					if ($data->fields["values"] != "") {
						preg_match_all("/<value name=\"(.+?)\">(.+?)<\/value>/is", base64_decode($record["values"]), $out);
						for ($i = 0; $i < count($out[1]); $i++) $res[$out[1][$i]] = $out[2][$i];
						$data[$key]['values'] = $res;
					}
				} else {
					$data[$key]['values'] = base64_decode($record["values"]);
				}
			}
			return $data;
		}

		/**
		 * Get RED Summary
		 *
		 * @return array|string
		 */
		public function getREDSummary() {
			if (!isset($this->red_summary)) {
				$this->red_summary = $this->execSQL("
				   SELECT screening.scrrefid,
				          screening.scrdesc,
				          red.redrefid,
				          red.red_desc,
				          red.red_text,
				          red.plafp,
				          red.skill,
				          red.lastuser,
				          red.lastupdate
				     FROM webset.es_std_red red
				          INNER JOIN webset.es_statedef_screeningtype screening ON red.screening_id = screening.scrrefid
				    WHERE stdrefid = " . $this->tsrefid . "
				      AND evalproc_id = " . $this->evalproc_id . "
				    ORDER BY screening.scrseq
				")->assocAllKeyed(0);
			}
			return $this->red_summary;
		}

		public function getGenralInfo() {
			$this->gen_info = db::execSQL("
				SELECT report_type,
				       lep_sw,
				       refferal_dt,
				       red_dt,
				       consent_dt,
				       eligibility_dt,
				       timiline_sw,
				       timiline_no,
				       reffered_by,
				       reffered_role,
				       stdname,
				       stddob,
				       stdage,
				       stdgrade,
				       stdschool,
				       stdparent,
				       stdphone,
				       stdaddress,
				       stdlang,
				       stdcmanager
				  FROM webset.es_std_er_generalinfo AS gi
				 WHERE gi.eprefid = $this->evalproc_id
			")->fields;

			return $this->gen_info;
		}

		public function getCaseHistory() {
			$this->case_hist = db::execSQL("
				SELECT concerns,
				 	   interventions,
				 	   school_history,
				 	   family_history
				  FROM webset.es_std_er_casehistory AS cs
				 WHERE cs.eprefid = $this->evalproc_id
			")->fields;

			return $this->case_hist;
		}

		/**
		 * Get ER Procedures/Asssessments Data
		 *
		 * @return array|string
		 */
		public function getERProcedures() {
			if (!isset($this->er_procedures)) {
				$this->er_procedures = $this->execSQL("
					SELECT scr.scrdesc AS area,
					       CASE WHEN hspdesc ILIKE '%other%' THEN COALESCE(test_name, hspdesc) ELSE hspdesc END AS procedure_name,
					       ass.xml_test AS procedure_template,
					       std.shsddate AS assessment_date,
					       std.screener AS assessment_person,
					       std.xml_data AS assessment_data,
					       std.location AS assessment_location,
					       CASE WHEN hspdesc ILIKE '%other%' THEN 1 ELSE 2 END AS flag_other
					  FROM webset.es_std_scr std
					       INNER JOIN webset.es_scr_disdef_proc ass ON std.hsprefid = ass.hsprefid
					       INNER JOIN webset.es_statedef_screeningtype AS scr ON scr.scrrefid = ass.screenid
					 WHERE std.stdrefid = " . $this->tsrefid . "
					   AND std.eprefid = " . $this->evalproc_id . "
					   AND std.archived IS NULL
					 ORDER BY scr.scrseq, order_num
				")->assocAll();
				foreach ($this->er_procedures AS $i => $procedure) {
					$this->er_procedures[$i]['assessment_data'] = base64_decode($procedure['assessment_data']);
					$this->er_procedures[$i]['procedure_template'] = preg_replace('/<pagebreak[^>]*>/', '<line><section></section></line>', $procedure['procedure_template']);
				}
			}
			return $this->er_procedures;
		}

		/**
		 * Get ER Results Data
		 *
		 * @return array|string
		 */
		public function getERResults() {
			if (!isset($this->er_results)) {
				$this->er_results = $this->execSQL("
					SELECT scr.scrrefid AS screening_id,
					       scr.scrdesc AS area,
					       scr.scrlongdesc AS description,
					       std.screen_summary AS eval_summary,
					       std.further_assess_needed_sw,
					       std.include_red_sw
					  FROM webset.es_std_join AS std
					       INNER JOIN webset.es_statedef_screeningtype AS scr ON scr.scrrefid = std.screening_id
					 WHERE eprefid = " . $this->evalproc_id . "
					 ORDER BY scr.scrseq
				")->assocAll();
			}
			return $this->er_results;
		}

		public function getTeamConcl() {
			$this->team_concl = db::execSQL("
				SELECT was_assess_sw,
				 	   disability_confirmed_sw,
				 	   eldesc,
				 	   array_to_string(
				           ARRAY(
				            SELECT s.elsdesc
				              FROM webset.es_statedef_eligibility_sub AS s
				             WHERE ',' || cn.disability_text || ',' LIKE '%,' || elsrefid::varchar || ',%'
				           ),
				           ', '
				       ) AS disability_text,
				 	   disability_affect_sw,
				 	   sped_needed_sw,
				 	   basis_determination,
				 	   lack_instruction_read_sw,
				 	   lack_instruction_math_sw,
				 	   lep_sw,
				 	   other_factors_sw,
				 	   other_factors_text,
				 	   medical_finding_no_sw,
				 	   medical_finding_yes_sw,
				 	   medical_finding_are,
				 	   suggestions
				  FROM webset.es_std_er_conclusions AS cn
					   LEFT JOIN webset.es_statedef_eligibility AS eg ON (eg.elrefid = cn.disability_id)
				 WHERE cn.eprefid = $this->evalproc_id
			")->fields;

			return $this->team_concl;
		}

		/**
		 * Get ER Observation
		 *
		 * @return array|string
		 */
		public function getERObservation() {
			if (!isset($this->er_observation)) {
				$this->er_observation = $this->execSQL("
					SELECT summary,
						   observer,
						   role,
						   location,
						   TO_CHAR(date, 'MM-DD-YYYY') AS date,
						   time,
						   activities_type,
						   array_to_string(
				           ARRAY(
					            SELECT validvalue || CASE WHEN conductedoth != '' AND validvalue LIKE 'Other' THEN ': ' || conductedoth ELSE '' END
					              FROM webset.glb_validvalues AS s
					             WHERE ',' || conducted || ',' LIKE '%,' || refid::VARCHAR || ',%'
					               AND valuename = 'EVAL_Observation'
					           ),
				               ', '
			               ) AS conducted_val
					  FROM webset.es_std_er_observation AS obs
					 WHERE eprefid  = " . $this->evalproc_id . "
					 ORDER BY order_num
				")->assocAll();
			}
			return $this->er_observation;
		}

		public function getParticipants() {
			$this->sld_member = db::execSQL("
				SELECT part_name,
	                   CASE WHEN lower(role) LIKE '%other%' THEN COALESCE(pt.part_role_oth, role) ELSE role END AS part_role,
	                   dissent_attached_sw
	              FROM webset.es_std_er_participants AS pt
					   INNER JOIN webset.es_statedef_red_part AS spt ON (pt.part_role_id = spt.refid)
	             WHERE eprefid = $this->evalproc_id
	             ORDER BY spt.seq, 1
			")->assocAll();

			return $this->sld_member;
		}

		public function getSLDMember() {
			$this->sld_member = db::execSQL("
				SELECT regular_prof_sw,
					   regular_prof_namerole,
					   regular_edu_teacher,
					   regular_edu_classroom,
					   regular_edu_ind,
					   regular_prof_agree,
					   edu_initials,
					   assess_prof_sw,
					   assess_prof_namerole,
					   assess_prof_agree,
					   prof_initials,
					   assess_qual_sw,
					   assess_qual_namerole,
					   assess_qual_role,
					   assess_qual_agree,
					   qual_initials,
					   assess_qual_namerole_sec,
					   assess_qual_role_sec,
					   assess_qual_agree_sec,
					   qual_initials_sec
	              FROM webset.es_std_er_participants_sld AS pt
	             WHERE eprefid = $this->evalproc_id
			")->fields;

			return $this->sld_member;
		}

		public function getProvideCopy() {
			$this->prov_copy = db::execSQL("
				SELECT nametitle,
	                   date_provided
	              FROM webset.es_std_er_providecopy AS pt
	             WHERE eprefid = $this->evalproc_id
			")->fields;

			return $this->prov_copy;
		}

		public function getCurEvalProc() {
			return $this->execSQL("
				SELECT eprefid
				  FROM webset.es_std_evalproc
				 WHERE stdrefid = $this->tsrefid
				   AND ep_current_sw = 'Y'
			")->getOne();
		}

	}
