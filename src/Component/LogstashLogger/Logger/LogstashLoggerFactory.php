<?php

namespace OxidEsales\LogstashLogger\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SocketHandler;
use Monolog\Formatter\JsonFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\Factory\LoggerFactoryInterface;
use Psr\Log\LoggerInterface;

class LogstashLoggerFactory implements LoggerFactoryInterface
{
    public function create(): LoggerInterface
    {
        $logger = new Logger('OXID Logger');

        // Handler für oxideshop.log
        $fileHandler = new StreamHandler(OX_BASE_PATH . '/log/oxideshop.log', Logger::ERROR);
        $logger->pushHandler($fileHandler);

        // Handler für Logstash
        $socketHandler = new SocketHandler('tcp://logstash:5000', Logger::DEBUG);
        $socketHandler->setFormatter(new JsonFormatter());
        $logger->pushHandler($socketHandler);

        return $logger;
    }
}
