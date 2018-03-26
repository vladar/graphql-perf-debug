<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\Database;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\Locale;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleRepository;

class DatabaseLocaleRepository implements LocaleRepository
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

    public function withCode(LocaleCode $localeCode): ?Locale
    {
        $locales = $this->withCodes([$localeCode]);

        return empty($locales) ? null : $locales[0];
    }

    public function withCodes(array $localeCodes): array
    {
        if (empty($localeCodes)) {
            return [];
        }

        $sql = <<<SQL
            SELECT l.code, l.is_activated 
            FROM pim_catalog_locale l
            WHERE l.code IN (:codes)
SQL;

        $connection = $this->entityManager->getConnection();
        $codes = array_map(function(LocaleCode $localeCode) {
            return $localeCode->getValue();
        }, $localeCodes);

        $stmt = $connection->executeQuery($sql,
            ['codes' => $codes],
            ['codes' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        );

        $platform = $this->entityManager->getConnection()->getDatabasePlatform();

        $rows = $stmt->fetchAll();

        $locales = [];
        foreach ($rows as $row) {
            $code = Type::getType(Type::STRING)->convertToPhpValue($row['code'], $platform);
            $enabled = Type::getType(Type::BOOLEAN)->convertToPhpValue($row['is_activated'], $platform);

            $locales[] = new Locale(
                LocaleCode::createFromString($code),
                $enabled
            );
        }

        return $locales;
    }
}
