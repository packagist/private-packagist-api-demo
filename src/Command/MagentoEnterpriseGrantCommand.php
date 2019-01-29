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

class MagentoEnterpriseGrantCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-enterprise-grant')
            ->setDescription('Grant a customer access to Mangento Enterprise packages')
            ->addArgument('mage-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        $customer = $client->customers()->show(strtolower($input->getArgument('mage-id')));

        $packages = [];
        foreach ($this->getMagentoEnterprisePackageNames() as $packageName) {
            $packages[] = [
                'name' => $packageName,
            ];
        }

        $packages = $client->customers()->addOrEditPackages($customer['urlName'], $packages);

        $output->writeln('Granting Enterprise access to customer '.$customer['name'].' (mage id '.$customer['urlName'].'):');
        foreach ($packages as $package) {
            $output->writeln('  - '.$package['name']);
        }

        return 0;
    }
}
