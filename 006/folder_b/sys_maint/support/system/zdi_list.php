<?php
	Security::init();

	$users = db::execSQL("
		SELECT vourefid,
		       vouname,
			   umdesktopzdi,
			   plpgsql_recs_to_str(
			   	   'SELECT umlastname || '' '' || umfirstname || '' ('' || umuid || '')'' AS column
    		    	  FROM sys_usermst
                   	 WHERE umdesktopzdi = ''' || umdesktopzdi || '''
                   	 ORDER BY 1', ', '
               ) as umname
          FROM (SELECT umdesktopzdi
		          FROM sys_usermst um
		         WHERE um.vndrefid = VNDREFID
		           AND umdesktopzdi IS NOT NULL
                   AND umdesktopzdi like '/web_disk%'
                 GROUP BY umdesktopzdi) as t
               LEFT OUTER JOIN sys_voumst ON vourefid = substring(t.umdesktopzdi from '-(\\\\d+).conf')::integer
        ORDER BY 1

	")->assocAll();

	$dataRows = array();

	foreach ($users as $user) {
		if (file_exists(SystemCore::$physicalRoot . $user['umdesktopzdi'])) {
			if (substr($user['umdesktopzdi'], -3) != 'zdi' || !file_exists(CoreUtils::getPhysicalPath($user['umdesktopzdi']))) {
				continue;
			}
			$w = array();
			$w["id"] = $user['umdesktopzdi'];
			$w["vourefid"] = $user['vourefid'];
			$w["vouname"] = $user['vouname'];
			$w["umname"] = $user['umname'];
			$w["umdesktopzdi"] = $user['umdesktopzdi'];
			$desktop = IDEAZdi::factory(CoreUtils::getPhysicalPath($w["id"]));
			$w["win"] = $desktop->getParam('WINDOW', 'WIN_SCHEME') . ', ' . $desktop->getParam('WINDOW', 'IC_SCHEME') . ', ' . $desktop->getParam('WINDOW', 'WIN_TOOLBAR') . ', ' . $desktop->getParam('WINDOW', 'WIN_ICONBAR');
			$w["dtype"] = $desktop->getParam('DESKTOP', 'DTYPE');
			$w["dtopmenu"] = $desktop->getParam('DESKTOP', 'DTOPMENU');
			$w["background"] = $desktop->getParam('DESKTOP', 'BGCOLOR') . ', ' . $desktop->getParam('DESKTOP', 'BGPICTURE') . ', ' . $desktop->getParam('DESKTOP', 'BGPICTUREALIGN');
			$w["font"] = $desktop->getParam('DESKTOP', 'BGFONTNAME') . ', ' . $desktop->getParam('DESKTOP', 'BGFONTSIZE') . ', ' . $desktop->getParam('DESKTOP', 'BGFONTCOLOR');
			$w["css"] = $desktop->getParam('DESKTOP', 'CSSFILE');
			$dataRows[] = $w;
		}
	}

	$list = new ListClass();

	$list->fillData($dataRows);
	$list->hideCheckBoxes = false;

	$list->pageCount = 2000;

	$list->addSearchField('Location', 'vourefid', 'select')
		->sql("
			SELECT vourefid,
			       vouname
			  FROM sys_voumst
			 WHERE vndrefid = VNDREFID
			 ORDER BY 2
		");
	$list->addSearchField('User', "LOWER(umname)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('File', "LOWER(umdesktopzdi)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Window', "LOWER(win)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Desktop Type', 'dtype', 'list')
		->name('dtype')
		->data(array('R' => 'Standard', 'I' => 'Interactive'));
	$list->addSearchField('Top Menu', 'dtopmenu');
	$list->addSearchField('Background', "LOWER(background)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('Font', "LOWER(font)  like '%' || LOWER('ADD_VALUE') || '%'");
	$list->addSearchField('CSS', "LOWER(css)  like '%' || LOWER('ADD_VALUE') || '%'");

	$list->showSearchFields = true;

	$list->addColumn('Location')->sqlField('vouname')->type('group');
	$list->addColumn('User(s)')->sqlField('umname');
	$list->addColumn('Login')->sqlField('umuid');
	$list->addColumn('File')->sqlField('umdesktopzdi');
	$list->addColumn('Window')->sqlField('win');
	$list->addColumn('Desktop')->sqlField('dtype');
	$list->addColumn('Top Menu')->sqlField('dtopmenu');
	$list->addColumn('Background')->sqlField('background');
	$list->addColumn('Font')->sqlField('font');
	$list->addColumn('CSS')->sqlField('css');

	$list->addButton(
		FFMenuButton::factory('Process')
			->addItem('Change Style', 'changeZDI("zdi_edit.php")')
			->addItem('Rename Icons', 'changeZDI("zdi_icons_rename.php")')
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('sys_usermst')
			->setKeyField('umdesktopzdi')
			->applyListClassMode()
	);

	$list->editURL = 'javascript:api.window.open("' . SystemCore::$virtualRoot . 'AF_REFID", "' . SystemCore::$virtualRoot . 'AF_REFID");';

	print $list->toHTML();
?>
<script type='text/javascript'>
		function changeZDI(url) {
			ids = ListClass.get().getSelectedValues().values;
			if (ids == '') {
				api.alert('Please select at least one record');
				return;
			}
			api.ajax.post(
				'zdi_edit.ajax.php',
				{'ids': ids},
			function(answer) {
				var wnd = api.window.open('Set Parameters for Selected Desktops', api.url(url, {'dskey': answer.dskey}));
				wnd.resize(950, 600);
				wnd.center();
				wnd.addEventListener('desktops_updated', function() {
					api.reload();
				});
				wnd.show();
			}
			)
		}
</script>