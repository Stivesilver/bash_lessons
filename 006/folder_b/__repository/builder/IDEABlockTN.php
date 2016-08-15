<?php

	/**
	 * Class for creation blocks in TN builder(State TN).
	 *
	 * @author Alex Kalevich
	 * Created 17-02-2015
	 */
	class IDEABlockTN extends IDEABlock {

		/**
		 * @var IDEAStudentTN
		 */
		protected $std;

		public function __construct() {
			$this->idBlock = IDEABlockBuilder::TN;
			parent::__construct();
		}

		public function setStd($tsRefID, $iepyear = null) {
			$this->std = new IDEAStudentTN($tsRefID);
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
			//			$layout = RCLayout::factory()
			//				->newLine()
			//				->addText('Child Name:', '[width: 5%;] right')
			//				->addText((string)$this->std->get('stdname'), 'italic center [border-bottom: 1px solid black; width: 15%;]');
			//
			//			$this->rcDoc->setPageHeader($layout);
			$this->rcDoc->setPageFooter(
				RCLayout::factory()
					->addText('[PN]', 'right')
				, '[font-size: 6px;]'
			);
		}

		/**
		 * Generate block Cover Page for IFSP doc
		 */
		public function renderCoverPage() {
			$info = $this->std->getCoverPage();
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory()
				->newLine()
				->addText('INDIVIDUALIZED FAMILY SERVICE PLAN', 'bold center');

			$headLeft = RCLayout::factory()
				->newLine()
				->addText('Child Name:', '[width: 14%;] left')
				->addText((string)$this->std->get('stdname'), 'italic left [border-bottom: 1px solid black; width: 86%;]')
				->newLine()
				->addText('Birthdate:', '[width: 11%;] left')
				->addText((string)$this->std->get('stddob'), 'italic left [border-bottom: 1px solid black; width: 89%;]');

			$headRight = RCLayout::factory()
				->newLine()
				->addText('IFSP Meeting Date:', '[width: 21%;]')
				->addText((string)$this->std->get('stdiepmeetingdt'), 'italic [border-bottom: 1px solid black; width: 79%;]')
				->newLine()
				->addText('IFSP Type:', '[width: 12%;] ')
				->addText((string)$info['siepmtdesc'], 'italic [width: 88%;]')
				->newLine()
				->addText('Designated Service Coordinator:', '[width: 32%;] ')
				->addText((string)$info['serv_coord'], 'italic [border-bottom: 1px solid black; width: 68%;]')
				->newLine()
				->addText('Service Coordinator Phone #:', '[width: 29%;] ')
				->addText((string)$info['phone_coord'], 'italic [border-bottom: 1px solid black; width: 71%;]');

			$coverHead = RCLayout::factory()
				->addObject($headLeft, '[width: 50%; padding-right: 25px]')
				->addObject($headRight, '[width:50%]');

			$secLeft = RCLayout::factory()
				->newLine()
				->addText('')
				->newLine()
				->addText('Six Month Review')
				->newLine()
				->addText('Annual IFSP');

			$secRightOne = RCLayout::factory()
				->newLine()
				->addText('Date Due', 'center')
				->newLine()
				->addText((string)$info['review_due'], 'italic center [border-bottom: 1px solid black;]')
				->newLine()
				->addText((string)$info['annual_due'], 'italic center [border-bottom: 1px solid black;]')
				->newLine();

			$secRightTwo = RCLayout::factory()
				->newLine()
				->addText('Date Completed', 'center')
				->newLine()
				->addText((string)$info['review_end'], 'italic center [border-bottom: 1px solid black;]')
				->newLine()
				->addText((string)$info['annual_end'], 'italic center [border-bottom: 1px solid black;]')
				->newLine();

			$secRight = RCLayout::factory()
				->newLine()
				->addObject($secRightOne, '[width: 50%; padding-right: 15px]')
				->addObject($secRightTwo, '[width: 50%;]');

			$secBlock = RCLayout::factory()
				->addObject($secLeft, '[width: 55%; padding-left:40px;]')
				->addObject($secRight, '[width:45%;]');

			$layout
				->newLine()
				->addObject($coverHead)
				->newLine()
				->addObject($secBlock)
				->newLine()
				->addText("Additional Review Dates: <i>" . (string)$info['add_review'] . "</i>")
				->newLine('')
				->addText('Transition Dates', 'bold center');

			$threeLeft = RCLayout::factory()
				->newLine()
				->addText('')
				->newLine()
				->addText('Notification of Local Education Agency (LEA) by age two.')
				->newLine()
				->addText("Planning Conference with Parent/s, Lead Agency, LEA and other Service Providers, as appropriate. (At least 90 days, or up to 6 months prior to child's third birthday)")
				->newLine()
				->addText('Transition to LEA, as appropriate.');

			$threeRightOne = RCLayout::factory()
				->newLine()
				->addText('Date Due', 'center')
				->newLine()
				->addText((string)$info['lea_nottif_due'], 'italic center [border-bottom: 1px solid black;]')
				->newLine()
				->addText((string)$info['lea_plann_due'], 'italic center [border-bottom: 1px solid black;]')
				->newLine()
				->addText((string)$info['lea_trans_due'], 'italic center [border-bottom: 1px solid black;]')
				->newLine();

			$threeRightTwo = RCLayout::factory()
				->newLine()
				->addText('Date Completed', 'center')
				->newLine()
				->addText((string)$info['lea_nottif_end'], 'italic center [border-bottom: 1px solid black;]')
				->newLine()
				->addText((string)$info['lea_plann_end'], 'italic center [border-bottom: 1px solid black;]')
				->newLine()
				->addText((string)$info['lea_trans_end'], 'italic center [border-bottom: 1px solid black;]')
				->newLine();

			$threeRight = RCLayout::factory()
				->newLine()
				->addObject($threeRightOne, '[width: 50%; padding-right: 15px]')
				->addObject($threeRightTwo, '[width: 50%;]');

			$threeBlock = RCLayout::factory()
				->addObject($threeLeft, '[width: 55%;]')
				->addObject($threeRight, '[width:45%;]');

			$layout
				->newLine()
				->addObject($threeBlock)
				->newLine()
				->addText('Natural Environments/Settings', 'bold center')
				->newLine()
				->addText("To the maximum extent appropriate, services will be provided in natural environments, including the home, and community settings that are natural or normal for the child's age peers who have no disabilities. Natural environments for young children are those environments/situations that are within the context of the family's lifestyle - their home, their culture, daily activities, routines and obligations. Services will only be provided in settings not identified as the natural environment when it is determined that the desired outcome/s cannot be satisfactorily achieved within the natural environment of this child and family.")
				->newLine()
				->addText('The natural environment for', '[width: 14%;]')
				->addText((string)$info['nat_enviroment'], 'italic [border-bottom: 1px solid black;]')
				->addText('includes the following places/settings:')
				->newLine()
				->addText((string)$info['follow_place'], 'italic [border-bottom: 1px solid black;]');

			$this->rcDoc->addObject($layout);
		}

		public function renderIdentInfo() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory();

			$layoutLeft = RCLayout::factory()
				->newLine()
				->addText('INDIVIDUALIZED FAMILY SERVICE PLAN', 'bold center')
				->newLine()
				->addText("Child's Name:", '[ width:19%;]')
				->addText((string)$this->std->get('stdname'), 'italic [border-bottom: 1px solid black; width:40%;]')
				->newLine()
				->addText("Child's Birthdate:", '[ width:22%;]')
				->addText((string)$this->std->get('stddob'), 'italic [border-bottom: 1px solid black; width:20%;]')
				->addText("Child's Social Security #:", '[ width:30%;]')
				->addText((string)$this->std->get('stdfedidnmbr'), 'italic [border-bottom: 1px solid black; width:20%;]')
				->newLine()
				->addText("Child's Address:", '[ width:21%;]')
				->addText((string)$this->std->get('stdhadr1'), 'italic [border-bottom: 1px solid black; width:71%;]')
				->newLine()
				->addText("City:", '[ width:8%;]')
				->addText((string)$this->std->get('stdhcity'), 'italic [border-bottom: 1px solid black; width:20%;]')
				->addText("TN  Zip:", '[ width:11%;]')
				->addText((string)$this->std->get('stdhzip'), 'italic [border-bottom: 1px solid black; width:20%;]')
				->newLine()
				->addText("Phone:", '[ width:11%;]')
				->addText((string)$this->std->get('stdhphn'), 'italic [border-bottom: 1px solid black; width:25%;]')
				->addText("County:", '[ width:11%;]')
				->addText((string)$this->std->get('stdhcity'), 'italic [border-bottom: 1px solid black; width:20%;]');

			$parents = $this->std->getGuardians();

			foreach ($parents as $parent) {
				$layoutLeft->newLine()
					->addText("Parent's Name(s):", '[ width:23%;]')
					->addText((string)$parent['gdfnm'] . ' ' . $parent['gdlnm'], 'italic [border-bottom: 1px solid black; width:69%;]')
					->newLine()
					->addText("Parent's Address (if different from child):", '[ width:48%;]')
					->addText($parent['gdadr1'] == $this->std->get('stdhadr1') ? '' : (string)$parent['gdadr1'], 'italic [border-bottom: 1px solid black; width:45%;]')
					->newLine()
					->addText("City:", '[ width:8%;]')
					->addText((string)$parent['gdcity'], 'italic [border-bottom: 1px solid black; width:20%;]')
					->addText("TN  Zip:", '[ width:11%;]')
					->addText((string)$parent['gdcitycode'], 'italic [border-bottom: 1px solid black; width:20%;]')
					->newLine()
					->addText("Phone:", '[ width:11%;]')
					->addText((string)$parent['gdhphn'], 'italic [border-bottom: 1px solid black; width:25%;]');
			}

			$xmlData = IDEADef::getConstructionTemplate(199);
			$values = $this->std->getConstruction(199, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}

			$layout->newLine()
				->addObject($layoutLeft, '[width:40%;]')
				->addObject($doc->getLayout(), '[width:60%;]');

			$layout->newLine()
				->addText('DOCUMENTATION', 'bold center');

			# styles for table
			$cols = array(
				90 => new RCStyle('bold center [width: 30%;]'),
				70 => new RCStyle('bold center [border-left: 1px solid black; width: 20%;]'),
				50 => new RCStyle('bold center [border-left: 1px solid black; width: 15%;]'),
				20 => new RCStyle('bold center [border-left: 1px solid black; width: 10%;]')
			);

			$tbl = RCTable::factory('.table')
				->addRow('.row [border: 1px solid black;]')
				->addCell("IFSP Team Member - If present, sign \n If not present, list member's name", $cols[90])
				->addCell('Agency/Title', $cols[70])
				->addCell('Date' . PHP_EOL . '(mm/dd/yyyy)', $cols[20])
				->addCell("Contributed/ \n not present/method", $cols[50])
				->addCell("Fully \n Agree", $cols[20])
				->addCell("Area(s) of Concerns/ \n Comments", $cols[50]);

			$members = $this->std->getTeamMembers();

			foreach ($members AS $member) {
				$tbl->addRow('.row')
					->addCell((string)$member['participantname'], 'left italic [font-weight:normal;]')
					->addCell((string)$member['participantrole'], 'left italic [font-weight:normal;]')
					->addCell((string)$member['participantdate'], 'left italic [font-weight:normal;]')
					->addCell((string)$member['participantatttype'], 'left italic [font-weight:normal;]')
					->addCell((string)$member['participantagree'], 'left italic [font-weight:normal;]')
					->addCell((string)$member['participantcomment'], 'left italic [font-weight:normal;]');

			}
			$layout->newLine()
				->addObject($tbl);

			$xmlData = IDEADef::getConstructionTemplate(195);
			$values = $this->std->getConstruction(195, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}

			$layout->newLine()
				->addObject($doc->getLayout());

			$xmlData = IDEADef::getConstructionTemplate(197);
			$values = $this->std->getConstruction(197, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}

			$layout->newLine()
				->addObject($doc->getLayout());

			$this->rcDoc->addObject($layout);
		}

		public function renderPLOP() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory()
				->newLine()
				->addText("PRESENT LEVELS OF DEVELOPMENT Child's Name", 'bold center')
				->newLine()
				->addText('(Include a statement of functional strengths & needs in each area)', 'bold center');

			# styles for table
			$cols = array(
				90 => new RCStyle('bold center [width: 10%;]'),
				50 => new RCStyle('bold center [border-left: 1px solid black; width: 15%;]'),
				20 => new RCStyle('bold center [border-left: 1px solid black; width: 50%;]'),
				30 => new RCStyle('bold center [border-left: 1px solid black; width: 10%;]')
			);

			$tbl = RCTable::factory('.table')
				->addRow('.row [border: 1px solid black;]')
				->addCell("Date" . PHP_EOL . '(mm/dd/yyyy)', $cols[90])
				->addCell("Area", $cols[50])
				->addCell("Strength", $cols[20])
				->addCell('By', $cols[30])
				->addCell('Needs', $cols[50]);

			$plods = $this->std->getPLOAD();

			foreach ($plods AS $pload) {
				$tbl->addRow('.row')
					->addCell((string)$pload['pgdate'], 'left italic [font-weight:normal;]')
					->addCell((string)$pload['tsndesc'], 'left italic [font-weight:normal;]')
					->addCell((string)$pload['strengths'], 'left italic [font-weight:normal;]')
					->addCell((string)$pload['pglpnarrative'], 'left italic [font-weight:normal;]')
					->addCell((string)$pload['concerns'], 'left italic [font-weight:normal;]');

			}

			$layout->newLine()
				->addObject($tbl);

			$this->rcDoc->addObject($layout);

		}

		public function renderSummaryFamily() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory()
				->newLine()
				->addText('SUMMARY OF FAMILY RESOURCES, PRIORITIES, AND CONCERNS RELATED TO ENHANCING THE DEVELOPMENT OF THE CHILD', 'bold center');

			$xmlData = IDEADef::getConstructionTemplate(203);
			$values = $this->std->getConstruction(203, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}
			$layout->newLine()
				->addObject($doc->getLayout());

			$this->rcDoc->addObject($layout);
		}

		public function renderOutcomeActionSteps() {

			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory()
				->newLine()
				->addText("OUTCOME/ACTION STEPS", 'bold center');

			$goals = $this->std->getBgbGoals();

			$cols = array(
				70 => new RCStyle('bold center [width: 70%;]'),
				30 => new RCStyle('bold center [border-left: 1px solid black; width: 30%;]')
			);

			foreach ($goals AS $goal) {
				$layout->newLine()
					->addText('Major Outcome #', '[width:9%;]')
					->addText((string)$goal['g_num'] . ' ' . (string)$goal['gsentance'], 'italic [border-bottom: 1px solid black; width:70%;]')
					->addText('Timeline (Target Date)', '[width:11%;]')
					->addText((string)$goal['gdate'], 'italic [border-bottom: 1px solid black; width:10%;]');

				if ($goal['objectives']) {

					$tbl = RCTable::factory('.table')
						->addRow('.row [border: 1px solid black;]')
						->addCell("Action Steps", $cols[70])
						->addCell("Person(s) Responsible", $cols[30]);

					foreach ($goal['objectives'] AS $obj) {
						$tbl->addRow('.row')
							->addCell((string)$obj['b_num_goal'] . ' ' . (string)$obj['bsentance'], 'left italic [font-weight:normal;]')
							->addCell((string)$obj['in_support'], 'left italic [font-weight:normal;]');

					}
				}
				$layout->newLine()
					->addObject($tbl);
			}

			$layout
				->newLine()
				->addText("Review/Changes", 'bold center');

			$reviews = $this->std->getReviewChanges();
			foreach ($reviews AS $review) {
				$layout->newLine()
					->addText('*' . (string)$review['rew_stat_key'] . ' Review Status', '[width:9%;]')
					->addText((string)$review['rew_comment'], 'italic [border-bottom: 1px solid black; width:80%;]')
					->addText('Date:', '[width:3%;]')
					->addText((string)$review['rew_date'], 'italic [border-bottom: 1px solid black; width:6%;]');
			}

			$layout->newLine()
				->addText('*Review Status Key (1) on going (2) completed (3) delayed (4) unavailable (for non-required services only) (5) modified Revised 6/22/98 State of Tennessee', 'bold [padding-top: 10px;]');

			$this->rcDoc->addObject($layout);
		}

		public function renderServices() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory()
				->newLine()
				->addText("SERVICES", 'bold center');

			$tbl = RCTable::factory()
				->border(1)
				->setCol('16%')
				->setCol('6%')
				->setCol('14%')
				->setCol('6%')
				->setCol('6%')
				->setCol('6%')
				->setCol('8%')
				->setCol('6%')
				->setCol('6%')
				->setCol('14%')
				->setCol('6%')
				->setCol('6%')
				->addRow()
				->addCell("Service", 'center', 1)
				->addCell("Outcome \n #/s", 'center', 1)
				->addCell('Provider', 'center', 1)
				->addCell("Required \n or \n Non/Req", 'center', 1)
				->addCell("Starting \n Date", 'center', 1)
				->addCell("Expected \n Duration", 'center', 1)
				->addCell("METHOD", 'center', 3)
				->addCell("Payor", 'center', 1)
				->addCell("Review \n Date", 'center', 1)
				->addCell("*Review \n Status", 'center', 1);

			$tbl->addRow()
				->addRowSpanCell()
				->addRowSpanCell()
				->addRowSpanCell()
				->addRowSpanCell()
				->addRowSpanCell()
				->addRowSpanCell()
				->addCell("Environment", 'center')
				->addCell("Frequency", 'center')
				->addCell('Intensity', 'center')
				->addRowSpanCell()
				->addRowSpanCell()
				->addRowSpanCell();

			$services = $this->std->getServices();

			foreach ($services as $service) {

				$tbl->addRow()
					->addCell((string)$service['nsdesc'], 'italic')
					->addCell((string)$service['goals'], 'italic')
					->addCell((string)$service['stn_provider'], 'italic')
					->addCell((string)$service['stn_required_sw'], 'italic')
					->addCell((string)$service['stn_begdate'], 'italic')
					->addCell((string)$service['stn_enddate'], 'italic')
					->addCell((string)$service['crtdesc'], 'italic')
					->addCell((string)$service['sfdesc'], 'italic')
					->addCell((string)$service['validvalue'], 'italic')
					->addCell((string)$service['stn_payor'], 'italic')
					->addCell((string)$service['stn_revdate'], 'italic')
					->addCell((string)$service['status'], 'italic');
			}

			$tbl->beginTableBody();

			$layout->newLine()
				->addObject($tbl, '[padding-bottom:10px;]');

			$layout->newLine()
				->addText("Justification for Provision of Service in Environments/Settings not Identified as the Natural Environment", 'bold center');

			$justifications = $this->std->getJustificationForProvision();
			foreach ($justifications AS $justf) {
				$layout->newLine()
					->addText('Service:', '[width:5%;]')
					->addText((string)$justf['txt01'], 'italic [border-bottom: 1px solid black; width:15%;]')
					->addText('Options Considered', '[width:10%;]')
					->addText((string)$justf['txt02'], 'italic [border-bottom: 1px solid black; width:70%;]')
					->newLine()
					->addText('The desired outcome could not be achieved in the natural environment because:', '[width:38%;]')
					->addText((string)$justf['txt03'], 'italic [border-bottom: 1px solid black; width:62%;]')
					->newLine()
					->addText('')
					->newLine();
			}

			$this->rcDoc->addObject($layout);
		}

		public function renderReviewChangeForm() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory();

			$xmlData = IDEADef::getConstructionTemplate(207);
			$values = $this->std->getConstruction(207, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}
			$layout->newLine()
				->addObject($doc->getLayout());

			$this->rcDoc->addObject($layout);
		}

		public function renderTransition() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory();

			$xmlData = IDEADef::getConstructionTemplate(209);
			$values = $this->std->getConstruction(209, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}
			$layout->newLine()
				->addObject($doc->getLayout());

			$layout->newLine()
				->addText('TRANSITION FROM PART C SERVICES PLAN', 'bold center');

			$tbl = RCTable::factory()
				->border(1)
				->setCol('60%')
				->setCol('20%')
				->setCol('10%')
				->setCol('10%')
				->addRow()
				->addCell("Planned Transitioning Procedures", 'center', 1)
				->addCell("Implementor", 'center', 1)
				->addCell('Timeframe', 'center', 1)
				->addCell("Date \n Completed", 'center', 1);

			$transitions = $this->std->getTransition();

			foreach ($transitions as $transition) {
				$tbl->addRow()
					->addCell((string)$transition['txt01'], 'italic')
					->addCell((string)$transition['txt02'], 'italic')
					->addCell((string)$transition['txt03'], 'italic')
					->addCell((string)$transition['dat01'], 'italic');
			}

			$tbl->beginTableBody();

			$layout->newLine()
				->addObject($tbl);

			$layout->newLine()
				->addText('IFSP CONFERENCE NOTES', 'bold center');

			$tbl = RCTable::factory()
				->border(1)
				->setCol('100%');

			$tbl->beginTableBody();

			$layout->newLine()
				->addObject($tbl);

			$notes = $this->std->getConferenceNotes();

			foreach ($notes as $note) {
				$tbl->addRow()
					->addCell((string)$note['cntext'], 'italic');
			}

			$xmlData = IDEADef::getConstructionTemplate(211);
			$values = $this->std->getConstruction(211, true);
			$doc = IDEADocument::factory($xmlData);
			if ($values) {
				$doc->mergeValues(base64_decode($values['values']));
			}
			$layout->newLine()
				->addObject($doc->getLayout());

			$this->rcDoc->addObject($layout);
		}

		public function renderOutcomeServicesSummary() {
			$this->rcDoc->startNewPage();

			$layout = RCLayout::factory()
				->newLine()
				->addText("OUTCOME/SERVICE SUMMARY PAGE (Optional)", 'bold center');
			$servs = $this->std->getServices();
			$justifs = $this->std->getJustificationForProvision();
			$goals = $this->std->getOutcomeSummary();
			$tbl = RCTable::factory()
				->border(1);
			if ($goals) {
				$sum = count($servs) + count($justifs);
				if ($sum) {
					$tbl->setCol('40%');
					$colwidth = 60 / $sum;
					for ($i = 1; $i <= $sum; $i++) {
						$tbl->setCol($colwidth . '%');
					}
					$tbl->addRow()
						->addCell("", 'center', 1);

					if ($servs) {
						$tbl->addCell("Services to be Provided (required by Part C)", 'center', count($servs));
					}
					if ($justifs) {
						$tbl->addCell('Non-req. Services', 'center', count($justifs));
					}
				} else {
					$tbl->setCol('100%');
				}


				$tbl->addRow()
					->addCell('MAJOR OUTCOME', 'center bold');

				if ($servs) {
					foreach ($servs as $serv) {
						$tbl->addCell((string)$serv['nsdesc']);
					}
				}

				if ($justifs) {
					foreach ($justifs as $justif) {
						$tbl->addCell((string)$justif['txt01']);
					}
				}

				foreach ($goals AS $goal) {
					$tbl->addRow()
						->addCell((string)$goal['gsentance']);
					foreach ($servs as $serv) {
						$desc = $this->execSQL("
						SELECT stsr_provided
						  FROM webset.std_tn_serv_summ_rc
						 WHERE grefid = " . $goal['grefid'] . "
						   AND stn_refid = " . $serv['stn_refid'] . "
					")->getOne();
						$tbl->addCell((string)$desc, 'italic');
					}
					foreach ($justifs as $justif) {
						$desc = $this->execSQL("
						SELECT stsn_provided
						  FROM webset.std_tn_serv_summ_nr
						 WHERE grefid = " . $goal['grefid'] . "
						   AND sns_refid = " . $justif['refid'] . "
					")->getOne();
						$tbl->addCell((string)$desc, 'italic');
					}
				}

				$tbl->beginTableBody();

				$layout->newLine()
					->addObject($tbl);
			}
			$this->rcDoc->addObject($layout);
		}

	}
