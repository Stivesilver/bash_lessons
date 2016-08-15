<?PHP
	Security::init();

    $trying = 0;

    function tableId($tablename, $arr) {
        for ($i=0; $i<count($arr); $i++) {
             if ($arr[$i]["table"]==$tablename) return $i;
        }
    }

    function liftMaster($tableId, $masterId, $arr) {
        global $trying;
        $trying++;
        if ($trying>20) {die("Too many recursive cycles");}
        $j = 0;
        for ($i=0; $i<count($arr); $i++) {
            if ($masterId==$i) continue;
            if ($tableId==$i) {
                $newArr[$j] = $arr[$masterId];
                $newArr[$j+1] = $arr[$tableId];
                $j = $j + 2;
                continue;
            }
            $newArr[$j] = $arr[$i];
            $j++;
        }
        if ($newArr[$tableId]["master"]!="") {
            if ($tableId<tableId($newArr[$tableId]["master"], $newArr))
	            $newArr = liftMaster($tableId, tableId($newArr[$tableId]["master"], $newArr), $newArr);
	    }
        return $newArr;
    }

    function reOrder($tables){

	    for ($i=0; $i<count($tables); $i++) {
	         if ($tables[$i]["master"]) {
	              if (tableId($tables[$i]["master"], $tables)>tableId($tables[$i]["table"], $tables)) {
                      //if($masterTable=="std_bgb_goal") {die(tableId($tables[$i]["master"], $tables). "<hr>");}
	                  $tables = liftMaster(tableId($tables[$i]["table"], $tables), tableId($tables[$i]["master"], $tables), $tables);
	              }
	         }
	    }
        return $tables;
    }

    if ($_GET["schema"]=="-1") $_GET["schema"] = "webset%";

	$results = db::execSQL("
		SELECT relname,
               datamode,
               (SELECT pg_classf.relname
                  FROM pg_constraint, pg_class AS pg_classf, pg_namespace AS pg_namespacef
                 WHERE pg_constraint.conrelid = pg_class.oid
                   AND pg_constraint.confrelid = pg_classf.oid
                   AND pg_classf.relnamespace=pg_namespacef.oid
                   AND pg_namespacef.nspname = pg_namespace.nspname
                   AND contype='f'
                 LIMIT 1) AS master
          FROM pg_class
               INNER JOIN pg_namespace ON pg_class.relnamespace = pg_namespace.oid
               LEFT OUTER JOIN webset.sys_install ON  relname = webset_table
         WHERE nspname LIKE '" . io::get('schema') . "'
           AND pg_class.relname not like '%_seq'
           AND (states like '%" . io::get('state') . "%' OR '" . io::get('state') . "'='-1' OR states is NULL)
           AND relkind = 'r'
         ORDER BY pg_class.relname
    ")->assocAll();


                      print "<pre>";

    $tables = array();
	foreach ($results as $result) {
		$w = array();
		$w["table"]    = $result['relname'];
		$w["master"]   = $result['master'];
		$w["datamode"] = $result['datamode'];
		$tables[] = $w;
	}
    //$tables = reOrder($tables);

    $script = "<pre>";
    for ($i=0; $i<count($tables); $i++) {
         if ($tables[$i]["datamode"]!="N")  $script .= "psql lumen < /tmp/webset/" . $tables[$i]["table"] . ".sql;\n";
    }

    print $script;
?>
