<?php

	/**
	 * IDEABlockEvalUT.php
	 * Class for creation blocks in UT Evaluation builder.
	 *
	 * @author Alex Kalevich
	 * Created 12-14-2015
	 */
	class IDEABlockEvalUT extends IDEABlockEval {

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


			if (IDEACore::disParam(114) == "Y") {
				$tbl->addRow('.default');
				$tbl->addCell(RCLayout::factory()
					->addText("Copy of Bill of Rights given to parent(s) on: <i>" . ($this->std->get('parentrightdt') != '' ? $this->std->get('parentrightdt') : '') . "</i>")
					->addText("Procedural Safeguards given to parent(s) on: <i>" . ($this->std->get('stdprocsafeguarddt') != '' ? $this->std->get('stdprocsafeguarddt') : '') . "</i>")
				);
			}

			$layout
				->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);
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

	}
