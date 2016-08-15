<?php

	Security::init();
	
	$tsRefID = io::geti('tsRefID');
	$stdrefid = io::geti('stdrefid');
	$winame = io::get('winame');
	$std_title = db::execSQL("
		SELECT stdfnm || ' ' || stdlnm 
          FROM webset.dmg_studentmst
         WHERE stdrefid = " . $stdrefid. "
	")->getOne() . ' - Data';

	$tabs = new UITabs('tabs');

	$tabs->addTab('Export Demo/SpEd')
		->url(CoreUtils::getURL('std_export_demo.php', array('stdrefid' => $stdrefid)));

	$tabs->addTab('Import Demo/SpEd')
		->url(CoreUtils::getURL('std_import_demo.php', array('stdrefid' => $stdrefid)));

	if ($tsRefID > 0) {
		$tabs->addTab('Export Sp Ed Data')
			->url(CoreUtils::getURL('std_export.php', array('tsRefID' => $tsRefID)));

		$tabs->addTab('Import Sp Ed Data')
			->url(CoreUtils::getURL('std_import.php', array('tsRefID' => $tsRefID)));
	
		$tabs->addTab('IEP')
		->url(
			CoreUtils::getURL(
				'/applications/webset/iep/wrk_stdmgr_menu.php', 
				array(
					'RefID' => $tsRefID,
					'AMRefID' => 'G02-L7-Z0801040221', 
					'ADRefID' => 'G02-L16-D0801040221'
				)
			)
		);

	}

	$tabs->addTab('Enrollment')
		->url(
			CoreUtils::getURL(
				'/apps/idea/iep/enrollment/enr_history.php', 
				array(
					'stdrefid' => $stdrefid, 
					'AMRefID' => 'G02-L7-Z0801040221', 
					'ADRefID' => 'G02-L16-D0801040221'
				)
			)
		);
	$tabs->addTab('Demo')
		->url(
			CoreUtils::getURL(
				'/apps/idea/std_dmg/dmg_add.php', 
				array(
					'RefID' => $stdrefid, 
					'AMRefID' => 'G02-L7-Z0801040221', 
					'ADRefID' => 'G02-L16-D0801040221'
				)
			)
		);

	$tabs->addTab('PDF')
		->url(
			CoreUtils::getURL(
				'/applications/webset/sys_maint/support/ieprestore.php', 
				array(
					'AMRefID' => 'G02-L7-Z0801040221', 
					'ADRefID' => 'G02-L16-D0801040221'
				)
			)
		);

	print $tabs->toHTML();
?>
<script type="text/javascript">
	parent.zWindow.changeCaption(<?=json_encode($std_title);?>);
	parent.zWindow.changeSystemBarCaption(<?=json_encode($std_title);?>);
	
	function doHere(sender, command, param) {
        if (command == 'close') {
             parent.zWindow.hardClose = false;
             parent.zDesktop.findWindow('<?=$winame;?>').restoreWindow();
             parent.zDesktop.findWindow("<?=$winame;?>").bringToFront();
        }
    }
    parent.zWindow.onCommand = doHere;
</script>
