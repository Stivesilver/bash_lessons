<?php
    Security::init();
        
    $dskey     = io::get('dskey');
    $ds        = DataStorage::factory($dskey);
    $tsRefID   = $ds->safeGet('tsRefID');
    
    $list = new ListClass();

    $list->title = 'Student Events';
    
    $list->multipleEdit     = false;
    $list->showSearchFields = true;

    $list->SQL = "
        SELECT semrefid,
               semddesc,
               message,
               std.lastuser,
               TO_CHAR(std.lastupdate, 'MM-DD-YYYY HH:MI am')
          FROM webset.std_eventmst std
               INNER JOIN webset.statedef_eventdesc state ON std.semdrefid =  state.semdrefid
         WHERE stdrefid = ".$tsRefID." 
               ADD_SEARCH
         ORDER BY semrefid DESC
    ";

    $list->addSearchField('Event Type', 'std.semdrefid', 'list')
        ->sql("SELECT semdrefid, semddesc
                 FROM webset.statedef_eventdesc
                WHERE screfid = ".VNDState::factory()->id."
                ORDER BY semddesc");

    $list->addColumn('Event');
    $list->addColumn('Event Detail');
    $list->addColumn('User');
    $list->addColumn('Date/Time');
    
    $list->hideCheckBoxes = FALSE;

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_eventmst')
            ->setKeyField('semrefid')
            ->applyListClassMode()
    );

    $list->printable = true;

    $list->printList();

?>
