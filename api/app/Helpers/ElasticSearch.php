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
                $client->indices()->create($params);
            }

            return $client;
        } catch (Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Indexing a data
     *
     * @param string $data
     * @param string $id
     * @param string $fieldName
     * @return void
     */
    public static function index(string $data, string $id, string $fieldName = ''): void
    {
        if (empty($data)) {
            return;
        }

        $client = self::getClient();

        if (empty($client)) {
            return;
        }

        if (empty($fieldName)) {
            $fieldName = config('services.elasticSearch.default_field');
        }

        $params = [
            'index' =>  config('services.elasticSearch.default_index'),
            'id'    =>  $id,
            'body'  =>  [
                $fieldName =>   $data,
            ],
        ];

        try {
            $client->index($params);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    /**
     * Indexing multiple data
     *
     * @param array $data
     * @param string $fieldName
     * @return void
     */
    public static function bulkIndex(array $data, string $fieldName = ''): void
    {
        if (empty($data)) {
            return;
        }

        $client = self::getClient();

        if (empty($client)) {
            return;
        }

        if (empty($fieldName)) {
            $fieldName = config('services.elasticSearch.default_field');
        }

        $indexName = config('services.elasticSearch.default_index');

        $params = ['body' => []];

        try {
            $totalItem = count($data);

            for ($i = 0; $i < $totalItem; $i++) {
                $params['body'][] = [
                    'index' =>  [
                        '_index'    =>  $indexName,
                        '_id'       =>  $data[$i]['id'],
                    ],
                ];

                $params['body'][] = [
                    $fieldName =>   $data[$i]['text'],
                ];

                if ($i % 1000 === 0) {
                    $client->bulk($params);
                    $params = ['body' => []];
                }
            }

            // Send the last batch if it exists
            if (!empty($params['body'])) {
                $client->bulk($params);
            }
        } catch (Exception $exception) {
            report($exception);
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
