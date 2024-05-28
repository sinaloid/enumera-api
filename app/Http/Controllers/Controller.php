<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

 /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="ENUMERA API",
     *      description="L5 Swagger OpenApi description",
     *      @OA\Contact(
     *          name="ENUMERA",
     *          email="enumera@gmail.com",
     *          url="https://enumera.tech"
     *      ),
     *      @OA\License(
     *          name="Apache 2.0",
     *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *      )
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="Demo API Server"
     * ),
     *
    * @OA\SecurityScheme(
    *   type="http",
    *   securityScheme="bearerAuth",
    *   scheme="bearer",
    *   bearerFormat="JWT"
    * )
     *
     */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @OA\Get(
     *      tags={"Users"},
     *      description="API Endpoints of utilisateur",
     *      path="/users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     @OA\PathItem (
     *     )
     * )
     */

     public function utilisateurs(){

        return null;
     }



     public function classes(){

        return null;
     }


}
