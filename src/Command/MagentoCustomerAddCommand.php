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

class MagentoCustomerAddCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('customer-add')
            ->setDescription('Add a customer')
            ->addArgument('mage-id', InputArgument::REQUIRED)
            ->addArgument('public-key', InputArgument::REQUIRED)
            ->addArgument('private-key', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Client(getenv('PACKAGIST_API_TOKEN'), getenv('PACKAGIST_API_SECRET'));

        $customer = $client->customers()->create($input->getArgument('mage-id'));
        $customer->setMagentoCredentials($input->getOption('public-key'), $input->getOption('private-key'));

        $output->writeln('Created customer '.$customer->name.' with id '.$customer->id.' and set up magento credentials.');

        // TODO grant access to all standard magento packages here

        return 0;
    }
}
