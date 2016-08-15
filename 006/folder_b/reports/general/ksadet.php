<?php

    Security::init();

    $list = new listClass();
    $list->title = 'Goal Bank Detail Listing';
    $list->showSearchFields = true;
    $list->printable = true;

    $list->SQL = "
        SELECT webset.disdef_bgb_goaldomain.gdSDesc || ' - ' ||
               webset.disdef_bgb_goaldomainscope.gdSSDesc  || ' - ' ||
               webset.disdef_bgb_goaldomainscopeksa.gdSkSDesc AS area,
               webset.disdef_bgb_ksaksgoalactions.gdsKgaAction AS Sentence,
               'Associated Sentence Verb Items' AS descr,
               1 AS ordr,
               webset.disdef_bgb_goaldomain.gdSDesc AS firstletter
          FROM webset.disdef_bgb_ksaksgoalactions
               INNER JOIN webset.disdef_bgb_goaldomainscopeksa ON webset.disdef_bgb_ksaksgoalactions.gdsKgRefid = webset.disdef_bgb_goaldomainscopeksa.gdSKRefID
               INNER JOIN webset.disdef_bgb_goaldomain
                     INNER JOIN webset.disdef_bgb_goaldomainscope ON webset.disdef_bgb_goaldomain.gdRefID = webset.disdef_bgb_goaldomainscope.gdRefID
               ON webset.disdef_bgb_goaldomainscopeksa.gdSRefID = webset.disdef_bgb_goaldomainscope.gdSRefID
         WHERE (webset.disdef_bgb_goaldomain.vndrefid = VNDREFID)

        UNION

        SELECT webset.disdef_bgb_goaldomain.gdSDesc || ' - ' ||
               webset.disdef_bgb_goaldomainscope.gdSSDesc  || ' - ' ||
               webset.disdef_bgb_goaldomainscopeksa.gdSkSDesc AS area,
               webset.disdef_bgb_scpksaksgoalcontent.gdsKgcContent AS Sentence,
               'Associated Sentence Content Items' AS descr,
               2 AS ordr,
              webset.disdef_bgb_goaldomain.gdSDesc AS firstletter
         FROM webset.disdef_bgb_scpksaksgoalcontent
              INNER JOIN webset.disdef_bgb_goaldomainscopeksa ON webset.disdef_bgb_scpksaksgoalcontent.gdsKgRefid = webset.disdef_bgb_goaldomainscopeksa.gdSKRefID
              INNER JOIN webset.disdef_bgb_goaldomain
                    INNER JOIN webset.disdef_bgb_goaldomainscope ON webset.disdef_bgb_goaldomain.gdRefID = webset.disdef_bgb_goaldomainscope.gdRefID
              ON webset.disdef_bgb_goaldomainscopeksa.gdSRefID = webset.disdef_bgb_goaldomainscope.gdSRefID
        WHERE (webset.disdef_bgb_goaldomain.vndrefid = VNDREFID)

        UNION

        SELECT webset.disdef_bgb_goaldomain.gdSDesc || ' - ' ||
               webset.disdef_bgb_goaldomainscope.gdSSDesc  || ' - ' ||
               webset.disdef_bgb_goaldomainscopeksa.gdSkSDesc AS area,
               webset.disdef_bgb_ksaconditions.cDesc AS Sentence,
               'Associated Condition Items' AS descr,
               3 AS ordr,
               webset.disdef_bgb_goaldomain.gdSDesc AS firstletter
          FROM webset.disdef_bgb_ksaconditions
               INNER JOIN webset.disdef_bgb_goaldomainscopeksa ON webset.disdef_bgb_ksaconditions.blKSA = webset.disdef_bgb_goaldomainscopeksa.gdSKRefID
               INNER JOIN webset.disdef_bgb_goaldomain ON webset.disdef_bgb_goaldomainscopeksa.gdRefID = webset.disdef_bgb_goaldomain.gdRefID
               INNER JOIN webset.disdef_bgb_goaldomainscope ON webset.disdef_bgb_goaldomainscopeksa.gdSRefID = webset.disdef_bgb_goaldomainscope.gdSRefID
         WHERE webset.disdef_bgb_goaldomainscopeksa.vndrefid = VNDREFID

        UNION

        SELECT webset.disdef_bgb_goaldomain.gdSDesc || ' - ' ||
               webset.disdef_bgb_goaldomainscope.gdSSDesc  || ' - ' ||
               webset.disdef_bgb_goaldomainscopeksa.gdSkSDesc AS area,
               webset.disdef_bgb_ksacriteria.crDesc AS Sentence,
               'Associated Criteria Items' AS descr,
               4 AS ordr,
               webset.disdef_bgb_goaldomain.gdSDesc AS firstletter
          FROM webset.disdef_bgb_ksacriteria INNER JOIN
               webset.disdef_bgb_goaldomainscopeksa ON webset.disdef_bgb_ksacriteria.blKSA = webset.disdef_bgb_goaldomainscopeksa.gdSKRefID INNER JOIN
               webset.disdef_bgb_goaldomainscope ON webset.disdef_bgb_goaldomainscopeksa.gdSRefID = webset.disdef_bgb_goaldomainscope.gdSRefID INNER JOIN
               webset.disdef_bgb_goaldomain ON webset.disdef_bgb_goaldomainscope.gdrefid = webset.disdef_bgb_goaldomain.gdRefID
         WHERE webset.disdef_bgb_goaldomainscopeksa.vndrefid =  VNDREFID
		 ORDER BY ordr, area, Sentence
    ";

	$list->addColumn('', '', 'group')->sqlField('descr');
    $list->addColumn('Area', '', '')->sqlField('area');
    $list->addColumn('Details', '', '')->sqlField('sentence');

    $list->printList();
?>
