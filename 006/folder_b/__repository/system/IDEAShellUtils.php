<?php
	/**
	 * Class IDEAShellUtils
	 * All methods returns physical path to shell scripts
	 *
	 * @author
	 * @copyright LumenTouch, 2016
	 */
	class IDEAShellUtils  extends RegularClass {

		/**
		 * Returns UPDATE SQL scripts base on input INSERT SQL
		 *
		 * @return string
		 */
		public static function getPathDBInsert2Update() {
			return SystemCore::$physicalRoot . dirname(CoreUtils::getAbstractPath(__FILE__)) . '/sh/postgres_insert2update.sh';
		}

		/**
		 * Returns insert ALTER COLUMNS scripts just after CREATE TABLE block
		 *
		 * @return string
		 */
		public static function getPathDBAlterColumns() {
			return SystemCore::$physicalRoot . dirname(CoreUtils::getAbstractPath(__FILE__)) . '/sh/postgres_get_altercolumns.sh';
		}

	}
