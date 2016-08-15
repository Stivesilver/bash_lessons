<?php

	/**
	 * IDEABlockRED.php
	 * Class for creation blocks in RED builder.
	 *
	 * @author Alex Kalevich
	 * Created 09-07-2015
	 */
	class IDEABlockRED extends IDEABlockEval {
		public function setStd($tsRefID, $iepyear = null, $params = null) {
			$this->std = new IDEAStudentRED($tsRefID);
			$this->student_params = $params;
			# set data about student to header. Because header use info about student.
			$this->setHeaderDoc();
		}

		public function setHeaderDoc() {
			$data = $this->std->getREDGenInfo();
			$report_type = '';
			if ($data['red_data_review'] == 'E') {
				$report_type = 'Initial Evaluation';
			} elseif ($data['red_data_review'] == 'R') {
				$report_type = 'Reevaluation';
			} elseif ($data['red_data_review'] == 'O') {
				$report_type = $data['red_data_review_o'];
			}
			$report_date = $this->student_params['report_date'] ? CoreUtils::formatDateForUser($this->student_params['report_date']) : '';

			$layout = RCLayout::factory()
				->newLine('.header')
				->addText('Student:', '[width: 7%;] right')
				->addText((string)$this->std->get('stdname'), 'italic center [border-bottom: 1px solid black; width: 15%;]')
				->addText('DOB:', '[margin-left: 10px; width: 6%;] right')
				->addText((string)$this->std->get('stddob'), 'italic center [border-bottom: 1px solid black; width: 10%;]')
				->addText('', '[margin-left: 10px; width: 42%;] right')
				->addText('Date:', '[margin-left: 10px; width: 9%;] right')
				->addText($report_date, 'italic center [border-bottom: 1px solid black; width: 10%;]');

			$this->rcDoc->setPageHeader($layout);
			$this->rcDoc->setPageFooter(
				RCLayout::factory()
					->addText('Updated July 10, 2014', '.docfont [width: 230px;] italic [font-size: 8px;]')
					->addText('[PN]', '.default right')
			);
		}

		public function renderREDGenInfo() {
			$this->rcDoc->startNewPage();
			$this->rcDoc->addBookmark('General Information');
			$data = IDEACore::clearUTF8($this->std->getREDGenInfo());

			$layout = RCLayout::factory()
				->newLine()
				->addText((string)SystemCore::$VndName, '.h1')
				->newLine()
				->addText('Review of Existing Data Documentation Form', '.h1');

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
				->addText("Student's Name: <i>" . (string)$this->std->get('stdname') . "</i>")
				->addText("School District: <i>" . (string)SystemCore::$VndName . "</i>")
				->newLine()
				->addText("Date of Birth: <i>" . $this->std->get('stddob') . "</i>")
				->addText("Age: <i>" . $data['stdage'] . "</i>")
				->addText("Grade: <i>" . $data['stdgrade'] . "</i>")
				->newLine()
				->addText("Current Eligibility Category (for reevaluation ONLY): <i>" . (string)$data['currdisability'] . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("Date of Referral (either for initial evaluation OR parent referral for reevaluation): <i>" . ($data['referraldt'] != '' ? CoreUtils::formatDateForUser((string)$data['referraldt']) : '') . "</i>")
				->newLine()
					->addText("Date Review of Existing Data Decision is Finalized: <i>" . ($data['finalized_date'] != '' ? CoreUtils::formatDateForUser((string)$data['finalized_date']) : '') . "</i>")
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("This data review is being conducted as part of:")
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['red_data_review'] == 'E' ? 'Y' : ''), '.checker')
				->addText('an initial evaluation')
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['red_data_review'] == 'R' ? 'Y' : ''), '.checker')
				->addText('a required three year reevaluation ')
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['red_data_review'] == 'O' ? 'Y' : ''), '.checker')
				->addText('Other: <i>' . (string)$data['red_data_review_o'] . '</i>')
			);

			$tbl->addRow('.default');
			$tbl->addCell(RCLayout::factory()
				->addText("IEP team members and other qualified professional, as appropriate")
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['red_teammet'] == 'M' ? 'Y' : ''), '.checker')
				->addText('met' . ($data['red_teammet_dt'] && $data['red_teammet'] == 'M'  ? ' Date: <i>' . CoreUtils::formatDateForUser($data['red_teammet_dt']) . '</i>' : ''))
				->newLine()
				->addText('', '[width:7%]')
				->addObject($this->addCheck((string)$data['red_teammet'] == 'C' ? 'Y' : ''), '.checker')
				->addText('conferred' . ($data['red_teammet_dt'] && $data['red_teammet'] == 'C'  ? ' Date: <i>' . CoreUtils::formatDateForUser($data['red_teammet_dt']) . '</i>' : ''))
				->newLine()
				->addText('to review all relevant existing evaluation information in order to determine what additional data, if any, was needed to determine:')
				->newLine()
				->addText('', '[width:7%]')
				->addText('1. Whether the child has a particular category of disability or, in the case of a reevaluation, whether the child continues to have a disability.')
				->newLine()
				->addText('', '[width:7%]')
				->addText('2. The present levels of performance and educational needs of the student.')
				->newLine()
				->addText('', '[width:7%]')
				->addText('3. Whether the child needs special education and related services, or in the case of a reevaluation, whether the child continues to need special education and related services.')
				->newLine()
				->addText('', '[width:7%]')
				->addText('4. Whether any additions or modifications to the special education and related services are needed to enable the child to meet the measurable annual goals set out in the individualized education program of the child and to participate, as appropriate, in the general curriculum. ')
			);

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
		}

		public function renderREDSummary() {
			$this->rcDoc->addBookmark('Summary');
			$data = IDEACore::clearUTF8($this->std->getREDSummary());

			$layout = RCLayout::factory()
				->newLine('.default')
				->addText('<b>In making this determination, the following information was reviewed by the team: </b><i>(Note: Not all areas will have all data sources addressed)</i>')
				->newLine();

			$tbl = RCTable::factory('.table')
				->setCol('[width:25%;]')
				->setCol('[width:25%;]')
				->setCol('[width:50%;]')
				->border(1)
				->addCell(RCLayout::factory()
					->addText('<b>AREA/ DATA SOURCE</b>', 'center')
				)->addCell(RCLayout::factory()
					->addText("<b>TYPE AND DESCRIPTION OF DATA REVIEWED (Include name and date of the previous assessment if applicable)</b>", 'center')
				)->addCell(RCLayout::factory()
					->addText("<b>SUMMARY OF INFORMATION GAINED\n(Describe strengths and concerns)</b>", 'center')
				);

			foreach ($data as $item) {
				$tbl->addRow();
				$tbl->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$item['redrefid'] != "" ? 'Y' : ''), '.checker')
					->addText('<b>' . (string)$item['scrdesc'] . ': ' . (string)$item['scrlongdesc'] . '</b>')
					, null, 3);

				$tbl->addRow();
				$ds_layout = RCLayout::factory();

				foreach ($item['ds'] as $ds) {
					$ds_layout
						->addObject($this->addCheck((string)$ds['dsrefid'] != "" ? 'Y' : ''), '.checker')
						->addText($ds['ds_other'] != "" ? (string)$ds['datasource'] . ': <i>' . $ds['ds_other'] . '</i>' : (string)$ds['datasource'])
						->newLine();
				}
				$tbl->addCell($ds_layout);
				$tbl->addCell((string)$item['red_desc'], 'italic');
				$tbl->addCell((string)$item['red_text'], 'italic');

				$cell = RCLayout::factory()
					->addText("Further Assessment Information Needed?", "70%")
					->addObject($this->addCheck((string)$item['red_assneed'] == 'Y' ? 'Y' : ''), '.checker')
					->addText('Yes', '.width25')
					->addObject($this->addCheck((string)$item['red_assneed'] == 'N' ? 'Y' : ''), '.checker')
					->addText('No', '.width25')
					->addText('');
				if (IDEACore::disParam(99) == 'Y') {
					$cell
						->newLine()
						->addObject($this->addCheck((string)$item['plafp'] == 'Y' ? 'Y' : ''), '.checker')
						->addText("Information is needed to update present level of performance")
						->newLine()
						->addObject($this->addCheck($item['skill'] == 'Y' ? 'Y' : ''), '.checker')
						->addText("Skill level has been established")
						->newLine()
						->addObject($this->addCheck($item['skill'] == 'N' ? 'Y' : ''), '.checker')
						->addText("Skill level needs to be established");
				}

				$tbl->addRow();
				$tbl->addCell(
					$cell, null, 2
				);

				$tbl->addCell(RCLayout::factory()
					->addText("Assessment instruments, if known <i>" . $item['red_asstext'] . "</i>")
					);
			}

			$layout
				->newLine('.default')
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
		}

		public function renderREDConclusions() {
			$this->rcDoc->startNewPage();
			$this->rcDoc->addBookmark('Team Conclusions and Decisions');

			$data = IDEACore::clearUTF8($this->std->getREDConclusions());

			$layout = RCLayout::factory()
				->newLine()
				->addText('Team Conclusions and Decisions', '.h2 underline')
				->newLine('.default')
				->addText('Based upon the Review of Existing Data the Team made the following decisions:', 'bold center');

			$tbl = RCTable::factory('.table')
				->setCol()
				->setCol('[width:5%;]')
				->setCol()
				->border(1)
				->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$data['base_no_data'] == 2 ? 'Y' : ''), '.checker')
					->addText('<b>ADDITIONAL DATA IS NEEDED:</b>')
				)->addCell(RCLayout::factory()
					->addText("<b>OR</b>", 'center')
				)->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$data['base_no_data'] == 1 ? 'Y' : ''), '.checker')
					->addText('<b>NO ADDITIONAL DATA IS NEEDED:</b>')
				);

			$tbl->addRow();
			$tbl->addCell(RCLayout::factory()
				->addText("If checked, choose type of evaluation.", 'bold [background: #c0c0c0;]')
			)
				->addCell('')
				->addCell(RCLayout::factory()
					->addText("If checked, choose type of evaluation.", 'bold [background: #c0c0c0;]')
				);

			$tbl->addRow();
			$tbl->addCell(RCLayout::factory()
				->addObject($this->addCheck((string)$data['yes_data_evi'] == 1 ? 'Y' : ''), '.checker')
				->addText('For <b>Initial Evaluation</b>')
				->newLine()
				->addText('<i>MUST provide parent with prior written Notice of Action for intent to evaluate and provide a description of the areas to be assessed and the tests to be administered, if known. Parental consent is required to initiate the evaluation.</i>')
			)
				->addCell('')
				->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$data['no_data_evi'] == 1 ? 'Y' : ''), '.checker')
					->addText('For <b>Initial Evaluation</b>')
					->newLine()
					->addText('<i>MUST provide parent with prior written Notice of Action <b>and</b> obtain Parental consent <b>and</b> provide an Evaluation Report that includes an eligibility determination based on the Review of Existing Data.</i>')
				);

			$tbl->addRow();
			$tbl->addCell(RCLayout::factory()
				->addText("OR", 'bold [background: #c0c0c0;]')
			)
				->addCell('')
				->addCell(RCLayout::factory()
					->addText("OR", 'bold [background: #c0c0c0;]')
				);

			$tbl->addRow();
			$tbl->addCell(RCLayout::factory()
				->addObject($this->addCheck((string)$data['yes_data_evr'] == 1 ? 'Y' : ''), '[width: 5%;] .checker')
				->addText('For <b>Reevaluation:</b>')
				->newLine()
				->addText('<b>Additional data will be collected by administering assessment instrument(s) requiring written parental consent.</b>')
				->newLine()
				->addText('')
				->newLine()
				->addText('<i>MUST provide parent with prior written Notice of Action for intent to evaluate and provide a description of the areas to be assessed and the tests to be administered, if known. Parental consent is required to initiate the evaluation.</i>')
				->newLine()
				->addText('')
				->newLine()
				->addText('<i>However, IF parent does not respond to two attempts by the public agency to provide prior written Notices of Action for intent to reevaluate, the public agency can proceed with reevaluation after the second 10 day waiting period if the parents do not file for due process.</i>')
			)
				->addCell('')
				->addCell(RCLayout::factory()
					->addObject($this->addCheck((string)$data['no_data_evr'] == 1 ? 'Y' : ''), '[width: 5%;] .checker')
					->addText('For <b>Reevaluation:</b> <i>(MUST select one reason below)</i>')
					->newLine()
					->newLine()
					->addText('', '[width: 5%;]')
					->addObject($this->addCheck((string)$data['no_data_cur'] == 1 ? 'Y' : ''), '.checker')
					->addText('The current Identification of (disability and sub-areas within disability) <b><i>' . $data['no_data_curtext'] . '</i></b> continues to be appropriate and sufficient information exists on which to base educational decisions. MUST complete <i>"Parent Notification Regarding Results of Review of Existing Data Documentation Form"</i> (page 6 of the RED form) to provide prior written notice.')
					->newLine()
					->newLine()
					->addText('OR', 'center bold')
					->newLine()
					->addText('', '[width: 5%;]')
					->addObject($this->addCheck((string)$data['no_data_noevi'] == 1 ? 'Y' : ''), '.checker')
					->addText('Sufficient information exists on which to base the decision that (name of student) <i>' . $this->std->get('stdname') . '</i> does not continue to show evidence of the disability indicated in the initial or most recent evaluation and does not continue to need special education and related service.<sup>1</sup>')
					->newLine()
					->newLine()
					->addText('OR', 'center bold')
					->newLine()
					->addText('', '[width: 5%;]')
					->addObject($this->addCheck((string)$data['no_data_change'] == 1 ? 'Y' : ''), '.checker')
					->addText('Sufficient information exists to change the current identification FROM <b><i>' . $data['no_data_change_from'] . '</i></b> TO <b><i>' . $data['no_data_change_to'] . '</i></b><sup>1</sup>')
					->newLine()
					->addText('<sup>1</sup>MUST Provide parent with Notice of Action and an Evaluation Report that includes an eligibility determination based on the Review of Existing Data.', 'italic')
				);

			$layout
				->newLine('.default')
				->addObject($tbl);

			$layout
				->newLine('.default')
				->addText('The following individuals, meeting the requirements of an IEP team and other qualified professionals made the above determination on <i><b>' . $data['add_data_deter'] . '</b></i> (m/d/y) <i><b>(date of meeting or, if no meeting, indicate the date the decision is finalized)</b></i>')
				->newLine();

			#Participants
			$data = IDEACore::clearUTF8($this->std->getREDParticipants());
			$params = $this->std->getConstructionData(124);
			if (isset($params["signatures"]) && $params["signatures"] == 'on') {

				$tbl = RCTable::factory('.table')
					->setCol('[width:35%;]')
					->setCol('[width:35%;]')
					->setCol('[width:30%;]')
					->border(1)
					->addCell('Names', 'bold')
					->addCell('Signature', 'bold')
					->addCell('Title/Role of Team Members', 'bold');

				foreach ($data as $item) {
					$tbl->addRow()
						->addCell((string)$item['part_name'], 'italic')
						->addCell('')
						->addCell((string)$item['role'], 'italic');
				}
			} else {
				$tbl = RCTable::factory('.table')
					->setCol()
					->setCol()
					->border(1)
					->addCell('Names', 'bold')
					->addCell('Title/Role of Team Members', 'bold');

				foreach ($data as $item) {
					$tbl->addRow()
						->addCell((string)$item['part_name'], 'italic')
						->addCell((string)$item['role'], 'italic');
				}
			}
			$layout
				->newLine('.default')
				->addObject($tbl)
				->newLine('.default')
				->addText('*Required team participants for the Review of Existing Data - may NOT be excused', 'bold italic');

			$this->rcDoc->addObject($layout);
		}

		public function renderREDParentNotification() {

			$this->rcDoc->startNewPage();
			$this->rcDoc->addBookmark('Parent Notification');
			$xmlData = IDEADef::getConstructionTemplate(88);
			$values = $this->std->getConstructionData(88, true, 2);
			$values = IDEACore::clearUTF8($values);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues($values);
			}

			$layout = RCLayout::factory()
				->newLine('.default')
				->addObject($doc->getLayout());

			$this->rcDoc->addObject($layout);
		}
		public function renderREDDisabilities() {
			$xmlData = IDEADef::getConstructionTemplate(83);
			$values = $this->std->getConstructionData(83, true, 2);
			$values = IDEACore::clearUTF8($values);
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
