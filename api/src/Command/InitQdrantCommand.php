<?php

namespace App\Command;

use App\Enum\EmbeddingEnum;
use App\Service\Client\QdrantClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:init:qdrant', description: 'Initialize Qdrant collections')]
class InitQdrantCommand extends Command
{
    public function __construct(
        private readonly QdrantClient $qdrantClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->qdrantClient->initCollection(EmbeddingEnum::RECOMMENDATION);
        $this->qdrantClient->initCollection(EmbeddingEnum::SEARCH);

        $output->writeln('<info>Qdrant collections initialized</info>');

        return Command::SUCCESS;
    }
}
