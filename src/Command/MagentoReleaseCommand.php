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
use PrivatePackagist\ApiClient\Exception\ResourceNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoReleaseCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-release')
            ->setDescription('Releases a new version of Magento')
            ->addArgument('version', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        foreach ($this->getMagentoReleaseData($input->getArgument('version')) as $packageName => $versions) {
            $packageDefinition = [
                "type" => "package",
                "package" => array_values($versions),
            ];
            $client->packages()->createCustomPackage(json_encode($packageDefinition));
            $output->writeln("Imported $packageName");

            $client->packages()->editCustomPackage(
                $packageName,
                json_encode([
                    "type" => "package",
                    "package" => array_values($versions),
                ]),
                $this->getMagentoDownloadCredential()['id']
            );
        }

        return 0;
    }
}
