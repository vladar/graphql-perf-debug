<?php
use GraphQL\Executor\ExecutionResult;
use GraphQL\Executor\Promise\Adapter\SyncPromiseAdapter;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Overblog\DataLoader\DataLoader;
use Overblog\DataLoader\Option;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\Attribute;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeCode;
use Pim\Bundle\ResearchBundle\DomainModel\Attribute\AttributeRepository;
use Pim\Bundle\ResearchBundle\DomainModel\Family\Family;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyCode;
use Pim\Bundle\ResearchBundle\DomainModel\Family\FamilyLabel;
use Pim\Bundle\ResearchBundle\DomainModel\Locale\LocaleCode;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Loader\AttributeLoader;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Loader\ChannelLoader;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Loader\LocaleLoader;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\AttributeRequirementType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\AttributeType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\CategoryType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\ChannelType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\CurrencyType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\FamilyType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\LabelType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\LocaleType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\ProductType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\QueryType;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Types;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryAttributeRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryCategoryRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryChannelRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryCurrencyRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryFamilyRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryLocaleRepository;
use Pim\Bundle\ResearchBundle\Infrastructure\Persistence\InMemory\InMemoryProductRepository;

require '../vendor/autoload.php';
$woPromiseAdapter = new SyncPromiseAdapter();
$promiseAdapter = new WebonyxGraphQLSyncPromiseAdapter($woPromiseAdapter);

$attributeRepository = new InMemoryAttributeRepository();
$categoryRepository = new InMemoryCategoryRepository();
$channelRepository = new InMemoryChannelRepository();
$currencyRepository = new InMemoryCurrencyRepository();
$familyRepository = new InMemoryFamilyRepository();
$localeRepository = new InMemoryLocaleRepository();
$productRepository = new InMemoryProductRepository();

$attributeLoader = new AttributeLoader($promiseAdapter, $attributeRepository);
$channelLoader = new ChannelLoader($promiseAdapter, $channelRepository);
$localeLoader = new LocaleLoader($promiseAdapter, $localeRepository);

$attributeDataLoader = new DataLoader([$attributeLoader, 'load'], $promiseAdapter, new Option(['cacheKeyFn' => [$attributeLoader, 'key']]));
$channelDataLoader = new DataLoader([$channelLoader, 'load'], $promiseAdapter, new Option(['cacheKeyFn' => [$channelLoader, 'key']]));
$localeDataLoader = new DataLoader([$localeLoader, 'load'], $promiseAdapter, new Option(['cacheKeyFn' => [$localeLoader, 'key']]));

$types = new Types();
$types->register(new AttributeRequirementType($types, $channelDataLoader, $attributeDataLoader));
$types->register(new AttributeType($types));
$types->register(new CategoryType($types, $categoryRepository));
$types->register(new ChannelType($types, $localeDataLoader, $currencyRepository));
$types->register(new CurrencyType());
$types->register(new FamilyType($types, $attributeDataLoader));
$types->register(new LabelType($types, $localeDataLoader));
$types->register(new LocaleType());
$types->register(new ProductType($types, $familyRepository));
$types->register(new QueryType($types, $productRepository, $familyRepository, $localeRepository, $currencyRepository, $channelRepository, $attributeRepository, $categoryRepository));

function createAttribute(AttributeCode $code) {
    return new Attribute(
        $code,
        'type',
        false,
        false
    );
}

function createFamily(FamilyCode $code, $attributeCodes) {
    return new Family(
        $code,
        new \DateTimeImmutable('2017-05-07T00:00:00+00:00'),
        new \DateTimeImmutable('2017-05-08T00:00:00+00:00'),
        null,
        null,
        $attributeCodes,
        [],
        []
    );
}

$familyCodes = array_map(function ($index) { return FamilyCode::createFromString("f$index"); }, range(0, 99));
$attributeCodes = array_map(function ($index) { return AttributeCode::createFromString("a$index"); }, range(0, 99));

foreach ($familyCodes as $codeString) {
    $familyRepository->add(createFamily($codeString, $attributeCodes));
}
foreach ($attributeCodes as $codeString) {
    $attributeRepository->add(createAttribute($codeString));
}

$startTime = microtime(true);

$schema = new Schema([
    'query' => $types->get(QueryType::class),
]);

$query = 'query ($codes: [String!]!){
  families(codes: $codes) {
    code,
    attributes {
      code
    }
  }
}';

$variables = [
    'codes' => array_map(function (FamilyCode $code) { return $code->getValue(); }, $familyCodes),
];

$promise = GraphQL::promiseToExecute(
    $woPromiseAdapter,
    $schema,
    $query,
    null,
    null,
    $variables
);

DataLoader::await();

/** @var ExecutionResult $result */
$result = $promiseAdapter->await($promise, true);
$output = $result->toArray();
// print_r($output);

echo 'Time taken: ' . (microtime(true) - $startTime);
