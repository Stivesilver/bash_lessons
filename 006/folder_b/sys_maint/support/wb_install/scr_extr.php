<?PHP
	Security::init();
	CoreUtils::increaseTime();

	define("MODE_GET", "get");
	define("MODE_PUT", "put");

	if (SystemCore::$VndRefID == 1) {
		io::msg('Warning!!!: You are about to export/import data on 1 location. Installation assumes that you would export/import on customer\'s location 2 and higher', false);
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

	if (io::get('mode') == MODE_GET) {
		$installation =  IDEAInstall::factory('spedex')
			->setPhRoot(SystemCore::$physicalRoot)
			->addDir('apps/idea')
			->addDir('applications/webset')
			->setSecRoot(SystemCore::$secDisk)
			->addDirSec('Iep')
			->addDirSec('Eval')
			->setDBName(SystemCore::$DBName)
			->setDBUser(SystemCore::$DBLogin)
			->addDBSchema('webset');

		# Add data to definitions table
		$results = db::execSQL("
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
			 WHERE nspname LIKE 'webset'
			   AND pg_class.relname NOT LIKE '%_seq'
			   AND relkind = 'r'
			   AND datamode::VARCHAR = 'D'
			 ORDER BY pg_class.relname
		")->assocAll();

		$tables = array();
		sortTables($results);

		foreach ($tables as $table) {
			$installation->addDBTableData($table['nspname'] . '.' . $table['relname']);
		}

		if (IDEAFormat::getIniOptions('install_district_defaults_xml')) {
			$installation->addXMLBunch(IDEAFormat::getIniOptions('install_district_defaults_xml'), SystemCore::$VndRefID, 'SELECT VNDREFID');
		}
		if (IDEAFormat::getIniOptions('install_registration_sql')) {
			$installation->addFinalSQL(IDEAFormat::getIniOptions('install_registration_sql'));
		}
		$script = $installation->getScriptGetContent();
		$script_path = $installation->getScriptGetFileName();
		$message = 'Bash script ' . $script_path . ' has been created on ' . $_SERVER['SERVER_NAME'] . '. Please run it as root in bash console to create installation files.';
	} elseif (io::get('mode') == MODE_PUT) {
		$installation =  IDEAInstall::factory('spedex')
			->setPhRoot(SystemCore::$physicalRoot)
			->setSecRoot(SystemCore::$secDisk)
			->setDBName(SystemCore::$DBName)
			->setDBUser(SystemCore::$DBLogin)
			->setVndrefid(SystemCore::$VndRefID);
		$script = $installation->getScriptPutContent();
		$script_path = $installation->getScriptPutFileName();
		$message = 'Bash script ' . $script_path . ' has been created on ' . $_SERVER['SERVER_NAME'] . '. Please run it as root in bash console to install SPEDEX.';
	}

	file_put_contents($script_path, $script);
	print UIMessage::factory($message, UIMessage::NOTE)->toHTML();
	print "<textarea style='width:100%; height:100%;'>" . PHP_EOL . $script . "</textarea>";
?>
