<?php

namespace Pim\Bundle\ResearchBundle\Infrastructure\Delivery\Http;

use GraphQL\Error\Debug;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Overblog\PromiseAdapter\Adapter\WebonyxGraphQLSyncPromiseAdapter;
use Pim\Bundle\ResearchBundle\Infrastructure\Delivery\API\GraphQL\Type\QueryType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GraphQLController
{
    /** @var QueryType */
    private $queryType;

    /** @var WebonyxGraphQLSyncPromiseAdapter */
    private $promiseAdapter;

    public function __construct(QueryType $queryType, WebonyxGraphQLSyncPromiseAdapter $promiseAdapter)
    {
        $this->queryType = $queryType;
        $this->promiseAdapter = $promiseAdapter;
    }

    public function handleGraphqlRequest(Request $request): Response
    {
        GraphQL::setPromiseAdapter($this->promiseAdapter->getWebonyxPromiseAdapter());

        $data = json_decode($request->getContent(), true);
        $query = $data['query'];
        $variables = $data['variables'] ?? [];

        $schema = new Schema([
            'query' => $this->queryType,
        ]);

        $promise = GraphQL::promiseToExecute(
            $this->promiseAdapter->getWebonyxPromiseAdapter(),
            $schema,
            $query,
            null,
            null,
            $variables
        );

        $result = $this->promiseAdapter->getWebonyxPromiseAdapter()->wait($promise);

        $output = $result->toArray();

        return new JsonResponse($output);
    }
}
