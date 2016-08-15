<?php

	/**
	 * IDEABlockEval.php
	 * Class for creation blocks in Evaluation builder.
	 *
	 * @author Alex Kalevich
	 * Created 09-07-201
	 */
	class IDEABlockEval extends IDEABlock {
		public static function defineDefaultStyles() {
			parent::defineDefaultStyles();
			RCStyle::defineStyleClass('width25', '[width: 25px;]');
			RCStyle::defineStyleClass('docfont', '[font-family: Times;]');
			RCStyle::defineStyleClass('default', '[font-size:10px] .docfont');
			RCStyle::defineStyleClass('header', '[font-size:8px] .docfont');
			RCStyle::defineStyleClass('h1', '[font-size:14px] .docfont bold center');
			RCStyle::defineStyleClass('h2', '[font-size:12px] .docfont bold center');
			RCStyle::defineStyleClass('grayed', '[background: #c0c0c0; padding-left: 5px;]');
		}

		public function setStd($tsRefID, $iepyear = null, $params = null) {
			$this->std = new IDEAStudentEval($tsRefID);
			$this->student_params = $params;
			# set data about student to header. Because header use info about student.
			$this->setHeaderDoc();
		}

		public function setHeaderDoc() {
			$data = $this->std->getGenralInfo();
			$report_type = '';
			if ($data['report_type'] == 1) {
				$report_type = 'Initial Evaluation';
			} elseif ($data['report_type'] == 2) {
				$report_type = 'Reevaluation';
			}
			$report_date = $this->student_params['report_date'] != '' ? CoreUtils::formatDateForUser($this->student_params['report_date']) : '';
			$layout = RCLayout::factory()
				->newLine('.header')
				->addText('Student:', '[width: 7%;] right')
				->addText((string)$this->std->get('stdname'), 'italic center [border-bottom: 1px solid black; width: 15%;]')
				->addText('DOB:', '[margin-left: 10px; width: 8%;] right')
				->addText((string)$this->std->get('stddob'), 'italic center [border-bottom: 1px solid black; width: 10%;]')
				->addText('Evaluation Report: ', '[margin-left: 10px; width: 25%;] right')
				->addText((string)($report_type), 'italic center [border-bottom: 1px solid black; width: 15%;]')
				->addText('Date:', '[margin-left: 10px; width: 9%;] right')
				->addText($report_date, 'italic center [border-bottom: 1px solid black; width: 10%;]');

			$this->rcDoc->setPageHeader($layout, '.header');
			$this->rcDoc->setPageFooter(
				RCLayout::factory()
					->addText('Revised July 31, 2015', '.docfont [width: 230px;] italic [font-size: 8px;]')
					->addText('[PN]', '.default right')
			);
		}

		public function renderERGenInfo() {
			$this->rcDoc->startNewPage();
			$this->rcDoc->addBookmark('General Information');
			$data = IDEACore::clearUTF8($this->std->getGenralInfo());

			$layout = RCLayout::factory()
				->newLine()
				->addText((string)SystemCore::$VndName, '.h1')
				->newLine()
				->addText(
					'EVALUATION REPORT'
					, '.h1'
				)->newLine('.default')
				->addText('The evaluation report documents assessment results and review of data that assists in determining whether a student is eligible for special education, and provides information to the IEP team to assist with IEP development. The evaluation process should be sufficient in scope to determine: (1) whether a student has a disability, (2) whether the disability adversely affects his/her performance in the general education curriculum, and (3) the nature and extent of the student\'s need for specially designed instruction and any necessary related services. Based on the review of the evaluation results, a group of qualified professionals and the parent of the child determine whether the student is eligible for special education.');

			$layout->newLine('.default')
				->addText('')
				->addObject(
					$this->addCheck((string)$data['report_type'] == 1 ? 'Y' : ''),
					'.checker center [padding-top: 5px;]'
				)
				->addText('Initial Evaluation', 'center bold 80px')
				->addText('', '40px')
				->addObject(
					$this->addCheck((string)$data['report_type'] == 2 ? 'Y' : ''),
					'.checker center [padding-top: 5px;]'
				)
				->addText('Reevaluation', 'center bold 80px')
				->addText('');

			$tbl = RCTable::factory()
				->setCol()
				->border(1)
				->addCell(
					RCLayout::factory()
						->addText('General Information', '.h2'),
					'.grayed'
				);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Student's Name: <i>" . (string)$data['stdname'] . "</i>")
				->addText("Date of Birth: <i>" . (string)$data['stddob'] . "</i>")
				->addText("Age: <i>" . (string)$data['stdage'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Grade: <i>" . (string)$data['stdgrade'] . "</i>")
				->addText("School: <i>" .(string) (string)$data['stdschool'] . "</i>")
				->addText('')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Parent's Name(s): <i>" . (string)$data['stdparent'] . "</i>")
				->addText("Phone: <i>" . (string)$data['stdphone'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Address: <i>" . (string)$data['stdaddress'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Primary Language:")
				->addObject($this->addCheck(stristr($data['stdlang'], 'English') ? 'Y' : ''), '.checker')
				->addText((string)$data['stdlang'])
				->addObject($this->addCheck(!stristr($data['stdlang'], 'English') ? 'Y' : ''), '.checker')
				->addText(!stristr($data['stdlang'], 'English') ? 'Other: <i>' . $data['stdlang'] . '</i>': 'Other:')
				->addText('')
				->addText('')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Does student have limited English proficiency?")
				->addObject($this->addCheck((string)$data['lep_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addObject($this->addCheck((string)$data['lep_sw'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No', '.width20')
				->addText('')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Referral Date: <i>" . ($data['refferal_dt'] != '' ? CoreUtils::formatDateForUser($data['refferal_dt']) : '') . "</i>")
				->addText("Review of Existing Data Date: <i>" . ($data['red_dt'] != '' ? CoreUtils::formatDateForUser($data['red_dt']) : '') . "</i>")
				->addText("Date of Consent to Evaluate: <i>" . ($data['consent_dt'] != '' ? CoreUtils::formatDateForUser($data['consent_dt']) : '') . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Eligibility Staffing Date: <i>" . ($data['eligibility_dt'] != '' ? CoreUtils::formatDateForUser($data['eligibility_dt']) : '') . "</i>")
				->newLine()
				->addText("Evaluation Held within Required Timelines (include acceptable extensions if appropriate): ", '[width:50%;]')
				->addObject($this->addCheck((string)$data['timiline_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Yes', '.width20')
				->addObject($this->addCheck((string)$data['timiline_sw'] == 'N' ? 'Y' : ''), '.checker')
				->addText('No', '.width20')
				->addText((string)$data['timiline_no'], 'italic')
			);


			if (IDEACore::disParam(114) == "Y") {
				$tbl->addRow('.default');
				$tbl->addCell(RCLayout::factory()
					->addText("Copy of Bill of Rights given to parent(s) on: <i>" . ($this->std->get('parentrightdt') != '' ? $this->std->get('parentrightdt') : '') . "</i>")
					->addText("Procedural Safeguards given to parent(s) on: <i>" . ($this->std->get('stdprocsafeguarddt') != '' ? $this->std->get('stdprocsafeguarddt') : '') . "</i>")
				);
			}

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Referred By: <i>" . (string)$data['reffered_by'] . "</i>")
				->addText("Role: <i>" . (string)$data['reffered_role'] . "</i>")
				->addText('')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Case Manager (if assigned): <i>" . $data['stdcmanager'] . "</i>")
			);

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
		}

		public function renderERCaseHistory() {

			$this->rcDoc->addBookmark('Case History');
			$data = IDEACore::clearUTF8($this->std->getCaseHistory());
			$layout = RCLayout::factory();

			$tbl = RCTable::factory()
				->setCol()
				->border(1)
				->addCell(RCLayout::factory()
						->addText('Case History', '.h2'),
					'.grayed'
				);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("<b>Description of Educational Concerns: </b><i>" . (string)$data['concerns'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("<b>Intervention Strategies Used Prior to Referral: </b><i>" . (string)$data['interventions'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("<b>School History: </b><i>" . (string)$data['school_history'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("<b>Family History: </b><i>" . (string)$data['family_history'] . "</i>")
			);

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Evaluation Procedures
		 */
		public function renderEREvalProcedures() {

			$this->rcDoc->addBookmark('Evaluation Procedures');

			$procedures = IDEACore::clearUTF8($this->std->getERProcedures());

			$layout = RCLayout::factory();

			$table = RCTable::factory();
			$table->border(1, '#000');

			$table->setCol('45%');
			$table->setCol('15%');
			$table->setCol('40%');

			$table->addCell(RCLayout::factory()
						->addText('Evaluation Procedures', '.h2'),
						'.grayed',
						3
					);

			$table->addRow('.default');
			$table->addCell('Name of Assessment', 'bold center');
			$table->addCell('Date of Assessment', 'bold center');
			$table->addCell('Name/Role of Person Conducting Assessment', 'bold center');

			foreach ($procedures as $procedure) {
				$table->addRow('.default');
				$table->addCell($procedure['procedure_name'], 'italic');
				$table->addCell($procedure['assessment_date'] != '' ? CoreUtils::formatDateForUser($procedure['assessment_date']) : '', 'italic');
				$table->addCell($procedure['assessment_person'], 'italic');
			}

			$layout->addObject($table);

			$this->rcDoc->addObject($layout);
		}

		/**
		 * Generate block Evaluation Results
		 */
		public function renderEREvalResults() {

			$this->rcDoc->addBookmark('Evaluation Results');

			$evalresults = IDEACore::clearUTF8($this->std->getERResults());
			$red_summary = IDEACore::clearUTF8($this->std->getREDSummary());



			foreach ($evalresults as $evalresult) {

				$tbl = RCTable::factory()
					->setCol()
					->border(1)
					->addCell(RCLayout::factory()
						->addText('Evaluation Results - ' . $evalresult['area'], '.h2'),
						'.grayed'
					);

				$area_content = RCLayout::factory()
					->addText('<b>' . $evalresult['area'] . '</b>: ' . (string)$evalresult['description'])->newLine()
					->addText('<b>Data Reviewed and Results:</b> <i>' . (string)$evalresult['eval_summary'] . '</i>')->newLine();
				if ($evalresult['include_red_sw'] == 'Y') {
					$red_text = '';
					if (isset($red_summary[$evalresult['screening_id']])) {
						$description = $red_summary[$evalresult['screening_id']]['red_desc'];
						$summary =  $red_summary[$evalresult['screening_id']]['red_text'];
						$arr = array();
						if ($description != '') $arr[] = $description;
						if ($summary != '') $arr[] = $summary;
						$red_text = implode(PHP_EOL, $arr);
					}
					$area_content
						->newLine()
						->addText((string)$red_text, 'italic');
				}
				$area_content
					->newLine()
					->addText('Further assessment needed: ', 'bold [width:22%;]')
					->addObject($this->addCheck((string)$evalresult['further_assess_needed_sw'] == 'Y' ? 'Y' : ''), '.checker')->addText('Yes', '.width20')
					->addObject($this->addCheck((string)$evalresult['further_assess_needed_sw'] == 'N' ? 'Y' : ''), '.checker')->addText('No', '.width20')
					->addText('[if yes, include results of assessment(s) below or attach <i>Documentation of Assessment Results</i> form]');

				if (IDEACore::disParam(99) == 'Y') {
					$item['plafp'] = '';
					$item['skill'] = '';
					if (isset($red_summary[$evalresult['screening_id']])) {
						$item['plafp'] = $red_summary[$evalresult['screening_id']]['plafp'];
						$item['skill'] =  $red_summary[$evalresult['screening_id']]['skill'];
					}
					$area_content
						->newLine()
						->addObject($this->addCheck($item['plafp'] == 'Y' ? 'Y' : ''), '.checker')
						->addText("Information is needed to update present level of performance")
						->newLine()
						->addObject($this->addCheck($item['skill'] == 'Y' ? 'Y' : ''), '.checker')
						->addText("Skill level has been established")
						->newLine()
						->addObject($this->addCheck($item['skill'] == 'N' ? 'Y' : ''), '.checker')
						->addText("Skill level needs to be established");
				}

				$tbl->addRow('.default');
				$tbl->addCell($area_content);

				$this->rcDoc->addObject($tbl)->newLine();

				$procedures = IDEACore::clearUTF8($this->std->getERProcedures());

				$area = '';
				foreach ($procedures as $procedure) {
					if ($procedure['assessment_data'] && $procedure['procedure_template'] && $procedure['area'] == $evalresult['area']) {

						if ($area != $procedure['area']) {
							$this->rcDoc
								->newLine()
								->addText(' ')
								->newLine()
								->addText('Individual Documentation of Assessment Results', 'center .h2')
								->newLine()
								->addText(' ')
								->newLine();
						} else {
							$this->rcDoc
								->newLine()
								->addText(' ')
								->newLine();
						}

						$tbl = RCTable::factory('.default')
							->setCol()
							->border(1)
							->addRow()
							->addCell('Student Name: <i>' . $this->std->get('stdname') . '</i>')
							->addCell('Birth Date: <i>' . $this->std->get('stddob') . '</i>')
							->addRow()
							->addCell('Grade: <i>' . $this->std->get('grdlevel') . '</i>')
							->addCell('Age: <i>' . $this->std->get('stdage') . '</i>')
							->addRow()
							->addCell('Examiner: <i>' . $procedure['assessment_person'] . '</i>')
							->addCell('Evaluation Date(s): <i>' . ($procedure['assessment_date'] != '' ? CoreUtils::formatDateForUser($procedure['assessment_date']) : '') . '</i>')
							->addRow()
							->addCell('Area of Assessment: <i>' . $procedure['area'] . '</i>')
							->addCell('Location of Assessment: <i>' . $procedure['assessment_location'] . '</i>');

						$this->rcDoc
							->addObject($tbl)
							->newLine()
							->addText('')
							->newLine();

						# Add Other Template Name
						if ($procedure['flag_other'] == '1') {
							$this->rcDoc
								->newLine()
								->addText($procedure['procedure_name'], 'center .default bold')
								->newLine();
						}

						$doc = IDEADocument::factory($procedure['procedure_template']);
						$doc->mergeValues($procedure['assessment_data']);

						$this->rcDoc->addObject($doc->getLayout(), '.default')->newLine();
					}
					$area = $procedure['area'];
				}
			}

		}

		/**
		 * Generate block Evaluation Observation
		 */
		public function renderERObservation() {

			$this->rcDoc->addBookmark('Observation');

			$observations = IDEACore::clearUTF8($this->std->getERObservation());

			$tbl = RCTable::factory()
				->setCol()
				->border(1)
				->addCell(RCLayout::factory()
						->addText('Observation (if applicable):', '.h2'),
					'.grayed'
				);

			foreach ($observations as $observation) {
				$tbl->addRow('.default');
				$tbl->addCell(RCLayout::factory()
					->addText('Observer: <i>' . $observation['observer'] . '</i>')
					->addText('Position/Role of Observer: <i>' . $observation['role'] . '</i>')
					->newLine()
					->addText('Location of Observation: <i>' . $observation['location'] . '</i>')
					->newLine()
					->addText('Date: <i>' . $observation['date'] . '</i>')
					->newLine()
					->addText('Time: <i>' . $observation['time'] . '</i>')
					->newLine()
					->addText('Type of activities observed: <i>' . $observation['activities_type'] . '</i>')
					->newLine()
					->addText('Observation conducted in area of concern(s): <i>' . $observation['conducted_val'] . '</i>')
					->newLine()
					->addText('Summary of Observation(s): <i>' . $observation['summary'] . '</i>')
				);
			}

			$this->rcDoc->addObject($tbl);
		}

		/**
		 * Generate block Evaluation Assessments
		 */
		public function renderERAssessments() {


		}

		public function renderERConclusions() {

			$this->rcDoc->startNewPage();
			$this->rcDoc->addBookmark('Team Conclusions and Decisions');
			$data = IDEACore::clearUTF8($this->std->getTeamConcl());
			$layout = RCLayout::factory();

			$tbl = RCTable::factory()
				->setCol()
				->border(1)
				->addCell(RCLayout::factory()
						->addText('Team Conclusions and Decisions', '.h2'),
					'.grayed'
				);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("<b>The student was assessed in all areas related to the suspected disability, including, if appropriate, health, vision, hearing,\n social/emotional status, general intelligence, academic performance, communication, and motor abilities.</b>")
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['was_assess_sw'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>No (<i>If no, the evaluation is not sufficiently comprehensive and the evaluation is incomplete.</i>)</b>')
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['was_assess_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>Yes</b>')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText('<b>There is documentation to confirm this student has a disability under the IDEA?</b>', '[width:60%;]')
				->addObject($this->addCheck((string)$data['disability_confirmed_sw'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>No</b>', '.width20')
				->addObject($this->addCheck((string)$data['disability_confirmed_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>Yes</b>', '.width20')
				->newLine()
				->addText('<b>If yes, list eligibility category: </b><i>' . (string)$data['eldesc'] . '</i>')
				->newLine()
				->newLine()
				->addText('<b>Subcategory (if appropriate): </b><i>' . (string)$data['disability_text'] . '</i>')
				->addText('')
				->newLine()
				->addText('<b>Does this disability adversely affect the student\'s education?</b>', '[width:45%;]')
				->addObject($this->addCheck((string)$data['disability_affect_sw'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>No</b>', '.width20')
				->addObject($this->addCheck((string)$data['disability_affect_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>Yes</b>', '.width20')
				->newLine()
				->addText('')
				->newLine()
				->addText('<b>Does the student need specially designed instruction?</b>', '[width:40%;]')
				->addObject($this->addCheck((string)$data['sped_needed_sw'] == 'N' ? 'Y' : ''), '.checker')
				->addText('<b>No</b>', '.width20')
				->addObject($this->addCheck((string)$data['sped_needed_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('<b>Yes</b>', '.width20')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText('<b>IF ELIGIBLE, THIS EVALUATION REPORT REFLECTS THAT THE CHILD\'S ELIGIBILITY DETERMINATION WAS NOT BASED ON ANY OF THE FOLLOWING FACTORS:</b>')
				->newLine()
				->addObject($this->addCheck((string)$data['lack_instruction_read_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText("A lack of appropriate instruction in reading including the essential components of reading instruction (as \ndefined in Section 1208 (3) of the ESEA):")
				->newLine('')
				->addText('', '[width:6%;]')
				->addText('1) Phonemic Awareness')
				->newLine('')
				->addText('', '[width:6%;]')
				->addText('2) Phonics')
				->newLine('')
				->addText('', '[width:6%;]')
				->addText('3) Vocabulary Development')
				->newLine('')
				->addText('', '[width:6%;]')
				->addText('4) Reading Fluency including oral reading skills')
				->newLine('')
				->addText('', '[width:6%;]')
				->addText('5) Reading Comprehension Strategies')
				->newLine()
				->addObject($this->addCheck((string)$data['lack_instruction_math_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText("A lack of appropriate instruction in math")
				->newLine()
				->addObject($this->addCheck((string)$data['lep_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText("Limited English Proficiency")
				->newLine()
				->addObject($this->addCheck((string)$data['other_factors_sw'] == 1 ? 'Y' : ''), '.checker')
				->addText("Describe any other exclusionary factors relevant to the eligibility category (additional requirements required for SLD, LI and SSD):")
				->newLine()
				->addText('', '[width:6%;]')
				->addText('<i>' . (string)$data['other_factors_text'] . '</i>')
				->newLine()
				->addText('')
				->newLine()
				->addText('<b>RELEVANT MEDICAL FINDINGS:</b>')
				->newLine()
				->addObject($this->addCheck((string)$data['medical_finding_no_sw'] == 'Y' ? 'Y' : ''), '.checker')
				->addText("There are no relevant medical findings.")
				->newLine()
				->addObject($this->addCheck((string)$data['medical_finding_yes_sw'] == '1' ? 'Y' : ''), '.checker')
				->addText("Relevant medical findings are: <i>" . (string)$data['medical_finding_are'] . "</i>")
				->newLine()
				->addText('')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("<b>If not eligible for special education and related services OR the student does not need specially designed instruction,\n suggestions for interventions for the student: </b><i>" . $data['suggestions'] . "</i>")
			);

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
		}

		public function renderERBasisDeterm() {

			$this->rcDoc->addBookmark('Basic Determination of Eligibility');
			$data = $this->std->getConstructionGroupData(1, true, 2);
			foreach ($data as $construction) {
				$this->rcDoc->startNewPage();
				$xmlData = IDEADef::getConstructionTemplate($construction['constr_id']);
				$xmlData = preg_replace('/<pagebreak[^>]*>/', '<line><section></section></line>', $xmlData);
				$values = IDEACore::clearUTF8($construction['values']);
				$doc = IDEADocument::factory($xmlData);
				if ($values) {
					$doc->mergeValues($values);
				}
				$layout = RCLayout::factory()
					->newLine('.default')
					->addObject($doc->getLayout());
				$this->rcDoc->addObject($layout);
			}
		}

		public function renderERParicipants() {
			$this->rcDoc->startNewPage();
			$this->rcDoc->addBookmark('Eligibility Meeting Participants');
			$participants = IDEACore::clearUTF8($this->std->getParticipants());
			$sld_memb = IDEACore::clearUTF8($this->std->getSLDMember());
			$layout = RCLayout::factory();

			$tbl = RCTable::factory()
				->setCol()
				->border(1)
				->addCell(RCLayout::factory()
						->addText('ELIGIBILITY MEETING PARTICIPANTS', '.h2'),
					'.grayed'
				);

			$tbl->addRow('.default');

			$part_tbl = RCTable::factory()
				->border(1)
				->setCol()
				->setCol()
				->addCell('<b>Name</b>')
				->addCell('<b>Role</b>');
			foreach ($participants as $participant) {
				$part_tbl
					->addRow('[height: 90px;]')
					->addCell((string)$participant['part_name'], 'italic')
					->addCell((string)$participant['part_role'] . PHP_EOL);
			}

			$tbl->addCell(
				RCLayout::factory()
				->addText("The following team of qualified professionals and the parent of the child have reviewed the evaluation data and participated in the determination of initial or continued eligibility for special education and related services.")
				->newLine()
				->addObject($part_tbl)
			);

			$sld_exists = false;
			foreach($sld_memb as $key => $value){
				if ($value) {
					$sld_exists = true;
				}
			};

			if ($sld_exists) {
				$sld = RCLayout::factory()
					->addText("<b>*ONLY for SPECIFIC LEARNING DISABILITY,</b> the eligibility determination team MUST include the following team members.  With the exception of the parent, each team member MUST certify in writing whether the report reflects his/her conclusion(s).  If a team member disagrees with the determination, a dissenting statement describing the team member's conclusion(s) must be attached:")
					->newLine()
					->addObject($this->addCheck((string)$sld_memb['regular_prof_sw'] == 'Y' ? 'Y' : ''), '.checker')
					->addText("<b>Regular Education Professional</b>")
					->newLine();

				$sld_tbl = RCTable::factory()
					->border(1)
					->setCol()
					->setCol()
					->setCol()
					->addCell('<b>Name</b>')
					->addCell('<b>Role</b>')
					->addCell('<b>Conclusion(s)</b>')
					->addRow()
					->addCell('<i>'. $sld_memb['regular_prof_namerole'] . '</i>')
					->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$sld_memb['regular_edu_teacher'] == 'Y' ? 'Y' : ''), '.checker')
					->addText("Child's Regular Education Teacher OR")
					->newLine()
					->addObject($this->addCheck((string)$sld_memb['regular_edu_classroom'] == 'Y' ? 'Y' : ''), '.checker')
					->addText('If the Child does not have a Regular Education Teacher, a regular classroom teacher qualified to teach a child of his/her age OR')
					->newLine()
					->addObject($this->addCheck((string)$sld_memb['regular_edu_ind'] == 'Y' ? 'Y' : ''), '.checker')
					->addText('For a Child less than school age, an individual qualified to teach a child of that age')
				)->addCell(RCLayout::factory()
				->addObject($this->addCheck((string)$sld_memb['regular_prof_agree'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Agree')
				->addObject($this->addCheck((string)$sld_memb['regular_prof_agree'] == 'N' ? 'Y' : ''), '.checker')
				->addText('Disagree')
				->newLine()
				->addText('')
				->newLine()
				->addText('Initials (if no signature): <i>' . $sld_memb['edu_initials'] . '</i>')
			);

				$sld->addObject($sld_tbl);

				$sld->newLine()
					->addObject($this->addCheck((string)$sld_memb['assess_prof_sw'] == 'Y' ? 'Y' : ''), '.checker')
					->addText("<b>Assessment Professional</b>")
					->newLine();

				$sld_tbl = RCTable::factory()
					->border(1)
					->setCol()
					->setCol()
					->setCol()
					->addCell('<b>Name</b>')
					->addCell('<b>Role</b>')
					->addCell('<b>Conclusion(s)</b>')
					->addRow()
					->addCell('<i>' . $sld_memb['assess_prof_namerole'] . '</i>')
					->addCell('At least one person qualified to conduct individual diagnostic examinations of children')
					->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$sld_memb['assess_prof_agree'] == 'Y' ? 'Y' : ''), '.checker')
					->addText('Agree')
					->addObject($this->addCheck((string)$sld_memb['assess_prof_agree'] == 'N' ? 'Y' : ''), '.checker')
					->addText('Disagree')
					->newLine()
					->addText('')
					->newLine()
					->addText('Initials (if no signature): <i>' . $sld_memb['prof_initials'] . '</i>')
				);

				$sld->addObject($sld_tbl);

				$sld->newLine()
					->addObject($this->addCheck((string)$sld_memb['assess_qual_sw'] == 'Y' ? 'Y' : ''), '.checker')
					->addText("<b>Additional Qualified Professionals</b>")
					->newLine();

				$sld_tbl = RCTable::factory()
					->border(1)
					->setCol()
					->setCol()
					->setCol()
					->addCell('<b>Name</b>')
					->addCell('<b>Role</b>')
					->addCell('<b>Conclusion(s)</b>')
					->addRow()
					->addCell('<i>' . $sld_memb['assess_qual_namerole'] . '</i>')
					->addCell('<i>' . $sld_memb['assess_qual_role'] . '</i>')
					->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$sld_memb['assess_qual_agree'] == 'Y' ? 'Y' : ''), '.checker')
					->addText('Agree')
					->addObject($this->addCheck((string)$sld_memb['assess_qual_agree'] == 'N' ? 'Y' : ''), '.checker')
					->addText('Disagree')
					->newLine()
					->addText('')
					->newLine()
					->addText('Initials (if no signature): <i>' . $sld_memb['qual_initials'] . '</i>')
				)
				->addRow()
				->addCell('<i>' . $sld_memb['assess_qual_namerole_sec'] . '</i>')
				->addCell('<i>' . $sld_memb['assess_qual_role_sec'] . '</i>')
				->addCell(RCLayout::factory()
				->addObject($this->addCheck((string)$sld_memb['assess_qual_agree_sec'] == 'Y' ? 'Y' : ''), '.checker')
				->addText('Agree')
				->addObject($this->addCheck((string)$sld_memb['assess_qual_agree_sec'] == 'N' ? 'Y' : ''), '.checker')
				->addText('Disagree')
				->newLine()
				->addText('')
				->newLine()
				->addText('Initials (if no signature): <i>' . $sld_memb['qual_initials_sec'] . '</i>')
			);

				$sld->addObject($sld_tbl);

				$tbl->addRow('.default');
				$tbl->addCell($sld);
			}

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);

		}

		public function renderERCopyOfReport() {
			$this->rcDoc->addBookmark('Provide Copy of ER');
			$data = IDEACore::clearUTF8($this->std->getProvideCopy());
			$layout = RCLayout::factory();

			$tbl = RCTable::factory('.default')
				->setCol()
				->border(1)
				->addCell(RCLayout::factory()
					->addText('A copy of the evaluation report including documentation of determination of eligibility was provided to the parent(s)/guardian(s) by:')
					->newLine()
					->addText('Name/Title: <i>' . (string)$data['nametitle'] . '</i>' . ' on Date: <i>' . ($data['date_provided'] != '' ? CoreUtils::formatDateForUser($data['date_provided']) : '') . '</i>')
				);

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
		}
	}
