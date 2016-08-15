<?php

	/**
	 * IDEAMenuBuilder.php
	 * Use for creating tree application. Each app have name, group and link to frame.
	 * Class generate tree apps with order by groups.
	 * Created 12-12-2013. Updated 12-12-2013
	 *
	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 */
	class IDEAMenuBuilder {

		/**
		 * Menu items with name apps & name groups
		 *
		 * @var array
		 */
		protected $items;

		/**
		 * Summ items
		 *
		 * @var int
		 */
		protected $countItems;

		/**
		 * Year student
		 *
		 * @var int
		 */
		protected $stdIEPYear;

		/**
		 * Student Id
		 *
		 * @var int
		 */
		protected $tsRefID;

		/**
		 * Screen ID
		 *
		 * @var int
		 */
		protected $screenID;

		/**
		 * Load menu items from db and count summ
		 */
		public function __construct($stdIEPYear = null, $tsRefID = null, $screenID = 1) {
			$this->items = IDEAFormat::getApplications($screenID);
			$this->countItems = count($this->items);
			$this->stdIEPYear = $stdIEPYear;
			$this->tsRefID = $tsRefID;
		}

		/**
		 * Build tree for menu apps
		 *
		 * @param $sdtInfo
		 * @return UITree
		 */
		public function generateTree($sdtInfo = true, $required_title_option = 'iep_year_title') {
			$tree = new UITree('iepTree');
			$tree->expand(true);

			if ($sdtInfo == true) {
				# Add branch with Student info
				$this->addStdInfoTree($tree);
			}
			$menu = $tree->addItem('Main');

			for ($i = 0; $i < $this->countItems; $i++) {
				$param = '';
				$item = $this->items[$i]['mdmenutext'];
				$link = $this->items[$i]['mdlink'];
				$disable = '';
				# old core use symbol '*' in some url's
				$link = str_replace('*', '', $link);
				$this->delNumberGroup($i);
				$inst = IDEAStudentChecker::factory($this->tsRefID, $this->stdIEPYear);

				if (isset($this->items[$i]['check_method'])) {
					$method = $this->items[$i]['check_method'];
					// $this->items[$i]['param'], $this->items[$i]['mdlink']
					if ($this->items[$i]['check_param']) {
						$args = current(FileCSV::factory()->setDataAsString($this->items[$i]['check_param'])->toArray());
					} else {
						$args = array();
					}
					$params = call_user_func_array(array($inst, $method), $args);

					if ($params !== null) {
						foreach ($params as $key => $param) {
							// hide item
							if ($key == 'condition') {
								if ($param == 'N') {
									continue 2;
								}
							}
							// change item link
							if ($key == 'link') {
								$link = $param;
							}
							// rename item
							if ($key == 'item') {
								$item = $param;
							}
							// disable item
							if ($key == 'disable') {
								$disable = $param;
							}
						}
					}
				}

				# if subItem not exist or prev group have different name create subItem
				if ($i == 0 || ($i > 0 && $this->items[$i]['mitemgroup'] != $this->items[$i - 1]['mitemgroup'])) {
					$subItem = $menu->addItem($this->items[$i]['mitemgroup']);
				}

				$path = SystemCore::$physicalRoot . str_replace('applications/webset', 'apps/idea', $link);
				if ($disable != '') {
					$linkToApp = $subItem->addItem(
						$item,
						SystemCore::$virtualRoot . str_replace('applications/webset', 'apps/idea', $link),
						$this->items[$i]['mdicon']
					)
						->hint($disable)
						->css('color', '#1B2426')
						->selectable(false)
						->category($this->items[$i]['mrefid']);
				} else {
					$linkToApp = $subItem->addItem(
						$item,
						SystemCore::$virtualRoot . str_replace('applications/webset', 'apps/idea', $link),
						$this->items[$i]['mdicon']
					)
						->category($this->items[$i]['mrefid']);
				}

				# Clear GET param from path to file and check the existence
				if (file_exists($this->getPathWithoutGet($path)) === false ||
					($this->stdIEPYear == null && $this->items[$i]['mitem_iep_req_sw'] == 'Y')
				) {
					$linkToApp->css('color', '#1B2426');
				}

				# check IEP year and name app. If IEP not exist for current student, block apps.
				if (
					$this->stdIEPYear == null
					&& ($item != IDEAFormat::getIniOptions($required_title_option)
						&& $item != '* Select IEP Year')
				) {
					$linkToApp->hint(IDEAFormat::getIniOptions($required_title_option) . ' not yet created');
					# if app not use IEP, add alert
					if ($this->items[$i]['mitem_iep_req_sw'] == 'Y') {
						$linkToApp->value('errorIEPYear');
					}
				}

			}

			return $tree;
		}

		/**
		 * Create brach in tree with student info
		 *
		 * @param UITree $tree
		 */
		public
		function addStdInfoTree(UITree $tree) {
			$studenMenu = $tree->addItem('Basic');

			$studenMenu->addItem(
				'Student Info',
				'./desk_info.php',
				'/applications/webset/icons/mainscreen/social_emotional.png'
			);

			$studenMenu->addItem(
				'Case Notes',
				CoreUtils::getURL('/apps/idea/iep/casenotes/cn_casenotes.php'),
				'/applications/webset/icons/mainscreen/case_notes.png'
			);

			$checkDataItem = $studenMenu->addItem(
				'Check Data',
				CoreUtils::getURL('/apps/idea/iep/error/err_main.php'),
				'/applications/webset/icons/mainscreen/crisis_management_plan.png'
			)
				# add unique category
				->category('-1');

			$prevIEPItem = $studenMenu->addItem(
				'Preview ',
				'prevIEP',
				'/applications/webset/icons/mainscreen/statewide_and_district_wide_testing.png'
			)
				->category('-2');

			# add alert & change color if IEP not select
			if ($this->stdIEPYear == null) {
				$checkDataItem->value('errorIEPYear');
				$prevIEPItem->value('errorIEPYear');
				$checkDataItem->css('color', '#1B2426');
				$prevIEPItem->css('color', '#1B2426');
			}

		}

		/**
		 * Delete id group from name group
		 *
		 * @param integer $numberItem
		 */
		public
		function delNumberGroup($numberItem) {
			$this->items[$numberItem]['mitemgroup'] = preg_replace('/[0-9]/', '', $this->items[$numberItem]['mitemgroup']);
		}

		/**
		 * Delete GET param from path to file.
		 * Files have GET param in db
		 *
		 * @param string $path
		 * @return string
		 */
		public
		function getPathWithoutGet($path) {
			$pos = strpos($path, '?');
			if ($pos > 0) {
				$path = substr($path, 0, $pos);
			}

			return $path;
		}

	}
