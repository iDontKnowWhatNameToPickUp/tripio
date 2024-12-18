<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReadyRoutesRequest;
use App\Http\Resources\ReadyRouteResource;
use App\Models\Ready_route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *    schema="ReadyRoutesSchema",
 *    @OA\Property(
 *        property="id",
 *        type="integer",
 *        description="ReadyRoute ID",
 *        nullable=false,
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="city_id",
 *        type="integer",
 *        description="Foreign key city",
 *        nullable=false,
 *        example="1"
 *    ),
 *    @OA\Property(
 *        property="name",
 *        type="string",
 *        description="Route Name",
 *        nullable=false,
 *        example="Музеи в Казане"
 *    ),
 *    @OA\Property(
 *        property="description",
 *        type="string",
 *        description="Описание маршрута",
 *        nullable=false,
 *        example="Посетим по Казани около 10 музеев"
 *    ),
 *     @OA\Property(
 *        property="duration",
 *        type="string",
 *        description="Продолжительность маршрута",
 *        nullable=false,
 *        example="10 часов"
 *    ),
 *     @OA\Property(
 *        property="photo",
 *        type="text",
 *        description="Путь до файла",
 *        nullable=false,
 *        example="file/file/name.png"
 *    ),
 *    @OA\Property(
 *        property="created_at",
 *        type="string",
 *        description="Date of route creation",
 *        nullable=false,
 *        format="date-time"
 *    ),
 *    @OA\Property(
 *        property="updated_at",
 *        type="string",
 *        description="Date of last updating route data",
 *        nullable=false,
 *        format="date-time"
 *    ),
 * )
 * 
 * 
 * 
 * @OA\Get(
 *     path="/api/routes",
 *     tags={"ReadyRoutes"},
 *     summary="Вывод списка записей (index())",
 *     description="Доступ только авторизованным пользователям",
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Everything OK",
*         @OA\JsonContent(
*               ref="#/components/schemas/ReadyRoutesSchema"
*            )
*     ),
*       @OA\Response(
*          response=401,
*          description="Unauthenticated",
*      ),     
* )
*
* @OA\Post(
*      path="/api/routes",
*      tags={"ReadyRoutes"},
*      summary="Добавление новой записи (store())",
*      description="Возвращает новый созданный route",
*      @OA\Response(
*          response=201,
*          description="Successful operation",
*          @OA\JsonContent(ref="#/components/schemas/ReadyRoutesSchema")
*       ),
*      @OA\Response(
*          response=400,
*          description="Bad Request"
*      ),
*      @OA\Response(
*          response=401,
*          description="Unauthenticated",
*      ),
*      @OA\Response(
*          response=403,
*          description="Forbidden"
*      )
* )

* @OA\Get(
*      path="/api/routes/{id}",
*      tags={"ReadyRoutes"},
*      summary="Вывод конкретной записи (show())",
*      description="Returns route data",
*      @OA\Parameter(
*          name="id",
*          description="Route id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\JsonContent(ref="#/components/schemas/ReadyRoutesSchema")
*       ),
*      @OA\Response(
*          response=400,
*          description="Bad Request"
*      ),
*      @OA\Response(
*          response=401,
*          description="Unauthenticated",
*      ),
*      @OA\Response(
*          response=403,
*          description="Forbidden"
*      )
* )
*
* @OA\Put(
*      path="/api/routes/{id}",
*      tags={"ReadyRoutes"},
*      summary="Обновление записи (update())",
*      description="Returns updated route data",
*      @OA\Parameter(
*          name="id",
*          description="Route id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\RequestBody(
*          required=true,
*          @OA\JsonContent(ref="#/components/schemas/ReadyRoutesSchema")
*      ),
*      @OA\Response(
*          response=200,
*          description="Successful operation",
*          @OA\JsonContent(ref="#/components/schemas/ReadyRoutesSchema")
*       ),
*      @OA\Response(
*          response=400,
*          description="Bad Request"
*      ),
*      @OA\Response(
*          response=401,
*          description="Unauthenticated",
*      ),
*      @OA\Response(
*          response=403,
*          description="Forbidden"
*      ),
*      @OA\Response(
*          response=404,
*          description="Resource Not Found"
*      )
* )

* @OA\Delete(
*      path="/api/routes/{id}",
*      tags={"ReadyRoutes"},
*      summary="Удаление записи (delete())",
*      description="Deletes a record and returns no content",
*      @OA\Parameter(
*          name="id",
*          description="Route id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=204,
*          description="Successful operation",
*          @OA\JsonContent()
*       ),
*      @OA\Response(
*          response=401,
*          description="Unauthenticated",
*      ),
*      @OA\Response(
*          response=403,
*          description="Forbidden"
*      ),
*      @OA\Response(
*          response=404,
*          description="Resource Not Found"
*      )
* )
*/
class ReadyRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ReadyRouteResource::collection(Ready_route::with('cities')->orderBy('id', 'desc')->paginate(5));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReadyRoutesRequest $request)
    {
        try {
            $image = Str::random(32).".".$request->photo->getClientOriginalExtension();

            #create 
            $data = Ready_route::create([
                'city_id' => $request->city_id,
                'name' => $request->name,
                'description' => $request->description,
                'duration' => $request->duration,
                'photo' => $image,
            ]);

            #сохранение изображения
            Storage::disk('public')->put($image, file_get_contents($request->photo));
            
            return response()->json([
                'message' => 'Created',
                'data' => $data
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ReadyRouteResource(Ready_route::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReadyRoutesRequest $request, $id)
    {
        try {
            if($request->photo) {
                $image = Str::random(32).".".$request->photo->getClientOriginalExtension();

                #update 
                $route = Ready_route::find($id);
                $route->update([
                    'city_id' => $request->city_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'duration' => $request->duration,
                    'photo' => $image,
                ]);

                #сохранение изображения
                Storage::disk('public')->put($image, file_get_contents($request->photo));
            } else {
                #update 
                $route = Ready_route::find($id);
                $route->update([
                    'city_id' => $request->city_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'duration' => $request->duration
                ]);
            }
            
            return response()->json([
                'message' => 'Updated',
                'data' => $route
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ready_route $route)
    {
        $route->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
