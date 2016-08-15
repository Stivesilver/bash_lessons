<?php

    $message = '
      - Missouri has determined that the MAP-A is the alternate assessment to be used by the state in lieu of participation in either the Grade-Level or End-of-Course assessments for students with the most significant cognitive disabilities who meet the multiple criteria for eligibility to participate in the alternate assessment based upon an educational curriculum focusing on essential skills and alternative learning standards.

      <br>- Information from the alternate assessment decision making resources including the guidance document, flowchart and/or checklist should be used to justify participation in the alternate assessment.  These resources can be found at the following link: ' . UIAnchor::factory('http://dese.mo.gov/college-career-readiness/assessment', 'http://dese.mo.gov/college-career-readiness/assessment')->toHTML() . '.';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
