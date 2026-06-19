<?php
namespace App\Http\Controllers;

use App\Http\Requests\ListInfoRequest;
use App\DTOs\ListInfoDTO;
use App\Actions\ListInfoAction;

class ListInfoController extends Controller
{
 
    public function store(ListInfoRequest $request, ListInfoAction $listInfoAction)
    {
        $dto = ListInfoDTO::fromRequest($request);
        try {
        $statResult = $listInfoAction->execute($dto);
        return response()->json([
                'success' => true,
                'data' => $statResult
            ]);
            $listInfoAction->execute($dto);
            return response()->noContent(); //when success
                             
            } catch (\Exception $e) {
            
            return response()->json(['error' => $e->getMessage()], 500); //when error
        }
    }
}   