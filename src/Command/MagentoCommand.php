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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MagentoCommand extends Command
{
    /** @var Client */
    protected $client;

    protected function getPackagistClient()
    {
        if ($this->client) {
            return $this->client;
        }

        $apiKey = getenv('PACKAGIST_API_KEY');
        $apiSecret = getenv('PACKAGIST_API_SECRET');

        if (!$apiKey || !$apiSecret) {
            throw new \RuntimeException("Environment variables PACKAGIST_API_KEY and PACKAGIST_API_SECRET must be set");
        }

        $client = new Client();
        $client->authenticate($apiKey, $apiSecret);

        return $client;
    }

    protected function getMagentoDownloadCredential()
    {
        $client = $this->getPackagistClient();

        foreach ($client->credentials()->all() as $credential) {
            if ($credential['description'] == 'Magento Download Credential') {
                return $credential;
            }
        }

        $publicKey = getenv('MAGENTO_PUBLIC_KEY');
        $privateKey = getenv('MAGENTO_PRIVATE_KEY');

        if (!$privateKey || !$publicKey) {
            throw new \RuntimeException("Environment variables MAGENTO_PUBLIC_KEY and MAGENTO_PRIVATE_KEY must be set");
        }

        // not found so create it
        return $client->credentials()->create(
            'Magento Download Credential',
            \PrivatePackagist\ApiClient\Api\Credentials::TYPE_HTTP_BASIC,
            'repo.magento.com',
            $publicKey,
            $privateKey
        );
    }

    protected function getMagentoBaseData()
    {
        $communityVersions = json_decode(file_get_contents(__DIR__.'/../../examples/magento/community.json'), true);
        $enterpriseVersions = json_decode(file_get_contents(__DIR__.'/../../examples/magento/enterprise.json'), true);

        $communityData = [];
        foreach ($communityVersions as $packageName => $versions) {
            $version = $this->getLatestVersion($packageName, $versions);
            $communityData[$packageName] = json_decode(file_get_contents(__DIR__.'/../../examples/magento/community/'.str_replace('/','-', $packageName).'-'.$version.'.json'), true);
        }

        $enterpriseData = [];
        foreach ($enterpriseVersions as $packageName => $versions) {
            $version = $this->getLatestVersion($packageName, $versions);
            $enterpriseData[$packageName] = json_decode(file_get_contents(__DIR__.'/../../examples/magento/enterprise/'.str_replace('/','-', $packageName).'-'.$version.'.json'), true);
        }

        return array_merge($communityData, $enterpriseData);
    }

    protected function getLatestVersion($packageName, $versions)
    {
        // for the product/project packages only import 2.0.0, so newer versions can be added with magento release command
        if (in_array($packageName, ['magento/product-community-edition', 'magento/project-community-edition', 'enterprise-magento/product-community-edition', 'enterprise-magento/project-community-edition'])) {
            return '2.0.0';
        }

        usort($versions, function ($a, $b) {
            return version_compare($a, $b);
        });

        return end($versions);
    }

    protected function getMagentoReleaseData($version)
    {
        $packages = [
            'magento/product-community-edition' => 'community',
            'magento/project-community-edition' => 'community',
            'enterprise-magento/product-community-edition' => 'enterprise',
            'enterprise-magento/project-community-edition' => 'enterprise',
        ];

        $packageData = [];
        foreach ($packages as $packageName => $type) {
            $packageData[$packageName] = json_decode(file_get_contents(__DIR__.'/../../examples/magento/'.$type.'/'.str_replace('/','-', $packageName).'-'.$version.'.json'), true);
        }

        return $packageData;
    }

    protected function getMagentoCommunityPackageNames()
    {
        return array_keys(json_decode(file_get_contents(__DIR__.'/../../examples/magento/community.json'), true));
    }

    protected function getMagentoEnterprisePackageNames()
    {
        return array_keys(json_decode(file_get_contents(__DIR__.'/../../examples/magento/enterprise.json'), true));
    }
}
