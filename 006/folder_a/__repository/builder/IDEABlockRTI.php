<?php
/**
 * IDEABlockRTI.php
 *
 * Class for creation blocks in RTI builder(State TX).
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 10-02-2014
 */
class IDEABlockRTI extends IDEABlock {

	/**
	 * @var IDEAStudentTXRTI
	 */
	protected $std;

	public function __construct() {
		parent::__construct();
	}

	public function setStd($tsRefID, $iepyear = null) {
		$this->std = new IDEAStudentTXRTI($tsRefID);
	}

	/**
	 * Generate block Cover Page for RC Document
	 */
	public function renderCoverPage() {
		$assRTI  = $this->std->getAssistanceRTI();
		$teacher = $this->std->getCaseManager();
		$layout  = $this->nameOffice(
			'Response to Intervention (RTI)'                         ,
			'SS#: ,' . (string)$this->std->get('stdfedidnmbr')       ,
			'Request for RTI Consultation: ' . $assRTI['meetingtype'],
			$teacher['cmname']
		);

		$layout->newLine('.martop10')
			->addText('I am requesting assistance from the RTI in the area of:');

		$values   = IDEADef::getValidValues('TX_SIT_areas');
		$allVall  = count($values);
		$selected = explode(',', $assRTI['sitareas']);

		for ($i = 0; $i < $allVall; $i++) {
			$this->addYN(
				$layout->newLine($i == 0 ? '.martop10' : null),
				in_array($values[$i]->get(IDEADefValidValue::F_VALUE_ID), $selected) ? 'Y' : ''
			);

			$layout->addText($values[$i]->get(IDEADefValidValue::F_VALUE), '.padtop5');
		}

		$const          = $this->std->getConstruction(86, true);
		$xml            = simplexml_load_string(base64_decode($const['values']));
		$bordBot        = new RCStyle('italic [border-bottom: 1px solid black;]');
		$data['rti_01'] = '';
		$data['rti_02'] = '';
		$data['rti_03'] = '';
		$data['rti_04'] = '';

		foreach ($xml->value as $el) {
			$key        = (string)$el->attributes();
			$data[$key] = (string)$el;
		}

		$layout->newLine('.martop10')
			->addObject(
				RCLayout::factory()
					->addText($data['rti_01'], $bordBot)
					->newLine()
					->addText('Signature of person making request')
					->newLine('.martop10')
					->addText('Received by'  , '[padding-left: 100px; width: 150px;]')
					->addText($data['rti_03'], $bordBot)
				, '[width: 300px; margin-right: 50px;]'
			)
			->addObject(
				RCLayout::factory()
					->addText($data['rti_02'], $bordBot)
					->newLine()
					->addText('Date')
					->newLine('.martop10')
					->addText('Date received', '[padding-left: 100px; width: 160px;]')
					->addText($data['rti_04'], $bordBot)
				, '[width: 250px;]'
			);

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Student Strengths and Weaknesses for RC Document
	 */
	public function renderWeaknesses() {
		$teacher = $this->std->getCaseManager();
		$layout  = $this->nameOffice(
			'Response to Intervention (RTI)'                  ,
			'SS#: ,' . (string)$this->std->get('stdfedidnmbr'),
			'Data Collection and Consultation:'               ,
			$teacher['cmname']
		);

		$layout->newLine()
			->addText('I. Student Strengths and Weaknesses:', $this->titleStyle());

		$structure = $this->prepareDataForWeaknesses();
		$tblWeakns = RCTable::factory('.table')
			->addRow('.row');
		# header
		for ($i = 1; $i < 5; $i++) {
			$tblWeakns->addCell($structure['cols'][$i], $i == 1 ? '.hr' : '.next-hr');
		}
		#rows
		for ($i = 1; $i < 9; $i++) {
			$tblWeakns->addRow('.row')
				->addCell($structure['rows'][$i], '.cellBorder'); // name row
			# values
			for ($j = 1; $j < 4; $j++) {
				$key = $i . '_' . $j;
				$tblWeakns->addCell('<i>' . $structure[$key] . '</i>', '.cellBorder');
			}
		}

		$values    = $this->std->getAssistanceRTI();
		$defStyle  = new RCStyle('[margin-right: 60px; width: 80px;]');
		$lastCheck = new RCStyle('[margin-right: 40px; width: 60px;]');
		$tblSocial = RCLayout::factory()
			->newLine()
			->addText('Social Skills/Behavior', 'center bold [border-bottom: 1px solid black;]')
			->newLine()
			->addText('', '[width: 120px;]')
			->addText('Excellent')
			->addText('Satisfactory')
			->addText('Unsatisfactory')
			->newLine()
			->addText('Peer interactions'   , '.padtop5')
			->addObject($this->addCheck($values['sk_peer'] == 'E' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_peer'] == 'S' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_peer'] == 'U' ? 'Y' : ''), $lastCheck)
			->newLine()
			->addText('Follows instructions', '.padtop5')
			->addObject($this->addCheck($values['sk_foll'] == 'E' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_foll'] == 'S' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_foll'] == 'U' ? 'Y' : ''), $lastCheck)
			->newLine()
			->addText('Stays on task'       , '.padtop5')
			->addObject($this->addCheck($values['sk_stay'] == 'E' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_stay'] == 'S' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_stay'] == 'U' ? 'Y' : ''), $lastCheck)
			->newLine()
			->addText('Teacher interactions', '.padtop5')
			->addObject($this->addCheck($values['sk_inte'] == 'E' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_inte'] == 'S' ? 'Y' : ''), $defStyle)
			->addObject($this->addCheck($values['sk_inte'] == 'U' ? 'Y' : ''), $lastCheck);

		$layout->newLine()
			->addObject($tblWeakns)
			->newLine()
			->addObject($tblSocial , '[border: 1px solid black; width: 350px;]');
		# if exist block before add new page
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Return structure table 'I. Student Strengths and Weaknesses:' with values
	 *
	 * @return array
	 */
	final private function prepareDataForWeaknesses() {
		$table  = array(
			'cols' => array(
				1 => 'Instructional Area'       ,
				2 => 'Instructional/Skill Level',
				3 => 'Strengths'                ,
				4 => 'Needs'
			),
			'rows' => array(
				1 => 'Basic Reading Skills/Reading Comprehension/Functional Reading Skills',
				2 => 'Math Calculation/Math Reasoning Functional Math Skills'              ,
				3 => 'Written Expression/Functional Writing Skills'                        ,
				4 => 'Science'                                                             ,
				5 => 'Social Studies'                                                      ,
				6 => 'Pre-Vocational/Vocational'                                           ,
				7 => 'Developmental/Cognitive'                                             ,
				8 => 'Community Living/Self-Help Skills'                                   ,
			)
		);
		# prepare keys
		for ($i = 1; $i < 9; $i++) {
			for ($j = 1; $j < 4; $j++) {
				$key         = $i . '_' . $j;
				$table[$key] = '';
			}
		}
		# set values from db to table
		$row = $this->std->getConstruction(85, true);
		$val = base64_decode($row['values']);
		$xml = simplexml_load_string($val);
		foreach ($xml->value as $el) {
			$key         = (string)$el->attributes();
			$table[$key] = (string)$el;
		}

		return $table;
	}

	/**
	 * Generate block Health Information for RC Document
	 */
	public function renderHealth() {
		$xmlData = IDEADef::getConstructionTemplate(87);
		$values  = $this->std->getConstruction(87, true);
		$doc     = IDEADocument::factory($xmlData)
			->mergeValues(base64_decode($values['values']));

		$this->rcDoc->addObject($doc->getLayout());
	}

	/**
	 * Generate block Enrollment/Attendance History for RC Document
	 */
	public function renderHistory() {
		$eHistory = $this->std->getEntrollmentHistory();
		$layout   = RCLayout::factory()
			->newLine()
			->addText('II. Health Information:'            , $this->titleStyle())
			->newLine()
			->addText('III. Enrollment/Attendance History:', $this->titleStyle())
			->newLine()
			->addText('<b>A. Enrollment History:</b>');

		$this->addYN($layout->newLine('.martop10'), $eHistory['curently']);
		$layout->addText(
			'Is this student currently enrolled in this district? <i>If no, explain: ' . $eHistory['curently_no'] . '</i>'
			, '.padtop5'
		);

		$this->addYN($layout->newLine('.martop10'), $eHistory['transfer']);
		$layout->addText(
			'Has the student recently transferred into the district? <i>If yes, what is the transfer date? ' . $eHistory['transfer_yes'] . '</i>'
			, '.padtop5'
		);

		$tbl = RCTable::factory('.table')
			->addRow('.row')
			->addCell('Previous Schools Attended', '.hr', 4)
			->addRow('.row')
			->addCell('School'                , '.hr')
			->addCell('District/State/Country', '.next-hr')
			->addCell('Dates of enrollment'   , '.next-hr')
			->addCell('Grade level'           , '.next-hr');

		$rows = $this->std->getPreviousSchools();
		foreach ($rows as $row) {
			$tbl->addRow('.row')
				->addCell('<i>' . $row['school']   . '</i>', '.cellBorder')
				->addCell('<i>' . $row['district'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['dates']    . '</i>', '.cellBorder')
				->addCell('<i>' . $row['grades']   . '</i>', '.cellBorder');
		}

		$aHistory = $this->std->getAttendanceHistory();
		$layout->newLine()
			->addObject($tbl)
			->newLine()
			->addText('<b>B. Attendance History:</b>')
			->newLine('.martop10')
			->addText('This student has been absent <i>' . $aHistory['absent_days'] . '</i> days out of <i>' . $aHistory['school_days'] . '</i> school days this year to date.')
			->newLine()
			->addText('Number of excused absences:  <i>' . $aHistory['abs_good'] . '</i>   Number of unexcused absences:  <i>' . $aHistory['abs_bad'] . '</i>')
			->newLine()
			->addText('Reasons for absences:')
			->newLine()
			->addObject(
				RCLayout::factory()
					->addObject($this->addCheck($aHistory['reason_abs'] == 'I' ? 'Y' : '')  , '.width20')
					->addText(
						'Illness (Number of days <i>'. $aHistory['ill_days'] .'</i> Notes from parents <i>'. $aHistory['ill_recs'] . '</i>)'
						, '.padtop5'
					)
					->newLine()
					->addObject($this->addCheck($aHistory['reason_abs'] == 'S' ? 'Y' : '')  , '.width20')
					->addText('Skipping classes: <i>'. $aHistory['skiping_classes'] . '</i>', '.padtop5')
					->newLine()
					->addObject($this->addCheck($aHistory['reason_abs'] == 'T' ? 'Y' : '')  , '.width20')
					->addText('Truant'                                                      , '.padtop5')
				, '[margin-left: 100px;]'
			);

		$this->addYN($layout->newLine('.martop10'), $aHistory['truancy']);
		$layout->addText(
			'Has a truancy report been filed? <i>If yes, date of report: ' . $aHistory['truancy_yes'] . '</i>'
			, '.padtop5'
		);
		$this->addYN($layout->newLine(), $aHistory['attlack']);
		$layout->addText(
			'Can this student\'s academic/achievement difficulties be attributed to lack of attendance?'
			, '.padtop5'
		);
		$this->addYN($layout->newLine(), $aHistory['attproblem']);
		$layout->addText(
			'Have there been attendance problems in previous school years?  <i>If yes, attach attendance records.</i>'
			, '.padtop5'
		);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Language for RC Document
	 */
	public function renderLanguage() {
		$language = $this->std->getLanguage();
		$layout   = RCLayout::factory()
			->newLine()
			->addText('IV. Language', $this->titleStyle())
			->newLine()
			->addText('Date of Home Language Survey: <i>' . $language['survey'] . '</i> Results: <i>' . $language['resulats'] . '</i>')
			->newLine()
			->addText('Can the student\'s academic/achievement difficulties be attributed to:');

		$this->addYN($layout->newLine('.martop10'), $language['secondlang']);
		$layout->addText('second language learning?', '.padtop5');
		$this->addYN($layout->newLine(), $language['cultural']);
		$layout->addText('cultural differences?'    , '.padtop5');

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Academic Information for RC Document
	 */
	public function renderAcademic() {
		$basisRetnd  = $this->std->getBasisRetained();
		$assessmtns  = $this->std->getFormativeAssessments();
		$taks        = $this->std->getTaks();
		$subjects    = $this->std->getSubjectsAndGrades();
		$tblSubjects = RCTable::factory('[border: 1px solid block; width: 400px;]')
			->addRow('.row')
			->addCell('Subjects and Current Grades*', '.hr', 2)
			->addRow('.row')
			->addCell('Subject'       , '.hr')
			->addCell('Grade or Score', '.next-hr');
		# add rows
		foreach ($subjects as $row) {
			$tblSubjects->addRow('.row')
				->addCell('<i>' . $row['subject'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['score'] . '</i>'  , '.cellBorder');
		}

		$layout = RCLayout::factory()
			->newLine()
			->addText('V. Academic Information:', $this->titleStyle())
			->newLine()
			->addObject($tblSubjects);

		$this->addYN($layout->newLine('.martop10'), $basisRetnd['retained']);
		$layout->addText(
			'Has the student been retained? <i>If yes, when and what was the basis: ' . $basisRetnd['basis'] . '</i>'
			, '.padtop5'
		);

		$tblAssessmnts = RCTable::factory('.table')
			->setCol('100px')
			->setCol('')
			->setCol()
			->setCol('100px')
			->addRow('.row')
			->addCell('Formative Assessment', '.hr', 4)
			->addRow('.row')
			->addCell('Date'        , '.hr')
			->addCell('Name of Test', '.next-hr')
			->addCell('Subject Area', '.next-hr')
			->addCell('Grade/Score' , '.next-hr');

		foreach ($assessmtns as $row) {
			$tblAssessmnts->addRow('.row')
				->addCell('<i>' . $row['date'] . '</i>'    , '.cellBorder')
				->addCell('<i>' . $row['testname'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['subjarea'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['score'] . '</i>'   , '.cellBorder');
		}

		$tblTaks = RCTable::factory('.table')
			->setCol('100px')
			->setCol('')
			->setCol()
			->setCol('100px')
			->addRow('.row')
			->addCell('Texas Assessment of Knowledge and Skills (TAKS)', '.hr', 4)
			->addRow('.row')
			->addCell('Date'              , '.hr')
			->addCell('Subject'           , '.next-hr')
			->addCell('Total Test Mastery', '.next-hr')
			->addCell('Scaled Score'      , '.next-hr');

		foreach ($taks as $row) {
			$tblTaks->addRow('.row')
				->addCell('<i>' . $row['date'] . '</i>'   , '.cellBorder')
				->addCell('<i>' . $row['subject'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['case'] . '</i>'   , '.cellBorder')
				->addCell('<i>' . $row['score'] . '</i>'  , '.cellBorder');
		}

		$layout->newLine('.martop10')
			->addObject($tblAssessmnts)
			->newLine()
			->addObject($tblTaks);

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Documentation/Summary of Core Programs for RC Document
	 */
	public function renderDocumentation() {
		$reading = $this->std->getCoreProgramsBySubject('Reading');
		$math    = $this->std->getCoreProgramsBySubject('Math');
		$layout  = RCLayout::factory()
			->newLine()
			->addText('VI. Documentation/Summary of Core Programs:', $this->titleStyle());

		if (isset($reading[0])) {
			$layout->newLine()
				->addText('<b>A. Core Reading Program</b>');

			$tbl = $this->tableCoreProgram($reading);

			$layout->newLine('.martop10')->addObject($tbl);
		}

		if (isset($math[0])) {
			$layout->newLine()
				->addText('<b>A. Core Math Program</b>');

			$tbl = $this->tableCoreProgram($math);

			$layout->newLine('.martop10')->addObject($tbl);
		}

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate table Core Program
	 *
	 * @param array $data array with data for rows
	 * @return RCLayout
	 */
	final private function tableCoreProgram($data) {
		$progStyle = new RCStyle('center bold [border-bottom: 1px solid black; width: 450px;]');
		$dataStyle = new RCStyle('center bold [border-bottom: 1px solid black; border-left: 1px solid black;]');
		$RLBorder  = new RCStyle('[border: 1px solid black; border-top: none; border-bottom: none;]');
		$title     = new RCStyle('center bold [background: #c0c0c0;]');
		$dateStyle = new RCStyle('italic [border-left: 1px solid black]');
		$itemStyle = new RCStyle('italic [width: 450px;]');
		$lastDate  = new RCStyle('italic [border-bottom: 1px solid black; border-left: 1px solid black]');
		$lastItem  = new RCStyle('italic [border-bottom: 1px solid black; width: 450px;]');
		$allRows   = count($data);
		$tbl       = RCLayout::factory();

		for ($i = 0; $i < $allRows; $i++) {
			if ($i == 0 || $data[$i]['category_name'] != $data[$i - 1]['category_name']) {
				$tbl->newLine('[border: 1px solid black;]')
					->addText($data[$i]['category_name'], $title)
					->newLine($RLBorder)
					->addText('Program'                 , $progStyle)
					->addText('Dates'                   , $dataStyle);
			}

			$tbl->newLine($RLBorder)
				->addText($data[$i]['item_name'], $i == $allRows - 1 ? $lastItem : $itemStyle)
				->addText($data[$i]['date']     , $i == $allRows - 1 ? $lastDate : $dateStyle);
		}

		return $tbl;
	}

	/**
	 * Generate block Speech/Language/Communication for RC Document
	 */
	public function renderSpeech() {
		$speech = $this->std->getSpeech();
		$layout = RCLayout::factory()
			->newLine()
			->addText('VII. Speech/Language/Communication', $this->titleStyle());

		$this->addYN($layout->newLine('.martop10'), $speech['concerns']);
		$layout->addText('There are concerns for speech/language/communication.');

		$desc = new RCStyle('italic [border-left: 1px solid black; padding-top: 5px;]');
		$tbl  = RCTable::factory('.table')
			->setCol('200px')
			->setCol()
			->addRow('.row')
			->addCell('Check area of concern:', '.hr')
			->addCell('Describe'              , '.next-hr')
			->addRow('.row')
			->addCell(
				RCLayout::factory()
					->addObject($this->addCheck($speech['articulation_sw']), '.width20')
					->addText('<i>Articulation</i>'                        , '.padtop5')
			)
			->addCell($speech['articulation'], $desc)
			->addRow('.row')
			->addCell(
				RCLayout::factory()
					->addObject($this->addCheck($speech['fluency_sw'])     , '.width20')
					->addText('<i>Fluency</i>'                             , '.padtop5')
			)
			->addCell($speech['fluency'], $desc)
			->addRow('.row')
			->addCell(
				RCLayout::factory()
					->addObject($this->addCheck($speech['language_sw'])    , '.width20')
					->addText('<i>Language</i>'                            , '.padtop5')
			)
			->addCell($speech['language'], $desc)
			->addRow('.row')
			->addCell(
				RCLayout::factory()
					->addObject($this->addCheck($speech['voice_sw'])       , '.width20')
					->addText('<i>Voice</i>'                               , '.padtop5')
			)
			->addCell($speech['voice'], $desc);

		$layout->newLine('.martop10')->addObject($tbl);
		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Behavior for RC Document
	 */
	public function renderBehavior() {
		$behavior = $this->std->getBehavior();
		$layout   = RCLayout::factory()
			->newLine()
			->addText('VIII. Behavior', $this->titleStyle());
		# get structure tables and print each table
		$data = $this->behaviorStructureTbls();
		$tbl0 = $this->behaviorPrintTbl($data[0]);
		$tbl1 = $this->behaviorPrintTbl($data[1]);
		$tbl2 = $this->behaviorPrintTbl($data[2]);
		$tbl3 = $this->behaviorPrintTbl($data[3]);
		$tbl4 = $this->behaviorPrintTbl($data[4]);

		$layout->newLine('.martop10')
			->addObject($tbl0)
			->newLine('.martop10')
			->addObject($tbl1)
			->newLine('.martop10')
			->addObject($tbl2)
			->newLine('.martop10')
			->addObject($tbl3)
			->newLine('.martop10')
			->addObject($tbl4)
			->newLine('.martop10')
			->addText('<b>B. Behavioral Observations:</b>')
			->newLine()
			->addText(
				'Describe the behavior (and location) for which the teacher is seeking guidance: ' . PHP_EOL .
				'<i>' . $behavior['guidance'] . '</i>'
			)
			->newLine('.martop10')
			->addText('<b>C. Classroom Behavior Management:</b>')
			->newLine()
			->addText(
				'Describe the behavior management techniques used in the classroom and the corresponding response of the student: ' . PHP_EOL .
				'<i>' . $behavior['techniques'] . '</i>'
			)
			->newLine()
			->addText(
				'Which behavior management technique has been the most effective: ' . PHP_EOL .
				'<i>' . $behavior['mosteffective'] . '</i>'
			)
			->newLine()
			->addText(
				'Which behavior management technique has been the least effective:' . PHP_EOL .
				'<i>' . $behavior['leasteffective'] . '</i>'
			);

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Build structure for table. Use in block Behavior.
	 * Method behaviorPrintTbl print table by this element array
	 *
	 * @return array
	 */
	final private function behaviorStructureTbls() {
		$tbl = array(
			array(
				'title' => 'POSITIVE PROACTIVE DISCIPLINE'                 ,
				'cols'  => array('Program', 'Program Description', 'Dates'),
				'keys'  => array('program', 'item_desc'          , 'dates'),
				'rows'  => $this->std->getBehaviorProgByArea(1)
			),
			array(
				'title' => 'CAMPUS RULES'                                                       ,
				'cols'  => array('Rule' , 'Positive Responses', 'Corrective Responses', 'Dates'),
				'keys'  => array('prule', 'responce_pos'      , 'responce_cor'        , 'dates'),
				'rows'  => $this->std->getBehaviorProgByArea(2)
			),
			array(
				'title' => 'GRADE APPROPRIATE CITIZENSHIP SKILLS'          ,
				'cols'  => array('Program', 'Program Description', 'Dates'),
				'keys'  => array('program', 'item_desc'          , 'dates'),
				'rows'  => $this->std->getBehaviorProgByArea(3)
			),
			array(
				'title' => 'SOCIAL SKILLS TRAINING'                                       ,
				'cols'  => array('Program', 'Weekly Skill', 'Role Play/Modeling', 'Dates'),
				'keys'  => array('program', 'weekly'      , 'roleplay'          , 'dates'),
				'rows'  => $this->std->getBehaviorProgByArea(4)
			),
			array(
				'title' => 'ACTIVE SUPERVISION AND MONITORING',
				'cols'  => array('Documentation', 'Dates')    ,
				'keys'  => array('documentation',  'dates')   ,
				'rows'  => $this->std->getBehaviorProgByArea(5)
			),
		);

		return $tbl;
	}

	/**
	 * Return Table for Behavior
	 *
	 * @param array $tbl data for table
	 * @return RCLayout
	 */
	final private function behaviorPrintTbl($tbl) {
		$hr       = new RCStyle('bold center');
		$hrNext   = new RCStyle('bold center [border-left: 1px solid black;]');
		$hrLast   = new RCStyle('bold center [border-left: 1px solid black; width: 150px;]');
		$row      = new RCStyle('[border: 1px solid black; border-top: none;]');
		$cell     = new RCStyle('left italic');
		$nextCell = new RCStyle('left italic [border-left: 1px solid black;]');
		$lastCell = new RCStyle('left italic [border-left: 1px solid black; width: 150px;]');
		$allCols  = count($tbl['cols']);
		$allRows  = count($tbl['rows']);
		$layout   = RCLayout::factory('.table')
			->addText($tbl['title'], 'center bold [background: #c0c0c0; border: 1px solid black;]')
			->newLine($row);
		# add header table
		for ($i = 0; $i < $allCols; $i++) {
			# styles for cols
			if ($i == 0) {
				$style = $hr;
			} elseif ($i == $allCols - 1) {
				$style = $hrLast;
			} else {
				$style = $hrNext;
			}

			$layout->addText($tbl['cols'][$i], $style);
		}
		# add rows with data
		for ($i = 0; $i < $allRows; $i++) {
			$layout->newLine($row);
			for ($j = 0; $j < $allCols; $j++) {
				$key = $tbl['keys'][$j];
				# styles for cell
				if ($j == 0) {
					$style = $cell;
				} elseif ($j == $allCols - 1) {
					$style = $lastCell;
				} else {
					$style = $nextCell;
				}

				$layout->addText((string)$tbl['rows'][$i][$key], $style);
			}
		}

		return $layout;
	}

	/**
	 * Generate block Summary/Additional Comments/Recommendations for RC Document
	 */
	public function renderReccommendations() {
		$data   = $this->std->getSummaryRecommendations('S');
		$layout = RCLayout::factory()
			->newLine()
			->addText('<b>Summary/Additional Comments/Recommendations:</b>');

		foreach ($data as $row) {
			$layout->newLine()
				->addText('<i>' . $row['siaitext'] . '</i>');
		}

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Signatures for RC Document
	 */
	public function renderSignatures() {
		$select = new RCStyle('center italic [border-left: 1px solid black;]');
		$data   = $this->std->getCommetteMembers('S');
		$layout = RCLayout::factory()
			->newLine()
			->addText('Signatures of RTI Committee members:');

		$tbl = RCTable::factory('.table')
			->setCol('200px')
			->setCol()
			->setCol()
			->setCol()
			->addRow('.row')
			->addCell('Signature and Title Members', '.hr')
			->addCell('Position'                   , '.next-hr')
			->addCell('Agree'                      , '.next-hr')
			->addCell('Disagree'                   , '.next-hr');

		foreach ($data as $row) {
			$tbl->addRow('.row')
				->addCell('<i>' . $row['participantname'] . '</i>', '.cellBorder')
				->addCell('<i>' . $row['participantrole'] . '</i>', '.cellBorder')
				->addCell($row['partcat'] == 1 ? 'v' : ''         , $select)
				->addCell($row['partcat'] == 2 ? 'v' : ''         , $select);
		}

		$layout->newLine('.martop10')->addObject($tbl);
		$this->rcDoc->addObject($layout);
	}

} 
