<?php

    Security::init(MODE_WS | NO_OUTPUT, 1);

    abstract class Processor {

        /**
         *
         * @param Server $server
         */
        function process(Server $server) {

        }

    }

    class NotificationProcessor extends Processor {

        const PATH = '/applications/webset/notification/sendinfo.php?mode=sendinfo';

        public function process(Server $server) {
            $txt = Requester::request($server->getURL() . self::PATH);
            $arr_m = array();
            preg_match_all("/<message>.+?<\/message>/is", $txt, $out);
            for ($i = 0; $i < count($out[0]); $i++) {
                preg_match_all("/<email>(.+?)<\/email>.+?<subject>(.+?)<\/subject>.+?<body>(.+?)<\/body>/is", $out[0][$i], $messAttr);
                $w = array();
                $w["email"] = base64_decode($messAttr[1][0]);
                $w["subject"] = base64_decode($messAttr[2][0]);
                $w["body"] = base64_decode($messAttr[3][0]);
                $arr_m[] = $w;
            }
            return $arr_m;
        }

    }

    class AlertMailer extends Mailer {

        /**
         *
         * @return AlertMailer
         */
        static public function factory() {
            $mailer = new Mailer();
            $reg = SystemRegistry::factory();
            $from = $reg->getOne('webset', 'mail', 'from', SystemRegistry::SR_SERVER);
            $server = $reg->getOne('webset', 'mail', 'server', SystemRegistry::SR_SERVER);
            $login = $reg->getOne('webset', 'mail', 'login', SystemRegistry::SR_SERVER);
            $pass = $reg->getOne('webset', 'mail', 'pass', SystemRegistry::SR_SERVER);
            return $mailer->server($server, $login, $pass)->from($from);
        }

    }

    class Requester {

        const LISTENERSERVER = 'dev.samconnection.com/lumen';

        /**
         * Making CURL request to server
         * @param string $url
         * @param int $timeout
         * @return string
         */
        public static function request($url, $timeout = 10) {

            // is curl installed?
            if (!function_exists('curl_init')) {
                se('CURL is not installed!');
            }

            $listen_url = "http://" . Requester::LISTENERSERVER . "/applications/webset/notification/listener.php?&timeout=" . $timeout . "&params=" . base64_encode($url);

            // create a new curl resource
            $ch = curl_init();

            // set URL to download
            curl_setopt($ch, CURLOPT_URL, $listen_url);

            // set referer:
            curl_setopt($ch, CURLOPT_REFERER, "http://www.google.com/");

            // user agent:
            curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

            // remove header? 0 = yes, 1 = no
            curl_setopt($ch, CURLOPT_HEADER, 0);

            // should curl return or print the data? true = return, false = print
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // timeout in seconds
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            // download the given URL, and return output
            $output = curl_exec($ch);

            // close the curl resource, and free system resources
            curl_close($ch);

            // print output
            return $output;
        }

    }

    class ServerFactory {

        const ALL = 1;
        const NO_DEMO = 2;
        const OWN = 3;

        /**
         *
         * @return \Server
         */
        public static function getServers($mode, $server_id = NULL) {
            switch ($mode) {
                case self::ALL:
                    $where = "";
                    break;
                case self::NO_DEMO:
                    $where = "AND sname not like '%DEMO%'";
                    break;
                case self::OWN:
                    $where = "
                        AND shost not like '%websis.lumensoftware.com%'
                        AND shost not like '%webset.lumensoftware.com%'
                        AND shost not like '%www.lumensoftware.com%'
                        AND sname not like '%DEMO%'
                        AND s.srefid not in (295, 390, 320, 319)
                    ";
                    break;
                default:
                    $where = "";
            }

            if ($server_id != NULL) {
                $where .= " AND s.srefid in (" . $server_id . ")";
            }


            $SQL = "
                SELECT replace(shost, 'https://', 'http://') || COALESCE(svirtualroot,'') as shost,
                       s.srefid,
                       sname
                  FROM lightbulb.ce_profile_servers as p, global.gl_servers as s
                 WHERE p.srefid = s.srefid
                   AND COALESCE(is_active, 'Y') = 'Y'
                   AND cep_refid = 31
                       $where
                 ORDER BY sname
            ";
            $array = db::execSQL($SQL)->assocAll();
            foreach ($array as $record) {
                $servers[] = new Server($record['srefid'], $record['sname'], $record['shost']);
            }
            return $servers;
        }

    }

    class Server {

        /**
         * Server ID
         * @var int
         */
        private $id;

        /**
         * Server Name
         * @var string
         */
        private $name;

        /**
         * Server URL
         * @var url
         */
        private $url;

        /**
         *
         * @param int $id
         * @param srring $name
         * @param string $url
         */
        function __construct($id, $name, $url) {
            $this->id = $id;
            $this->name = $name;
            $this->url = $url;
        }

        /**
         * Returns Server Name
         * @return string
         */
        public function getName() {
            return $this->name;
        }

        /**
         * Returns Server ID
         * @return string
         */
        public function getID() {
            return $this->id;
        }

        /**
         * Returns Server URL
         * @return string
         */
        public function getURL() {
            return $this->url;
        }

    }

    class Logger {

        /**
         * Log Index
         * @var mixed
         */
        private $index;

        /**
         * Constructor
         * @param int $id
         */
        public function __construct($id) {
            $this->index = $id;
        }

        /**
         * Loggs event
         * @param string $txt
         */
        public function logEvent($txt) {
            $SQL = "
                UPDATE webset.sys_sql_archive
                   SET sql_body = COALESCE(sql_body, '') || '" . addslashes($txt) . PHP_EOL . "',
                       lastupdate = NOW(),
                       lastuser = 'notiflog'
                 WHERE refid = " . $this->index . "
            ";
            db::execSQL($SQL);
            return $this;
        }

        /**
         * Clears Log
         */
        public function logClear() {

            $SQL = "
                UPDATE webset.sys_sql_archive
                   SET sql_body = NULL,
                       lastupdate = NOW(),
                       lastuser = 'notiflog'
                 WHERE refid = " . $this->index . "
            ";
            db::execSQL($SQL);
            return $this;
        }

        /**
         * Reads Log
         */
        public function logRead() {

            $SQL = "
                SELECT sql_body
                  FROM webset.sys_sql_archive
                 WHERE refid = " . $this->index . "
            ";
            return db::execSQL($SQL)->getOne();
        }

        /**
         *
         * @param int $id
         * @return \Logger
         */
        static public function factory($id) {
            return new Logger($id);
        }

    }

    //SP ED NOTIFICATION
    $servers = ServerFactory::getServers(ServerFactory::NO_DEMO, 192);
    $processor = new NotificationProcessor();
    $logger = Logger::factory(1)->logClear();
    $admin_email = SystemRegistry::factory()->getOne('webset', 'mail', 'admin_email', SystemRegistry::SR_SERVER);

    foreach ($servers as $server) {
        $logger->logEvent('Processing ' . $server->getName());
        $server->results = $processor->process($server);
        foreach ($server->results as $email) {
            $logger->logEvent($email['subject'] . ' has been sent to ' . $email['email']);
        }
    }

    AlertMailer::factory()
        ->send($admin_email, 'Sp Ed Notification Log', UICustomHTML::factory('<pre>' . $logger->logRead() . '</pre>')->toHTML());
?>