<?php

	/**
	 * IDEAStudentMO.php
	 * Class return data for IEP Builder(State MO)
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 21-02-2014
	 */
	class IDEAStudentMO extends IDEAStudent {

		protected $docDetails = array();

		/**
		 * Something data for block Student Demographics(State MO)
		 *
		 * @var array
		 */
		protected $stdDemoSimple;

		/**
		 * Present Levels of Academic Achievement and Functional Performance
		 *
		 * @var array
		 */
		protected $presentLevels;

		/**
		 * SpecConsiderations
		 *
		 * @var array
		 */
		protected $specialConsiderations;

		/**
		 * ProgressReportIEP
		 *
		 * @var array
		 */
		protected $progressReportIEP;

		/**
		 * ProgressReportIEP
		 *
		 * @var array
		 */
		protected $bgbGoalsMeasures;

		/**
		 * Return document details by ID
		 *
		 * @param int $id
		 * @return array mixed
		 */
		public function getDocDetailsByID($id) {
			if (!isset($this->docDetails[$id])) {
				$SQL = "SELECT *
		              FROM webset.sped_doctype
		             WHERE drefid = $id
		           ";

				$this->docDetails[$id] = db::execSQL($SQL)->fields;
			}

			return $this->docDetails[$id];
		}

		/**
		 * Return data for block Student Demographics(State MO)
		 *
		 * @return array
		 */
		public function getStdDemoSimple() {
			if (!isset($this->stdDemoSimple)) {
				$SQL = "
				SELECT UPPER(stdlnm) as stdlnm,
		               UPPER(stdfnm) as stdfnm,
	                   UPPER(stdmnm) as stdmnm,
	                   stdschid,
	                   stdstateidnmbr
		          FROM webset.vw_dmg_studentmst
		         WHERE stdRefId = " . $this->tsrefid . "
		        ";

				$this->stdDemoSimple = db::execSQL($SQL)->fields;
			}

			return $this->stdDemoSimple;
		}

		/**
		 * Return Present Levels of Academic Achievement data
		 *
		 * @return array
		 */
		public function getPresentLevels() {
			if (!isset($this->presentLevels)) {
				$SQL = "
				SELECT prefid,
					   webset.std_plepmst.stdrefid,
					   TO_CHAR(stdevaldt,'MM/DD/YYYY') AS stdevaldt,
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
	                   INNER JOIN webset.sys_teacherstudentassignment ON tsrefid = webset.std_plepmst.stdrefid
	             WHERE tsrefid = " . $this->tsrefid . "
	            ";

				if ($this->stdiepyear > 0) $SQL .= " AND webset.std_plepmst.iepyear = " . $this->stdiepyear . "";

				$this->presentLevels = db::execSQL($SQL)->fields;
			}

			return $this->presentLevels;
		}

		/**
		 * Returns BGB Goals for Goals block in MO IEP
		 *
		 * @param string $esy
		 * @return array
		 */
		public function getProgressReportIEP($esy = 'N') {
			if (!isset($this->progressReportIEP)) {
				$this->progressReportIEP = array();
				$goals = $this->getBgbGoals($esy);

				$progresses = $this->db->execute("
					SELECT sprnarative,
					       stdgoalrefid,
					       stdbenchmarkrefid,
					       sprmarkingprd,
					       dsyrefid,
					       pr_result
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
				$marks = IDEADef::getValidValues('MOBGBProgressResults');

				foreach ($goals as $goal) {
					//Goal Line
					$table = array();
					$line = array();
					$line[] = 'Date of Report';
					foreach ($periods as $period) {
						$line[] = $period['bmendt'];
					}
					$table[] = $line;

					foreach ($marks as $mark) {
						$line = array();
						$line[] = $mark->get(IDEADefValidValue::F_VALUE);
						foreach ($periods as $period) {
							$checked = 'N';
							foreach ($progresses as $progress) {
								if ($progress['dsyrefid'] == $period['dsyrefid'] &&
									$progress['sprmarkingprd'] == $period['bmnum'] &&
									$progress['stdgoalrefid'] == $goal['grefid'] &&
									$progress['pr_result'] == $mark->get(IDEADefValidValue::F_REFID) &&
									$progress['stdbenchmarkrefid'] == ''
								) {
									$checked = 'Y';
									break;
								}
							}
							$line[] = $checked;
						}
						$table[] = $line;
					}
					$this->progressReportIEP[$goal['grefid']] = $table;
				}
			}
			return $this->progressReportIEP;
		}

		/**
		 * Returns Progress Report data for Goals block in MO IEP
		 *
		 * @param string $esy
		 * @return array
		 */
		public function getBgbGoalsMeasures($esy = 'N') {
			if (!isset($this->bgbGoalsMeasures)) {
				$this->bgbGoalsMeasures = array();
				$goals = $this->getBgbGoals($esy);
				$goals_ids = array();
				foreach($goals as $goal) {
					$goals_ids[] = $goal['grefid'];
				}
				$selections = $this->db->execute("
					SELECT meas_refid,
					       goal_refid,
					       other
				 	  FROM webset.std_bgb_goal_meas
                     WHERE goal_refid in (" . (count($goals_ids) == 0 ? '0' : implode(',', $goals_ids)) . ")
	            ")->assocAll();
				$measures = IDEADef::getValidValues('MOBGBProgressMeasurement');

				foreach ($goals as $goal) {
					$entries = array();
					foreach ($measures as $measure) {
						$entry = array();
						$entry['name'] = $measure->get(IDEADefValidValue::F_VALUE);
						$entry['value'] = 'N';
						$entry['narrative'] = '';
						foreach ($selections as $selection) {
							if ($selection['goal_refid'] == $goal['grefid'] && $selection['meas_refid'] == $measure->get(IDEADefValidValue::F_REFID)) {
								$entry['value'] = 'Y';
								$entry['narrative'] = $entry['name'] == 'Other' ? $selection['other'] : '';
							}
						}
						$entries[] = $entry;
					}
					$this->bgbGoalsMeasures[$goal['grefid']] = $entries;

				}
			}
			return $this->bgbGoalsMeasures;
		}

	}
