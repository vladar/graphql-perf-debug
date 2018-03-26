<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\Infrastructure\Persistence\Database;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeCode;
use Pim\Bundle\ResearchBundle\DomainModel\Channel\ChannelCode;
use Pim\Bundle\ResearchBundle\DomainModel\Family\AttributeRequirement;
use Pim\Bundle\ResearchBundle\DomainModel\Family\Family;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyLabel;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;

class DatabaseFamilyRepository implements FamilyRepository
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

    public function withCode(FamilyCode $familyCode): ?Family
    {
        $families = $this->withCodes([$familyCode]);

        return empty($families) ? null : $families[0];
    }

    public function withCodes(array $familyCodes): array
    {
        $sql = <<<SQL
           SELECT
                f.code, 
                f.created, 
                f.updated,
                a_label.code as attribute_as_label_code,
                a_image.code as attribute_as_image_code,
                attributes.attributes as attribute_codes,
                attribute_requirements.attribute_requirements as attribute_requirements,
				JSON_ARRAYAGG(JSON_OBJECT('locale', ft.locale, 'label', ft.label)) as translations
            FROM pim_catalog_family f
            LEFT JOIN pim_catalog_attribute a_label on a_label.id = f.label_attribute_id
            LEFT JOIN pim_catalog_attribute a_image on a_image.id = f.image_attribute_id
            LEFT JOIN pim_catalog_family_translation ft on ft.foreign_key = f.id
            LEFT JOIN (
                SELECT 
                    f.id as family_attribute_id, 
                    JSON_ARRAYAGG(a.code) as attributes
                FROM pim_catalog_family f
                JOIN pim_catalog_family_attribute fa on fa.family_id = f.id
                JOIN pim_catalog_attribute a on a.id = fa.attribute_id
                GROUP BY f.id
            ) as attributes on attributes.family_attribute_id = f.id
            LEFT JOIN (
				SELECT 
					family_attribute_id,
                    JSON_ARRAYAGG(attribute_requirements_per_channel) as attribute_requirements
				FROM (
					SELECT
						f.id as family_attribute_id,
						JSON_OBJECT('channel', c.code, 'attribute_requirement_codes',  JSON_ARRAYAGG(a.code)) as attribute_requirements_per_channel
					FROM pim_catalog_family f
					JOIN pim_catalog_attribute_requirement r on r.family_id = f.id 
					JOIN pim_catalog_channel c on c.id = r.channel_id and r.required = '1'
					JOIN pim_catalog_attribute a on a.id = r.attribute_id
					GROUP BY f.id, c.code
				) as attribute_requirements_per_channel
                GROUP BY family_attribute_id 
            ) as attribute_requirements on attribute_requirements.family_attribute_id = f.id 
            WHERE f.code IN (:codes)
            GROUP BY f.code, a_label.code, a_image.code
SQL;

        $connection = $this->entityManager->getConnection();
        $codes = array_map(function(FamilyCode $familyCode) {
            return $familyCode->getValue();
        }, $familyCodes);

        $stmt = $connection->executeQuery($sql,
            ['codes' => $codes],
            ['codes' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY]
        );

        $platform = $this->entityManager->getConnection()->getDatabasePlatform();

        $rows = $stmt->fetchAll();

        $families = [];
        foreach ($rows as $row) {
            $code = Type::getType(Type::STRING)->convertToPhpValue($row['code'], $platform);
            $created = Type::getType(Type::DATETIME)->convertToPhpValue($row['created'], $platform);
            $updated = Type::getType(Type::DATETIME)->convertToPhpValue($row['updated'], $platform);
            $attributeAsLabelCode = Type::getType(Type::STRING)->convertToPhpValue($row['attribute_as_label_code'], $platform);
            $attributeAsImageCode = Type::getType(Type::STRING)->convertToPhpValue($row['attribute_as_image_code'], $platform);

            $families[] = new Family(
                FamilyCode::createFromString($code),
                $created,
                $updated,
                null !== $attributeAsLabelCode ? AttributeCode::createFromString($attributeAsLabelCode) : null,
                null !== $attributeAsImageCode ? AttributeCode::createFromString($attributeAsImageCode) : null,
                $this->hydrateAttributeCodes($row),
                $this->hydrateAttributeRequirements($row),
                $this->hydrateLabels($row)
            );
        }

        return $families;
    }

    private function hydrateAttributeCodes(array $row): array
    {
        $attributeCodes = [];
        if (isset($row['attribute_codes'])) {
            $decodedAttributeCodes = json_decode($row['attribute_codes'], true);
            if (null !== $decodedAttributeCodes) {
                foreach ($decodedAttributeCodes as $attributeCode) {
                    $attributeCodes[] = AttributeCode::createFromString($attributeCode);
                }
            }
        }

        return $attributeCodes;
    }

    private function hydrateAttributeRequirements(array $row): array
    {
        $attributeRequirements = [];
        if (!isset($row['attribute_requirements'])) {
            return $attributeRequirements;
        }

        $decodedAttributeRequirements = json_decode($row['attribute_requirements'], true);
        if (null === $decodedAttributeRequirements) {
            return $attributeRequirements;
        }

        foreach ($decodedAttributeRequirements as $attributeRequirement) {
            if (!isset($attributeRequirement['channel'])) {
                continue;
            }

            $attributeRequirementCodes = [];
            foreach ($attributeRequirement['attribute_requirement_codes'] as $attributeCode) {
                $attributeRequirementCodes[] = AttributeCode::createFromString($attributeCode);
            }

            $attributeRequirements[] = new AttributeRequirement(
                ChannelCode::createFromString($attributeRequirement['channel']),
                $attributeRequirementCodes
            );
        }

        return $attributeRequirements;
    }

    private function hydrateLabels(array $row): array
    {
        $labels =[];
        $decodedTranslations = json_decode($row['translations'], true);
        foreach ($decodedTranslations as $translation) {
            if (isset($translation['locale'])) {
                $labels[] = FamilyLabel::createFromLocaleCode(
                    LocaleCode::createFromString($translation['locale']),
                    $translation['label']
                );
            }
        }

        return $labels;
    }
}
