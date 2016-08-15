<?php

	/**
	 * Time Tracking Item
	 *
	 * @author Oleg Bychkovski, Michael Rogov
	 * @copyright Lumen Touch, 2014
	 */
	class SCTimeScaleItem extends TimeScaleItem {

		/**
		 * Type
		 *
		 * @var string
		 */
		private $type;

		/**
		 * Current Service Provider
		 *
		 * @var int
		 */
		private $mp_refid;


		/**
		 * Status
		 *
		 * @var string
		 */
		private $status = '';


		/**
		 * Student IDs
		 *
		 * @var string
		 */
		private $student = '';

		/**
		 * Student Not Visited IDs
		 *
		 * @var string
		 */
		private $student_not_visited = '';

		/**
		 * location ID
		 *
		 * @var int
		 */
		private $location = 0;

		/**
		 * Service ID
		 *
		 * @var int
		 */
		private $service = 0;

		/**
		 * Provider ID
		 *
		 * @var int
		 */
		private $provider = 0;

		/**
		 * Class Constructor
		 * @param string $type
		 * @param int $mp_refid
		 *
		 * @return SCTimeScaleItem
		 */
		public function __construct($type, $mp_refid) {
			$this->type = $type;
			$this->mp_refid = $mp_refid;
		}

		/**
		 * Creates and returns instance of this class
		 *
		 * @param string $type
		 * @param int $mp_refid
		 *
		 * @return SCTimeScaleItem
		 */
		public static function factory($type, $mp_refid) {
			return new SCTimeScaleItem($type, $mp_refid);
		}



		/**
		 * Sets/gets Status
		 *
		 * @param string $status
		 * @return SCTimeScaleItem|$this|int
		 */
		public function status($status = null) {
			if (func_num_args() == 1) {
				# setter
				CoreUtils::checkArguments('string');
				$this->status = $status;
				return $this;
			} else {
				# getter
				return $this->status;
			}
		}


		/**
		 * Sets/gets Students
		 *
		 * @param string $student
		 * @return SCTimeScaleItem|$this|int
		 */
		public function student($student = null) {
			if (func_num_args() == 1) {
				# setter
				CoreUtils::checkArguments('int|string');
				$this->student = $student;
				return $this;
			} else {
				# getter
				return $this->student;
			}
		}

		/**
		 * Sets/gets Not Visited Students
		 *
		 * @param string $student_not_visited
		 * @return SCTimeScaleItem|$this|int
		 */
		public function studentNotVisisted($student_not_visited = null) {
			if (func_num_args() == 1) {
				# setter
				CoreUtils::checkArguments('int|string');
				$this->student_not_visited = $student_not_visited;
				return $this;
			} else {
				# getter
				return $this->student_not_visited;
			}
		}

		/**
		 * Sets/gets Location
		 *
		 * @param int $location
		 * @return SCTimeScaleItem|$this|int
		 */
		public function location($location = null) {
			if (func_num_args() == 1) {
				# setter
				CoreUtils::checkArguments('int');
				$this->location = $location;
				return $this;
			} else {
				# getter
				return $this->location;
			}
		}

		/**
		 * Sets/gets Service
		 *
		 * @param int $service
		 * @return SCTimeScaleItem|$this|int
		 */
		public function service($service = null) {
			if (func_num_args() == 1) {
				# setter
				CoreUtils::checkArguments('int');
				$this->service = $service;
				return $this;
			} else {
				# getter
				return $this->service;
			}
		}

		/**
		 * Sets/gets Provider
		 *
		 * @param int $provider
		 * @return SCTimeScaleItem|$this|int
		 */
		public function provider($provider = null) {
			if (func_num_args() == 1) {
				# setter
				CoreUtils::checkArguments('int');
				$this->provider = $provider;
				return $this;
			} else {
				# getter
				return $this->provider;
			}
		}

		/**
		 * Returns associative array with the key/value pairs of the item properties.
		 *
		 * @return array
		 */
		public function getData() {
			$data = parent::getData();
			$data['status'] = $this->status;
			$data['student'] = $this->student;
			$data['student_not_visited'] = $this->student_not_visited;
			$data['location'] = $this->location;
			$data['service'] = $this->service;
			$data['provider'] = $this->provider;
			return $data;
		}

		/**
		 * Sets values for item properties by the specified associative array.
		 *
		 * @param array $data
		 * @return SCTimeScaleItem
		 */
		public function setData($data) {
			if (!isset($data['student'])) {
				throw new Exception('Invalid data format.');
			}
			$this->status = $data['status'];
			$this->student = $data['student'];
			$this->student_not_visited = $data['student_not_visited'];
			$this->location = $data['location'];
			$this->service = $data['service'];
			$this->provider = $data['provider'];
			return parent::setData($data);
		}

		/**
		 * Generates and returns HTML form for the item by the following rules:
		 *  1. All HTML elements must have unique IDs;
		 *  2. All input elements may have HTML attribute "data-name", which
		 *      will be used as item parameter for loading/saving data.
		 *
		 * @param DBConnection $db
		 * @return string
		 */
		public function getFormHTML(DBConnection $db = null) {

			$id = CoreUtils::generateUID();

			list($mp_refid, $mp_name) = db::execSQL("
				SELECT mp_refid, mp_lname || ', ' || mp_fname AS mp_name
				  FROM webset.med_disdef_providers
				 WHERE mp_refid = " . $this->mp_refid
			)->index();
			$this->provider($this->mp_refid);

			list($vouname, $mds_desc) = db::execSQL("
				SELECT vouname,
				       COALESCE(mds_code || ' - ', '') ||  COALESCE(mds_desc, '')
				  FROM webset.med_std_services AS mss
				       LEFT JOIN sys_voumst AS vou ON mss.vourefid = vou.vourefid
		       		   LEFT JOIN webset.med_disdef_providers AS mp ON mss.mp_refid = mp.mp_refid
		               LEFT JOIN webset.med_disdef_provider_types AS mpt ON mpt.mpt_refid = mp.mpt_refid
		               LEFT JOIN webset.med_disdef_services AS mds ON mss.mds_refid = mds.mds_refid
				 WHERE mss_refid = " . $this->id()
			)->index();

			$std_name = db::execSQL("
				SELECT stdlnm || ', ' || stdfnm AS column
				  FROM webset.med_std_services_visited AS msv
				       LEFT JOIN webset.vw_dmg_studentmst AS std ON std.stdrefid = msv.stdrefid
				 WHERE mss_refid =" . $this->id()
			)->indexCol();
			$std_name = implode('; ', $std_name);

			$std_name_not_visited = db::execSQL("
				SELECT stdlnm || ', ' || stdfnm AS column
				  FROM webset.med_std_services_not_visited AS msv
				       LEFT JOIN webset.vw_dmg_studentmst AS std ON std.stdrefid = msv.stdrefid
				 WHERE mss_refid =" . $this->id()
			)->indexCol();
			$std_name_not_visited = implode('; ', $std_name_not_visited);

			$st = $this->status();

			if ($st == 'S') {
				$elem = UILayout::factory()
					->newLine('')
					->addObject(
						FFInput::factory()
							->readonly(true)
							->transparent(true)
							->width('100%')
							->attr('data-name', 'title'),
						'[border-bottom: 1px dotted #888]'
					)
					->newLine('')
					->addObject(
						FFInput::factory()
							->caption('Visited Students')
							->readonly(true)
							->transparent(true)
							->value($std_name)
							->width('90%')
						,
						'[border-bottom: 1px dotted #888;]'
					)
					->newLine('')
					->addObject(
						FFInput::factory()
							->caption('Not Visited Students')
							->readonly(true)
							->transparent(true)
							->value($std_name_not_visited)
							->width('90%')
						,
						'[border-bottom: 1px dotted #888;]'
					)
					->newLine('')
					->addObject(
						FFInput::factory()
							->caption('Location')
							->readonly(true)
							->transparent(true)
							->value($vouname),
						'[border-bottom: 1px dotted #888]'
					)
					->addHTML('', '10px')
					->addObject(
						FFInput::factory()
							->caption('Service')
							->readonly(true)
							->transparent(true)
							->value($mds_desc),
						'[border-bottom: 1px dotted #888; width: 50px]'
					)
					->addHTML('', '10px')
					->addObject(
						FFInput::factory()
							->caption('Service Provider')
							->readonly(true)
							->transparent(true)
							->value($mp_name),
						'[border-bottom: 1px dotted #888]'
					)
					->toHTML($db);

			} else {
				$elem =  UILayout::factory()
					->newLine('')
					->addObject(
						FFInput::factory()
							->htmlWrap('')
							->grayText('Title of the service...')
							->css('background', 'transparent')
							->css('border', 'none')
							->css('outline', 'none')
							->width('100%')
							->attr('data-name', 'title'),
						'[border-bottom: 1px dotted #888; width: 100%;]'
					)
					->addObject(
						FFInput::factory()
							->attr('data-name', 'status')
							->value('N')
							->hide()
					)
					->newLine('')
					->addObject(
						FFInput::factory()
							->caption('Visited Students')
							->attr('data-name', 'student_name')
							->readonly(true)
							->transparent(true)
							->css('border', 'none')
							->css('outline', 'none')
							->width('90%')
							->value($std_name),
						'[border-bottom: 1px dotted #888; width: 300px; padding-top: 5px; padding-bottom: 5px; width: 100%;]'
					)
					->addHTML(
						FFButton::factory('Search Student')
							->leftIcon('magnify.png')
							->toolBarView(true)
							->htmlWrap('')
							->onClick('
								var block_section = $(this).closest("div[data-type=\\"block_section\\"]");
								var student_ids = block_section.find("input[data-name=\\"student\\"]").val();
								var m_sel = FFMultiSelect.get("select_item_student");
								$("#select_item_student").val(student_ids).change();
								m_sel.searchWindowSize(900, 600);
								m_sel.search();

								m_sel.removeAllEventListeners();
								m_sel.addEventListener(
									ObjectEvent.SELECT,
									function(e) {
										var m_sel = FFMultiSelect.get("select_item_student");
										block_section.find("input[data-name=\\"student\\"]")
											.val($("#select_item_student").val())
											.change();
										var sel = m_sel.getSelectedItems();
										var student_names = [];
										var i = 0;
										while(i < sel.length) {
											student_names[i] = sel[i][15];
											i++;
										}
										block_section.find("input[data-name=\\"student_name\\"]")
											.val(student_names.join("; "))
											.change()
											.blur();
										// close child window
										//wnd.destroy();
									}
								);
							'),
							'1px'
					)
					->newLine('')
					->addObject(
						FFInput::factory()
							->caption('Not Visited Students')
							->attr('data-name', 'not_visited_student_name')
							->readonly(true)
							->transparent(true)
							->css('border', 'none')
							->css('outline', 'none')
							->width('90%')
							->value($std_name_not_visited),
						'[border-bottom: 1px dotted #888; width: 300px; padding-top: 5px; padding-bottom: 5px; width: 100%;]'
					)
					->addHTML(
						FFButton::factory('Search Student')
							->leftIcon('magnify.png')
							->toolBarView(true)
							->htmlWrap('')
							->onClick('
								var block_section = $(this).closest("div[data-type=\\"block_section\\"]");
								var student_ids = block_section.find("input[data-name=\\"student_not_visited\\"]").val();
								var m_sel = FFMultiSelect.get("select_item_student_not_visited");
								$("#select_item_student_not_visited").val(student_ids).change();
								m_sel.searchWindowSize(900, 600);
								m_sel.search();

								m_sel.removeAllEventListeners();
								m_sel.addEventListener(
									ObjectEvent.SELECT,
									function(e) {
										var m_sel = FFMultiSelect.get("select_item_student_not_visited");
										block_section.find("input[data-name=\\"student_not_visited\\"]")
											.val($("#select_item_student_not_visited").val())
											.change();
										var sel = m_sel.getSelectedItems();
										var student_names = [];
										var i = 0;
										while(i < sel.length) {
											student_names[i] = sel[i][15];
											i++;
										}
										block_section.find("input[data-name=\\"not_visited_student_name\\"]")
											.val(student_names.join("; "))
											.change()
											.blur();
										// close child window
										//wnd.destroy();
									}
								);
							'),
							'1px'
					)
					->newLine('')
					->addObject(
						UIElements::factory()
							->addObject(
								UICustomHTML::factory()
									->append(
										FFSelect::factory('Location')
											->sql("
												SELECT vourefid, vouname
								                  FROM sys_voumst
								                 WHERE vndrefid = VNDREFID
								                 ORDER BY LOWER(vouname)
											")
											->attr('data-name', 'location')
									)
							)
							->addObject(
								UICustomHTML::factory()
									->append(
										FFInput::factory()
											->attr('data-name', 'student')
											->value($this->student())
											->hide()
									)
							)
							->addObject(
								UICustomHTML::factory()
									->append(
										FFInput::factory()
											->attr('data-name', 'student_not_visited')
											->value($this->studentNotVisisted())
											->hide()
									)
							)
							->addObject(
								UICustomHTML::factory()
									->append(
										FFSelect::factory('Service')
											->sql("
												SELECT mds_refid,  COALESCE(mds_code || ' - ', '') ||  COALESCE(mds_desc, '')
								                  FROM webset.med_disdef_services
								                 WHERE vndrefid = VNDREFID
								                 ORDER BY LOWER(mds_code), LOWER(mds_desc)
											")
											->attr('data-name', 'service')
									)
									->css('margin-right', '15px')
							)
							->addObject(
								UICustomHTML::factory()
									->append(
										FFInput::factory()
											->caption('Service Provider')
											->readonly(true)
											->transparent(true)
											->value($mp_name)
									)
							)
							->addObject(
								UICustomHTML::factory()
									->append(
										FFInput::factory()
											->attr('data-name', 'provider')
											->value($mp_refid)
											->hide()
									)
							)
					)
					->toHTML($db);
			}

			return UICustomHTML::factory()
				->asBlockElement()
				->append($elem)
				->attr('data-type', 'block_section')
				->toHTML();
		}
	}
?>