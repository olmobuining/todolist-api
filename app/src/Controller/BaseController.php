<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends AbstractFOSRestController
{

    public function response($data, array $groups = ['Default'], string $format = 'json')
    {
        $payload = [
            'data' => $data,
        ];

        return Response::create($this->serialize($payload, $groups, $format), 200, [
            'Content-type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    private function serialize($data, array $groups = ['Default'], string $format = 'json')
    {
        $serializer = SerializerBuilder::create()
            ->addDefaultHandlers()
            ->build();

        $context = SerializationContext::create();
        $context->setGroups($groups);

        return $serializer->serialize($data, $format, $context);
    }

    public function badRequest($errors)
    {
        $data = [
            'errors' => $errors,
        ];

        return Response::create($this->serialize($data, ['Default'], 'json'), 400, [
            'Content-type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }


    public function notFound($message)
    {
        $data = [
            'errors' => $message,
        ];

        return Response::create($this->serialize($data, ['Default'], 'json'), 404, [
            'Content-type' => 'application/json',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
