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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoCustomerAddCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-customer-add')
            ->setDescription('Add a customer')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('mage-id', InputArgument::REQUIRED)
            ->addArgument('public-key', InputArgument::REQUIRED)
            ->addArgument('private-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        $customer = $client->customers()->create($input->getArgument('username'), false, strtolower($input->getArgument('mage-id')));
        $client->customers()->magentoLegacyKeys()->create($customer['urlName'], $input->getArgument('public-key'), $input->getArgument('private-key'));

        $output->writeln('Created customer '.$customer['name'].' with mage id '.$customer['urlName'].', Packagist id '.$customer['id'].' and set up magento credentials.');

        $packages = [];
        foreach ($this->getMagentoCommunityPackageNames() as $packageName) {
            $packages[] = [
                'name' => $packageName,
            ];
        }
        $client->customers()->addOrEditPackages($customer['urlName'], $packages);

        $output->writeln("Granted customer ".$customer['urlName']." access to all community packages");

        return 0;
    }
}
