<?php
	Security::init();
    $dskey = io::get('dskey', true);
    
    $edit = new EditClass('edit1', io::geti('RefID'));
        
    $edit->title = 'Icons Corrections XML Template';

    $edit->addGroup('General Information');

    $edit->addControl('Template', 'textarea')
	    ->name('template')
        ->value('
            <template>
				<item contains="IDEA" action="rename" to="SPEDEX" casesensitive="yes"/>
				<item contains="WeBSET" action="rename" to="SPEDEX" casesensitive="yes"/>
				<item contains="WEBSET" action="rename" to="SPEDEX" casesensitive="yes"/>
				<item contains="WeBSIS" action="rename" to="AXSIS" casesensitive="yes"/>
				<item contains="WEBSIS" action="rename" to="AXSIS" casesensitive="yes"/>
				<item contains="SAM" action="rename" to="AXSIS" casesensitive="yes"/>
				<item contains="WeBSAS - " action="rename" to="" casesensitive="yes"/>
				<item contains="WeBSAS-" action="rename" to="" casesensitive="yes"/>

				<item contains="Lumen Messenger" action="set_title" to="Lumen Messenger V2" casesensitive="no"/>
				<item contains="Lumen Messenger" action="set_url" to="/core/interface/dialogs/instantMessenger/im_starter.php?AMRefID=G02-L3694-Z2909100841" casesensitive="no"/>
			</template>
        ')
        ->css('height', '250px');
    
    $edit->addControl('dskey', 'hidden')->name('dskey')->value($dskey);
	
	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Update Desktops');
    $edit->saveAndAdd = false;
    $edit->saveLocal = false;
    $edit->finishURL = CoreUtils::getURL('zdi_icons_rename_process.php');
    $edit->saveURL = CoreUtils::getURL('zdi_icons_rename_process.php');
    $edit->cancelURL = 'javascript:api.window.destroy();'; 

    $edit->printEdit();
    
?>