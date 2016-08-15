<?php

	/**
	 * IDEAStudentCT.php
	 * Include SQL-queries for CT State
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 04-03-2014
	 */
	class IDEAStudentCT extends IDEAStudent {

		/**
		 * Recomendations and Planning for Student
		 *
		 * @var array
		 */
		protected $recommendationsAndPlanning;

		/**
		 * Recomendations and Planning for Student
		 *
		 * @var array
		 */
		protected $accommodation;

		/**
		 * Recomendations and Planning for Student
		 *
		 * @var array
		 */
		protected $testAccommodation;

		/**
		 * Accommodation category
		 *
		 * @var array
		 */
		protected $accommodationProgs;

		/**
		 * Data about next year school, next grade, next home school
		 *
		 * @var array
		 */
		protected $nextYearData;

		/**
		 * PLAAFP data
		 *
		 * @var array
		 */
		protected $plaafp;

		/**
		 * Transition data
		 *
		 * @var array
		 */
		protected $transitions = array(
			'stdage' => null,
			'invited' => null,
			'attended' => null,
			'interests' => null,
			'interests_other' => null,
			'interests_summary' => null,
			'assessments' => null,
			'agencies_invited' => null,
			'agencies_attended' => null,
			'agency_agreed' => null,
			'agency_agreed_other' => null,
			'agencies_invited_other' => null,
			'post_school_edu' => null,
			'post_school_edu_sw' => null,
			'post_school_emp' => null,
			'post_school_emp_sw' => null,
			'post_school_liv' => null,
			'post_school_liv_sw' => null,
			'course_study' => null,
			'course_other' => null,
			'rights' => null,
			'sop_date' => null
		);

		/**
		 * Testing data
		 *
		 * @var array
		 */
		protected $testing = array(
			'grade' => null,
			'assessment' => null,
			'mas' => null,
			'accommodations_prov' => null,
			'ell' => null,
			'grade_districtwide' => null,
			'distr_assessment_na' => null,
			'distr_assessment' => null,
			'aleternativ_assessment' => null,
			'distr_accommodation' => null,
			'aleternativ_accommodation' => null,
			'has_learner' => null,
			'has_student' => null
		);

		/**
		 * Total School Hours data
		 *
		 * @var array
		 */
		protected $totalSchoolHours = array(
			'assistive_technology' => null,
			'voc' => null,
			'voc_text' => null,
			'physical' => null,
			'physical_text' => null,
			'transportation' => null,
			'transportation_text' => null,
			'length_day' => null,
			'number_day' => null,
			'length_year' => null,
			'total_week' => null,
			'special_week' => null,
			'hours_per_week' => null,
			'since_peers' => null,
			'extended_services' => null,
			'extent' => null,
			'extent_explan' => null,
			'removal' => null,
			'removal_explan' => null
		);

		/**
		 * Services data
		 *
		 * @var array
		 */
		protected $services;

		/**
		 * Annual Goals
		 *
		 * @var array
		 */
		protected $annualGoals;

		public function __construct($tsRefID = 0, $stdiepyear = 0) {
			parent::__construct($tsRefID, $stdiepyear);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @param int $stdiepyear
		 * @return IDEAStudentCT
		 */
		public static function factory($tsRefID, $stdiepyear = 0) {
			return new IDEAStudentCT($tsRefID, $stdiepyear);
		}

		/**
		 * Return data about Recomendations and Planning
		 *
		 * @return array
		 */
		public function getRecAndPlanning() {
			if (!isset($recommendationsAndPlanning)) {
				$this->recommendationsAndPlanning = db::execSQL("
				SELECT *
				  FROM webset.std_future_plan
				 WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
			")->fields;
			}

			return $this->recommendationsAndPlanning;
		}

		/**
		 * Create keys for recommendations and Planing. Convert data from JSON and add to array
		 */
		final private function convertRecommendations() {
			$this->recommendationsAndPlanning['recommendations'] = '';
			$this->recommendationsAndPlanning['planning'] = '';
			$this->recommendationsAndPlanning['parent_notif'] = '';
			$this->recommendationsAndPlanning['parent_date'] = '';
			if ($this->recommendationsAndPlanning['fptext'] != '') {
				$data = json_decode($this->recommendationsAndPlanning['fptext'], true);

				$this->recommendationsAndPlanning['recommendations'] = $data['recommendations'];
				$this->recommendationsAndPlanning['planning'] = $data['planning'];
				$this->recommendationsAndPlanning['parent_notif'] = $data['parent_notif'];
				$this->recommendationsAndPlanning['parent_date'] = CoreUtils::formatDateForUser($data['parent_date']);
			}
		}

		/**
		 * Return string about recommendations for current Student. Before this method must call convertRecommendations()
		 *
		 * @return string
		 */
		final public function getRecommendations() {
			if (!isset($this->recommendationsAndPlanning['recommendations'])) $this->convertRecommendations();

			return $this->recommendationsAndPlanning['recommendations'];
		}

		/**
		 * Return string about planning for current Student. Before this method must call convertRecommendations()
		 *
		 * @return string
		 */
		final public function getPlanning() {
			if (!isset($this->recommendationsAndPlanning['planning'])) $this->convertRecommendations();

			return $this->recommendationsAndPlanning['planning'];
		}

		/**
		 * Return string about planning for current Student. Before this method must call convertRecommendations()
		 *
		 * @return string
		 */
		final public function getParentNotiffy() {
			if (!isset($this->recommendationsAndPlanning['parent_notif'])) $this->convertRecommendations();

			return $this->recommendationsAndPlanning['parent_notif'];
		}

		/**
		 * Return string about planning for current Student. Before this method must call convertRecommendations()
		 *
		 * @return string
		 */
		final public function getParentDate() {
			if (!isset($this->recommendationsAndPlanning['parent_date'])) $this->convertRecommendations();

			return $this->recommendationsAndPlanning['parent_date'];
		}

		/**
		 * Return data about next year
		 *
		 * @return array
		 */
		public function getNextYearData() {
			if (!isset($this->nextYearData)) {
				$this->nextYearData = db::execSQL("
				SELECT sys.vouname AS next_school_year,
					   grade.gl_code AS grade_next_yr,
					   next.vouname AS home_next_school_year,
					   teacher.trs_iepmeetingdt,
					   teacher.amendment,
					   teacher.ks_cur_iep,
					   teacher.ks_trs_iep
				  FROM webset.sys_teacherstudentassignment teacher
				  LEFT JOIN public.sys_voumst sys ON teacher.nsy_attsch = sys.vourefid
				  LEFT JOIN public.sys_voumst next ON teacher.nsy_ressch = next.vourefid
				  LEFT JOIN c_manager.def_grade_levels grade ON teacher.nsy_gl_refid = grade.gl_refid
				 WHERE tsrefid = " . $this->tsrefid . "
				")->fields;
			}

			return $this->nextYearData;
		}

		/**
		 * Returns PLAAFP data
		 *
		 * @return array
		 */
		public function getPlaafpData() {
			if (!isset($this->plaafp)) {
				$this->plaafp = array();
				$this->getPLEP();
				$this->plaafp['general'] = $this->plep['pleadstat'];

				$this->plaafp['areas'] = db::execSQL("
				SELECT pglprefid,
					   tsndesc,
					   pglpnarrative,
					   strengths,
					   concerns,
					   impact
				  FROM webset.std_in_pglp
					   LEFT OUTER JOIN webset.disdef_tsn ON webset.disdef_tsn.tsnrefid = webset.std_in_pglp.tsnrefid
				 WHERE stdrefid =  " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
			     ORDER BY pglpseq, tsnnum
			")->assocAll();
			}

			return $this->plaafp;
		}

		/**
		 * Returns Transition Data
		 *
		 *
		 * @param int $mode
		 * @return array
		 */
		public function getTesting($mode = 1) {
				if ($mode == 2) {
					$fields = json_decode(db::execSQL("
						SELECT stateansw
						  FROM webset.std_assess_state
						 WHERE stdrefid =  " . $this->tsrefid . "
						   AND iepyear = " . $this->stdiepyear . "
					")->getOne(), true);
				} else {
					$fields = json_decode(db::execSQL("
						SELECT sswanarr
						  FROM webset.std_assess_state
						 WHERE stdrefid =  " . $this->tsrefid . "
						   AND iepyear = " . $this->stdiepyear . "
					")->getOne(), true);
				}
				foreach ($this->testing as $key => $field) {
					if (isset($fields[$key])) {
						$this->testing[$key] = $fields[$key];
					}
				}
			return $this->testing;
		}

		/**
		 * Returns Transition Data
		 *
		 * @return array
		 */
		public function getTransition() {
			if (!isset($this->transitions['stdage'])) {

				$fields = json_decode(db::execSQL("
					SELECT summary
					  FROM webset.std_in_ts
					 WHERE stdrefid =  " . $this->tsrefid . "
					   AND iepyear = " . $this->stdiepyear . "
			")->getOne(), true);

				foreach ($this->transitions as $key => $field) {
					if (isset($fields[$key])) {
						$this->transitions[$key] = $fields[$key];
					}
				}

			}
			return $this->transitions;
		}

		/**
		 * Returns Total School Data
		 *
		 * @return array
		 */
		public function getTotalSchoolHours() {
			if (!isset($this->totalSchoolHours['assistive_technology'])) {
				$fields = json_decode(db::execSQL("
					SELECT txt01
					  FROM webset.std_general
					 WHERE stdrefid =  " . $this->tsrefid . "
					   AND area_id = " . IDEAAppArea::TOTAL_MINUTES . "
			")->getOne(), true);

				foreach ($this->totalSchoolHours as $key => $field) {
					if (isset($fields[$key])) {
						$this->totalSchoolHours[$key] = $fields[$key];
					}
				}
			}
			return $this->totalSchoolHours;
		}

		/**
		 * Returns Services
		 *
		 * @return array
		 */
		public function getServices() {

			$goalsInf = IDEAStudent::factory($this->tsrefid)->getBgbGoals('N');
			$goals = array();
			foreach ($goalsInf as $goal) {
				$goals[$goal['grefid']] = $goal['bl_num'] . '.' . $goal['g_num'];
			}
			$sql = "
				SELECT ns.refid,
					   typedesc,
					   CASE SUBSTRING(lower(nsdesc) FROM 'other') WHEN 'other' THEN tnsoth ELSE nsdesc || COALESCE('. ' || tnsoth, '') END AS services,
					   TO_CHAR(ns.begdate, 'MM-DD-YYYY') AS begdate,
					   TO_CHAR(ns.enddate, 'MM-DD-YYYY') AS enddate,
					   um_title,
					   inarr,
					   goals,
					   umfirstname,
					   umlastname,
					   frequency_text,
					   CASE SUBSTRING(lower(crtdesc) FROM 'other') WHEN 'other' THEN locoth ELSE crtdesc END AS loc,
					   addcomments
				  FROM webset.std_oh_ns AS ns
				 INNER JOIN webset.disdef_oh_ns ON webset.disdef_oh_ns.refid = tnsrefid
				  LEFT JOIN webset.disdef_location AS loc ON (ns.locid = loc.crtrefid)
				 INNER JOIN webset.statedef_services_type AS t3 ON t3.trefid = webset.disdef_oh_ns.servicetype
				  LEFT OUTER JOIN public.sys_usermst ON ns.umrefid = public.sys_usermst.umrefid
				 WHERE ns.stdrefid = " . $this->tsrefid . "
                   AND iepyear = " . $this->stdiepyear . "
				   AND esy = 'N'
				 ORDER BY t3.seqnum, typedesc, ns.refid
			";

			$data[0] = db::execSQL($sql)->assocAll();
			$data[1] = $goals;
			$this->services = $data;

			return $this->services;
		}

		public function getAnnualGoals() {
			$data = array();
			$data[0] = $this->getBgbGoals();
			$data[1] = array();
			$periods = IDEAStudent::factory($this->tsrefid)->getProgressReportBGB('N');
			foreach ($periods as $period) {
				if (isset($period['periods'])) {
					if ($period['grefid'] != '') {
						$data[1][$period['grefid']] = $period['periods'];
					} else {
						$data[1][$period['brefid']] = $period['periods'];
					}
				}
			}
			$this->annualGoals = $data;

			return $this->annualGoals;
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
	                       plpgsql_recs_to_str ('SELECT validvalue AS column
		                        FROM webset.glb_validvalues ggb
		                       WHERE ggb.refid IN (' || COALESCE(g.used_stand, '0') || ')', ', ') AS progress,
	                       glbc.validvalue AS criteria,
						   g.txs_noticeoth AS trial,
						   txs_scheduleoth AS eval_oth,
						   txs_implementoth AS crit_oth,
						   plpgsql_recs_to_str ('SELECT validvalue AS column
		                        FROM webset.glb_validvalues ggb
		                       WHERE ggb.refid IN (' || COALESCE(o.bitemslist_new, '0') || ')', ', ') as bprogress,
                           txs_evalproc AS beval_oth,
						   bglbc.validvalue as bcriteria,
						   txs_evalprocoth AS bcrit_oth,
						   txs_level AS btrial
					  FROM webset.std_bgb_baseline bl
					       INNER JOIN webset.std_bgb_goal g ON g.blrefid = bl.blrefid
					       LEFT OUTER JOIN webset.std_bgb_benchmark o ON o.grefid = g.grefid
					       LEFT OUTER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON bl.blksa = ksa.gdskrefid
					       LEFT OUTER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
					       LEFT OUTER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
					       LEFT OUTER JOIN webset.glb_validvalues AS glbc ON g.gcriteria = glbc.refid
					       LEFT OUTER JOIN webset.glb_validvalues AS bglbc ON o.in_proc_other = bglbc.refid::VARCHAR
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
					$goal['g_num'] = $record['g_num'] == '' ? (count($result) + 1) : $record['g_num'];
					$goal['bl_num'] = $record['bl_num'] == '' ? $bl_count : $record['bl_num'];
					$goal['progress'] = $record['progress'];
					$goal['criteria'] = $record['criteria'];
					$goal['trial'] = $record['trial'];
					$goal['eval_oth'] = $record['crit_oth'];
					$goal['crit_oth'] = $record['trial'];
				}
				if ($record['orefid'] > 0) {
					$objective = array();
					$objective['orefid'] = $record['orefid'];
					$objective['bsentance'] = $record['bsentance'];
					$objective['b_num'] = $record['b_num'] == '' ? (count($goal['objectives']) + 1) : $record['b_num'];
					$objective['b_num_goal'] = $goal['g_num'] . '.' . $objective['b_num'];
					$objective['bprogress'] = $record['bprogress'];
					$objective['bcriteria'] = $record['bcriteria'];
					$objective['btrial'] = $record['btrial'];
					$objective['beval_oth'] = $record['beval_oth'];
					$objective['bcrit_oth'] = $record['bcrit_oth'];
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
		 * Return student Program Accommodations and Modifications
		 *
		 * @return array|bool
		 */
		public function getAccommodations() {
			$this->accommodation = db::execSQL("
				SELECT progmod.ssmrefid AS ssmrefid,
					   area.macdesc AS area,
					   progmod.ssmmbrother AS ssmmbrother,
					   progmod.ssmteacherother AS ssmteacherother,
					   progmod.ssmbegdate AS ssmbegdate,
					   progmod.ssmenddate AS ssmenddate
				  FROM webset.std_srv_progmod progmod
				 INNER JOIN webset.statedef_mod_acc_cat area ON area.macrefid = progmod.malrefid
				 WHERE iepyear = " . $this->stdiepyear . "
				   AND stdrefid = " . $this->tsrefid . "
			")->assocAll();

			return $this->accommodation;
		}

		public function getTestAccommodations() {
			$this->testAccommodation = db::execSQL("
				SELECT cat.catdesc,
                       sta.acccode,
                       CASE LOWER(SUBSTRING(sta.accdesc,0,6)) = 'other' WHEN TRUE THEN sta.accdesc ||' '|| COALESCE(std.acc_oth,'') ELSE sta.accdesc END,
                       CASE std.refid IS NOT NULL WHEN TRUE THEN 'checked' ELSE '' END AS sel,
                       cat.catrefid,
                       progdesc,
                       (SELECT plpgsql_recs_to_str('SELECT cast(progdesc as varchar) AS column
        	                       FROM webset.statedef_aa_prog subj
                                  WHERE subj.progrefid in (' || COALESCE(acc_subjects,'0') || ') ORDER BY seqnum', ', ')) AS subj_desc
                  FROM webset.statedef_aa_acc sta
                       LEFT JOIN webset.statedef_aa_cat AS cat ON cat.catrefid = sta.acccat
                       LEFT JOIN webset.statedef_aa_prog AS sbj ON sbj.code = sta.cat
                       LEFT JOIN webset.std_form_d_acc AS std ON (sta.accrefid =  std.accrefid AND std.syrefid = " . $this->stdiepyear . " AND std.stdrefid = " . $this->tsrefid . ")
                 WHERE (sta.enddate IS NULL OR NOW ()< sta.enddate)
				   AND cat.screfid = " . VNDState::factory()->id . "
                 ORDER BY sta.seq_num, sbj.seqnum
			")->assocAll();

			return $this->testAccommodation;
		}

		public function getAccommodationProgs() {
			$this->accommodationProgs = db::execSQL("
				SELECT progrefid,
					   progdesc
				  FROM webset.statedef_aa_prog AS prog
				 WHERE screfid = " . VNDState::factory()->id . "
				 ORDER BY seqnum
			")->assocAll();

			return $this->accommodationProgs;
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
					   CASE WHEN lower(prddesc) LIKE '%other%' THEN prddesc || ' ' || COALESCE(participantrole, '') ELSE prddesc END AS pdesc,
	                   seq_num,
	                   spirefid,
	                   partcat,
	                   signature
	              FROM webset.statedef_participantrolesdef AS rol
	              	   LEFT JOIN webset.std_iepparticipants AS partc ON (rol.prdrefid = partc.role_id AND stdrefid = " . $this->tsrefid . " $iep)
	             WHERE rol.screfid = " . VNDState::factory()->id . "
	                   $area
	             ORDER BY seq_num, participantname
	            ";

				$this->commetteMembers = db::execSQL($SQL)->assocAll();
			}

			return $this->commetteMembers;
		}
	}
