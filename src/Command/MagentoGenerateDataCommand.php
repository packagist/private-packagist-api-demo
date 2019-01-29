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


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoGenerateDataCommand extends MagentoCommand
{
    protected function configure(): void
    {
        $this->setName('magento-generate-data')
            ->setDescription('Generate JSON data for use with magento commands')
            ->addArgument('extensions', InputArgument::IS_ARRAY);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extensions = $input->getArgument('extensions');

        $data = json_decode(file_get_contents(__DIR__.'/../../packages.json'), true);

        exec('rm -r '.__DIR__.'/../../examples/magento/');
        exec('mkdir -p '.__DIR__.'/../../examples/magento/community');
        exec('mkdir -p '.__DIR__.'/../../examples/magento/enterprise');
        exec('mkdir -p '.__DIR__.'/../../examples/magento/extension');

        $names = [
            'extension' => [],
            'community' => [],
            'enterprise' => [],
        ];

        foreach ($data['packages'] as $packageName => $versions) {
            if (in_array($packageName, $extensions, true)) {
                $types = ['extension'];
            } else {
                $types = ['community', 'enterprise'];
            }

            uksort($versions, function ($a, $b) {
                return version_compare($a, $b);
            });

            foreach ($types as $type) {
                if ($type === 'enterprise') {
                    $names[$type]['enterprise-'.$packageName] = array_keys($versions);
                } else {
                    $names[$type][$packageName] = array_keys($versions);
                }
            }


            $versionsSoFar = [];
            foreach ($versions as $version => $versionData) {
                $versionsSoFar[] = $versionData;
                foreach ($types as $type) {
                    $packageFilename = str_replace('/', '-', $packageName);
                    $writeData = $versionsSoFar;
                    if ($type === 'enterprise') {
                        $packageFilename = 'enterprise-'.$packageFilename;
                        foreach ($writeData as $writeVersion => $writeVersionData) {
                            $writeData[$writeVersion]['name'] = 'enterprise-'.$writeData[$writeVersion]['name'];
                        }
                    }
                    file_put_contents(__DIR__.'/../../examples/magento/'.$type.'/'.$packageFilename.'-'.$version.'.json', json_encode($writeData, JSON_PRETTY_PRINT));
                }
            }
        }

        foreach ($names as $type => $namesForType) {
            file_put_contents(__DIR__.'/../../examples/magento/'.$type.'.json', json_encode($namesForType, JSON_PRETTY_PRINT));
        }

        return 0;
    }
}
