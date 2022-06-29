<?php


namespace App\Repositories;


use App\Models\Board;

class BoardRepository
{
    protected $mBoard;

    public function __construct()
    {
        $this->mBoard = new Board();
    }

    public function all()
    {
        return $this->mBoard
            ->with('area')
            ->with('subarea')
            ->get();
    }

    public function create($request)
    {
        return $this->mBoard->create($request->all());
    }

    public function find($id)
    {
        return $this->mBoard->find($id);
    }

    public function updateBoard($id, $request)
    {
        $board = $this->find($id);
        $board->update($request->all());
        return $board;
    }

    public function delete($id)
    {
        $board = $this->find($id);
        $board->delete();
    }
}
