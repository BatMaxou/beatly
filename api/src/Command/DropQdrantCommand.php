<?php

namespace App\Command;

use App\Enum\EmbeddingEnum;
use App\Service\Client\QdrantClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:drop:qdrant', description: 'Initialize Qdrant collections')]
class DropQdrantCommand extends Command
{
    public function __construct(
        private readonly QdrantClient $qdrantClient,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->qdrantClient->removeCollection(EmbeddingEnum::RECOMMENDATION);
        $this->qdrantClient->removeCollection(EmbeddingEnum::SEARCH);

        $output->writeln('<info>Qdrant collections removed</info>');

        return Command::SUCCESS;
    }
}
