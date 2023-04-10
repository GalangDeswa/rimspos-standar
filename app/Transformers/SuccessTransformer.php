<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SuccessTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($messages)
    {
        return [
            'success'              => true,
            'message'       => $messages,
            
        ];
    }
}
