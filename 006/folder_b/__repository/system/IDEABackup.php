<?php

	/**
	 * Class IDEABackup
	 *
	 * @author
	 * @copyright LumenTouch, 2014
	 */
	class IDEABackup extends RegularClass {

		/**
		 * @var string $table
		 */
		private $table;

		/**
		 * @var int $stdrefid
		 */
		private $stdrefid;

		/**
		 * @var int $constr
		 */
		private $constr;

		/**
		 * @var str $callBack
		 */
		private $callBack;

		/**
		 * Class Constructor
		 *
		 * @param int $stdrefid
		 * @param int $constr
		 * @param null $callBack
		 * @return IDEABackup
		 */
		public function __construct($stdrefid, $table, $constr = null, $callBack = null) {
			parent::__construct();
			$this->stdrefid = $stdrefid;
			$this->table = $table;
			$this->constr = $constr;
			$this->callBack = $callBack;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param int $stdrefid
		 * @param int $constr
		 * @param $callBack
		 * @return IDEABackup
		 */
		public static function factory($stdrefid, $table, $constr = null, $callBack = null) {
			return new IDEABackup($stdrefid, $table, $constr, $callBack);
		}

		/**
		 * Backup Data into Database
		 *
		 * @param string $table
		 * @param string $keyField, 
		 * @param string $value
		 * @return void
		 */
		public function backupData($keyField, $value) {
			$sql = "
                SELECT *
                  FROM " . $this->table . "
                 WHERE  " . $keyField . " = " . $value . " 
            ";
			$result = $this->execSQL($sql);

			$record = "<recordset>\n";
			while (!$result->EOF) {
				$record .= "<values>\n";
				foreach ($result->fields as $key => $val) {
					if ($key * 1 > 0) continue;
					if ($key == "0") continue;
					$record .= "<value name=\"$key\">" . base64_encode($val) . "</value>\n";
				}
				$record .= "</values>\n";
				$result->MoveNext();
			}
			$record .= "</recordset>\n";

			DBImportRecord::factory('webset.std_backup', 'refid')
				->set('area', $this->table)
				->set('stdrefid', $this->stdrefid)
				->set('content', base64_encode($record))
				->setUpdateInformation()
				->import();
			
			//DELETE OLD RECORDS ONCE A DAY
			$regDate = $this->registry->getOne('webset', 'std_backup', 'lastpurge', SystemRegistry::SR_SERVER);
			if ($regDate != date("m-d-Y")) {
				//DELETE BACKUPS OLDER THAN YEAR
				$sql = "
                    DELETE
	                  FROM webset.std_backup
	                 WHERE (lastupdate + INTERVAL '1 year') < NOW()
                ";
				$this->execSQL($sql);

				//MARK SYS REGISTRY WITH TODAY PURGE DATE
				$this->registry->updateKey('webset', 'std_backup', 'lastpurge', date("m-d-Y"), SystemRegistry::SR_SERVER);
			}
		}


		/**
		 * Returns button which opens Backups Preview Window 
		 *
		 * @var mixed $search_key
		 * @var mixed $search_id
		 * @return FFMenuButton
		 */
		public function previewBackup($search_key = null, $search_id = null) {
			$button = FFMenuButton::factory('Backup')
				->leftIcon('wizard2_16.png')
				->addItem(
					'Restore',
					"
						var wnd = PageAPI.singleton().desktop.open(
							'Restore',
							PageAPI.singleton().url(
								" . json_encode(CoreUtils::getVirtualPath('./api/backup.php')) .  ",
								{
									'tsrefid' : '" . $this->stdrefid . "', 
									'table' : '" . $this->table . "', 
									'search_key' : '" . $search_key . "', 
									'search_id' : '" . $search_id . "', 
									'constr' : '" . $this->constr . "'
								}
							)
						);
						wnd.resize(1200, 700);
						wnd.addEventListener(
							ObjectEvent.COMPLETE,
							function (e) {
								" . $this->callBack . "(e.param);
							}
						);
					",
					'wizard2_16.png'
				);
			return $button;
		}

		/**
		 * Get XML template
		 *
		 * @return string
		 */
		public function getTamplate() {
			$strSql = $this->execSQL("
				SELECT *
				  FROM webset.sped_constructions
				 WHERE cnrefid = " . $this->constr . "
			");

			# By mistake many templates has no doc tag in the beginings
			$start_tag = '';
			try {
				$xml_elem =  new SimpleXMLElement($strSql->fields["cnbody"]);
				$start_tag = $xml_elem->getName();
			} catch (Exception $e) {
			}

			if ($start_tag = 'doc') {
				$body = $strSql->fields["cnbody"];
			} else {
				$body = "<doc>" . $strSql->fields["cnbody"] . "</doc>";
			}
			return $body;
		}

		/**
		 * Get data of stored backup
		 *
		 * @param int $id
		 * @return string
		 */
		public function getValues($id, $return_as_is = false) {
			$strSql = $this->execSQL("
				SELECT content
				  FROM webset.std_backup
				 WHERE refid = " . $id . "
			");
			$values_xml = base64_decode($strSql->fields["content"]);
			if ($return_as_is) return $values_xml;
			$values_arr = IDEADocument::convertValuesXmlToArray($values_xml);
			$base64_detected = IDEADocument::is_base64(reset($values_arr));
			if ($base64_detected) {
				foreach ($values_arr as $key => $value) {
					$values_arr[$key] = base64_decode($value); 
				}
			}
			$values_xml = IDEADocument::convertValuesArrayToXml($values_arr);
			return $values_xml;
		}

		/**
		 * Set Previous Backup ID
		 *
		 * @param int $id
		 * @return int
		 */
		public function getPreviousID($id) {
			$hash = $this->execSQL("
				SELECT (t.stdrefid, t.area)::varchar AS f
				  FROM webset.std_backup AS t
				 WHERE refid = " . $id . "
			")->getOne();

			$prev_id = $this->execSQL("
				SELECT refid
				  FROM webset.std_backup
				 WHERE (stdrefid, area)::varchar = '" . $hash . "'
				   AND refid < " . $id . "
				 ORDER BY 1 DESC LIMIT 1
			")->getOne();
			
			return (int)$prev_id;
		}

	}
