<?php

namespace Xigen\SmarterLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Filesystem\Glob;

class Rotate extends AbstractHelper
{
    const SMARTER_LOG_ENABLED = 'smarter_log/smarter_log/enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var false|string
     */
    protected $archiveFolder;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $file;

    /**
     * @var \Magento\Framework\Archive\Zip
     */
    protected $zip;

    /**
     * @var string
     */
    protected $logDirectory;

    /**
     * @var string;
     */
    protected $rotateDirectory;

    /**
     * Smart constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Archive\Zip $zip
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Archive\Zip $zip
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
        $this->archiveFolder = date("Y/m/d");
        $this->logDirectory = $this->directoryList->getPath('log');
        $this->rotateDirectory = $this->logDirectory . "/" . $this->archiveFolder;
        $this->file = $file;
        $this->zip = $zip;
        parent::__construct($context);
    }

    /**
     * Smarter Log Enabled
     * @return bool
     */
    public function getSmarterLogEnabled()
    {
        return $this->scopeConfig->getValue(
            self::SMARTER_LOG_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Make archive directory
     */
    public function makeDir()
    {
        $this->file->checkAndCreateFolder($this->rotateDirectory . "/");
    }

    /**
     * Create zip file
     * @param null $sourceFile
     */
    public function zipAndDeleteFile($sourceFile = null)
    {
        if ($sourceFile) {
            $sourceFile = basename($sourceFile);
            $copy = $this->file->cp($this->logDirectory . "/" . $sourceFile, $this->rotateDirectory . "/" . $sourceFile);
            if (!$copy) {
                return false;
            }
            $archive = $this->zip->pack($this->rotateDirectory . "/" . $sourceFile, $this->rotateDirectory . "/" . $sourceFile . ".zip");
            if (!$archive) {
                return false;
            }
            $this->file->rm($this->logDirectory . "/" . $sourceFile);
            $this->file->rm($this->rotateDirectory . "/" . $sourceFile);
        }
    }

    /**
     * Find all log files
     */
    public function findLogs()
    {
        $logDirectory = $this->directoryList->getPath('log');
        $files = GLOB::glob("$logDirectory/*.log");
        return $files;
    }

    /**
     * Rotate log files process
     */
    public function rotateLogs()
    {
        $files = $this->findLogs();
        if ($files) {
            $this->makeDir();
            foreach ($files as $file) {
                $this->zipAndDeleteFile($file);
            }
        }
    }
}
