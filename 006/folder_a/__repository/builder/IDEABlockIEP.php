<?php

	/**
	 * IDEABlockIEP.php
	 * Class use common render functions for IEP document. This is methods calls in some child builders.
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 13-02-2014
	 */
	abstract class IDEABlockIEP extends IDEABlock {

		/**
		 * @var IDEAStudentTXARD
		 */
		protected $std;

		public function __construct() {
			parent::__construct();
		}

		public function setStd($tsRefID, $iepyear = null) {
			$this->std = new IDEAStudentTXARD($tsRefID);
		}

		/**
		 * Generate block Goals and Objectives
		 */
		public function renderGoals() {
			# first field for checkboxes 'Other'
			$other = 1;
			$reporting = $this->std->progresReportingStd();
			$labels = $this->std->progresReporting();
			$count = count($labels);
			$layout = RCLayout::factory()
				->addText('<b>B. Goals and Objectives:</b>')
				->newLine('.martop10')
				->addObject($this->addCheck($reporting['field0'] == 'Y' ? 'Y' : 'N'), '.width20')
				->addText(
					'Prior to determining placement, the ARD/IEP committee developed IEP goals and objectives and/or accommodations based on consideration of current assessment and the student\'s educational needs <i>(see attached)</i>.'
					, 'padtop5'
				)
				->newLine('.martop10')
				->addText('1.', '.width20')
				->addText('<b>Schedule for IEP evaluation:</b> Annually');

			$numTitle = 2;
			for ($i = 0; $i < $count; $i++) {
				# add title with number
				if (!isset($labels[$i - 1]) || $labels[$i - 1]['go_desc'] != $labels[$i]['go_desc']) {
					$layout->newLine('.martop10')
						->addText($numTitle . '.', '.width20')
						->addText('<b>' . $labels[$i]['go_desc'] . '</b>')
						->newLine('.martop10');
					$numTitle++;
				}
				# if checkbox 'Other' add border-bottom for text
				if (!isset($labels[$i + 1]) || $labels[$i + 1]['go_desc'] != $labels[$i]['go_desc']) {
					# add num field to key
					$bus = 'field' . $other . '_bas';
					$busLabel = 'field' . $other . '_oth';
					$check = $this->checkSwitcher($labels[$i]['go_sub_refid'], $reporting[$bus]);
					$layout->addObject($this->addCheck($check), '.width20')
						->addText($labels[$i]['go_sub_desc'], '[padding-top: 5px; width: 30px;]')
						->addText(
							isset($reporting[$busLabel]) ? $reporting[$busLabel] : '',
							'[width: 50px; border-bottom: 1px solid black; padding-top: 5px;]'
						);
					# next Other field
					$other++;
				} else {
					$check = $this->checkSwitcher($labels[$i]['go_refid'], $labels[$i]['go_sub_refid']);
					$layout->addObject($this->addCheck($check), '.width20')
						->addText($labels[$i]['go_sub_desc'], '.padtop5');
				}
			}

			$layout->newLine('.martop10')
				->addText('<i>Note: The parent(s) will be regularly informed of their child\'s progress (through such means as periodic reports) at least as often as parents are informed of their non-disabled children\'s progress.</i>');
			$this->rcDoc->addObject($layout);
			$basedGoals = $this->std->standartBasedGoals();
			if (isset($basedGoals[0])) {
				$this->rcDoc->startNewPage();
				$this->commonGoals($basedGoals, 'B. Standards Based Goals and Objectives');
			}
			$bgbGoals = $this->std->bgbGoals();
			if (isset($bgbGoals[0])) {
				$this->rcDoc->startNewPage();
				$this->commonGoals($bgbGoals, 'B. Goals and Objectives');
			}
			$mainstreamGoals = $this->std->getMainstream();
			if (count($mainstreamGoals) > 0) {
				$this->rcDoc->startNewPage();
				$this->commonGoals(array($mainstreamGoals), 'B. Mainstream', false);
				$this->goalsMainstream($mainstreamGoals);
			}
		}

		/**
		 * Check in array value. If exist return 'Y' for checkbox
		 *
		 * @param array $array
		 * @param string $val
		 * @return string
		 */
		protected function checkArraySwitcher($array, $val) {
			if (in_array($val, $array)) {
				return 'Y';
			} else {
				return 'N';
			}
		}

		/**
		 * Render page for one Goal. Each goal can include objectives or no(mainstream goals).
		 * Be careful in method many recursion
		 *
		 * @param array $goals
		 * @param string $title
		 * @param bool $fullPage If true add to DOC table with Objectives
		 * @param string $ESY
		 */
		protected function commonGoals($goals, $title, $fullPage = true, $ESY = 'No') {
			$layout = new RCLayout();
			$half = new RCStyle('[width: 50%;]');
			$validV = IDEADef::getValidValues('TXServiceLoc');
			$schedule = IDEADef::getValidValues('TXServiceSchedule');
			$eval = IDEADef::getValidValues('TXbgbEval');
			$notice = IDEADef::getValidValues('TXServiceNotice');
			$sumNotice = count($notice);
			$sumSchedule = count($schedule);
			$sumValidV = count($validV);
			$sumGoals = count($goals);

			for ($i = 0; $i < $sumGoals; $i++) {
				# values for check Service Type
				$check = $this->checkSwitcher($goals[$i]['servtype'], 'I');
				$check2 = $this->checkSwitcher($goals[$i]['servtype'], 'R');
				$layout->newLine()
					->addText('<b>' . $title . ' : </b>')
					->newLine('.martop10')
					->addObject($this->addCheck($check), '.width20')
					->addText('Instructional Services', '.padtop5')
					->newLine('.martop10')
					->addObject($this->addCheck($check2), '.width20')
					->addText('Related Services', '.padtop5')
					->newLine('.martop10')
					->addText('Duration of services from: <i>' . $goals[$i]['durbeg'] . ' to ' . $goals[$i]['durend'] . '</i>')
					->newLine('.martop10')
					->addText('<b>Location of Service:</b>', $half)
					->addText('<b>Implementors:</b>');
				#clear checks
				$check = $check2 = null;
				for ($j = 0; $j < $sumValidV; $j++) {
					$check = $this->checkSwitcher($validV[$j]->get(IDEADefValidValue::F_VALUE_ID), $goals[$i]['location']);
					$check2 = $this->checkSwitcher($validV[$j]->get(IDEADefValidValue::F_VALUE_ID), $goals[$i]['implement']);
					$layout->newLine()
						->addObject(
							RCLayout::factory()
								->addObject($this->addCheck($check), '.width20')
								->addText(
									$validV[$j]->get(IDEADefValidValue::F_VALUE) . '<i> ' . $goals[$i]['locationoth'] . '</i>',
									'.padtop5'
								)
							, $half
						)
						->addObject($this->addCheck($check2), '.width20')
						->addText(
							$validV[$j]->get(IDEADefValidValue::F_VALUE) . '<i> ' . $goals[$i]['implementoth'] . '</i>',
							'.padtop5'
						);
				}
				# clear
				$check = $check2 = null;
				# if not mainstream Goals(if goals have objectives)
				if ($fullPage === true) {
					$leftCenter = new RCStyle('center [width: 10%; border-left: 1px solid black;]');
					# add objectives for this goal
					$tbl = RCTable::factory('.table')
						->addLeftHeading('<b>Area: </b><i>' . $goals[$i]['subject'] . '</i>')
						->addLeftHeading('<b>Measurable Annual Goal</b>', '<i>' . $goals[$i]['gsentance'] . '</i>', '[width: 500px;]')
						->addRow('.row')
						->addCell('', '[width: 70%;]')
						->addCell('Level of Mastery Criteria', $leftCenter)
						->addCell('Evaluation Procedures', $leftCenter)
						->addCell('ESY*', $leftCenter);
					#add rows with objectives
					foreach ($goals[$i]['objectives'] as $objective) {
						$tbl->addRow('.row')
							# don't know why, but tag <i> print to PDF on the 3d line(add before bsentance)
							->addCell($objective['bsentance'], '[text-align: left; font-style: italic;]')
							->addCell('<i>' . $objective['obj_level'] . '</i>', '.cellBorder')
							->addCell('<i>' . $objective['evalproc'] . PHP_EOL . 'Other</i>', '.cellBorder')
							->addCell($ESY, '.cellBorder');
					}

					$third = new RCStyle('[width: 30%;]');
					$layout->newLine('.martop5')
						->addObject($tbl)
						->newLine('.martop10')
						->addText('<b>Evaluation Procedures:</b>', $third)
						->addText('', $third)
						->addText('<b>Schedule for evaluation of goals and objectives:</b>');

					for ($z = 0; $z < $sumSchedule; $z++) {
						if (isset($eval[$z + 6])) {
							$label = $eval[$z + 6]->get(IDEADefValidValue::F_VALUE_ID) . ' - ' . $eval[$z + 6]->get(IDEADefValidValue::F_VALUE);
						} else {
							$label = '';
						}
						# add value other from db
						$other = $this->checkOther($goals[$i]['scheduleoth'], $i, $sumSchedule);
						$check = $this->checkSwitcher($goals[$i]['schedule'], $schedule[$z]->get(IDEADefValidValue::F_VALUE_ID));
						$layout->newLine()
							->addText(
								$eval[$z]->get(IDEADefValidValue::F_VALUE_ID) . ' - ' . $eval[$z]->get(IDEADefValidValue::F_VALUE)
							)
							->addText($label)
							->addObject($this->addCheck($check), '.width20')
							->addText($schedule[$z]->get(IDEADefValidValue::F_VALUE) . $other, '.padtop5');
					}

					# clear
					$check = null;
					$layout->newLine('.martop10')
						->addText('<b>Parents will be notified of student progress by</b>');
					# keys notices for this goal
					$keys = explode(',', $goals[$i]['notice']);

					for ($k = 0; $k < $sumNotice; $k++) {
						if ($k % 3 == 0) {
							$layout->newLine();
						}
						# add value other from db
						$other = $this->checkOther($goals[$i]['noticeoth'], $k, $sumNotice);
						$check = $this->checkArraySwitcher($keys, $notice[$k]->get(IDEADefValidValue::F_VALUE_ID));
						$layout->addObject($this->addCheck($check), '.width20')
							->addText($notice[$k]->get(IDEADefValidValue::F_VALUE) . $other, '.padtop5');
					}

					$check = null;
					$layout->newLine()
						->addText('<i>Parents will be informed if progress is not sufficient to enable the student to achieve the goals by the end of the year.</i>');
				}

				# page for next goal
				if ($i > 0) $this->rcDoc->startNewPage();
				$this->rcDoc->addObject($layout);
				# clear layout for next itteration
				$layout = new RCLayout();
			}
		}

		/**
		 * Add to PDF block with mainstream goal. Not use objectives
		 *
		 * @param array $mainstreamGoals
		 */
		protected function goalsMainstream($mainstreamGoals) {
			$mainstream = $this->std->getMainstream();
			$eval = IDEADef::getValidValues('TXbgbEval');
			$sumEval = count($eval);
			$layout = RCLayout::factory()
				->newLine('.martop10')
				->addText('<b>' . $mainstream['mainstream_taks'] . '</b>')
				->newLine('.martop10')
				->addText('<b>Level of Mastery Criteria: </b><i>' . $mainstream['level'] . '</i>')
				->newLine('.martop10')
				->addText('<b>Evaluation Procedures:</b>');

			$keys = explode(',', $mainstreamGoals['evalproc']);

			for ($i = 0; $i < $sumEval; $i++) {
				if ($i == 0 || $i % 3 == 0) {
					$layout->newLine();
				}
				$other = $this->checkOther($mainstreamGoals['evalprocoth'], $i, $sumEval);

				$layout->addObject(
					$this->addCheck(
						$this->checkArraySwitcher($keys, $eval[$i]->get(IDEADefValidValue::F_VALUE_ID))
					)
					, '.width20'
				)
					->addText($eval[$i]->get(IDEADefValidValue::F_VALUE) . $other, '.padtop5');
				$other = null;
			}

			$layout->newLine('.martop10')
				->addText('<b>Schedule for evaluation of goals and objectives:</b>');
			# add checkboxes with schedules
			$schedule = IDEADef::getValidValues('TXServiceSchedule');
			$sumschedule = count($schedule);

			for ($i = 0; $i < $sumschedule; $i++) {
				if ($i == 0 || $i % 3 == 0) {
					$layout->newLine();
				}
				# create notice 'Other'
				$other = $this->checkOther($mainstreamGoals['scheduleoth'], $i, $sumschedule);

				$layout->addObject(
					$this->addCheck(
						$this->checkSwitcher($mainstreamGoals['schedule'], $schedule[$i]->get(IDEADefValidValue::F_VALUE_ID))
					)
					, '.width20'
				)
					->addText($schedule[$i]->get(IDEADefValidValue::F_VALUE) . $other, '.padtop5');
			}

			$layout->newLine('.martop10')
				->addText('<b>Parents will be notified of student progress by</b>');

			# add notices checkboxes
			$notices = IDEADef::getValidValues('TXServiceNotice');
			$sumNotices = count($notices);
			$selected = explode(',', $mainstreamGoals['notice']);

			for ($i = 0; $i < $sumNotices; $i++) {
				if ($i == 0 || $i % 3 == 0) {
					$layout->newLine();
				}
				# create notice 'Other'
				$other = $this->checkOther($mainstreamGoals['noticeoth'], $i, $sumNotices);
				$layout->addObject(
					$this->addCheck($this->checkArraySwitcher($selected, $notices[$i]->get(IDEADefValidValue::F_VALUE_ID))),
					'.width20'
				)
					->addText($schedule[$i]->get(IDEADefValidValue::F_VALUE) . $other, '.padtop5');
			}

			$layout->newLine()
				->addText('<i>Parents will be informed if progress is not sufficient to enable the student to achieve the goals by the end of the year.</i>');
			$this->rcDoc->newLine()->addObject($layout);
		}

		/**
		 * Check if last label for checkbox(Other) add to label information
		 *
		 * @param string $other value for label Other
		 * @param int $i num itteration
		 * @param int $sum sum elements array
		 * @return string value for checkbox
		 */
		protected function checkOther($other, $i, $sum) {
			if ($i == $sum - 1) {
				return '<i> ' . $other . '</i>';
			} else {
				return '';
			}
		}

		/**
		 * Generate block Program Interventions
		 */
		public function renderProgramInterventions() {
			$layout = RCLayout::factory()
				->addText('<b>C. Program Interventions and Accommodations: ' . $this->std->getProgramInterventArea(1) . '</b>')
				->newLine('.martop10')
				->addText('Student: ', new RCStyle('[width: 45px;]'))
				->addText(
					'<i>' . $this->std->get('stdname') . '</i>',
					new RCStyle('[width: 150px; border-bottom: 1px solid black;]')
				)
				->addText('ID#: ', new RCStyle('[width: 30px; margin-left: 5px;]'))
				->addText('<i>' . (string)$this->std->get('stdschid') . '</i>', '[width: 90px; border-bottom: 1px solid black;]')
				->addText('DOB: ', new RCStyle('[width: 30px; padding-left: 5px;]'))
				->addText(
					'<i>' . $this->std->get('stddob') . '</i>',
					new RCStyle('[width: 90px; border-bottom: 1px solid black; margin-left: 5px;]')
				)
				->addText('ARD Date:', '[width: 50px; margin-left: 5px;]')
				->addText((string)$this->std->get('stdiepmeetingdt'), '[border-bottom: 1px solid black; font-style: italic;]')
				->newLine('.martop10')
				->addText('The ARD/IEP Committee has determined that the following checked modifications are necessary for the student to advance in the general curriculum, achieve his/her goals and objectives and be educated with non-disabled students to the maximum extent appropriate:');

			$check = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'tx_iep',
				'Program Interventions',
				$this->std->get('stdiepyear')
			);

			$tbl = $this->tblInterventions(1);
			if ($tbl != null) {
				$layout->newLine('.martop10')
					->addObject($tbl);
			}

			$tbl = $this->tblInterventions(2);

			if ($tbl != null) {
				$layout->newLine()
					->addText('<b>C. Program Interventions and Accommodations: ' . $this->std->getProgramInterventArea(2) . '</b>')
					->newLine('.martop10')
					->addObject($tbl);
			}

			$layout->newLine('.martop10');
			$this->addYN($layout, $check);
			$layout->addText('The student has a BIP.', '.padtop5');
			$this->rcDoc->addObject($layout);
		}

		/**
		 * Create table by area for method renderProgramInterventions
		 *
		 * @param int $areaID
		 * @return RCTable||null
		 */
		protected function tblInterventions($areaID) {
			$data = $this->std->getProgramInterventStudent($areaID);
			$countCol = count($data);
			$center = new RCStyle('center [border-left: 1px solid black;]');

			if ($countCol > 0) {
				$tbl = new RCTable('.table');
				$tbl->setCol('300px')
					->setCol('')
					->setCol('')
					->setCol('')
					->setCol('')
					->setCol('')
					->setCol('')
					->setCol('')
					->setCol('')
					->addRow('.row')
					->addCell('', '.hr');
				# header table
				foreach ($data[0]['subjects'] as $hr) {
					$tbl->addCell('<b>' . $hr['coalesce'] . '</b>', '.next-hr');
				}
				# rows
				for ($i = 0; $i < $countCol; $i++) {
					# add name Group Modifications
					if (!isset($data[$i - 1]) || $data[$i - 1]['mod_desc'] != $data[$i]['mod_desc']) {
						$tbl->addRow('.row')
							->addCell('<b>' . $data[$i]['mod_desc'] . '</b>', '.cellBorder');
						# empty cells
						for ($j = 0; $j < 8; $j++) {
							$tbl->addCell('', '.cellBorder');
						}
					}

					# name modification can be in accommodation || sub_mod_desc
					$modification = '';
					if (isset($data[$i]['sub_mod_desc'])) {
						$modification = $data[$i]['sub_mod_desc'];
					}
					if (isset($data[$i]['accommodation'])) {
						$modification = $data[$i]['accommodation'];
					}
					$tbl->addRow('.row')
						->addCell($modification, '.cellBorder');
					# selected values for row
					foreach ($data[$i]['subjects'] as $cell) {
						$tbl->addCell($cell['selected'] == 1 ? 'v' : '', $center);
					}
				}

				return $tbl;
			} else {
				return null;
			}
		}

		/**
		 * Generate block Assistive Technology
		 */
		public function renderAssistiveTech() {
			$check = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'tx_iep',
				'assistive_technology',
				$this->std->get('stdiepyear')
			);

			$layout = RCLayout::factory()
				->addText('<b>D. Assistive Technology (AT):</b>')
				->newLine('.martop10')
				->addText('In its discussion of Assistive Technology, the ARD/IEP committee reviewed the present levels of performance and goals and objectives as written and determined the following:')
				->newLine('.martop10')
				->addObject($this->addCheck($check), '.width20')
				->addText(
					'Assistive technology is required in order for the student to access the curriculum and make satisfactory academic progress. <i>See recommended devices/services noted in Section C. "Program Interventions, Accommodations or other Program Modifications."</i>'
					, '.padtop5'
				)
				->newLine('.martop10')
				->addObject($this->addCheck($check == 'N' ? 'Y' : 'N'), '.width20')
				->addText(
					'The student does not need assistive technology devices in order to access the curriculum and make satisfactory progress.'
					, '.padtop5'
				);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Texas Assessment Program
		 */
		public function renderTexasAssessment() {
			$stateAssessments = $this->std->getStateAssessments();
			$countAssess = count($stateAssessments);
			$layout = new RCLayout();
			if ($countAssess > 1) {
				$rowBottom = new RCStyle('[border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;]');
				$row = new RCStyle('[border-left: 1px solid black; border-right: 1px solid black;]');
				$cellBorder = new RCStyle('[width: 120px; border-right: 1px solid black;]');
				$center = new RCStyle('center [font-weight: bold;]');

				for ($i = 0; $i < $countAssess; $i++) {
					$tbl = RCLayout::factory()
						->newLine('[background: #C0C0C0; border: 1px solid black;]')
						->addText('State of Texas Assessments of Academic Readiness (STAAR)', $center);

					$this->addYN(
						$tbl->newLine($row)
							->addObject($this->addCheck($stateAssessments[$i]['staar_take']), '.width20')
							->addText('Yes', '[width: 20px; padding-top: 5px;]')
							->addObject($this->addCheck($stateAssessments[$i]['staar_take'] == 'N' ? 'Y' : 'N'), '.width20')
							->addText('No', '[width: 20px; padding-top: 5px;]')
							->addObject($this->addCheck($stateAssessments[$i]['staar_take'] == 'A' ? 'Y' : 'N'), '.width20')
							->addText('N/A', '[width: 20px; padding-top: 5px;]'));
					$tbl->addText('The student will take the STAAR', '.padtop5')
						->newLine($rowBottom)
						->addText('If no, identify the reason: <i>' . $stateAssessments[$i]['staar_whynot'] . '</i>');

					if (isset($stateAssessments[0]['staar_subjects'])) {
						$tbl->newLine($rowBottom)
							->addText('<b>Assessment</b>', $cellBorder)
							->addText('<b>Subject</b>', $cellBorder)
							->addText('<b>Language</b>', $cellBorder)
							->addText('<b>Grade</b>', $cellBorder)
							->addText('<b>Accommodation</b>');
						# STAAR subjects(Rationale for taking the STAAR:)
						foreach ($stateAssessments[$i]['staar_subjects'] as $subject) {
							$tbl->newLine($rowBottom)
								->addText('<i>' . $subject['assessments'] . '</i>', $cellBorder)
								->addText('<i>' . $subject['subjects'] . '</i>', $cellBorder)
								->addText('<i>' . $subject['languages'] . '</i>', $cellBorder)
								->addText('<i>' . $subject['grades'] . '</i>', $cellBorder)
								->addText('<i>' . $subject['accomodation'] . '</i>');
						}

						$tbl->newLine($row)
							->addText('Rationale for taking the STAAR:', 'bold center [border-bottom: 1px solid black;]')
							->newLine($row)
							->addText('The student is receiving TEKS instruction on grade level and does not need any accommodations and/or modifications that would invalidate STAAR.');
						# add STAAR_ratio(Student Success Initiative)
						$sumSTAAR = count($stateAssessments[$i]['staar_ratio']);

						for ($j = 0; $j < $sumSTAAR; $j++) {
							# id last row add border-bottom
							if ($j == $sumSTAAR - 1) {
								$tbl->newLine($rowBottom);
							} else {
								$tbl->newLine($row);
							}

							$ratio = $stateAssessments[$i]['staar_ratio'][$j];
							$tbl->addText('<b>' . $ratio['subject'] . ': </b><i>' . $ratio['rationale'] . '</i>');
						}

						# STAAR_success(table Student Success Initiative)
						$this->texasTable($tbl, $stateAssessments[$i], 'staar_success', 'Student Success Initiative');
						# Add STAAR_success(table Accelerated Instruction)
						$this->texasTable($tbl, $stateAssessments[$i], 'staar_acceler', 'Accelerated Instruction');
						# TAKS_subjects(Texas Assessment of Knowledge & Skills (TAKS))
						$tbl->newLine('bold [background: #C0C0C0; border: 1px solid black; border-top: none;]')
							->addText('Texas Assessment of Knowledge & Skills (TAKS)', 'center');
						$this->addYN($tbl->newLine($row), $stateAssessments[$i]['taks_take']);
						$tbl->addText('The student will take the TAKS', '.padtop5')
							->newLine($row)
							->addText(
								'If no, identify the reason: <i>' . $stateAssessments[$i]['taks_whynot'] . '</i>',
								'[padding-left: 20px; border-bottom: 1px solid black;]
						');

						$tbl->newLine($rowBottom)
							->addText('<b>Assessment</b>', $cellBorder)
							->addText('<b>Subject</b>', $cellBorder)
							->addText('<b>Language</b>', $cellBorder)
							->addText('<b>Grade</b>', $cellBorder)
							->addText('<b>Accommodation</b>');

						# rows TAKS Subjects
						$sumAcceler = count($stateAssessments[$i]['staar_acceler']);
						for ($j = 0; $j < $sumAcceler; $j++) {
							$taks = $stateAssessments[$i]['taks_subjects'][$j];
							$tbl->newLine($rowBottom)
								->addText('<i>' . $taks['assessments'] . '</i>', $cellBorder)
								->addText('<i>' . $taks['subjects'] . '</i>', $cellBorder)
								->addText('<i>' . $taks['languages'] . '</i>', $cellBorder)
								->addText('<i>' . $taks['languages'] . '</i>', $cellBorder)
								->addText('<i>' . $taks['accomodation'] . '</i>');
						}

						$tbl->newLine($rowBottom)
							->addText('Rationale for taking the TAKS:', 'bold center')
							->newLine($row)
							->addText(
								'The student is receiving TEKS instruction on grade level and does not need any accommodations and/or modifications that would invalidate TAKS.'
								, '.padtop5'
							);

						# TAKS ratio
						$sumTaks = count($stateAssessments[$i]['taks_ratio']);
						for ($j = 0; $j < $sumTaks; $j++) {
							$taksRatio = $stateAssessments[$i]['taks_ratio'][$j];
							# id last row add border-bottom
							if ($j == $sumTaks - 1) {
								$tbl->newLine($rowBottom);
							} else {
								$tbl->newLine($row);
							}

							$tbl->addText('<b>' . $taksRatio['subject'] . ': </b><i>' . $taksRatio['rationale'] . '</i>');
						}
						# Add Table Student Success Initiative
						$this->texasTable($tbl, $stateAssessments[$i], 'taks_success', 'Student Success Initiative');
						# Add Table Accelerated Instruction
						$this->texasTable($tbl, $stateAssessments[$i], 'taks_acceler', 'Accelerated Instruction');
						# TEXAS ENGLISH LANGUAGE PROFICIENCY ASSESSMENT SYSTEM (TELPAS)
						$tbl->newLine('bold [background: #C0C0C0; border: 1px solid black; border-top: none;]')
							->addText('TEXAS ENGLISH LANGUAGE PROFICIENCY ASSESSMENT SYSTEM (TELPAS)', 'center');
						# TELPAS Reading
						$tbl->newLine('bold [background: #C0C0C0; border: 1px solid black; border-top: none;]')
							->addText('TELPAS Reading', 'center')
							->newLine($row)
							->addObject($this->addCheck($stateAssessments[$i]['telpas_take']), '.width20')
							->addText('Yes', '[width: 20px; padding-top: 5px;]')
							->addObject($this->addCheck($stateAssessments[$i]['telpas_take'] == 'N' ? 'Y' : 'N'), '.width20')
							->addText('No', '[width: 20px; padding-top: 5px;]')
							->addObject($this->addCheck($stateAssessments[$i]['telpas_take'] == 'A' ? 'Y' : 'N'), '.width20')
							->addText('N/A', '[width: 20px; padding-top: 5px;]')
							->addText('The student will take the TELPAS Reading', '.padtop5')
							->newLine($rowBottom)
							->addText(
								'If no, identify the local alternative assessment: <i>' . $stateAssessments[$i]['telpas_alternative'] . '</i>',
								'[padding-left: 40px;]'
							);
						# TELPAS
						$tbl->newLine('bold [background: #C0C0C0; border: 1px solid black; border-top: none;]')
							->addText('TELPAS', 'center')
							->newLine($rowBottom)
							->addText('<b>Assessment</b>', $cellBorder)
							->addText('<b>Subject</b>', $cellBorder)
							->addText('<b>Language</b>', $cellBorder)
							->addText('<b>Grade</b>', $cellBorder)
							->addText('<b>Accommodation</b>');

						$topSubjects = count($stateAssessments[$i]['top_subjects']);

						for ($j = 0; $j < $topSubjects; $j++) {
							$subj = $stateAssessments[$i]['top_subjects'][$j];
							$tbl->newLine($rowBottom)
								->addText('<i>' . $subj['assessments'] . '</i>', $cellBorder)
								->addText('<i>' . $subj['subjects'] . '</i>', $cellBorder)
								->addText('<i>' . $subj['languages'] . '</i>', $cellBorder)
								->addText('<i>' . $subj['languages'] . '</i>', $cellBorder)
								->addText('<i>' . $subj['accomodation'] . '</i>');
						}

						# Additional Assessment(s)
						$tbl->newLine('bold [background: #C0C0C0; border: 1px solid black; border-top: none;]')
							->addText('Additional Assessment(s)', 'center')
							->newLine($row)
							->addObject($this->addCheck($stateAssessments[$i]['additional_take']), '.width20')
							->addText('Yes', '[width: 20px; padding-top: 5px;]')
							->addObject($this->addCheck($stateAssessments[$i]['additional_take'] == 'N' ? 'Y' : 'N'), '.width20')
							->addText('No', '[width: 20px; padding-top: 5px;]')
							->addObject($this->addCheck($stateAssessments[$i]['additional_take'] == 'A' ? 'Y' : 'N'), '.width20')
							->addText('N/A', '[width: 20px; padding-top: 5px;]')
							->addText('The student will participate in the following assessment(s):', '.padtop5')
							->newLine($rowBottom)
							->addText(
								'If no, identify the local alternative assessment: <i>' . $stateAssessments[$i]['additional_assessment'] . '</i>',
								'[padding-left: 40px;]'
							);
					}

					$layout->newLine('.martop10')
						->addText('<b>E. ARD/IEP Committee Decision Making Process for the Texas Assessment Program:</b>')
						->newLine('.martop10')
						->addText('Student: ', '[width: 40px;]')
						->addText('<i>' . $this->std->get('stdname') . '</i>', '[width: 150px; border-bottom: 1px solid black;]')
						->addText('Enrollment grade at testing:', '[width: 125px; padding-left: 20px;]')
						->addText('<i>' . $stateAssessments[$i]['samgrade'] . '</i>', '[width: 30px; border-bottom: 1px solid black;]')
						->addText('Date: ', '[width: 50px; padding-left: 20px;]')
						->addText('<i>' . $stateAssessments[$i]['samdate'] . '</i>', '[width: 100px; border-bottom: 1px solid black;]')
						->newLine()
						->addText('School Year:', '[width: 50px;]')
						->addText('<i>' . $stateAssessments[$i]['samdate'] . '</i>', '[width: 140px; border-bottom: 1px solid black;]')
						->addText('Description:', '[width: 75px; padding-left: 20px;]')
						->addText('<i>' . $stateAssessments[$i]['samdesc'] . '</i>', '[width: 130px; border-bottom: 1px solid black;]')
						->newLine('.martop10')
						->addObject($tbl)
						->newLine()
						->addText('<i>ASSURANCE:  The ARD/IEP committee considered as appropriate the results of the child\'s performance on any general state or district-wide assessment programs.</i>');
				}
			}

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Add rows to Texas table
		 *
		 * @param RCLayout $tbl
		 * @param array $assess assessments
		 * @param string $key column name
		 * @param string $title
		 */
		protected function texasTable(RCLayout $tbl, $assess, $key, $title) {
			$rowBottom = new RCStyle('[border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;]');
			$cellBorder = new RCStyle('[width: 120px; border-right: 1px solid black;]');

			$tbl->newLine('bold [background: #C0C0C0; border: 1px solid black; border-top: none;]')
				->addText($title, 'center');

			$sumItems = count($assess[$key]);

			for ($j = 0; $j < $sumItems; $j++) {
				$acceler = $assess[$key][$j];
				$tbl->newLine($rowBottom)
					->addText('<i>' . $acceler['assessment'] . '</i>', $cellBorder)
					->addText('<i>' . $acceler['subject'] . '</i>', $cellBorder)
					->addText('<i>' . $acceler['grade'] . '</i>', $cellBorder)
					->addText('<i>' . $acceler['plans'] . '</i>');
			}
		}

		/**
		 * Generate block Schedule of Services
		 */
		public function renderScheduleServices() {
			$layout = RCLayout::factory()
				->newLine()
				->addText('<b>G. Schedule of Services:</b>')
				->newLine('.martop10')
				->addText('1. ACADEMIC SCHEDULE:');

			$academic = $this->std->getAcademicSchedule();
			$sumAcadem = count($academic);

			if ($sumAcadem > 0) {
				for ($i = 0; $i < $sumAcadem; $i++) {
					$tbl = RCTable::factory('.table')
						->addLeftHeading('<i>' . $academic[$i]['semester_txt'] . '</i>')
						->addRow('.row')
						->addCell('Course', '.hr')
						->addCell('Sp. Ed. Time', '.next-hr')
						->addCell('Sp. Ed. Location', '.next-hr')
						->addCell('Gen. Ed. Time', '.next-hr')
						->addCell('Gen. Ed. Location', '.next-hr');

					$tbl->addRow('.row')
						->addCell('<i>' . $academic[$i]['course'] . '</i>', '.cellBorder')
						->addCell('<i>' . $academic[$i]['duration'] . '</i>', '.cellBorder')
						->addCell('<i>' . $academic[$i]['gen_location'] . '</i>', '.cellBorder')
						->addCell('<i>' . $academic[$i]['gen_duration'] . '</i>', '.cellBorder')
						->addCell('<i>' . $academic[$i]['location'] . '</i>', '.cellBorder');

					$layout->newLine()
						->addObject($tbl);
				}
			}

			$constr = $this->std->getConstruction(117);
			if ($constr) {
				$xml = simplexml_load_string(base64_decode($constr['values']));
				$layout->newLine()
					->addText((string)$xml->value, '[font-style: italic;]');
			}

			$layout->newLine('.martop10')
				->addText('2. MAINSTREAM INSTRUCTIONAL SETTING:')
				->newLine('.martop10')
				->addText('If the student is mainstreamed, qualified special education personnel will provide the following services to the student and/or to the student\'s general education teacher(s):');
			# table with Mainstream Services
			$mainstreamServices = $this->std->getMainstreamServices();
			$tbl = RCTable::factory('.table')
				->setCol('250px')
				->setCol('')
				->setCol('')
				->setCol('')
				->setCol('')
				->addRow('.row')
				->addCell('Course', '.hr')
				->addCell('Start Date', '.next-hr')
				->addCell('Frequency', '.next-hr')
				->addCell('Location', '.next-hr')
				->addCell('Duration', '.next-hr');

			foreach ($mainstreamServices as $row) {
				$tbl->addRow('.row')
					->addCell('<i>' . $row['servicetxt'] . '</i>', '.cellBorder')
					->addCell('<i>' . $row['start'] . '</i>', '.cellBorder')
					->addCell('<i>' . $row['frequency'] . '</i>', '.cellBorder')
					->addCell('<i>' . $row['location'] . '</i>', '.cellBorder')
					->addCell('<i>' . $row['duration'] . '</i>', '.cellBorder');
			}

			$layout->newLine()
				->addObject($tbl)
				->newLine()
				->addText('3. SUPPLEMENTARY SERVICES AND PROGRAM SUPPORT:')
				->newLine()
				->addText('<i>(Consisting of those services, aids, and other supports that are provided in general education classes, or other related settings to enable students with disabilities to be educated with non-disabled students to the maximum extent appropriate)</i>')
				->newLine('.martop10');

			$supplementary = $this->std->getSupplementaryServices();
			# supplementary checkboxes
			$this->addYN($layout, $supplementary['gen_edu']);
			$layout->addText(
				'The student is in need of support in the general education setting. <i>(If yes, please complete):</i>'
				, '.padtop5'
			)
				->newLine()
				->addText('<b>Supplementary Aids and Services for the Student</b>', 'center')
				->newLine()
				->addObject(
					$this->addCheck($supplementary['std_c'])
					, '.width20'
				)
				->addText('See Section C. "Program Interventions, Accommodations or other Program Modifications"', '.padtop5')
				->newLine()
				->addObject(
					$this->addCheck($supplementary['std_oth'] == '' ? 'N' : 'Y')
					, '.width20'
				)
				->addText('Other: <i>' . $supplementary['std_oth'] . '</i>', '.padtop5')
				->newLine('.martop10')
				->addText('<b>Program Modifications for Support For School Personnel</b>', 'center')
				->newLine()
				->addObject(
					$this->addCheck($supplementary['pers_c'])
					, '.width20'
				)
				->addText('See Section C. "Program Interventions, Accommodations or other Program Modifications"', '.padtop5')
				->newLine()
				->addObject(
					$this->addCheck($supplementary['pers_oth'] == '' ? 'N' : 'Y')
					, '.width20'
				)
				->addText('Other: <i>' . $supplementary['pers_oth'] . '</i>', '.padtop5')
				->newLine('.martop10')
				->addText('4. RELATED SERVICES:');

			$relServices = $this->std->getRelatedServices();
			$sumRelSrv = count($relServices);
			# if exist related services add selected
			$this->addYN($layout->newLine(), $sumRelSrv > 0 ? 'Y' : 'N');
			$layout->addText('The student is in need of related services', '.padtop5');
			# if related services > 0 add table with services
			if ($sumRelSrv > 0) {
				$tbl = RCTable::factory('.table')
					->addRow('.row')
					->addCell('Related Services', '.hr')
					->addCell('Time', '.next-hr')
					->addCell('Location', '.next-hr');

				for ($i = 0; $i < $sumRelSrv; $i++) {
					$tbl->addRow('.row')
						->addCell('<i>' . $relServices[$i]['service'] . '</i>', '.cellBorder')
						->addCell('<i>' . $relServices[$i]['frequency'] . '</i>', '.cellBorder')
						->addCell('<i>' . $relServices[$i]['location'] . '</i>', '.cellBorder');
				}

				$layout->newLine('.martop10')
					->addObject($tbl);
			}

			$layout->newLine()
				->addText('5. TRANSPORTATION:');

			$specTransportation = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'tx_iep',
				'Transportation_chk',
				$this->std->get('stdiepyear')
			);

			$extendedSchool = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'tx_iep',
				'ESY Services_chk',
				$this->std->get('stdiepyear')
			);

			$this->addYN($layout->newLine('.martop10'), $specTransportation == 'Y' ? 'Y' : 'N');
			$layout->addText('Special Transportation was recommended. <i>(If yes, see attached justification)</i>', '.padtop5')
				->newLine('.martop10')
				->addText('6. EXTENDED SCHOOL YEAR SERVICES:');

			$this->addYN($layout->newLine('.martop10'), $extendedSchool == 'Y' ? 'Y' : 'N');
			$layout->addText('Extended School Year Services (ESY) were recommended.', '.padtop5');
			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Summary/Additional Comments/Recommendations
		 */
		public function renderSummaryAdditional() {
			$recommendations = $this->std->getSummaryRecommendations('A');
			$sumRecommend = count($recommendations);
			$layout = RCLayout::factory()
				->addText('<b>J. Summary/Additional Comments/Recommendations:</b>')
				->newLine('.martop10');
			for ($i = 0; $i < $sumRecommend; $i++) {
				if ($i > 0) $layout->newLine();

				$layout->addText('<i>' . $recommendations[$i]['siaitext'] . '</i>');
			}
			$this->rcDoc->addObject($layout);
		}

	}
