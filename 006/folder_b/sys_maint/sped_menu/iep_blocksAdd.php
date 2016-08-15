<?PHP
	Security::init();


	$SQL = "SELECT max(iepnum)  FROM webset.sped_iepblocks
               WHERE iepformat =" . io::get("iep");
	$result = db::execSQL($SQL);
	$maxNum = $result->fields[0];

	$edit = new editClass("edit1", io::get("RefID"));

	$edit->title = "Add/Edit IEP Blocks";

	$edit->setSourceTable('webset.sped_iepblocks', 'ieprefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("IEP Format"))
		->req()
		->sql("
            SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             ORDER BY shortdesc
        ")
		->sqlField('iepformat');

	$edit->addControl(FFSelect::factory("Doc Type"))
		->sql("
			SELECT drefid,  shortdesc || ' - ' || doctype , 2
			  FROM webset.sped_doctype
			       INNER JOIN webset.sped_menu_set ON srefid = setrefid
			 ORDER BY 3, 2
        ")->sqlField('ieptype');

	$edit->addControl("Description")
		->css('width', '50%')
		->sqlField('iepdesc')
		->req();

	$edit->addControl("Included Fil")
		->css('width', '50%')
		->sqlField('iepinclude');

	$edit->addControl("Block`s number")
		->css('width', '10%')
		->sqlField('iepnum')
		->help("<b>Now max number for block is $maxNum</b>")
		->req();

	$edit->addControl("Block`s sequence number")
		->css('width', '10%')
		->sqlField('iepseqnum')
		->req('');

	$edit->addControl("Name for Help Mode")
		->css('width', '50%')
		->sqlField('iepnames');

	$edit->addControl("Application URL")
		->sqlField('iepurl')
		->width('90%')
		->help("<b>Use '|' separator to devide more then one URLs</b>");

	$edit->addControl("Render Function")
		->css('width', '50%')
		->sqlField('ieprenderfunc');

	$edit->addControl("Check Method")
		->css('width', '50%')
		->sqlField('check_method');

	$edit->addControl("Check Parameter")
		->css('width', '50%')
		->sqlField('check_param');

	$edit->finishURL = "./iep_blocksMain.php?iep=" . io::get("iep");
	$edit->cancelURL = "./iep_blocksMain.php?iep=" . io::get("iep");

	$edit->printEdit();
?>
