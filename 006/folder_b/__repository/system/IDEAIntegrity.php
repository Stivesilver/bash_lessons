<?php

    /**
     * IDEA Integrity class
     * This class provides IDEA check utilities
     *
     * @final
     * @copyright Lumen Touch, 2012
     */
    class IDEAIntegrity extends RegularClass {

        /**
         * Long repeating SQL field
         */
        const SQL_FIELD_SERVER_URL = "'https://' || REPLACE(REPLACE(shost, 'http://', ''), 'https://', '') || COALESCE(svirtualroot ,'') AS url";

        /**
         * listenerServer url
         *
         * @var string
         */
        private static $listenerServer = "dev.lumentouch.com/lumen";

        /**
         * devServer url
         *
         * @var string
         */
        private static $devServer = "dev.lumentouch.com/lumen";

        /**
         * Returns Servers array
         *
         * @param string $table
         * @return string
         */
        public static function readServerList($xml) {
            if ($xml == '') throw new Exception('Please specify xml.');
            $params = new SimpleXMLElement($xml);

            $profiles = (string) $params->profiles;
            $mainServerId = (int) $params->mainserver;
            $serversOnly = (string) $params->serversonly;

            $SQL = "
                SELECT srefid,
                       sname,
                       " . self::SQL_FIELD_SERVER_URL . "
                  FROM global.gl_servers
                 WHERE srefid = " . $mainServerId . "
                 UNION ALL
               (SELECT s.srefid,
                       sname,
                       " . self::SQL_FIELD_SERVER_URL . "
                  FROM global.gl_servers as s
                 WHERE EXISTS (SELECT 1
                                 FROM lightbulb.ce_profile_servers as p
                                WHERE p.srefid = s.srefid
                                  AND cep_refid in (" . $profiles . ")
                                  )
                   AND COALESCE(is_active, 'Y') = 'Y'
                   " . ($serversOnly ? "AND s.srefid in (" . $serversOnly . ")" : "" ) . "
                 ORDER BY sname)
            ";
            $serverLines = db::execSQL($SQL)->assocAll();

            return base64_encode(CryptClass::factory()->encode(serialize($serverLines)));
        }

        /**
         * Returns Hash Value of Table
         * Example: readTableHash1'webset.dmg_studentmst')
         *
         * @param string $table
         * @return string
         */
        public static function readTableHash($xml) {
            if ($xml == '') throw new Exception('Please specify xml.');
            $params = new SimpleXMLElement($xml);
            $table = (string) $params->table;
            $SQL = "
				SELECT *
                  FROM " . $table . "
                 ORDER BY 1
                 LIMIT 1
			";
            $columns = db::execSQL($SQL)->columns();
            $keyField = $columns[0];
            sort($columns);

            $SQL = "
                SELECT " . implode(',', $columns) . "
                  FROM " . $table . "
                 ORDER BY " . $keyField . "
            ";
            $data = db::execSQL($SQL)->assocAll();
            return md5(print_r($data, true));
        }

        /**
         * Checks Hash Values of Table
         *
         * @param string $table
         * @param string $hash
         * @param int $mainServerId
         * @return string
         */
        public static function checkHashes($clientHash = null, $mainHash = null) {
            if ($clientHash == null) throw new Exception('Please specify client hash.');
            if ($mainHash == null) throw new Exception('Please specify main server hash.');

            if (strlen($clientHash) == 32 && strlen($mainHash) == 32) {
                if ($clientHash == $mainHash) {
                    return array('valid' => 'Y', 'details' => '');
                } else {
                    return array('valid' => 'N', 'details' => 'Client hash "' . $clientHash . '" is not equal to Main hash "' . $mainHash . '"');
                }
            }
        }

		/**
		 * Returns Count for table
		 * Example: readTableCount('webset.dmg_studentmst')
		 *
		 * @param string $table
		 * @return string
		 */
		public static function readTableCount($xml) {
			if ($xml == '') throw new Exception('Please specify xml.');
			$params = new SimpleXMLElement($xml);
			$table = (string) $params->table;
			$where = (string) $params->where;
			$SQL = "
				SELECT count(1)
				  FROM " . db::escape($table) . "
				" . db::escape($where) . "
				";

			return db::execSQL($SQL)->getOne();
		}

		/**
		 * This method simply add output to details and consider blank output as none-valid
		 *
		 * @param string $output
		 * @return string
		 */
		public static function checkSimpleOutput($output = null) {
			if ($output == null) {
				return array('valid' => 'N', 'details' => '');
			} else {
				return array('valid' => 'Y', 'details' => $output);
			}
		}

        /**
         * Read file contents
         * Example: readFile('/apps/idea/sys_maint/support/system/check.example.php')
         *
         * @param string $xml
         */
        public static function readFile($xml) {
            if ($xml == '') throw new Exception('Please specify xml.');
            $params = new SimpleXMLElement($xml);
            $file = (string) $params->file;
            ob_start();
            include(SystemCore::$physicalRoot . $file);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }

        /**
         * Checks File Contents
         *
         * @param string $table
         * @param string $hash
         * @param int $mainServerId
         * @return string
         */
        public static function checkFile($clientContent = null, $mainContent = null) {

            if ($clientContent == $mainContent) {
                return array('valid' => 'Y', 'details' => $clientContent);
            } else {
                return array('valid' => 'N', 'details' => 'Client content "' . $clientContent . '" is not equal to Main content "' . $mainContent . '"');
            }
        }

        /**
         * Makes curl query to specified url
         *
         * @param string $url
         * @param string $xml
         * @param bool $useListerner
         * @param int $timeout
         * @return string
         */
        public static function DownloadUrl($url = null, $xml = null, $useListerner = true, $timeout = 10) {
            if ($url == null) throw new Exception('Please specify URL');
            if ($xml == null) throw new Exception('Please specify XML');

            // is curl installed?
            if (!function_exists('curl_init')) {
                se('CURL is not installed!');
            }

            if ($useListerner) {
                $url = 'http://' .
                    self::$listenerServer .
                    '/apps/idea/sys_maint/support/system/check.listener.php' .
                    '?url=' . base64_encode(CryptClass::factory()->encode($url));

                $xml = base64_encode(CryptClass::factory()->encode($xml));
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/");
            curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'xml=' . $xml);
            $output = curl_exec($ch);
            # repeat request if SSL problem detected without https
            if (curl_errno($ch) == '60') {
                $url = str_replace('https://', 'http://', $url);
                curl_setopt($ch, CURLOPT_URL, $url);
                $output = curl_exec($ch);
            }
            curl_close($ch);
            return $output;
        }

        /**
         * Generates SQL report based on input xml data
         *
         * @param xml $template
         * @param xml $data
         * @return void
         */
        public function genReport($template = null, $data = null) {
            if ($template == null) throw new Exception('Please specify Report Template in XML format');
            $dataRows = array();
            $dataTree = new SimpleXMLElement($data);
            $templateTree = new SimpleXMLElement($template);

            foreach ($templateTree->xpath('checks/check') as $check) {
                $checks[(string) $check['id']]['checkmethod'] = (string) $check->checkmethod;
                $checks[(string) $check['id']]['parameters'] = (array) $check->parameters;
            }

            foreach ($dataTree->xpath('mainresult/read_results/read_result') as $read_result) {
                $main[(string) $read_result['id']] = (string) $read_result;
            }

            foreach ($dataTree->xpath('clientresult') as $clientResult) {
                if ($clientResult->read_results) {
                    foreach ($clientResult->xpath('read_results/read_result') as $read_result) {
                        $arr = null;
                        $check_id = (string) $read_result['id'];
                        $arr['id'] = (int) $clientResult['server_id'];
                        $arr['server'] = (string) $clientResult['server_name'] . ' (Id: ' . (string) $clientResult['server_id'] . ')';
                        $arr['checkmethod'] = $checks[$check_id]['checkmethod'];
                        $arr['parameters'] = implode(', ', $checks[$check_id]['parameters']);
                        $finalCheck = self::$arr['checkmethod']((string) $read_result, $main[$check_id]);
                        $arr['valid'] = $finalCheck['valid'];
                        $arr['details'] = $finalCheck['details'];
                        $dataRows[] = $arr;
                    }
                } else {
                    $arr = null;
                    $arr['id'] = (int) $clientResult['server_id'];
                    $arr['server'] = (string) $clientResult['server_name'] . ' (Id: ' . (string) $clientResult['server_id'] . ')';
                    $arr['checkmethod'] = FFTextArea::factory('Error Description')
                        ->value(base64_decode((string) $clientResult->fail_results))
                        ->toHTML();
                    $arr['param'] = '';
                    $arr['valid'] = '';
                    $arr['details'] = (string) $clientResult;
                    $dataRows[] = $arr;
                }
            }

            return DataStorage::factory()
                    ->set('dataRows', serialize($dataRows))
                    ->getKey();
        }

        /**
         * Run Integrity Process on Main Server
         *
         * @param mixed $xml
         */
        public static function serverProcess($xml = null, $showProgress = false) {
            if ($xml == null) throw new Exception('Please specify Integrity Process XML.');

            $servers = array();

            $processTree = new SimpleXMLElement($xml);
            $mainServerId = (int) $processTree->mainserver;
            $checkTree = current($processTree->xpath('/integrity_process/checks'));

            if (isset($processTree->mainserver)) {
                $mainServerId = (int) $processTree->mainserver;
            } else {
                throw new Exception('Please specify Main Server ID');
            }

            if (isset($processTree->profiles)) {
                $profiles = (array) $processTree->profiles;
                $profiles = $profiles['profile'];
            } else {
                throw new Exception('Please specify Profile ID');
            }

            if (isset($processTree->servers)) {
                foreach ($processTree->xpath('/integrity_process/servers/server') as $serverId) {
                    $servers[] = (int) $serverId;
                }
            }
            $serverLines = self::getServerList($profiles, $mainServerId, $servers);

            $result = '<results>' . PHP_EOL;

            $result .= '<mainresult>' . PHP_EOL;
            $result .= self::DownloadUrl($serverLines[0]['url'], $checkTree->asXML()) . PHP_EOL;
            $result .= '</mainresult>' . PHP_EOL;
            unset($serverLines[0]);

            foreach ($serverLines as $i => $server) {
                if ($showProgress) io::progress($i / count($serverLines), $server['sname']);
                $result .= '<clientresult server_id="' . $server['srefid'] . '" server_name="' . htmlentities($server['sname']) . '">' . PHP_EOL;
                $data = self::DownloadUrl($server['url'], $checkTree->asXML());
                if (strstr($data, '<read_results>')) {
                    $result .= $data . PHP_EOL;
                } else {
                    $result .= '<fail_results>' . base64_encode($data) . '</fail_results>' . PHP_EOL;
                }
                $result .= '</clientresult>' . PHP_EOL;
            }

            $result .= '</results>';
            return $result;
        }

        /**
         * Creates list of servers
         *
         * @param array $profiles
         * @param int $mainServerId
         * @param array $servers
         * @return array
         */
        public static function getServerList($profiles = null, $mainServerId = null, $servers = null) {
            if ($profiles == null) throw new Exception('Please specify Profiles Array.');
            if ($mainServerId == null) throw new Exception('Please specify Main Server ID.');

            $xml = '
            	<checks>
		        	<check id="1">
		            	<readmethod>readServerList</readmethod>
		            		<parameters>
		            	    		<profiles>' . (is_array($profiles) ? implode(',', $profiles) : $profiles) . '</profiles>
		            	    		<mainserver>' . $mainServerId . '</mainserver>
		            	    		<serversonly>' . ($servers == null ? '' : implode(',', $servers)) . '</serversonly>
		            		</parameters>
		        	</check>
		     	</checks>
		    ';
            $resultTree = new SimpleXMLElement(self::DownloadUrl(self::$devServer, $xml));
            $result = (string) $resultTree->read_result;

            return unserialize(CryptClass::factory()->decode(base64_decode($result)));
        }

        /**
         * Run Read Checks on Client Server
         *
         * @param mixed $xml
         */
        public static function clientProcess($xml = null) {
            if ($xml == null) throw new Exception('Please specify Client Process XML.');
            $checksTree = new SimpleXMLElement($xml);
            $answer = '<read_results>' . PHP_EOL;
            foreach ($checksTree as $check) {
                $checkId = $check['id'];
                $readmethod = (string) $check->readmethod;
                $parameters = $check->parameters;
                $answer .= '<read_result id="' . $checkId . '">' . self::$readmethod($parameters->asXML()) . '</read_result>' . PHP_EOL;
            }
            $answer .= '</read_results>';
            return $answer;
        }

    }

?>
