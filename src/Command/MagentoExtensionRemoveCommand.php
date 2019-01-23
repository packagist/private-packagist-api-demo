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

class MagentoExtensionRemoveCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('magento-extension-remove')
            ->setDescription('Delete an extension')
            ->addArgument('extension-package-names', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client(getenv('PACKAGIST_API_TOKEN'), getenv('PACKAGIST_API_SECRET'));

        return 0;
    }
}

