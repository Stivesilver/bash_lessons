<?php
/**
 * IDEAStudentTXARD.php
 *
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 16-01-2014.
 */

class IDEAStudentTXARD extends IDEAStudentTX {

	/**
	 * Possible IEP meet types
	 *
	 * @var array
	 */
	protected $meetIEPTypes;

	/**
	 * Review Assessment Data  about student
	 *
	 * @var array
	 */
	protected $reviewAssessmentData;

	/**
	 * Other information about student
	 *
	 * @var array
	 */
	protected $otherItems;

	/**
	 * Parameters disability
	 *
	 * @var array
	 */
	protected $disabilityList;

	/**
	 * Concerns student
	 *
	 * @var array
	 */
	protected $concerns;

	/**
	 * PREACADEMICS/ACADEMICS data
	 *
	 * @var array
	 */
	protected $competentAcademic;

	/**
	 * Competencies student
	 *
	 * @var array
	 */
	protected $competencies;

	/**
	 * Reporting data student
	 *
	 * @var array
	 */
	protected $progresReporting;

	/**
	 * Reporting ID's, titles, subreporting etc.
	 *
	 * @var array
	 */
	protected $reportingLabels;

	/**
	 * Data about standart Goals(1st Tab in app B. Goals)
	 *
	 * @var array
	 */
	protected $standartBasedGoals;

	/**
	 * Mainstream goals
	 *
	 * @var array
	 */
	protected $mainstream;

	/**
	 * ID's and names areas
	 *
	 * @var array
	 */
	protected $nameArea = array();

	/**
	 * Subjects for Program Interventions
	 *
	 * @var array
	 */
	protected $subjections;

	/**
	 * Modifications for Program Interventions
	 *
	 * @var array
	 */
	protected $modsInterventions;

	/**
	 * Information about notices
	 *
	 * @var array
	 */
	protected $noticesInfo;

	/**
	 * Main state assessments
	 *
	 * @var array
	 */
	protected $stateAssessments;

	protected $summaryRecommendations;

	/**
	 * Student efforts
	 *
	 * @var array
	 */
	protected $efforts;

	/**
	 * Statements
	 *
	 * @var array
	 */
	protected $LREStatement;

	/**
	 * Array with values LRE
	 *
	 * @var array
	 */
	protected $LREValues = array();

	/**
	 * Mainstream Services
	 *
	 * @var array
	 */
	protected $mainstreamServices;

	/**
	 * Supplementary Services
	 *
	 * @var array
	 */
	protected $supplementaryServices;

	/**
	 * Academic Schedule
	 *
	 * @var array
	 */
	protected $academicSchedule;

	/**
	 * Arrangements
	 *
	 * @var array
	 */
	protected $instructArrangement;

	/**
	 * Transition Services
	 *
	 * @var array
	 */
	protected $transitionServices;

	protected $commetteMembers;

	/**
	 * Progress Report Standard Goals
	 *
	 * @var array
	 */
	protected $progressReportStandard;

	/**
	 * Progress Report Mainstream Goals
	 *
	 * @var array
	 */
	protected $progressReportMainstream;

	/**
	 * Transfer Packet data
	 *
	 * @var array
	 */
	protected $transferPacket;

	/**
	 * Don't know what is it =_)
	 *
	 * @var array
	 */
	protected $attrDistrict;

	/**
	 * Return meet types
	 *
	 * @return array
	 */
	public function meetIEPTypes() {
		if (!isset($this->meetIEPTypes)) {
			$SQL = "
				SELECT siepmtrefid,
	                   siepmtdesc
	              FROM webset.statedef_ieptypes
	             WHERE screfid = " . VNDState::factory()->id . "
	             ORDER BY CASE length(siepmtdesc)>40 WHEN TRUE THEN 500 ELSE siepmtrefid END
	            ";

			$this->meetIEPTypes = db::execSQL($SQL)->assocAll();
		}

		return $this->meetIEPTypes;
	}

	/**
	 * Return some data from array AssessmentData by key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function reviewAssData($key) {
		if (!isset($this->reviewAssessmentData)) {
		$SQL = "
			SELECT to_char(stdiepmeetingdt,'MM/DD/YYYY') as stdiepmeetingdt,
                   to_char(stdenrolldt,'MM/DD/YYYY') as stdenrolldt,
                   to_char(stdcmpltdt,'MM/DD/YYYY') as stdcmpltdt,
                   to_char(stdevaldt,'MM/DD/YYYY') as stdevaldt,
                   to_char(stdtriennialdt,'MM/DD/YYYY') as stdtriennialdt,
                   to_char(stddraftiepcopydt,'MM/DD/YYYY') as stddraftiepcopydt,
                   to_char(stdiepcopydt,'MM/DD/YYYY') as stdiepcopydt,
                   to_char(dts.longard,'MM/DD/YYYY') as longard,
                   to_char(briefard,'MM/DD/YYYY') as briefard,
                   to_char(dts.amendment,'MM/DD/YYYY') as amendment,
                   to_char(dts.inituni,'MM/DD/YYYY') as inituni,
                   to_char(assistive,'MM/DD/YYYY') as assistive,
                   to_char(fba,'MM/DD/YYYY') as fba,
                   to_char(fve,'MM/DD/YYYY') as fve,
                   relatedasm,
                   to_char(related,'MM/DD/YYYY') as related,
                   to_char(speach,'MM/DD/YYYY') as speach,
                   to_char(transition,'MM/DD/YYYY') as transition,
                   other_desc,
                   to_char(other,'MM/DD/YYYY') as other,
                   addcomments,
                   seccode,
                   to_char(stdexitdt,'MM/DD/YYYY') as stdexitdt
              FROM webset.sys_teacherstudentassignment ts
                   LEFT OUTER JOIN webset_tx.std_dates dts ON tsrefid = dts.stdrefid AND iepyear = $this->stdiepyear
                   LEFT OUTER JOIN webset.statedef_exitcategories edef on edef.secrefid = exitrefid
	         WHERE tsRefID = $this->tsrefid
	        ";
			$this->reviewAssessmentData = db::execSQL($SQL)->assoc();
		}
		return $this->reviewAssessmentData[$key];
	}

	/**
	 * Return array with other information about student
	 *
	 * @return array
	 */
	public function otherInfo() {
		if (!isset($this->otherItems)) {
			$SQL = "
				SELECT item,
	                   item_other,
	                   concerns
	              FROM webset_tx.std_sam_other
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND iepyear = " . $this->stdiepyear . "
	            ";
			$items = db::execSQL($SQL)->assocAll();
			$this->otherItems['first'] = $items;
			$SQL = "
				SELECT refid,
	                   validvalue,
	                   (SELECT SUBSTRING(item_addition FROM 'oth_' || CAST(webset.glb_validvalues.refid as VARCHAR) || '::([^|]+)[|$]')
	                      FROM webset_tx.std_sam_other
	                     WHERE stdrefid = " . $this->tsrefid . "
			               AND iepyear = " . $this->stdiepyear . "
			           ) as itemval
	              FROM webset.glb_validvalues
	             WHERE valuename = 'TX_SAM_Oth'
	             ORDER BY valuename, sequence_number, validvalue ASC
	            ";
			$values = db::execSQL($SQL)->assocAll();
			# add other item if exist and default values for selected
			if (isset($items[0])) {
				$this->otherItems['option'] = $items[0]['item_other'];

				$selected = 'Y';
			} else {
				$this->otherItems['option'] = '';

				$selected = 'N';
			}
			# set up selected values for checkboxes
			$count = count($values);
			for ($i = 0; $i < $count; $i++) {
				if (isset($items[0]['item']) && strpos($items[0]['item'], $values[$i]['refid']) > 0) {
					$values[$i]['selected'] = $selected;
				} else {
					$values[$i]['selected'] = 'N';
				}
			}
			$this->otherItems['items'] = $values;
		}

		return $this->otherItems;
	}

	/**
	 * Return parameters disability
	 *
	 * @return array
	 */
	public function disabilityList() {
		if (!isset($this->disabilityList)) {
			$SQL = "
				SELECT dcdesc || COALESCE(' <b>(' || validvalue || ')</b>', '') AS desc,
	                   std.dcrefid,
	                   CASE WHEN lower(dcdesc) not like '%other%' THEN
	                       plpgsql_recs_to_str ('SELECT cast (adname as varchar)  AS column
	                                               FROM webset.statedef_disabling_indicatam as am
	                                                    INNER JOIN webset.statedef_disabling_indicatad as ad ON am.amirefid = ad.amirefid
	                                              WHERE adirefid in (' || COALESCE(CASE WHEN substring(replace(indicators,',','') from chr(92) || 'd{1,}')=replace(indicators,',','') THEN indicators ELSE NULL END,'0') || ' )
	                                              ORDER BY am.code, ad.code', ', ')
	                   ELSE indicators END,
	                   dcdesc
	              FROM webset.statedef_disablingcondition stt
	                   LEFT OUTER JOIN webset.std_disabilitymst std ON stt.dcrefid = std.dcrefid AND std.stdRefID = " . $this->tsrefid . "
	                   LEFT OUTER JOIN webset.glb_validvalues ON validvalueid = CAST(sdtype as varchar) AND valuename = 'TXDisabilityType'
	             WHERE screfid = " . VNDState::factory()->id . "
	               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	             ORDER BY dcdesc";
			$this->disabilityList = db::execSQL($SQL)->assocAll();
		}
		return $this->disabilityList;
	}

	/**
	 * Return some concerns
	 *
	 * @param string $key
	 * @return string
	 */
	public function getConcerns($key) {
		if (!isset($this->concerns)) {
			$SQL = "
				SELECT concerns
	              FROM webset_tx.std_sam_other
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND iepyear = " . $this->stdiepyear . "
	            ";
			$this->concerns = db::execSQL($SQL)->assoc();
		}
		return $this->concerns[$key];
	}

	/**
	 * Change value for checkboxes
	 *
	 * @param int $i
	 * @param mixed $val
	 */
	public function changeDisability($i, $val) {
		$this->disabilityList[$i]['dcrefid'] = $val;
	}

	/**
	 * Get complete answer from db by questions
	 * @return array
	 */
	public function presentCompetencies() {
		if (!isset($this->competencies)) {
			$this->competencies['achievement_sw'] = '';
			$this->competencies['capable_sw'] = '';
			$this->competencies['capable_txt'] = '';
			$this->competencies['lenses_sw'] = '';
			$this->competencies['hearing_sw'] = '';
			$this->competencies['policy_sw'] = '';
			$this->competencies['teks_sw'] = '';
			$this->competencies['teks_txt'] = '';
			$this->competencies['vocational_affect_sw'] = '';
			$this->competencies['vocational_met_sw'] = '';
			$this->competencies['cognitive_sw'] = '';
			$this->competencies['second_lang_sw'] = '';
			$this->competencies['english_sw'] = '';
			$this->competencies['native_sw'] = '';
			$this->competencies['native_lang_txt'] = '';
			$this->competencies['alt_lang_sw'] = '';
			$this->competencies['alt_esl_sw'] = '';
			$this->competencies['alt_bilingual_sw'] = '';
			$this->competencies['alt_other_sw'] = '';
			$this->competencies['alt_other_txt'] = '';
			$this->competencies['impede_sw'] = '';
			$this->competencies['impede_capable_sw'] = '';
			$this->competencies['impede_diff_sw'] = '';
			$this->competencies['impede_notcapable_sw'] = '';

			$SQL = "
				SELECT scqrefid,
				       std.scarefid,
				       CASE SUBSTRING(scanswer FROM 'Yes|No|N/A')
				            WHEN 'Yes' THEN 'Y'
				            WHEN 'No'  THEN 'N'
				            WHEN 'N/A' THEN 'A'
				            ELSE SUBSTRING(scanswer FROM '...')
				       END as answer,
				       sscmnarrative
				  FROM webset.std_spconsid std
				       INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
				 WHERE std.stdrefid = " . $this->tsrefid . "
				   AND std.syrefid = " . $this->stdiepyear . "
	            ";
			$dbdata = db::execSQL($SQL)->assocAll();
			foreach ($dbdata as $a) {
				$answer = $a['answer'];
				$text = str_replace('Needs: ', '', $a['sscmnarrative']);
				switch ($a['scqrefid']) {
					case 296:
						$this->competencies['achievement_sw'] = $answer;
						break;
					case 297:
						$this->competencies['capable_sw'] = $answer;
						$this->competencies['capable_txt'] = $text;
						break;
					case 298:
						$this->competencies['lenses_sw'] = $answer;
						break;
					case 305:
						$this->competencies['hearing_sw'] = $answer;
						break;
					case 299:
						$this->competencies['policy_sw'] = $answer;
						break;
					case 300:
						$this->competencies['teks_sw'] = $answer;
						$this->competencies['teks_txt'] = $text;
						break;
					case 301:
						$this->competencies['vocational_affect_sw'] = $answer;
						break;
					case 302:
						$this->competencies['vocational_met_sw'] = $answer;
						break;
					case 303:
						$this->competencies['cognitive_sw'] = $answer;
						break;
					case 304:
						$this->competencies['second_lang_sw'] = $answer;
						break;
					case 308:
						$this->competencies['alt_lang_sw'] = ($answer == 'Alt' ? 'Y' : '');
						break;
					case 306:
						$this->competencies['english_sw'] = ($answer == 'All' ? 'Y' : '');
						$this->competencies['native_sw'] = ($answer == 'Ins' ? 'Y' : '');
						$this->competencies['native_lang_txt'] = $text;
						break;
					case 307:
						$this->competencies['alt_esl_sw'] = ($answer == 'ESL' ? 'Y' : '');
						$this->competencies['alt_bilingual_sw'] = ($answer == 'Bil' ? 'Y' : '');
						$this->competencies['alt_other_sw'] = ($answer == 'Oth' ? 'Y' : '');
						$this->competencies['alt_other_txt'] = $text;
						break;
					case 309:
						$this->competencies['impede_sw'] = $answer;
						break;
					case 310:
						$this->competencies['impede_capable_sw'] = ($a['scarefid'] == 762 ? 'Y' : '');
						$this->competencies['impede_diff_sw'] = ($a['scarefid'] == 763 ? 'Y' : '');
						$this->competencies['impede_notcapable_sw'] = ($a['scarefid'] == 764 ? 'Y' : '');
						break;
				}
			}
		}
		return $this->competencies;
	}

	/**
	 * Return array with PREACADEMICS/ACADEMICS data
	 *
	 * @return array
	 */
	public function competentAcademic() {
		if (!isset($this->competentAcademic)) {
			$SQL = "
				SELECT CASE ac_desc WHEN 'Other' THEN 'Other: ' || COALESCE(area_other,'') ELSE ac_desc END,
	                   skill_level,
	                   strengths,
	                   needs
	              FROM webset_tx.def_academics  def
	                   LEFT OUTER JOIN webset_tx.std_academics std ON std.ac_refid = def.ac_refid
	                    AND std_refid = " . $this->tsrefid . " AND iep_year = " . $this->stdiepyear . "
	             ORDER BY seqnum, ac_desc
	            ";
			$this->competentAcademic = db::execSQL($SQL)->assocAll();
		}
		return $this->competentAcademic;
	}

	/**
	 * Return reporting data student
	 *
	 * @return array|bool
	 */
	public function progresReportingStd() {
		if (!isset($this->progresReporting)) {
			$SQL = "
				SELECT field0,
	                   field1_bas,
	                   field1_oth,
	                   field2_bas,
	                   field2_oth,
	                   field3_bas,
	                   field3_oth
	              FROM webset_tx.std_goal_progress
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND iepyear = " . $this->stdiepyear . "
	            ";
			$this->progresReporting = db::execSQL($SQL)->assoc();
		}
		return $this->progresReporting;
	}

	/**
	 * Return array for labels reporting
	 *
	 * @return array
	 */
	public function progresReporting() {
		if (!isset($this->reportingLabels)) {
			$SQL = "
				SELECT mst.go_refid,
	                   go_desc,
	                   go_sub_refid,
	                   go_sub_desc
	              FROM webset_tx.def_goalobj_mst mst
	                   INNER JOIN  webset_tx.def_goalobj_dtl dtl ON mst.go_refid = dtl.go_refid
	             ORDER BY mst.seqnum, dtl.seqnum
	            ";
			$this->reportingLabels = db::execSQL($SQL)->assocAll();
		}
		return $this->reportingLabels;
	}

	/**
	 * Return student standart goals
	 *
	 * @param string $ESY
	 * @return array|bool
	 */
	public function standartBasedGoals($ESY = 'N') {
		if (!isset($this->standartBasedGoals)) {
			$SQL = "
				SELECT g.grefid,
                       o.orefid,
                       CASE WHEN subject='Other' THEN othersub ELSE subject END as subject,
                       " . IDEAPartsTX::get('goal_statement') . " as gSentance,
                       " . IDEAPartsTX::get('objective_statement') . " as bSentance,
	                   g.order_num as g_num,
	                   o.order_num as b_num,
                       servtype,
	                   to_char(durbeg, 'MM/DD/YYYY') as durbeg,
	                   to_char(durend, 'MM/DD/YYYY') as durend,
	                   location,
	                   locationoth,
	                   implement,
	                   implementoth,
	                   schedule,
	                   scheduleoth,
	                   notice,
	                   noticeoth,
	                   evalproc,
	                   evalprocoth,
                       TRIM(CASE oc.criteria
	                      WHEN 'Other' THEN COALESCE(o.criteria_oth,'')
	                      ELSE COALESCE(oc.criteria, '')
	                   END) as obj_level
                  FROM webset_tx.std_sb_goals g
                       INNER JOIN webset_tx.def_sb_subjects s ON s.subrefid = g.subrefid
                       INNER JOIN webset_tx.def_sb_action ga ON ga.arefid = g.action_id
                       INNER JOIN webset.glb_validvalues gv ON gv.refid = g.timeframe_id
                       INNER JOIN webset_tx.def_sb_criteria gc ON gc.ctrefid = g.criteria_id
                       LEFT OUTER JOIN webset_tx.std_sb_objectives o ON o.grefid = g.grefid
                       LEFT OUTER JOIN webset_tx.def_sb_action oa ON oa.arefid = o.action_id
                       LEFT OUTER JOIN webset.glb_validvalues ov ON ov.refid = o.timeframe_id
                       LEFT OUTER JOIN webset_tx.def_sb_criteria oc ON oc.ctrefid = o.criteria_id
                 WHERE stdrefid = " . $this->tsrefid . "
	               AND iepyear = " . $this->stdiepyear . "
                   AND esy = '$ESY'
                 ORDER BY g.order_num, g.grefid, o.order_num, o.orefid
                ";
			$this->standartBasedGoals = $this->goalsArrayPrepare(db::execSQL($SQL)->assocAll());
		}
		return $this->standartBasedGoals;
	}

	/**
	 * Return student standart goals
	 *
	 * @param string string $esy
	 * @return array|bool
	 */
	public function bgbGoals($esy = 'N') {
		if (!isset($this->bgbGoals)) {
			$this->bgbGoals = array();
			$SQL = "
				SELECT g.grefid,
				       o.brefid as orefid,
				       " . IDEAParts::get('baselineArea') . " as subject,
				       COALESCE(g.overridetext, gsentance) as gsentance,
				       COALESCE(o.overridetext, bsentance) as bsentance,
				       o.order_num as b_num,
				       g.order_num as g_num,
				       g.txs_servtype as servtype,
				       to_char(g.txs_durbeg, 'MM/DD/YYYY') as durbeg,
				       to_char(g.txs_durend, 'MM/DD/YYYY') as durend,
				       g.txs_location as location,
				       g.txs_locationoth as locationoth,
				       g.txs_implement as implement,
				       g.txs_implementoth as implementoth,
				       g.txs_schedule as schedule,
				       g.txs_scheduleoth as scheduleoth,
				       g.txs_notice as notice,
				       g.txs_noticeoth as noticeoth,
				       o.txs_evalproc as evalproc,
				       o.txs_evalprocoth as evalprocoth,
				       o.txs_level as obj_level
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
		$grefid = null;
		$goal = array();
		foreach ($data as $key => $record) {
			if ($grefid != $record['grefid']) {
				$goal = array();
				$goal['grefid'] = $record['grefid'];
				$goal['subject'] = $record['subject'];
				$goal['gsentance'] = $record['gsentance'];
				$goal['g_num'] = $record['g_num'] == '' ? (count($result) + 1) : $record['g_num'];
				$goal['servtype'] = $record['servtype'];
				$goal['durbeg'] = $record['durbeg'];
				$goal['durend'] = $record['durend'];
				$goal['location'] = $record['location'];
				$goal['locationoth'] = $record['locationoth'];
				$goal['implement'] = $record['implement'];
				$goal['implementoth'] = $record['implementoth'];
				$goal['schedule'] = $record['schedule'];
				$goal['scheduleoth'] = $record['scheduleoth'];
				$goal['notice'] = $record['notice'];
				$goal['noticeoth'] = $record['noticeoth'];
				$goal['objectives'] = array();
			}
			if ($record['orefid'] > 0) {
				$objective = array();
				$objective['orefid'] = $record['orefid'];
				$objective['bsentance'] = $record['bsentance'];
				$objective['b_num'] = $record['b_num'] == '' ? (count($goal['objectives']) + 1) : $record['b_num'];
				$objective['b_num_goal'] = $goal['g_num'] . '.' . $objective['b_num'];
				$objective['evalproc'] = $record['evalproc'];
				$objective['evalprocoth'] = $record['evalprocoth'];
				$objective['obj_level'] = $record['obj_level'];
				$goal['objectives'][] = $objective;
			}
			if (($grefid != $record['grefid'])) {
				$result[] = $goal;
			} else {
				$result[count($result) - 1] = $goal;
			}
			$grefid = $record['grefid'];
		}
		return $result;
	}

	/**
	 * Return array with mainstream goals
	 *
	 * @return array
	 */
	public function getMainstream() {
		if (!isset($this->mainstream)) {
		$SQL = "
			SELECT refid,
			       taks_rationale,
                   sdaa_level,
                   sdaa_schedule,
                   trpi_take,
                   trpi_alternative,
                   telpas_take,
                   telpas_alternative,
                   telpop_take,
                   telpop_alternative,
                   additional_take,
                   additional_assessment,
                   additional_alternative,
                   mainstream_taks,
                   servtype,
                   TO_CHAR(durbeg, 'MM/DD/YYYY') as durbeg,
                   TO_CHAR(durend, 'MM/DD/YYYY') as durend,
                   location,
                   locationoth,
                   implement,
                   implementoth,
                   level,
                   evalproc,
                   evalprocoth,
                   schedule,
                   scheduleoth,
                   notice,
                   noticeoth,
                   COALESCE(taks_take,'Y') as taks_take,
                   taks_whynot
              FROM webset_tx.std_sam_general
             WHERE stdrefid = " . $this->tsrefid . "
			   AND iepyear = " . $this->stdiepyear . "
			";
			$this->mainstream = db::execSQL($SQL)->assoc();
		}
		return $this->mainstream;
	}

	/**
	 * Return name area by ID
	 *
	 * @param int $id
	 * @return mixed
	 */
	public function getProgramInterventArea($id) {
		if (!isset($this->nameArea[$id])) {
		    $SQL = "
				SELECT area
	              FROM webset_tx.def_pi_modifications_area
	             WHERE arefid = $id
	            ";

			$this->nameArea[$id] = db::execSQL($SQL)->getOne();
		}
		return $this->nameArea[$id];
	}

	/**
	 * Return subjections for Program Interventions
	 *
	 * @return array
	 */
	public function getProgramInterventSubjects() {
		if (!isset($this->programInterventSubjects)) {
			$SQL = "
				SELECT sub_refid,
                       COALESCE(sub_print, sub_desc)
                  FROM webset_tx.def_pi_subjects
                 WHERE (end_date>now() OR end_date IS NULL)
                   AND COALESCE(vndrefid, " . SystemCore::$VndRefID . ") = " . SystemCore::$VndRefID . "
                 ORDER BY seqnum
	            ";

			$this->programInterventSubjects = db::execSQL($SQL)->assocAll();
		}
		return $this->programInterventSubjects;
	}

	/**
	 * Returns State Program Interventions
	 *
	 * @param $id int
	 * @return array
	 */
	public function getProgramInterventState($id) {
		$SQL = "
				SELECT sub_mod_refid as refid,
					   mod_desc,
					   sub_mod_desc,
					   mst.seqnum,
					   dtl.seqnum as dtl_seqnum,
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
	 * Returns Own Program Interventions
	 *
	 * @param $area int
	 * @return array
	 */
	public function getProgramInterventOwn($area) {
		$SQL = "
				SELECT refid,
                       mod_desc,
                       accommodation,
                       mst.seqnum,
                       dtl.seqnum * 100 as dtl_seqnum,
                       'O' as mode
	              FROM webset_tx.def_pi_modifications_mst mst
	                   INNER JOIN webset_tx.std_pi_own dtl ON dtl.category_id = mst.mod_refid
			  	 WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
                   AND area_id = $area
	             ORDER BY 4, 5, 3
            ";
		return $this->execSQL($SQL)->assocAll();
	}

	/**
	 * Returns Students Program Interventions
	 *
	 * @param int $area
	 * @internal param string $mode
	 * @return array
	 */
	public function getProgramInterventStudent($area) {
		$inerventions = $this->getProgramInterventState($area);
		$own_inerventions = $this->getProgramInterventOwn($area);
		$inerventions = array_merge($own_inerventions, $inerventions);

		$seqnum = array();
		$dtl_seqnum = array();
		foreach ($inerventions as $key => $row) {
			$seqnum[$key]  = $row['seqnum'];
			$dtl_seqnum[$key] = $row['dtl_seqnum'];
		}

		array_multisort($seqnum, SORT_ASC, $dtl_seqnum, SORT_ASC, $inerventions);

		$subjects = $this->getProgramInterventSubjects();
		$result_data = array();
		$std_data = array();
		$SQL = "
			SELECT substring(mod_sub_id from '(.+)_.+') as intervent_id,
			       substring(mod_sub_id from '.+_(.+)') as subject_id,
			       accomod_mode
              FROM webset_tx.std_pi
             WHERE std_refid = $this->tsrefid
               AND iep_year  = $this->stdiepyear
            ";
		$data = db::execSQL($SQL)->assocAll();
		foreach ($data as $key => $row) {
			$std_data[$row['accomod_mode']] [$row['intervent_id']] [$row['subject_id']] = true;
		}
		foreach ($inerventions as $inervention) {
			if (!isset($std_data[$inervention['mode']] [$inervention['refid']])) continue;
			$inervention['subjects'] = $subjects;
			foreach ($subjects as $key => $subject) {
				$inervention['subjects'][$key]['selected'] = isset($std_data[$inervention['mode']] [$inervention['refid']] [$subject['sub_refid']]);
			}
			$result_data[] = $inervention;
		}
		return $result_data;
	}

	/**
	 * Return array with information about notices
	 *
	 * @return array
	 */
	public function getNoticesInfo() {
		if (!isset($this->noticesInfo)) {
			$SQL = "
				SELECT notes
		          FROM webset_tx.std_notes
		         WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
				 ORDER BY siairefid
				";

			$this->noticesInfo = db::execSQL($SQL)->assocAll();
		}
		return $this->noticesInfo;
	}

	/**
	 * Return array with main assessments
	 *
	 * @return array
	 */
	public function getStateAssessments() {
		if (!isset($this->stateAssessments)) {
			$this->stateAssessments = array();
			$SQL = "
				SELECT samrefid,
	                   to_char(begdate, 'mm-dd-yyyy') as samdate,
	                   samdesc,
	                   COALESCE(gl_code, '$this->grdlevel') as samgrade,
	                   dsydesc as samyear
	              FROM webset_tx.std_sam_main sam
	                   LEFT OUTER JOIN webset.disdef_schoolyear sy ON sy.dsyrefid = sam.syrefid
	                   LEFT OUTER JOIN c_manager.def_grade_levels grd ON grd.gl_refid = sam.grade_id
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND ardinclude = 'Y'
	             ORDER BY begdate desc, samrefid desc
	            ";
			$assessments = db::execSQL($SQL)->assocAll();

			foreach ($assessments as $assessment) {
				$assessment = array_merge($assessment, $this->getStateAssessmentsGeneral($assessment['samrefid']));
				$assessment['staar_subjects'] = $this->getStateAssessmentsStaarSubjects($assessment['samrefid']);
				$assessment['staar_ratio'] = $this->getStateAssessmentsStaarRatio($assessment['samrefid']);
				$assessment['staar_success'] = $this->getStateAssessmentsStaarSuccess($assessment['samrefid']);
				$assessment['staar_acceler'] = $this->getStateAssessmentsStaarAcceler($assessment['samrefid']);
				$assessment['taks_subjects'] = $this->getStateAssessmentsStaarSubjects($assessment['samrefid'], 'TAKS|TAAS');
				$assessment['taks_ratio'] = $this->getStateAssessmentsStaarRatio($assessment['samrefid'], 'TAKS');
				$assessment['taks_success'] = $this->getStateAssessmentsStaarSuccess($assessment['samrefid'], 'TAKS');
				$assessment['taks_acceler'] = $this->getStateAssessmentsStaarAcceler($assessment['samrefid'], 'TAKS');
				$assessment['top_subjects'] = $this->getStateAssessmentsStaarSubjects($assessment['samrefid'], 'TELPAS');
				$this->stateAssessments[] = $assessment;
			}

		}
		return $this->stateAssessments;
	}

	/**
	 * Return general assessment data array
	 *
	 * @param int $samrefid
	 * @return array
	 * @throws Exception
	 */
	public function getStateAssessmentsGeneral($samrefid) {
		if (!is_numeric($samrefid)) throw new Exception('Assessment ID should be Integer. "$samrefid" provided.');
		$SQL = "
			SELECT refid AS std_sam_general_id,
				   taks_rationale,
				   sdaa_level,
				   sdaa_schedule,
				   trpi_take,
				   trpi_alternative,
				   telpas_take,
				   telpas_alternative,
				   telpop_take,
				   telpop_alternative,
				   additional_take,
				   additional_assessment,
				   additional_alternative,
				   mainstream_taks,
				   COALESCE(taks_take, 'Y') as taks_take,
				   taks_whynot,
				   staar_take,
				   staar_whynot
              FROM webset_tx.std_sam_general
             WHERE samrefid = $samrefid
        ";

		return db::execSQL($SQL)->fields;
	}

	/**
	 * Return STAAR/TAKS assessment data array
	 *
	 * @param int $samrefid
	 * @param string $area
	 * @return array
	 * @throws Exception
	 */
	public function getStateAssessmentsStaarSubjects($samrefid, $area='STAAR') {
		if (!is_numeric($samrefid)) throw new Exception('Assessment ID should be Integer. "$samrefid" provided.');
		$SQL = "
			SELECT plpgsql_recs_to_str ('SELECT cast (swadesc as varchar)  AS column
                                           FROM webset.statedef_assess_state
                                          WHERE swarefid in (' || assessments || ')', ', ') as assessments,

                   aaadesc || CASE aaarefid WHEN 0 THEN COALESCE(' - ' || na_reason,'')  ELSE '' END as subjects,

                   plpgsql_recs_to_str ('SELECT cast (adesc as varchar)  AS column
                                           FROM webset.statedef_prim_lang
                                          WHERE refid in (' || languages || ')', ', ') as languages,

                   plpgsql_recs_to_str ('SELECT cast (gldesc as varchar)  AS column
                                           FROM webset.def_gradelevel
                                          WHERE glrefid in (' || grades || ')', ', ') as grades,
                   TRIM(both '".PHP_EOL."' FROM
                       COALESCE(
	                       plpgsql_recs_to_str ('SELECT CAST(stsdesc as varchar) as column
	                                               FROM webset.statedef_mod_acc
	                                              WHERE stsrefid in (' || COALESCE(ids_accommodations,'-1')  || ')
	                                              ORDER BY stsseq, stsdesc', '".PHP_EOL."'),
	                       ''
	                   ) || '".PHP_EOL."' || COALESCE(accomodation, '')
	               ) as accomodation
              FROM webset_tx.std_sam_taks std
                   INNER JOIN webset.statedef_assess_acc ON CAST(aaarefid as varchar)= subjects
             WHERE samrefid = $samrefid
               AND (plpgsql_recs_to_str ('SELECT swadesc  AS column
                                            FROM webset.statedef_assess_state
                                           WHERE swarefid in (' || assessments || ')', ', ') SIMILAR TO '%(" . $area . ")%')
             ORDER BY refid desc
        ";
		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return STAAR/TAKS assessment data array
	 *
	 * @param int $samrefid
	 * @param string $area
	 * @return array
	 * @throws Exception
	 */
	public function getStateAssessmentsStaarRatio($samrefid, $area='STAAR') {
		if (!is_numeric($samrefid)) throw new Exception('Assessment ID should be Integer. "$samrefid" provided.');
		$SQL = "
			SELECT t1.validvalue as subject,
                   CASE WHEN t2.validvalue like 'Other%' THEN COALESCE(rationale, '') ELSE COALESCE(t2.validvalue,rationale) END as rationale
              FROM webset_tx.std_sam_taks_ratio AS t0
                   LEFT JOIN webset.glb_validvalues AS t1 ON subject_id = t1.refid
                   LEFT JOIN webset.glb_validvalues AS t2 ON reationale_id = t2.refid
             WHERE samrefid = $samrefid
               AND COALESCE(t2.validvalueid, '" . $area . "') = '" . $area . "'
             ORDER BY t0.lastupdate
        ";
		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return STAAR/TAKS Success Initiative data array
	 *
	 * @param int $samrefid
	 * @param string $area
	 * @return array
	 * @throws Exception
	 */
	public function getStateAssessmentsStaarSuccess($samrefid, $area='STAAR') {
		if (!is_numeric($samrefid)) throw new Exception('Assessment ID should be Integer. "$samrefid" provided.');
		$SQL = "
			SELECT swadesc as assessment,
                   aaadesc as subject,
                   gldesc as grade,
                   '1st - Accelerated Improvement Plan: ' || COALESCE(plan1, '') || '".PHP_EOL."' ||
                   '2nd - Accelerated Improvement Plan: ' || COALESCE(plan2, '') || '".PHP_EOL."' ||
                   '3rd - Accelerated Improvement Plan: ' || COALESCE(plan3, '') as plans
              FROM webset_tx.std_sam_taks_success AS t0
                   INNER JOIN webset.statedef_assess_state AS t1 ON assessment_id = CAST(t1.swarefid as varchar)
                   INNER JOIN webset.def_gradelevel AS t2 ON grade_id = CAST(glrefid as varchar)
                   INNER JOIN webset.statedef_assess_acc AS t3 ON subject_id = CAST(aaarefid as varchar)
             WHERE samrefid = $samrefid
               AND swadesc like '%" . $area . "%'
             ORDER BY t0.lastupdate
        ";
		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return STAAR/TAKS Accelerated Instruction data array
	 *
	 * @param int $samrefid
	 * @param string $area
	 * @return array
	 * @throws Exception
	 */
	public function getStateAssessmentsStaarAcceler($samrefid, $area='STAAR') {
		if (!is_numeric($samrefid)) throw new Exception('Assessment ID should be Integer. "$samrefid" provided.');
		$SQL = "
			SELECT CASE WHEN swadesc = 'Other' THEN assessment_oth ELSE swadesc END as assessment,
                   CASE WHEN aaadesc = 'Other' THEN subject_oth ELSE aaadesc END as subject,
				   CASE WHEN grade_id = '0' THEN grade_oth ELSE gldesc END as grade,
                   '1st - Accelerated Improvement Plan: ' || COALESCE(plan1, '') || '".PHP_EOL."' ||
                   '2nd - Accelerated Improvement Plan: ' || COALESCE(plan2, '') || '".PHP_EOL."' ||
                   '3rd - Accelerated Improvement Plan: ' || COALESCE(plan3, '') as plans
              FROM webset_tx.std_sam_taks_accelerate AS t0
                   INNER JOIN webset.statedef_assess_state AS t1 ON assessment_id::integer = t1.swarefid
                   INNER JOIN webset.statedef_assess_acc AS t3 ON subject_id::integer = aaarefid
				   LEFT OUTER JOIN webset.def_gradelevel AS t2 ON grade_id::integer = glrefid
             WHERE samrefid = $samrefid
               AND swadesc like '%" . $area . "%'
             ORDER BY t0.lastupdate
        ";
		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return array with student efforts
	 *
	 * @param string $mode
	 * @return array
	 */
	public function getEfforts($mode) {
		if (!isset($this->efforts)) {
			$SQL = "
				SELECT CASE modesw WHEN 'M' THEN 'Modifications/accommodations for' ELSE Null END,
	                   edesc || CASE WHEN edesc like 'Other%' THEN COALESCE(' <i>' || other || '</i>', '') ELSE '' END AS effort,
	                   mark
	              FROM webset_tx.def_lre_efforts AS t0
	                   LEFT OUTER JOIN webset_tx.std_lre_efforts AS t1 ON t1.erefid = t0.refid
	                    AND stdrefid = " . $this->tsrefid . "
	                    AND iep_year = " . $this->stdiepyear . "
	                    AND smode = '$mode'
	             ORDER BY t0.seqnum, edesc
	            ";
			$this->efforts = db::execSQL($SQL)->assocAll();
		}
		return $this->efforts;
	}

	/**
	 * Return LRE value by ID
	 *
	 * @param int $area
	 * @return array
	 */
	public function getLREValue($area) {
		if (!isset($this->LREValues[$area])) {
			$SQL = "
				SELECT all_objects
	              FROM webset_tx.std_lre_statements
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND iep_year = " . $this->stdiepyear . "
	               AND area = '" . $area . "'
	            ";
			$this->LREValues[$area] = db::execSQL($SQL)->assocAll();
		}
		return $this->LREValues;
	}

	/**
	 * Return LRE statements
	 *
	 * @param string $area
	 * @return array
	 */
	public function getLREStatement($area) {
		if (!isset($this->LREStatement)) {
			$SQL = "
				SELECT drefid,
	                   mst.srefid,
	                   stmtext,
	                   dtltext,
	                   mst.othersw as mst_other,
	                   dtl.othersw as dtl_other,
	                   chckmode
			      FROM webset_tx.def_lre_statement mst
	                   LEFT OUTER JOIN webset_tx.def_lre_statementdtl dtl on mst.srefid = dtl.srefid
			     WHERE area = '" . $area . "'
	             ORDER BY mst.seqnum, dtl.seqnum
	            ";
			$this->LREStatement = db::execSQL($SQL)->assocAll();
		}
		return $this->LREStatement;
	}

	/**
	 * Return Mainstream Services
	 *
	 * @return array
	 */
	public function getMainstreamServices() {
		if (!isset($this->mainstreamServices)) {
			$SQL = "
				SELECT service || COALESCE (' - ' || servicetxt, '') AS servicetxt,
	                   to_char(startdate,'MM/DD/YYYY') as start,
	                   CASE WHEN freq.frequency   like 'Other%'  THEN COALESCE('<i>' || freq_oth   || '</i>', '') ELSE freq.frequency END,
	                   CASE WHEN loc.location   	like 'Other%'  THEN COALESCE('<i>' || loc_oth    || '</i>', '') ELSE loc.location   END,
	                   CASE WHEN dur.duration   	like 'Other%'  THEN COALESCE('<i>' || duration_oth    || '</i>', '') ELSE dur.duration   END
	              FROM webset_tx.std_srv_mainstream std
	                   INNER JOIN webset_tx.def_srv_mainstream rel ON mrefid = srefid
	                   INNER JOIN webset_tx.def_srv_frequency freq ON freq.frefid = std.freq
	                   INNER JOIN webset_tx.def_srv_duration dur ON dur.drefid = std.duration
	                   INNER JOIN webset_tx.def_srv_locations loc ON loc.lrefid = std.loc
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND iep_year = " . $this->stdiepyear . "
	             ORDER BY 1, rel.seqnum
	            ";

			$this->mainstreamServices = db::execSQL($SQL)->assocAll();
		}

		return $this->mainstreamServices;
	}

	/**
	 * Return SupplementaryServices
	 *
	 * @return array
	 */
	public function getSupplementaryServices() {
		if (!isset($this->supplementaryServices)) {
			$SQL = "
				SELECT gen_edu,
	                   std_c,
	                   std_oth,
	                   pers_c,
	                   pers_oth
	              FROM webset_tx.std_srv_suppl
	             WHERE std_refid = " . $this->tsrefid . "
	               AND iep_year = " . $this->stdiepyear . "
	            ";
			$this->supplementaryServices = db::execSQL($SQL)->assoc();
		}

		return $this->supplementaryServices;
	}

	/**
	 * Return Academic Schedule
	 */
	public function getAcademicSchedule() {
		if (!isset($this->academicSchedule)) {
			$SQL = "
				SELECT semester_txt,
	                   course,
	                   CASE spfreq.frequency  WHEN 'Other:' THEN COALESCE(spedfreq_oth, '')     ELSE spfreq.frequency  END || ' ' ||
	                   CASE spdur.duration    WHEN 'Other:' THEN COALESCE(spedduration_oth, '') ELSE spdur.duration    END AS duration,
	                   CASE sploc.location    WHEN 'Other:' THEN COALESCE(spedloc_oth, '')      ELSE sploc.location    END AS location,
	                   CASE genfreq.frequency WHEN 'Other:' THEN COALESCE(genfreq_oth, '')      ELSE genfreq.frequency END || ' ' ||
	                   CASE gendur.duration   WHEN 'Other:' THEN COALESCE(genduration_oth, '')  ELSE gendur.duration   END AS gen_duration,
	                   CASE genloc.location   WHEN 'Other:' THEN COALESCE(genloc_oth, '')       ELSE genloc.location   END AS gen_location
	              FROM webset_tx.std_srv_courses std
	                   INNER JOIN webset_tx.def_srv_frequency spfreq ON spfreq.frefid = std.spedfreq
	                   INNER JOIN webset_tx.def_srv_duration spdur ON spdur.drefid = std.spedduration
	                   INNER JOIN webset_tx.def_srv_locations sploc ON sploc.lrefid = std.spedloc
	                   INNER JOIN webset_tx.def_srv_frequency genfreq ON genfreq.frefid = std.genfreq
	                   INNER JOIN webset_tx.def_srv_duration gendur ON gendur.drefid = std.genduration
	                   INNER JOIN webset_tx.def_srv_locations genloc ON genloc.lrefid = std.genloc
	             WHERE stdrefid = " . $this->tsrefid . "
	               AND iep_year = " . $this->stdiepyear . "
	             ORDER BY order_num, refid
            ";

			$this->academicSchedule = db::execSQL($SQL)->assocAll();
		}

		return $this->academicSchedule;
	}

	/**
	 * Return arrangement
	 *
	 * @return array
	 */
	public function getInstructArrangement() {
		if (!isset($this->instructArrangement)) {
			$SQL = "
				SELECT COALESCE(to_char(period_dt, 'mm-dd-yyyy') || ' / ', '') || COALESCE(vouname,school_camp) AS location,
	                   spccode || ' - ' || spcdesc AS spcdesc,
	                   camp_attend,
	                   camp_attend_no ,
	                   camp_close ,
	                   camp_close_no ,
	                   instruct_day ,
	                   instruct_day_no,
	                   crtdesc,
	                   spc.validvalue AS speechcode,
	                   ppcd.validvalue AS ppcdcode
	              FROM webset_tx.std_instruct_arrange std
	                   LEFT OUTER JOIN webset.statedef_placementcategorycode ON spcrefid = placement
	                   LEFT OUTER JOIN sys_voumst ON vourefid = campus_id
	                   LEFT OUTER JOIN webset.disdef_location ON crtrefid = location
	                   LEFT OUTER JOIN webset.glb_validvalues ppcd ON ppcd.refid = std.ppcdind
	                   LEFT OUTER JOIN webset.glb_validvalues spc ON spc.refid = speechind
	             WHERE std_refid = " . $this->tsrefid . "
	             ORDER BY COALESCE(std.period_dt,std.lastupdate)
	            ";

			$this->instructArrangement = db::execSQL($SQL)->assocAll();
		}

		return $this->instructArrangement;
	}

	/**
	 * Return Transition Services
	 *
	 * @return array|bool
	 */
	public function getTransitionServices() {
		if (!isset($this->transitionServices)) {
			$SQL = "
				SELECT dt_age,
	                   age14,
	                   age16,
	                   career_c,
	                   career_t,
	                   courses_c,
	                   courses_t,
	                   notice17,
	                   inform17
	              FROM webset_tx.std_trans_serv
	             WHERE std_refid = " . $this->tsrefid . "
	               AND iep_year = " . $this->stdiepyear . "
	            ";

			$this->transitionServices = db::execSQL($SQL)->assoc();
		}

		return $this->transitionServices;
	}

	/**
	 * Returns Progress Report for Standard Goals data
	 *
	 * @param string $esy
	 * @return array
	 */
	public function getProgressReportStandard($esy = 'N') {
		if (!isset($this->progressReportStandard)) {
			$this->progressReportStandard = array();
			$goals = $this->standartBasedGoals($esy);

			$progresses = $this->db->execute("
				SELECT sprnarative,
				       stdgoalrefid,
				       stdbenchmarkrefid,
				       sprmarkingprd,
				       dsyrefid,
				       epsdesc
                  FROM webset_tx.std_sb_progress std
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
				$this->progressReportStandard[] = $line;

				//Attached Objectives Lines
				foreach ($goal['objectives'] as $objective) {
					$line = array();
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
					$this->progressReportStandard[] = $line;
				}
			}
		}

		return $this->progressReportStandard;
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
			$goals = $this->bgbGoals($esy);

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
	 * Returns Progress Report for Mainstream Goals data
	 *
	 * @param string $esy
	 * @return array
	 */
	public function getProgressReportMainstream($esy = 'N') {
		if (!isset($this->progressReportMainstream)) {
			$this->progressReportMainstream = array();
			$goal = $this->getMainstream($esy);

			$progresses = $this->db->execute("
				SELECT sprnarative,
				       stdgoalrefid,
				       sprmarkingprd,
				       dsyrefid,
				       epsdesc
                  FROM webset_tx.std_mainstr_progress std
                       INNER JOIN webset.disdef_progressrepext ext ON ext.eprefid = std.eprefid
                 WHERE stdrefid =  " . $this->tsrefid . "
            ")->assocAll();

			$iepyear = IDEAStudentIEPYear::factory($this->stdiepyear);
			$periods = IDEASchool::factory($this->get('vourefid'))
				->getMarkingPeriods(
					$iepyear->get(IDEAStudentIEPYear::F_BEG_DATE),
					$iepyear->get(IDEAStudentIEPYear::F_END_DATE)
				);
			$line = array();
			$line['goal'] = $goal['mainstream_taks'];
			$line['objective'] = '';
			foreach ($periods as $period) {

				$period['value'] = '';
				$period['narrative'] = '';
				foreach ($progresses as $progress) {
					if ($progress['dsyrefid'] == $period['dsyrefid'] &&
						$progress['sprmarkingprd'] == $period['bmnum'] &&
						$progress['stdgoalrefid'] == $goal['refid']
					) {
						$period['value'] = $progress['epsdesc'];
						$period['narrative'] = $progress['sprnarative'];
						break;
					}
				}
				$line['periods'][] = $period;
			}
			$this->progressReportMainstream[] = $line;
		}

		return $this->progressReportMainstream;
	}

	/**
	 * Creates an instance of this class
	 *
	 * @param int $tsRefID
	 * @param int $stdiepyear
	 * @return IDEAStudentTXARD
	 */
	public static function factory($tsRefID, $stdiepyear = 0) {
		return new IDEAStudentTXARD($tsRefID, $stdiepyear);
	}

	/**
	 * Return Transfer Packet data
	 *
	 * @return array|bool
	 */
	public function getTransferPacket() {
		if (!isset($this->transferPacket)) {
			$SQL = "
				SELECT field0,
	                   field1_yn,
	                   field1_oth,
	                   field2_yn,
	                   field2_oth,
	                   field3,
	                   field4,
	                   field5,
	                   field6,
	                   field7,
	                   field8
	              FROM webset_tx.std_transfer_packet
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->transferPacket = db::execSQL($SQL)->assoc();
		}

		return $this->transferPacket;
	}

	/**
	 * Return some data. Don't know what is it =_)
	 *
	 * @return array
	 */
	public function getAttDistrict() {
		if (!isset($this->attrDistrict)) {
			$SQL = "
				SELECT webset.vnd_att(s.stdrefid),
	                   webset.vou_att(s.stdrefid),
	                   vouaddline1,
	                   voucity,
	                   voustate,
	                   vouzip,
	                   vouphone,
	                   vouaddline2
				  FROM webset.sys_teacherstudentassignment t
	                   INNER JOIN webset.dmg_studentmst s ON s.stdrefid = t.stdrefid
	                   LEFT OUTER JOIN public.sys_voumst v ON v.vourefid = s.vourefid
	             WHERE tsrefid = " . $this->tsrefid . "
	            ";

			$this->attrDistrict = db::execSQL($SQL)->assoc();
		}

		return db::execSQL($SQL)->assoc();
	}

}
