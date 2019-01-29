# Private Packagist API Demos

## Magento Demo

### Setup

Start by creating a mage id on Magento Marketplace and downloading the packages.json to the project root.
Generate all the data we need for later examples with the following code, listing any purchased extensions so they can
be treated as such:

```
/bin/packagist magento-generate-data foo/bar [more extension-names here]
```

Create API credentials for your Private Packagist organization and set the two environment variables
`PACKAGIST_API_KEY` and `PACKAGIST_API_SECRET` accordingly.

Set environment variables `MAGENTO_PUBLIC_KEY` and `MAGENTO_PRIVATE_KEY` to allow for package download from
current repo.magento.com.

### Importing all default Magento packages
This loads all data for all packages into your organization, it skips the Magento project/product packages, so we can
use them later to simulate releases.

```
./bin/packagist magento-import
```

### Creating a customer
This will create a customer entry for my username and mage id with a corresponding access key pair. It will also grant
access to all default Magento packages stored in packages.json.

```
./bin/packagist magento-customer-add nils_adermann MAG005458681 [public-key here] [private-key here]
```

### Releasing an extension
This requires the version JSON data to be present in `examples/magento-extensions/[package-name]-[version].json`. Each
JSON file for a version should contain the JSON for all previous versions as well. You can generate these files from the
downloaded packages.json using `examples/magento-export.php`.

```
./bin/packagist magento-extension-release foo/bar 1.0.0
```

### Granting a customer access to an extension
This requires the version JSON data to be present in `examples/magento-extensions/[package-name]-[version].json`. Each
JSON file for a version should contain the JSON for all previous versions as well. You can generate these files from the
downloaded packages.json using `examples/magento-export.php`.

```
./bin/packagist magento-extension-grant MAG005458681 foo/bar
```

### Revoking customer access to an extension
```
./bin/packagist magento-extension-revoke MAG005458681 foo/bar
```

### Deleting an extension
```
./bin/packagist magento-extension-remove foo/bar
```

### Granting a customer Enterprise access
```
./bin/packagist magento-enterprise-grant MAG005458681
```

### Revoking Enterprise access from a customer
```
./bin/packagist magento-enterprise-revoke MAG005458681
```

### Complete Script
```
./bin/packagist magento-generate-data foo/bar
./bin/packagist magento-import
./bin/packagist magento-customer-add nils_adermann MAG005458681 [public-key here] [private-key here]

./bin/packagist magento-release 2.0.0
./bin/packagist magento-release 2.1.0

./bin/packagist magento-extension-release foo/bar 1.0.0
./bin/packagist magento-extension-grant MAG005458681 foo/bar
./bin/packagist magento-extension-release foo/bar 1.2.0
./bin/packagist magento-extension-revoke MAG005458681 foo/bar
./bin/packagist magento-extension-release foo/bar 1.3.0
./bin/packagist magento-extension-grant MAG005458681 foo/bar
./bin/packagist magento-extension-remove foo/bar

./bin/packagist magento-enterprise-grant MAG005458681
./bin/packagist magento-release 2.3.0
./bin/packagist magento-enterprise-revoke MAG005458681
gtgt```
