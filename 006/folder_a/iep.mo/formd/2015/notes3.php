<?php

    $message = '
      <b>ACT® Notes</b>
      <br>- The ACT® provides a variety of approved accommodations for students with IEPs and 504 plans.  In Missouri, only these ACT®-Allowed accommodations are used so that assessments administered using ACT® will result in college reportable ACT® scores.
      <br>- In order to receive accommodations on the ACT®, the district must submit a request supported by documentation to ACT®.   Each request is reviewed by ACT® and the district is then notified via e-mail with an Accommodations Decision Notification.  Only those accommodations approved by ACT® can be provided to the student during the administration of the ACT® at the district.
      <br>- For more information on submitting ACT® Accommodations, please visit: ' . UIAnchor::factory('http://dese.mo.gov/college-career-readiness/assessment/act', 'http://dese.mo.gov/college-career-readiness/assessment/act')->toHTML() . '.';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>

