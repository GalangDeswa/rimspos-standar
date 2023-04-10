<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ErorrTransformer extends TransformerAbstract
{

    protected $status_code;

    public function __construct($status_code) {
        $this->status_code = $status_code;
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($messages)
    {
        return [
            "success" => false,
            'status_code'              => $this->status_code,
            'message'       => $messages,
            
        ];
    }
}
