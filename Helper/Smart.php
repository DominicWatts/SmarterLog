<?php


namespace Xigen\SmarterLog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Smart extends AbstractHelper
{

    const SMARTER_LOG_ENABLED = 'smarter_log/smarter_log/enabled';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

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
        $this->archiveFolder = date("Ymd") . "/";
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
    public function makeDir() {
        $logDirectory = $this->directoryList->getPath('log');
        $rotateDirectory = $logDirectory . $this->archiveFolder;
        $this->file->checkAndCreateFolder($rotateDirectory);
    }

    /**
     * Create zip file
     * @param null $sourceFile
     */
    public function zipFile($sourceFile = null) {
        if($sourceFile) {
            $logDirectory = $this->directoryList->getPath('log');
            $rotateDirectory = $logDirectory . $this->archiveFolder;
            $this->file->cp($logDirectory . $sourceFile, $rotateDirectory . $rotateDirectory);
            $this->zipArchive->pack($rotateDirectory . $rotateDirectory, $rotateDirectory . $rotateDirectory . ".zip");
        }
    }

    /**
     * Find all log files
     */
    public function findLogs() {
        $logDirectory = $this->directoryList->getPath('log');
        $files = glob("$logDirectory/*.txt");
        return $files;
    }

    /**
     * Rotate log files process
     */
    public function rotateLogs() {
        $files = $this->findLogs();
        if($files) {
            $this->makeDir();
            foreach($files as $file) {
                $this->zipFile($file);
            }
        }
    }

}
