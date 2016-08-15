<?php
    Security::init();
    
    #Update District State
    $SQL = "
    	UPDATE sys_vndmst
           SET vndstate = webset.sped_menu_set.state
          FROM webset.disdef_spedmenu, webset.sped_menu_set
         WHERE webset.disdef_spedmenu.srefid = webset.sped_menu_set.srefid
           AND webset.disdef_spedmenu.vndrefid = sys_vndmst.vndrefid
           AND sys_vndmst.vndrefid = VNDREFID
    ";
    db::execSQL($SQL);
    
    $SQL = "
        SELECT dsrefid
          FROM webset.disdef_spedmenu
         WHERE vndrefid = VNDREFID
    ";
    $result = db::execSQL($SQL);
    if ($result->fields[0]>0) {
        $RefID = $result->fields[0];
    } else {
        $RefID = 0;
    }
    
    $edit = new EditClass('edit1', $RefID);
    $edit->title = 'Student Menu Set Setup';
        
    $edit->setSourceTable('webset.disdef_spedmenu', 'dsrefid');

    $edit->addGroup('General Information');
    $edit->addControl('Menu Set', 'select_radio')
        ->sqlField('srefid')
        ->sql("
            SELECT srefid, 
                   state || ' - ' || shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL or now()< enddate)
             ORDER BY state, shortdesc
        ")
        ->breakRow();
        
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

    $edit->saveAndEdit = true;
    $edit->saveAndAdd = false;
    $edit->getButton(EditClassButton::SAVE_AND_FINISH)->hide();

    $edit->finishURL = 'menuset.php';
    $edit->cancelURL = 'menuset.php';
    
    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.disdef_spedmenu')
            ->setKeyField('dsrefid')
            ->applyEditClassMode()
    );

    $edit->printEdit();
    
?>
