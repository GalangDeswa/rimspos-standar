<?php

namespace App\Http\Controllers\Api;

use League\Fractal\Serializer\ArraySerializer as BaseArraySerializer;
use League\Fractal\Serializer\SerializerAbstract;

class ArraySerializerV2 extends BaseArraySerializer
{
    protected $success,$status_code,$messages;

    public function __construct($success,$status_code,$messages) {
        $this->success = $success;
        $this->status_code = $status_code;
        $this->messages = $messages;
    }

    /**
     * Serialize a collection to a plain array.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        return [
            'success'=> $this->success,
            'status_code'=> $this->status_code,
            'messages'=> $this->messages,
            $resourceKey ?: 'data' => $data,
        ];
    }

    public function item($resourceKey, array $data)
    {
        return [
            'success'=> $this->success,
            'status_code'=> $this->status_code,
            'messages'=> $this->messages,
            $resourceKey ?: 'data' => $data,
        ];
    }
}
