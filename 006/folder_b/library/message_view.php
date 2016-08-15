<?php
    Security::init();

    $message = db::execSQL("
        SELECT acaption,
               abody
          FROM public.sys_anouncements
	     WHERE arefid = " . io::geti('arefid', TRUE) . "
    ")->assoc();

    print
        UIMessage::factory($message['abody'], 2)
            ->textAlign(1)
            ->transparent()
            ->toHTML();
?>
<script type="text/javascript">
    api.window.changeTitle(<?= json_encode($message['acaption']); ?>);
</script>