<?php

	/**
	 * IDEABlockMO.php
	 * Class for creation blocks in MO builder(State MO).
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 20-02-2014
	 */
	class IDEABlockMO extends IDEABlock {

		/**
		 * @var IDEAStudentMO
		 */
		protected $std;

		protected $printMarkingPeriods = false;

		public function __construct($id = null) {
			$this->idBlock = $id;

			parent::__construct();
		}

		public function setStd($tsRefID, $iepyear = null) {
			$this->std = new IDEAStudentMO($tsRefID, $iepyear);
		}

		/**
		 * Generate block Student Demographics for RC Document
		 */
		public function renderStdDemographic() {
			$docDetails = $this->std->getDocDetailsByID($this->queryData['ReportType']);
			$defRow = new RCStyle('[border: 1px solid black; border-top: none;]');
			$leftBor = new RCStyle('[border-left: 1px solid black;]');
			$greyRow = new RCStyle('center bold [background: #C0C0C0; border: 1px solid black; border-top: none;]');
			$middle = $this->std->getStdDemoSimple();
			$manager = $this->std->getCaseManager();
			$parents = new RCLayout();
			$tbl = RCLayout::factory();
			$parentData = $this->std->getGuardians();
			# set parents to Layout
			foreach ($parentData as $parent) {
				$parents->newLine()
					->addText('Parent Name: <i>' . $parent['gdfnm'] . ' ' . $parent['gdlnm'] . '</i>')
					->newLine()
					->addText('Parent Type: <i>' . $parent['gtdesc'] . '</i>')
					->newLine()
					->addText('Address: <i>' . $parent['gdadr1'] . ', ' . $parent['gdcity'] . ', ' . $parent['gdstate'] . ' ' . $parent['gdcitycode'] . '</i>')
					->newLine()
					->addText(
						'Home Phone: <i>' . $parent['gdhphn'] . '</i> Work Phone: <i>' . $parent['gdwphn'] . '</i> Cell Phone: <i>' . $parent['gdmphn'] . '</i> Email: <i>' . $parent['gdemail'] . '</i>'
						, '[border-bottom: 1px solid black;]'
					);
			}

			$layout = RCLayout::factory()
				->addObject(
					RCLayout::factory()
						->newLine('[background: #C0C0C0]')
						->addText(
							SystemCore::$VndName,
							'bold [font-size: 22px; color: black; padding: 5px 0px 5px 10px;]'
						)
						->newLine()
						->addText('Special Education Department', 'italic [font-size: 16px; color: black; padding: 0px 0px 0px 5px; width: 470px;]')
						->addText('Review ' . $docDetails['doctype'], '[font-size: 15px;]')
						->newLine()
						->addText('The Individualized Education Program', 'center bold [font-size: 17px;]')
					, '[background: #C0C0C0;]'
				)
				->newLine()
				->addObject(
					$tbl->newLine()
						->addText('Name <i>' . $this->std->get('stdfirstname') . '</i>')
						->addText('Middle <i>' . $middle['stdmnm'] . '</i>', $leftBor)
						->addText('Last Name <i>' . $this->std->get('stdlastname') . '</i>', $leftBor)
					, '[border: 1px solid black;]'
				)
				->newLine()
				->addText('Student Demographic Information (Optional)', $greyRow)
				->newLine($defRow)
				->addText(
					'Current Address: <i>' . $this->std->get('stdhadr1') . ', ' . $this->std->get('stdhcity') . ', ' . $this->std->get('stdhstate') . ' ' . $this->std->get('stdhzip') . '</i>',
					'[border-left: 1px solid black; width: 350px;]')
				->addText('Phone:', $leftBor)
				->newLine($defRow)
				->addText(
					'Birth date: <i>' . $this->std->get('stddob') . '</i> Age: <i>' . $this->std->get('stdage') . '</i>'
					, $leftBor
				)
				->addText('Student ID #: <i>' . $this->std->get('stdschid') . '</i>', $leftBor)
				->addText('MOSIS#: <i>' . $this->std->get('stdstateidnmbr') . '</i>', $leftBor)
				->newLine($defRow)
				->addText('Present Grade Level: <i>' . $this->std->get('grdlevel') . '</i>', '[width: 188px;]')
				->addText('Attending School: <i>' . $this->std->get('vouname') . '</i>', $leftBor)
				->newLine($defRow)
				->addText('Primary Language or Communication Mode(s): <i>' . $this->std->get('prim_lang') . '</i>')
				->newLine()
				->addText('Educational Decision Makers Are:', $greyRow)
				->newLine()
				->addObject($parents, '[border: 1px solid black; border-top: none; border-bottom: none;]')
				->newLine($defRow)
				->addText('IEP Case Manager: <i>' . $manager['cmname'] . '</i>')
				->addText('Case Manager phone number: <i>' . $manager['cmphone'] . '</i>', $leftBor)
				->newLine($defRow)
				->addText('Process Coordinator: <i>' . $manager['pcname'] . '</i>')
				->addText('Process Coordinator phone number: <i>' . $manager['pcphone'] . '</i>', $leftBor)
				->newLine($defRow)
				->addText('IEP Type: <i>Review ' . $docDetails['doctype'] . '</i>')
				->addText('Date of most recent evaluation/reevaluation: <i>' . $this->std->get('stdevaldt') . '</i>', $leftBor)
				->newLine($defRow)
				->addText('Date of Previous IEP: <i>' . $this->std->get('stdevaldt') . '</i>')
				->addText('Projected date for next triennial evaluation: <i>' . $this->std->get('stdtriennialdt') . '</i>', $leftBor)
				->newLine()
				->addText('IEP Content (Required):', $greyRow)
				->newLine($defRow)
				->addText('Date of IEP Meeting: <i>' . $this->std->get('stdiepmeetingdt') . '</i>')
				->addText('Initiation Date of IEP: <i>' . $this->std->get('stdiepmeetingdt') . '</i>', $leftBor)
				->newLine($defRow)
				->addText('Projected Date of Annual IEP Review: <i>' . $this->std->get('stdcmpltdt') . '</i>')
				->addText('Parent(s)/Legal Guardian(s) provided copy of this IEP: <i>' . $this->std->get('stdevaldt') . '</i>', $leftBor)
				->newLine($defRow)
				->addText('Copy of Bill of Rights given to parent on: <i>' . $this->std->get('stdevaldt') . '</i>')
				->addText('', $leftBor);

			$this->rcDoc->addObject($layout->newLine());
		}

		/**
		 * Generate block Participants
		 */
		public function renderParticipants() {
			$frstCol = new RCStyle('[width: 25%;]');
			$secCol = new RCStyle('center [border-left: 1px solid black; width: 25%;]');
			$header = RCLayout::factory()
				->addText(
					'Participants In IEP Meeting And Role(s) ' . PHP_EOL . ' The names and roles of individuals participating in developing the IEP meeting must be ' . PHP_EOL . ' documented.'
					, 'center bold [font-size: 12px;]'
				);

			$layout = RCLayout::factory()
				->newLine()
				->addObject($header, '[background: #C0C0C0; border: 1px solid black; border-bottom: none;]')
				->newLine()
				->addObject(
					RCLayout::factory()
						->addText(
							'<b>Name of Person and Role</b>' . PHP_EOL . 'Signatures are not required. If a signature is used it only' . PHP_EOL . 'indicates attendance, not agreement.',
							'center'
						)
						->addText('Method of Attendance/Participation', 'center bold [border-left: 1px solid black;]')
					, '[border: 1px solid black;]'
				);

			$participants = $this->std->getCommetteMembers(null);

			foreach ($participants as $row) {
				$signature = RCLayout::factory();

				if ($row['signature'] != null) {
					$file = FileUtils::createTmpFile(base64_decode($row['signature']), 'png');
					$img = RCImage::factory($file, '100px');

					$signature->addObject($img)->newLine();
				}

				$signature->addText('<i>' . $row['participantatttype'] . '</i>');
				$layout->newLine()
					->addObject(
						RCLayout::factory()
							->addText('<i>' . $row['participantname'] . '</i>', $frstCol)
							->addText('<i>' . $row['participantrole'] . '</i>', $secCol)
							->addObject($signature, '.cellBorder')
						, '[border: 1px solid black; border-top: none;]'
					);
			}

			$this->rcDoc->addObject($layout);
		}


		/**
		 * Generate block Progress Report
		 *
		 * @param string $esy
		 */
		public function renderProgresReport($esy = 'N') {
			$goals = array(
				$this->std->getProgressReportSimpleBGB($esy),
			);
			if ($esy == 'N') {
				$name = 'Student Progress Report';
			} else {
				$name = 'ESY Student Progress Report';
			}
			if (isset($goals[0][0])) {
				$this->rcDoc->newLine();
				$this->progressReportGoals($name, $goals[0]);
			}
		}

		/**
		 * Generate block PLAFP
		 */
		public function renderPLAFP() {
			$levels = $this->std->getPresentLevels();
			$results = $levels['prcntevalrslts'];
			$labelResults = array(
				'Vision:', 'Hearing:', 'Health:', 'Motor:', 'Speech:',
				'Language:', 'Cognitive:', 'Adaptive Behavior:', 'Social/Emotional/Behavioral:',
				'Academics:', 'Transition:', 'Assistive Technology:', 'Parent Comments/Observations:'
			);

			foreach ($labelResults as $label) {
				$results = str_replace($label, "<b>$label</b>", $results);
			}

			$layout = RCLayout::factory()
				->addText(
					'Present Levels of Academic Achievement and Functional Performance',
					'bold center [background: #C0C0C0; border: 1px solid black;]'
				)
				->newLine('.martp10')
				->addText('<b>Provide Brief Lead-in Statement, which includes:</b> Student\'s Name, Age, and Grade')
				->newLine()
				->addText('<i>' . $levels['pleadstat'] . '</i>')
				->newLine('.martop10')
				->addText('<i>On ' . $levels['stdevaldt'] . ' the IEP Team determined that ' . $this->std->get('stdname') . ' met eligibility criteria for Auditory Impairment, Specific Learning Disability</i>')
				->newLine('.martop10')
				->addText('<b>How the child\'s disability affects his/her involvement and progress in the general education curriculum; or for preschool children, participation in age-appropriate activities.</b>')
				->newLine()
				->addText('For students with transition plans, consider how the child\'s disability will affect the child\'s ability to reach his/her post-secondary goals (what the child will do after high school).')
				->newLine()
				->addText('<i>' . $levels['pmtsgened'] . '</i>')
				->newLine('.martop10')
				->addText('<b>The strengths of the child:</b>')
				->newLine()
				->addText('For students with transition plans, consider how the strengths of the child relate to the child\'s post-secondary goals.')
				->newLine()
				->addText('<i>' . $levels['pbaseline'] . '</i>')
				->newLine('.martop10')
				->addText('<b>Concerns of the parent/guardian for enhancing the education of the child:</b>')
				->newLine()
				->addText('For students with transition plans, consider the parent/guardian\'s expectations for the child after the child leaves high school.')
				->newLine()
				->addText('<i>' . $levels['pgdconcrn'] . '</i>')
				->newLine('.martop10')
				->addText('<b>Changes in current functioning of the child since the initial or prior IEP</b>')
				->newLine()
				->addText('For students with transition plans, consider how changes in the child\'s current functioning will impact the child\'s ability to reach his/her post-secondary goal.')
				->newLine()
				->addText('<i>' . $levels['pstrstd'] . '</i>')
				->newLine('.martop10')
				->addText('<b>A summary of the most recent evaluation/re-evaluation results</b>')
				->newLine()
				->addText('The following information is based upon the Evaluation Report dated: <i>' . $levels['stdevaldt'] . '</i>')
				->newLine()
				->addText('<i>' . $results . '</i>')
				->newLine('.martop10')
				->addText('<b>A summary of the results of the child\'s performance on:</b>')
				->newLine()
				->addText('- Formal or informal age appropriate transition assessments:')
				->newLine()
				->addText('<i>' . $levels['mo_formal'] . '</i>')
				->newLine('.martop10')
				->addText('<b>For students participating in alternative assessments, a description of benchmarks or short-term objectives</b>')
				->newLine()
				->addText('- N/A Objectives/benchmarks are on goal page(s):')
				->newLine()
				->addText('<i>' . $levels['mo_bench_pages'] . '</i>')
				->newLine()
				->addText('- Objectives/benchmarks described below:')
				->newLine()
				->addText('<i>' . $levels['mo_bench_desc'] . '</i>');

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Special Considerations: Federal and State Requirements
		 */
		public function renderConsiderations() {
			$questions = $this->std->getSpecConsiderations();
			$layout = RCLayout::factory()
				->newLine()
				->addText(
					'Special Considerations: Federal and State Requirements',
					'bold center [background: #C0C0C0; border: 1px solid black;]'
				)
				->newLine()
				->addText('<i>Note: For the first six items below, if the IEP team determines that the child needs a particular device or service (including an intervention, accommodation, or other program modification) information documenting the team\'s decision regarding the device or service must be included in the appropriate section of the IEP. These must be considered annually.</i>');

			foreach ($questions as $question) {
				$layout->newLine('.martop10')
					->addText('<b>' . $question['scmquestion'] . '</b>')
					->newLine()
					->addObject($this->addCheck($question['scanswer'] == 'No' ? '' : 'Y'), '.width20')
					->addText($question['scanswer'], '.padtop5');
			}

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Goals and Objectives
		 */
		public function renderGoals() {
			$layout = RCLayout::factory();
			$goals = $this->std->getBgbGoals();
			$reports = $this->std->getProgressReportIEP();
			$measures = $this->std->getBgbGoalsMeasures();

			foreach ($goals as $goal) {
				$goalRefID = $goal['grefid'];
				$reportsGoal = $reports[$goalRefID];
				$measureGoal = $measures[$goalRefID];
				$allMeasurs = count($measureGoal);
				$benckmarks = '<b>Measurable Benchmarks/Objectives:</b>';
				$annualGoal = '';
				$selectDomain = explode(',', $goal['mo_domains']);
				$domains = IDEADef::getValidValues('MO_BGB_Domains');
				$domainsBoxes = RCLayout::factory()
					->addText('For students with Post-secondary Transition Plans, please indicate which goal domain(s) this annual goal will support:')
					->newLine();
				# create layout for domains with checkboxes
				foreach ($domains as $domain) {
					/** @var IDEADefValidValue $domain */
					$domainsBoxes->addObject(
						RCLayout::factory()
							->addObject(
								$this->addCheck(
									in_array($domain->get(IDEADefValidValue::F_VALUE_ID), $selectDomain) ? 'Y' : 'N'
								), '.width20'
							)
							->addText($domain->get(IDEADefValidValue::F_VALUE), '.padtop5')
					);
				}
				# row with benchmarks/objectives
				foreach ($goal['objectives'] as $objective) {
					$benckmarks .= PHP_EOL . '<i>' . $objective['bsentance'] . '</i>';
					$annualGoal = $goal['bl_num'] . '.' . $goal['g_num'];
				}

				$tbl = RCTable::factory('.table')
					->addCell(
						'3. IEP Goal(s) with Objectives/Benchmarks and Reporting Form' . PHP_EOL . 'Annual Measurable Goals',
						'.hr',
						4
					)
					->addRow('.row')
					->addCell(
						'<b>Baseline: ' . $goal['baseline'] . PHP_EOL . 'Annual Goal #: ' . $annualGoal . '</b>' . PHP_EOL . '<i>' . $goal['gsentance'] . '</i>' . $benckmarks,
						'.cellBorder',
						4
					)
					->addRow('.row')
					->addCell(
						$domainsBoxes->newLine()
							->addText('Progress toward the goal will be measured by: <b>(check all that apply)</b>'),
						null,
						4
					);
				# Progress toward the goal will be measured
				for ($i = 0; $i < $allMeasurs; $i++) {
					if ($i % 4 == 0 || $i == 0) $tbl->addRow('.row');

					$tbl->addCell(
						RCLayout::factory()
							->addObject($this->addCheck($measureGoal[$i]['value']), '.width20')
							->addText($measureGoal[$i]['name'], '.padtop5')
						,
						'.cellBorder'
					);
				}
				# table with periods
				$allPeriods = count($reportsGoal);
				$progReportTbl = RCTable::factory('.table')
					->addCell('Periodic Progress Report', '.hr')
					->addCell('Progress Toward the Goal', '.next-hr', $allPeriods - 1);

				foreach ($reportsGoal as $row) {
					$allCell = count($row);
					$progReportTbl->addRow('.row');
					for ($i = 0; $i < $allCell; $i++) {
						$check = '';
						# if first item add lable else add checkbox
						if ($i === 0) $check = $row[$i];
						if ($i > 0 && $row[$i] == 'Y') $check = $this->addCheck('Y');

						$progReportTbl->addCell(
							$check,
							# styles for first and default cells
							$i == 0 ? 'left [font-weight: normal;]' : 'center [border-left: 1px solid black; font-weight: normal;]');
					}
				}

				$progReportTbl->addRow('.row')
					->addCell('<b>Comments:</b> <i>' . $goal['mo_comments'] . '</i>', 'left', $allPeriods);

				$layout->newLine()
					->addObject($tbl)
					->newLine()
					->addObject($progReportTbl);
			}

			$this->rcDoc->addObject($layout);
		}

	}
