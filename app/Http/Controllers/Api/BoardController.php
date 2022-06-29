<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\BoardRequest;
use App\Repositories\BoardRepository;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index(BoardRepository $rBoard)
    {
        try {
            return ApiResponses::okObject($rBoard->all());
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function store(BoardRequest $request, BoardRepository $rBoard)
    {
        try {
            return ApiResponses::okObject($rBoard->create($request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function edit($id, BoardRepository $rBoard)
    {
        try {
            $board = $rBoard->find($id);
            if (!$board) {
                return ApiResponses::notFound();
            }
            return ApiResponses::okObject($board);
        } catch (\Exception $e)
        {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function update($id, BoardRequest $request, BoardRepository $rBoard)
    {
        try {
            return ApiResponses::okObject($rBoard->updateBoard($id, $request));
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }

    public function destroy($id, BoardRepository $rBoard)
    {
        try {
            $rBoard->delete($id);
            return ApiResponses::ok('Tablero eliminado');
        } catch (\Exception $e) {
            return ApiResponses::internalServerError($e->getMessage());
        }
    }
}
