<?
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$staterefid = VNDState::factory()->id;

	io::jsVar('dskey', $dskey);

	if ($RefID == '') {
		$list = new ListClass();

		$list->title = 'Summary';

		$list->SQL = "
           SELECT redrefid,
                  screen.scrdesc,
                  red_desc,
                  red_text,
                  red.lastuser,
                  red.lastupdate
             FROM webset.es_std_red AS red
                  INNER JOIN webset.es_statedef_screeningtype AS screen ON red.screening_id = screen.scrrefid
            WHERE stdrefid = " . $tsRefID . "
              AND evalproc_id = $evalproc_id
            ORDER BY screen.scrseq, red.redrefid
        ";

		$list->addColumn('Area');
		$list->addColumn('Description Of Data Reviewed');
		$list->addColumn('Summary Of Information Gained');
		$list->addColumn('Last User');
		$list->addColumn('Last Update')->type('date');

		$list->addURL = CoreUtils::getURL('summary.php', array('dskey' => $dskey));
		$list->editURL = CoreUtils::getURL('summary.php', array('dskey' => $dskey));

		$list->addButton(
			FFIDEAExportButton::factory()
			->setTable('webset.es_std_red')
			->setKeyField('redrefid')
			->setNesting('webset.es_std_redds', 'refid', 'redrefid', 'webset.es_std_red', 'redrefid')
			->applyListClassMode()
		);

		$list->addButton(
			IDEAFormat::getPrintButton(array('dskey' => $dskey))
		);

		$list->addButton(FFButton::factory('Import'))
			->onClick('copy()')
			->width('80px');

		$list->addRecordsProcess('Delete')
			->url(CoreUtils::getURL('summary.delete.ajax.php'))
			->type(ListClassProcess::DATA_UPDATE)
			->css('width', '80px')
			->progressBar(false);

		$list->getButton(ListClassButton::ADD_NEW)
			->disabled(db::execSQL("
                SELECT 1
                  FROM webset.es_statedef_screeningtype st
                 WHERE st.screfid = " . $staterefid . "
                   AND (st.enddate>now() OR st.enddate IS NULL  OR scrrefid IN ( " . (IDEACore::disParam(155) ? IDEACore::disParam(155) : '0') . "))
                   AND st.scrrefid NOT IN (
						SELECT COALESCE(screening_id, 0)
						  FROM webset.es_std_red red
						 WHERE red.stdrefid = " . $tsRefID . "
						   AND red.evalproc_id = " . $evalproc_id . "
                       )
            ")->getOne() != '1');

		$list->printList();

		#Stores last area ID to hide Save&Next button in Edit screen
		$data = db::execSQL($list->SQL)->indexAll();
		if (count($data) > 0) {
			$ds->set('lastArea', $data[count($data) - 1][0]);
		}
	} else {

		$edit = new EditClass('edit1', $RefID);

		$edit->title = 'Summary';

		$edit->setSourceTable('webset.es_std_red', 'redrefid');

		$edit->addGroup('General Information');
		$SQL = $RefID > 0 ? "
							SELECT scrrefid,
								   scrdesc
							  FROM webset.es_statedef_screeningtype
							 WHERE scrrefid IN (
									SELECT screening_id
									  FROM webset.es_std_red
									 WHERE redrefid = " . $RefID . "
								   )
							"
							:
							"
							SELECT scrrefid,
								   scrdesc
							  FROM webset.es_statedef_screeningtype
							 WHERE screfid = " . VNDState::factory()->id . "
							   AND (enddate IS NULL OR now()< enddate OR scrrefid IN ( " . (IDEACore::disParam(155) ? IDEACore::disParam(155) : 'NULL') . "))
							   AND scrrefid NOT IN (
									SELECT screening_id
									  FROM webset.es_std_red
									 WHERE stdrefid = " . $tsRefID . "
									   AND evalproc_id = " . $evalproc_id . "
								   )
							 ORDER BY scrseq
							";

		$edit->addControl('Area', 'select')
			->name('screening_id')
			->sqlField('screening_id')
			->sql($SQL);

		$statements = 'Type and Description of Data Reviewed <br/>' .
			UIAnchor::factory('Add Form Statements')
			->onClick('addStatement("red_desc", "1")')
			->toHTML();

		$edit->addControl($statements, 'textarea')
			->name('red_desc')
			->sqlField('red_desc')
			->css('width', '100%')
			->css('height', '100px');

		$statements = 'Summary of Information Gained <br/>' .
			UIAnchor::factory('Add Form Statements')
			->onClick('addStatement("red_text", "2")')
			->toHTML();

		$edit->addControl($statements, 'textarea')
			->name('red_text')
			->sqlField('red_text')
			->css('width', '100%')
			->css('height', '250px');

		$edit->addControl('Data Source', 'select_check')
			->name('datasource')
			->value(implode(',', db::execSQL("
				SELECT dsrefid
				  FROM webset.es_std_redds
				 WHERE redrefid = $RefID
				")->indexCol(0))
			)
			->sql("
			SELECT refid,
			       datasource
			  FROM webset.es_statedef_red_ds
			 WHERE screening_id = VALUE_01
			 ORDER BY seq
			")
			->tie('screening_id')
			->breakRow();

		$edit->addControl('Specify')
			->name('datasource_other')
			->css('width', '30%')
			->showIf('datasource', db::execSQL("
					SELECT refid
					  FROM webset.es_statedef_red_ds
					 WHERE LOWER(datasource) LIKE '%other%'
                ")->indexAll())
			->sql("
                SELECT ds_other
                  FROM webset.es_std_redds std
                 WHERE redrefid = $RefID
			");

		$edit->addControl('Further Assessment Information Needed?', 'select')
			->name('red_assneed')
			->sqlField('red_assneed')
			->data(array('Y' => 'Yes', 'N' => 'No'))
			->emptyOption(true);

		$statements = 'Assessment instruments, if known <br/>' .
			UIAnchor::factory('Add Assessments')
			->onClick('addAssessment("red_asstext")')
			->toHTML();

		$edit->addControl($statements, 'textarea')
			->name('red_asstext')
			->sqlField('red_asstext')
			->css('width', '100%')
			->css('height', '50px');


		if (IDEACore::disParam(99) == 'Y') {
			$edit->addControl(FFIDEASwitchYN::factory('Information is needed to update present level of performance'))
				->sqlField('plafp');

			$edit->addControl('Skill level', 'select')
				->data(array('Y' => 'Skill level has been established', 'N' => 'Skill level needs to be established.'))
				->sqlField('skill')
				->emptyOption(true);
		}

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
		$edit->addControl("evalproc_id", "hidden") ->value($evalproc_id) ->sqlField('evalproc_id');

		$edit->finishURL = CoreUtils::getURL('summary.php', array('dskey' => $dskey));
		$edit->cancelURL = CoreUtils::getURL('summary.php', array('dskey' => $dskey));
		$edit->setPostsaveCallback('dagasources', 'summary.inc.php');
		$edit->saveAndAdd = (db::execSQL($SQL . ' OFFSET 1 ')->getOne() != '');
		$edit->saveAndNext = true;
		$edit->saveAndEdit = true;

		if ($RefID > 0 && $ds->get('lastArea')) {
			$edit->getButton(EditClassButton::SAVE_AND_NEXT)->hide($ds->get('lastArea') == $RefID);
		}

		$edit->topButtons = true;

		$edit->printEdit();
	}
?>
<script type="text/javascript">
    function addStatement(field, acategory) {
        var wnd = api.window.open(
            '',
            api.url('statements.php', {
            'screening_id': $("#screening_id").val(),
            'field': field,
            'acategory': acategory
        }
        )
            );
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('statetment_selected', onStatement);
        wnd.show();
    }

    function onStatement(e) {
        var statement = e.param.stm;
        var field = e.param.field;
        if ($("#" + field).val() != "")
            statement = "\r" + statement;
        $("#" + field).val($("#" + field).val() + statement);
    }

    function addAssessment(field) {
        var wnd = api.window.open(
            '',
            api.url('assessments.php', {
            'screening_id': $("#screening_id").val(),
            'field': field
        }
        )
            );
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('assessment_selected', onAssessment);
        wnd.show();
    }

    function onAssessment(e) {
        var statement = e.param.stm;
        var field = e.param.field;
        if ($("#" + field).val() != "")
            statement = ", " + statement;
        $("#" + field).val($("#" + field).val() + statement);
    }

    function copy() {
	    var wnd = api.window.open(
		    'Import',
		    api.url('./import.php'),
		    {
			    'dskey': dskey
		    }
	    );
	    wnd.resize(950, 700);
	    wnd.center();
	    wnd.addEventListener(
		    ObjectEvent.COMPLETE,
		    function (e) {
			    api.reload();
		    }
	    );
	    wnd.show();

    }

</script>
