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

class MagentoEnterpriseRevokeCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-enterprise-revoke')
            ->setDescription('Revokes Magento Enterprise access from a customer')
            ->addArgument('mage-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        $customer = $client->customers()->show(strtolower($input->getArgument('mage-id')));

        $output->writeln('Revoking Enterprise access for customer '.$customer['name'].' (mage id '.$customer['urlName'].'):');

        $packages = [];
        foreach ($this->getMagentoEnterprisePackageNames() as $packageName) {
            try {
                $client->customers()->removePackage($customer['urlName'], $packageName);
            } catch (ResourceNotFoundException $e) {
                // ignore, package did not exist
            }
            $output->writeln('  - '.$packageName);
        }

        return 0;
    }
}
