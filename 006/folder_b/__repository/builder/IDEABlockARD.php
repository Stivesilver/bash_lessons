<?php

/**
 * IDEABlockARD.php
 *
 * Class for creation blocks in ARD/IEP builder(State TX).
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 16-01-2014
 */
class IDEABlockARD extends IDEABlockIEP {

	/**
	 * @var IDEAStudentTXARD
	 */
	protected $std;

	/**
	 * Add information about parents to block Student Demographics or no
	 *
	 * @var bool
	 */
	protected $printParent = true;

	/**
	 * Type use when get data for block Signatures
	 *
	 * @var string
	 */
	protected $typeCommitteMembers = 'A';

	/**
	 * Add information about Purpose of Meeting to block Student Demographics or no
	 *
	 * @var bool
	 */
	protected $printPurposeOfMeeting = true;

	/**
	 * Generate block Student Demographics for RC Document
	 */
	public function renderStdDemographic() {
		$layout  = $this->nameOffice('ADMISSION, REVIEW AND DISMISSAL (ARD) COMMITTEE MEETING Individual Education Plan (IEP)');
		$layout->newLine()
			->addText('Address:', '[width: 45px;]')
			->addText('<i>' . $this->std->get('stdhadr1') . $this->std->get('stdhcity') . ', ' . $this->std->get('stdhstate') . ' ' . $this->std->get('stdhzip') . '</i>',
				'[width: 250px; border-bottom: 1px solid; black;]'
			)
			->addText('Phone:', '[width: 60px; margin-left: 25px;]')
			->addText('<i>' . $this->std->get('stdhphn') . '</i>', '[width: 210px; border-bottom: 1px solid black;]');
		$parentStyle  = new RCStyle('[width: 55px;]');
		$phoneStyle   = new RCStyle('[width: 60px;]');
		$cellPhone    = new RCStyle('[width: 135px; border-bottom: 1px solid black; margin-right: 20px;]');
		$parentAdrs   = new RCStyle('[width: 325px; border-bottom: 1px solid black;]');
		$parentBorder = new RCStyle(
			'[width: 140px; border-bottom: 1px solid black; margin-right: 10px;]'
		);
		# if we have add info about parents
		if ($this->printParent === true) {
			$parents = $this->std->getGuardians();
			# rows with info about parents
			foreach ($parents as $parent) {
				$name = '<i>' . $parent['gdfnm'] . ' ' . $parent['gdlnm'] . '</i>';
				$layout->newLine('[margin-top: 10px 0px 5px 0px;]')
					->addText('Parent Name:'                    , $parentStyle)
					->addText($name                             , $parentBorder)
					->addText('Parent Type:'                    , $parentStyle)
					->addText('<i>' . $parent['gtdesc'] . '</i>', $parentBorder)
					->newLine()
					->addText('Address:'                        , $parentStyle)
					->addText(
						'<i>' . $parent['gdadr1'] . ', ' . $parent['gdcity'] . ', ' . $parent['gdstate'] . ', ' . $parent['gdcitycode'] . '</i>',
						$parentAdrs
					)
					->newLine()
					->addText('Home Phone:'                             , $phoneStyle)
					->addText('<i>' . (string)$parent['gdhphn'] . '</i>', $cellPhone)
					->addText('Work Phone:'                             , $phoneStyle)
					->addText('<i>' . (string)$parent['gdwphn'] . '</i>', $cellPhone)
					->addText('Cell Phone:'                             , $phoneStyle)
					->addText('<i>' . (string)$parent['gdmphn'] . '</i>', $cellPhone);
			}
		}

		$layout->newLine('.martop10')
			->addText('The student\'s dominant language is:', '[with: 70px;]')
			->addText(
				'<i>' . $this->std->getLanguages('dominant_language') . '</i>',
				'[width: 430px; border-bottom: 1px solid black;]'
			);

		$this->addYN($layout->newLine('.martop10'), $this->std->getLanguages('interpreter_used'));
		$layout->addText('An interpreter is being used to assist in conducting the meeting. <i>If Yes, specify the language or other mode of communication:' . $this->std->getLanguages('interpreter_mode') . '</i>', '.padtop5');
		$this->addYN($layout->newLine(), $this->std->getLanguages('writing_translate'));
		$layout->addText('A written translation of the IEP in the parent\'s/adult student\'s dominant language is being given to the parent/adult student.', '.padtop5');
		$this->addYN($layout->newLine(), $this->std->getLanguages('audio_tape'));
		$layout->addText('An audio tape translation of the IEP in the parent\'s/adult student\'s dominant language is being given to the parent/adult student.', '.padtop5');
		# if we have add info about Purpose Of Meeting
		if ($this->printPurposeOfMeeting === true) {
			$layout->newLine()
				->addText('I. Purpose of Meeting', $this->titleStyle('width: 100px;'))
				->addText('<i>(attach notice and response):</i>', '[padding-top: 15px;]');

			$purposes = $this->std->getMeetPurposes();
			$count    = count($purposes);
			$idPur    = explode(',', $this->std->getMeetPurposesSelected('type_report'));
			for ($i = 0; $i < $count; $i++) {
				if ($i == 0) {
					$layout->newLine('[margin: 10px 0px 10px 0px;]');
				}
				if (in_array($purposes[$i]['refid'], $idPur)) {
					$check = 'Y';
				} else {
					$check = 'N';
				}
				$layout->addObject($this->addCheck($check), '.width20')
					->addText($purposes[$i]['adesc'],       '.padtop5');
			}

			$typesIEP = $this->std->meetIEPTypes();
			$typesID  = explode(',', $this->std->getMeetPurposesSelected('type_iep'));
			$count    = count($typesIEP);
			for ($i = 0; $i < $count; $i++) {
				# add new row after 3 checkboxes
				if ($i == 0 || $i % 3 == 0) $layout->newLine();

				$check = $this->checkArraySwitcher($typesID, $typesIEP[$i]['siepmtrefid']);
				$layout->addObject($this->addCheck($check), '.width20')
					->addText($typesIEP[$i]['siepmtdesc'] , '.padtop5');
			}

			$type = $this->std->getMeetPurposesSelected('type_iep_other');
			if (!$type) {
				$type  = 'Other';
				$check = 'N';
			} else {
				$check = 'Y';
			}

			$layout->newLine('.martop10')
				->addObject($this->addCheck($check), '.width20')
				->addText($type, '.padtop5');
		}

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Review of Assessment Data
	 */
	public function renderReviewAssessment() {
		RCStyle::defineStyleClass('width170'     , '[width: 170px; padding-top: 5px;]');
		RCStyle::defineStyleClass('width50border', '[width: 50px; border-bottom: 1px solid black; padding-top: 5px;]');

		$table     = RCTable::factory('.table');
		$otherInfo = $this->std->otherInfo();
		$otherKeys = $this->getOtherKeys();
		$countKeys = count($otherKeys);
		#add rows into table
		for ($i = 0; $i < $countKeys; $i++) {
			# styles for first, last and default rows
			if ($i == $countKeys - 1) {
				$style = new RCStyle('[padding: 0px 0px 10px 5px; width: 50%;]');
			} elseif ($i == 0) {
				$style = new RCStyle('[padding: 5px 0px 0px 5px; width: 50%;]');
			} else {
				$style = new RCStyle('[padding: 0px 0px 0px 5px; width: 50%;]');
			}

			# check other items
			$otherItem = '';
			if (isset($otherInfo['items'][$i])) {
				$otherItem = $otherInfo['items'][$i];
			}
			# if last rows in right column is empty add option
			if (isset($otherInfo['items'][$i - 1]) && $otherItem == '') {
				$otherItem = array(
					'selected'   => 'Y'                 ,
					'validvalue' => $otherInfo['option'],
					'itemval'    => ''                  ,
				);
			}

			# add value to left and right cell
			$table->addRow()
				->addCell($this->addOtherLeft($otherKeys[$i]), $style)
				->addCell($this->addOtherRight($otherItem)   , $style);
		}

		$layout = RCLayout::factory()
			->addText(
				'II. Review of Assessment Data and Other Information',
				$this->titleStyle('width: 230px;')
			)
			->addText('<i>(check if applicable):</i>', '[margin-top: 10px;]')
			->newLine()
			->addObject($table)
			->newLine()
			->addText('Issues expressed by the parents concerning the education of their child: <i>' . $this->std->getConcerns('concerns') . '</i>');
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Boild left part row about other information student
	 * Use in block Review of Assessment Data
	 *
	 * @param array $item
	 * @return RCLayout
	 */
	private function addOtherLeft($item) {
		if ($this->std->reviewAssData($item['key']) == '') {
			$check = 'N';
		} else {
			$check = 'Y';
		}
		$row = RCLayout::factory()
			->addObject($this->addCheck($check), '.width20')
			->addText($item['label'], '.width170')
			->addText(
				'<i>' . (string)$this->std->reviewAssData($item['key']) . '</i>',
				'.width50border'
			);
		return $row;
	}

	/**
	 * Boild right part row about other information student
	 * Use in block Review of Assessment Data
	 *
	 * @param array|string $other
	 * @return RCLayout
	 */
	private function addOtherRight($other) {
		if ($other == '') {
			return $other;
		} else {
			return RCLayout::factory()
				->addObject($this->addCheck($other['selected']), '.width20')
				->addText($other['validvalue'] . '<i> ' . $other['itemval'] . '</i>',   '.padtop5');
		}
	}

	/**
	 * Get key for files and lables for rows.
	 * Use in block Review of Assessment Data
	 *
	 * @return array
	 */
	private function getOtherKeys() {
		return array(
			array(
				'key'   => 'stdiepmeetingdt'            ,
				'label' => 'ARD/IEP Annual Meeting Date',
			),
			array(
				'key'   => 'stdenrolldt'                   ,
				'label' => 'ARD/IEP Annual Initiation Date',
			),
			array(
				'key'   => 'stdcmpltdt'                             ,
				'label' => 'ARD/IEP Projected Date of Annual Review',
			),
			array(
				'key'   => 'stdevaldt'                     ,
				'label' => 'Full and Individual Evaluation',
			),
			array(
				'key'   => 'stdtriennialdt',
				'label' => 'Reevaluation'  ,
			),
			array(
				'key'   => 'longard'              ,
				'label' => 'Long Ard Meeting Date',
			),
			array(
				'key'   => 'briefard'              ,
				'label' => 'Brief ARD Meeting Date',
			),
			array(
				'key'   => 'amendment'                 ,
				'label' => 'IEP Amendment Meeting Date',
			),
			array(
				'key'   => 'inituni'                                        ,
				'label' => 'Initiation Date (for Long, Brief, or Amendment)',
			),
			array(
				'key'   => 'assistive'                      ,
				'label' => 'Assistive Technology Assessment',
			),
			array(
				'key'   => 'fba'                           ,
				'label' => 'Functional Behavior Assessment',
			),
			array(
				'key'   => 'fve'                             ,
				'label' => 'Functional Vocational Evaluation',
			),
			array(
				'key'   => 'related'                    ,
				'label' => 'Related Services Assessment',
			),
			array(
				'key'   => 'speach'                        ,
				'label' => 'Speech and Language Assessment',
			),
			array(
				'key'   => 'transition'         ,
				'label' => 'Transition Services',
			),
			array(
				'key'   => 'other',
				'label' => 'Other',
			)
		);
	}

	/**
	 * Generate block III. Determination of Additional Assessment Needed
	 *
	 * @return RCLayout
	 */
	public function renderDeterminationAdditional() {
		$bottom = new RCStyle('[margin-bottom: 10px;]');
		$check  = IDEAStudentRegistry::readStdKey(
			$this->std->get('tsrefid')    ,
			'tx_iep'                      ,
			'assessment_additional_needed',
			$this->std->get('stdiepyear')
		);
		$text = IDEAStudentRegistry::readStdKey(
			$this->std->get('tsrefid')        ,
			'tx_iep'                          ,
			'assessment_additional_evaluation',
			$this->std->get('stdiepyear')
		);

		if ($check == '') {
			$firstRow = 'N';
			$secRow   = 'N';
		} elseif ($check == 'N') {
			$firstRow = 'Y';
			$secRow   = 'N';
		} else {
			$firstRow = 'N';
			$secRow   = 'Y';
		}

		$layout = RCLayout::factory()
			->addText(
				'III. Determination of Additional Assessment Needed:',
				$this->titleStyle('width: 230px;')
			)
			->newLine()
			->addText(
				'The ARD/IEP committee addressed the need for additional assessment and determined <i>(check applicable):</i>',
				$bottom
			)
			->newLine()
			->addObject($this->addCheck($firstRow), '.width20')
			->addText(
				'No additional data is needed because the existing data is appropriate to determine whether the student has or continues to have a disability under IDEA and an educational need for special education and related services. The existing data is sufficient to develop an appropriate IEP for the student. <i>(If this refers to the discussion of reevaluation, see attached Determination of Needed Evaluation.)</i>',
				$bottom
			)
			->newLine('[margin-top: 10px;]')
			->addObject($this->addCheck($secRow), '.width20')
			->addText('Additional data is needed to determine whether any additions or modifications to the student\'s special education program are needed to enable the student to achieve the measurable annual goals and objectives and to participate. <i>If checked, identify the type of evaluation and the date it will be completed: ' . $text . '</i>');
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block IV. Determination of Eligibility
	 */
	public function renderDeterminationEligibility() {
		$disabilityList = $this->std->disabilityList();
		$check          = $this->checkDisability();
		$layout         = RCLayout::factory()
			->addText(
				'IV. Determination of Eligibility',
				$this->titleStyle('width: 230px;')
			)
			->addText('<i>(check if applicable):</i>', '[padding-top: 13px;]')
			->newLine()
			->addText(
				'Based on the assessment data reviewed, the ARD/IEP committee has determined that the student:',
				'.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($check), '.width20')
			->addText('does not have a disability that adversely affects the student\'s educational performance <i>(proceed to the signature page).</i>', '.padtop5')
			->newLine()
			->addObject($this->addCheck(!$check), '.width20')
			->addText(
				'has a disability that adversely affects the student\'s educational performance and meets the eligibility criteria for special education and related services:',
				'.padtop5'
			);
			$count = count($disabilityList);
			$boxes = new RCLayout();
			for ($i = 0; $i < $count; $i++) {
				# if first || 3d checkbox add new line
				if ($i == 0 || $i % 3 == 0 ) {
					$boxes->newLine();
				}
				$boxes->addObject($this->addCheck($disabilityList[$i]['dcrefid']), '.width20')
					->addText($disabilityList[$i]['desc'], '.padtop5');
			}

			# default options for checkbox
			$optYes = 'N';
			$optNo  = 'N';
			$key    = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'tx_iep',
				'disability_letter'
			);
			if ($key == 'Y') {
				$optYes = 'Y';
			}
			if ($key == 'N') {
				$optNo = 'N';
			}
			$layout->newLine()
				->addObject($boxes, '[margin: 10px 0px 10px 0px;]')
				->newLine()
				->addObject(
					RCLayout::factory()
						->addObject($this->addCheck($optYes), '.width20')
						->addText('Yes'                     , '[padding-top: 5px; width: 20px;]')
						->addObject($this->addCheck($optNo) , '.width20')
						->addText('N/A'                     , '[padding-top: 5px; width: 20px;]')
						->addText('Parents of student\'s who meet eligibility criteria for visual or auditory impairments or deaf-blindness have been given written  information, within the past year, about programs offered by the Texas School for the Blind and Visually Impaired or Texas School for the Deaf, including eligibility and admission requirements and the rights of student\'s related to admission.')
				)
				->newLine()
				->addText(
					'<b>Note: </b>A child shall not be determined to be a child with a disability if the determinant factor for such determination is lack of appropriate instruction in reading <i>(including in the essential components of reading instruction),</i> lack of instruction in math or limited English proficiency.',
					'.martop10'
				);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Check field dcrefid and retur nvalue for checkbox
	 *
	 * @return string
	 */
	private function checkDisability() {
		$disabilityList = $this->std->disabilityList();
		$check          = 'Y';
		$count          = count($disabilityList);
		for ($i = 0; $i < $count; $i++) {
			if ($disabilityList[$i]['dcrefid'] > 0) {
				$this->std->changeDisability($i, 'N');
				$check = 'N';
			} else {
				$this->std->changeDisability($i, 'Y');
			}
		}
		return $check;
	}

	/**
	 * V. Development of the Individual Educational Plan (IEP):
	 */
	public function renderPresentCompetencies() {
		$layout = RCLayout::factory()
			->addText(
				'V. Development of the Individual Educational Plan (IEP):',
				$this->titleStyle('width: 230px;')
			)
			->newLine()
			->addText('<b>A. Present Competencies</b> <i>(Including the strengths of the student and present levels of academic, developmental and functional performance):</i>');

		$data = $this->std->presentCompetencies();

		$this->addYN($layout->newLine('.martop10'), $data['achievement_sw']);
		$layout->addText(
				'The ARD/IEP committee reviewed achievement on the previous short-term objectives on the IEP (applicable to all but initial ARD/IEP meetings).',
				'.padtop5'
			)
			->newLine()
			->addText('1. PHYSICAL (as it affects participation):');

		$this->addYN($layout->newLine('.martop10'), $data['capable_sw']);
		$layout->addText(
				'The student is physically capable of receiving instruction in the classroom without special modifications or arrangements. If No, describe needs and address in the IEP.',
				'.padtop5'
			)
			->newLine()
			->addText('Needs: <i>' . $data['capable_txt'] . '</i>', '[margin-left: 50px;]');

		$this->addYN($layout->newLine('.martop10'), $data['lenses_sw']);
		$layout->addText('The student wears corrective lenses.', '.padtop5');
		$this->addYN($layout->newLine('.martop10'), $data['hearing_sw']);
		$layout->addText('The student wears hearing aid(s)',     '.padtop5');
		$this->addYN($layout->newLine('.martop10'), $data['policy_sw']);
		$layout->addText(
			'The student is able to follow the state attendance policy. If No, attach documentation and notify campus  attendance staff.',
			'.padtop5'
		);
		$this->addYN($layout->newLine('.martop10'), $data['teks_sw']);
		# add checkbox check field on value 'A'
		$layout->addObject($this->addCheck($this->checkNA($data['teks_sw'])), '.width20')
			->addText('N/A', '[width: 20px; padding-top: 5px;]')
			->addText('The student receives grade level TEKS Instruction in Physical Education. If No, describe the student\'s needs and address in the IEP. Needs: <i>' . $data['teks_txt'] . '</i>')
			->newLine('.martop10')
			->addText('2. PREVOCATIONAL/VOCATIONAL:');

		$this->addYN($layout->newLine('.martop10'), $data['vocational_affect_sw']);

		$layout->addObject($this->addCheck($this->checkNA($data['vocational_affect_sw'])), '.width20')
			->addText('N/A', '[width: 20px; padding-top: 5px;]')
			->addText('The educational needs of the student do not affect the student\'s ability to participate in the general vocational program.');

		$this->addYN($layout->newLine('.martop10'), $data['vocational_met_sw']);
		$layout->addObject($this->addCheck($this->checkNA($data['vocational_met_sw'])),  '.width20')
			->addText('N/A', '[width: 20px; padding-top: 5px;]')
			->addText('The student\'s educational needs cannot be met with the general vocational program.')
			->newLine('.martop10')
			->addText('3. COMMUNICATION:')
			->newLine('.martop10')
			->addText('<i>a. Speech/Language:</i>');

		$this->addYN($layout->newLine('.martop10'), $data['cognitive_sw']);
		$layout->addText(
				'Performance is commensurate with similar age peers and/or level of cognitive ability. <i>If Yes, go to Language needs for second language learners. If No, address in the IEP.</i>',
				'.padtop5'
			)
			->newLine('.martop10')
			->addText('<i>b. Language Needs for Second Language Learners:</i>', '.padtop5');

		$this->addYN($layout->newLine('.martop10'), $data['second_lang_sw']);
		$layout->addText(
				'Student is a second language learner. <i>If No, go to Pre-academics/Academics.</i>',
				'.padtop5'
			)
			->newLine('.martop10')
			->addObject($this->addCheck($data['english_sw']), '.width20')
			->addText('All instruction is in English <b>OR</b>', '[width: 115px; padding-top: 5px;]')
			->addObject($this->addCheck($data['native_sw']), '.width20')
			->addText('Instruction is in the student\'s native language', '.padtop5')
			->newLine()
			->addText('<i>' . $data['native_lang_txt'] . '</i>', '[margin-left: 20px;]')
			->newLine('.martop10')
			->addText('<b>AND/OR</b>', 'center');

		$this->addCheck($layout->newLine('.martop10'), $data['alt_bilingual_sw']);
		$layout->addObject($this->addCheck($data['alt_lang_sw']), '.width20')
			->addText('An alternate language program is needed.', '.padtop5')
			->newLine()
			->addText('<i>Specify alternate program:</i>', '[width: 110px; padding-top: 5px;]')
			->addObject($this->addCheck($data['alt_esl_sw']),       '.width20')
			->addText('ESL',       '.padtop5')
			->addObject($this->addCheck($data['alt_bilingual_sw']), '.width20')
			->addText('Bilingual', '.padtop5')
			->addObject($this->addCheck($data['alt_other_sw']),     '.width20')
			->addText('Other: ',    '[width: 30px; padding-top: 5px;]')
			->addText('<i>' . (string)$data['alt_other_txt'] . '</i>', '[border-bottom: 1px solid black; width: 120px;]');

		$tbl = RCTable::factory('.table')
			->addLeftHeading('4. PREACADEMICS/ACADEMICS:')
			->addRow('.row')
			->addCell('Instructional Area'       , '.hr')
			->addCell('Instructional/Skill Level', '.next-hr')
			->addCell('Strengths'                , '.next-hr')
			->addCell('Needs'                    , '.next-hr');

		$tblData = $this->std->competentAcademic();
		$count   = count($tblData);
		for ($i = 0; $i < $count; $i++) {
			$tbl->addRow('.row')
				->addCell('<i>' . $tblData[$i]['ac_desc'] . '</i>'            , '.cellBorder')
				->addCell('<i>' . (string)$tblData[$i]['skill_level'] . '</i>', '.cellBorder')
				->addCell('<i>' . (string)$tblData[$i]['strengths']  . '</i>' , '.cellBorder')
				->addCell('<i>' . (string)$tblData[$i]['needs']   . '</i>'    , '.cellBorder');
		}

		$layout->newLine('.martop10')
			->addObject($tbl)
			->newLine()
			->addText('4. BEHAVIOR');

		$this->addYN($layout->newLine('.martop10'), $data['impede_sw']);
		$layout->addText(
				'Does the student\'s behavior impede self/others from learning? <i>If yes, check one:</i>',
				'.padtop5'
			)
			->newLine()
			->addObject(
				RCLayout::factory()
					->addObject($this->addCheck($data['impede_capable_sw']), '.width20')
					->addText(
						'The student is capable of following the Student Code of Conduct with BIP supplementation.',
						'.padtop5'
					)
					->newLine()
					->addObject($this->addCheck($data['impede_diff_sw']), '.width20')
					->addText(
						'Due to the students level of functioning, he/she may have difficulty understanding the Student Code of Conduct; however, the student is responding to the classroom behavior management system and is not exhibiting behaviors requiring an individual Behavior Intervention Plan at this time.',
						'.padtop5'
					)
					->newLine()
					->addObject($this->addCheck($data['impede_notcapable_sw']), '.width20')
					->addText(
						'The student is not capable of following the Student Code of Conduct (See BIP).',
						'.padtop5'
					)
				, '[margin-left: 20px;]'
			);

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Check value for NA switcher
	 *
	 * @param string $val
	 * @return string
	 */
	private function checkNA($val) {
		if ($val == 'A') {
			return 'Y';
		} else {
			return 'N';
		}
	}

	/**
	 * Generate block ESY Goals and Objectives
	 */
	public function renderESY() {
		$basedGoals = $this->std->standartBasedGoals('Y');
		if (isset($basedGoals[0])) {
			$this->commonGoals($basedGoals, 'B. ESY Goals and Objectives', true, 'Yes');
		}
		$bgbGoals = $this->std->bgbGoals('Y');
		if (isset($bgbGoals[0])) {
			$this->rcDoc->startNewPage();
			$this->commonGoals($bgbGoals, 'B. ESY Goals and Objectives', true, 'Yes');
		}
	}

	/**
	 * Generate block Least Restrictive Environment
	 */
	public function renderLeastRestrictive() {
		$layout = RCLayout::factory()
			->newLine()
			->addText('<b>F. Consideration of Least Restrictive Environment:</b>')
			->newLine('.martop10')
			->addText('1. PREVIOUS EFFORTS/OPTIONS CONSIDERED:')
			->newLine()
			->addText('Describe previous efforts to educate the student in a general education classroom with supplementary aids and services:');

		$tbl = $this->effortTable('EFFORTS (if applicable, rate "S" if successful or "U" if unsuccessful)', 'E');

		$tbl->addRow()
			->addCell(
				'If efforts are not successful, give reasons:',
				'center [border-top: 1ox solid black;]',
				$tbl->getColumnsCount()
			);

		$layout->newLine()
			->addObject($tbl)
			->newLine()
			->addText('<i>*Denotes programs/services that require students meet specific criteria.</i>')
			->newLine('.martop10')
			->addText('The ARD/IEP committee discussed issues involved in educating the student in a general education environment with supplementary aids and services and the reasons those options were rejected:');

		$tbl = null;
		$tbl = $this->effortTable(
			'OPTIONS (indicate as applicable "R" if rejected, "I" if selected for implementation or "C" if continued**)',
			'O'
		);

		$tbl->addRow()
			->addCell(
				'If options were discussed and rejected, give reasons:',
				'center [border-top: 1ox solid black;]',
				$tbl->getColumnsCount()
			);

		$layout->newLine()
			->addObject($tbl)
			->newLine()
			->addText('<i>*Denotes programs/services that require students meet specific criteria.</i>')
			->newLine()
			->addText('<i>**Results of implementation will be discussed at the next ARD/IEP committee meeting.</i>')
			->newLine('.martop10')
			->addText('2. DESCRIBE HOW THE DISABILITY AFFECTS THE STUDENT\'S INVOLVEMENT AND PROGRESS IN THE GENERAL CURRICULUM:')
			->newLine('.martop10')
			->addText('Based on the review of the student\'s assessment data, levels of educational performance and goals and objectives accepted by the ARD/IEP committee, the student:');

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate table wit efforts by mode & title
	 *
	 * @param string $header first row table
	 * @param string $mode use for type effort
	 * @return RCTable
	 */
	final private function effortTable($header, $mode) {
		$tbl = RCTable::factory('[border: 1px solid black;]')
			->setCol('10px')
			->setCol()
			->setCol('10px')
			->setCol()
			->addRow('.row')
			->addCell(
				'<b>' . $header .'</b>',
				'center',
				4
			);

		$efforts    = $this->std->getEfforts($mode);
		$sumEfforts = count($efforts);
		$printMod   = true;
		$markStyle  = new RCStyle('[border-bottom: 1px solid black; margin-left: 5px;]');

		for ($i = 0; $i < $sumEfforts; $i += 2) {
			$nextEffort = '';
			# add name Group
			if ($printMod === true && $efforts[$i]['case'] != '') {
				$printMod = false;
				$tbl->addRow()
					->addCell(''                        ,  $markStyle)
					->addCell(' ' . $efforts[$i]['case'], 'left')
					->addCell(''                        , $markStyle)
					->addCell($nextEffort);
			}

			$tbl->addRow()
				->addCell($efforts[$i]['mark']    , $markStyle)
				->addCell($efforts[$i]['effort']  , 'left')
				->addCell($efforts[$i + 1]['mark'], $markStyle)
				->addCell($efforts[$i + 1]['effort']);
		}

		return $tbl;
	}

	/**
	 * Generate block Placement Determination
	 */
	public function renderPlacementDetermination() {
		$layout = RCLayout::factory()
			->newLine()
			->addText('<b>H. Placement Determination:</b>')
			->newLine('.martop10')
			->addText('1. INSTRUCTIONAL ARRANGEMENT:')
			->newLine()
			->addText('The ARD/IEP committee determined that services will be provided at:');

		$arrangements = $this->std->getInstructArrangement();
		$sumArrangem  = count($arrangements);

		if ($sumArrangem > 0) {
			$width120 = new RCStyle('center [width: 120px; margin-right: 10px;]');
			$stCRT    = new RCStyle('center [border-bottom: 1px solid black; width: 120px; font-style: italic;]');
			$width95  = new RCStyle('center [width: 95px;]');
			$stLoc    = new RCStyle('center [width: 120px;]');
			$col      = new RCStyle('center [border-bottom: 1px solid black; width: 120px; margin-right: 10px; font-style: italic;]');
			$col3     = new RCStyle('center [border-bottom: 1px solid black; width: 95px; margin-right: 10px; font-style: italic;]');
			$row      = new RCLayout();
			# add arragements and checkboxes
			for ($i = 0; $i < $sumArrangem; $i++) {
				$row->newLine('.martop10')
					->addText($arrangements[$i]['location'], $col)
					->addText($arrangements[$i]['spcdesc'], $col);

				# if not empty speechcode & description add to doc
				if ($arrangements[$i]['ppcdcode']   != '') $row->addText($arrangements[$i]['ppcdcode']  , $col3);
				if ($arrangements[$i]['speechcode'] != '') $row->addText($arrangements[$i]['speechcode'], $col3);
				if ($arrangements[$i]['crtdesc']    != '') $row->addText($arrangements[$i]['crtdesc']   , $stCRT);

				$row->newLine()
					->addText('Effective Date / School Campus', $width120)
					->addText('Instructional Arrangement'     , $width120);

				# label for speechcode & description
				if ($arrangements[$i]['ppcdcode']   != '') $row->addText('SLC Code'  , $width95);
				if ($arrangements[$i]['speechcode'] != '') $row->addText('Speech Indicator Code', $width95);
				if ($arrangements[$i]['crtdesc']    != '') $row->addText('Location'             , $stLoc);

				$this->addYN($row->newLine('.martop10'), $arrangements[$i]['camp_attend']);
				$row->addText(
					'This is the campus the student would attend if not disabled. <i>If No, identify (list or describe) the services which cannot reasonably be provided on the student\'s home campus: ' . $arrangements[$i]['camp_attend_no'] . '</i>'
					, '.padtop5'
				);

				$this->addYN($row->newLine('.martop10'), $arrangements[$i]['camp_close']);
				$row->addText(
					'This is the campus which is as close as possible to the student\'s home. <i>If No, justify: ' . $arrangements[$i]['camp_close_no'] . '</i>'
					, '.padtop5'
				);

				$this->addYN($row->newLine('.martop10'), $arrangements[$i]['instruct_day']);
				$row->addText(
					'The student has available an instructional day commensurate with that of students without disabilities. <i>If No, justify: ' . $arrangements[$i]['instruct_day_no'] . '</i>'
					, '.padtop5'
				);
			}

			$layout->newLine()
				->addObject($row);
		}

		$layout->newLine('.martop10')
			->addText('2. TRANSITION SERVICES:');
		# data for TRANSITION checkboxes
		$tranServ = $this->std->getTransitionServices();

		$layout->newLine()
			->addObject($this->addCheck($tranServ['dt_age'] == 'Y' ? 'Y' : 'N'), '.width20')
			->addText('N/A due to age', '.padtop5')
			->newLine()
			->addText('The ARD/IEP committee discussed the following as applicable <i>(check if discussed):</i>')
			->newLine()
			->addObject($this->addCheck($tranServ['age14'] == 'Y' ? 'Y' : 'N'), '.width20')
			->addText(
				'Beginning at age 14 <i>(or younger if deemed appropriate by the IEP team)</i> the IEP must include a statement of the transition service needs of the student under the applicable components in the student\'s courses of study. The statement is updated annually.'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($tranServ['career_c'] == 'Y' ? 'Y' : 'N'), '.width20')
			->addText(
				'The student is interested in pursuing the following career: <i>' . $tranServ['career_t'] . '</i>'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($tranServ['courses_c'] == 'Y' ? 'Y' : 'N'), '.width20')
			->addText(
				'Courses to consider: <i>' . $tranServ['courses_t'] . '</i>'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($tranServ['notice17'] == 'Y' ? 'Y' : 'N'), '.width20')
			->addText(
				'Notice of transfer of parental rights required on or before the student\'s 17th birthday.'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($tranServ['inform17'] == 'Y' ? 'Y' : 'N'), '.width20')
			->addText(
				'The student/parent has been informed, on or before the student\'s 17th birthday, that the rights granted to parents under IDEA will transfer to the student when he/she reaches the age of 18 unless the parent has obtained guardianship. The parent will continue to receive notice of ARD/IEP committee meetings.'
				, '.padtop5'
			);

		$layout->newLine('.martop10')
			->addText('3. LEAST RESTRICTIVE ENVIRONMENT (LRE) ASSURANCES:')
			->newLine('.martop10')
			->addText('The ARD/IEP committee assures that:')
			->newLine()
			->addObject(
				RCLayout::factory()
					->addText('- removal of students with disabilities from the regular educational environment occurs only if the nature or severity of the disability is such that education in regular classes with the use of supplementary aids and services cannot be achieved satisfactorily.')
					->newLine()
					->addText('- each student with a disability participates in nonacademic and extracurricular services and activities, including meals, and recess periods, with non-disabled students to the maximum extent appropriate to the needs of that student.')
					->newLine()
					->addText('- to the maximum extent appropriate, students with disabilities, including students in public or private institutions or other care facilities, are educated with students who are non-disabled.')
					->newLine()
					->addText('- goals and objectives to facilitate the student\'s movement toward a more inclusive environment have been embedded within the IEP.')
					->newLine()
					->addText('- special education and related services are provided at public expense, under public supervision and direction, and without charge.')
				, '[margin-left: 40px; font-style: italic;]'
			);

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Notices
	 */
	public function renderNotices() {
		$notices = $this->std->getNoticesInfo();
		$count   = count($notices);
		$layout  = RCLayout::factory()
			->addText('<b>I. Notices:</b>')
			->newLine('.martop10');
		for ($i = 0; $i < $count; $i++) {
			if ($i > 0) $layout->newLine();

			$layout->addText('<i>' . $notices[$i]['notes'] . '</i>');
		}
		$layout->newLine('.martop10')
			->addText('1. DESTRUCTION OF RECORDS: Special education eligibility and educational records will be maintained for seven (7) years following the date of the last recorded action for each student served by the Special Education Department of the LEA. Every year these records will be reviewed and destroyed.')
			->newLine('.martop10')
			->addText('<i>2. NOTICE OF RELEASE OF RECORDS: </i>' . 'When a student enrolls in another school district, the LEA, upon notification, will send all records, including special education records, to the receiving school district.');

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Signatures
	 */
	public function renderSignatures() {
		$layout = RCLayout::factory()
			->newLine()
			->addText('<b>K. Signatures of the ARD/IEP Committee Members and Other Participants:</b>')
			->newLine('.martop10')
			->addText('My signature indicates that I was present at the ARD/IEP meeting, participated in the discussion, and understand what was discussed. I further understand that a check of agreement indicates that I have agreed to the decisions of the ARD/IEP committee made at this meeting.');

		$members  = $this->std->getCommetteMembers($this->typeCommitteMembers);
		$sumMemb  = count($members);
		$leftBord = new RCStyle('center [border-left: 1px solid black;]');
		$tbl      = RCTable::factory('.table')
			->setCol('300px')
			->setCol('')
			->setCol('50px')
			->setCol('50px')
			->addRow('.row')
			->addCell('Signature and Title Members', '.hr')
			->addCell('Position'                   , '.next-hr')
			->addCell('Agree'                      , '.next-hr')
			->addCell('Disagree'                   , '.next-hr');

		for ($i = 0; $i < $sumMemb; $i++) {
			$agree = $disagree = '';
			if ($members[$i]['partcat'] == 1) {
				$agree = '*';
			}
			if ($members[$i]['partcat'] == 2) {
				$disagree = '*';
			}

			$tbl->addRow('.row')
				->addCell('<i>' . $members[$i]['participantname'] . '</i>', '.cellBorder')
				->addCell('<i>' . $members[$i]['participantrole'] . '</i>', '.cellBorder')
				->addCell('<i>' . $agree . '</i>'                         , $leftBord)
				->addCell('<i>' . $disagree . '</i>'                      , $leftBord);
		}

		$checkerString = IDEAStudentRegistry::readStdKey(
			$this->std->get('tsrefid'),
			'tx_iep',
			'signatures_agreements_A',
			$this->std->get('stdiepyear')
		);

		$checkboxes = array();

		if ($checkerString != '') {
			# get array with fieldName|fieldvalue
			$fieldValue = explode('!!!', $checkerString);
			$sumFields  = count($fieldValue);
			for ($i = 0; $i < $sumFields - 1; $i++) {
				# change default value for current key
				$checkbox              = explode('|', $fieldValue[$i]);
				$keyField              = $checkbox[0];
				$checkboxes[$keyField] = $checkbox[1];
			}
		}

		$date     = '';
		$location = '';
		if (isset($checkboxes['field1'])) {
			$date = $checkboxes['field1'];
		}
		if (isset($checkboxes['field2'])) {
			$location = $checkboxes['field2'];
		}
		$layout->newLine()
			->addObject($tbl)
			->newLine()
			->addObject($this->addCheck(isset($checkboxes['field0']) ? $checkboxes['field0'] : ''), '.width20')
			->addText(
				'The committee mutually agreed to implement the services reflected in these proceedings.'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck(isset($checkboxes['field1']) ? $checkboxes['field1'] : ''), '.width20')
			->addText(
				'The members of this ARD committee have not reached mutual agreement. The school has offered and the parent has agreed to a recess of not more than 10 school days. During the recess the members shall consider alternatives, gather additional data, and/or obtain additional resource persons to enable them to reach mutual agreement. This recess does not apply if the student presents a danger of physical harm to himself or herself or others, or if the student has committed an expellable offense or an offense that may lead to placement in the DAEP. The committee will reconvene on this date: <i>' . $date .'</i> at this location: <i>' . $location . '</i>'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck(isset($checkboxes['field3']) ? $checkboxes['field3'] : ''), '.width20')
			->addText(
				'Information explaining why mutual agreement has not been reached shall be attached.'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck(isset($checkboxes['field4']) ? $checkboxes['field4'] : ''), '.width20')
			->addText(
				'The members who disagree shall be offered the opportunity to write their own statement.'
				, '.padtop5'
			)
			->newLine('.martop10')
			->addText('NOTE: SHARS is a school based program that does not affect an individual\'s personal lifetime benefits.')
			->newLine('.martop10')
			->addText('I understand that my signature indicates consent for the school to submit for Medicaid payment for all allowable services provided to me/my child as outlined in the IEP. I understand my consent is voluntary and can be revoked at anytime. Revocation is not retroactive (i.e. it does not negate an action that has occurred after the consent was given and before the consent was revoked). I also understand that the services as outlined in the IEP will be implemented whether or not my consent is given for Medicaid billing.')
			->newLine('.martop10')
			->addText('PARENT/STUDENT RIGHTS:')
			->newLine()
			->addText('You received a copy of the procedural safeguards upon the initial referral of you/your child for special education services. You/your child\'s rights were explained to you when you were/your child was initially referred for a full and individual evaluation. A copy of the procedural safeguards will be provided by the district only one time a year, except upon initial referral or parental request for evaluation, upon the first occurrence of the filing of a complaint, and upon request by a parent.');

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Health Information for RC Document
	 */
	public function renderNoticeOfAction() {
		$xmlData = IDEADef::getConstructionTemplate(270);
		$values  = $this->std->getConstruction(270, true);
		$doc     = IDEADocument::factory($xmlData)
			->mergeValues(base64_decode($values['values']));

		$this->rcDoc->addObject($doc->getLayout());
	}

	/**
	 * Generate block Progress Report
	 */
	public function renderProgresReport() {
		$goals = array(
			$this->std->getProgressReportStandard(),
			$this->std->getProgressReportBGB(),
			$this->std->getProgressReportMainstream(),
		);
		if (isset($goals[0][0])) {
			$this->progressReportGoals('Standards Based Goals Progress Report', $goals[0]);
		}
		if (isset($goals[1][0])) {
			$this->rcDoc->startNewPage();
			$this->progressReportGoals('Student Progress Report', $goals[1]);
		}
		if (isset($goals[2][0])) {
			$this->rcDoc->startNewPage();
			$this->progressReportGoals('Mainstream Progress Report', $goals[2]);
		}
	}

	/**
	 * Add to page table with goals & objectives
	 * @deprecated
	 * @param string $title title page(in header)
	 * @param array $goals array with goals, objectives & periods
	 */
//	protected function progressReportGoals($title, $goals) {
//		$layout = RCLayout::factory()
//			->newLine('[background: #C0C0C0]')
//			->addText(
//				$title,
//				'bold [font-size: 22px; color: white; padding: 5px 0px 5px 10px;]'
//			)
//			->newLine('[background: #C0C0C0]')
//			->addText(SystemCore::$VndName, '[font-size: 16px; color: white; padding: 0px 0px 5px 5px; font-style: italic;]')
//			->newLine()
//			->addText(
//				'<b>Student\'s Name: </b><i>' . (string)$this->std->get('stdname') . '</i>',
//				'[margin: 10px 0px 0px 10px; width: 400px;]'
//			)
//			->addText(
//				'<b>Student Folder: </b><i>' . (string)$this->std->get('stdiepyeartitle'),
//				'[margin-top: 10px;]'
//			);
//
//		$sumGoals  = count($goals);
//		$objective = new RCStyle('[padding-left: 20px;]');
//		$borderBot = new RCStyle('[border-bottom: 1px solid black; padding-left: 20px;]');
//		$stylePer  = new RCStyle('[border: 1px solid black; border-top: none; border-right: none; width: 50px;] center');
//		$tbl       = RCTable::factory('.table');
//		# even if exist goal || objective
//		for ($i = 0; $i < $sumGoals && ($goals[$i]['goal'] != '' || $goals[$i]['objective'] != ''); $i++) {
//			# if first row add cols & names
//			if ($i == 0) {
//				$tbl->setCol('');
//				# cols for periods
//				foreach ($goals[0]['periods'] as $per) {
//					$tbl->setCol('50px');
//				}
//				# 1st col
//				$tbl->addRow('[border-top: 1px solid black;]')
//					->addCell('Goals/Objectives', '.hr');
//				# periods
//				foreach ($goals[0]['periods'] as $per) {
//					$tbl->addCell($per['bm'] . PHP_EOL . $per['dsydesc'], '.next-hr');
//				}
//			}
//			# if objective is empty add goal
//			if ($goals[$i]['objective'] == '') {
//				$tbl->addRow()
//					->addCell('<b>' . (string)$goals[$i]['goal'] . '</b>');
//			}
//			# if goal is empty add objective
//			if ($goals[$i]['goal'] == '') {
//				$tbl->addRow()
//					->addCell(
//						$goals[$i]['objective'],
//						# if exist next goal add border
//						isset($goals[$i + 1]) && $goals[$i + 1]['objective'] == '' ? $borderBot : $objective
//					);
//			}
//			# add values periods
//			foreach ($goals[$i]['periods'] as $per) {
//				$narrative = '';
//
//				if ($per['narrative'] != '') $narrative = '<i>' . PHP_EOL . $per['narrative'] . '</i>';
//
//				$tbl->addCell((string)$per['value'] . $narrative, $stylePer);
//			}
//		}
//
//		$layout->newLine()
//			->addObject($tbl)
//			->newLine()
//			->addObject(
//				RCLayout::factory('[width: 70%]')
//					->addText('<b>Marking Periods:</b>', '[width: 70px; border-bottom: 1px solid black;]')
//			)
//			->addObject(
//				RCLayout::factory('[width: 30%]')
//					->addText('<b>Legend:</b>', '[width: 40px; border-bottom: 1px solid black;]')
//			);
//		# add rows with periods & legends
//		if (isset($goals[0])) {
//			$allPeriods = count($goals[0]['periods']);
//			$legends    = IDEADistrict::factory(SystemCore::$VndRefID)->getProgressExtents();
//
//			for ($j = 0; $j < $allPeriods; $j++) {
//				$per = $goals[0]['periods'][$j];
//				$layout->newLine()
//					->addText(
//						'<b>' . $per['bm'] . ' (' . $per['dsydesc'] . ')</b> - ' . $per['bmbgdt'] . ' - ' . $per['bmendt']
//					);
//				if (isset($legends[$j])) {
//					$layout->addText('<b>' . $legends[$j]->get('epsdesc') . '</b> - ' . $legends[$j]->get('epldesc'));
//				}
//			}
//		}
//
//		$this->rcDoc->addObject($layout);
//	}

}

?>
