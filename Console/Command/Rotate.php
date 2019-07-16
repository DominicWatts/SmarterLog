<?php

namespace Xigen\SmarterLog\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Rotate extends Command
{

    const ROTATE_ARGUMENT = 'rotate';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * @var Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @var Xigen\SmarterLog\Helper\Rotate
     */
    protected $rotate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Xigen\SmarterLog\Helper\Rotate $rotate
    ) {
        $this->logger = $logger;
        $this->state = $state;
        $this->dateTime = $dateTime;
        $this->rotate = $rotate;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $this->input = $input;
        $this->output = $output;
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

        $enabled = $this->rotate->getSmarterLogEnabled() ?: false;
        $rotate = $input->getArgument(self::ROTATE_ARGUMENT) ?: false;
        
        if ($rotate && $enabled) {

            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Start');

            $this->rotate->rotateLogs();

            $this->output->writeln('');
            $this->output->writeln('[' . $this->dateTime->gmtDate() . '] Finish');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("xigen:smarterlog:rotate");
        $this->setDescription("Rotate the logs");
        $this->setDefinition([
            new InputArgument(self::ROTATE_ARGUMENT, InputArgument::REQUIRED, 'Generate'),
        ]);
        parent::configure();
    }
}
