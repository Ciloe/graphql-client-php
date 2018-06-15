<?php

namespace GraphClientPhp\Bridge;

use GraphClientPhp\Exception\BadResponseException;
use GraphClientPhp\Model\ApiModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BasicBridge implements BridgeInterface
{
    /**
     * @var ApiModel
     */
    private $model;

    /**
     * @param ApiModel $model
     */
    public function __construct(ApiModel $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadResponseException|GuzzleException
     */
    public function query(string $name, string $query): \stdClass
    {
        $headers = $this->getHeaders();

        $client = new Client(['base_uri' => $this->model->getHost()]);
        $response = $client->request(
            'POST',
            $this->model->getUri(),
            [
                'body' => $query,
                'headers' => $headers
            ]
        );

        $result = $response->getBody()->getContents();

        $decodedBody = json_decode($result);

        if (isset($decodedBody->errors)) {
            throw new BadResponseException(
                $this->parseResponseErrors($decodedBody->errors)
            );
        }

        return $decodedBody->data;
    }

    /**
     * {@inheritdoc}
     *
     * @throws BadResponseException|GuzzleException
     */
    public function queryAsync(array $queries): array
    {
        $results = [];
        foreach ($queries as $key => $query) {
            $results[$key] = $this->query($key, $query);
        }

        return $results;
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'content-type'  => 'application/json',
            'authorization' => sprintf('Bearer %s', $this->model->getToken()),
        ];
    }

    /**
     * @param array $errors
     *
     * @return string
     */
    protected function parseResponseErrors(array $errors): string
    {
        $errorMessage = 'Wrong call to graphAPI cause:';

        foreach ($errors as $error) {
            $errorMessage .= ' ==> ' . $error->message . '\n';
        }

        return $errorMessage;
    }
}
