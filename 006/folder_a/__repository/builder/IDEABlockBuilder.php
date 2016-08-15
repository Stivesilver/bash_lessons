<?php

	/**
	 * IDEABlockBuilder.php
	 * Need for creation specific block. Each constant characterizes ID block in table webset.sped_iepblocks(col 'ieptype')

	 * @author Ganchar Danila <dganchar@lumentouch.com>
	 * Created 26-12-2013.
	 */
	final class IDEABlockBuilder {

		/**
		 * ID ARD/IEP AMENDMENT Builder
		 */
		const ARD_AMENDMENT   = 31;

		/**
		 * ID Transfer Packet Builder
		 */
		const TRANSFER_PACKET = 32;

		/**
		 * ID Service Plan Builder
		 */
		const SERVICE_PLAN    = 33;

		/**
		 * ID ARD/IEP Builder State(TX)
		 */
		const ARD   = 34;

		/**
		 * ID Brief ARD Builder
		 */
		const BRIEF = 35;

		/**
		 * ID FIE Builder State(TX)
		 */
		const FIE   = 36;

		/**
		 * ID RTI Builder State(TX)
		 */
		const RTI   = 37;

		/**
		 * ID IEP Builder State(MO)
		 */
		const MO_IEP = 13;

		/**
		 * ID Service Plan Builder State(MO)
		 */
		const MO_SERVICE_PLAN = 14;

		/**
		 * ID Exit Summary Builder State(MO)
		 */
		const MO_EXIT_SUMMARY = 15;

		/**
		 * ID CT Builder State(CT)
		 */
		const CT = 79;

		/**
		 * ID CT Optional IEP(CT)
		 */
		const CT_IEP_OPTIONAL = 101;

		/**
		 * ID TN Builder State(TN)
		 */
		const TN = 99;

		/**
		 * MO Evaluation Report
		 */
		const ER = 21;

		/**
		 * MO RED Report
		 */
		const RED = 40;

		/**
		 * UT RED Report
		 */
		const UT_RED = 95;

		/**
		 * Class initializes object specific block. Use static method 'create'.
		 */
		private function __construct() {}

		/**
		 * Return object with specific type block. Create object block by type.
		 *
		 * @static
		 * @param int $doc_id key block || references to webset.sped_doctype
		 * @return IDEABlock
		 * @throws Exception
		 */
		public static function create($doc_id) {
			if (!($doc_id > 0))throw new Exception("Specify Correct ID of Document");
			$block_class = IDEADocumentType::factory($doc_id)->getBlockClass();
			if (class_exists($block_class)) {
				$instance =  new $block_class();
				return $instance;
			} else {
				throw new Exception("Not exist type block with id = $doc_id. You can see possible ID's in IDEABlockBuilder.");
			}
	}

	/**
	 * Return path to add/edit page of builder for current state.
	 * Builders have commot list page, but different edit page.
	 *
	 * @param int $const ID builder
	 * @return string path to add/edit page
	 */
	public static function getPathToEditPage($const) {
		$path = array(
			self::CT => '/apps/idea/iep.ct/builder/builder_edit.php',
			self::TN => '/apps/idea/iep.tn/builder/builder_edit.php'
		);

		return $path[$const];
	}

}
