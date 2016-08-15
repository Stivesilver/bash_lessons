<?php

    $message = '
      - Missouri has determined that the MAP-A is the appropriate alternate assessment for the MAP based upon the educational curriculum focusing on essential skills and alternative learning standards for students with the most significant cognitive disabilities meeting the eligibility criteria to participate in the alternate assessment.
      <br>- Justification for why the child cannot participate in the regular assessment (Grade-Level or EOC) based upon the multiple criteria for eligibility to participate in the MAP-A:

(Use information from the alternate assessment decision making resources including the guidance document, flowchart and/or checklist to justify participation in the alternate assessment at the following link:

        ' . UIAnchor::factory('http://dese.mo.gov/college-career-readiness/assessment', 'http://dese.mo.gov/college-career-readiness/assessment')->toHTML() . '.
     )';
    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
