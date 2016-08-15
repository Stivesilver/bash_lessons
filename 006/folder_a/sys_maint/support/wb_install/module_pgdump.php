<?PHP
	Security::init(NO_OUTPUT | MODE_WS, 1);

	define("SRC_DB_SCHEMA", 'webset');

	function getTables() {
		return db::execSQL("
			SELECT relname,
				   datamode,
				   psql,
				   nspname,
				   array_to_string(
					   ARRAY(
						   SELECT cl.relname AS child_table
							 FROM pg_class cl
								  INNER JOIN pg_namespace ns ON cl.relnamespace = ns.oid
								  INNER JOIN pg_constraint con1 ON con1.conrelid = cl.oid
							WHERE con1.contype = 'f'
							  AND pg_class.oid = con1.confrelid
							  AND cl.relname NOT LIKE pg_class.relname
							ORDER BY 1
					   ),
					   ','
				   ) AS children_tables
			  FROM pg_class
				   INNER JOIN pg_namespace ON pg_class.relnamespace = pg_namespace.oid
				   LEFT OUTER JOIN webset.sys_install ON relname = webset_table
			 WHERE nspname LIKE '" . SRC_DB_SCHEMA . "'
			   AND pg_class.relname NOT LIKE '%_seq'
			   AND relkind = 'r'
			   AND datamode = 'D'
			 ORDER BY pg_class.relname
		")->assocAll();
	}

	function sortTables($results) {
		global $tables;
		foreach ($results as $i => $result) {
			$unset = true;
			foreach ($results as $res) {
				$ftables = explode(',', $res['children_tables']);
				if (in_array($result['relname'], $ftables)) {
					$unset = false;
				}
			}
			if ($unset) {
				$tables[] = $result;
				unset($results[$i]);
			}
		}

		if ($results) {
			sortTables($results);
		} else {
			return;
		}
	}

	$tables = array();
	$tables_start = getTables();
	sortTables($tables_start);
	$tables = array_map(create_function('$a', 'return $a["nspname"] . "." . $a["relname"];'), $tables);

	echo implode("\n", $tables);

?>
