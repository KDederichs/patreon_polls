<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

#[AsCommand('app:user:promote-admin')]
class MakeAdminCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $questionId = new Question('Patreon ID: ', null);
        $patreonId = $helper->ask($input, $output, $questionId);

        if (!$patreonId) {
            $output->writeln('Not a valid ID');
            return self::FAILURE;
        }

        $user = $this->userRepository->findByPatreonId($patreonId);
        if (!$user) {
            $output->writeln('User not found');
            return self::FAILURE;
        }

        $user->setAdmin(true);
        $this->userRepository->save();

        $output->writeln(sprintf('%s has been promoted to admin!', $user->getUsername()));

        return self::SUCCESS;
    }
}
