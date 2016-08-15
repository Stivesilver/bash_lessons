<?php

	/**
	 * Contains basic definitions methods
	 *
	 * @copyright Lumen Touch, 2013
	 */
	final class IDEADef extends RegularClass {

		/**
		 * Returns list of the instances of IDEADefValidValue.
		 * Output array format:
		 *       [
		 *               <IDEADefValidValue>,
		 *               <IDEADefValidValue>,
		 *               ....
		 *       ]
		 *
		 * @return array.<IDEADefValidValue>
		 */
		public static function getValidValues($area = null) {
			if ($area === null) throw new Exception('Area is not specified.');
			return IDEADefValidValue::createInstances(
				SystemCore::$DBUtils->execSQL(self::getValidValueSql($area))->indexCol()
			);
		}

		/**
		 * Return SQL for validValues. Often use in Edit, List or in libs
		 *
		 * @param string|array $area valuename or valuename and param WHERE
		 * @param null|string $col if null get all cols else cols from string $col
		 * @return string
		 */
		public static function getValidValueSql($area, $col = null) {
			$where = "WHERE valuename = ";

			if (is_string($area)) {
				$valuename = $area;
				$where .= "'$valuename'";
			} else {
				# array. Need add params to block WHERE
				$valuename = $area[0];
				$where .= "'$valuename' AND $area[1]";
			}

			$SQL = "
				SELECT ";
			if ($col == null) {
				$SQL .= "*";
			} else {
				$SQL .= $col;
			}

			$SQL .= "
				  FROM webset.glb_validvalues
                $where
                   AND (glb_enddate IS NULL or now()< glb_enddate)
                 ORDER BY sequence_number";
			return $SQL;
		}

		public static function getConstructionTemplate($id = null) {
			if ($id === null) throw new Exception('Construction ID is not specified.');
			$xml = db::execSQL("
				SELECT cnbody
	              FROM webset.sped_constructions
	             WHERE cnrefid = $id
			")->getOne();

			if (!str::stristr($xml, '<doc>')) {
				$xml = '<doc>' . PHP_EOL . $xml;
			}

			if (!str::stristr($xml, '</doc>')) {
				$xml = $xml . PHP_EOL . '</doc>';
			}

			$repl = array(
				'<doc>' 	=> '<doc>',
				'</doc>' 	=> '</doc>'
			);

			$xml = str_ireplace(array_keys($repl), array_values($repl), $xml);

			try {
				$doc = new SimpleXMLElement($xml);
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}

			return $xml;
		}

	}

?>
