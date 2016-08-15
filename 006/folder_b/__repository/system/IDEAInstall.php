<?php
	define('MODE_FS', 'fs');
	define('MODE_SEC', 'sec');
	define('MODE_DB', 'db');
	define('MODE_XML', 'xml');
	define('MODE_SQL', 'sql');
	define('BROKER_IP', '66.39.207.91');
	define('BROKER_PORT', '222');
	define('SCRIPT_HOME_DIR', '/tmp');
	define('PHP_MODULE_XML_EXPORT', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . SystemCore::$virtualRoot . dirname(CoreUtils::getAbstractPath(__FILE__)) . '/api/module_xml_export.php');
	define('PHP_MODULE_XML_IMPORT', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . SystemCore::$virtualRoot . dirname(CoreUtils::getAbstractPath(__FILE__)) . '/api/module_xml_import.php');
	define('PHP_MODULE_SQL_IMPORT', 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . SystemCore::$virtualRoot . dirname(CoreUtils::getAbstractPath(__FILE__)) . '/api/module_sql_import.php');

	/**
	 * Class IDEAInstall
	 * Allows to prepare get/put installation scripts
	 *
	 * @author
	 * @copyright LumenTouch, 2015
	 */
	class IDEAInstall extends RegularClass {

		/** 
		 * installation name
		 * @var string $name
		 */
		private $name;

		/**
		 * install home folder
		 * @var string $install_home_dir
		 */
		private $install_home_dir;

		/**
		 * install home folder archinve
		 * @var string $install_home_dir_arc
		 */
		private $install_home_dir_arc;

		/**
		 * install home folder for file system peaces
		 * @var string $install_home_dir_fs
		 */
		private $install_home_dir_fs;

		/**
		 * install home folder for db dumps
		 * @var string $install_home_dir_db
		 */
		private $install_home_dir_db;

		/**
		 * install home folder for xml files
		 * @var string $install_home_dir_xml
		 */
		private $install_home_dir_xml;

		/**
		 * install home folder for sql files
		 * @var string $install_home_dir_sql
		 */
		private $install_home_dir_sql;

		/**
		 * Database name
		 * @var string $db_name
		 */
		private $db_name;

		/**
		 * Database user
		 * @var string $db_user
		 */
		private $db_user;

		/**
		 * Destination vndrefid
		 * @var string $vndrefid
		 */
		private $vndrefid;

		/**
		 * Lumen Root
		 * @var string $ph_root
		 */
		private $ph_root;

		/**
		 * Sec Disk Root
		 * @var string $sec_root
		 */
		private $sec_root;

		/**
		 * Source Directories which will be transferred
		 * @var array $src_dirs
		 */
		private $src_dirs;

		/**
		 * Source Sec Disk Blank Directories which will be transferred
		 * @var array $src_dirs
		 */
		private $src_dirs_sec;

		/**
		 * Source Postgres Schemas
		 * @var array $src_db_schemas
		 */
		private $src_db_schemas;

		/**
		 * Source Postgres Tables Structures Only
		 * @var array $src_db_tables_s
		 */
		private $src_db_tables_s;

		/**
		 * Source Postgres Tables Structure + Data
		 * @var array $src_db_tables_d
		 */
		private $src_db_tables_d;

		/**
		 * XML tasks array
		 * @var array $src_db_tables_d
		 */
		private $src_xml_tasks;

		/**
		 * SQLs for final run
		 * @var array $src_sqls
		 */
		private $src_sqls;

		/**
		 * Bash header with basic variables and functions
		 * @var string $script_header
		 */
		private $script_header;

		/**
		 * Class Constructor
		 *
		 * @param string $stdrefid
		 * @return IDEAInstall
		 */
		public function __construct($name) {
			parent::__construct();
			$this->name = $name;
			$this->install_home_dir = SCRIPT_HOME_DIR . '/' . preg_replace('/\s+/', '', $name); 
			$this->install_home_dir_arc = $this->install_home_dir . '.tar.gz'; 
			$this->install_home_dir_fs = $this->install_home_dir . '/' . MODE_FS; 
			$this->install_home_dir_sec = $this->install_home_dir . '/' . MODE_SEC; 
			$this->install_home_dir_db = $this->install_home_dir . '/' . MODE_DB; 
			$this->install_home_dir_xml = $this->install_home_dir . '/' . MODE_XML; 
			$this->install_home_dir_sql = $this->install_home_dir . '/' . MODE_SQL; 
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param int $name
		 * @return IDEAInstall
		 */
		public static function factory($name) {
			return new IDEAInstall($name);
		}

		/**
		 * Set Phisical Root
		 *
		 * @param string $path
		 * @return IDEAInstall
		 */
		public function setPhRoot($path) {
			if (strlen(trim($path)) == 0) throw new Exception('Path is blank');
			if (!is_dir($path)) throw new Exception($path . ' is not a directory');
			$this->ph_root = $path;
			return $this;
		}

		/**
		 * Set Sec Disk Root
		 *
		 * @param string $path
		 * @return IDEAInstall
		 */
		public function setSecRoot($path) {
			if (strlen(trim($path)) == 0) throw new Exception('Path is blank');
			if (!is_dir($path)) throw new Exception($path . ' is not a directory');
			$this->sec_root = $path;
			return $this;
		}

		/**
		 * Set database name
		 *
		 * @param string $name
		 * @return IDEAInstall
		 */
		public function setDBName($name) {
			if (strlen(trim($name)) == 0) throw new Exception('Name is blank');
			$this->db_name = $name;
			return $this;
		}

		/**
		 * Set database user
		 *
		 * @param string $user
		 * @return IDEAInstall
		 */
		public function setDBUser($user) {
			if (strlen(trim($user)) == 0) throw new Exception('User is blank');
			$this->db_user = $user;
			return $this;
		}

		/**
		 * Set Destination Vndrefid
		 *
		 * @param string $vndrefid
		 * @return IDEAInstall
		 */
		public function setVndrefid($vndrefid) {
			if (!($vndrefid > 0)) throw new Exception('Vndrefid is incorrect');
			$this->vndrefid = $vndrefid;
			return $this;
		}

		/**
		 * Add File/Dirs relative to lumen_root
		 *
		 * @param string $path
		 * @return IDEAInstall
		 */
		public function addDir($path) {
			if (!isset($this->ph_root)) throw new Exception('Specify root first');
			if (strlen(trim($path)) == 0) throw new Exception('Path is blank');
			if (!file_exists($this->ph_root . '/' . $path)) throw new Exception($this->ph_root . '/' .$path . ' does not exits');
			$this->src_dirs[] = $path;
			return $this;
		}

		/**
		 * Add Blank File/Dirs relative to lumen_root
		 *
		 * @param string $path
		 * @return IDEAInstall
		 */
		public function addDirSec($path) {
			if (!isset($this->sec_root)) throw new Exception('Specify root first');
			if (strlen(trim($path)) == 0) throw new Exception('Path is blank');
			$this->src_dirs_sec[] = $path;
			return $this;
		}

		/**
		 * Add Postgres Schema for structure import/export
		 *
		 * @param string $schema
		 * @return IDEAInstall
		 */
		public function addDBSchema($schema) {
			if (!isset($this->db_name)) throw new Exception('Specify database name first');
			if (!isset($this->db_user)) throw new Exception('Specify database user first');
			if (strlen(trim($schema)) == 0) throw new Exception('Schema is blank');
			$this->src_db_schemas[] = $schema;
			return $this;
		}

		/**
		 * Add Postgres table for structure only import/export
		 *
		 * @param string $table
		 * @return IDEAInstall
		 */
		public function addDBTableStructure($table) {
			if (!isset($this->db_name)) throw new Exception('Specify database name first');
			if (!isset($this->db_user)) throw new Exception('Specify database user first');
			if (strlen(trim($table)) == 0) throw new Exception('Schema is blank');
			$this->src_db_tables_s[] = $table;
			return $this;
		}

		/**
		 * Add Postgres table for structure plus data import/export
		 *
		 * @param string $table
		 * @return IDEAInstall
		 */
		public function addDBTableData($table) {
			if (!isset($this->db_name)) throw new Exception('Specify database name first');
			if (!isset($this->db_user)) throw new Exception('Specify database user first');
			if (strlen(trim($table)) == 0) throw new Exception('Schema is blank');
			$this->src_db_tables_d[] = $table;
			return $this;
		}

		/**
		 * Add XML task for import/export
		 *
		 * @param string $template
		 * @param mixed $root_id
		 * @param string $sql
		 * @return IDEAInstall
		 */
		public function addXMLTask($template, $root_id, $sql) {
			if (!isset($template)) throw new Exception('Specify XML template');
			if (!isset($root_id)) throw new Exception('Specify root id value for export');
			if (!isset($sql)) throw new Exception('Specify SQL for calculation root id on destination server');
			$task = array('template' => $template, 'root_id' => $root_id, 'sql' => $sql);
			$this->src_xml_tasks[] = $task;
			return $this;
		}

		/**
		 * Add Bunch of XML tasks for import/export
		 * Difference between addXMLTask is that it provides templates rather than template
		 *
		 * @param string $template
		 * @param mixed $root_id
		 * @param string $sql
		 * @return IDEAInstall
		 */
		public function addXMLBunch($templates, $root_id, $sql) {
			if (!isset($templates)) throw new Exception('Specify XML template');
			if (!isset($root_id)) throw new Exception('Specify root id value for export');
			if (!isset($sql)) throw new Exception('Specify SQL for calculation root id on destination server');
			$xml_templates = new SimpleXMLElement($templates);
			foreach ($xml_templates->children() as $xml_template) {
				$xml = $xml_template->asXML();
				$task = array('template' => $xml, 'root_id' => $root_id, 'sql' => $sql);
				$this->src_xml_tasks[] = $task;
			}
			return $this;
		}

		/**
		 * Create XML task file for export
		 *
		 * @param array $task
		 * @return string
		 */
		private function createXMLTaskFile($task) {
			if (!isset($task['template'])) throw new Exception('Task has no XML template');
			if (!isset($task['root_id'])) throw new Exception('Task has no root id');
			if (!isset($task['sql'])) throw new Exception('Task has no sql');
			$task = '
				<task>
					<template>' . $task['template'] . '</template>
					<root_id>' . $task['root_id'] . '</root_id>
					<sql>' . $task['sql'] . '</sql>
				</task>
				';
			$task = CryptClass::factory()->encode($task);
			return FileUtils::createTmpFile($task, 'xml_task');
		}

		/**
		 * Autoindent script for readability using vi command mode
		 *
		 * @param string $path
		 */
		private function formatOutputFile($path) {
			if (!isset($path)) throw new Exception('Path is not set up');
			if (dirname($path) != SCRIPT_HOME_DIR) throw new Exception('You are allowed to format only file in ' . SCRIPT_HOME_DIR);
			$this->result = CLIRequest::factory('vi')
				->params("-c 'normal gg=G' -c 'x' " . $path)
				->execute()
				->result;
		}

		/**
		 * Add SQL for final run on destination server
		 *
		 * @param string $sql
		 * @return IDEAInstall
		 */
		public function addFinalSQL($sql) {
			if (!isset($sql)) throw new Exception('Specify SQL for final run on destination server');
			$this->src_sqls[] = $sql;
			return $this;
		}

		/**
		 * Set initial script variables and functions
		 *
		 * @return IDEAInstall
		 */
		public function setScriptHeader() {
			$this->script_header = '
#!/bin/bash 

# ask and run if confirm
ask_and_run () {
	if [ -z "$1" ]
	then
		echo "Parameter #1 is zero length. Specify text for your question"
		exit 1
	fi
	if [ -z "$2" ]
	then
		echo "Parameter #2 is zero length. Specify function name"
		exit 1
	fi
	echo "$1"
	select yn in "Yes" "No"; do
		case $yn in
			Yes ) 
				eval "$2"
				break;;
			No ) 
				break;;
		esac
	done
}

#check whether directory blank or not
files_exist () {
	ls $1/* 2>/dev/null 1>/dev/null 
}

# create temp directory for dumped sql files
func_dir_create () {
	if [ -z "$1" ]
	then
		echo "Parameter #1 is zero length. Specify directory name"
		exit 1
	fi
	# delete previous temp directory for dumped sql files
	(( ${#1} > 4 )) && [ -d "$1" ] && mv -v "$1" $(mktemp -d "$1"_XXXXXX)
	# create blank directory 
	mkdir -v "$1" 
}

# load on broker server
func_broker_put () {
	if [ -z "$1" ]
	then
		echo "Parameter #1 is zero length. Specify file path please."
		exit 1
	fi
	if [ -f "$1" ]
	then
		echo; echo "$message_pr loading $1 on broker server"; echo
		scp -P "' . BROKER_PORT . '" "$1" "root@' . BROKER_IP . ':' . SCRIPT_HOME_DIR . '"
		echo; echo "$message_pr $1 has been loaded on broker server" 
	else
		echo "File $1 does not exist. "
		exit 1
	fi
}

# get archives from broker server
func_broker_get () {
	if [ -z "$1" ]
	then
		echo "Parameter #1 is zero length. Specify file path please."
		exit 1
	fi
	echo; echo "$message_pr copying $1 from server"; echo
	eval "scp -P ' . BROKER_PORT . ' root@' . BROKER_IP . ':$1 ' . SCRIPT_HOME_DIR . '" 
	echo; echo "$message_pr $1 has been loaded from broker server"
}

# prepared and send
func_prepare_and_upload () {
	cd /
	tar -czvf "' . $this->install_home_dir_arc . '" "' . $this->install_home_dir . '"
	func_broker_put "' . $this->install_home_dir_arc . '"
}

# prepared and send
func_download_and_prepare () {
	func_broker_get "' . $this->install_home_dir_arc . '"
	func_dir_create "' . $this->install_home_dir  . '"
	tar -xzvf "' . $this->install_home_dir_arc . '" -C /
}

			';
			return $this;
		}

		/**
		 * Return GET bash script name
		 *
		 * @return string
		 */
		public function getScriptGetFileName() {
			return SCRIPT_HOME_DIR . "/". $this->db_user . "_" . $this->name . "_get.sh";
		}

		/**
		 * Return PUT bash script name
		 *
		 * @return string
		 */
		public function getScriptPutFileName() {
			return SCRIPT_HOME_DIR . "/". $this->db_user . "_" . $this->name . "_put.sh";
		}

		/**
		 * Return GET bash script content
		 *
		 * @return string
		 */
		public function getScriptGetContent() {
			$this->createScriptGet();
			return file_get_contents($this->getScriptGetFileName());
		}

		/**
		 * Return PUT bash script content
		 *
		 * @return string
		 */
		public function getScriptPutContent() {
			$this->createScriptPut();
			return file_get_contents($this->getScriptPutFileName());
		}

		/**
		 * Create GET bash script
		 *
		 * @return IDEAInstall
		 */
		public function createScriptGet() {
			$this->setScriptHeader();
			$script = $this->script_header;
			$script .= '
				func_dir_create "' . $this->install_home_dir  . '"
				mkdir "' . $this->install_home_dir_fs  . '"
				mkdir "' . $this->install_home_dir_sec  . '"
				mkdir "' . $this->install_home_dir_db  . '"
				mkdir "' . $this->install_home_dir_xml  . '"
				mkdir "' . $this->install_home_dir_sql  . '"
			';

			if (count($this->src_dirs) > 0) {
				$script .= '
					# copy lumen directories
					func_fs_export () {
						cd "' . $this->ph_root . '"
				';
				foreach ($this->src_dirs AS $dir) {
					$script .= '
						cp --parents -vR "' . $dir . '" "' .  $this->install_home_dir_fs . '"
					';
				}
				$script .= '
					}
					ask_and_run "Add lumen folder/files?" func_fs_export
				';
			}

			if (count($this->src_dirs_sec) > 0) {
				$script .= '
					# create blank sec_disk directories
					func_sec_create_blank () {
						cd "' . $this->install_home_dir_sec . '"
				';
				foreach ($this->src_dirs_sec AS $dir) {
					$script .= '
						mkdir -v "' . $dir . '"
					';
				}
				$script .= '
					}
					ask_and_run "Add Sec Disk Blank Folders?" func_sec_create_blank
				';
			}

			if (count($this->src_db_schemas) > 0) {
				$script .= '
					# create structures of whole db schemas
					func_db_schema_blank () {
				';
				foreach ($this->src_db_schemas AS $i => $schema) {
					$script .= '
						output_file=' . $this->install_home_dir_db. '/0000' . $i . '_structure_' . $schema . '.sql
						pg_dump -v -U ' . $this->db_user . ' --no-owner --schema-only --schema=' . $schema . ' ' . $this->db_name . ' > "$output_file"
						sql_alter=$(' . IDEAShellUtils::getPathDBAlterColumns() . ' "$output_file")
						echo "$sql_alter" > "$output_file"
					';
				}
				$script .= '
					}
					ask_and_run "Dump schema(s)?" func_db_schema_blank
				';
			}

			if (count($this->src_db_tables_s) > 0) {
				$script .= '
					# create structures of tables
					func_db_tables_blank () {
				';
				foreach ($this->src_db_tables_s AS $i => $table) {
					$script .= '
						output_file=' . $this->install_home_dir_db. '/' . $i . '_' . $table . '.sql
						pg_dump -v -U ' . $this->db_user . ' --no-owner --schema-only --table=' . $table . ' ' . $this->db_name . ' > "$output_file";
						sql_alter=$(' . IDEAShellUtils::getPathDBAlterColumns() . ' "$output_file")
						echo "$sql_alter" > "$output_file"
					';
				}
				$script .= '
					}
					ask_and_run "Dump Structure of table(s)?" func_db_tables_blank
				';
			}

			if (count($this->src_db_tables_d) > 0) {
				$script .= '
					# create structures and data of tables
					func_db_tables_data () {
				';
				foreach ($this->src_db_tables_d AS $i => $table) {
					$script .= '
						output_file=' . $this->install_home_dir_db. '/' . $i . '_' . $table . '.sql
						pg_dump -v -U ' . $this->db_user . ' --no-owner --insert --column-inserts --table=' . $table . ' ' . $this->db_name . ' > "$output_file";
						sql_alter=$(' . IDEAShellUtils::getPathDBAlterColumns() . ' "$output_file")
						echo "$sql_alter" > "$output_file"
						sql_update=$(' . IDEAShellUtils::getPathDBInsert2Update() . ' "$output_file")
						echo "$sql_update" >> "$output_file"

					';
				}
				$script .= '
					}
					ask_and_run "Dump Structure and Data of table(s)?" func_db_tables_data
				';
			}

			if (count($this->src_xml_tasks) > 0) {
				$script .= '
					# create xml tasks
					func_xml_export () {
				';
				foreach ($this->src_xml_tasks AS $i => $task) {
					$task_file = $this->createXMLTaskFile($task);
					$script .= '
						task_url="' . PHP_MODULE_XML_EXPORT . '?task_file=' . $task_file . '"
						echo "Curling $task_url"
						xml_task=$(curl "$task_url")
						echo $xml_task > "' . $this->install_home_dir_xml . '/' . $i . '_' . 'task.xml"
					';
				}
				$script .= '
					}
					ask_and_run "Export XML Tasks?" func_xml_export
				';
			}

			if (count($this->src_sqls) > 0) {
				$script .= '
					# create sql files for final run
					func_sql_export () {
				';
				foreach ($this->src_sqls AS $i => $sql) {
					$sql = CryptClass::factory()->encode($sql);
					$sql_file = FileUtils::createTmpFile($sql, 'sql');
					$script .= '
						mv -v "' . $sql_file . '" "' . $this->install_home_dir_sql . '/' . $i . '.sql"
					';
				}
				$script .= '
					}
					ask_and_run "Add Final SQL?" func_sql_export
				';
			}

			$script .= '
				ask_and_run "Load Installaton files on broker server?" func_prepare_and_upload
			';
			$script_path = $this->getScriptGetFileName();
			file_put_contents($script_path, $script);
			$this->formatOutputFile($script_path); 

			return $this;
		}

		/**
		 * Create PUT bash script
		 *
		 * @return IDEAInstall
		 */
		public function createScriptPut() {
			$this->setScriptHeader();
			$script = $this->script_header;

			# Variables
			$script .= '
				DB_NAME="' . $this->db_name . '"
				DB_USER="' . $this->db_user . '"
				VNDREFD="' . $this->vndrefid . '"
				PH_ROOT="' . $this->ph_root . '"
				SEC_ROOT="' . $this->sec_root . '"
				INSTALL_HOME_DIR="' . $this->install_home_dir . '"
				INSTALL_HOME_DIR_FS="' . $this->install_home_dir_fs . '"
				INSTALL_HOME_DIR_SEC="' . $this->install_home_dir_sec . '"
				INSTALL_HOME_DIR_DB="' . $this->install_home_dir_db . '"
				INSTALL_HOME_DIR_XML="' . $this->install_home_dir_xml . '"
				INSTALL_HOME_DIR_SQL="' . $this->install_home_dir_sql . '"
			';
			
			# Download and Unpack
			$script .= '
				ask_and_run "Download Installation Files from Broker Server?" func_download_and_prepare
			';

			# MODE - FS 
			$script .= '
				# copy lumen directories
				func_fs_import () {
					if [ -z "$INSTALL_HOME_DIR_FS" ]
					then
						echo "INSTALL_HOME_DIR_FS is zero length."
						exit 1
					fi

					if [ -z "$PH_ROOT" ]
					then
						echo "PH_ROOT is zero length."
						exit 1
					fi

					cd "$INSTALL_HOME_DIR_FS"
					chown -R apache:apache *
					chmod -R 0777 *
					cp --parents -vrp * "$PH_ROOT" 
				}
				files_exist "$INSTALL_HOME_DIR_FS" && ask_and_run "Import lumen folder/files?" func_fs_import
			';

			# MODE - SEC 
			$script .= '
				# copy sec disk directories
				func_sec_import () {
					if [ -z "$INSTALL_HOME_DIR_SEC" ]
					then
						echo "INSTALL_HOME_DIR_SEC is zero length."
						exit 1
					fi

					if [ -z "$SEC_ROOT" ]
					then
						echo "SEC_ROOT is zero length."
						exit 1
					fi

					cd "$INSTALL_HOME_DIR_SEC"
					chown -R apache:apache *
					chmod -R 0777 *
					cp -vrp * "$SEC_ROOT" 
				}
				files_exist "$INSTALL_HOME_DIR_SEC" && ask_and_run "Import Sec Disk folder/files?" func_sec_import
			';

			# MODE - DB
			$script .= '
				# restore DB dumps
				func_db_restore () {
					if [ -z "$INSTALL_HOME_DIR_DB" ]
					then
						echo "INSTALL_HOME_DIR_DB is zero length."
						exit 1
					fi

					if [ -z "$DB_NAME" ]
					then
						echo "DB_NAME is zero length."
						exit 1
					fi

					if [ -z "$DB_USER" ]
					then
						echo "DB_USER is zero length."
						exit 1
					fi

					find "$INSTALL_HOME_DIR_DB" -type f | sort | xargs -n 1 -I {} psql -U "$DB_USER" -d "$DB_NAME" -f {}
 
				}
				files_exist "$INSTALL_HOME_DIR_DB" && ask_and_run "Restore DB dumps?" func_db_restore
			';

			# MODE - XML
			$script .= '
				# import XML data
				func_xml_import () {
					if [ -z "$INSTALL_HOME_DIR_XML" ]
					then
						echo "INSTALL_HOME_DIR_XML is zero length."
						exit 1
					fi

					if [ -z "$VNDREFD" ]
					then
						echo "VNDREFD is zero length."
						exit 1
					fi

					tasks=$(find "$INSTALL_HOME_DIR_XML" -type f | sort)
					for task in $tasks
					do
					    task_file=$(readlink -e $task)
						task_url="' . PHP_MODULE_XML_IMPORT . '?task_file=$task_file&vndrefid=$VNDREFD"
						echo "Curling $task_url"
						xml_task_result=$(curl "$task_url")
						echo $xml_task_result 
					done
				}
				files_exist "$INSTALL_HOME_DIR_XML" && ask_and_run "Import XML Data?" func_xml_import
			';

			# MODE - SQL
			$script .= '
				# import SQL data
				func_sql_import () {
					if [ -z "$INSTALL_HOME_DIR_SQL" ]
					then
						echo "INSTALL_HOME_DIR_SQL is zero length."
						exit 1
					fi

					if [ -z "$VNDREFD" ]
					then
						echo "VNDREFD is zero length."
						exit 1
					fi

					tasks=$(find "$INSTALL_HOME_DIR_SQL" -type f | sort)
					for sql in $tasks
					do
					    sql_file=$(readlink -e $sql)
						sql_url="' . PHP_MODULE_SQL_IMPORT . '?sql_file=$sql_file&vndrefid=$VNDREFD"
						echo "Curling $sql_url"
						sql_result=$(curl "$sql_url")
						echo $sql_result 
					done
				}
				files_exist "$INSTALL_HOME_DIR_SQL" && ask_and_run "Run Final SQL(s)?" func_sql_import
			';
			$script_path = $this->getScriptPutFileName();
			file_put_contents($script_path, $script);
			$this->formatOutputFile($script_path); 

			return $this;
		}

	}
