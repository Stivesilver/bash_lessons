<?php
	Security::init();

	$RefID = io::get('RefID');

	//Allow FB 504 Forms only if FB Parameter is set to ON
	if (SystemCore::$coreVersion == 1 and IDEACore::disParam(153) != 'Y') {
		io::js('api.goto("' . CoreUtils::getURL('/applications/webset/fif/district/forms.php') . '")', true);
		die();
	}

	if ($RefID > 0 or $RefID == '0') {

		$edit = new EditClass('edit1', $RefID);
		$fb = false;
		if ($RefID) {
			if (db::execSQL("
				SELECT is_fb
				  FROM webset.disdef_fif_forms
				 WHERE frefid = $RefID
			")->getOne()
			) {
				$fb = true;
			}
		} else {
			$fb = true;
		}

		$edit->title = 'Add/Edit 504 Form';

		$edit->setSourceTable('webset.disdef_fif_forms', 'frefid');

		$edit->addGroup('General Information');
		$edit->addControl('Category', 'select')
			->sqlField('fcrefid')
			->sql("
        		SELECT fcrefid,
	                   cname
	              FROM webset.disdef_fif_form_category
	             WHERE vndrefid = VNDREFID
	               AND (enddate IS NULL OR NOW() < enddate)
	             ORDER BY cname
        	")
			->req();

		$edit->addControl('Form Name')->sqlField('fname')->name('fname')->size(80)->req();

		if ($fb) {
			$edit->addControl(FFFormBuilder::factory())
				->settings(FB504Settings::factory())
				->caption('Form Template')
				->sqlField('fb_content');

			$edit->addControl('is_fb', 'hidden')->value(1)->sqlField('is_fb');
		} else {
			$edit->addControl('XML Content', 'textarea')
				->value(base64_decode(
						db::execSQL("
        					SELECT xmlbody
        		  			  FROM webset.disdef_fif_forms
        		 			 WHERE frefid = " . $RefID . "
        				")->getOne()
					)
				)
				->name('xmlbody')
				->req();
		}

		$edit->addControl('Sequence Number', 'integer')
			->sqlField('seqnum')
			->value((int)db::execSQL("
        		SELECT count(1)
                 FROM webset.disdef_fif_forms f
                WHERE vndrefid = VNDREFID
                 AND (enddate IS NULL OR NOW() < enddate)
        	")->getOne() * 10 + 10);
		$edit->addControl('Deactivation Date', 'date')->sqlField('enddate')->name('enddate');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		$edit->addSQLConstraint(
			'Such category already exists', "
            SELECT 1
              FROM webset.disdef_fif_forms
             WHERE vndrefid = VNDREFID
               AND fname = '[fname]'
               AND frefid != AF_REFID
        ");

		$edit->saveAndEdit = true;
		$edit->saveAndAdd = false;
		$edit->finishURL = 'forms.php';
		$edit->cancelURL = 'forms.php';

		$edit->setPresaveCallback('xmlEncode', 'forms.inc.php');

		$edit->firstCellWidth = '30%';

		$edit->printEdit();
	} else {
		$list = new ListClass();

		$list->title = '504 Forms';
		$list->showSearchFields = true;

		$list->SQL = "
            SELECT frefid,
                   cname,
                   fname,
                   is_fb,
                   f.lastuser,
                   f.lastupdate,
                   CASE WHEN is_fb = '1' THEN 'Y' ELSE 'N' END AS ftype,
                   CASE WHEN NOW() > f.enddate THEN 'N' ELSE 'Y' END AS status,
                   'XML'
              FROM webset.disdef_fif_forms f
                   LEFT OUTER JOIN webset.disdef_fif_form_category c ON f.fcrefid = c.fcrefid
             WHERE f.vndrefid = VNDREFID
                   ADD_SEARCH
             ORDER BY cname, f.seqnum, fname
        ";

		$list->addRecordsResequence(
			'webset.disdef_fif_forms',
			'seqnum'
		);
		$list->addSearchField('Category', 'f.fcrefid', 'select')
			->sql("
				SELECT fcrefid,
	                   cname
	              FROM webset.disdef_fif_form_category
	             WHERE vndrefid = VNDREFID
	               AND (enddate IS NULL OR NOW() < enddate)
	             ORDER BY cname
			");

		$list->addSearchField('Form', "LOWER(fname)  like '%' || LOWER('ADD_VALUE') || '%'");
		$list->addSearchField("Text in Body (XML)", "LOWER(encode(decode(xmlbody, 'base64'),'escape'))  like '%' || LOWER('ADD_VALUE')|| '%'");

		$list->addSearchField(
			FFSwitchYN::factory('FB')
				->sqlField("CASE WHEN is_fb = '1' THEN 'Y' ELSE 'N' END")
		);

		$list->addSearchField(
			FFSwitchAI::factory('Status')
				->sqlField("CASE WHEN NOW() > f.enddate THEN 'I' ELSE 'A' END")
				->value('A')
		);

		$list->addColumn('Category', '', 'group');
		$list->addColumn('Form');
		$list->addColumn('Preview')->dataCallback('editXml')->width('10%');
		$list->addColumn('FB')->sqlField('ftype')->type('switch');
		$list->addColumn('Active')->type('switch')->sqlField('status');
		$list->addColumn('Last User')->sqlField('lastuser'); 
		$list->addColumn('Last Update')->sqlField('lastupdate')->type('datetime');

		$list->addURL = './forms.php';
		$list->editURL = './forms.php';

		$list->addButton(
			FFButton::factory('Print')
				->onClick('getForms();')
				->width(80)
				->leftIcon('./img/printer.png')
		);

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_fif_forms')
				->setKeyField('frefid')
				->applyListClassMode()
		);

		$list->addButton(
			IDEAFormChecker::factory()
				->setTable('webset.disdef_fif_forms')
				->setKeyField('frefid')
				->setNameField('fname')
				->setXmlField('xmlbody')
				->setEncodedFlag()
				->applyListClassMode()
		);

		$list->printList();
	}

	function editXml($data) {
		$link = UILayout::factory();
		if ($data['is_fb'] != '1') {
			if (SystemCore::$coreVersion == '1') {
				$link->newLine()
					->addObject(UIAnchor::factory('XML PC')->onClick('editXml(AF_REFID, event)'), 'center');
			} else {
				$link = UILayout::factory()
					->addObject(UIAnchor::factory('XML Tablet')->onClick('completeForm(AF_REFID, event)'), 'center');

			}
		}
		return $link->toHTML();
	}
?>
<script type="text/javascript">
	function editXml(RefID, evt) {
		api.event.cancel(evt);
		var wnd = api.window.open('Form Edit and Test', api.url('<?= CoreUtils::getURL('/applications/webset/support/form.php'); ?>', {
			'form_id': RefID,
			'area': 'fif'
		}));
		wnd.resize(950, 700);
		wnd.show();
		return;
	}

	function completeForm(RefID, evt) {
		api.event.cancel(evt);
		var wnd = api.window.open('Form Edit and Test', api.url('<?= CoreUtils::getURL('/apps/idea/sys_maint/support/form/form.php'); ?>', {
			'form_id': RefID,
			'area': 'fif'
		}));
		wnd.resize(950, 700);
		wnd.show();
		return;
	}

	function getForms() {
		var selVal = ListClass.get().getSelectedValues().values.join(',');
		if (selVal != '') {
			api.ajax.process(
				UIProcessBoxType.REPORT,
				api.url('./gen_forms.ajax.php'),
				{
					'selVal': selVal
				}
			);
		} else {
			alert('Please select Form(s)')
		}
	}
</script>
