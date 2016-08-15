<?php

	/**
	 * IDEABlockCT.php
	 * Class for creation blocks in CT builder(State CT).
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 26-02-2014
	 */
	class IDEABlockCT extends IDEABlock {

		/**
		 * @var IDEAStudentCT
		 */
		protected $std;

		public function __construct() {
			parent::__construct();
		}

		public function setStd($tsRefID, $iepyear = null) {
			$this->std = new IDEAStudentCT($tsRefID);
			# set data about student to header. Because header use info about student.
			$this->setHeaderDoc();
		}

		public static function defineDefaultStyles() {
			parent::defineDefaultStyles();
			# for small checkboxes
			RCStyle::defineStyleClass('width15', '[width: 15px;]');
			RCStyle::defineStyleClass('font', '[font-size:25px]');
		}

		public function setHeaderDoc() {
			$layout = RCLayout::factory()
				->newLine()
				->addText('Student:', '[width: 5%;] right')
				->addText((string)$this->std->get('stdname'), 'italic center [border-bottom: 1px solid black; width: 15%;]')
				->addText('DOB:', '[margin-left: 10px; width: 10%;] right')
				->addText((string)$this->std->get('stddob'), 'italic center [border-bottom: 1px solid black; width: 15%;]')
				->addText('District:', '[margin-left: 10px; width: 10%;] right')
				->addText((string)SystemCore::$VndName, 'center italic [border-bottom: 1px solid black; width: 15%;]')
				->addText('Meeting Date:', '[margin-left: 10px; width: 10%;] right')
				->addText((string)$this->std->get('stdiepmeetingdt'), 'italic center [border-bottom: 1px solid black; width: 15%;]');

			$this->rcDoc->setPageHeader($layout);
		}

		private function setIEPPageFooter($leftText = 'February 2009a') {
			$this->rcDoc->setPageFooter(
				RCLayout::factory()
					->addText('ED620, Revised ' . $leftText, '[width: 230px;]')
					->addText('INDIVIDUALIZED EDUCATION PROGRAM', 'center')
					->addText('[PN]', '[width: 230px;] right')
				, '[font-size: 6px;]'
			);
		}

		/**
		 * Generate block Student Demographics for IEP doc
		 */
		public function renderStdDemographics() {

			$this->rcDoc->startNewPage(false, null, false, 1);

			$haveOwnSchool = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'ct_iep',
				'cover_page_high_school',
				$this->std->get('stdiepyear')
			);

			$hscredits = IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid'),
				'ct_iep',
				'hscredits',
				$this->std->get('stdiepyear')
			);
			$nextYearData = $this->std->getNextYearData();
			$layout = RCLayout::factory()
				->newLine()
				->addText('PLANNING AND PLACEMENT TEAM (PPT) COVER PAGE', 'bold center')
				->newLine()
				->addText('Current Enrolled School:', '[padding-top: 5px; width: 12%;]')
				->addText(
					(string)$this->std->get('vouname'),
					'italic [border-bottom: 1px solid black; padding-top: 5px; width: 24%;]'
				)
				->addText('Age:', '[padding-top: 5px; width: 4%;]')
				->addText(
					(string)$this->std->get('stdage'),
					'italic center [border-bottom: 1px solid black; padding-top: 5px; width: 4%;]'
				)
				->addText('Current Grade:', '[padding-top: 5px; width: 8%;]')
				->addText(
					(string)$this->std->get('grdlevel'),
					'italic center [border-bottom: 1px solid black; padding-top: 5px; width: 4%;]'
				)
				->addText('H.S. Credits', '[padding-top: 5px; width: 7%;]')
				->addText(
					(string)$hscredits,
					'italic center [border-bottom: 1px solid black; padding-top: 5px; width: 4%;]'
				)
				->addText('Grade Next Yr:', '[padding-top: 5px; width: 8%;]')
				->addText(
					(string)$nextYearData['grade_next_yr'],
					'italic center [border-bottom: 1px solid black; padding-top: 5px; width: 4%;]'
				)
				->addText('Gender:', '[padding-top: 5px; width: 5%;]')
				->addObject(
					$this->addCheck($this->std->get('stdsex') == 'Female' ? 'Y' : ''),
					'.checker [padding-top: 5px;]'
				)
				->addText('Female', '[padding-top: 5px; width:5%;]')
				->addObject(
					$this->addCheck($this->std->get('stdsex') == 'Male' ? 'Y' : ''),
					'.checker [padding-top: 5px;]'
				)
				->addText('Male', '[padding-top: 5px; width: 3%;]')
				->newLine()
				->addText('Current Home School:', '[width: 11%;]')
				->addText((string)$this->std->get('vouname_res'), 'italic [border-bottom: 1px solid black; width: 20%;]')
				->addText('School Next Year:', '[margin-left: 10px; width: 11%;]')
				->addText((string)$nextYearData['next_school_year'], 'italic [border-bottom: 1px solid black; width: 19%;]')
				->addText('Home School Next Year:', '[margin-left: 10px; width: 14%;]')
				->addText((string)
				$nextYearData['home_next_school_year'],
					'italic [border-bottom: 1px solid black; black; width: 20%;]'
				)
				->newLine()
				->addText('SASID #:', '[width: 5%;]')
				->addText('' . $this->std->get('stdstateidnmbr') . '', 'italic [border-bottom: 1px solid black; width: 36%;]')
				->addText(
					'If your school district does not have its own high school, is the student attending his/her designated high school?'
					, '[margin-left: 20px; width: 65%;]'
				)
				->newLine()
				->addText('Case Manager:', '[padding-top: 5px; width: 8%;]')
				->addText(
					(string)$this->std->get('cmname'),
					'italic [border-bottom: 1px solid black; padding-top: 5px; width: 37%;]'
				)
				->addObject(
					$this->addCheck($haveOwnSchool == 'Y' ? 'Y' : ''),
					'.checker [padding-top: 5px;]'
				)
				->addText('Yes', '[padding-top: 5px; width: 5%;]')
				->addObject(
					$this->addCheck($haveOwnSchool == 'N' ? 'Y' : ''),
					'.checker [padding-top: 5px;]'
				)
				->addText('No', '[padding-top: 5px; width: 5%;]')
				->addObject(
					$this->addCheck($haveOwnSchool == 'A' ? 'Y' : ''),
					'.checker [padding-top: 5px;]'
				)
				->addText('N\A', '[padding-top: 5px; width: 5%;]')
				->newLine()
				->addText('Student Address:<sup>1</sup>', '[padding-top: 5px; width: 9%;]')
				->addText(
					(string)$this->std->get('stdaddress'),
					'italic [border-bottom: 1px solid black; padding-top: 5px; width: 36%;]'
				)
				->addText('Student Instructional Lang:', '[padding: 5px 0px 0px 5px; width: 14%;]')
				->addObject(
					$this->addCheck($this->std->get('prim_lang') == 'English' ? 'Y' : ''),
					'.checker [padding-top: 5px;]'
				)
				->addText('English', '[padding-top: 5px; width: 6%;]')
				->addObject(
					$this->addCheck(
					# if Student have not English language add checked
						$this->std->get('prim_lang') != 'English' && $this->std->get('prim_lang') != '' ? 'Y' : ''
					), '.checker [padding-top: 5px;]'
				)
				->addText('Other: (specify)', '[padding-top: 5px; width: 8%;]')
				->addText(
				# if Student have not English language add name language
					$this->std->get('prim_lang') != 'English' &&
					$this->std->get('prim_lang') != '' ? '' . $this->std->get('prim_lang') . '' : '',
					'[border-bottom: 1px solid black; padding-top: 5px; width: 18%;]'
				)
				->newLine()
				->addText('Student Home Phone:', '[width: 11%;]')
				->addText('' . $this->std->get('stdhphn') . '', 'italic [border-bottom: 1px solid black; width: 34%;]');

			$parents = $this->std->getGuardians();

			foreach ($parents as $parent) {
				$layout->newLine()
					->addText('Parent/Guardian (Name):', '[width: 13%;]')
					->addText((string)$parent['gdfnm'] . ' ' . $parent['gdlnm'], 'italic [border-bottom: 1px solid black; width: 32%;]')
					->newLine()
					->addText('Parent/Guardian (Address):', '[padding-top: 5px; width: 14%;]')
					->addObject(
						$this->addCheck(
							$parent['gdadr1'] == $this->std->get('stdhadr1') ? 'Y' : ''
						),
						'.checker [padding-top: 5px;]'
					)
					->addText('Same', '[padding-top:5px; width: 4%;]')
					->addText((string)$parent['gdadr1'] . ', ' . $parent['gdcity'] . ', ' . $parent['gdstate'] . ' ' . $parent['gdcitycode'], 'italic [border-bottom: 1px solid black; padding-top: 5px; width: 25%;]')
					->addText('Parent Home Phone:', '[padding-top:5px; width: 10.5%;]')
					->addText((string)$parent['gdhphn'], 'italic [padding-top:5px; border-bottom: 1px solid black; width: 14.5%;]')
					->addText('Parent Work Phone:', '[padding-top:5px; width: 10%;]')
					->addText((string)$parent['gdwphn'], 'italic [padding-top:5px; border-bottom: 1px solid black; width: 15%;]');
			}

			$layout
				->newLine()
				->addText('Most Recent Annual Review Date:', '[width: 17%;]')
				->addText(
					(string)$this->std->get('stdenrolldt'),
					'center italic [border-bottom: 1px solid black; width: 10%]'
				)
				->addText('Next Annual Review Date:', '[width: 13%]')
				->addText(
					(string)$this->std->get('stdcmpltdt'),
					'center italic [border-bottom: 1px solid black;  width: 10%]'
				)
				->addText('Most Recent Eval. Date:', '[width: 12%]')
				->addText(
					(string)$this->std->get('stdevaldt'),
					'center italic [border-bottom: 1px solid black; width: 10%]'
				)
				->addText('Next Reevaluation Date:', '[width: 12%]')
				->addText(
					(string)$this->std->get('stdtriennialdt'),
					'center italic [border-bottom: 1px solid black; width: 11%;]'
				)
				->newLine();

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Reason for Meeting for IEP doc
		 */
		public function renderReasonForMeeting() {

			$checkBoxes = db::execSQL("
			SELECT siepcprefid,
				   siepcpdesc
			  FROM webset.statedef_iepconfpurpose
		     WHERE screfid = " . VNDState::factory()->id . "
			 ORDER BY siep_seq, siepcprefid
			")->assocAll();

			$selected = db::execSQL("
	            SELECT siepcprefid
	              FROM webset.std_in_iepconfpurpose
	             WHERE stdrefid = " . $this->std->get('tsrefid') . "
            ")->indexCol(0);
			# layout for checkboxes and main layout
			$subLayout = new RCLayout();
			$layout = new RCLayout();
			$allBoxes = count($checkBoxes);

			for ($i = 0; $i < $allBoxes; $i++) {
				if (($i + 6) % 6 == 0 || $i == 0) $subLayout->newLine();

				$subLayout->addObject(
					$this->addCheck(in_array($checkBoxes[$i]['siepcprefid'], $selected) ? 'Y' : ''),
					'.checker'
				);

				$subLayout->addText((string)$checkBoxes[$i]['siepcpdesc'], '[width: 14%;]');
			}

			$layout->newLine()
				->addText('Reason for Meeting:<sup>2</sup>', 'bold [width: 12%;]')
				->addObject($subLayout);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Primary  Disability for IEP doc
		 */
		public function renderPrimaryDisability() {

			$info = $this->std->getDisability(true);

			/**
			 * Sorts array by disability key
			 */
			usort($info, create_function('$a,$b', 'return strcmp($a["disability"],$b["disability"]);'));

			$subLayout = new RCLayout();
			$layout = new RCLayout();
			$all = count($info);

			for ($i = 0; $i < $all; $i++) {

				if (($i + 6) % 6 == 0 || $i == 0) $subLayout->newLine();

				$subLayout->addObject(
					$this->addCheck($info[$i]['dcrefid'] ? 'Y' : ''),
					'.checker'
				);

				$subLayout->addText((string)$info[$i]['disability'], '[width: 14%;]');
			}

			$layout->newLine()
				->addText('Primary Disability:', 'bold [width: 12%; margin-top:-9px;]')
				->newLine()
				->addObject($subLayout);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Team Member Present for IEP doc
		 */
		public function renderTeamMember() {

			$layout = new RCLayout();
			$nextData = $this->std->getNextYearData();
			$tbl = RCLayout::factory()
				->addText('The next projected PPT meeting date is:', 'bold [width: 170px;]')
				->addText(
					$nextData['trs_iepmeetingdt'] ? CoreUtils::formatDateForUser($nextData['trs_iepmeetingdt']) : '',
					'italic [border-bottom: 1px solid black; width: 100px;]'
				)
				->newLine()
				->addText('Eligible as a student in need of Special Education (The child is evaluated as having a disability, and needs special education and related services)', '[width: 70%;]')
				->addObject($this->addCheck($nextData['ks_cur_iep']), '.checker')
				->addText('Yes', '[width: 5%;]')
				->addObject($this->addCheck($nextData['ks_cur_iep'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No');

			$tbl->newLine()
				->addText('Is this an amendment to a current IEP using Form ED634?', '[padding-top: 5px; width: 30%;]')
				->addObject($this->addCheck($nextData['ks_trs_iep']), '.checker')
				->addText(
					'YES, attached is the ED634 and amendments (revised IEP pages 1, 2, 3) and other supporting IEP documents'
					, '[padding-top: 5px; width: 55%;]'
				)
				->addObject($this->addCheck($nextData['ks_trs_iep'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No')
				->newLine()
				->addText('If YES, what is the date of the IEP being amended?', '[padding-top: 5px; width: 190px;]')
				->addText(
					$nextData['amendment'] ? CoreUtils::formatDateForUser($nextData['amendment']) : '',
					'italic [border-bottom: 1px solid black; width: 80px;]'
				)
				->newLine('[border-top: 1px solid black; margin-top: 5px;]')
				->addText('Team Member Present (required)', 'bold center');

			$members = $this->std->getCommetteMembers();
			$allMemb = count($members);
			$colNumb = $allMemb / 3;
			$tbl1 = RCLayout::factory();
			$tbl2 = RCLayout::factory();
			$tbl3 = RCLayout::factory();

			for ($i = 0; $i < $allMemb; $i++) {
				if ($i < $colNumb) {
					$tbl1
						->newLine()
						->addObject(
							RCLayout::factory()
								->addText((string)$members[$i]['pdesc'] . ': ', '[width: 40%;] right')
								->addText((string)$members[$i]['participantname'], '[border-bottom: 1px solid black; width: 60%;] italic')
						);
				} elseif ($i < ($colNumb * 2)) {
					$tbl2
						->newLine()
						->addObject(
							RCLayout::factory()
								->addText((string)$members[$i]['pdesc'] . ': ', '[width: 40%;] right')
								->addText((string)$members[$i]['participantname'], '[border-bottom: 1px solid black; width: 60%;] italic')
						);
				} elseif ($i >= ($colNumb * 2)) {
					$tbl3
						->newLine()
						->addObject(
							RCLayout::factory()
								->addText((string)$members[$i]['pdesc'] . ': ', '[width: 40%;] right')
								->addText((string)$members[$i]['participantname'], '[border-bottom: 1px solid black; width: 60%;] italic')
						);
				}
			}

			$tbl->newLine()
				->addObject($tbl1, '[width 33%;]')
				->addObject($tbl2, '[width 33%;]')
				->addObject($tbl3, '[width 34%;]');

			$layout->newLine()
				->addObject($tbl, '[border: 1px solid black;]')
				->newLine()
				->addText('Address of student\'s primary residence. May choose more than one', 'right [font-size: 7px;]');

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Recommendations and Planning for IEP doc
		 */
		public function renderRecommendations() {

			$this->setIEPPageFooter('October 2014');
			$this->rcDoc->startNewPage(false, null, false, 2);

			$layout = new RCLayout();
			$tblRecmnd = new RCLayout();
			$tblPlnng = new RCLayout();

			$this->std->getRecAndPlanning();
			$tblRecmnd->newLine()
				->addText((string)$this->std->getRecommendations(), 'italic [padding-left: 5px; padding-top: 0px; padding-bottom: 10px;]');

			$tblPlnng->newLine()
				->addText((string)$this->std->getPlanning(), 'italic [padding-left: 5px; padding-top: 0px;]');

			$tblPlnng->newLine()
				->addObject(
					RCLayout::factory()
						->addText('<b>Parents please note:</b> Effective October 1, 2009, parents must be provided with a copy of the state developed Parental Notification of the Laws Relating to Physical Restraint and Seclusion in the Public Schools (http://www.sde.ct.gov/sde/cwp/view.asp?a=2678&Q=320730#Legal) at the first PPT meeting following a child\'s initial referral for special education. In addition, the notice must also be provided to parents at the first PPT meeting where the use of seclusion as a behavior intervention is included in a child\'s IEP.')
						->newLine()
						->addObject($this->addCheck($this->std->getParentNotiffy() ? $this->std->getParentNotiffy() : ''), '.checker')
						->addText(
							'A copy of the <i>Parental Notification of the Laws Relating to Physical Restraint and Seclusion in the Public Schools</i> has been provided to the parents on</i>'
							, '[padding-top: 5px;] 70%'
						)
						->addText((string)$this->std->getParentDate(), 'italic [border-bottom: 1px solid black; width: 70px;] center')
						->addText('(date)')
				);

			$layout->newLine()
				->addText('LIST OF PPT RECOMMENDATIONS', 'bold center')
				->newLine('.martop10')
				->addObject($tblRecmnd, '[border: 1px solid black; padding-top: 10px;]')
				->newLine('.martop10')
				->addText('PLANNING AND PLACEMENT TEAM MEETING SUMMARY (OPTIONAL)', 'bold center')
				->newLine('.martop10')
				->addObject($tblPlnng, '[border: 1px solid black; padding-top: 10px;]');

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Prior Written Notice for IEP doc
		 */
		public function renderWrittenNotice() {

			$this->setIEPPageFooter('March 2013');
			$this->rcDoc->startNewPage(false, null, false, 3);

			$xmlData = IDEADef::getConstructionTemplate(161);
			$values = $this->std->getConstruction(161, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}

			$this->rcDoc->addObject($doc->getLayout());
		}

		/**
		 * Build Layout with checkboxes and labels by area
		 *
		 * @param string $area
		 * @param null $data
		 * @return RCLayout
		 */
		final private function buildLayoutWithLabels($area, $data = null) {
			$layout = new RCLayout();
			$values = IDEADef::getValidValues($area);

			foreach ($values as $val) {
				/** @var IDEADefValidValue $val */
				$layout->newLine()
					->addObject($this->addCheck(''), '.checker')
					->addText((string)$val->get(IDEADefValidValue::F_VALUE));
			}

			return $layout;
		}

		/**
		 * Generate block PLAAFP for IEP doc
		 */
		public function renderPLAAFP() {

			$this->setIEPPageFooter('December 2013');
			$this->rcDoc->startNewPage(false, null, false, 4);

			$layout = RCLayout::factory()
				->newLine()
				->addText(
					'PRESENT LEVELS OF ACADEMIC ACHIEVEMENT AND FUNCTIONAL PERFORMANCE' . PHP_EOL . '(The following information was derived from: report data, documentation from classroom performance, observations, parent/student reports, and curriculum based and standardized assessments, including Smarter Balanced and CT Alternate Assessments results and student samples).'
					, 'bold center'
				);

			$data = $this->std->getPlaafpData();
			$tblParentStudent = RCLayout::factory()
				->newLine()
				->addText('Parent and Student Input and Concerns', 'bold center [border: 1px solid black;]')
				->newLine()
				->addText('' . $data['general'] . '', 'italic [border: 1px solid black; border-top: none;]');

			$layout->newLine()
				->addObject($tblParentStudent);

			$this->rcDoc->addObject($layout);

			$this->setIEPPageFooter();
			$this->rcDoc->startNewPage(false, null, false, 5);

			$layout = RCLayout::factory()
				->newLine()
				->addText(
					'PRESENT LEVELS OF ACADEMIC ACHIEVEMENT AND FUNCTIONAL PERFORMANCE'
					, 'bold center'
				);

			$tblAcademic = RCLayout::factory()
				->newLine('[border-bottom: 1px solid black;]')
				->addText('Area' . PHP_EOL . '(briefly describe current performance)', 'center')
				->addText('Strengths' . PHP_EOL . '(include data as appropriate)', 'center')
				->addText('Concerns/Needs' . PHP_EOL . '(requiring specialized instruction', 'center')
				->addText(
					'Impact of student\'s disability on involvement and progress in the general education curriculum or appropriate preschool activities.'
					, 'center'
				);

			$allRows = count($data['areas']);

			for ($i = 0; $i < $allRows; $i++) {
				$tblAcademic->newLine('[border: 1px solid black; border-top: none;]')
					->addText('<b>' . (string)$data['areas'][$i]['tsndesc'] . '</b>' . PHP_EOL . '<i>' . (string)$data['areas'][$i]['pglpnarrative'] . '</i>')
					->addText('<i>' . (string)$data['areas'][$i]['strengths'] . '</i>', '.cellBorder')
					->addText('<i>' . (string)$data['areas'][$i]['concerns'] . '</i>', '.cellBorder')
					->addText('<i>' . (string)$data['areas'][$i]['impact'] . '</i>', '.cellBorder');
			}

			$layout->newLine('.martop10')
				->addObject($tblAcademic);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Return array with selected checkboxes. Often use.
		 *
		 * @param string $area
		 * @param array $data
		 * @return array
		 */
		public function getSelectedValuesByArea($area, $data) {
			$values = IDEADef::getValidValues($area);
			$allData = count($values);
			$checkboxes = array();

			for ($i = 0; $i < $allData; $i++) {
				$checkboxes[$i] = '';

				if (in_array($values[$i]->get(IDEADefValidValue::F_REFID), $data)) {
					$checkboxes[$i] = 'Y';
				}
			}

			return $checkboxes;
		}

		/**
		 * Generate block Transition Planning for IEP doc
		 */
		public function renderTransitionPlanning() {
			
			$this->setIEPPageFooter();
			$this->rcDoc->startNewPage(false, null, false, 6);

			$values = $this->std->getTransition();
			$selectedPreferences = explode(',', $values['interests']);
			$selectedRights = explode(',', $values['rights']);
			$preferencesCheckbox = $this->getSelectedValuesByArea('CT_Transition_Interest', $selectedPreferences);
			$rightsCheckbox = $this->getSelectedValuesByArea('CT_Transition_Rights', $selectedRights);
			$layout = RCLayout::factory()
				->newLine()
				->addText('TRANSITION PLANNING', 'bold center')
				->newLine()
				->addText('<b>1.</b>', '.width15')
				->addObject($this->addCheck($values['stdage'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>Not Applicable: Student has not reached the age of 15 and transition planning is not required or appropriate at this time.</b>')
				->newLine()
				->addText('', '.width15')
				->addObject($this->addCheck($values['stdage'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>This is either the first IEP to be in effect when the student turns 16 (or younger if appropriate and transition planning is needed) or the student is 16 or older and transition planning is required.</b>')
				->newLine()
				->addText('<b>2.</b>', '.width15')
				->addText('<b>Student Preferences/Interests - document the following:</b>')
				->newLine()
				->addText(
					'a) Was the student invited to attend her/his Planning and Placement Team (PPT) meeting?',
					'[width: 350px;]'
				)
				->addObject($this->addCheck($values['invited'] == 'Y' ? 'Y' : 'N'), '.checker')
				->addText('Yes', '.width20')
				->addObject($this->addCheck($values['invited'] == 'N' ? 'Y' : 'N'), '.checker')
				->addText('No', '.width20')
				->newLine()
				->addText('b) Did the student attend?', '[width: 350px;]')
				->addObject($this->addCheck($values['attended'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addObject($this->addCheck($values['attended'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No', '.width20')
				->newLine()
				->addText('c) How were the student\'s preferences/interests, as they relate to planning for transition services, determined?')
				->newLine()
				->addObject($this->addCheck($preferencesCheckbox[0]), '.checker')
				->addText('Personal Interviews', '[width: 80px;]')
				->addObject($this->addCheck($preferencesCheckbox[1]), '.checker')
				->addText('Comments at Meeting', '[width: 100px;]')
				->addObject($this->addCheck($preferencesCheckbox[2]), '.checker')
				->addText('Functional Vocational Evaluations', '[width: 150px;]')
				->addObject($this->addCheck($preferencesCheckbox[3]), '.checker')
				->addText('Age appropriate transition assessments', '[width: 150px;]')
				->newLine()
				->addObject($this->addCheck($preferencesCheckbox[4]), '.checker')
				->addText('Other:', '[width: 40px;]')
				->addText(
					$preferencesCheckbox[4] == 'Y' ? $values['interests_other'] : '',
					'italic [border-bottom: 1x solid black; width: 70px;]'
				)
				->newLine()
				->addText('d) Summarize student preferences/interests as they relate to planning for transition services:')
				->newLine()
				->addText((string)$values['interests_summary'], 'italic [border-bottom: 1x solid black; padding-top: 5px; width: 570px;]')
				->newLine()
				->addText('<b>3.</b>', '.width15')
				->addText('<b>Age Appropriate Transition Assessment(s) performed: (Specify assessment(s) and dates administered) </b>')
				->newLine()
				->addText((string)$values['assessments'], 'italic [border-bottom: 1px solid black; width: 570px;]')
				->newLine()
				->addText('<b>4.</b>', '.width15')
				->addText('<b>Agency Participation:</b>')
				->newLine()
				->addText('a) Were any outside agencies invited to attend the PPT meeting?', '[width: 250px;]')
				->newLine()
				->addObject($this->addCheck($values['agencies_invited'] == 967 ? 'Y' : ''), '.checker')
				->addText('Yes, with written consent', '[width: 120px;]')
				->addObject($this->addCheck($values['agencies_invited'] == 968 ? 'Y' : ''), '.checker')
				->addText('No (If No, MUST specify reason as listed in the IEP Manual)', '[width: 230px;]')
				->addText(
					$values['agencies_invited'] == 968 ? $values['agencies_invited_other'] : '',
					'italic [border-bottom: 1px solid black; width: 50px;]'
				)
				->newLine()
				->addText('b) If yes, did the agency\'s representative attend?', '[width: 300px;]')
				->addObject($this->addCheck($values['agencies_attended'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addObject($this->addCheck($values['agencies_attended'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No', '.width20')
				->newLine()
				->addText(
					'c) Has any participating agency agreed to provide or pay for services/linkages?',
					'[width: 300px;]')
				->addObject($this->addCheck($values['agency_agreed'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addObject($this->addCheck($values['agency_agreed'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No (If Yes, specify)', '[width: 80px;]')
				->addText(
					$values['agency_agreed'] == 'Y' ? $values['agency_agreed_other'] : '',
					'italic [border-bottom: 1px solid black; width: 50px;]'
				)
				->newLine()
				->addText('<b>5.</b>', '.width15')
				->addText('<b>Post-School Outcome Goal Statement(s) and Transition Services recommended in this IEP</b>')
				->newLine()
				->addText('a) <b>Post-School Outcome Goal Statement - Postsecondary Education or Training:</b>')
				->newLine()
				->addText((string)$values['post_school_edu'], 'italic [border-bottom: 1px solid black; width: 570px;]')
				->newLine()
				->addObject($this->addCheck($values['post_school_edu_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Annual goal(s) and related objectives regarding Postsecondary Education or Training have been developed and are included in this IEP')
				->newLine()
				->addText('b) <b>Post-School Outcome Goal Statement - Employment:</b>')
				->newLine()
				->addText((string)$values['post_school_emp'], 'italic [border-bottom: 1px solid black; width: 570px;]')
				->newLine()
				->addObject($this->addCheck($values['post_school_emp_sw'] == 'Y' ? 'Y' : 'N'), '.checker')
				->addText('Annual goal(s) and related objectives regarding Employment have been developed and are included in this IEP')
				->newLine()
				->addText('c) <b>Post-School Outcome Goal Statement - Independent Living Skills (if appropriate):</b>')
				->newLine()
				->addText((string)$values['post_school_liv'], 'italic [border-bottom: 1px solid black; width: 570px;]')
				->newLine()
				->addObject($this->addCheck($values['post_school_liv_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Annual goals and related objectives regarding Independent Living have been developed and are included in this IEP (may include Community Participation)')
				->newLine()
				->addText('<b>6.</b>', '.width15')
				->addText('<b>Please select ONLY one:</b>')
				->newLine()
				->addObject($this->addCheck($values['course_study'] == 1 ? 'Y' : ''), '.checker')
				->addText('<b>The course of study</b> needed to assist the child in reaching the transition goals and related objectives <b>will include</b> (including general education activities): ' . (strlen($values['course_other']) > 0 ? PHP_EOL : '') . '<i>' . (string)$values['course_other'] . '</i>')
				->newLine()
				->addObject($this->addCheck($values['course_study'] == 2 ? 'Y' : ''), '.checker')
				->addText('<b>Student has completed academic requirements</b> no academic course of study is required - student\'s IEP includes <b>only</b> transition goals and services.')
				->newLine()
				->addText('<b>7.</b>', '.width15')
				->addText('<b>At least one year prior to reaching the age of 18, the student must be informed of her/his rights under IDEA which will transfer at age 18.</b>')
				->newLine()
				->addObject($this->addCheck($rightsCheckbox[0] == 'Y' ? 'Y' : ''), '.checker')
				->addText('NA (Student will not be 17 within one year)', '[width: 180px;]')
				->addObject($this->addCheck($rightsCheckbox[1] == 'Y' ? 'Y' : ''), '.checker')
				->addText('The student has been informed of her/his rights under IDEA which will transfer at age 18', '[width: 280px;]')
				->addObject($this->addCheck($rightsCheckbox[2] == 'Y' ? 'Y' : ''), '.checker')
				->addText('No IDEA rights will transfer')
				->newLine()
				->addText('<b>8.</b>', '.width15')
				->addText('<b>For a child whose eligibility under special education will terminate the following year due to graduation with a regular education diploma or due</b>')
				->newLine()
				->addText(
					'<b>to exceeding the age of eligibility the Summary of Performance will be completed on or before: (specify date)</b>'
					, '[width: 420px;]'
				)
				->addText((string)$values['sop_date'], 'italic [border-bottom: 1px solid black; width 30px;]')
				->newLine()
				->addText(
					'Parents please note: Rights afforded to parents under the Individuals with Disabilities Education Act (IDEA) transfer to students at the age of 18, unless legal guardianship has been obtained.'
					, 'bold [border: 1x solid black; margin-top: 10px; padding: 5px;]'
				);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Annual Goals for IEP doc
		 */
		public function renderAnnualGoals() {
			$data = $this->std->getAnnualGoals();

			$this->setIEPPageFooter();
			$this->rcDoc->startNewPage(false, null, true, 7);

			foreach ($data[0] as $info) {
				$layout = RCLayout::factory();
				if (!empty($info)) {
					$layout
						->newLine()
						->addText('<b>Area: </b><i>' . $info['subject'] . '</i>');
					$layout_goal = RCLayout::factory()
						->newLine()
						->addText('<b>Measurable Annual Goal* (Linked to Present Levels of Performance)</b> <i>#' . $info['bl_num'] . '.' . $info['g_num'] . '</i>')
						->newLine()
						->addText($info['gsentance'] ? '<b>' . (string)$info['gsentance'] . '</b>' : '', 'italic')
						->newLine()
						->addText($info['progress'] == 'Other' ? '<b>Eval. Procedure:  </b><i>' . $info['progress'] . ': ' . $info['eval_oth'] : '<b>Eval. Procedure:  </b><i>' . $info['progress'] . '</i>')
						->newLine()
						->addText($info['criteria'] == 'Other' ? '<b>Perf. Criteria:  </b><i>' . $info['criteria'] . ': ' . $info['crit_oth'] : '<b>Perf. Criteria:  </b><i>' . $info['criteria'] . '</i>')
						->newLine()
						->addText('<b>(%, Trials, etc.)  </b><i>' . $info['trial'] . '</i>');

					if (isset($data[1][$info['grefid']])) {
						$tbl = RCTable::factory('.table')
							->addTitle('Enter Dates for Evaluating and Reporting Progress in Boxes Below', '[font-size: 8]')
							->addRow('.row');
						foreach ($data[1][$info['grefid']] as $period) {
							$tbl
								->addCell($period['bmnum'], 'center [border-left: 1px solid black;]');
						}
						$tbl->addRow('.row');
						foreach ($data[1][$info['grefid']] as $period) {
							$tbl
								->addCell($period['value'], 'center [border-left: 1px solid black;]');
						}

						$layout
							->newLine()
							->addObject($layout_goal, '[width: 65%]')
							->addObject($tbl, '[width: 35%]')
							->newLine()
							->addText('Short Term Objectives/Benchmarks (Linked to achieving progress towards Annual Goal)');
					}
					foreach ($info['objectives'] as $obj) {
						$layout_obj = RCLayout::factory()
							->newLine()
							->addText('<b>Objective</b> <i>#' . $info['bl_num'] . '.' . $obj['b_num_goal'] . '</i>')
							->newLine()
							->addText('<b>' . (string)$obj['bsentance'] . '</b>', 'italic')
							->newLine()
							->addText($obj['bprogress'] == 'Other' ? '<b>Eval. Procedure:  </b><i>' . $obj['bprogress'] . ': ' . $obj['beval_oth'] : '<b>Eval. Procedure:  </b><i>' . $obj['bprogress'] . '</i>')
							->newLine()
							->addText($obj['bcriteria'] == 'Other' ? '<b>Perf. Criteria:  </b><i>' . $obj['bcriteria'] . ': ' . $obj['bcrit_oth'] : '<b>Perf. Criteria:  </b><i>' . $obj['bcriteria'] . '</i>')
							->newLine()
							->addText('<b>(%, Trials, etc.)  </b><i>' . $obj['btrial'] . '</i>');
						if (isset($data[1][$obj['orefid']])) {
							$tbl2 = RCTable::factory('.table')
								->addTitle('Enter Dates for Evaluating and Reporting Progress in Boxes Below', '[font-size: 8]')
								->addRow('.row');
							if (!empty($obj)) {
								foreach ($data[1][$obj['orefid']] as $period) {
									$tbl2
										->addCell($period['bmnum'], 'center [border-left: 1px solid black;]');
								}
								$tbl2->addRow('.row');
								foreach ($data[1][$obj['orefid']] as $period) {
									$tbl2
										->addCell($period['value'], 'center [border-left: 1px solid black;]');
								}

								$layout
									->newLine()
									->addObject($layout_obj, '[padding-left:10px;width:65%]')
									->addObject($tbl2, '[width: 35%]');
							}
						}
					}
				}
				$this->rcDoc->newLine()->addObject($layout);
			}
		}

		/**
		 * Generate block Program Accommodations and Modifications for IEP doc
		 */
		public function renderProgramAccommodations() {

			$this->setIEPPageFooter();
			$this->rcDoc->startNewPage(false, null, false, 8);

			$data = $this->std->getAccommodations();

			$layout = RCLayout::factory()
				->newLine()
				->addText(
					'Program Accommodations and Modifications - INCLUDING NONACADEMIC AND EXTRACURRICULAR ACTIVITIES/COLLABORATION/SUPPORT FOR SCHOOL PERSONNEL'
					, 'bold center [font-size: 8px;]'
				);

			$subItem = new RCStyle('[font-size: 8px; margin-left: 80px;]');

			$tbl = RCTable::factory('.table')
				->border(1)
				->addCell(RCLayout::factory()
					->addText('<b>Accommodations and Modifications to be provided to enable the child:</b>')
					->newLine()
					->addText('- To advance appropriately toward attaining his/her annual goals;', $subItem)
					->newLine()
					->addText('- To be involved in and make progress in the general education curriculum;', $subItem)
					->newLine()
					->addText('- To participate in extracurricular and other non-academic activities, and', $subItem)
					->newLine()
					->addText(
						'- To be educated and participate with other children with and without disabilities.'
						, $subItem
					)
					->newLine()
					->addText('<b>Accommodations may include Assistive Technology Devices and Services</b>')
					, '[background: #c0c0c0; padding-left: 5px; width: 420px;]')
				->addCell(RCLayout::factory()
					->addText(
						'Sites/Activities Where Required',
						'bold center'
					)
					, '[background: #c0c0c0;]')
				->addCell(RCLayout::factory()
					->addText(
						'Duration',
						'bold center'
					)
					, '[background: #c0c0c0;]');

			foreach ($data as $val) {
				$tbl
					->addRow()
					->addCell('<b>' . $val['area'] . ':</b> <i>' . $val['ssmmbrother'] . '</i>', '[background: #ffffff ;]')
					->addCell('<i>' . $val['ssmteacherother'] . '</i>', '[background: #ffffff ;]')
					->addCell('<i>' . CoreUtils::formatDateForUser($val['ssmbegdate']) . ' - ' . CoreUtils::formatDateForUser($val['ssmenddate']) . '</i>', '[background: #ffffff ;] center');
			}

			$tblNote = RCLayout::factory()
				->newLine()
				->addText('<b>Frequency and Duration of Supports Required for School Personnel to Implement this IEP include:</b>')
				->newLine()
				->addText(
					(string)IDEAStudentRegistry::readStdKey(
						$this->std->get('tsrefid'),
						'ct_iep',
						'general_progran_mod',
						$this->std->get('stdiepyear')
					),
					'italic'
				);

			$layout->newLine()
				->addObject($tbl)
				->newLine()
				->addText(
					'Note: When specifying required supports for personnel to implement this IEP, include the specific supports required, how often they are to be provided (frequency) and for how long (duration)'
					, 'italic [font-size: 7px;]'
				)
				->newLine()
				->addObject($tblNote, '[border: 1px solid black; padding: 0px 5px 10px 5px;]');

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block State and District Testing for IEP doc
		 */
		public function renderStateTesting() {

			$this->setIEPPageFooter();
			$this->rcDoc->startNewPage(false, null, true, 9);

			$values = $this->std->getTesting();

			$layout = RCLayout::factory()
				->newLine()
				->addText('STATE AND DISTRICT TESTING AND ACCOMMODATIONS', 'center bold')
				->newLine()
				->addText(
					'STATEWIDE ASSESSMENTS AND DISTRICTWIDE ASSESSMENTS section must be completed'
					, 'center bold [font-size: 8px;]'
				);
			# left table side
			$leftColTbl = RCLayout::factory()
				->newLine()
				->addText('STATEWIDE ASSESSMENTS', 'bold center')
				->newLine()
				->addText('Check the grade the student will be in when the test is given.', 'bold center');
			$grades = db::execSQL('
			   SELECT gl_refid,
		              gl_code
		         FROM c_manager.def_grade_levels
		        WHERE vndrefid = VNDREFID
		        ORDER BY gl_numeric_value, gl_code'
			)->assocAll();

			if ($values['grade']) {
				$selGrade = db::execSQL("
				   SELECT gl_refid
			         FROM c_manager.def_grade_levels
			        WHERE vndrefid = VNDREFID
			          AND gl_refid = " . $values['grade'] . "
			        ORDER BY gl_numeric_value, gl_code
				")->indexCol(0);
			} else {
				$selGrade = array();
			}

			$allGrades = count($grades);

			for ($i = 0; $i < $allGrades; $i++) {
				if (($i + 4) % 4 == 0 || $i == 0) $leftColTbl->newLine();

				$leftColTbl->addObject(
					$this->addCheck(in_array($grades[$i]['gl_refid'], $selGrade) ? 'Y' : ''),
					'.checker'
				);

				$leftColTbl->addText($grades[$i]['gl_code'], '[width: 50px;]');
			}

			$leftColTbl->newLine()
				->addText('Standard Assessments and Alternate Assessment', 'bold center')
				->newLine()
				->addText('Smarter Balanced Assessments; Connecticut SAT and the CTAA include English Language Arts and Mathematics. ALL students in grades 5 & 8 will also take the CMT Science Test or CMT Skills Checklist Science. Students in Grade 10 will ONLY take the CAPT Science or CAPT Skills Checklist Science.')
				->newLine()
				->addText('<b>Assessment Options: (Select Only ONE Option.)</b>')
				->newLine()
				->addObject($this->addCheck($values['assessment'] == '922' ? 'Y' : ''), '.checker')
				->addText('<b>1. Smarter Balanced Assessments (Includes CMT Science for grades 5 & 8)</b>')
				->newLine()
				->addObject($this->addCheck($values['assessment'] == '923' ? 'Y' : ''), '.checker')
				->addText('<b>2. CTAA - CT Alternate Assessment* (Includes CMT Skills Checklist Science for grades 5 & 8)</b>')
				->newLine()
				->addObject($this->addCheck($values['assessment'] == '924' ? 'Y' : ''), '.checker')
				->addText('<b>3. Grade 10 ONLY (Select ONE):</b>', 'bold [width: 140px;]')
				->addObject($this->addCheck($values['mas'] == '948' ? 'Y' : ''), '.checker')
				->addText('CAPT Science', 'bold [width: 80px;]')
				->addObject($this->addCheck($values['mas'] == '949' ? 'Y' : ''), '.checker')
				->addText('CAPT Skills Checklist Science', 'bold [width: 120px;]')
				->newLine()
				->addObject($this->addCheck($values['assessment'] == '978' ? 'Y' : ''), '.checker')
				->addText('<b>4. Grade 11 ONLY Connecticut SAT</b>')
				->newLine()
				->addText('Administration Options: (Select Only ONE Option.) Accommodations will be provided.', 'bold')
				->newLine()
				->addObject($this->addCheck($values['accommodations_prov'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addText('The student is participating in the Smarter Balanced Assessments or CAPT Science and requires designated supports and/or accommodations**')
				->newLine()
				->addObject($this->addCheck($values['ell'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addText('The student is participating in the Connecticut SAT and will request accommodations***')
				->newLine('[border-top: 1px solid black; padding-top: 5px;]')
				->addText('
		* <i>CTAA for grades 3-8 & 11 and CMT/CAPT Science Skills Checklists Eligibility & Learner Characteristics Inventory (LCI)</i> should be used for guidance on eligibility requirements. Provide a completed copy of the LCI to the district test coordinator for required registration of students assessed with the CT Alternate Assessment (CTAA) and the CMT/CAPT Science Skills Checklists. <b>A PPT decision to assess the student using the CTAA and/or the CMT/CAPT Science Skills Checklists must be recorded on page 3 of the IEP, Prior Written Notice.</b>

		** If accommodations are given, attach a copy of the Test Supports/Accommodations Form to the IEP and provide a copy to the district test coordinator for required registration.

		*** <b>Please note</b>: There are two options for requesting accommodations. One option is through the <b>College Board (CB) process</b>: If all accommodations are approved through the CB process, test scores can be used for college admission and state accountability. The other option is through the <b>State Allowed Accommodations (SAA) process</b>: If accommodations are approved through the SAA process, test scores can ONLY be used for state accountability and NOT for college admission. <b>Please make sure to discuss these options at a PPT meeting before completing this page of the IEP.</b>');
			# right table side
			$rightColTbl = RCLayout::factory()
				->newLine()
				->addText('DISTRICTWIDE ASSESSMENTS', 'bold center')
				->newLine()
				->addText('Check the grade(s) the student will be in when the test is given.', ' bold center');
			if ($values['grade_districtwide']) {
				$selGrades = db::execSQL("
				   SELECT gl_refid
			         FROM c_manager.def_grade_levels
			        WHERE vndrefid = VNDREFID
			          AND gl_refid IN (" . $values['grade_districtwide'] . ")
			        ORDER BY gl_numeric_value, gl_code
				")->indexCol(0);
			} else {
				$selGrades = array();
			}

			for ($i = 0; $i < $allGrades; $i++) {
				if (($i + 4) % 4 == 0 || $i == 0) $rightColTbl->newLine();

				$rightColTbl->addObject(
					$this->addCheck(in_array($grades[$i]['gl_refid'], $selGrades) ? 'Y' : ''),
					'.checker'
				);

				$rightColTbl->addText($grades[$i]['gl_code'], '[width: 50px;]');
			}

			$rightColTbl->newLine()
				->addText('DISTRICTWIDE ASSESSMENTS', 'bold center')
				->newLine()
				->addText('(Select all appropriate options.)', 'center')
				->newLine()
				->addObject($this->addCheck($values['distr_assessment_na'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>N/A</b> - No districtwide assessments are scheduled during the term of this IEP.')
				->newLine()
				->addObject($this->addCheck($values['distr_assessment'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>Alternate Assessment(s)</b>')
				->newLine()
				->addText('Alternate assessments must be specified and a statement provided for each as to why the child cannot participate in the standard assessment and why the particular alternate assessment selected is appropriate for the child.')
				->newLine();
			if ($values['distr_assessment'] == 'N' && $values['aleternativ_assessment'] != '') {
				$rightColTbl->addText($values['aleternativ_assessment'], 'italic underline')
					->newLine();
			}
			$rightColTbl
				->addText('<b>Select one of the following options:</b>')
				->newLine()
				->addObject($this->addCheck($values['distr_accommodation'] == 'A' ? 'Y' : ''), '.checker')
				->addText('<b>No accommodations will be provided, OR</b>')
				->newLine()
				->addObject($this->addCheck($values['distr_accommodation'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>Accommodations will be provided as specified on Page 8, OR</b>')
				->newLine()
				->addObject($this->addCheck($values['distr_accommodation'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>Accommodations will be provided as specified below.</b>');
			if ($values['distr_accommodation'] == 'N' && $values['aleternativ_accommodation'] != '') {
				$rightColTbl
					->newLine()
					->addText($values['aleternativ_accommodation'], 'italic')
					->newLine();
			}
			# table with 2 cols
			$tblWithCols = RCLayout::factory()
				->addObject($leftColTbl, '[width 50%;]')
				->addObject($rightColTbl, '[border-left: 1px solid black; width: 50%;]');

			$layout->newLine()
				->addObject($tblWithCols, '[border: 1px solid black;]');

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Special Factors for IEP doc
		 */
		public function renderSpecialFactors() {

			$this->setIEPPageFooter('December 2015');
			$this->rcDoc->startNewPage(false, null, true, 10);

			$questions = $this->std->getSpecConsiderations();

			$layout = RCLayout::factory()
				->newLine()
				->addText('SPECIAL FACTORS, PROGRESS REPORTING, EXIT CRITERIA', 'center bold')
				->newLine()
				->addText('CONSIDERATION OF SPECIAL FACTORS:', $this->titleStyle())
				->newLine();

			$i = 1;
			foreach ($questions as $question) {
				$layout->addText(($question['scmlinksw'] != 'Y' ? $i . '. ' : '' ) . $question['scmquestion'], 'bold')
					->newLine();
				foreach ($question['answ'] as $ans) {
					$layout
						->addObject($this->addCheck($ans['checked'] == 'yes' ? 'Y' : ''), '.checker')
						->addText((string)$ans['scanswer'])
						->newLine();
				}
				if ($question['scmlinksw'] != 'Y') $i++;
			}

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Progress Reporting for IEP doc
		 */
		public function renderProgressReporting() {
			$results = $this->std->getProgressReporting();

			$layout = RCLayout::factory()
				->addText('PROGRESS REPORTING:', $this->titleStyle())
				->newLine();
			foreach ($results as $res) {
				if ($res['fprdesc'] != 'Other:') {
					$layout
						->addObject($this->addCheck($res['sfprrefid'] != '' ? 'Y' : ''), '.checker')
						->addText((string)$res['fprdesc'])
						->newLine();
				} else {
					$layout
						->addObject($this->addCheck($res['sfprrefid'] != '' ? 'Y' : ''), '.checker')
						->addText((string)$res['fprdesc'] . ' <i>' . $res['other_desc'] . '</i>')
						->newLine();
				}
			}

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Exit Criteria for IEP doc
		 */
		public function renderExitCriteria() {
			$codes = IDEADistrict::factory(SystemCore::$VndRefID)->getExitCodes();
			$exitCode = $this->std->get('dexrefid');
			$exitOther = $this->std->get('parcomments');

			$layout = RCLayout::factory()
				->addText('EXIT CRITERIA:', $this->titleStyle())
				->newLine()
				->addText('1. Exit Criteria: Student will be exited from Special Education upon: (Check One)')
				->newLine();
			foreach ($codes as $code) {
				if ($code['exitcode'] == '04 - Other: (specify)') {
					$layout
						->addObject($this->addCheck($code['dexrefid'] == $exitCode ? 'Y' : ''), '.checker')
						->addText($code['dexrefid'] == $exitCode ? $code['exitcode'] . ': ' . $exitOther : (string)$code['exitcode'])
						->newLine();
				} else {
					$layout
						->addObject($this->addCheck($code['dexrefid'] == $exitCode ? 'Y' : ''), '.checker')
						->addText((string)$code['exitcode'])
						->newLine();
				}
			}

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Secondary Transition
		 */
		public function renderSecondaryTransition() {
			$layout = RCLayout::factory()
				->newLine()
				->addText('INFORMATION ON IEPs and SECONDARY TRANSITION', $this->titleStyle());
			$this->rcDoc->addObject($layout)->newLine();

			$xmlData = IDEADef::getConstructionTemplate(273);
			$values = $this->std->getConstruction(273, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}
			$this->rcDoc->addObject($doc->getLayout());
		}

		/**
		 * Generate block Services for IEP doc
		 */
		public function renderServices() {

			$this->setIEPPageFooter('December 2015');
			$this->rcDoc->startNewPage(false, null, false, 11);

			$values = $this->std->getTotalSchoolHours();
			$services = $this->std->getServices();

			$layout = RCLayout::factory()
				->newLine()
				->addText('SPECIAL EDUCATION, RELATED SERVICES, AND REGULAR EDUCATION', 'center bold');

			# styles for table
			$cols = array(
				80 => new RCStyle('bold center [width: 80px;]'),
				35 => new RCStyle('bold center [border-left: 1px solid black; width: 35px;]'),
				50 => new RCStyle('bold center [border-left: 1px solid black; width: 50px;]'),
				70 => new RCStyle('bold center [border-left: 1px solid black; width: 70px;]'),
				60 => new RCStyle('bold center [border-left: 1px solid black; width: 60px;]'),
				30 => new RCStyle('bold center [border-left: 1px solid black; width: 50px;]'),
			);

			$tbl = RCTable::factory('.table')
				->addRow('.row [background: #c0c0c0; border: 1px solid black;]')
				->addCell('Special Education Services', $cols[80])
				->addCell('Goal(s) #', $cols[35])
				->addCell('Frequency', $cols[50])
				->addCell('Implementor Title', $cols[70])
				->addCell('Responsible Staff', $cols[70])
				->addCell('Start Date' . PHP_EOL . '(mm/dd/yyyy)', $cols[60])
				->addCell('End Date' . PHP_EOL . '(mm/dd/yyyy)', $cols[60])
				->addCell('Site*', $cols[30])
				->addCell('If needed, description of Instructional Service Delivery (e.g. small group, team taught classes, etc.)'
					, 'bold center [border-left: 1px solid black;]');

			foreach ($services[0] as $serv) {
				if (strtolower($serv['typedesc']) == 'special education services') {
					$goals = explode(',', $serv['goals']);
					$goal = '';
					$i = 0;
					foreach ($goals as $gl) {
						if ($i > 0) {
							$goal .= ', ';
						}
						if (isset($services[1][$gl])) {
							$goal .= $services[1][$gl];
						}
						$i++;
					}
					$tbl->addRow('.row')
						->addCell($serv['services'], 'italic [font-weight:normal;]')
						->addCell($goal, 'italic [font-weight:normal;]')
						->addCell($serv['frequency_text'], 'italic [font-weight:normal;]')
						->addCell($serv['um_title'], 'italic [font-weight:normal;]')
						->addCell($serv['inarr'], 'italic [font-weight:normal;]')
						->addCell($serv['begdate'], 'italic [font-weight:normal;]')
						->addCell($serv['enddate'], 'italic [font-weight:normal;]')
						->addCell($serv['loc'], 'italic [font-weight:normal;]')
						->addCell($serv['addcomments'], 'italic [font-weight:normal;]');
				}
			}

			$tbl->addRow('[background: #c0c0c0; border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black;]')
				->addCell('Related Services')
				->addCell('')
				->addCell('')
				->addCell('')
				->addCell('')
				->addCell('')
				->addCell('')
				->addCell('')
				->addCell('');

			foreach ($services[0] as $serv) {
				if (strtolower($serv['typedesc']) == 'related services') {
					$goals = explode(',', $serv['goals']);
					$goal = '';
					$i = 0;
					foreach ($goals as $gl) {
						if ($i > 0) {
							$goal .= ', ';
						}
						if (isset($services[1][$gl])) {
							$goal .= $services[1][$gl];
						}
						$i++;
					}
					$tbl->addRow('.row')
						->addCell($serv['services'], 'italic [font-weight:normal;]')
						->addCell($goal, 'italic [font-weight:normal;]')
						->addCell($serv['frequency_text'], 'italic [font-weight:normal;]')
						->addCell($serv['um_title'], 'italic [font-weight:normal;]')
						->addCell($serv['inarr'], 'italic [font-weight:normal;]')
						->addCell($serv['begdate'], 'italic [font-weight:normal;]')
						->addCell($serv['enddate'], 'italic [font-weight:normal;]')
						->addCell($serv['loc'], 'italic [font-weight:normal;]')
						->addCell($serv['addcomments'], 'italic [font-weight:normal;]');
				}
			}

			$layout->newLine()
				->addObject($tbl);

			$layout->newLine()
				->addObject(
					RCLayout::factory()
						->addText('Note: <b>Each Item #1-13 must include a response</b>')
					, '[width: 10%;]'
				)
				->addObject(
					RCLayout::factory()
						->addText('1. Assistive Technology:')
						->newLine()
						->addText('2. Applied (Voc.) Ed:')
						->newLine()
						->addText('3. Physical Education:')
						->newLine()
						->addText('4. Transportation:')
					, 'bold [width: 15%;]'
				)
				->addObject(
					RCLayout::factory()
						->addObject($this->addCheck($values['assistive_technology'] == 'N' ? 'Y' : ''), '.checker')
						->addText('Not Required')
						->newLine()
						->addObject($this->addCheck($values['voc'] == 'Y' ? 'Y' : ''), '.checker')
						->addText('Regular')
						->newLine()
						->addObject($this->addCheck($values['physical'] == 'Y' ? 'Y' : ''), '.checker')
						->addText('Regular')
						->newLine()
						->addObject($this->addCheck($values['transportation'] == 'Y' ? 'Y' : ''), '.checker')
						->addText('Regular')
					, 'bold [width: 10%;]'
				)
				->addObject(
					RCLayout::factory()
						->addObject($this->addCheck($values['assistive_technology'] == 'Y' ? 'Y' : ''), '.checker')
						->addText('Required: See Pg. 8')
						->newLine()
						->addObject($this->addCheck($values['voc'] == 'N' ? 'Y' : ''), '.checker')
						->addText('Special', '[width: 35px;]')
						->addText($values['voc'] == 'N' ? (string)$values['voc_text'] : '', 'italic [border-bottom: 1px solid black; width: 60px; margin-right: 15px]')
						->addObject($this->addCheck($values['voc'] == '' ? 'Y' : ''), '.checker')
						->addText('N/A')
						->newLine()
						->addObject($this->addCheck($values['physical'] == 'N' ? 'Y' : ''), '.checker')
						->addText('Special', '[width: 35px;]')
						->addText($values['physical'] == 'N' ? (string)$values['physical_text'] : '', 'italic [border-bottom: 1px solid black; width: 60px; margin-right: 15px]')
						->addObject($this->addCheck($values['physical'] == '' ? 'Y' : ''), '.checker')
						->addText('N/A')
						->newLine()
						->addObject($this->addCheck($values['transportation'] == 'N' ? 'Y' : ''), '.checker')
						->addText('Special', '[width: 35px;]')
						->addText($values['transportation'] == 'N' ? (string)$values['transportation_text'] : '', 'italic [border-bottom: 1px solid black; width: 60px; margin-right: 15px]')
						->addObject($this->addCheck($values['transportation'] == '' ? 'Y' : ''), '.checker')
						->addText('N/A')
					, 'bold [width: 30%;]'
				)
				->addObject(
					RCLayout::factory()
						->addText('5. Length of School Day:', '[width: 120px;]')
						->addText('' . $values['length_day'] . '', 'italic [border-bottom: 1px solid black; width: 50px;]')
						->newLine()
						->addText('6. Number of Days/Week:', '[width: 120px;]')
						->addText('' . $values['number_day'] . '', 'italic [border-bottom: 1px solid black; width: 50px;]')
						->newLine()
						->addText('7. Length of School Year:', '[width: 120px;]')
						->addText('' . $values['length_year'] . '', 'italic [border-bottom: 1px solid black; width: 50px;]')
					, 'bold [width: 20%;]'
				);

			$tblSchools = RCTable::factory('.table')
				->addRow('.row')
				->addCell('8. Total School Hours/Week:', '.hr')
				->addCell('9. Special Education Hours/Week:', '.next-hr')
				->addCell(
					'10. Hours per week the student <b>will spend</b> with children/students who do not have disabilities (time with non-disabled peers):'
					, '.next-hr'
				)
				->addRow()
				->addCell($values['total_week'], 'italic')
				->addCell($values['special_week'], 'italic')
				->addCell($values['hours_per_week'], 'italic');

			$layout->newLine()
				->addObject($tblSchools)
				->newLine()
				->addText(
					'<b>11. Since the last Annual Review, has the student participated in school sponsored extracurricular activities with non-disabled peers?</b>'
					, '.padtop5 [width: 480px;]'
				);

			$layout->addObject($this->addCheck($values['since_peers'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No')
				->addObject($this->addCheck($values['since_peers'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes');

			$layout->newLine()
				->addText('12. Extended School Year Services:', 'bold [width: 120px;]')
				->addObject($this->addCheck($values['extended_services'] == '' ? 'Y' : ''), '.checker')
				->addText('Not Required', 'bold [width: 70px;]')
				->addObject($this->addCheck($values['extended_services'] == 'Y' ? 'Y' : ''), '.checker')
				->addText(
					'Required: See service delivery grid above or an additional page 11 for services to be provided '
					, 'bold [width: 150px;]'
				)
				->addObject($this->addCheck($values['extended_services'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>Required: Continue to implement current IEP</b>')
				->newLine()
				->addText('<b>13. a) The extent, if any, to which the student <b>will not</b> participate in regular classes and in extracurricular and other nonacademic activities,</b>')
				->newLine()
				->addText(
					'<b>including lunch, recess, transportation, etc., with</b> students who do not have disabilities:'
					, '[width: 340px;]'
				)
				->newLine()
				->addText($values['extent'] == '' ? '' . $values['extent_explan'] . '' : '', 'italic')
				->newLine()
				->addObject($this->addCheck($values['extent'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Not Applicable: Student will participate fully')
				->newLine()
				->addText('')
				->newLine()
				->addText('b) If the IEP requires <b>any</b> removal of the student from the school, classroom, extracurricular, or nonacademic activities, (e.g., lunch, recess, transportation,')
				->newLine()
				->addText(
					'etc.) that s/he would attend if not disabled, the PPT must justify this removal from the regular education environment.'
					, '[width: 420px;]'
				)
				->newLine()
				->addObject($this->addCheck($values['removal'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Not applicable: Student will participate fully')
				->newLine()
				->addObject($this->addCheck($values['removal'] == 'N' ? 'Y' : ''), '.checker')
				->addText('The IEP requires removal of the student from the regular education environment because: (provide a detailed explanation - use additional pages if necessary)')
				->newLine()
				->addText($values['removal'] == 'N' ? '' . $values['removal_explan'] . '' : '', 'italic')
				->newLine()
				->addText(
					'Note: The LRE Checklist (ED632) <b>must be completed and attached</b> to this IEP if the student is to be removed from the regular education environment for <b>60% or more</b> of the time. It is <b>recommended</b> that the LRE Checklist be utilized when making <b>any</b> placement decision to ensure conformity with the LRE provisions of the Individuals with Disabilities Education Act.'
					, 'italic [border: 1px solid black; border-left: none; border-right: none; padding: 5px 0px 5px 0px; margin: 5px 0px 5px 0px;]'
				);

			$this->rcDoc->addObject($layout);
		}

		public function renderStateAccommod() {

			$this->setIEPPageFooter('December 2015');
			$this->rcDoc->startNewPage();

			$statevalues = $this->std->getTesting(2);

			$layout = RCLayout::factory()
				->newLine()
				->addText('Test Supports/Accommodations Form: General Education, Special Education, Section 504 Students, EL Students', 'center bold')
				->newLine();

			$accs = $this->std->getTestAccommodations();
			$progs = $this->std->getAccommodationProgs();

			$layout->newLine();
			$student = '';
			if (isset($statevalues['has_student']) and $statevalues['has_student'] == 950) {
				$student = '<b>A)</b> A Special Education IEP';
			}
			if (isset($statevalues['has_student']) and $statevalues['has_student'] == 951) {
				$student = '<b>B)</b> A Section 504 Plan';
			}
			if (isset($statevalues['has_student']) and $statevalues['has_student'] == 952) {
				$student = '<b>C)</b> Neither';
			}

			$fTable = RCTable::factory('[width:95%]');
			$fTable->border();
			$fTable->addCell('<b>This student has (circle one):</b> ' . $student);
			$fTable->addRow();
			$fTable->addCell((isset($statevalues['has_learner']) and $statevalues['has_learner'] == 'Y') ? '<b>This is an English Learner - EL (circle one): Yes</b>' : '<b>This is an English Learner - EL (circle one): No</b>');

			$layout->addObject($fTable, '[padding-left: 20%]');

			$layout->newLine();

			$table = RCTable::factory();
			$table->border();
			$acc_width = '[width: ' . 100 - (7 * count($progs)) . '%]';
			foreach ($progs as $prog) {
				$table->addColumn($prog['progdesc'], '[ width: 7%]');
			}
			$table->addColumn('Accommodations', $acc_width);
			$category = "";
			foreach ($accs AS $acc) {
				if ($acc["subj_desc"] != "") {
					if ($acc["catrefid"] != $category) {
						$table->addRow();
						$table->addCell($acc["catdesc"], 'bold', count($progs) + 1);
						$category = $acc["catrefid"];
					}
					$table->addRow();
					foreach ($progs as $prog) {
						$table->addCell($this->addCheck($acc["subj_desc"] == $prog['progdesc'] ? 'Y' : ''), '.checker center');
					}
					$table->addCell($acc["accdesc"]);
				}
			}
			$layout->addObject($table);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Data Collection for IEP doc
		 */
		public function renderDataCollection() {

			$this->setIEPPageFooter();
			$this->rcDoc->startNewPage(false, null, false, 12);

			$xmlData = IDEADef::getConstructionTemplate(160);
			$values = $this->std->getConstruction(160, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}

			$this->rcDoc->addObject($doc->getLayout());
			$this->setIEPPageFooter('October 2014');
		}

		public function renderProgessReport() {
			$name = 'Student Progress Report';
			$goals = array(
				$this->std->getProgressReportBGB('N'),
			);
			if (isset($goals[0][0])) {
				$this->rcDoc->startNewPage(false, null, false, 13);
				$this->rcDoc->newLine();
				$this->progressReportGoals($name, $goals[0]);
			}
		}
	}
