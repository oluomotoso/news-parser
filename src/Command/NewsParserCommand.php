<?php

namespace App\Command;

use App\Service\NewsParser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

//#[AsCommand(
//    name: 'NewsParser',
//    description: 'Add a short description for your command',
//)]
class NewsParserCommand extends Command
{
    protected static $defaultName = 'news:parse';
    protected static $defaultDescription = 'this helps fetch news from any news page';
    private $messageBus;
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        MessageBusInterface $messageBus
    )
    {
        $this->logger = $logger;
        $this->messageBus = $messageBus;
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setHelp('This command helps fetch news from a provided source')
            ->addArgument('rss_url', InputArgument::REQUIRED, 'URL of the RSS feed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $rss = $input->getArgument('rss_url');

        if ($rss) {
            $parse = new NewsParser($this->logger,$this->messageBus);
            $parse->fetchNews($rss);
        }

        $io->success('News Fetched Successfully');

        return Command::SUCCESS;
    }
}
