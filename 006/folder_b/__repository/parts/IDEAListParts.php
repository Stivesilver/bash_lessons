<?php

	/**
	 * Basic IDEA blocks
	 * Contains sql fields, tables, query parts, titles special for logged user's district
		 *
		 * @copyright Lumen Touch, 2012
		 */
		abstract class IDEAListParts {

			/**
			 * This variable indicates that startup params have been initialized
			 *
			 * @var bool
			 */
			private static $initialized = false;

			/**
			 * Current State
			 *
			 * @var string
			 */
			public static $state;

			/**
			 * Disability Column
			 *
			 * @var string
			 */
			private static $dis_field = "
				ARRAY_TO_STRING(
					ARRAY(
						SELECT dccode
						  FROM webset.std_disabilitymst sd
							   INNER JOIN webset.statedef_disablingcondition st ON st.dcrefid = sd.dcrefid
						 WHERE sd.stdrefid = tsrefid
						 ORDER BY sdtype, sdrefid DESC
					),
					', '
				)
			";

			/**
			 * Placement Column
			 *
			 * @var string
			 */
			private static $plc_field = "
				ARRAY_TO_STRING(
					ARRAY(
						SELECT spccode
						  FROM webset.std_placementcode std
							   INNER JOIN webset.statedef_placementcategorycode plc ON std.spcrefid = plc.spcrefid
						 WHERE std.stdrefid = tsrefid
						 ORDER BY spcbeg, pcrefid DESC
					),
					', '
				)
			";

			/**
			 * Placement Column Title
			 *
			 * @var string
			 */
			private static $plc_title = 'Placement';

			/**
			 * Other Join part if needed
			 *
			 * @var string
			 */
			private static $otherJoin = '';

			/**
			 * Initializes properties for Sp Ed Lists
			 *
			 * @return void
			 */
			public static function init() {
				if (self::$initialized) return;
				self::$initialized = true;
				self::$state = VNDState::factory()->code;
				if (IdeaCore::disParam(96) == 'Y') {
					self::$dis_field = "
						ARRAY_TO_STRING(
							ARRAY(
								SELECT COALESCE(dccode || ' - ' || dcdesc, '')
								  FROM webset.std_disabilitymst sd
									   INNER JOIN webset.statedef_disablingcondition st ON st.dcrefid = sd.dcrefid
								 WHERE sd.stdrefid = tsrefid
								 ORDER BY sdtype, sdrefid DESC
							),
							'<br/>'
						)
					";
				}
				if (self::$state == 'TX') {
					self::$plc_field = "
						ARRAY_TO_STRING(
							ARRAY(
								SELECT spccode
								  FROM webset_tx.std_instruct_arrange sd
									   INNER JOIN webset.statedef_placementcategorycode st ON st.spcrefid = sd.placement
								 WHERE sd.std_refid = tsrefid
								 ORDER BY sd.lastupdate
							),
							', '
						)
					";
				} elseif (self::$state == 'IL') {
					self::$dis_field = "
						ARRAY_TO_STRING(
							ARRAY(
								SELECT COALESCE(validvalue || ' - ', '') || dcdesc
								  FROM webset.std_disabilitymst sd
									   INNER JOIN webset.statedef_disablingcondition st ON st.dcrefid = sd.dcrefid
									   LEFT OUTER JOIN webset.glb_validvalues v ON sd.dcrefid = CAST(v.validvalueid AS INTEGER)
								   AND valuename = 'IL_Disability_Codes'
								 WHERE sd.stdrefid = tsrefid
								 ORDER BY sdtype, sdrefid DESC
							),
							'<br/>'
						)
					";
				} elseif (self::$state == 'IN') {
					self::$plc_title = "Speech Triennial";
					self::$plc_field = "TO_CHAR(edncdeval, 'MM-DD-YYYY')";
					self::$otherJoin .= "LEFT OUTER JOIN webset.std_in_eligibility eli ON eli.stdrefid = tsrefid";
				} elseif (self::$state == 'KS') {
					self::$plc_title = "";
					self::$plc_field = "NULL";
				}
			}

			/**
			 * Create search SQL
			 *
			 * @param $type
			 * @param $refiFieldName
			 * @param string $condition
			 * @return ListClassContent
			 */
			public static function createSearchSql($type, $refiFieldName = 'std.stdrefid', $condition) {
				if (!self::$initialized) self::init();

				$where = "";
				switch ($type) {
					case 'related':
						if (in_array(IDEAListParts::$state, array('CT', 'OH'))) {
							$where = "
								  AND EXISTS (
									  SELECT 1
										FROM webset.std_oh_ns rel
									   WHERE rel.umrefid = " . SystemCore::$userID . "
										 AND (COALESCE(relsrvsw,'') = 'Y' OR COALESCE(servicetype, 0) IN (28, 52))
										 AND rel.stdrefid = tsrefid
								  )
							  ";
						}
						else {
							$where = "
								  AND EXISTS (
									  SELECT 1
										FROM webset.std_srv_rel rel
									   WHERE rel.umrefid = " . SystemCore::$userID . "
										 AND rel.stdrefid = tsrefid
								  )
							  ";
						}
						break;
					case 'school':
						$where = "AND std.vourefid = VOUREFID";
						break;
					case 'uaccess':
						$where = "
							  AND EXISTS (
								  SELECT 1
									FROM webset.std_useraccess ua
								   WHERE ua.miprefid = " . SystemCore::$userID . "
									 AND ua.stdrefid = tsrefid
							  )
						  ";
						break;
				}
				if ($condition) {
					$where .= ' ' . $condition;
				}

				$SQL = "
						SELECT $refiFieldName,
							   stdlnm || ', ' || stdfnm
						  FROM webset.sys_teacherstudentassignment ts
							   " . IDEAParts::get('studentJoin') . "
							   " . IDEAParts::get('gradeJoin') . "
							   " . IDEAParts::get('casemanJoin') . "
							   " . IDEAParts::get('schoolJoin') . "
							   " . IDEAParts::get('enrollJoin') . "
							   " . self::get('otherJoin') . "
						 WHERE std.vndrefid = VNDREFID
							   " . $where . "
						 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
					";
				return $SQL;
			}


			/**
			 * Create basic Sp Ed List Class Content
			 *
			 * @param $type
			 * @param $refiFieldName
			 * @param bool $showAnnualReview
			 * @param bool $showTriennialDate
			 * @param string $condition
			 * @param int $userID
			 * @return ListClassContent
			 */
			public static function createListContent($type, $refiFieldName = 'std.stdrefid', $showAnnualReview = false, $showTriennialDate = false, $condition = '', $userID = null) {
				if (!self::$initialized) self::init();
				if (!$userID)
					$userID = SystemCore::$userID;
				$where = "";
				switch ($type) {
					case 'related':
						if (in_array(IDEAListParts::$state, array('CT','OH'))) {
							$where = "
							  AND EXISTS (
								  SELECT 1
									FROM webset.std_oh_ns rel
								   WHERE rel.umrefid = " . $userID . "
									 AND (COALESCE(relsrvsw,'') = 'Y' OR COALESCE(servicetype, 0) IN (28, 52))
									 AND rel.stdrefid = tsrefid
							  )
						  ";
						} else {
							$where = "
							  AND EXISTS (
								  SELECT 1
									FROM webset.std_srv_rel rel
								   WHERE rel.umrefid = " . $userID . "
									 AND rel.stdrefid = tsrefid
							  )
						  ";
						}
						break;
					case 'school':
						$where = "AND std.vourefid = VOUREFID";
						break;
					case 'uaccess':
						$where = "
						  AND EXISTS (
							  SELECT 1
								FROM webset.std_useraccess ua
							   WHERE ua.miprefid = " . $userID . "
								 AND ua.stdrefid = tsrefid
						  )
					  ";
						break;
				}
				/*
				if ($serviceImplementor) {
					$where .= " AND EXISTS(SELECT 1 FROM webset.std_srv_rel AS srv WHERE tsrefid = srv.stdrefid AND mp_refid IS NOT NULL)";
				}*/
				if ($condition) {
					$where .= ' ' . $condition;
				}

				$content = new ListClassContent();
				$content->showSearchFields(true);
				$sqlList = "
					SELECT $refiFieldName,
						   stdlnm,
						   stdfnm,
						   stdmnm,
						   " . IDEAParts::get('schoolName') . " || ' ' || COALESCE(' - ' || " . IDEAParts::get('username') . ", '') as school,
						   gl_code,
						   " . IDEAParts::get('spedPeriod') . " as spedperiod,
						   " . IDEAParts::get('stdcmpltdt') . " as stdcmpltdt,
						   " . IDEAParts::get('stdtriennialdt') . " as stdtriennialdt,
						   " . self::get('dis_field') . " as disability,
						   " . self::get('plc_field') . " as placement,
						   CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
						   CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus,
						   stdcmpltdt as stdcmpltdt_real,
						   stdtriennialdt as stdtriennialdt_real,
						   stdlnm || ', ' || stdfnm
					  FROM webset.sys_teacherstudentassignment ts
						   " . IDEAParts::get('studentJoin') . "
						   " . IDEAParts::get('gradeJoin') . "
						   " . IDEAParts::get('casemanJoin') . "
						   " . IDEAParts::get('schoolJoin') . "
						   " . IDEAParts::get('enrollJoin') . "
						   " . self::get('otherJoin') . "
					 WHERE std.vndrefid = VNDREFID
						   " . $where . "
						   ADD_SEARCH
					 ORDER BY UPPER(stdlnm), UPPER(stdfnm), UPPER(stdmnm)
				";

				$content->addSearchField(FFIDEAStudentName::factory()->help('Examples:<br>1. By last name<br><br>Smith => Smith, Adam<br>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp => Smith, John<br><br>2. By first name<br><br>%, Adam => Smith, Adam<br>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp => White, Adam<br><br>3. By last and first name<br><br>Smith, A => Smith, Adam<br>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp => Smith, Alex'));

				if ($type == 'admin') $content->addSearchField(FFIDEACaseManager::factory()->name('umrefid')->sqlField('ts.umrefid'));

				if ($type == 'sped') {
					/** @var FFSelect */
					$a = FFIDEACaseManager::factory('pc')
						->name('umrefid')
						->sqlField('ts.umrefid')
						->emptyOption(false);

					$defCM = db::execSQL($a->sql)->getOne();

					if ($defCM == '') {
						$defCM = '-1';
						$sqlList = str_replace("ADD_SEARCH", "AND 1=0 ADD_SEARCH", $sqlList);
						$a->hide();
					}

					$SQL = "
						SELECT t3.umlastname || ', ' || t3.umfirstname
						  FROM webset.sys_proccoordmst AS t1
							   INNER JOIN webset.sys_proccoordassignment AS t2 ON t2.pcrefid = t1.pcrefid
							   INNER JOIN public.sys_usermst AS t3 ON t3.umrefid = t1.umrefid
							   INNER JOIN webset.sys_casemanagermst cm ON cm.umrefid = t3.umrefid
					";
					if (io::posti('umrefid') > 0) {
						$SQL .= ' WHERE t2.cmrefid = ' . io::post('umrefid');
					} else {
						$SQL .= ' WHERE t2.cmrefid = ' . $defCM;
					}
					$PCName = db::execSQL($SQL)->getOne();
					$a->value($defCM);
					if ($PCName != '') $a->append(UILayout::factory()->addHTML('', '2%')->addHTML('<b>PC:</b> ' . $PCName));

					$content->addSearchField($a);
				}

				if ($type != 'school' && $type != 'related')
					$content->addSearchField(
						FFIDEASchool::factory()
							->name('vourefid_l')
					);

				$content->addSearchField('Lumen #', 'std.stdrefid');
				$content->addSearchField('External #', 'std.externalid');
				$content->addSearchField('Federal #', 'std.stdfedidnmbr');
				$content->addSearchField('State #', 'std.stdstateidnmbr');

				if (IDEACore::disParam(35) == 'Y') $content->addSearchField('Student ID #', 'stdschid');

				$content->addSearchField(FFIDEAStdStatus::factory());

				$content->addSearchField(FFIDEASpEdStatus::factory());

				$content->addSearchField(
					FFIDEAEnrollCodes::factory()
						->sqlField('ts.denrefid')
						->name('denrefid')
				);

				$content->addSearchField(
					FFGradeLevel::factory()
						->sqlField('std.gl_refid')
						->name('gl_refid')
				);
				$content->addSearchField(FFIDEADisability::factory()->name('disability'));

				$content->setSQL($sqlList);

				$content->attachColumn(
					ListClassColumn::factory('Last Name')
						->sqlField('stdlnm')
				);

				$content->attachColumn(
					ListClassColumn::factory('First Name')
						->sqlField('stdfnm')
				);

				$content->attachColumn(
					ListClassColumn::factory('Middle Name')
						->sqlField('stdmnm')
				);

				$content->attachColumn(
					ListClassColumn::factory('Attending School')
						->sqlField('school')
				);

				$content->attachColumn(
					ListClassColumn::factory('GL')
						->hint('Grade Level')
						->sqlField('gl_code')
				);

				if (!SystemCore::$isTablet)
					$content->attachColumn(
						ListClassColumn::factory('Sp Ed Enrollment')
							->sqlField('spedperiod')
					);

				if ($showAnnualReview)
					$content->attachColumn(
						ListClassColumn::factory('Annual Review')
							->hint('IEP Projected Date of Annual Review')
							->sqlField('stdcmpltdt')
							->dataCallback('markPastAnnual')
					);

				if ($showTriennialDate)
					$content->attachColumn(
						ListClassColumn::factory('Triennial Date')
							->sqlField('stdtriennialdt')
							->dataCallback('markPastTriennial')
					);

				$content->attachColumn(
					ListClassColumn::factory('Disability')
						->sqlField('disability')
				);

				$content->attachColumn(
					ListClassColumn::factory(self::get('plc_title'))
						->sqlField('placement')
				);

				$content->attachColumn(
					ListClassColumn::factory('Std')
						->hint('Student Status')
						->type('switch')
						->sqlField('stdstatus')
						->printable(false)
				);

				$content->attachColumn(
					ListClassColumn::factory('Sp Ed')
						->hint('Sp Ed Status')
						->type('switch')
						->sqlField('spedstatus')
						->printable(false)
				);

				$content->includeFile(CoreUtils::getVirtualPath('./') . 'api/idea_list_parts.inc.php');

				return $content;

			}

			/**
			 *  * Create basic Sp Ed List
			 *
			 * @param $type
			 * @param string $doctype
			 * @throws Exception
			 */
			public static function createList($type, $doctype = "") {

				$list = new ListClass();
				$list->setContent(self::createListContent($type, 'tsrefid', true, true));

				$list->multipleEdit = false;
				$list->hideCheckBoxes = true;
				$list->printable = true;
				$list->getPrinter()->setPageFormat(RCPageFormat::LETTER | RCPageFormat::LANDSCAPE);

				$list->prepareRow('prepareLine');

				$list->printList();

				$url = IDEAScreenType::factory($doctype)->getUrl();

				$id = IDEAScreenType::factory($doctype)->getID();

				io::js("function openStdScreen(refid) {
							var win = api.desktop.open('Loading...', api.url('" . SystemCore::$appVirtualRoot . '/idea/' . $url . "', {'tsRefID' : refid, 'screenID' : $id}));
							win.maximize();
							win.show();
						}
				");
			}

		/**
		 * Returns specified property value
		 *
		 * @param mixed $property
		 * @return mixed
		 */
		public static function get($property) {
			if (!self::$initialized) self::init();
			return self::$$property;
		}

	}

?>
