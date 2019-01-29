<?php
declare(strict_types=1);

/*
 * This file is part of Composer.
 *
 * (c) Packagist Conductors UG (haftungsbeschränkt) <contact@packagist.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrivatePackagist\Demo\Command;


use PrivatePackagist\ApiClient\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteAllCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('delete-all')
            ->setDescription('Deletes all packages, projects and customers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        $output->writeln("Deleting customers");
        foreach ($client->customers()->all() as $customer) {
            $output->write('.');
            $client->customers()->remove($customer['id']);
        }
        $output->writeln("");

        $output->writeln("Deleting projects");
        foreach ($client->projects()->all() as $project) {
            $output->write('.');
            $client->projects()->remove($project['id']);
        }
        $output->writeln("");

        $output->writeln("Deleting packages");
        foreach ($client->packages()->all() as $package) {
            $output->write('.');
            $client->packages()->remove($package['name']);
        }
        $output->writeln("");

        $output->writeln("Deleting credentials");
        foreach ($client->credentials()->all() as $credential) {
            $output->write('.');
            $client->credentials()->remove($credential['id']);
        }
        $output->writeln("");

        return 0;
    }
}
