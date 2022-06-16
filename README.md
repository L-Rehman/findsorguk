# The Portable Antiquities Scheme's Database source code

[![DOI](https://zenodo.org/badge/19055/findsorguk/findsorguk.svg)](https://zenodo.org/badge/latestdoi/19055/findsorguk/findsorguk) [![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

![Scheme logo](https://avatars3.githubusercontent.com/u/4288770?v=3&s=200)

A repository for the current generation of the Portable Antiquities Scheme website. A British Museum project that 
encourages the voluntary recording of archaeological artefacts found by the public in England and Wales. A working 
version of this software can be seen at [https://finds.org.uk](https://finds.org.uk) or at [https://marinefinds.org.uk](https://marinefinds.org.uk) 
and comprehensive instructions about how to install a copy can be found in the [wiki](https://github.com/findsorguk/findsorguk/wiki).

## Requirements

The database is written with a Linux operating system in mind. 

### Operating system requirements:

* Recommended Ubuntu 20.04 or other LTS 
* PHP 7.4.3+
* Solr 4.2
* MySQL 8.0.28
* APC Cache or equivalent
* Curl
* ImageMagick
* GD
* PDO

### PHP libraries

Most of the PHP libaries are included as submodules, which will be pulled on deployment via Git on your server.

* Zend Framework 1.12.21dev
* Solarium 2
* ZendX_JQuery
* HTMLPurifier
* EasyBib
* mpdf
* imagecow

### Geographical boundaries

The system also makes use of converted OS boundary data in geoJSON format (also included as a submodule.) These are also maintained in a [Github repository](https://github.com/findsorguk/findsorguk-geodata).

## Search index

You will also need to install and configure SOLR for the system to be fully functioning. The schemas for this are 
located in our [SOLR repo](https://github.com/findsorguk/findsorguk-solr).

## Virtual host configuration

A set of example [virtual host](https://github.com/findsorguk/vhostsConfigs) configuration files for Apache 2.4 are available. 

## SSL 

It is recommended that the system is configured to use https and the free service offered by Letsencrypt is ideal.

## Contributing 

We welcome code contributions to make this system better. Please refer to our [contributing](CONTRIBUTING.md) guidelines before submitting patches or new features.

## Changes

A [change log](CHANGELOG.md) is now maintained by the project developers to try and explain how things are being improved. 

## Current development team

* [Stephen Moon](https://github.com/s-moon)
* [Liam Rehman](https://github.com/L-Rehman)

## Previous development team

* [Daniel Pett](https://github.com/portableant)
* [Mary Chester-Kadwell](https://github.com/mchesterkadwell)
* [Minakshi Chidrawar](https://github.com/minakshi-chidrawar)
* [Adetokunbo Aribilola](https://github.com/adetoks) 

## Acknowledgements
This software was built on the foundations of Oxford Arch Digital:

* Andrew Larcombe
* Chad
* Kos Vankov
* Tyler Bell
* Vuk Trifkovic
* Yegor Veter

The Scheme is also grateful for the contributions of Richard Wareham (Cambridge University) and Ethan Gruber (ANS).

## License

The codebase is released under [GPL V3](LICENSE.md).

## Funded by

* [Heritage Lottery Fund](https://www.hlf.org.uk/) 
* [DCMS](https://www.gov.uk/government/organisations/department-for-digital-culture-media-sport)
* [Arts and Humanities Research Council](https://ahrc.ukri.org/)
* [The British Museum](https://britishmuseum.org)
