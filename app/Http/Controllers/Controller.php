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
     *          name="DIONOU Sinali",
     *          email="admin@admin.com",
     *          x="+226 75 63 82 03",
     *          url="https://dionousinali.com"
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

    /**
     * @OA\Get(
     *      tags={"Classes"},
     *      description="API Endpoints of Projects",
     *      path="/classes",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *     @OA\PathItem (
     *     )
     * )
     */

     public function classes(){

        return null;
     }


}
