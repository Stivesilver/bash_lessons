<?php

    /**
     * Select Field with District School Year
     *
     * @copyright Lumen Touch, 2012
     */
    class FFIDEASchoolYear extends FFSelect {

        /**
         * Class Constructor
         *
         */
        public function __construct() {
			parent::__construct();
            $this->caption('District School Year')
                ->sql("
                    SELECT dsyrefid,
                           dsydesc,
                           dsybgdt,
                           dsyendt
                      FROM webset.disdef_schoolyear
                     WHERE vndrefid = VNDREFID
                     ORDER BY dsybgdt DESC
                ")
                ->value(
                    db::execSQL("
                        SELECT dsyrefid
             			  FROM webset.disdef_schoolyear
       	                 WHERE vndrefid = VNDREFID
                           AND NOW() BETWEEN dsybgdt and dsyendt
                    ")->getOne()
                )
            ;
        }

        /**
         * Creates an instance of this class
         *
         * @static
         * @return FFIDEASchoolYear
         */
        public static function factory() {
            return new FFIDEASchoolYear();
        }

    }

?>
