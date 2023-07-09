<?php



namespace App\Transformers;



use App\Http\Models\Beban;



use League\Fractal\TransformerAbstract;



use DB;



class UserTransformer extends TransformerAbstract

{

    /**

     * A Fractal transformer.

     *

     * @return array

     */

    public function transform($dtl)

    {

        

       

        $respon["id"] = $dtl->id;

        $respon["id_toko"] = $dtl->id_toko;

        $respon["nama"] = $dtl->name;

        $respon["email"] = $dtl->email;

        $respon["hp"] = $dtl->hp;

        $respon["role"] = $dtl->role;

        $respon["status"] = $dtl->status;

        $respon["password"] = $dtl->password;



        return $respon;

    }

    

}
