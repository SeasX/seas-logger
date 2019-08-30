<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

use Exception;
use function array_shift;
use function count;
use function lcfirst;
use function strpos;
use function substr;
use function trim;
use function version_compare;

/**
 * @method string getClientId()
 * @method string getBrokerVersion()
 * @method array getMetadataBrokerList()
 * @method int getMessageMaxBytes()
 * @method int getMetadataRequestTimeoutMs()
 * @method int getMetadataRefreshIntervalMs()
 * @method int getMetadataMaxAgeMs()
 * @method string getSecurityProtocol()
 * @method bool getSslEnable()
 * @method void setSslEnable(bool $sslEnable)
 * @method string getSslLocalCert()
 * @method string getSslLocalPk()
 * @method bool getSslVerifyPeer()
 * @method void setSslVerifyPeer(bool $sslVerifyPeer)
 * @method string getSslPassphrase()
 * @method void setSslPassphrase(string $sslPassphrase)
 * @method string getSslCafile()
 * @method string getSslPeerName()
 * @method void setSslPeerName(string $sslPeerName)
 * @method string getSaslMechanism()
 * @method string getSaslUsername()
 * @method string getSaslPassword()
 * @method string getSaslKeytab()
 * @method string getSaslPrincipal()
 */
abstract class Config
{
    /**
     * @var mixed[]
     */
    private static $defaults = [
        'clientId' => 'seaslog-kafka',
        'brokerVersion' => '0.10.1.0',
        'metadataBrokerList' => '',
        'messageMaxBytes' => 1000000,
        'metadataRequestTimeoutMs' => 60000,
        'metadataRefreshIntervalMs' => 300000,
        'metadataMaxAgeMs' => -1
    ];
    /**
     * @var mixed[]
     */
    protected $options = [];

    /**
     * Config constructor.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $name => $value) {
            $method = 'set' . ucfirst($name);
            $this->$method($value);
        }
    }

    /**
     * @param string $name
     * @param mixed[] $args
     *
     * @return bool|mixed
     */
    public function __call(string $name, array $args)
    {
        $isGetter = strpos($name, 'get') === 0 || strpos($name, 'iet') === 0;
        $isSetter = strpos($name, 'set') === 0;

        if (!$isGetter && !$isSetter) {
            return false;
        }

        $option = lcfirst(substr($name, 3));

        if ($isGetter) {
            if (isset($this->options[$option])) {
                return $this->options[$option];
            }

            if (isset(self::$defaults[$option])) {
                return self::$defaults[$option];
            }

            if (isset(static::$defaults[$option])) {
                return static::$defaults[$option];
            }

            return false;
        }

        if (count($args) !== 1) {
            return false;
        }

        $this->options[$option] = array_shift($args);

        // check todo
        return true;
    }

    /**
     * @param string $val
     * @throws Exception
     */
    public function setClientId(string $val): void
    {
        $client = trim($val);

        if ($client === '') {
            throw new Exception('Set clientId value is invalid, must is not empty string.');
        }

        $this->options['clientId'] = $client;
    }

    /**
     * @param string $version
     * @throws Exception
     */
    public function setBrokerVersion(string $version): void
    {
        $version = trim($version);

        if ($version === '' || version_compare($version, '0.8.0', '<')) {
            throw new Exception('Set broker version value is invalid, must is not empty string and gt 0.8.0.');
        }

        $this->options['brokerVersion'] = $version;
    }

    /**
     * @param string $brokerList
     * @throws Exception
     */
    public function setMetadataBrokerList(string $brokerList): void
    {
        $brokerList = trim($brokerList);

        $brokers = array_filter(
            explode(',', $brokerList),
            function (string $broker): bool {
                return preg_match('/^(.*:[\d]+)$/', $broker) === 1;
            }
        );

        if (empty($brokers)) {
            throw new Exception(
                'Broker list must be a comma-separated list of brokers (format: "host:port"), with at least one broker'
            );
        }

        $this->options['metadataBrokerList'] = $brokers;
    }

    public function clear(): void
    {
        $this->options = [];
    }

    /**
     * @param int $messageMaxBytes
     * @throws Exception
     */
    public function setMessageMaxBytes(int $messageMaxBytes): void
    {
        if ($messageMaxBytes < 1000 || $messageMaxBytes > 1000000000) {
            throw new Exception('Set message max bytes value is invalid, must set it 1000 .. 1000000000');
        }
        $this->options['messageMaxBytes'] = $messageMaxBytes;
    }

    /**
     * @param int $metadataRequestTimeoutMs
     * @throws Exception
     */
    public function setMetadataRequestTimeoutMs(int $metadataRequestTimeoutMs): void
    {
        if ($metadataRequestTimeoutMs < 10 || $metadataRequestTimeoutMs > 900000) {
            throw new Exception('Set metadata request timeout value is invalid, must set it 10 .. 900000');
        }
        $this->options['metadataRequestTimeoutMs'] = $metadataRequestTimeoutMs;
    }

    /**
     * @param int $metadataRefreshIntervalMs
     * @throws Exception
     */
    public function setMetadataRefreshIntervalMs(int $metadataRefreshIntervalMs): void
    {
        if ($metadataRefreshIntervalMs < 10 || $metadataRefreshIntervalMs > 3600000) {
            throw new Exception('Set metadata refresh interval value is invalid, must set it 10 .. 3600000');
        }
        $this->options['metadataRefreshIntervalMs'] = $metadataRefreshIntervalMs;
    }

    /**
     * @param int $metadataMaxAgeMs
     * @throws Exception
     */
    public function setMetadataMaxAgeMs(int $metadataMaxAgeMs): void
    {
        if ($metadataMaxAgeMs < 1 || $metadataMaxAgeMs > 86400000) {
            throw new Exception('Set metadata max age value is invalid, must set it 1 .. 86400000');
        }
        $this->options['metadataMaxAgeMs'] = $metadataMaxAgeMs;
    }
}
