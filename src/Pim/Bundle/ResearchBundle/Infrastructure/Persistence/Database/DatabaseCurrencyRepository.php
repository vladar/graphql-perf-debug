<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\Database;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\Currency;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Currency\CurrencyRepository;

class DatabaseCurrencyRepository implements CurrencyRepository
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

    public function withCode(CurrencyCode $currencyCode): ?Currency
    {
        $currencies = $this->withCodes([$currencyCode]);

        return empty($currencies) ? null : $currencies[0];
    }

    public function withCodes(array $currencyCodes): array
    {
        if (empty($currencyCodes)) {
            return [];
        }

        $sql = <<<SQL
            SELECT c.code, c.is_activated 
            FROM pim_catalog_currency c
            WHERE c.code IN (:codes)
SQL;

        $connection = $this->entityManager->getConnection();
        $codes = array_map(function(CurrencyCode $currencyCode) {
            return $currencyCode->getValue();
        }, $currencyCodes);

        $stmt = $connection->executeQuery($sql,
            ['codes' => $codes],
            ['codes' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        );

        $platform = $this->entityManager->getConnection()->getDatabasePlatform();

        $rows = $stmt->fetchAll();

        $currencies = [];
        foreach ($rows as $row) {
            $code = Type::getType(Type::STRING)->convertToPhpValue($row['code'], $platform);
            $enabled = Type::getType(Type::BOOLEAN)->convertToPhpValue($row['is_activated'], $platform);

            $currencies[] = new Currency(
                CurrencyCode::createFromString($code),
                $enabled
            );
        }

        return $currencies;
    }
}
