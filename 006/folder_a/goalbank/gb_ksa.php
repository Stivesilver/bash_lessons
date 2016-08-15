<?php

    Security::init();

    $RefID = io::get('RefID');

    if ($RefID > 0 or $RefID == '0') {


        $edit = new EditClass('edit1', $RefID);

        $edit->title = 'Add/Edit Key Skill Area';
        $edit->setSourceTable('webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid');

        $edit->addTab('General Information');

        $edit->addControl('Domain/Scope', 'select')
            ->sqlField('gdsrefid')
            ->optionsCSS("color: AF_COL2")
            ->sql("
				SELECT gdsrefid,
			           CASE WHEN NOW() > domain.enddate OR NOW() > scope.enddate THEN 'Inactive - ' ELSE '' END ||
			           domain.gdsdesc || ' / ' || scope.gdssdesc,
			           CASE WHEN NOW() > domain.enddate OR NOW() > scope.enddate THEN 'red' ELSE '' END
		     	  FROM webset.disdef_bgb_goaldomainscope scope
		     	       INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
			     WHERE domain.vndrefid = VNDREFID
			     ORDER BY CASE WHEN NOW() > domain.enddate OR NOW() > scope.enddate THEN 2 ELSE 1 END, domain.gdsdesc, scope.gdssdesc
			");

        $edit->addControl('Key Skill Area', 'edit')->sqlField('gdsksdesc')->size(80)->req();
        $edit->addControl('Description', 'textarea')->sqlField('gdskdesc');
        $edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		if ($RefID > 0) {
			$edit->addTab('State Standards');
			$edit->addIFrame(CoreUtils::getURL('gb_ksa_standart.php', array('ksa_refid' => $RefID)))
				->height('500px');
		}

        $edit->finishURL = 'gb_ksa.php';
        $edit->cancelURL = 'gb_ksa.php';

        $edit->printEdit();
    } else {
        $list = new ListClass();

        $list->title = 'Key Skill Area';

        $list->showSearchFields = true;

        $list->SQL = "
			SELECT gdskrefid,
				   domain.gdsdesc || ' / ' || scope.gdssdesc AS dmn,
				   ksa.gdsksdesc,
				   (SELECT count(1)
                      FROM webset.disdef_bgb_standart_key
                     WHERE key_refid::integer = gdskrefid) || ' items' AS items,
				   CASE
		               WHEN NOW() > domain.enddate THEN 'N'
		               WHEN NOW() > scope.enddate  THEN 'N'
		               WHEN NOW() > ksa.enddate    THEN 'N'
		               ELSE 'Y'
		           END as status,
		           'Action' AS actn,
               	   'Content' AS cnt,
               	   'Condition' AS cond,
               	   'Criteria' AS crit
			  FROM webset.disdef_bgb_goaldomainscopeksa ksa
				   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
				   INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
			 WHERE domain.vndrefid = VNDREFID
			       ADD_SEARCH
             ORDER BY domain.gdsdesc, scope.gdssdesc, gdsksdesc
		";

        $list->addSearchField('Domain', 'scope.gdrefid', 'select')
            ->name('gdrefid')
            ->sql("
				SELECT gdrefid,
			           gdsdesc
		     	  FROM webset.disdef_bgb_goaldomain
			     WHERE vndrefid = VNDREFID
			       AND CASE WHEN NOW() > enddate THEN 2 ELSE 1 END = 1
			     ORDER BY gdsdesc
			");

        $list->addSearchField('Scope', 'ksa.gdsrefid', 'select')
            ->sql("
				SELECT gdsrefid,
			           gdssdesc
		     	  FROM webset.disdef_bgb_goaldomainscope
			     WHERE gdrefid = VALUE_01
			       AND CASE WHEN NOW() > enddate THEN 2 ELSE 1 END = 1
			     ORDER BY gdssdesc
			")
            ->tie('gdrefid');

        $list->addSearchField('Key Skill Area', "lower(ksa.gdsksdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%'");

        $list->addSearchField(FFIDEAStatus::factory('Status'))
            ->sqlField("
    			CASE
	              WHEN NOW() > domain.enddate THEN 'N'
	              WHEN NOW() > scope.enddate  THEN 'N'
	              WHEN NOW() > ksa.enddate THEN 'N'
	              ELSE 'Y'
	            END
	        ");

        $list->addColumn('Domain/Scope')->sqlField('dmn');
        $list->addColumn('Key Skill Area')->sqlField('gdsksdesc');
		$list->addColumn('State Standards')
			->sqlField('items')
			->type('tablehint')
			->param("
				SELECT ssdurl,
					   ssddesc
				  FROM webset.disdef_bgb_standart_key
				 WHERE key_refid::integer = AF_REFID
				 ORDER BY ssddesc
            ");

	    $list->addColumn('Action')->sqlField('actn')->dataCallback('action')->dataHintCallback('actionHint');
	    $list->addColumn('Content')->sqlField('cnt')->dataCallback('content')->dataHintCallback('contentHint');
	    $list->addColumn('Condition')->sqlField('cond')->dataCallback('condition')->dataHintCallback('conditionHint');
	    $list->addColumn('Criteria')->sqlField('crit')->dataCallback('criteria')->dataHintCallback('criteriaHint');

        $list->addColumn('Active')->type('switch')->sqlField('status');

        $list->addURL = 'gb_ksa.php';
        $list->editURL = 'gb_ksa.php';

        $list->addButton(
            FFIDEAExportButton::factory()
                ->setTable('webset.disdef_bgb_goaldomainscopeksa')
                ->setKeyField('gdskrefid')
                ->applyListClassMode()
	            ->setNesting('webset.disdef_bgb_ksaksgoalactions', 'gdskgarefid', 'gdskgrefid', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
	            ->setNesting('webset.disdef_bgb_scpksaksgoalcontent', 'gdskgcrefid', 'gdskgrefid', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
	            ->setNesting('webset.disdef_bgb_ksaconditions', 'crefid', 'blksa', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
	            ->setNesting('webset.disdef_bgb_ksacriteria', 'crrefid', 'blksa', 'webset.disdef_bgb_goaldomainscopeksa', 'gdskrefid')
        );

        $list->printList();
    }

	function action($data) {
		$count = db::execSQL("
			SELECT count(action.gdskgarefid)
		      FROM webset.disdef_bgb_ksaksgoalactions action
             WHERE gdskgrefid = " . $data['gdskrefid'] . "
               AND umrefid IS NULL
		")->getOne();
		if ($count > 0) {
			return UIAnchor::factory("Action (" . $count . ")")->onClick('action(AF_REFID, event)')->toHTML();
		} else {
			return UIAnchor::factory("Action (" . $count . ")")->onClick('action(AF_REFID, event)')->css('color:#ff0000;')->toHTML();
		}
	}

	function actionHint($data) {
		$names = db::execSQL("
			SELECT action.gdskgaaction
		      FROM webset.disdef_bgb_ksaksgoalactions action
             WHERE gdskgrefid = " . $data['gdskrefid'] . "
               AND umrefid IS NULL
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['gdskgaaction'] . "<br/>";
		}
		return $res;
	}

	function content($data) {
		$count = db::execSQL("
			SELECT count(content.gdskgcrefid)
		      FROM webset.disdef_bgb_scpksaksgoalcontent content
             WHERE gdskgrefid = " . $data['gdskrefid'] . "
               AND umrefid IS NULL
		")->getOne();
		if ($count > 0) {
			return UIAnchor::factory("Content (" . $count . ")")->onClick('content(AF_REFID, event)')->toHTML();
		} else {
			return UIAnchor::factory("Content (" . $count . ")")->onClick('content(AF_REFID, event)')->css('color:#ff0000;')->toHTML();
		}
	}

	function contentHint($data) {
		$names = db::execSQL("
			SELECT content.gdskgccontent
		      FROM webset.disdef_bgb_scpksaksgoalcontent content
             WHERE gdskgrefid = " . $data['gdskrefid'] . "
               AND umrefid IS NULL
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['gdskgccontent'] . "<br/>";
		}
		return $res;
	}

	function condition($data) {
		$count = db::execSQL("
			SELECT count(condition.crefid)
		      FROM webset.disdef_bgb_ksaconditions condition
             WHERE blksa = " . $data['gdskrefid'] . "
               AND umrefid IS NULL
		")->getOne();
		if ($count > 0) {
			return UIAnchor::factory("Condition (" . $count . ")")->onClick('condition(AF_REFID, event)')->toHTML();
		} else {
			return UIAnchor::factory("Condition (" . $count . ")")->onClick('condition(AF_REFID, event)')->css('color:#ff0000;')->toHTML();
		}
	}

	function conditionHint($data) {
		$names = db::execSQL("
			SELECT condition.cdesc
		      FROM webset.disdef_bgb_ksaconditions condition
             WHERE blksa = " . $data['gdskrefid'] . "
               AND umrefid IS NULL
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['cdesc'] . "<br/>";
		}
		return $res;
	}

	function criteria($data) {
		$count = db::execSQL("
			SELECT count(criteria.crrefid)
		      FROM webset.disdef_bgb_ksacriteria criteria
             WHERE blksa = " . $data['gdskrefid'] . "
		")->getOne();
		if ($count > 0) {
			return UIAnchor::factory("Criteria (" . $count . ")")->onClick('criteria(AF_REFID, event)')->toHTML();
		} else {
			return UIAnchor::factory("Criteria (" . $count . ")")->onClick('criteria(AF_REFID, event)')->css('color:#ff0000;')->toHTML();
		}
	}

	function criteriaHint($data) {
		$names = db::execSQL("
			SELECT criteria.crdesc
		      FROM webset.disdef_bgb_ksacriteria criteria
             WHERE blksa = " . $data['gdskrefid'] . "
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['crdesc'] . "<br/>";
		}
		return $res;
	}


?>

<script>
	function action(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('', api.url("./ksa_action.php?gskid=" + id));
		win.resize(1200, 700);
		win.show();
		win.addEventListener(
			WindowEvent.CLOSE,
			function(e) {
				ListClass.get().reload();
			}
		);
	}

	function content(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('', api.url("./ksa_content.php?gskid=" + id));
		win.resize(1200, 700);
		win.show();
		win.addEventListener(
			WindowEvent.CLOSE,
			function(e) {
				ListClass.get().reload();
			}
		);
	}

	function condition(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('', api.url("./ksa_condition.php?gskid=" + id));
		win.resize(1200, 700);
		win.show();
		win.addEventListener(
			WindowEvent.CLOSE,
			function(e) {
				ListClass.get().reload();
			}
		);
	}

	function criteria(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('', api.url("./ksa_criteria.php?gskid=" + id));
		win.resize(1200, 700);
		win.show();
		win.addEventListener(
			WindowEvent.CLOSE,
			function(e) {
				ListClass.get().reload();
			}
		);
	}
</script>
