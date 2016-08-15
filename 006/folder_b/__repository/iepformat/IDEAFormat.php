<?php

	/**
	 * Contains current IDEA Format
	 *
	 * @copyright Lumen Touch, 2012
	 */
	abstract class IDEAFormat {

		/**
		 * This variable indicates that startup params have been initialized
		 *
		 * @var bool
		 */
		private static $initialized = false;

		/**
		 * IDEA Format Numerical ID
		 * DB Table: webset.sped_menu_set
		 *
		 * @var int
		 */
		private static $id = 0;

		/**
		 * Default Documement Generate File
		 * DB Table: webset.sped_menu_set
		 *
		 * @var string
		 */
		private static $gen_file;

		/**
		 * Initiates basic properties
		 *
		 * @return void
		 */
		public static function init() {
			if (self::$initialized) return;
			self::$initialized = true;
			# get the current IDEA Format
			$SQL = "
                SELECT dd.srefid,
	                   shortdesc,
                       gen_file
	              FROM webset.disdef_spedmenu dd
	                   INNER JOIN webset.sped_menu_set iformat ON dd.srefid = iformat.srefid
	             WHERE vndrefid = VNDREFID
            ";
			$props = db::execSQL($SQL)->fields;
			if (empty($props)) {
				throw new Exception('IDEA Format is not yet defined. Contact District Administrator.');
			}
			self::$id = (int)$props['srefid'];
			self::$gen_file = str_replace('/applications/webset', '/apps/idea', $props['gen_file']);
		}

		/**
		 * Creates Application Items for current IDEA format
		 *
		 * @param int $screenID
		 * @return array
		 */
		public static function getApplications($screenID = 1) {
			if (!self::$initialized) self::init();
			$SQL = "
                SELECT mrefid,
                       mitemgroup,
                       mitemnewline,
                       mgroupnewline,
                       mstate,
                       mdlink,
                       mdmenutext,
                       md.mdrefid,
                       mm.displcondition,
                       md.mdicon,
                       mitem_iep_req_sw,
                       check_method,
					   check_param
                  FROM webset.sped_menu mm
                       INNER JOIN webset.sped_menudef md ON mm.mdrefid=md.mdrefid
                 WHERE mitemapp = " . $screenID . "
                   AND set_refid = " . self::$id . "
                 ORDER BY mitemgroup, mitemorder
            ";
			return db::execSQL($SQL)->assocAll();
		}

		/**
		 * Creates Error Check Files for current IDEA format
		 *
		 * @return array
		 */
		public static function getErrorHandlers() {
			if (!self::$initialized) self::init();

			$SQL = "
                SELECT resol_file_path,
                       srrefid
                  FROM webset.err_systemreference err
                       INNER JOIN webset.sped_menudef md ON err.smenuid = md.mdrefid
                       INNER JOIN webset.sped_menu    mm ON mm.mdrefid  = md.mdrefid
                 WHERE set_refid = " . self::$id . "
                   AND mitemapp = 1
            ";
			return db::execSQL($SQL)->assocAll();
		}

		/**
		 * Creates Options Array
		 *
		 * @param string $option
		 * @return mixed
		 */
		public static function getIniOptions($option = null) {
			if (!self::$initialized) self::init();

			$SQL = "
                SELECT ini_codeword,
                       COALESCE(value, ini_default) as value
                  FROM webset.sped_ini ini
                       LEFT OUTER JOIN (SELECT sini.irefid, value
                                          FROM webset.sped_ini_set sini
                                         WHERE sini.srefid = " . self::$id . ") as din  ON ini.irefid = din.irefid
                 ORDER BY ini.irefid
            ";
			$result = db::execSQL($SQL);
			$set_ini = array();
			while (!$result->EOF) {
				$set_ini[$result->fields['ini_codeword']] = $result->fields['value'];
				if ($option != null) {
					if ($result->fields['ini_codeword'] == $option) {
						return $result->fields['value'];
					}
				}
				$result->MoveNext();
			}
			return $set_ini;
		}

		/**
		 * Creates Basic Documents Array or Default IDEADocumentType object
		 *
		 * @param bool $default_only
		 * @return IDEADocumentType
		 */
		public static function getDocs($default_only = false) {
			if (!self::$initialized) self::init();

			$docs = array();
			$arr = db::execSQL("
				SELECT drefid
				  FROM webset.sped_doctype
				 WHERE setrefid = " . self::$id . "
				" . ($default_only ? " AND defaultdoc = 'Y' " : "") . "
				 ORDER BY seqnum, doctype
            ")->indexCol(0);

			foreach ($arr as $drefid) {
				$doc = IDEADocumentType::factory($drefid);
				if ($default_only) return $doc;
				$docs[] = $doc;
		    }
			return $docs;
		}

		/**
		 * Get Block Information
		 *
		 * @param int $id
		 * @return array
		 */
		public static function getBlock($id) {
			$SQL = "
                SELECT ieprefid,
                       ieprenderfunc,
                       iepnum,
                       iepdesc,
                       iepinclude,
                       ieptype
                  FROM webset.sped_iepblocks
                 WHERE ieprefid = " . $id . "
                 ORDER BY iepseqnum
            ";
			return db::execSQL($SQL)->assoc();
		}

		/**
		 * Creates Block Items for specified IEDA format
		 *
		 * @param int $doc_id
		 * @return array
		 */
		public static function getDocBlocks($doc_id = 0) {
			if ($doc_id > 0) {
				$SQL = "
                    SELECT ieprefid,
                           ieprenderfunc,
                           iepnum,
                           iepdesc,
                           iepinclude,
                           check_method,
                           check_param
                      FROM webset.sped_iepblocks
                     WHERE ieptype = " . $doc_id . "
                     ORDER BY iepseqnum
                ";
			} else {
				if (!self::$initialized) self::init();
				$SQL = "
                    SELECT ieprefid,
                           ieprenderfunc,
                           iepnum,
                           iepdesc,
                           iepinclude,
                           check_method,
                           check_param
                      FROM webset.sped_iepblocks
                           INNER JOIN webset.sped_doctype ON drefid = ieptype
                     WHERE iepformat = " . self::$id . "
                       AND defaultdoc = 'Y'
                     ORDER BY iepseqnum
                ";
			}
			return db::execSQL($SQL)->assocAll();
		}

		/**
		 * Creates Print Block button for specified url
		 *
		 * @param Array $params
		 * @param integer $doc_id
		 * @param integer $block_id
		 * @return FFMenuButton
		 */
		public static function getPrintButton($params = null, $doc_id = null, $block_id = null) {
			if (!self::$initialized) self::init();
			$SQL = "
                SELECT ieprefid,
                       iepdesc,
                       iepurl,
                       genfile,
                       iepnum,
                       drefid
                  FROM webset.sped_iepblocks
                       LEFT JOIN webset.sped_doctype ON drefid = ieptype
                 WHERE iepformat = " . self::$id . "
                   AND iepurl IS NOT NULL
                   " . ($doc_id === null ? "" : " AND drefid = " . $doc_id) . "
                   " . ($block_id === null ? "" : " AND ieprefid = " . $block_id) . "
                 ORDER BY iepseqnum
            ";
			$allblocks = db::execSQL($SQL)->assocAll();

			$str = '';
			$collected = array();
			$params['print'] = true;

			/** @var FFMenuButton $button */
			$button = FFMenuButton::factory('Print')
				->iconsSize(32)
				->leftIcon('./img/printer.png');

			foreach ($allblocks as $i => $block) {
				if (self::blockExists($block['iepurl']) && !in_array($block['iepnum'], $collected)) {
					$collected[] = $block['iepnum'];
					$str .= $block['iepnum'] . ',';
					$block['genfile'] = IDEADocumentType::factory($block['drefid'])->getPreviewGenFile();
					$url = CoreUtils::getURL($block['genfile'], array_merge(array('str' => $str, 'block_id' => $block['ieprefid'],  'doc_id' => $block['drefid']), $params));
					$button
						->addItem($block['iepdesc'], 'PageAPI.singleton().ajax.process(UIProcessBoxType.REPORT, ' . json_encode($url) . ')', './img/PDF.png');
				}
			}
			return $button;
		}

		/**
		 * Checks whether current file belongs to specified files list
		 *
		 * @param string $allfiles
		 * @return bool
		 */
		private static function blockExists($allfiles) {
			$arr_files = explode("|", $allfiles);
			$thisfile = CoreUtils::getURL(null, array('AMRefID' => null, 'ADRefID' => null, 'pak' => null, 'wak' => null, 'dskey' => null));
			//$arr_files[] = $thisfile;
			//io::trace($arr_files);
			for ($i = 0; $i < count($arr_files); $i++) {
				$tmp = str_replace(SystemCore::$virtualRoot . "/apps/idea" . trim($arr_files[$i]), "", $thisfile);
				if ($tmp != $thisfile) {
					if (strlen($tmp) == 0 || substr($tmp, 0, 1) == "&" || substr($tmp, 0, 1) == "?") {
						return true;
					} else {
						return false;
					}
				}
			}
			return false;
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
