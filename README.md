Collection of common classes used in Nalgoo APIs
================================================

Credits
-------
Some classes taken from slim-skeleton: https://github.com/slimphp/Slim-Skeleton

Changes
-------

### v4

- removed ORM functionality, it will be migrated to separate package
- bumped minimal PHP version to 8.3
- bumped symfony/serializer to v7
- bumped lcobucci/jwt to v5
- drop support for league/uri v6
- bumped minor versions of other dependencies
- removed PropertyNormalizer and DoctrineCollectionNormalizer
- updated GenderNormalizer and IdentifierNormalizer to match changes in upstream interfaces
- added PSR-20 Clock interface
- added UniqueConstraintException

License
-------
MIT
