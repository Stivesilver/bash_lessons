<?php

	/**
	 * IDEABlock.php
	 * Common class for generation IEP blocks. Blocks use in IEP documents.
	 * Class have some properties & methods for all posible blocks.
	 * Specific blocks in apps or states use render functions for generation each block.
	 * This is functions add RCLayout|RCTable to common IEP Document.
	 * In apps user can select only one or many blocks for print.
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 26-12-2013
	 */
	abstract class IDEABlock extends RegularClass implements IOTraceableInterface {

		/**
		 * Blocks for PDF doc. Each block include name block and name function for render
		 *
		 * @var array
		 */
		protected $blocks;

		/**
		 * Object current student. Use for getting information about student
		 *
		 * @var IDEAStudent|IDEAStudentTX (or child)
		 */
		protected $std;

		/**
		 * Array with data. Data can be from POST\GET or small unique arrays from DB
		 *
		 * @var array
		 */
		protected $queryData;

		/**
		 * Object for building document. Ready doc.
		 *
		 * @var RCDocument
		 */
		protected $rcDoc;

		/**
		 * Image for selected checkbox
		 *
		 * @var RCImage
		 */
		protected $checked;

		/**
		 * Image for unselected checkbox
		 *
		 * @var RCImage
		 */
		protected $unchecked;

		/**
		 * Add information about marking periods in method progressReportGoals
		 *
		 * @var bool
		 */
		protected $printMarkingPeriods = true;

		/**
		 * Set blocks and summ blocks
		 */
		public function __construct() {
			$this->defineDefaultStyles();
			$this->setRcDoc();
			$this->checked = RCImage::factory(
				SystemCore::$physicalRoot . '/apps/idea/img/reportComposer/reduced_check.jpg'
			);
			$this->unchecked = RCImage::factory(
				SystemCore::$physicalRoot . '/apps/idea/img/reportComposer/reduced_uncheck.jpg'
			);
		}

		/**
		 * Define common styles which use IEP Builder
		 *
		 * @return void
		 */
		public static function defineDefaultStyles() {
			RCStyle::defineStyleClass('checker', '[width: 15px; height: 15px;]');
			RCStyle::defineStyleClass('width20', '[width: 20px;]');
			RCStyle::defineStyleClass('padtop5', '[padding-top: 5px;]');
			RCStyle::defineStyleClass('padbot10', '[padding-bottom: 10px;]');
			RCStyle::defineStyleClass('martop10', '[margin-top: 10px;]');
			RCStyle::defineStyleClass('row', '[border-bottom: 1px solid black;]');
			RCStyle::defineStyleClass('table', '[border: 1px solid black; margin-bottom: 10px;]');
			RCStyle::defineStyleClass(
				'hr',
				'[text-align: center; font-weight: bold; border-bottom: 1px solid black;]'
			);
			RCStyle::defineStyleClass(
				'next-hr',
				'[text-align: center; font-weight: bold; border-left: 1px solid black; border-bottom: 1px solid black;]'
			);
			RCStyle::defineStyleClass(
				'yes',
				'[width: 25px; padding-right: 5px; font-size: 10px; padding-top: 4px;]]'
			);
			RCStyle::defineStyleClass(
				'no',
				'[width: 25px; font-size: 10px; padding-top: 4px;]]'
			);
			RCStyle::defineStyleClass(
				'cellBorder',
				'[text-align: left; font-weight: normal; border-left: 1px solid black;]'
			);
		}

		/**
		 * Return array blocks with id block => name block
		 *
		 * @return array
		 */
		public function getBlocks() {
			$blocks = array();
			foreach ($this->blocks as $key => $block) {
				$blocks[$key] = $this->blocks[$key]['name'];
			}

			return $blocks;
		}

		/**
		 * Call render function for current block by key. Each function add to RCDoc object(RCLayout)
		 *
		 * @param string $keyBlock
		 * @throws Exception
		 * @return null
		 */
		public function render($block) {
			if ($block->renderFunction == '') {
				throw new Exception('Not exist renderFunction in db for block "' . $block->name . '"');
			} else {
				return call_user_func(array($this, $block->renderFunction));
			}
		}

		/**
		 * Set array to queryData
		 *
		 * @param array $array
		 */
		public function setQueryData($array) {
			$this->queryData = $array;
		}

		/**
		 * Set one value to queryData by name
		 *
		 * @param string $key
		 * @param mixed $val
		 */
		public function setQueryVal($key, $val) {
			$this->queryData[$key] = $val;
		}

		/**
		 * Add checker Yes | No to PDF
		 *
		 * @param RCLayout $layout
		 * @param string $val 'Y' | 'N'
		 */
		public function addYN(RCLayout $layout, $val) {
			if ($val == 'Y') {
				$layout->addObject($this->getChecked(), '.checker');
			} else {
				$layout->addObject($this->getUnchecked(), '.checker');
			}

			$layout->addText('Yes', '.yes');

			if ($val == 'N') {
				$layout->addObject($this->getChecked(), '.checker');
			} else {
				$layout->addObject($this->getUnchecked(), '.checker');
			}

			$layout->addText('No', '.no');
		}

		/**
		 * Add only image checkbox by value
		 *
		 * @param string $val
		 * @return RCImage
		 */
		public function addCheck($val) {
			if ($val == 'Y') {
				return $this->getChecked();
			} else {
				return $this->getUnchecked();
			}
		}

		/**
		 * Return object student
		 *
		 * @return IDEAStudent
		 */
		public function getStd() {
			return $this->std;
		}

		/**
		 * Return header with name office and info about student. Use in several blocks & builders
		 *
		 * @param string $header string after name office
		 * @param string $id some blocks use not ID#, but SS#
		 * @param string $title row after header.
		 * @param null|string $teacher
		 * @return RCLayout
		 */
		public function nameOffice($header, $id = '', $title = '', $teacher = null) {
			# parse string for row ID#(SS#)
			if ($id == '') {
				$id[0] = 'ID#: ';
				$id[1] = (string)$this->std->get('stdschid');
			} else {
				$id = explode(',', $id);
			}

			$layout = RCLayout::factory()
				->addObject(
					RCTable::factory()
						# name Office
						->addTitle(SystemCore::$VndName)
						->addTitle($header)
				);
			# if exist row with title add after header
			if ($title != '') $layout->newLine()->addText($title, $this->titleStyle());
			if ($teacher === null) $teacher = (string)$this->std->getLanguages('teachername');

			$layout->newLine()
				->addText('Student: ', new RCStyle('[width: 45px;]'))
				->addText(
					'<i>' . $this->std->get('stdname') . '</i>',
					new RCStyle('[width: 150px; border-bottom: 1px solid black;]')
				)
				->addText($id[0], new RCStyle('[width: 25px;]'))
				->addText('<i>' . $id[1] . '</i>', '[width: 100px; border-bottom: 1px solid black;]')
				->addText('DOB: ', new RCStyle('[width: 30px; padding-left: 5px;]'))
				->addText(
					'<i>' . $this->std->get('stddob') . '</i>',
					new RCStyle('[width: 215px; border-bottom: 1px solid black; margin-left: 5px;]')
				)
				->newLine()
				->addText('Campus: ', new RCStyle('[width: 45px;]'))
				->addText(
					'<i>' . $this->std->get('vouname') . '</i>',
					new RCStyle('[width: 120px; border-bottom: 1px solid black;]')
				)
				->addText('Grade: ', new RCStyle('[width: 55px; margin-left: 20px]'))
				->addText(
					'<i>' . $this->std->get('grdlevel') . '</i>',
					new RCStyle('[width: 50px; border-bottom: 1px solid black;]')
				)
				->addText('Teacher: ', new RCStyle('[width: 85px; padding-left: 45px;]'))
				->addText($teacher, 'italic [width: 210px; border-bottom: 1px solid black;]'
				);
			# not all blocks use reason. Reason exist in FIE builder(TX)
			if (isset($this->queryData['reason'])) {
				$layout->newLine()
					->addText('Reason for referral: ', new RCStyle('[width: 80px;]'))
					->addText('<i>' . $this->queryData['reason'] . '</i>', '[width: 485px; border-bottom: 1px solid black;]')
					->newLine();
			}

			return $layout;
		}

		/**
		 * Add styles to default styles for title block
		 *
		 * @param string|null $string css props
		 * @return RCStyle
		 */
		public function titleStyle($string = null) {
			return new RCStyle("[background: #C0C0C0; margin: 10px 0px 10px 0px; font-weight: bold; $string]");
		}

		/**
		 * Set object student for current block
		 *
		 * @param int $tsRefID id Student
		 * @param null $iepyear
		 * @return mixed
		 */
		abstract function setStd($tsRefID, $iepyear = null);

		/**
		 * Set selected blocks
		 *
		 * @param IDEADocumentBlock
		 * @return void
		 */
		public function setSelectedBlocks($blocks) {
			$this->blocks = array();
			if (is_array($blocks)) {
				foreach ($blocks as $block) {
					if (!is_a($block, 'IDEADocumentBlock')) {
						throw new Exception('Block parameter should have IDEADocumentBlock type'); 
					}
					$this->blocks[] = $block;
				}
			} else {
					if (!is_a($blocks, 'IDEADocumentBlock')) {
						throw new Exception('Block parameter should have IDEADocumentBlock type'); 
					}
					$this->blocks[] = $blocks;
			}
		}

		/**
		 * Set RCDoc
		 *
		 * @param int $param
		 * @return RCDocument
		 */
		public function setRcDoc($param = null) {
			$this->rcDoc = new RCDocument($param);
		}

		/**
		 * Set WaterMark
		 *
		 * @param int $param
		 * @return RCDocument
		 */
		public function setWaterMark($param = null) {
			$this->rcDoc->setWatermark($param);
		}

		/**
		 * Add selected blocks to PDF file(IEP Document). Print each block to common PDF
		 *
		 * @param bool $statusInfo flag for generation status
		 */
		public function addBlocks($statusInfo = false) {
			$numBlock = 1;
			$i = 0;
			foreach ($this->blocks as $block) {
				if ($i != 0) {
					$this->rcDoc->newLine();
				}
				$i++;
				# create objects for document, insert data to objects and add blocks
				$this->render($block);
				if ($statusInfo === true) {
					$this->countStatus($numBlock, $block->name);
					$numBlock++;
				}
			}
		}

		/**
		 * Generate status about builder from ajax
		 *
		 * @param int $numBlock
		 * @param string $nameBlock
		 */
		public function countStatus($numBlock, $nameBlock) {
			$per = $numBlock / count($this->blocks);
			io::progress($per, $nameBlock, true);
		}

		/**
		 * Add header for document
		 *
		 * @param string $title
		 * @param string $value
		 * @return void
		 */
		public function setHeaderDoc($title, $value) {
			$tbl = RCTable::factory()
				->addLeftHeading('Student Name: ', $this->getStd()->get('stdname'))
				->addRightHeading($title, $value);
			$this->rcDoc->addObject($tbl, new RCStyle('[border-bottom: 2px solid grey]'));
		}

		/**
		 * Encode & compile finish IEP document
		 *
		 * @return RCDocument
		 */
		public function compile() {
			$path = $this->rcDoc->compile();
			$content_in = file_get_contents($path);
			$content_out = IDEAFormPDF::repair_structure($content_in);
			file_put_contents($path, $content_out);
			return CryptClass::factory()->encode($path);
		}

		/**
		 * Return finish IEP Doc
		 *
		 * @return RCDocument
		 */
		public function getRCDoc() {
			return $this->rcDoc;
		}

		/**
		 * Return object for selected checkbox
		 *
		 * @return RCImage
		 */
		public function getChecked() {
			return $this->checked;
		}

		/**
		 * Return object for unselected checkbox
		 *
		 * @return RCImage
		 */
		public function getUnchecked() {
			return $this->unchecked;
		}

		/**
		 * Check to values and return string for checkbox. Some switchers use letters 'I', 'O' etc.
		 *
		 * @param string $val1
		 * @param string $val2
		 * @return string
		 */
		public function checkSwitcher($val1, $val2) {
			if ($val1 === $val2) {
				return 'Y';
			} else {
				return 'N';
			}
		}

		/**
		 * Returns summarized info about the object.
		 * Implementation of interface IOTraceableInterface.
		 * This method helps to correct output the object using the class IOTrace (static alias is io::trace() )
		 *
		 * @return mixed
		 */
		public function trace() {
			$props = CoreUtils::getClassProperties($this, true, false, true);
			foreach ($props as $key => $val) {
				$props[$key] = $this->$key;
			}

			return $props;
		}

		/**
		 * Add to page table with goals & objectives
		 *
		 * @param string $title title page(in header)
		 * @param array $goals array with goals, objectives & periods
		 */
		protected function progressReportGoals($title, $goals) {
			$layout = RCLayout::factory()
				->newLine('[background: #C0C0C0]')
				->addText(
					$title,
					'bold [font-size: 22px; color: white; padding: 5px 0px 5px 10px;]'
				)
				->newLine('[background: #C0C0C0]')
				->addText(SystemCore::$VndName, '[font-size: 16px; color: white; padding: 0px 0px 5px 5px; font-style: italic;]')
				->newLine()
				->addText(
					'<b>Student\'s Name: </b><i>' . (string)$this->std->get('stdname') . '</i>',
					'[margin: 10px 0px 0px 10px; width: 400px;]'
				)
				->addText(
					'<b>' . IDEAFormat::getIniOptions('iep_year_title') . ': </b><i>' . (string)$this->std->get('stdiepyeartitle'),
					'[margin-top: 10px;]'
				);

			$sumGoals = count($goals);
			$objective = new RCStyle('[padding-left: 20px;] unicode');
			$borderBot = new RCStyle('[border-bottom: 1px solid black; padding-left: 20px;] unicode');
			$stylePer = new RCStyle('[border: 1px solid black; border-top: none; border-right: none; width: 50px;] center unicode');
			$tbl = RCTable::factory('.table');
			# even if exist goal || objective
			for ($i = 0; $i < $sumGoals && ($goals[$i]['goal'] != '' || $goals[$i]['objective'] != ''); $i++) {
				# if first row add cols & names
				if ($i == 0) {
					$tbl->setCol('');
					# cols for periods
					foreach ($goals[0]['periods'] as $per) {
						$tbl->setCol('50px');
					}
					# 1st col
					$benchObjectives = json_decode(IDEAFormat::getIniOptions('bgb'), true);
					$tbl->addRow('[border-top: 1px solid black;]')
						->addCell('Goals/' . $benchObjectives['benchmarks'], '.hr unicode'); // Benchmarks || Objectives
					# periods
					foreach ($goals[0]['periods'] as $per) {
						$tbl->addCell($per['bm'] . PHP_EOL . $per['dsydesc'], '.next-hr unicode');
					}
				}
				# if objective is empty add goal
				if ($goals[$i]['objective'] == '') {
					$period = '';
					foreach ($goals[$i]['periods'] as $per) {
						if ($per['narrative'] != '') {
							$period .= "<i>MP-" . $per['bm'] . " Comments: " . $per['narrative'] . "</i>\n";
						}
					}
					$tbl->addRow()
						->addCell("<b>" . (string)$goals[$i]['goal'] . "</b>\n" . $period, 'unicode');

				}
				# if goal is empty add objective
				if ($goals[$i]['goal'] == '') {
					$period = '';
					foreach ($goals[$i]['periods'] as $per) {
						if ($per['narrative'] != '') {
							$period .= "<i>MP-" . $per['bm'] . " Comments: " . $per['narrative'] . "</i>\n";
						}
					}
					$objdata = RCLayout::factory()
						->newLine()
						->addText((string)$goals[$i]['objective'] . "\n" . $period . "",
							# if exist next goal add border
							isset($goals[$i + 1]) && $goals[$i + 1]['objective'] == '' ? $borderBot : $objective);
					$trials = IDEAStudentBenchmarkAssessment::factory($goals[$i]['brefid'])->getTrialGraph(2);
					foreach ($trials as $trial) {
						$objdata->newLine()
							->addText($trial['name'])
							->newLine()
							->addObject(RCImage::factory($trial['img']));
					}
					$tbl->addRow()
						->addCell($objdata);
				}
				# add values periods
				foreach ($goals[$i]['periods'] as $per) {
					$tbl->addCell((string)$per['value'], $stylePer);
				}
			}

			$layout->newLine()
				->addObject($tbl)
				->newLine()
				->addObject(
					RCLayout::factory('[width: 70%]')
						->addText(
							$this->printMarkingPeriods === true ? '<b>Marking Periods:</b>' : '<b>Legend:</b>',
							$this->printMarkingPeriods === true ? '[width: 70px; border-bottom: 1px solid black;]' : '[width: 40px; border-bottom: 1px solid black;]')
				)
				->addObject(
					RCLayout::factory('[width: 30%]')
						->addText(
							$this->printMarkingPeriods === true ? '<b>Legend:</b>' : '',
							$this->printMarkingPeriods === true ? '[width: 40px; border-bottom: 1px solid black;]' : null
						)
				);
			# add rows with periods & legends
			if (isset($goals[0]) && isset($goals[0]['periods'])) {
				$allPeriods = count($goals[0]['periods']);
				$legends = IDEADistrict::factory(SystemCore::$VndRefID)->getProgressExtents();

				for ($j = 0; $j < $allPeriods; $j++) {
					$per = $goals[0]['periods'][$j];
					$valPeriod = '';
					$valLegend = '';
					# title period with name and dates
					if ($this->printMarkingPeriods === true) {
						$valPeriod = '<b>' . $per['bm'] . ' (' . $per['dsydesc'] . ')</b> - ' . $per['bmbgdt'] . ' - ' . $per['bmendt'];
					}

					if (isset($legends[$j])) {
						$valLegend = '<b>' . $legends[$j]->get('epsdesc') . '</b> - ' . $legends[$j]->get('epldesc');
					}

					$layout->newLine()->addText($this->printMarkingPeriods === true ? $valPeriod : $valLegend, 'unicode');
					$layout->addText($this->printMarkingPeriods === true ? $valLegend : '', 'unicode');
				}
			}

			$this->rcDoc->addObject($layout);
		}
	}
