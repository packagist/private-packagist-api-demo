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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoImportCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-import')
            ->setDescription('Loads packages.json file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        foreach ($this->getMagentoBaseData() as $packageName => $versions) {
            $packageDefinition = [
                "type" => "package",
                "package" => array_values($versions),
            ];
            $client->packages()->createCustomPackage(json_encode($packageDefinition), $this->getMagentoDownloadCredential()['id']);
            $output->writeln("Imported $packageName");
        }

        return 0;
    }
}
