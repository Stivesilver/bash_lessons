<?php
    Security::init();
    $area = io::get('area', TRUE);
    $form_id = io::get('form_id', TRUE);
    $cont = io::post('cont');
    require_once("form_inc.php");

    if ($base64 == "yes") {
        $cont = base64_encode(stripslashes($cont));
    }

    db::execSQL("
        UPDATE $table
	       SET $content   = '" . db::escape($cont) . "',
	           lastupdate     = now(),
	           lastuser       = '" . db::escape(SystemCore::$userUID) . "'
	     WHERE $refid = " . $form_id . "
    ");
?>
<script>
    parent.acctivateSave();
</script>