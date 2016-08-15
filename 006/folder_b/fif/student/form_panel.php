<?php
    Security::init();

    $hisrefid = io::geti('hisrefid');
    $RefID    = io::geti('RefID');
    require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
    require_once(SystemCore::$physicalRoot . "/apps/c-manager/__repository/demographics/Student.php");
    require_once(SystemCore::$physicalRoot . "/apps/c-manager/__repository/demographics/Guardian.php");

    if ($RefID > 0) {
        $form = db::execSQL("
            SELECT fname,
                   xmlbody,
                   values_content,
                   s.frefid,
                   archived
              FROM webset.std_fif_forms s
                   INNER JOIN webset.disdef_fif_forms f ON f.frefid = s.frefid
             WHERE sfrefid = ".$RefID."
        ")->assoc();

        $xml_title    = $form['fname'];
        $xml_content  = base64_decode($form['xmlbody']);
        $xml_values   = base64_decode($form['values_content']);
        $frefid       = $form['frefid'];
        $archived     = ($form['archived']=='Y');
    } else {
        $form = db::execSQL("
            SELECT fname,
                   xmlbody
              FROM webset.disdef_fif_forms
             WHERE frefid = ".io::geti('frefid')."
        ")->assoc();
        $xml_title    = $form['fname'];
        $xml_content  = base64_decode($form['xmlbody']);

        $stdrefid = db::execSQL("
        	SELECT stdrefid
              FROM webset.std_fif_history his
	         WHERE hisrefid = ".$hisrefid."
	    ")->getOne();
	    $student = new Student($stdrefid);
	    $guardians = $student->getGuardians();
	    $parents_name = '';
	    $parents_phones = '';
	    $parents_emails = '';
	    $a = $student->getGrade();
	    for ($i = 0; $i < count($guardians); $i++) {
	    	/** @var Guardian */
	    	$guardian = $guardians[$i];
	    	if ($guardian->getName('', 'L')) $parents_name .= $guardian->getName('', 'L').', ';
	    	if ($guardian->getPhone('W')) $parents_phones .= $guardian->getPhone('W').', ';
	    	if ($guardian->getEmail()) $parents_emails .= $guardian->getEmail().', ';
	    	if ($i == 1) break;
	    }
	    if ($parents_name != '') $parents_name = substr($parents_name, 0, -2);
	    if ($parents_phones != '') $parents_phones = substr($parents_phones, 0, -2);
	    if ($parents_emails != '') $parents_emails = substr($parents_emails, 0, -2);

        $xml_values  = '<values>
                             <value name="StdName">'.$student->getName().'</value>
                             <value name="StdFirstName">'.$student->getName('F', 'L').'</value>
                             <value name="StdDob">'.$student->getDob().'</value>
                             <value name="StdAge">'.$student->getAge().'</value>
                             <value name="StdGrade">'.$student->getGrade().'</value>
                             <value name="StdAddress">'.$student->getAddress('W').'</value>
                             <value name="StdHomePhone">'.$student->getPhone().'</value>
                             <value name="StdSchool">'.$student->getSchool().'</value>
                             <value name="StdParents">'.$parents_name.'</value>
                             <value name="StdParentWorkPhone">'.$parents_phones.'</value>
                             <value name="StdParentEmail">'.$parents_emails.'</value>
                             <value name="DistrictName">'.SystemCore::$VndName.'</value>
                             <value name="CurrUser">'.SystemCore::$userName.'</value>
                             <value name="CurrDate">'.date("m/d/Y").'</value>
                         <values>';
                         //die('<textarea cols=50 rows=30>' . $xml_values .'</textarea>');
        $frefid       = io::geti('frefid');
        $archived     = false;
    }

    //PROCESS XML DOCUMENT
    $doc = new xmlDoc();
    $doc->edit_mode    = 'yes';
    $doc->border_color = 'silver';
    $doc->edit_prefix  = 'constr_';

    $mergedDocData     = $doc->xml_merge($xml_content, $xml_values);
    $doc->xml_data     = $mergedDocData;
    $html_result       = $doc->getHtml();

    $edit = new editClass('edit1', $RefID);

    $edit->title = $xml_title;

    $edit->setSourceTable('webset.std_fif_forms', 'sfrefid');

    $edit->addGroup('General Information');
    $edit->addHTML($html_result);

    $edit->addGroup('Update Information', true);
    $edit->addControl('', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('', 'hidden')->value($frefid)->sqlField('frefid')->name('frefid');
    $edit->addControl('', 'hidden')->value($hisrefid)->sqlField('hisrefid');


    $edit->saveAndEdit = true;
    $edit->saveAndAdd  = false;
    $edit->topButtons  = true;

    $edit->saveLocal = false;
    $edit->firstCellWidth  = '0%';

    $edit->addButton('Print')
        ->css('width', '120px')
        ->onClick('printForm()');

    $edit->finishURL = CoreUtils::getURL('form_save.php', $_GET);
    $edit->saveURL   = CoreUtils::getURL('form_save.php', $_GET);
    $edit->cancelURL = 'javascript:api.window.destroy()';

    if ($archived) {
        $edit->getButton(EditClassButton::SAVE_AND_FINISH)->disabled(true);
        $edit->getButton(EditClassButton::SAVE_AND_EDIT)->disabled(true);
    }

    $edit->printEdit();

?>
<script type="text/javascript">
    function printForm() {
        var d = new Date();
        var wname = 'printWin' + d.getTime()
        $('#edit1').attr('target', wname);
        $('#edit1').attr('action', api.url('form_print.php'));
        $('#edit1').get(0).submit();
    }

</script>
