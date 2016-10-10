<?php
namespace Synerise;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;

abstract class SyneriseAbstractHttpClient extends Client
{

    /** @var array The required config variables for this type of client */
    public static $required = [
        'apiKey',
        'headers',
    ];

    /** @var string */
    const DEFAULT_CONTENT_TYPE = 'application/json';

    /** @var string */
    const DEFAULT_ACCEPT_HEADER = 'application/json';

    /** @var string */
    const USER_AGENT = 'synerise-php-sdk/2.1';

    /** @var string */
    const DEFAULT_API_VERSION = '2.1';

    /** @var string */
    const BASE_API_URL = 'http://api.synerise.com';

    /** @var string */
    const BASE_TCK_URL = 'http://tck.synerise.com/sdk-proxy';


    private static $_instances = array();

    protected $_pathLog = './var/log/synerise.log';

    protected $_log = true;


    /**
     * Returns a singleton instance of SyneriseAbstractHttpClient
     * @param array $config
     * @return SyneriseAbstractHttpClient
     */
    public static function getInstance($config = array(), $logger = null)
    {
        $class = get_called_class();

        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class($config, $logger);
        }
        return self::$_instances[$class];
    }

    /**
     * Instantiates a new instance.
     * @param array $config
     */
    public function __construct($config = array(), $logger = null)
    {
        $config = self::mergeConfig($config);

        $stack = HandlerStack::create();

        if($logger) {

            if ($logger instanceof \Psr\Log\LoggerInterface ) {
                $stack->push(
                    Middleware::log(
                        $logger,
                        new MessageFormatter("\n----------------\n{req_headers}\n\n{req_body}"
                            . "\n----------------\n{res_headers}\n\n{res_body}\n----------------\n")
                    )
                );
            } else {
                throw new \Exception('Logger must implement PsrLogLoggerInterface');
            }
        }

        $config['handler'] = $stack;

        $this->setErrorHandler();
        
        parent::__construct($config);
    }

    /**
     * Overrides the error handling in Guzzle so that when errors are encountered we throw
     * Synersie errors, not Guzzle ones.
     *
     */
    private function setErrorHandler()
    {
//        $this->getEmitter()->on('error', function (ErrorEvent $e) {
            //@TODO ErrorHendler
            //if ($e->getResponse()->getStatusCode() >= 400 && $e->getResponse()->getStatusCode() < 600) {
            //}
//        });
    }

    /**
     * Gets the default configuration options for the client
     *
     * @return array
     */
    public static function getDefaultConfig()
    {
        return [
            'base_uri' => self::BASE_API_URL,
            'headers' => [
                'Content-Type' => self::DEFAULT_CONTENT_TYPE,
                'Accept' => self::DEFAULT_ACCEPT_HEADER,
                'User-Agent' => self::USER_AGENT,
                'Api-Version' => self::DEFAULT_API_VERSION,
            ]
        ];
    }

    /**
     * @return bool|string
     */
    protected function getUuid()
    {

        $snrsP = isset($_COOKIE['_snrs_p'])?$_COOKIE['_snrs_p']:false;
        if ($snrsP) {
            $snrsP = explode('&', $snrsP);
            foreach ($snrsP as $snrs_part) {
                if (strpos($snrs_part, 'uuid:') !== false) {
                    return str_replace('uuid:', null, $snrs_part);
                }
            }
        }

        return false;
    }

    public function setPathLog($pathFile)
    {
        $this->_pathLog = $pathFile;
    }

    public function _log($message, $tag)
    {
        if ($this->_log) {
            file_put_contents($this->_pathLog, print_r("----------------\n" .
                date("Y-m-d H:i:s") . " $tag: \n " . (string)$message . "\n", true), FILE_APPEND);
        }
    }

    /**
     * Merge config with defaults
     *
     * @param array $config   Configuration values to apply.
     *
     * @return array
     * @throws \InvalidArgumentException if a parameter is missing
     */
    protected function mergeConfig(array $config = []) {

        $defaults = static::getDefaultConfig();
        $required = static::$required;

        $data = $config + $defaults;

        if ($missing = array_diff($required, array_keys($data))) {
            throw new \InvalidArgumentException(
                'Config is missing the following keys: ' .
                implode(', ', $missing));
        }

        if(isset($config['apiKey'])) {
            $data['headers']['Api-Key'] = $config['apiKey'];
        }

        if(isset($config['apiVersion'])) {
            $data['headers']['Api-Version'] = $config['apiVersion'];
        }

        return ($data);
    }

}