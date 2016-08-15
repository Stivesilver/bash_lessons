<?php

    $message = '
     	<b>Note (1):</b>
        Oral reading, oral reading in native language, or signing during the Communication Arts Assessment will result in the Lowest Obtainable Scale Score (LOSS). The use of a dictionary, grammar handbook, thesaurus, or bilingual dictionary is permitted ONLY in Session 2 of the Communication Arts Assessment (writing prompt) for Grades 3 and 7.  Those same tools are not permitted in any other content area for any other grade unless stated in a student\'s IEP. Students identified as blind/visually impaired (who do not read Braille) may use the oral reading accommodation if it is their primary instructional method.

        <br><br>

        <b>Note (2):</b>
        Paraphrasing of test questions on all Grade-Level and EOC assessments will result in the Lowest Obtainable Scale Score (LOSS).

        <br><br>

        <b>Note (3):</b> If used, the score cannot be compared with scores generated under standard conditions.

        <br><br>

        <b>Note (4):</b> Use of magnifying equipment, amplification equipment, graph paper and testing with the teacher facing the student are not listed as accommodations because these are not required to be reported as accommodations for the EOC assessments and no longer required to be reported as accommodations for the Grade-Level test.

        <br><br>

        <b>Note (5):</b> NAEP offers most of the accommodations that Missouri allows on state assessments; however, a few differences exist. The NAEP accommodations, as listed, are of a general nature and may vary somewhat by year and content area being assessed. A current, more specific list of allowable NAEP accommodations will be included in the NAEP materials sent to schools selected for the NAEP sample.

        <br><br>

        For a more complete description of the accommodations list see ' . UIAnchor::factory('http://dese.mo.gov/divspeced/Compliance/documents/TAB-StateDistAssessment.pdf', 'http://dese.mo.gov/divspeced/Compliance/documents/TAB-StateDistAssessment.pdf')->toHTML() . '.

        <br><br>

        For additional information regarding NAEP, visit our website at: ' . UIAnchor::factory('http://www.dese.mo.gov/divimprove/naep/', 'http://www.dese.mo.gov/divimprove/naep/')->toHTML() . '
     ';
    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>