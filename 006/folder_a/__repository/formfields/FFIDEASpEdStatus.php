<?php

    /**
     * Creates standard Sp Ed Status search field
     *
     * @copyright Lumen Touch, 2012
     */
    class FFIDEASpEdStatus {

        /**
         * Creates an instance of this class
         *
         * @static
         * @return FFIDEASpEdStatus
         */
        public static function factory() {		
            return FFSwitchAI::factory('Sp Ed Status')
                    ->value('A')
                    ->sqlField("CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'A' ELSE 'I' END")
                    ->name('spedstatus');
        }

    }

?>