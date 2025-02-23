<?php

namespace App\Command;

use App\Entity\AdminUser;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand('app:make:admin')]
class MakeAdminCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        ?string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $userNameQuestion = new Question('Username: ', null);
        $username = $helper->ask($input, $output, $userNameQuestion);

        if (!$username) {
            $output->writeln('Not a valid username');
            return self::FAILURE;
        }

        $passwordQuestion = new Question('Password: ', null);
        $password = $helper->ask($input, $output, $passwordQuestion);

        $adminUser = new AdminUser();
        $adminUser
            ->setEmail($username)
            ->setPassword($this->passwordHasher->hashPassword($adminUser, $password));

        $this->userRepository->persist($adminUser);

        $this->userRepository->save();

        return self::SUCCESS;
    }
}
