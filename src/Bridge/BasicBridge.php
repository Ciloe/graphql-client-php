<?php

namespace GraphClientPhp\Bridge;

use GraphClientPhp\Exception\BadResponseException;
use GraphClientPhp\Model\ApiModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

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
                'headers' => $headers,
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
     */
    public function queryAsync(array $queries): array
    {
        $promises = [];
        $payloads = [];

        $client = new Client(['base_uri' => $this->model->getHost()]);
        foreach ($queries as $key => $query) {
            $guzzleRequest = new Request(
                'POST',
                $this->model->getUri(),
                $this->getHeaders(),
                $query
            );
            $promise = $client->sendAsync($guzzleRequest);
            $promise->then(
                function (ResponseInterface $res) use (&$payloads, $key) {
                    $payload = json_decode($res->getBody()->getContents());
                    $payloads[$key] = $payload;
                },
                function (RequestException $e) use (&$payloads, $key) {
                    $payloads[$key] = [
                        ['message' => $this->parseResponseErrors([$e->getMessage()])],
                    ];
                }
            );
            $promises[$key] = $promise;
        }

        \GuzzleHttp\Promise\settle($promises)->wait();

        return $payloads;
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return [
            'content-type' => 'application/json',
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
