<?php

namespace Model;

use GraphQLClientPhp\Model\ApiModel;
use PHPUnit\Framework\TestCase;

class ApiModelTest extends TestCase
{
    public function testModel()
    {
        $model = new ApiModel('host', 'uri', 'token');

        $this->assertSame(['host', 'uri', 'token'], [$model->getHost(), $model->getUri(), $model->getToken()]);

        $model->setHost('host2')
            ->setUri('uri2')
            ->setToken('token2');

        $this->assertSame(['host2', 'uri2', 'token2'], [$model->getHost(), $model->getUri(), $model->getToken()]);
    }
}
