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

namespace PrivatePackagist\Demo\Customer;


use PrivatePackagist\ApiClient\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoExtensionGrantCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('magento-extension-grant')
            ->setDescription('Make an extension available to a customer')
            ->addArgument('mage-id', InputArgument::REQUIRED)
            ->addArgument('extension-package-names', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client(getenv('PACKAGIST_API_TOKEN'), getenv('PACKAGIST_API_SECRET'));

        $customer = $client->customers()->findByName($input->getArgument('mage-id'));

        $packages = [];
        foreach ($input->getArgument('extension-package-names') as $packageName) {
            $packages[] = [
                'name' => $packageName,
                //'versionConstraint' => '^1.0 | ^2.0', // optional version constraint to limit updades the customer receives
                //'expirationDate' => (new \DateTime())->add(new \DateInterval('P1Y'))->format('c'), // optional expiration date to limit updates the customer receives
            ];
        }

        $packages = $client->customers()->addOrUpdatePackages($customer->id, $packages);

        $output->writeln('Customer '.$customer->name.' (id '.$customer->id.') now has access to these packages:');
        foreach ($packages as $package) {
            $output->writeln('  - '.$package->name);
        }

        return 0;
    }
}

