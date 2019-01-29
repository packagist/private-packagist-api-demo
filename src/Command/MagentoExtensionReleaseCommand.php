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
use PrivatePackagist\ApiClient\Exception\ResourceNotFoundException;
use PrivatePackagist\ApiClient\Exception\RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoExtensionReleaseCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-extension-release')
            ->setDescription('Release a new version of an extension')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('version', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = $this->getPackagistClient();

        $extensionName = $input->getArgument('name');

        $extensionData = json_decode(file_get_contents(
            __DIR__.'/../../examples/magento/extension/'.str_replace('/', '-', $extensionName).'-'.$input->getArgument('version').'.json'));
        
        try {
            $package = $client->packages()->show($extensionName);
        } catch (ResourceNotFoundException $e) {
            $package = null;
        }

        if ($package) {
            $client->packages()->editCustomPackage(
                $extensionName,
                json_encode([
                    "type" => "package",
                    "package" => array_values($extensionData),
                ]),
                $this->getMagentoDownloadCredential()['id']
            );

        } else {
            $client->packages()->createCustomPackage(
                json_encode([
                    "type" => "package",
                    "package" => array_values($extensionData),
                ]),
                $this->getMagentoDownloadCredential()['id']
            );
        }

        return 0;
    }
}

