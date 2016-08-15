<?php
	Security::init();

	$field = io::get("field");

	$list = new ListClass();
	$tsrefid = io::get('tsrefid');
	$list->title = '';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT 's' || CAST(dmg.stdrefid AS VARCHAR),
               COALESCE(stdlnm,'') || ' ' || COALESCE(stdfnm,'') AS tname,
               'Student' AS title,
               0,
               NULL,
               COALESCE(stdfnm,'') || ' ' || COALESCE(stdlnm,'')
          FROM webset.dmg_studentmst dmg
               INNER JOIN webset.sys_teacherstudentassignment ts ON dmg.stdrefid = ts.stdrefid
         WHERE tsrefid = " . $tsrefid . "
         UNION
         SELECT 'g' || CAST(gdrefid AS VARCHAR),
               COALESCE(gdlnm,'') || ' ' || COALESCE(gdfnm,'') AS tname,
               CASE gtdesc IS NULL WHEN TRUE THEN 'Guardian' ELSE gtdesc END AS title,
               1,
               gtrank,
               COALESCE(gdfnm,'') || ' ' || COALESCE(gdlnm,'')
          FROM webset.dmg_guardianmst grd
               INNER JOIN webset.def_guardiantype ON grd.gdtype = webset.def_guardiantype.gtrefid
               INNER JOIN webset.sys_teacherstudentassignment ts ON grd.stdrefid = ts.stdrefid
         WHERE tsrefid = " . $tsrefid . "
         UNION
        SELECT 'u' || CAST(umrefid AS VARCHAR),
               umlastname  || ', ' || umfirstname AS tname,
               umtitle AS title,
			   2,
               0,
               umfirstname || ' ' || umlastname
          FROM sys_usermst
         WHERE sys_usermst.vndrefid = " . SystemCore::$VndRefID . "
           AND um_internal IS TRUE
         ORDER BY 4,5,2
    ";

	$list->addSearchField('Last Name')->sqlField('umlastname')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField('Title')->sqlField('umtitle')->sqlMatchType(FormFieldMatch::SUBSTRING);
	$list->addSearchField(FFSelect::factory('Building'))
		->sql("
			SELECT vourefid, vouname
              FROM sys_voumst
             WHERE vndrefid = " . SystemCore::$VndRefID . "
             ORDER BY vouname
		")
		->sqlField('vourefid');

	$list->addColumn("Name")->sqlField('tname');
	$list->addColumn("Title")->sqlField('title');

	$list->editURL = 'javascript:addTeacher("AF_COL5")';

	$list->printList();

	print FFInput::factory()->name('field')->value($field)->hide()->toHTML();

?>
<script type='text/javascript'>
	function addTeacher(teacher) {
		api.window.dispatchEvent('teacher_selected', {teacher: teacher, field: $("#field").val()});
		api.window.destroy();
	}
</script>
