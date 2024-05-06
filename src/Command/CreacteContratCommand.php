<?php



namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:update-contrat')]
class CreacteContratCommand extends Command

{
    protected static $defaultDescriptioon = 'Update';
    /*  protected function configure()
    {
        $this
            ->setName('create:contrat')
            ->setDescription('Create a new contract');
    } */

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('gggggg');

        $dataService = $this->getApplication()->getKernel()->getContainer()->get('app.cron.task');

        $updateData = $dataService->updateData();
        $output->writeln($updateData);
        return Command::SUCCESS;
    }
}
