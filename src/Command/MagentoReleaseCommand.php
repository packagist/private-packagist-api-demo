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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoReleaseCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('magento-release')
            ->setDescription('Releases a new version of Magento');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client(getenv('PACKAGIST_API_TOKEN'), getenv('PACKAGIST_API_SECRET'));

        $magentoCommunityPackages = [];
        $magentoEnterprisePackages = [];

        $magentoPackages = array_merge($magentoCommunityPackages, $magentoEnterprisePackages);

        foreach ($magentoPackages as $packageName) {
            $package = $client->show($packageName);

            $packageDefinition = [

            ];

            // if the package is new, create it, otherwise add missing versions
            if (!$package) {
                $client->packages()->createCustomPackage($packageDefinition);
            } else {
                $client->packages()->updateCustomPackage($packageName, $packageDefinition);
            }
        }

        return 0;
    }
}
