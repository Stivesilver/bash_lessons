<?php
    Security::init();
    
    $list = new ListClass();

    $list->title = 'System Integrity';
	
    $list->SQL = "
        SELECT refid,
       		   sql_about,
       		   'Check'
		  FROM webset.sys_sql_archive
         WHERE sql_body LIKE '%<integrity_process>%'
         ORDER BY refid desc
    ";

    $list->addColumn('Integrity Process');
    $list->addColumn('Run')
    	->type('link')
        ->param('javascript:startProcess(AF_REFID)');

    $list->deleteTableName = 'webset.sys_sql_archive';
    $list->deleteKeyField  = 'refid';

    $list->editURL = 'integrity_edit.php';
    $list->addURL  = 'integrity_edit.php';

    $list->printList();
    
?>
<script type="text/javascript">
    function startProcess(RefID, ids) {
        win = api.ajax.process(
        	ProcessType.REPORT, 
        	'check.server.ajax.php', 
        	{'RefID' : RefID, 'ids' : ids==undefined ? '' : ids}
        );
        win.addEventListener(
        	ObjectEvent.COMPLETE, 
        	function (e) {
        	    openReport(e.param.dskey, e.param.RefID);
        	    win.destroy();
            }
        );
    }
    
    function openReport(dskey, RefID) {
        var wnd = api.window.open('System Integrity', api.url('check.report.php', {'dskey' : dskey , 'RefID' : RefID}));
        wnd.addEventListener('repeat_again', onRepeat);
        wnd.resize(950, 600);
        wnd.center();
        wnd.show();
    }
    
    function onRepeat(e) {
        var RefID = e.param.RefID;
        var ids = e.param.ids;
        startProcess(RefID, ids);
    }

</script>