<?php

	/**
	* Time Tracking
	*
	* @author Oleg Bychkovski
	* @copyright Lumen Touch, 2014
	*/
	class SCTimeScale extends TimeScale {

		/**
		 * SPEDEX Medicaid Services
		 */
		const MEDICAID_SERVICE = 0;

		/**
		 * SPEDEX Medicaid Service Capture - Provider
		 */
		const MEDICAID_PROVIDER = 1;

		/**
		 * Type (MEDICAID_SERVICE or MEDICAID_PROVIDER)
		 *
		 * @var string
		 */
		private $type;

		/**
		 * Service Provider
		 *
		 * @var int
		 */
		private $mp_refid;

		/**
		 * Class Constructor
		 *
		 * @param string $type
		 * @param int $mp_refid
		 * @return SCTimeScale
		 */
		public function __construct($type = self::MEDICAID_SERVICE, $mp_refid = 0) {
			parent::__construct();
			$this->mp_refid = $mp_refid == 0 ? db::execSQL("SELECT mp_refid FROM webset.med_disdef_providers WHERE umrefid = " . SystemCore::$userID)->getOne() : (int)$mp_refid;
			$this->type = $type;
		}

		/**
		 * Creates empty item
		 *
		 * @return TimeScaleItem
		 */
		public function createItem() {
			return new SCTimeScaleItem($this->type, $this->mp_refid);
		}

		/**
		 * Returns list of the hours per days for the specified date range
		 * Output array format:
		 *    [
		 *        'yyyy-mm-dd' => float,
		 *        'yyyy-mm-dd' => float,
		 *        'yyyy-mm-dd' => float,
		 *        'yyyy-mm-dd' => float,
		 *        ....
		 *    ]
		 * This method returns all days, including Weekends and Holidays.
		 * Example of output array:
		 *    [
		 *        '2013-04-08' => 8,
		 *        '2013-04-09' => 7.25,
		 *        '2013-04-10' => 11,
		 *        '2013-04-11' => 11.2
		 *    ]
		 *
		 * @param string $dateFrom
		 * @param string $dateTo
		 * @return array
		 */
		public function getHours($dateFrom, $dateTo) {
			CoreUtils::checkArguments('string, string');
			return $this->execSQL("
				SELECT dt,
					   EXTRACT(EPOCH FROM(SUM(mins)))/3600 as hours
				  FROM (SELECT TO_CHAR(mss_srv_date,'YYYY-MM-DD') as dt, mss_srv_time_end - mss_srv_time_start AS mins
						  FROM webset.med_std_services AS mss
						       INNER JOIN webset.med_disdef_providers AS mp ON mss.mp_refid = mp.mp_refid
						 WHERE mss.mp_refid = " . $this->mp_refid . "
						   AND mss_srv_date >= '" . CoreUtils::formatDate($dateFrom) . "'::DATE
						   AND mss_srv_date <= '" . CoreUtils::formatDate($dateTo) . "'::DATE)  AS t01
				 GROUP BY dt
				 ORDER BY 1
				")->keyedCol();
		}
		/**
		 * Save log informations
		 * @param int $reqId
		 * @param string $type
		 * @param string $field_name
		 * @param string $field_caption
		 * @param string $value_old
		 * @param string $value_new
		 * @param string $text_value_old
		 * @param string $text_value_new
		 * @return void
		 */
		protected function saveJournalData($reqId, $type, $field_name, $field_caption, $value_old, $value_new, $text_value_old, $text_value_new) {
			DBImportRecord::factory('webset.med_std_services_log', 'msl_refid')
				->set('mss_refid', $reqId)
				->set('msl_name', $field_name)
				->set('msl_caption', $field_caption)
				->set('msl_type', $type)
				->set('msl_value_old', $value_old)
				->set('msl_value_new', $value_new)
				->set('msl_text_value_old', $text_value_old)
				->set('msl_text_value_new', $text_value_new)
				->set('umrefid', SystemCore::$userID)
				->setUpdateInformation()
				->import();
		}

		/**
		 * Loads records for the specified day.
		 * This method returns list of the instances on TimeScaleItem
		 * Output format:
		 *    [
		 *        <TimeScaleItem>,
		 *        <TimeScaleItem>,
		 *        <TimeScaleItem>,
		 *        ...
		 *    ]
		 *
		 * @param string $date
		 * @param int $lastRecordsCount count of last records to be returned. If null - will return all
		 * @return TimeScaleItem[]
		 */
		public function loadDay($date, $lastRecordsCount = null) {
			CoreUtils::checkArguments('string,[int|null]');
			$res = array();

			$where = "mss_srv_date = '" . CoreUtils::formatDate($date) . "'::DATE";
			if ($lastRecordsCount === null) {
				$limit = '';
				$order = 'ORDER BY mss_srv_date';
			} else {
				$limit = 'LIMIT ' . (int)$lastRecordsCount;
				$order = 'ORDER BY mss_srv_date DESC';
			}

			$rs = $this->execSQL("
				SELECT mss_refid,
					   EXTRACT(EPOCH FROM mss_srv_time_start)/60 AS start,
					   EXTRACT(EPOCH FROM mss_srv_time_end)/60 AS end,
					   mss_desc,
					   public.plpgsql_recs_to_str('SELECT stdrefid AS column FROM webset.med_std_services_visited WHERE mss_refid =' || mss_refid, ',') AS stdrefids,
					   COALESCE(public.plpgsql_recs_to_str('SELECT stdrefid AS column FROM webset.med_std_services_not_visited WHERE mss_refid =' || mss_refid, ','), '-1') AS not_visited_stdrefids,
					   vourefid,
					   mds_refid,
					   mss.mp_refid,
					   mss_status,
					   stdrefid
				 FROM  webset.med_std_services AS mss
				       INNER JOIN webset.med_disdef_providers AS mp ON mss.mp_refid = mp.mp_refid
				 WHERE " . $where . "
				   AND mss.mp_refid = " . $this->mp_refid . "
				   " . $order . "
				   " . $limit . "
			");


			while (false !== ($row = $rs->assoc())) {
				$res[] = SCTimeScaleItem::factory($this->type, $this->mp_refid)
					->id($row['mss_refid'])
					->start($row['start'])
					->end($row['end'])
					->title($row['mss_desc'])
					->status($row['mss_status'])
					->student($row['stdrefids'])
					->studentNotVisisted($row['not_visited_stdrefids'])
					->location($row['vourefid'])
					->service($row['mds_refid'])
					->provider($row['mp_refid']);
			}
			return $res;
		}

		/**
		 * Saves records for the specified day.
		 * This method receives list of instances of TimeScaleItem.
		 * Also this method may receive list of instances of stdClass, but with the same structure as in TimeScaleItem.
		 *
		 * @param string $date
		 * @param TimeScaleItem[] $items
		 * @return void
		 */
		public function saveDay($date, array $items) {
			CoreUtils::checkArguments('string, array');
			$date = CoreUtils::formatDate($date);


			# remember all ids
			$storedIds = array();
			/** @var SCTimeScaleItem $item */
			foreach ($items as $item) {

				$oldId = $item->id();
				$title = ($item->title() ? $item->title() : 'Untitled');
				$strSQL = "
					SELECT interval'" . $this->escape($item->start()) . " minutes' as started,
						   interval'" . $this->escape($item->end()) . " minutes' as finished";
				list($timeStarted, $timeFinished) = $this->execSQL($strSQL)->index();

				$ins = DBImportRecord::factory('webset.med_std_services', 'mss_refid')
					->key('mss_refid',          $item->id())
					->set('mss_desc',           $title)
					->set('mss_srv_date',       $date)
					->set('mss_srv_time_start', $timeStarted)
					->set('mss_srv_time_end',   $timeFinished)
					->set('mss_status',         $item->status())
					->set('vourefid',           $item->location())
					->set('mp_refid',           $item->provider())
					->set('mds_refid',          $item->service())
					//->set('stdrefid',           $item->student())
					->set('lastuser',           SystemCore::$userUID)
					->set('lastupdate',         'NOW()', true)
					->set('vndrefid',           SystemCore::$VndRefID, true);
				if ($oldId == 0)
					$ins->set('mss_submission', 'CURRENT_DATE', true);

				$storedIds[] = $newID = $ins->import()
					->recordID();
				$strSQL = "DELETE FROM webset.med_std_services_visited WHERE mss_refid = $newID";
				$this->execSQL($strSQL);
				if ($item->student()) {
					$strSQL = "
	                    INSERT INTO webset.med_std_services_visited (mss_refid, stdrefid, lastuser, lastupdate)
	                    SELECT $newID, stdrefid, '" . SystemCore::$userUID . "', NOW()
	                      FROM webset.dmg_studentmst
	                     WHERE stdrefid IN (" . $item->student() . ")
					";
					$this->execSQL($strSQL);
				}

				$strSQL = "DELETE FROM webset.med_std_services_not_visited WHERE mss_refid = $newID";
				$this->execSQL($strSQL);
				if ($item->studentNotVisisted()) {
					$strSQL = "
	                    INSERT INTO webset.med_std_services_not_visited (mss_refid, stdrefid, lastuser, lastupdate)
	                    SELECT $newID, stdrefid, '" . SystemCore::$userUID . "', NOW()
	                      FROM webset.dmg_studentmst
	                     WHERE stdrefid IN (" . $item->studentNotVisisted() . ")
					";
					$this->execSQL($strSQL);
				}

				$item->id($newID);
				if ($oldId == 0)
					$save_type = 'I';
				else
					$save_type = 'U';
				$this->saveJournalData($newID, $save_type, 'mss_desc', 'Description', '', $title, '', $title);
				$this->saveJournalData($newID, $save_type, 'mss_status', 'Service Status', '', $item->status(), '', 'Needs Approval');
				$this->saveJournalData($newID, $save_type, 'vourefid', 'Location', '', $item->location(), '',
					                   db::execSQL("SELECT vouname FROM sys_voumst WHERE vourefid = " . $item->location())->getOne());
				$this->saveJournalData($newID, $save_type, 'mp_refid', 'Service Provider', '', $item->provider(), '',
					                   db::execSQL("
											SELECT mp_lname || ', ' || mp_fname || COALESCE(' (' || mp_id || ')', '')
											  FROM webset.med_disdef_providers
											 WHERE mp_refid = " . $item->provider())->getOne()
				                       );
				$this->saveJournalData($newID, $save_type, 'mds_refid', 'Service', '', $item->service(), '',
					                   db::execSQL("
											SELECT COALESCE(mds_code || ' - ', '') ||  COALESCE(mds_desc, '')
											  FROM webset.med_disdef_services
											 WHERE mds_refid = " . $item->service())->getOne()
				                      );
                if ($item->student())
					$std_names = db::execSQL("
						SELECT public.plpgsql_recs_to_str('SELECT stdlnm || '', '' || stdfnm AS column FROM webset.vw_dmg_studentmst WHERE stdrefid IN (" . $item->student() . ")', '; ')
					")->getOne();
				else
					$std_names = '';
				$this->saveJournalData(
					$newID, $save_type, 'stdrefid', 'Visited Student', '', $item->student(), '', $std_names);

				if ($item->studentNotVisisted())
					$std_names = db::execSQL("
						SELECT public.plpgsql_recs_to_str('SELECT stdlnm || '', '' || stdfnm AS column FROM webset.vw_dmg_studentmst WHERE stdrefid IN (" . $item->studentNotVisisted() . ")', '; ')
					")->getOne();
				else
					$std_names = '';
				$this->saveJournalData(
					$newID, $save_type, 'stdrefid_not_visited', 'Not Visited Student', '', $item->studentNotVisisted(), '', $std_names);


			}

			# delete from db records which was deleted by user
			$strSQL = "
					DELETE FROM webset.med_std_services
					WHERE mss_srv_date = '$date'::DATE
					  AND EXISTS(
    			          SELECT 1
    			            FROM webset.med_disdef_providers
    			           WHERE webset.med_std_services.mp_refid = webset.med_disdef_providers.mp_refid
    			             AND webset.med_disdef_providers.mp_refid = " . $this->mp_refid . ")
					  " . (count($storedIds) == 0 ? '' : "AND mss_refid NOT IN (" . implode(',', $storedIds) . ")" );
			$this->execSQL($strSQL);

		}

		/**
		 * Returns list of items by the specified IDs.
		 * Output array format:
		 * [
		 *      'id' => <TimeScaleItem>,
		 *      'id' => <TimeScaleItem>,
		 *      'id' => <TimeScaleItem>,
		 *      ...
		 * ]
		 *
		 * @param array $ids
		 * @return TimeScaleItem[]
		 */
		public function getItems(array $ids) {
			$out = array();
			foreach ($ids as $id) {
				$out[$id] = $item = new SCTimeScaleItem($this->type, $this->mp_refid);
				$item->id($id);
			}
			return $out;
		}

		/**
		 * Returns total number of entered minutes for the specified month.
		 * First argument is a number from 1 to 12.
		 *
		 * @param int $monthNumber
		 * @return int
		 */
		public function getTotalMinsByMonth($monthNumber) {
			CoreUtils::checkArguments('int');
			if (strlen($monthNumber) == 1)
				$monthNumber = '0' . $monthNumber;
			return (int)$this->execSQL("
				SELECT ROUND(SUM(EXTRACT(EPOCH FROM (mss_srv_date + mss_srv_time_end)::timestamp - (mss_srv_date + mss_srv_time_start)::timestamp)/60))
				       --SUM(mss_srv_time_end::time - mss_srv_time_start::time)
				  FROM webset.med_std_services,
						(SELECT (TO_CHAR(now(),'YYYY')||'-'||'" . $monthNumber . "'||'-01'||' 00:00:00')::TIMESTAMP as date_from,
								(TO_CHAR(now(),'YYYY')||'-'||'" . $monthNumber . "'||'-01')::DATE + INTERVAL '1 MONTH - 1 sec' as date_to) as dates

				 WHERE mss_srv_date >= date_from AND date_to >=  mss_srv_date
    			   AND EXISTS(
    			          SELECT 1
    			            FROM webset.med_disdef_providers
    			           WHERE webset.med_std_services.mp_refid = webset.med_disdef_providers.mp_refid
    			             AND webset.med_disdef_providers.mp_refid = " . $this->mp_refid . ")
			")->getOne();
		}

		/**
		 * Returns total number of entered requests for the specified month.
		 * First argument is a number from 1 to 12.
		 *
		 * @param int $monthNumber
		 * @return int
		 */
		public function getTotalRecordsByMonth($monthNumber) {
			CoreUtils::checkArguments('int');
			if (strlen($monthNumber) == 1)
				$monthNumber = '0' . $monthNumber;
			return (int)$this->execSQL("
				SELECT count(1)
				  FROM webset.med_std_services,
						(SELECT (TO_CHAR(now(),'YYYY')||'-'||'" . $monthNumber . "'||'-01'||' 00:00:00')::TIMESTAMP as date_from,
								(TO_CHAR(now(),'YYYY')||'-'||'" . $monthNumber . "'||'-01')::DATE + INTERVAL '1 MONTH - 1 sec' as date_to) as dates
				 WHERE mss_srv_date >= date_from AND date_to >=  mss_srv_date
    			   AND EXISTS(
    			          SELECT 1
    			            FROM webset.med_disdef_providers
    			           WHERE webset.med_std_services.mp_refid = webset.med_disdef_providers.mp_refid
    			             AND webset.med_disdef_providers.mp_refid = " . $this->mp_refid . ")
			")->getOne();
		}

		/**
		 * Saves user settings
		 *
		 * @param SCTimeScaleSettings $settings
		 * @return void
		 */
		public function saveSettings(SCTimeScaleSettings $settings) {
			SystemRegistry::factory(null, SystemCore::$userID)
				->updateKey('webset', 'medicaid', 'user_settings', serialize($settings), SystemRegistry::SR_USER);
		}

		/**
		 * Load settings
		 *
		 * @return SCTimeScaleSettings
		 */
		public function loadSettings() {
			$settings = SystemRegistry::factory(null, SystemCore::$userID)
				->getOne('webset', 'medicaid', 'user_settings', SystemRegistry::SR_USER);
			if ($settings) {
				return unserialize($settings);
			} else {
				return new SCTimeScaleSettings();
			}
		}


		/**
		 * Generates and returns RC (Report Composer) element of this object.
		 * Also this method can return RCML (Report Composer Markup Language) as a string.
		 *
		 * @param DBConnection $db
		 * @return RCElementInterface|string
		 */

		public function toRCE(DBConnection $db = null) {
			$personName = $this->execSQL("
				SELECT mp_lname || ', ' || mp_fname
				  FROM webset.med_disdef_providers
				 WHERE mp_refid = " . $this->mp_refid . "
				")->getOne();

			$rcd = RCLayout::factory()
				->addObject(RCHeading::factory()->addTitle('Activity Report of ' . $personName . ' from ' .  CoreUtils::formatDate($this->printDateRange[0],'m/d/Y') . ' to ' . CoreUtils::formatDate($this->printDateRange[1],'m/d/Y')))
				->newLine();

			$tbl = RCTable::factory('[background: #f5f5f5]')
				->repeatTableHeaders()
				->border(1, '#aaa')

				->setCol('45px')
				->setCol('35px')
				->setCol('35px')
				->setCol('35px')
				->setCol()

				->addRow('[background: #B6E7AD]')

				->addCell('Date')
				->addCell('Started')
				->addCell('Finished')
				->addCell('Hours')
				->addCell('Comments')

				->beginTableBody();

			$rs = $this->execSQL("
				SELECT TO_CHAR(mss_srv_date,'YYYY-MM-DD') as dt,
				       TO_CHAR(MIN(mss_srv_time_start),'HH24:MI') as started,
				       TO_CHAR(MAX(mss_srv_time_end),'HH24:MI') as finished,
				       TO_CHAR(SUM(mss_srv_time_end - mss_srv_time_start),'HH24:MI') as hours
		          FROM webset.med_std_services
		         WHERE mss_srv_date BETWEEN '" . $this->printDateRange[0] . "'::DATE AND '" . $this->printDateRange[1] . "'
		           AND EXISTS(SELECT 1
    			                FROM webset.med_disdef_providers
    			               WHERE webset.med_std_services.mp_refid = webset.med_disdef_providers.mp_refid
    			                 AND webset.med_disdef_providers.mp_refid = " . $this->mp_refid . ")
		         GROUP BY TO_CHAR(mss_srv_date,'YYYY-MM-DD')
		         ORDER BY 1
			");
			while ($row = $rs->FetchRow()) {
				$rsC = $this->execSQL("
					SELECT TO_CHAR(mss_srv_time_start,'HH24:MI') as started, TO_CHAR(mss_srv_time_end,'HH24:MI') as finished, mss_desc
					  FROM webset.med_std_services
					 WHERE mss_srv_date BETWEEN '" . $this->printDateRange[0] . "'::DATE AND '" . $this->printDateRange[1] . "'
					   AND EXISTS(SELECT 1
	                                FROM webset.med_disdef_providers
	                               WHERE webset.med_std_services.mp_refid = webset.med_disdef_providers.mp_refid
	                                 AND webset.med_disdef_providers.mp_refid = " . $this->mp_refid . ")
		             ORDER BY mss_srv_time_start
				");
				$comments = '';
				while ($rowC = $rsC->FetchRow()) {
					$comments .= PHP_EOL . $rowC['started'] . ' - ' . $rowC['finished'] . ': ' . $rowC['mss_desc'];
				}
				$comments = substr($comments,1);

				$tbl->addRow('[background: #FFFFFF]')
					->addCell(CoreUtils::formatDateForUser($row['dt']))
					->addCell($row['started'])
					->addCell($row['finished'])
					->addCell($row['hours'])
					->addCell($comments);
			}

			$rcd->addObject($tbl);

			return $rcd;
		}

		/**
		 * Compiles a document by the specified format.
		 * The first argument can be:
		 *    1. pdf    - Portable Document Format
		 *    2. csv    - Comma-separated values
		 *    3. html   - Hypertext Markup Language
		 *
		 * @param string $format
		 * @return void
		 */
		public function compile($format = '') {
			$rcd = new RCDocument(RCPageFormat::LANDSCAPE);
			$rcd->addObject($this->toRCE());
			$rcd->open();
		}


	}

?>