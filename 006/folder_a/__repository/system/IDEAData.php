<?php

	define('SHELL_DB_GET_ALTER_COLUMNS', SystemCore::$physicalRoot . dirname(CoreUtils::getAbstractPath(__FILE__)) . '/sh/postgres_get_altercolumns.sh');

	/**
	 * IDEA Data class
	 * This class provides DB utilities such as XML Export/Import
	 *
	 * @final
	 * @copyright Lumen Touch, 2012
	 */
	class IDEAData extends RegularClass {

		/**
		 * Input root Id for import/export
		 *
		 * @var mixed
		 */
		private $rootId;

		/**
		 * Template SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $xmlTemplate;

		/**
		 * Tables Template SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $xmlTemplateTables;

		/**
		 * Fields Template SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $xmlTemplateFields;

		/**
		 * Resulting SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $xmlOutput;

		/**
		 * Resulting SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $deleteRows;

		/**
		 * Resulting SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $insertRows;

		/**
		 * Resulting SimpleXMLElement object
		 *
		 * @var SimpleXMLElement
		 */
		private $updateRows;

		/**
		 * Show Sql Parametr
		 */
		private $showSql;

		/**
		 * Returns Key Field of Table
		 * Example: getKeyField('webset.dmg_studentmst') = 'stdrefid'
		 *
		 * @param string $table
		 * @return string
		 */
		public static function getKeyField($table = '') {
			if ($table == '') throw new Exception('Please specify table name.');
			$SQL = "
				SELECT a.attname
                  FROM pg_class c, pg_attribute a,pg_type t, pg_namespace n
                 WHERE relkind = 'r' AND nspname || '.' || c.relname='" . $table . "'
                   AND c.relnamespace = n.oid
                   AND a.attnum > 0
                   AND a.atttypid = t.oid
                   AND a.attrelid = c.oid
                   AND (SELECT indisprimary
                               FROM pg_index i, pg_class ic, pg_attribute ia
                              WHERE i.indrelid = a.attrelid
                                AND i.indexrelid = ic.oid
                                AND ic.oid = ia.attrelid
                                AND ia.attname = a.attname
                                AND indisprimary IS NOT NULL
                              ORDER BY indisprimary DESC
                              LIMIT 1)
                 ORDER BY a.attnum
			";
			return db::execSQL($SQL)->getOne();
		}

		/**
		 * Returns Fields of Table
		 * Example: getTableFields('webset.dmg_studentmst') = [["stdrefid", "x"], ["stdlnm", ""], ...]
		 *
		 * @param string $table
		 * @param bool $includeKey
		 * @return array
		 */
		public static function getTableFields($table = '', $includeKey = true) {
			if ($table == '') throw new Exception('Please specify table name.');
			if (!$includeKey) {
				$where = "
                    AND CASE (SELECT indisprimary
                                FROM pg_index i, pg_class ic, pg_attribute ia
                               WHERE i.indrelid = a.attrelid
                                 AND i.indexrelid = ic.oid
                                 AND ic.oid = ia.attrelid
                                 AND ia.attname = a.attname
                                 AND indisprimary IS NOT NULL
                               ORDER BY indisprimary DESC
                               LIMIT 1) WHEN TRUE THEN 'x' ELSE '' END != 'x'";
			} else {
				$where = "";
			}
			$SQL = "
                SELECT a.attname
                  FROM pg_class c, pg_attribute a,pg_type t, pg_namespace n
                 WHERE relkind = 'r'
                   AND nspname || '.' || c.relname = '" . $table . "'
                   AND c.relnamespace = n.oid
                   AND a.attnum > 0
                   AND a.atttypid = t.oid
                   AND a.attrelid = c.oid
                   " . $where . "
                 ORDER BY a.attnum
            ";

			return db::execSQL($SQL)->indexCol(0);
		}

		/**
		 * Generates SELECT scripts for specified table
		 *
		 * @param string $table
		 * @param string $id_name
		 * @param string $id_vals
		 * @return string
		 */
		public static function getSelects($table, $id_name, $id_vals) {
			$fields = self::getTableFields($table, true);
			foreach (explode(',', $id_vals) as $value) $svalues[] = "'" . db::escape($value) . "'";
			return "SELECT " . implode(',' . PHP_EOL . '       ', $fields) . PHP_EOL .
			"  FROM " . $table . " " . PHP_EOL .
			" WHERE " . $id_name . " IN (" . ($id_vals == '' ? '0' : implode(', ', $svalues)) . ")" . PHP_EOL .
			" ORDER BY 1 DESC";
		}

		/**
		 * Generates INSERT scripts for specified table
		 *
		 * @param string $table
		 * @param string $id_name
		 * @param string $id_vals
		 * @param string $innerJoin
		 * @return string
		 */
		public static function getInserts($table, $id_name, $id_vals, $innerJoin = '') {

			$script_lines = array();
			$valuesForDel = array();
			$keyField = self::getKeyField($table);
			$fields = self::getTableFields($table, true);
			foreach ($fields as $field) $sfields[] = $table . '.' . $field;

			$SQL = "
                SELECT " . implode(', ', $sfields) . "
                  FROM " . $table . " " . $innerJoin . "
                 WHERE " . $id_name . " IN (" . ($id_vals == '' ? '0' : $id_vals) . ")
            ";

			//START INSERT CREATE
			$result = db::execSQL($SQL);

			while (!$result->EOF) {
				$values = array();
				while ($ind = each($result->fields)) {
					$fld = each($result->fields);
					$val = "'" . db::escape($fld['value']) . "'";
					if ($fld['key'] == $keyField && $keyField != '' && $fld['value'] > 0) $valuesForDel[] = $val;
					if ($val == "''") $val = 'NULL';
					$values[] = $val;
				}
				$script_lines[] = "INSERT INTO $table " . PHP_EOL . "(" . implode(', ', $fields) . ") " . PHP_EOL . "VALUES " . PHP_EOL . "(" . implode(', ', $values) . ");";
				$result->MoveNext();
			}

			if ($keyField != '' && count($valuesForDel) > 0) {
				array_unshift($script_lines, "DELETE FROM " . $table . " WHERE " . $keyField . " IN (" . implode(', ', $valuesForDel) . ");");
			} else {
				array_unshift($script_lines, "DELETE FROM " . $table . " WHERE " . $id_name . " IN (" . ($id_vals == '' ? '0' : $id_vals) . ");");
			}

			array_unshift($script_lines, "/* --------  " . strtolower($table) . " --------- */");

			return implode(PHP_EOL . PHP_EOL, $script_lines);
		}

		/**
		 * Generates UPDATE scripts for specified table
		 *
		 * @param string $table
		 * @param string $id_name
		 * @param string $id_vals
		 * @param string $innerJoin
		 * @return string
		 */
		public static function getUpdates($table, $id_name, $id_vals, $innerJoin = '') {

			$script_lines = array();
			$keyField = self::getKeyField($table);
			$fields = self::getTableFields($table, true);
			foreach ($fields as $field) $sfields[] = $table . '.' . $field;

			$SQL = "
                SELECT " . implode(', ', $sfields) . "
                  FROM " . $table . " " . $innerJoin . "
                 WHERE " . $id_name . " IN (" . ($id_vals == '' ? '0' : $id_vals) . ")
            ";

			//START UPDATE CREATE
			$result = db::execSQL($SQL);

			while (!$result->EOF) {
				$sets = array();
				$script = "UPDATE " . $table . " SET " . PHP_EOL;
				while ($ind = each($result->fields)) {
					$fld = each($result->fields);
					if ($fld['value'] == '') {
						$fld['value'] = 'NULL';
					} else {
						$fld['value'] = "'" . db::escape($fld['value']) . "'";
					}
					if ($fld['key'] == strtolower($keyField)) {
						$id = $fld['value'];
						continue;
					}
					$sets[] = $fld['key'] . " = " . $fld['value'];

				}
				$script .= implode(',' . PHP_EOL, $sets) . PHP_EOL;
				$script .= "WHERE " . $keyField . " = " . $id . ";";
				$script_lines[] = $script;
				$result->MoveNext();
			}

			array_unshift($script_lines, "/* --------  " . strtolower($table) . " --------- */");

			return implode(PHP_EOL . PHP_EOL, $script_lines);
		}

		/**
		 * Generates TTL scripts for specified table
		 *
		 * @param string $table
		 * @param bool $structure_only
		 * @return string
		 */
		public static function getTTL($table, $structure_only = true, $alter_table = false) {
			$file = SystemCore::$tempPhysicalRoot . '/' . $table . '.sql';

			exec('pg_dump -v -U postgres --no-owner ' . ($structure_only ? '--schema-only ' : '--insert --column-inserts ') . '--table=' . $table . ' ' . SystemCore::$DBName . ' > ' . $file);

			if (!$structure_only) {
				# add UPDATE SQLs to final dump file
				$cmd = '
					sql_update=$(' . IDEAShellUtils::getPathDBInsert2Update() . ' "' . $file . '")
					echo "$sql_update" >> "' . $file . '"
				';
				exec($cmd); 
				io::download($file);
				die();
			}

			$data = file_get_contents($file);
			if ($alter_table) {
				$output = '';
				preg_match('/search_path = (\w+),/', $data, $out);
				$schema = $out[1];
				preg_match('/CREATE TABLE (\S+) .+?\);/s', $data, $out);
				$script = $out[0];
				$table = $out[1];
				preg_match_all('/ {4}(.+?)[\,\n]/', $script, $out);
				$fields = $out[1]; 
				foreach ($fields as $line) {
					$output .= "ALTER TABLE $schema.$table ADD COLUMN $line;\r\n";
					preg_match('/^(\w+) /', $line, $out);
					$field = $out[1];
					preg_match_all('/ALTER TABLE [^;]+? \(' . $field . '\).*?;/', $data, $out);
					foreach ($out[0] as $additional) {
						$output .= "SET search_path TO $schema;"  . "\r\n" . $additional . "\r\n";
					} 
					$output .= "\r\n";
				}
				return $output;
			}
			return $data;
		}

		/**
		 * Returns DB data in XML format
		 *
		 * @param string $template
		 * @param string|int $id
		 * @param string $showSql
		 * @return string
		 * @throws Exception
		 */
		public function xmlExport($template = '', $id = '', $showSql ='N') {
			$this->showSql = $showSql;
			if ($template == '') throw new Exception('Please specify xml template.');
			if ($id == '') throw new Exception('Please specify root id.');

			$this->xmlTemplate = new SimpleXMLElement($template);
			$this->rootId = $id;
			$this->xmlOutput = null;

			$this->xmlTemplateTables = $this->xmlTemplate->xpath('/template/tables');
			$this->xmlTemplateTables = $this->xmlTemplateTables[0]->children();
			$this->xmlTemplateTables = new SimpleXMLElement($this->xmlTemplateTables[0]->asXML());

			if (isset($this->xmlTemplate->fields)) {
				$this->xmlTemplateFields = $this->xmlTemplate->xpath('/template/fields');
				$this->xmlTemplateFields = new SimpleXMLElement($this->xmlTemplateFields[0]->asXML());
			}

			$this->readAndRun($this->xmlTemplateTables, 'getExportRows');
			$this->readAndRun($this->xmlOutput, 'removeKeys');

			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($this->xmlOutput->asXML());
			return $dom->saveXML();
		}

		/**
		 * Generates xml elements according DB data
		 *
		 * @param SimpleXMLElement $node
		 */
		private function getExportRows($node) {

			$element = $node->getName();
			if ($this->xmlOutput == null) {
				$this->xmlOutput = new SimpleXMLElement('<' . $element . '/>');
				$this->xmlOutput->addAttribute($this->getKeyField($element), $this->rootId);
			} else {
				$mask = '';
				$tempNode = $this->getParentNode($node);
				while ($tempNode) {
					$mask = '/' . $tempNode->getName() . $mask;
					$tempNode = $this->getParentNode($tempNode);
				}
				$outputParentNode = $this->xmlOutput->xpath($mask);
				$cleanfields = $this->getTableFields($element);
				$fields = $cleanfields;
				#add special fields values
				if ($this->xmlTemplateFields != null and $this->xmlTemplateFields->$element) {
					foreach ($this->xmlTemplateFields->$element->children() as $child) {
						foreach ($fields as $i => $value) {
							if ($value == $child->getName() && $child->out) {
								$fields[$i] = '(' . $child->out . ') as ' . $value;
								break;
							}
						}
					}
				}
				while (list(, $foundNode) = each($outputParentNode)) {
					$SQL = "
                        SELECT " . implode(',', $fields) . "
                          FROM " . $element . "
                         WHERE " . $this->getAttribute($node, 'parent_id') . " in ('" . $this->getAttribute($foundNode, $this->getKeyField($foundNode->getName())) . "')
                    ";
					if ($this->getAttribute($node, 'where')) {
						$SQL .= "
                          AND " . $this->getAttribute($node, 'where') . "
                        ";
					}
					$result = db::execSQL($SQL);
					while (!$result->EOF) {
						$newNode = $foundNode->addChild($element);
						for ($i = 0; $i < count($cleanfields); $i++) {
							$newNode->addAttribute($cleanfields[$i], $result->fields[$i]);
						}
						$this->deleteRows .= "DELETE FROM " . $element . " WHERE " . $this->getKeyField($element) . "=" . $result->fields[0] . ";\n";

						$result->MoveNext();
					}
					if ($this->showSql == 'Y') {
						//get inserts
						$SQL = "
	                        SELECT " . implode(',', $cleanfields) . "
	                          FROM " . $element . "
	                         WHERE " . $this->getAttribute($node, 'parent_id') . " in ('" . $this->getAttribute($foundNode, $this->getKeyField($foundNode->getName())) . "')
	                    ";
						if ($this->getAttribute($node, 'where')) {
							$SQL .= "
	                          AND " . $this->getAttribute($node, 'where') . "
	                        ";
						}
						$result = db::execSQL($SQL);
						while (!$result->EOF) {
							$insert_fields = ' (';
							$insert_values = ' (';
							for ($i = 0; $i < count($cleanfields); $i++) {
								if ($i == count($cleanfields) - 1) {
									$insert_fields .= $cleanfields[$i] . ')';
									$insert_values .= $result->fields[$i] == '' ? 'NULL)' : "'" . pg_escape_string($result->fields[$i]) . "')";
								} else {
									$insert_fields .= $cleanfields[$i] . ', ';
									$insert_values .= $result->fields[$i] == '' ? 'NULL, ' : "'" . pg_escape_string($result->fields[$i]) . "', ";
								}
							}
							$this->insertRows .= "INSERT INTO " . $element . $insert_fields . " VALUES" . $insert_values . ";\n";
							$this->updateRows .= "UPDATE " . $element . " SET" . $insert_fields . " = " . $insert_values . " WHERE " . $this->getKeyField($element) . "=" . $result->fields[0] . ";\n";

							$result->MoveNext();
						}
					}
				}
			}
		}

		/**
		 * Removes key fields and parent_id attributes
		 *
		 * @param SimpleXMLElement $node
		 */
		private function removeKeys($node) {
			$element = $node->getName();
			$out = $this->xmlTemplateTables->xpath('//' . $element);
			if ($this->getAttribute($out[0], 'parent_id')) {
				unset($node[$this->getAttribute($out[0], 'parent_id')]);
			}
			if ($this->getAttribute($out[0], 'include_key') != '1') {
				unset($node[$this->getKeyField($element)]);
			}
		}

		/**
		 * Loads data into DB
		 *
		 * @param string $xmldata
		 * @param string|int $id
		 * @return string
		 */
		public function xmlImport($template = '', $id = '', $data = '') {
			if ($template == '') throw new Exception('Please specify xml template.');
			if ($id == '') throw new Exception('Please specify root id.');
			if ($data == '') throw new Exception('Please specify xml data.');

			$this->rootId = $id;
			$this->xmlTemplate = new SimpleXMLElement($template);

			$this->xmlTemplateTables = $this->xmlTemplate->xpath('/template/tables');
			$this->xmlTemplateTables = $this->xmlTemplateTables[0]->children();
			$this->xmlTemplateTables = new SimpleXMLElement($this->xmlTemplateTables[0]->asXML());

			$this->xmlTemplateFields = $this->xmlTemplate->xpath('/template/fields');
			$this->xmlTemplateFields = new SimpleXMLElement($this->xmlTemplateFields ? $this->xmlTemplateFields[0]->asXML() : '<fields/>');

			$this->xmlOutput = new SimpleXMLElement($data);
			$this->xmlOutput->addAttribute($this->getKeyField($this->xmlOutput->getName()), $this->rootId);

			$this->readAndRun($this->xmlOutput, 'importRows');
			print 'Import Completed.';
		}

		/**
		 * Generates xml elements according DB data
		 *
		 * @param SimpleXMLElement $node
		 */
		private function importRows($node) {
			$element = $node->getName();
			if ($this->getParentNode($node)) {
				$out = $this->xmlTemplateTables->xpath('//' . $element);

				if (empty($out)) throw new Exception($element . ' is not present in template.');

				if ($this->getAttribute($out[0], 'include_key') == '1') {
					$fields = $this->getTableFields($element, true);
				} else {
					$fields = $this->getTableFields($element, false);
				}
				#add parent_id field attribute
				$out = $this->xmlTemplateTables->xpath('//' . $element);
				$parentLink = $this->getAttribute($out[0], 'parent_id');
				$parentNode = $this->getParentNode($node);
				$parentId = $parentNode[$this->getKeyField($parentNode->getName())];
				if ($parentLink && $parentId) {
					$node->addAttribute($parentLink, $parentId);
				}

				if ($this->xmlTemplateFields->$element) {
					foreach ($this->xmlTemplateFields->$element->children() as $child) {
						foreach ($node->attributes() as $a => $v) {
							if ($a == $child->getName()) {
								if (isset($child->in)) {
									$SQL = $child->in;
									$SQL = str_replace('AUTOFIELD_PARENT_ID', $this->escape($parentId), $SQL);
									$SQL = str_replace('AUTOFIELD_THIS', $this->escape($v), $SQL);
									$node[$a] = db::execSQL($SQL)->getOne();
									break;
								}
							}
						}
					}
				}

				$dbRec = DBImportRecord::factory($element, $this->getKeyField($element));
				if ($this->getAttribute($out[0], 'include_key') == '1') {
					$forceKey = $this->getKeyField($element);
				} else {
					$forceKey = '';
				}
				foreach ($fields as $a => $v) {
					if ($node[$v] == '') {
						$dbRec->set($v, 'NULL', true);
					} else {
						$fieldSettings = $this->xmlTemplateFields->$element->$v;
						if ($fieldSettings['key'] == '1' || $v == $forceKey) {
							$dbRec->key($v, $node[$v]);
						} else {
							$dbRec->set($v, $node[$v]);
						}
					}
				}
				$dbRec->import();
				$newId = $dbRec->recordID();
				if ($this->getAttribute($out[0], 'include_key') != '1') {
					$node->addAttribute($this->getKeyField($element), $newId);
				}
			}
		}

		/**
		 * Reads each node of template and create export element for it
		 *
		 * @param SimpleXMLElement $node
		 */
		private function readAndRun($node, $method) {
			if (method_exists($this, $method)) $this->$method($node);
			foreach ($node->children() as $child) {
				$this->readAndRun($child, $method);
			}
		}

		/**
		 * Returns Parent Node
		 *
		 * @param SimpleXMLElement $node
		 * @return SimpleXMLElement
		 */
		public static function getParentNode($node) {
			return current($node->xpath('parent::*'));
		}

		/**
		 * put your comment there...
		 *
		 * @param SimpleXMLElement $node
		 * @param string $attribute
		 * @return string
		 */
		public static function getAttribute($node, $attribute) {
			if ($attribute == '') throw new Exception('Attribute can not be blank.');
			foreach ($node->attributes() as $a => $v) {
				$temp[$a] = $v;
			}
			if (isset($temp[$attribute])) return $temp[$attribute];
		}

		/**
		 * Returns delete scripts created by xmlExport methos
		 *
		 * @param string $node
		 */
		public function getDeleteRows() {
			return $this->deleteRows;
		}

		/**
		 * Returns Inserte scripts created by xmlExport methos
		 *
		 * @return SimpleXMLElement
		 */
		public function getInsertRows() {
			return $this->insertRows;
		}

		/**
		 * Returns Inserte scripts created by xmlExport methos
		 *
		 * @return SimpleXMLElement
		 */
		public function getUpdateRows() {
			return $this->updateRows;
		}

		/**
		 * Return Main Table Name
		 *
		 * @param $fields
		 * @param $sql
		 * @return string
		 */
		public static function tableName($fields, $sql, $dbcon) {

			$sql = str_replace("\"", "", $sql);
			if ($dbcon) {
				$sql = DBUtils::factory($dbcon)->escape($sql);
			} else {
				$sql = db::escape($sql);
			}

			for ($i = 0; $i < count($fields); $i++) {
				$fields[$i] = "'" . $fields[$i] . "'";
			}
			if ($dbcon) {
				return DBUtils::factory($dbcon)->execSQL("
		            SELECT nspname || '.' ||relname
		              FROM pg_class c, pg_attribute a,pg_type t, pg_namespace n
		             WHERE relkind = 'r'
		               AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid
		               AND c.relnamespace = n.oid
		               AND attname IN (" . implode(',', $fields) . ")
		               AND (LOWER('" . $sql . "') LIKE '%' || nspname || '.' ||relname || '%' OR
		                   (LOWER('" . $sql . "') LIKE '%' || relname || '%'  AND nspname = 'public'))
		             GROUP BY  relname, nspname
		             ORDER BY count(1) DESC
		             LIMIT 1
		        ")->getOne();
			} else {
				return db::execSQL("
		            SELECT nspname || '.' ||relname
		              FROM pg_class c, pg_attribute a,pg_type t, pg_namespace n
		             WHERE relkind = 'r'
		               AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid
		               AND c.relnamespace = n.oid
		               AND attname IN (" . implode(',', $fields) . ")
		               AND (LOWER('" . $sql . "') LIKE '%' || nspname || '.' ||relname || '%' OR
		                   (LOWER('" . $sql . "') LIKE '%' || relname || '%'  AND nspname = 'public'))
		             GROUP BY  relname, nspname
		             ORDER BY count(1) DESC
		             LIMIT 1
		        ")->getOne();
			}
		}

		/**
		 * Creates an instance of this class
		 *
		 * @return IDEAData
		 */
		public static function factory() {
			return new IDEAData();
		}

	}

?>
