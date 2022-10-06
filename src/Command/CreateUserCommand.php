<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    protected static $defaultDescription = 'Create User Command';

    private $requirePassword,$passwordHasher,$registry;
    public function __construct(UserPasswordHasherInterface $passwordHasher,ManagerRegistry $registry,bool $requirePassword = true)
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor
        $this->requirePassword = $requirePassword;
        $this->passwordHasher=$passwordHasher;
        $this->registry = $registry->getManager();

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED , 'User email')
            ->addArgument('role', InputArgument::REQUIRED , 'User role')
            ->addArgument('password', $this->requirePassword ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'User password')
            ->setHelp('This command helps to create a new user')
        ;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $emailQuestion = new Question('Provide the user email: ', 'oluomotoso@gmail.com');
        $passwordQuestion = new Question('Provide a password: ', 'secret');
        $roleQuestion = new ChoiceQuestion('Provide the user role',['ROLE_ADMIN','ROLE_MODERATOR'],1);
        $password=$helper->ask($input, $output, $passwordQuestion);
        $email=$helper->ask($input, $output, $emailQuestion);
        $role=$helper->ask($input, $output, $roleQuestion);
        $input->setArgument('email',$email);
        $input->setArgument('role',$role);
        $input->setArgument('password',$password);


    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $input->setInteractive(true);
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $role = $input->getArgument('role');
        $password = $input->getArgument('password');
// ... e.g. get the user data from a registration form
        $user = new User();
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setRoles([$role]);
        $this->registry->persist($user);
        $this->registry->flush();

        $io->success('User Created Successfully');

        return Command::SUCCESS;
    }
}
