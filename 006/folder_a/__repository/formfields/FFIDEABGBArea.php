<?php

	/**
	 * Creates BGB Domain / Scope / Key Skill Area Search list
	 *
	 * @copyright Lumen Touch, 2014
	 */
	class FFIDEABGBArea {

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @return FFIDEABGBArea
		 */
		public static function factory() {
			return FFMultiSelect::factory('Area')
				->maxRecords(1)
				->setSearchList(
					ListClassContent::factory('Domain / Scope / Key Skill Area')
						->addColumn('Area')
						->addSearchField('Area', "LOWER(COALESCE(domain.gdsdesc || ' -> ', '') || COALESCE(gdssdesc || ' -> ','') || COALESCE(gdsksdesc,''))  LIKE '%' || LOWER(ADD_VALUE) || '%'")
						->setSQL("
							SELECT ksa.gdskrefid,
				                   COALESCE(domain.gdsdesc || ' -> ', '') || COALESCE(gdssdesc || ' -> ','') || COALESCE(gdsksdesc,'')
				              FROM webset.disdef_bgb_goaldomainscopeksa ksa
				                   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
				                   INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
				             WHERE domain.vndrefid = VNDREFID
				               AND (domain.enddate IS NULL or now()< domain.enddate)
				               AND (scope.enddate IS NULL or now()< scope.enddate)
				               AND (ksa.enddate IS NULL or now()< ksa.enddate)
				             ORDER BY 2
						")
				);
		}

	}

?>