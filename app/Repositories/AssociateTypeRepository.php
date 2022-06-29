<?php
namespace App\Repositories;

use App\Models\AssociateType;

class AssociateTypeRepository
{
    protected $mAssociateType;
    public function __construct()
    {
        $this->mAssociateType = new AssociateType();
    }

    public function getAll()
    {
        return $this->mAssociateType->withTrashed()->get();
    }

    public function createAssociateType($request)
    {
        return $this->mAssociateType->create($request->all());
    }

    public function getAssociateType($id)
    {
        return $this->mAssociateType->withTrashed()->find($id);
    }

    public function updateAssociateType($id, $request)
    {
        $associateType = $this->getAssociateType($id);
        if ($associateType) {
            $associateType->name = $request->name;
            $associateType->save();
            return $associateType;
        }
    }

    public function destroyAssociateType($id)
    {
        return $this->mAssociateType->destroy($id);
    }

    public function restoreAssociateType($id)
    {

        return $this->mAssociateType->withTrashed()->find($id)->restore();
    }
}
