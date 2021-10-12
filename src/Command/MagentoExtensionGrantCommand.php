<?php
declare(strict_types=1);

/*
 * This file is part of Composer.
 *
 * (c) Packagist Conductors GmbH <contact@packagist.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PrivatePackagist\Demo\Command;


use PrivatePackagist\ApiClient\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoExtensionGrantCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-extension-grant')
            ->setDescription('Make an extension available to a customer')
            ->addArgument('mage-id', InputArgument::REQUIRED)
            ->addArgument('extension-names', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        $customer = $client->customers()->show(strtolower($input->getArgument('mage-id')));

        $packages = [];
        foreach ($input->getArgument('extension-names') as $packageName) {
            $packages[] = [
                'name' => $packageName,
                //'versionConstraint' => '^1.0 | ^2.0', // optional version constraint to limit updades the customer receives
                //'expirationDate' => (new \DateTime())->add(new \DateInterval('P1Y'))->format('c'), // optional expiration date to limit updates the customer receives
            ];
        }

        $packages = $client->customers()->addOrEditPackages($customer['urlName'], $packages);

        $output->writeln('Customer '.$customer['name'].' (mage id '.$customer['urlName'].') now has access to these extensions:');
        foreach ($packages as $package) {
            $output->writeln('  - '.$package['name']);
        }

        return 0;
    }
}

