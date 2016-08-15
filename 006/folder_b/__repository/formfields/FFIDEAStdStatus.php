<?php

    /**
     * Creates standard Student Status search field
     *
     * @copyright Lumen Touch, 2012
     */
    class FFIDEAStdStatus {

        /**
         * Creates an instance of this class
         *
         * @static
         * @return FFIDEAStdStatus
         */
        public static function factory() {			
            return FFSwitchAI::factory('Student Status')
                    ->sqlField("COALESCE(stdstatus, 'A')")
                    ->name('stdstatus')
                    ->value('A');
        }

    }

?>