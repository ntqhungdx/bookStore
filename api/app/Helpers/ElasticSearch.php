<?php

namespace App\Helpers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Exception;

class ElasticSearch
{
    /**
     * Get elasticSearch client
     *
     * @return Client|null
     */
    public static function getClient(): ?Client
    {
        try {
            $configuration = config('database.connections.elasticsearch.hosts')[0];
            $dns = $configuration['host'] . ':' . $configuration['port'];

            $client = ClientBuilder::create()
                ->setHosts([$dns])
                ->build();

            $params = [
                'index' =>  config('services.elasticSearch.default_index'),
            ];

            if ($client->indices()->exists($params)->getStatusCode() === 404) {
                $params['body'] = [
                    'mappings' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties'    =>  [
                            'book_id' => [
                                'type'  => 'keyword',
                            ],
                        ],
                    ],
                ];
                $client->indices()->create($params);
            }

            return $client;
        } catch (Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Search with a keyword
     *
     * @param string $keyword
     * @param int $limit
     * @param string $fieldName
     * @return array|null
     */
    public static function search(string $keyword, int $limit, string $fieldName = ''): ?array
    {
        if (empty($keyword)) {
            return null;
        }

        $client = self::getClient();

        if (empty($client)) {
            return null;
        }

        if (empty($fieldName)) {
            $fieldName = config('services.elasticSearch.default_field');
        }

        $params = [
            'index' =>  config('services.elasticSearch.default_index'),
            'size'  =>  $limit,
            'sort'  =>  [
                '_score:desc',
                'book_id:asc',
            ],
            'body'  =>  [
                'query' =>  [
                    'match' =>  [
                        $fieldName  =>  $keyword,
                    ],
                ]
            ],
        ];

        try {
            $response = $client->search($params);

            return $response->asArray();
        } catch (Exception $exception) {
            report($exception);
            return null;
        }
    }
}
