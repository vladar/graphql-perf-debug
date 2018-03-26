<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\Database;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\ResearchBundle\DomainModel\Category\CategoryCode;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Product\Product;
use Pim\Bundle\ResearchBundle\DomainModel\Product\ProductIdentifier;
use Pim\Bundle\ResearchBundle\DomainModel\Product\ProductRepository;

class DatabaseProductRepository implements ProductRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function withIdentifier(ProductIdentifier $productIdentifier): ?Product
    {
        $sql = <<<SQL
            SELECT 
                p.created, 
                p.updated, 
                p.is_enabled, 
                p.raw_values,
                f.code as family_code,
                JSON_ARRAYAGG(c.code) as category_codes
            FROM pim_catalog_product p
            LEFT JOIN pim_catalog_family f ON f.id = p.family_id
            LEFT JOIN pim_catalog_category_product cp on cp.product_id = p.id
            LEFT JOIN pim_catalog_category c on c.id = cp.category_id
            WHERE p.identifier = :identifier
            GROUP BY p.identifier
SQL;

        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->bindValue('identifier', $productIdentifier->getValue());
        $stmt->execute();
        $row = $stmt->fetch();

        if (false === $row) {
            return null;
        }

        $platform = $this->entityManager->getConnection()->getDatabasePlatform();

        $created = Type::getType(Type::DATETIME)->convertToPhpValue($row['created'], $platform);
        $updated = Type::getType(Type::DATETIME)->convertToPhpValue($row['updated'], $platform);
        $isEnabled = Type::getType(Type::BOOLEAN)->convertToPhpValue($row['is_enabled'], $platform);
        $familyCode = Type::getType(Type::STRING)->convertToPhpValue($row['family_code'], $platform);

        return new Product(
            $productIdentifier,
            $created,
            $updated,
            $isEnabled,
            null !== $familyCode ? FamilyCode::createFromString($familyCode) : null,
            $this->hydrateCategoryCodes($row)
        );
    }

    private function hydrateCategoryCodes(array $row): array
    {
        $categoryCodes = [];
        if (isset($row['category_codes'])) {
            $decodedCategoryCodes = json_decode($row['category_codes'], true);
            if (null !== $decodedCategoryCodes) {
                foreach ($decodedCategoryCodes as $categoryCode) {
                    if (isset($categoryCode)) {
                        $categoryCodes[] = CategoryCode::createFromString($categoryCode);
                    }
                }
            }
        }

        return $categoryCodes;
    }
}
