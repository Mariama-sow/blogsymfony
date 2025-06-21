<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:promote-user',
    description: 'Promouvoir un utilisateur à admin ou editeur'
)]
class PromoteUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Promouvoir un utilisateur à admin ou editeur')
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->addArgument('role', InputArgument::REQUIRED, 'Rôle à attribuer (admin ou editeur)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $email = $input->getArgument('email');
        $role = strtoupper($input->getArgument('role'));
        
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        
        if (!$user) {
            $io->error('Utilisateur non trouvé !');
            return Command::FAILURE;
        }

        switch ($role) {
            case 'ADMIN':
                $user->setRoles(['ROLE_ADMIN']);
                $message = sprintf('%s est maintenant Administrateur', $email);
                break;
            case 'EDITEUR':
                $user->setRoles(['ROLE_EDITEUR']);
                $message = sprintf('%s est maintenant Éditeur', $email);
                break;
            default:
                $io->error('Rôle invalide. Utilisez "admin" ou "editeur"');
                return Command::FAILURE;
        }

        $this->entityManager->flush();
        $io->success($message);
        
        return Command::SUCCESS;
    }
}