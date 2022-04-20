<?php

namespace Vangrg\ProfanityBundle\Command;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Vangrg\ProfanityBundle\Entity\Profanity;

/**
 * Class ProfanitiesPopulateCommand.
 */
class ProfanitiesPopulateCommand extends Command
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('vangrg:profanities:populate')
            ->setDescription('Load profanities into database.')
            ->addOption('connection',
                null,
                InputOption::VALUE_OPTIONAL,
                'The connection to use for this command. If empty then use default doctrine connection.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->container->get('doctrine');

        $connectionName = $input->getOption('connection');
        $em = (empty($connectionName) === true)
            ? $doctrine->getManagerForClass(Profanity::class)
            : $doctrine->getManager($connectionName);

        $profanities = $this->container->get('vangrg_profanity.storage.default')->getProfanities();

        $existedWords = $em->getRepository(Profanity::class)->getProfanitiesArray();

        $profanities = array_diff($profanities, $existedWords);

        $i = 0;
        foreach ($profanities as $word) {
            $profanity = new Profanity();
            $profanity->setWord($word);

            $em->persist($profanity);

            if (($i % 100) === 0) {
                $em->flush();
                $em->clear();
            }
            ++$i;
        }

        $em->flush();

        $output->writeln(sprintf('Populated %d words', $i));
        
        return Command::SUCCESS;
    }
}
