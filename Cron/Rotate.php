<?php

namespace Xigen\SmarterLog\Cron;

/**
 * Rotate class
 */
class Rotate
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Xigen\SmarterLog\Helper\Rotate
     */
    protected $rotate;

    /**
     * Constructor
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Xigen\SmarterLog\Helper\Rotate $rotate
    ) {
        $this->logger = $logger;
        $this->rotate = $rotate;
    }

    /**
     * Execute the cron
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Smarter Log Rotate is started.");
        $enabled = $this->rotate->getSmarterLogEnabled() ?: false;
        if ($enabled) {
            $this->rotate->rotateLogs();
        }

        $this->logger->addInfo("Cronjob Smarter Log Rotate is finished.");
    }
}
