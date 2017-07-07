<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity\Region;
use AppBundle\Entity\Department;
use AppBundle\Entity\City;

class AppCitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:cities')
            ->setDescription('Importer les villes de france depuis un CSV')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $em EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();

        // yolo
        ini_set("memory_limit", "-1");

        // On vide les 3 tables
        //$connection = $em->getConnection();
        //$platform   = $connection->getDatabasePlatform();
        //$connection->executeUpdate($platform->getTruncateTableSQL('department', true /* whether to cascade */));
        //$connection->executeUpdate($platform->getTruncateTableSQL('region', true /* whether to cascade */));
        //$connection->executeUpdate($platform->getTruncateTableSQL('city', true /* whether to cascade */));

        $csv = dirname($this->getContainer()->get('kernel')->getRootDir()) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'villes.csv';
        $lines = explode("\n", file_get_contents($csv));
        $regions = [];
        $departements = [];
        $villes = [];

        foreach ($lines as $k => $line) {
            $line = explode(';', $line);
            if (count($line) > 10 && $k > 0) {
                // On sauvegarde la region
                if (!key_exists($line[1], $regions)) {
                    $region = new Region();
                    $region->setCode($line[1]);
                    $region->setName($line[2]);
                    $regions[$line[1]] = $region;
                    $em->persist($region);
                } else {
                    $region = $regions[$line[1]];
                }

                // On sauvegarde le departement
                if (!key_exists($line[4], $departements)) {
                    $departement = new Department();
                    $departement->setName($line[5]);
                    $departement->setCode($line[4]);
                    $departement->setRegion($region);
                    $departements[$line[4]] = $departement;
                    $em->persist($departement);
                } else {
                    $departement = $departements[$line[4]];
                }

                // On sauvegarde la ville
                $ville = new City();
                $ville->setName($line[8]);
                $ville->setCode($line[9]);
                $ville->setLat($line[11]);
                $ville->setLong($line[12]);
                $ville->setDepartment($departement);
                $villes[] = $line[8];
                $em->persist($ville);
            }
        }
        $em->flush();
        $output->writeln(count($villes) . ' villes importées');
    }

}