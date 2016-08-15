<?php

/**
 * IDEABlockFIE.php
 * Class for creation blocks in FIE builder(State TX).
 *
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 26-12-2013
 */
class IDEABlockFIE extends IDEABlock {

	/**
	 * @var IDEAStudentTXFIE
	 */
	protected $std;

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Generate block Student Demographics for RC Document
	 */
	public function renderStDem() {
		$layout = $this->nameOffice('FULL AND INDIVIDUAL EVALUATION
		Determination of Disability and Educational Need');
		# default value for checkbox
		if ($this->queryData['standartAss'] == '') {
			$this->queryData['standartAss'] = 'Y';
		}
		$this->addYN($layout, $this->queryData['standartAss']);
		$layout->addText(
			'Evaluation of this student was conducted using standard assessment procedures for all tests administered.',
			new RCStyle('[width: 500px; padding-top: 7px;]')
		);
		$layout->newLine();
		$layout->addText(
			'If no, please explain the rationale:',
			new RCStyle('[width: 125px; font-style: italic;]')
		);
		$layout->addText('<i>' . $this->queryData['rationale'] . '</i>');
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Language/Communicative Status for RC Document
	 */
	public function renderLang() {
		# common layout
		$layout = new RCLayout();
		$layout->addText(
			'I. Language/Communicative Status',
			$this->titleStyle('width: 200px;')
		);
		# create table Source Of Data by param
		$dataofSource            = $this->tableSourceOfData(1);
		$resultAndInterpretation = RCTable::factory('.table')
			->addLeftHeading('Results and Interpretations:')
			->addRow('.row')
			->addCell('Student\'s dominant language:', '.hr')
			->addCell('Student expresses himself/herself best:', '.next-hr')
			->addRow('.row');
		# set language 'English'
		$englishCell = new RCLayout();
		$englishCell->addObject($this->addCheck($this->std->englishKnowledge('dominant')), '.width20');
		$resultAndInterpretation->addCell(
			$englishCell->addText('English', '.padtop5'),
			'.cellBorder');
		# set orally params
		$orallyCell = new RCLayout();
		$orallyCell->addObject($this->addCheck($this->checkOrally()), '.width20');
		$resultAndInterpretation->addCell(
				$orallyCell->addText('Orally', '.padtop5'),
				'.cellBorder'
			)
			->addRow('.row');
		# check Spanish language
		$spanishCell = new RCLayout();
		$spanishCell->addObject($this->addCheck($this->checkLang('Spanish')), '.width20');
		$resultAndInterpretation->addCell(
			$spanishCell->addText('Spanish', '.padtop5'),
			'.cellBorder');
		$other = new RCLayout();
		$other->addText(
			'Other <i>(Specify the method of communication and the basis for the
			determination.):</i>'
		);
		$resultAndInterpretation->addCell($other)
			->addRow('.row')
			->addCell('Other: ', '.cellBorder')
			->addCell('',        '.cellBorder');

		$averageCell    = new RCStyle('[width: 50px; padding-right: 20px;]');
		$widthReceptive = new RCStyle('[padding-left: 15px; width: 120px;]');
		$average        = new RCLayout();

		$average->addObject(
			RCLayout::factory()
				->addText('Above',   $averageCell)
				->addText('Average', $averageCell)
				->addText('Below',   $averageCell)
				->newLine()
				->addText('Average', $averageCell)
				->addText('',        $averageCell)
				->addText('Average', $averageCell)
		);
		$widthMar           = new RCStyle('[width: 50px; margin-right: 30px;]');
		$levelOfProficiency = RCTable::factory('.table')
			->setCol('')
			->setCol('')
			->addRow('.row')
			->addCell('Level of Proficiency', '.hr', 2)
			->addRow('.row')
			->addCell('English',         '.hr')
			->addCell('Other language:', '.next-hr')
			->addRow('.row')
			->addCell($average, new RCStyle('[padding-left: 120px;]'))
			->addCell($average, new RCStyle('[padding-left: 120px; border-left: 1px solid black;]'))
			->addRow('.row')
			->addCell(
				# add Receptive row to left column
				RCLayout::factory()
					->addText('Receptive', $widthReceptive)
					->addObject($this->levelOfProficiencyYN('eng_lep_rec', 1), $widthMar)
					->addObject($this->levelOfProficiencyYN('eng_lep_rec', 2), $widthMar)
					->addObject($this->levelOfProficiencyYN('eng_lep_rec', 3), $widthMar)
				, '.cellBorder'
			)
			->addCell(
				# add Receptive to right column
				RCLayout::factory()
					->addText('Receptive', $widthReceptive)
					->addObject($this->levelOfProficiencyYN('oth_lep_rec', 1), $widthMar)
					->addObject($this->levelOfProficiencyYN('oth_lep_rec', 2), $widthMar)
					->addObject($this->levelOfProficiencyYN('oth_lep_rec', 3), $widthMar)
				,
				'.cellBorder'
			)
			->addRow('.row')
			->addCell(
				# add Expressive row to left column
				RCLayout::factory()
					->addText('Expressive', $widthReceptive)
					->addObject($this->levelOfProficiencyYN('oth_lep_rec', 1), $widthMar)
					->addObject($this->levelOfProficiencyYN('oth_lep_rec', 2), $widthMar)
					->addObject($this->levelOfProficiencyYN('oth_lep_rec', 3), $widthMar)
				, '.cellBorder'
			)
			->addCell(
				# add Expressive row to right column
				RCLayout::factory()
					->addText('Expressive', $widthReceptive)
					->addObject($this->levelOfProficiencyYN('eng_lep_exp', 1), $widthMar)
					->addObject($this->levelOfProficiencyYN('eng_lep_exp', 2), $widthMar)
					->addObject($this->levelOfProficiencyYN('eng_lep_exp', 3), $widthMar)
				, '.cellBorder'
			);
		# if column is empty add padding to row
		if ($this->std->englishKnowledge('lpac_test') == null) {
			$lpacStyle = new RCStyle('[padding-top: 10px;]');
		} else {
			$lpacStyle = null;
		}
		# add LPAC
		$lpac = RCTable::factory('.table')
			->setCol('')
			->setCol('')
			->addRow('.row')
			->addCell('LPAC', '.hr', 2)
			->addRow('.row')
			->addCell('Name of Test',  '.hr')
			->addCell('Score/Results', '.next-hr')
			->addRow('.row')
			->addCell($this->std->englishKnowledge('lpac_test'),  $lpacStyle)
			->addCell($this->std->englishKnowledge('lpac_score'), '.cellBorder');
		# switcher with conducted languages
		$lpacYN = new RCLayout();
		$this->addYN($lpacYN, $this->std->englishKnowledge('limited_prof'));
		$lpacYN->addText(
				'No This student is limited English proficient.',
				new RCStyle('[padding: 5px 0px 15px 0px; width: 155px;]')
			)
			->addText('If yes, give LPAC recommendations:', new RCStyle('[padding-top: 5px; font-style: italic;]'))
			->newLine()
			->addText('Based on the evaluation of this student\'s language abilities, the remainder of the evaluation was conducted:');

		# add checkboxes, add info about level language
		$languageContucted = IDEADef::getValidValues('TXLEP');
		foreach ($languageContucted as $lng) {
			/** @var IDEADefValidValue $lng */
			$lpacYN->newLine();
			if ($lng->get(IDEADefValidValue::F_VALUE) == $this->std->englishKnowledge('conducted')) {
				$lpacYN->addObject($this->addCheck('Y'), '.width20');
			} else {
				$lpacYN->addObject($this->addCheck('N'), '.width20');
			}
			$lpacYN->addText($lng->get(IDEADefValidValue::F_VALUE), '.padtop5');
		}
		$lpacYN->newLine()
			->addText('
				Describe other pertinent findings, including how testing procedures or test selections were adapted/modified to address the student\'s
				language/communication needs:'
			)
			->newLine()
			->addText('<i>' . (string)$this->std->englishKnowledge('findings') . '</i>');
		# add objects with data to common layout
		$layout->newLine()
			->addObject($dataofSource)
			->newLine()
			->addObject($resultAndInterpretation)
			->newLine()
			->addObject($levelOfProficiency)
			->newLine()
			->addObject($lpac)
			->newLine()
			->addObject($lpacYN);
		$this->rcDoc->addObject($layout);
	}

	private function levelOfProficiencyYN($key, $val) {
		$flag = 'N';
		if ($this->std->englishKnowledge($key) == $val) {
			$flag = 'Y';
		}
		return $this->addCheck($flag);
	}

	/**
	 * Check language student. Return 'Y' | 'N' for checkbox
	 *
	 * @param string $lang
	 * @return string string
	 */
	public function checkLang($lang) {
		$return = 'N';
		#default language
		if ($lang == 'English' && !isset($this->queryData['dominant_language'])) {
			$return = 'Y';
		}
		# if other language
		if (isset($this->queryData['dominant_language'])) {
			if ($this->queryData['dominant_language'] == $lang) {
				$return = 'Y';
			}
		}
		return $return;
	}

	/**
	 * Check Student expresses himself/herself best
	 *
	 * @return string
	 */
	public function checkOrally() {
		if (!isset($this->queryData['interpreter_mode']) ||
			$this->queryData['interpreter_mode'] == 'Yes') {
			$check = 'Y';
		} else {
			$check = 'N';
		}
		return $check;
	}

	/**
	 * Generate Physical block for RC Document
	 */
	public function renderPhysical() {
		# base layout for block
		$layout = new RCLayout();

		$layout->addText(
			'II. Physical',
			$this->titleStyle('width: 70px;')
		)
		->newLine();
		# create table Source Of Data by param
		$dataofSource            = $this->tableSourceOfData(2);
		# create table with Strength & Weaknesses
		$resultAndInterpretation = $this->tableStrength(3, 'tx_fie', 'physical_results');
		$layout->addObject($dataofSource)
			->newLine()
			->addObject($resultAndInterpretation);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block III. Vision/Hearing for RC Document
	 */
	public function renderVision() {
		$layout = new RCLayout();
		$left20 = new RCStyle('[padding-left: 20px; width: 40px;]');
		$desc   = new RCStyle('[padding-left: 20px; font-style: italic;]');

		$layout->addText(
			'III. Vision/Hearing:',
			$this->titleStyle('width: 90px;')
		)
		->newLine();

		$table = RCTable::factory('.table')
			->addRow('.row')
			->addCell('Vision',  '.hr')
			->addCell('Hearing', '.next-hr')
			->addRow('.row')
			->addCell(
				# create layout with checkboxes for left column
				RCLayout::factory()
					->addObject(
						$this->addCheck(
							# 'Y' | 'N'
							$this->std->visionData('visionok')
						), '.width20'
					)
					->addText('Within normal limits', '.padtop5')
					->newLine()
					->addObject(
						$this->addCheck(
							# 'Y' | 'N'
							$this->YN('vision_glass', 'O')
						), $left20
					)
					->addText('Without glasses', '.padtop5')
					->newLine()
					->addObject(
						$this->addCheck(
							$this->YN('vision_glass', 'W')
						), $left20
					)
					->addText('With glasses')
				, '.cellBorder'
			)
			->addCell(
				# create layout with checkboxes for left column
				RCLayout::factory()
					->addObject(
						$this->addCheck(
							$this->std->visionData('hearingok')
						), '.width20'
					)
					->addText('Within normal limits', '.padtop5')
					->newLine()
					->addObject(
						$this->addCheck(
							$this->YN('hearing_aid', 'O')
						), '.width20'
					)
					->addText('Unaided', '.padtop5')
					->newLine()
					->addObject(
						$this->addCheck(
							$this->YN('hearing_aid', 'A')
						), $left20
					)
					->addText('Aided', '.padtop5')
				, '.cellBorder'
			)
			->addRow('.row')
			->addCell(
				RCLayout::factory()
					->addObject(
						$this->addCheck(
							$this->YN('visionok', 'N')
						), '.width20'
					)
					->addText('Not within normal limits', '.padtop5')
					# add description
					->newLine()
					->addText('(See the ophthalmologist\'s or optometrist\'s report.)', $desc)
				, '.cellBorder'
			)
			->addCell(
				RCLayout::factory()
					->addObject(
						$this->addCheck(
							$this->YN('hearingok', 'N')
						), '.width20'
					)
					->addText('Not within normal limits', '.padtop5')
					# add description
					->newLine()
					->addText('(See the ophthalmologist\'s or optometrist\'s report.)', $desc)
				, '.cellBorder'
			);
		$this->rcDoc->addObject($layout->addObject($table));
	}

	/**
	 * Check field value in array. Return 'Y' | 'N' for image checkbox.
	 * Some fields in db have different values. But method addCheck use only 'Y' | 'N'
	 *
	 * @param string $keyField
	 * @param string $value
	 * @return string
	 */
	private function YN($keyField, $value) {
		if ($this->std->visionData($keyField) == $value) {
			return 'Y';
		} else {
			return 'N';
		}
	}

	/**
	 * Create table Source Of Data by options from db.
	 *
	 * @param int $apptype
	 * @return RCTable
	 */
	private function tableSourceOfData($apptype) {
		$data         = $this->std->getSourcesData($apptype);
		$dataofSource = RCTable::factory('.table')
			->setCol()
			->setCol('100px')
			->addRow('.row')
			->addCell('Sources of Data (formal and informal measures)', '.hr')
			->addCell('Date', '.next-hr');

		$count = count($data);
		# add rows source of data
		for ($i = 0; $i < $count; $i++) {
			$dataofSource->addRow('.row')
				->addCell('<i>' . $data[$i]['datasource'] . '</i>', '.cellBorder')
				->addCell('<i>' . $data[$i]['datesource'] . '</i>', '.cellBorder');
		}
		return $dataofSource;
	}

	/**
	 * Create table with Strength & Weaknesses
	 *
	 * @param int $area
	 * @param string $keygroup
	 * @param string $keyname
	 * @return RCTable
	 */
	private function tableStrength($area, $keygroup, $keyname) {
		$data        = $this->std->strength($area);
		$leftHeading = 'Results and Interpretations: ' . IDEAStudentRegistry::readStdKey(
			$this->std->get('tsrefid'),
			$keygroup,
			$keyname,
			$this->std->get('stdiepyear')
		);

		$resultAndInterpretation = RCTable::factory('.table')
			->addLeftHeading($leftHeading)
			->addRow('.row')
			->addCell('Strengths',  '.hr')
			->addCell('Weaknesses', '.next-hr');

		$count = count($data);
		for ($i = 0; $i < $count; $i++) {
			$resultAndInterpretation->addRow('.row')
				->addCell('<i>' . $data[$i]['strength'] . '</i>', '.cellBorder')
				->addCell('<i>' . $data[$i]['weakness'] . '</i>', '.cellBorder');
		}
		return $resultAndInterpretation;
	}

	/**
	 * Generate block Health History for RCDocument
	 *
	 * @return RCLayout
	 */
	public function renderHealth() {
		$layout = new RCLayout();
		$layout->addText(
			'IV. Health History',
			$this->titleStyle('width: 90px;')
		)
		->newLine();
		# first row with checkbox and description
		$this->addYN($layout, $this->YN('health_history', 'Y'));
		$layout->addObject(
			RCLayout::factory()
				->addText('Is there a significant health history? <i>If yes, specify: ' . $this->std->visionData('health_history_text') . '</i>')
			, '.padtop5'
		)
		->newLine();
		# second row
		$this->addYN($layout, $this->YN('health_condition', 'Y'));
		$layout->addObject(
			RCLayout::factory()
				->addText('This student appears to have one or more physical conditions which directly affect his/her ability to benefit from ')
				->newLine()
				->addText('the educational process? <i>If yes, specify: ' . $this->std->visionData('health_condition_text') . '</i>')
			, '.padtop5'
		)
		->newLine();
		# third row
		$this->addYN($layout, $this->YN('health_adaptive', 'Y'));
		$layout->addObject(
			RCLayout::factory()
				->addText('Adapted physical education is indicated? <i>If Yes, see attached evaluation report for adapted physical education. </i>' . $this->std->visionData('health_adaptive_text'))
			, '.padtop5'
		);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block V. Sociological for RCDocument
	 */
	public function renderSociological() {
		$layout = new RCLayout();
		$layout->addText(
			'V. Sociological',
			$this->titleStyle('width: 70px;')
		)
		->newLine();
		# create table Source Of Data by param
		$dataofSource = $this->tableSourceOfData(5);
		$layout->addObject($dataofSource)
			->newLine()
			->addText(
				'Results and Interpretations: <i>' .
				IDEAStudentRegistry::readStdKey(
					$this->std->get('tsrefid'),
					'tx_fie',
					'sociological_results',
					0
				) . '</i>');
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create block Cultural, Linguistic, and Experiential Background
	 */
	public function renderCultural() {
		# Get data from db for this block
		$data   = $this->std->cultural();
		$layout = new RCLayout();
		$layout->addText(
			'VI. Cultural, Linguistic, and Experiential Background',
			$this->titleStyle('width: 220px;')
		);
		$count = count($data);
		for ($i = 0; $i < $count; $i++) {
			$layout->newLine();
			# if stdref id > 0 add checked image
			if ($data[$i]['stdrefid'] > 0) {
				$check = 'Y';
			} else {
				$check = 'N';
			}
			$layout->addObject(
				$this->addCheck($check),
				'.width20'
			)
			->addText($data[$i]['b_name'], '.padtop5');
		}
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create block VII. Social/Emotional
	 */
	public function renderEmotional() {
		$layout = new RCLayout();
		$layout->addText(
			'VII. Social/Emotional',
			$this->titleStyle('width: 220px;')
		)
		->newLine();
		# create table Source Of Data by param & table with Strength param
		$dataofSource              = $this->tableSourceOfData(7);
		$resultsAndInterpretations = $this->tableStrength(2, 'tx_fie', 'social_emotional');
		$layout->addObject($dataofSource)
			->newLine()
			->addObject($resultsAndInterpretations);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create block VIII. Intelligence and Adaptive Behavior
	 */
	public function renderIntelligence() {
		$layout = new RCLayout();
		$layout->addText(
			'VIII. Intelligence and Adaptive Behavior',
			$this->titleStyle('width: 220px;')
		)
		->newLine();
		# create table Source Of Data by param
		$dataofSource = $this->tableSourceOfData(8);
		$leftHeading  = 'Results and Interpretations: ' . IDEAStudentRegistry::readStdKey(
			$this->std->get('tsrefid'),
			'tx_fie',
			'adaptice_b',
			$this->std->get('stdiepyear')
		);
		# table Intellectual Functioning
		$resultAndInterpretation = RCTable::factory('.table')
			->addLeftHeading($leftHeading)
			->setCol()
			->setCol()
			->setCol('150px')
			->setCol()
			->setCol()
			->addRow('.row')
			->addCell('Intellectual Functioning',  '.hr', 5)
			->addRow('.row')
			->addCell('Verbal Score: <i>'               . $this->std->intelligence('verbal') . '</i>',     '.cellBorder')
			->addCell('Nonverbal Score: <i>'            . $this->std->intelligence('nonverbal') . '</i>',  '.cellBorder')
			->addCell('Full Scale/Composite Score: <i>' . $this->std->intelligence('composite') . '</i>',  '.cellBorder')
			->addCell('SEM: <i>'                        . $this->std->intelligence('sem') . '</i>',        '.cellBorder')
			->addCell('Other: <i>'                      . $this->std->intelligence('func_other') . '</i>', '.cellBorder');

		# table Adaptive Behavior
		$behavior = RCTable::factory('.table')
			->setCol()
			->setCol()
			->addRow('.row')
			->addCell('Adaptive Behavior', '.hr', 2)
			->addRow('.row')
			->addCell('Area',  '.hr')
			->addCell('Score', '.next-hr')
			->addRow('.row');

		$scopes = $this->std->adaptiveScores();
		foreach ($scopes as $scope) {
			$behavior->addCell('<i>' . $scope['validvalue'] . '</i>', '.cellBorder')
				->addCell('<i>' . $scope['score'] . '</i>',           '.cellBorder')
				->addRow('.row');
		}
		$behavior->addCell(
				'Composite Score: <i>' . $this->std->intelligence('composite1') . '</i>',
				new RCStyle('center'), 2
			)
			->addRow('.row');

		# layout for checkboxes
		$sublayout = new RCLayout();

		$this->addYN($sublayout, $this->std->intelligence('level'));
		$sublayout->addText(
			'This student\'s level of intellectual functioning is consistent with his/her adaptive behavior.',
			new RCStyle('[width: 320px;]')
		)
		->addText('<i>If no, check applicable:</i>');

		$areas      = $this->std->adaptiveAreas();
		$count      = count($areas);
		$tableAreas = RCTable::factory(new RCStyle('[margin-left: 90px;]'))
			->setCol()
			->setCol();
		for ($i = 0; $i < $count; $i += 2) {
			$tableAreas->addRow()
				->addCell(
					RCLayout::factory()
						->addObject(
							# if validvalue exist in arrays areas & scopes add checked, else - unchecked
							$this->addCheck($this->checkScope($scopes, $areas[$i])),
							'.width20'
						)
						->addText($areas[$i]['validvalue'], '.padtop5')
				)
				->addCell(
					RCLayout::factory()
						->addObject(
							$this->addCheck($this->checkScope($scopes, $areas[$i + 1])),
							'.width20'
						)
						->addText($areas[$i + 1]['validvalue'], '.padtop5')
				);
		}
		$sublayout->newLine()->addObject($tableAreas);
		$behavior->addCell($sublayout, null, 2);
		$layout->addObject($dataofSource)
			->newLine()
			->addObject($resultAndInterpretation)
			->newLine()
			->addObject($behavior);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Return value for checkbox. Check validvalue in arrays scopes & areas
	 *
	 * @param array $scopes
	 * @param array $area
	 * @return string
	 */
	private function checkScope($scopes, $area) {
		$flag = 'N';
		foreach ($scopes as $scope) {
			if ($area['validvalue'] == $scope['validvalue']) {
				$flag = 'Y';
			}
		}
		return $flag;
	}

	/**
	 * Create block Academic Performance
	 */
	public function renderAcademic() {
		$layout = new RCLayout();
		$layout->addText(
			'IX. Academic Performance',
			$this->titleStyle('width: 220px;')
		)
		->newLine();

		$dataofSource = $this->tableSourceOfData(9);
		$resAndInt    = $this->tableStrength(1, 'tx_fie', 'academic_results');
		$specialRule  = RCLayout::factory()
			->addText(
				'IX. Special Rule for Eligibility Determination:',
				$this->titleStyle('width: 220px;')
			)
			->newLine()
			->addText('A child shall not be determined to be a child with a disability if the determinant factor for such determination is:')
			->newLine()
			->addObject(
				RCLayout::factory()
					->addText('1. lack of appropriate instruction in reading', new RCStyle('[width: 155px;]'))
					->addText('<i>(including in the essential components of reading instruction)</i>,')
					->newLine()
					->addText('2. lack of instruction in math,', new RCStyle('[width: 110px;]'))
					->addText('<b>or</b>')
					->newLine()
					->addText('3. limited English proficiency')
				,
				new RCStyle('[margin-left: 40px;]')
			);
		$layout->addObject($dataofSource)
			->newLine()
			->addObject($resAndInt)
			->newLine()
			->addObject($specialRule);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create block Recommendations
	 */
	public function renderRecommendations() {
		$layout   = new RCLayout();
		$padBot10 = new RCStyle('[padding-bottom: 10px;]');
		$layout->addText(
			'X. Recommendations',
			$this->titleStyle('width: 90px;')
		)
		->newLine()
		->addText(
			'TO BE COMPLETED FOR STUDENTS ACCESSING GENERAL EDUCATION CLASSES:',
			$padBot10
		)
		->newLine()
		->addText('Based upon the evaluation data, the following accommodations of instructional content, settings, methods, or materials ')
		->newLine()
		->addText('in the regular educational setting', new RCStyle('[width: 120px;]'))
		->addText(
			'(including other special, compensatory and accelerated instructional programs)',
			new RCStyle('[width: 285px; font-style: italic;]')
		)
		->addText('required by this')
		->newLine()
		->addText(
			'student to achieve and maintain satisfactory progress are being recommended at this time',
			new RCStyle('[width: 325px;]')
		)
		->addText('<i>(check applicable):</i>')
		->newLine();

		#create style for tables and generate table wit checkboxes
		$tableStyle   = new RCStyle('[margin-top: 10px; margin-bottom: 10x;]');
		$generalTable = $this->tableSpecialGeneral(1, $tableStyle);
		$specialTable = $this->tableSpecialGeneral(0, $tableStyle);

		$layout->addObject($generalTable)
			->newLine()
			->addText(
				'TO BE COMPLETED FOR STUDENTS ACCESSING SPECIAL EDUCATION SERVICES OUTSIDE OF THE GENERAL EDUCATION SETTING',
				$padBot10
			)
			->newLine()
			->addText('Based upon the evaluation data, the following accommodations and/or modifications of instructional content, settings, methods, or materials that can')
			->newLine()
			->addText('only', new RCStyle('[width: 18px; font-style: italic;]'))
			->addText('be provided through special education services required by this student to achieve and maintain satisfactory progress are being recommended')
			->newLine()
			->addText('at this time', new RCStyle('[width: 45px;]'))
			->addText('<i>(check applicable):</i>')
			->newLine()
			->addObject($specialTable);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create tables with checkboxes by part. Tables use in block Recommendations
	 *
	 * @param int $part
	 * @param RCStyle $tableStyle
	 * @return RCTable
	 */
	private function tableSpecialGeneral($part, RCStyle $tableStyle) {
		$table = RCTable::factory($tableStyle)
			->setCol()
			->setCol();
		$checkboxes = $this->std->recomendations($part);
		$count      = count($checkboxes);
		for ($i = 0; $i < $count; $i += 2) {
			$table->addRow()
				->addCell(
					RCLayout::factory()
						->addObject(
						# if stdrefid not null add selected
							$this->addCheck($checkboxes[$i]['stdrefid'] > 0 ? 'Y' : 'N'),
							'.width20'
						)
						->addText($checkboxes[$i]['r_name'], '.padtop5')
				)
				->addCell(
					RCLayout::factory()
						->addObject(
							$this->addCheck($checkboxes[$i + 1]['stdrefid'] > 0 ? 'Y' : 'N'),
							'.width20'
						)
						->addText($checkboxes[$i + 1]['r_name'], '.padtop5')
				);
		}
		return $table;
	}

	/**
	 * Create block Other Factors to Consider to Ensure FAPE
	 */
	public function renderFactors() {
		$layout = new RCLayout();
		$layout->addText(
			'XI. Other Factors to Consider to Ensure FAPE',
			$this->titleStyle('width: 200px;')
		)
		->newLine()
		->addText(
			'The student\'s assistive technology needs were considered and screened. Formal and informal measures were used to evaluate the student\'s need for assistive technology devices and/or services.'
		)
		->newLine();
		$this->addYN($layout, $this->std->visionData('factors_oth_ch'));
		$layout->addObject(
				RCLayout::factory()
					->addText(
						'Based upon this information, it was decided that assistive technology devices and/or services are required in order',
						'.padtop5'
					)
					->newLine()
					->addText(
						'for the student to receive a free appropriate public education',
						new RCStyle('[width: 220px;]')
					)
					->addText('<i>If yes, please explain: ' . $this->std->visionData('factors_oth') . '</i>'),
				new RCStyle('[line-height: 6px;]')
			);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create block Assurances
	 */
	public function renderAssurances() {
		$layout       = new RCLayout();
		# layout for text. Use unique style
		$textLayout   = new RCLayout();
		$italicHeight = new RCStyle('[line-height: 6px; font-style: italic;]');
		$top10        = new RCStyle('[margin-top: 10xp;]');

		# add blocks with text
		$textLayout->addText(
				'- The school district assures a variety of assessment tools and strategies were used to gather relevant functional, developmental, and academic information, including information provided by the parent, that may assist in determining whether the child is a child with a disability and the content of the child\'s IEP, including information related to enabling the child to be'
			)
			->newLine()
			->addText(
				'involved in and progress in the general education curriculum,',
				new RCStyle('[width: 220px; line-height: 6px;]')
			)
			->addText('(or for a preschool student, information related to enabling the).', $italicHeight)
			->newLine()
			->addText('student to participate in appropriate activities).', $italicHeight)
			->newLine($top10)
			->addText('- The school district assures technically sound instruments were used that may assess the relative contribution of cognitive and behavioral factors, in addition to physical or developmental factors.')
			->newLine($top10)
			->addText('- The school district assures that the testing, evaluation materials, and procedures used for the purposes of evaluation were selected and administered so as not to be racially or culturally discriminatory.')
			->newLine($top10)
			->addText('- The school district assures that the tests and other evaluation materials have been validated for the specific purpose for which they were used.')
			->newLine($top10)
			->addText('- The school district assures that the tests and other evaluation materials were administered by trained and knowledgeable personnel in conformance with the instructions provided by their producers.')
			->newLine($top10)
			->addText('- The school district assures assessments and other evaluation materials used were provided and administered in the child\'s native language or other mode of communication and in the form most likely to yield accurate information on what the child knows and can do academically, developmentally, and functionally, unless it is not feasible to so provide or administer.')
			->newLine($top10)
			->addText('- The school district assures tests and other evaluation materials used to evaluate the child are those tailored to assess specific areas of educational need and not merely those that are designed to provide a single general intelligence quotient.')
			->newLine($top10)
			->addText('- The school district assures that tests were selected and administered so as to best ensure that when the test is administered to the child with impaired sensory, manual, or speaking skills, the test results accurately reflect the child\'s aptitude or achievement levels, or whatever other factors the test purports to measure, rather than reflecting the child\'s impaired sensory,')
			->newLine()
			->addText('manual, or speaking skills', new RCStyle('[line-height: 6px; width: 98px;]'))
			->addText('(unless those skills are the factors that the test purports to measure).', $italicHeight)
			->newLine($top10)
			->addText('- The school district assures all areas of suspected disability were assessed, including, if appropriate, health, vision, hearing, social and emotional status, general intelligence, academic performance, communicative status, and motor abilities.')
			->newLine($top10)
			->addText('- The school district assures the evaluation is sufficiently comprehensive to identify all of the child\'s special education and related services needs, whether or not commonly linked to the disability category in which the child has been classified.')
			->newLine($top10)
			->addText('- The school district assures assessment tools and strategies were administered that provide relevant information that directly assists persons in determining the educational needs of the child.');
		$layout->addText(
			'XII. Assurances',
			$this->titleStyle('width: 70px;')
		)
		->newLine()
		->addObject($textLayout, new RCStyle('[margin-left: 20px; width: 480px;]'));
		# add participants
		$participants = $this->std->participants();
		$borderSign   = new RCStyle('[border-bottom: 1px solid black; width: 250px; padding-top: 10px;]');
		$borderPos    = new RCStyle('[border-bottom: 1px solid black; width: 200px; margin: 10px 0px 0px 50px;]');
		$textSign     = new RCStyle('[padding-left: 5px; line-height: 7px; font-style: italic;]');
		$textPos      = new RCStyle('[padding-left: 80px; line-height: 7px; font-style: italic;]');
		foreach ($participants as $participant) {
			$textLayout->newLine()
				->addText($participant['name'],     $borderSign)
				->addText($participant['position'], $borderPos)
				->newLine()
				->addText('Signature', $textSign)
				->addText('Position',  $textPos);
		}
		$textLayout->newLine($top10)
			->addObject(
				$this->addCheck($this->std->visionData('assur_copy')),
				'.width20'
			)
			->addText(
				'A copy was provided to the parent. Date provided:',
				new RCStyle('[padding-top: 5px; width: 190px;]')
			)
			->addText(
				(string)$this->std->visionData('assur_copy_dt'),
				new RCStyle('[border-bottom: 1px solid black; padding-top: 5px; width: 55px; height: 20px;]')
			);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Create block Speech Impairment
	 */
	public function renderImpairment() {
		$layout       = $this->nameOffice('FULL AND INDIVIDUAL EVALUATION
		Eligibility Report: SPEECH IMPAIRMENT');
		$center       = new RCStyle('[text-align: center; border-left: 1px solid black;]');
		$profEvalData = $this->std->speechHearing();
		$profEvalTbl  = RCTable::factory('.table')
			->setCol('300px')
			->setCol()
			->setCol()
			->setCol()
			->addRow('.row')
			->addCell('Sources of Data (formal and informal measures)', '.hr')
			->addCell('Assessment Date', '.next-hr')
			->addCell('Report Attached', '.next-hr', 2);
		# add rows into table
		foreach ($profEvalData as $row) {
			$profEvalTbl->addRow('.row')
				->addCell($row['s_src'],      '.cellBorder')
				->addCell($row['date'],       '.cellBorder')
				->addCell($row['report_yes'], $center)
				->addCell($row['report_no'],  $center);
		}
		$layout->addObject(
			RCLayout::factory()
				->addText(
					'PROFESSIONAL EVALUATOR: ',
					new RCStyle('[padding-left: 5px; font-weight: bold; width: 130px;]')
				)
				->addText('<i>Certified speech and hearing therapist</i>')
				->newLine()
				->addObject($profEvalTbl)
			, '.padtop5'
		);
		$this->addYN($layout->newLine(), $this->std->speechSummary('disorder'));
		$layout->addText('This student has a communication disorder, such as stuttering, impaired articulation, a language impairment, or a voice impairment, which adversely effects his/her educational performance creating a need for special education (i.e. specially designed instruction) and related services.')
			->newLine()
			->addText('<b>Reason for Referral to Special Education</b>')
			->newLine()
			->addText('<i>' . $this->std->speechSummary('rfr') . '</i>')
			->newLine()
			->addText('<b>Educational History</b>')
			->newLine()
			->addText('<i>' . $this->std->speechSummary('eduhistory') . '</i>')
			->newLine()
			->addText('<b>General Observations</b>')
			->newLine()
			->addText('<i>' . $this->std->speechSummary('observations') . '</i>')
			->newLine()
			->addText(
				'SPEECH/LANGUAGE TEST RESULTS AND INTERPRETATION OF RESULTS',
				'center [font-weight: bold;]'
			)
			->newLine()
			->addText('<b>Language Assessment: </b>')
			->newLine()
			->addText(
				'The following formal assessment was administered: <i>' .
				IDEAStudentRegistry::readStdKey(
					$this->std->get('tsrefid'),
					'tx_speech',
					'language_assessment',
					$this->std->get('stdiepyear')
				) . '</i>. A standard score of 85-115 is within normal limits while a standard score of 78 or less is a significant weakness.  A standard score of 79-84 is a relative weakness. The student\'s standard scores are listed on the following table.'
				, '.padbot10'
			);
		$assessmentTbl = RCTable::factory('.table')
			->addCell('Tests',           '.hr')
			->addCell('Standard Score',  '.next-hr')
			->addCell('Percentile Rank', '.next-hr');

		$assessmentRows = $this->std->speechLanguageTests();
		#add rows to table
		foreach ($assessmentRows as $row) {
			$assessmentTbl->addRow('.row')
				->addCell('<i>' . $row['test'] . '</i>',  '.cellBorder')
				->addCell('<i>' . $row['score'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['rank'] . '</i>',  '.cellBorder');
		}
		$layout->newLine()
			->addObject($assessmentTbl)
			->newLine()
			->addText(
				'<b>Informal Assessment procedures used: </b><i>' . $this->std->speechInformal('informal') . '</i>',
				'.padbot10'
			);
		RCStyle::defineStyleClass('padleft20', '[padding: 0px 0px 10px 20px;]');
		# add group checkboxes with labels
		$this->informalAssessmentGroup(
			$layout,
			'Syntax (including morphemes, phrases, clauses, and transformations)',
			array('syn_cognitive',    'syn_relative', 'syn_significant'),
			array('syn_relative_txt', 'syn_significant_txt')
		);
		$this->informalAssessmentGroup(
			$layout,
			'Semantics',
			array('sem_cognitive',    'sem_relative', 'sem_significant'),
			array('sem_relative_txt', 'sem_significant_txt')
		);
		$this->informalAssessmentGroup(
			$layout,
			'Pragmatics (including communicative intent, conversation, and narratives)',
			array('prag_cognitive',    'prag_relative', 'prag_significant'),
			array('prag_relative_txt', 'prag_significant_txt')
		);
		$this->informalAssessmentGroup(
			$layout,
			'Metalinguistics (including defining and describing)',
			array('met_cognitive',    'met_relative', 'met_significant'),
			array('met_relative_txt', 'met_significant_txt')
		);
		$layout->newLine()
			->addText('<b>Comments: </b>' . $this->std->speechInformal('comments'), '.padbot10')
			->newLine()
			->addText('<b>Articulation:</b>', '[border-bottom: 1px solid black; width: 55px;]')
			->newLine()
			->addText(
				'A percentile of 17% or greater is within normal limits while a percentile of 7% or less is a significant weakness.  A percentile of 8-16% is a relative weakness. The student achieved a percentile of <i>' .
				$this->std->speechArticulation('percentile') . '.</i>'
				, '.padbot10'
			)
			->newLine()
			->addText(
				'<b>The following formal assessment was administered: </b><i>' .
				$this->std->speechArticulation('formal') . '</i>'
				, '.padbot10'
			)
			->newLine()
			->addText(
				'<b>Informal Assessment procedures used: </b><i>' .
				$this->std->speechArticulation('informal') . '</i>'
				, '.padbot10'
			)
			->newLine()
			->addText(
				'<b>Phonemes and position in error: </b><i>' .
				$this->std->speechArticulation('phonemes') . '</i>'
				, '.padbot10'
			)
			->newLine()
			->addText(
				'<b>Phonological processes present: </b><i>' .
				$this->std->speechArticulation('phonological') . '</i>'
				, '.padbot10'
			);
		$this->informalAssessmentGroup(
			$layout,
			'Stimulable phonemes',
			array('stim_cognitive',    'stim_relative', 'stim_significant'),
			array('stim_relative_txt', 'stim_significant_txt'),
			'stimulable',
			'speechArticulation'
		);
		$layout->newLine()
			->addText('<b>Comments: </b>' . $this->std->speechArticulation('comments'), '.padbot10')
			->newLine()
			->addText('<b>Fluency:</b>', '[border-bottom: 1px solid black; width: 55px; margin-bottom: 10px;]')
			->newLine()
			->addText(
				'<b>Formal assessment tool used: </b><i>' .
				$this->std->speechFluency('formal') . '</i>'
				, '.padbot10'
			);
		$this->informalAssessmentGroup(
			$layout,
			'Informal assessment tool used',
			array('stim_cognitive',    'stim_relative', 'stim_significant'),
			array('stim_relative_txt', 'stim_significant_txt'),
			'formal',
			'speechFluency'
		);
		$layout->newLine()
			->addText('<b>Comments: </b>' . $this->std->speechFluency('comments'), '.padbot10')
			->newLine()
			->addText('<b>Voice:</b>', '[border-bottom: 1px solid black; width: 55px; margin-bottom: 10px;]');
		$this->informalAssessmentGroup(
			$layout,
			'Informal assessment tool used',
			array('stim_cognitive',    'stim_relative', 'stim_significant'),
			array('stim_relative_txt', 'stim_significant_txt'),
			'informal',
			'speechVoice'
		);
		$layout->newLine()
			->addText('<b>Comments: </b>' . $this->std->speechVoice('comments'), '.padbot10')
			->newLine()
			->addText('<b>Oral Peripheral:</b>', '[border-bottom: 1px solid black; width: 70px;]')
			->newLine()
			->addText((string)$this->std->speechSummary('oral'), '[line-height: 6px; margin-bottom: 10px;]')
			->newLine()
			->addText('<b>Summary of Evaluation:</b>', '[border-bottom: 1px solid black; width: 100px; margin-bottom: 10px;]');
		# questions with checkboxes
		RCStyle::defineStyleClass('questionPad', '[padding: 5px 0px 15px 0px;]');
		$this->question($layout, 'Is a speech-language disorder present',   'disorder_sw',  'disorder_txt');
		$this->question($layout, 'Is there an adverse effect on education', 'adverse_sw',   'adverse_txt');
		$this->question($layout, 'Are speech pathology services needed',    'pathology_sw', 'pathology_txt');
		$this->question(
			$layout,
			'According to the ' . SystemCore::$VndName . ' Speech Eligibility Guidelines, does the student meet the criteria as speech impaired',
			'criteria_sw',
			'criteria_txt'
		);
		# styles for columns & descriptions (overline)
		$col     = new RCStyle('[border-bottom: 1px solid black; width: 50%; margin-right: 20px;]');
		$colDesc = new RCStyle('center [width: 50%; margin-right: 20px;]');
		$layout->newLine()
			->addText('<b>Evaluation Summary</b>')
			->newLine()
			->addText(
				'<i>' . $this->std->speechSummOfEval('eval_summary') . '</i>',
				'[line-height: 6px; margin-bottom: 15px;]'
			)
			->newLine()
			->addText(
				'<b>Recommendations:</b>',
				'[border-bottom: 1px solid black; width: 80px; margin-bottom: 10px;]'
			)
			# checkboxes with labels(rows)
			->newLine()
			->addObject($this->addCheck($this->std->speechRecommend('therapy')), '.width20')
			->addText('It is recommended that the student receive speech therapy services to address his/her speech impairment.', '.padtop5')
			->newLine()
			->addObject($this->addCheck($this->std->speechRecommend('continue')), '.width20')
			->addText('It is recommended that the student continue to receive speech therapy services to address his/her speech impairment.', '.padtop5')
			->newLine()
			->addObject($this->addCheck($this->std->speechRecommend('remain')), '.width20')
			->addText('The student did not meet criteria as speech impaired, therefore, it is recommended that he/she remain in his/her current educational placement.', '.padtop5')
			->newLine()
			->addObject($this->addCheck($this->std->speechRecommend('dis_miss')), '.width20')
			->addText('The student did not meet criteria as speech impaired, therefore, it is recommended that he/she remain in his/her current educational placement.', '.padtop5')
			->newLine()
			->addObject($this->addCheck($this->std->speechRecommend('comment_sw')), '.width20')
			->addText(
				'Comments/other recommendations: <i>' . $this->std->speechRecommend('comments') . '</i>',
				'[margin-bottom: 10px; padding-top: 5px;]'
			)
			# underline rows
			->newLine()
			->addText('<i>' . $this->std->speechRecommend('signame') . '</i>',   $col)
			->addText('<i>' . $this->std->speechRecommend('sigelator') . '</i>', $col)
			->newLine()
			->addText('Speech-Language Pathologist', $colDesc)
			->addText('Certification/License',       $colDesc)
			->newLine()
			->addText('<i>' . $this->std->speechRecommend('signature') . '</i>',   $col)
			->addText('<i>' . $this->std->speechRecommend('position') . '</i>', $col)
			->newLine()
			->addText('Signature', $colDesc)
			->addText('Position',  $colDesc);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Add to layout question, checkboxes and comments
	 *
	 * @param RCLayout $layout
	 * @param string $text
	 * @param string $val
	 * @param mixed $comments
	 */
	private function question(RCLayout $layout, $text, $val, $comments) {
		$layout->newLine()
			->addText($text . '?');
		$this->addYN($layout->newLine(), $this->std->speechSummOfEval($val));
		$layout->addText('Comments: ' . $this->std->speechSummOfEval($comments), '.questionPad');
	}

	/**
	 * Add to layout row with checkbox and lables(often used).
	 * $func - name function std class
	 *
	 * @param RCLayout $layout
	 * @param string $title
	 * @param array $check
	 * @param array $valCheck
	 * @param null|string $valTitle
	 * @param string $func
	 */
	private function informalAssessmentGroup(
		RCLayout $layout, $title, $check, $valCheck, $valTitle = null, $func = 'speechInformal'
	) {
		if ($valTitle != null) {
			$valTitle = '<i>' . $this->std->$func($valTitle) . '</i>';
		}
		$layout->newLine()
			->addText('<b>' . $title . ':</b> ' . $valTitle)
			->newLine()
			->addObject(
				RCLayout::factory()
					->addObject($this->addCheck($this->std->$func($check[0])), '.width20')
					->addText('Commensurate with age and/or cognitive ability', '.padtop5')
					->newLine()
					->addObject($this->addCheck($this->std->$func($check[1])), '.width20')
					->addText('Relative weakness: <i>' . $this->std->$func($valCheck[0]) . '</i>', '.padtop5')
					->newLine()
					->addObject($this->addCheck($this->std->$func($check[2])), '.width20')
					->addText('Significant weakness: <i>' . $this->std->$func($valCheck[1]) . '</i>', '.padtop5')
				, '.padleft20'
			);
	}

	public function setStd($tsRefID, $iepyear = null) {
		$this->std = new IDEAStudentTXFIE($tsRefID);
	}

} 
