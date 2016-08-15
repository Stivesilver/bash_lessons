<?php
	Security::init();

	db::execSQL("
		INSERT INTO webset.sys_install
                (webset_table)
            SELECT relname
	          FROM pg_class, pg_namespace
	         WHERE pg_class.relnamespace = pg_namespace.oid
	           AND nspname LIKE 'webset%'
	           AND pg_class.relname NOT LIKE '%_seq'
	           AND relkind = 'r'
               AND relname NOT IN (SELECT webset_table FROM webset.sys_install)
	         ORDER BY pg_class.relname
	");

	$list = new listClass();

	$list->title = 'Prepare install Scripts';
	$list->showSearchFields = true;

	$list->SQL = "
        SELECT refid,
               relname,
               CASE datamode
               WHEN 'D' THEN 'Include Data'
               WHEN 'S' THEN 'Structure Only'
               WHEN 'N' THEN 'Table not needed'
               END as table_mode,
               CASE states IS NOT NULL WHEN TRUE THEN
                    plpgsql_recs_to_str('
                        SELECT CAST(state AS VARCHAR) AS column
                          FROM webset.glb_statemst
                         WHERE staterefid in ('|| COALESCE(states,'0') || ')
                         ORDER BY 1', ', ')
                         ELSE 'All' END AS states,
               psql
          FROM pg_class
               INNER JOIN pg_namespace ON pg_class.relnamespace = pg_namespace.oid
               LEFT OUTER JOIN webset.sys_install ON  relname = webset_table
         WHERE nspname LIKE 'webset%'
           AND pg_class.relname NOT LIKE '%_seq'
           AND relkind = 'r'
          	   ADD_SEARCH
         ORDER BY pg_class.relname
    ";

	$list->addSearchField('Schema', 'nspname', 'list')
		->sql("
            SELECT nspname,
                   nspname
              FROM pg_namespace
		     WHERE nspname LIKE 'webset%'
        ")
		->value('webset')
		->name('schema');

	$list->addSearchField('Data Mode', 'datamode', 'list')
		->sql("
            SELECT 'D', 'Include Data'
             UNION
            SELECT 'S', 'Structure Only'
             UNION
            SELECT 'N', 'Table not needed'
        ");

	$list->addSearchField('State', 'lower(relname)', 'list')
		->sql("
           SELECT staterefid, state
             FROM webset.sped_sm_area
                  LEFT JOIN webset.glb_statemst ON glb_statemst.staterefid = screfid
            WHERE screfid > 0
            GROUP BY staterefid, state
            ORDER BY 1
        ")
		->name('state');

	$list->addColumn('Table')->sqlField('relname');
	$list->addColumn('Data')->sqlField('table_mode');
	$list->addColumn('State')->sqlField('states');
	$list->addColumn('Export as psql')->sqlField('psql');

	$list->editURL = CoreUtils::getURL('./inst_edit.php');

	$list->addButton(FFButton::factory('Extract Script'))->onClick('scr_extr()');
	$list->addButton(FFButton::factory('Load Script'))->onClick('scr_load()');

	$list->deleteTableName = "webset.sys_install";
	$list->deleteKeyField = "refid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.sys_install')
			->setKeyField('refid')
			->applyListClassMode()
	);


	$list->printList();
?>
<script>
	function scr_extr() {
		var url = api.url('./scr_extr.php', {'mode': 'get', 'schema': $('#schema').val(), 'state': $('#state').val()});
		api.window.open('Extract Script', url).addEventListener(
			WindowEvent.CLOSE,
			function (e) {
			},
			null,
			null
		);
	}

	function scr_load() {
		var url = api.url('./scr_extr.php', {'mode': 'put', 'schema': $('#schema').val(), 'state': $('#state').val()});
		api.window.open('Extract Script', url).addEventListener(
			WindowEvent.CLOSE,
			function (e) {
			},
			null,
			null
		);
	}
</script>
