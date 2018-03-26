<?php

declare(strict_types=1);

namespace Pim\Bundle\ResearchBundle\DomainModel\Product;

class ValueCollectionFactory
{
    public function createFromStorageFormat(
        array $rawValues,
        array $attributeCodesToKeep,
        array $localeCodesToKeep,
        array $channelCodesToKeep
    ) {
        $values = [];

        foreach ($rawValues as $attributeCode => $channelRawValue) {
            if (isset($attributeCodesToKeep[$attributeCode])) {
                foreach ($channelRawValue as $channelCode => $localeRawValue) {
                    if ('<all_channels>' !== $channelCode && !isset($channelCodesToKeep[$channelCode])) {
                        continue;
                    }

                    if ('<all_channels>' === $channelCode) {
                        $channelCode = null;
                    }

                    foreach ($localeRawValue as $localeCode => $data) {
                        if ('<all_locales>' !== $localeCode && !isset($localeCodesToKeep[$localeCode])) {
                            continue;
                        }

                        if ('<all_locales>' === $localeCode) {
                            $localeCode = null;
                        }

                        $values[] = $this->valueFactory->create($attributeCodesToKeep[$attributeCode], $channelCode, $localeCode, $data);
                    }
                }
            }
        }

        return $values;
    }
}
