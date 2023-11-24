<?php

namespace App\Http\Controllers;

use App\Helpers\ApiCrud;
use App\Models\EarthQuake;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class EarthQuakesController extends ApiCrud
{


    public function getYears(Request $request)
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50)
        ]);
        $this->overrideModelFunctions = [
            'index' => [
                'function' => function (Builder $model) {
                    return $model->selectRaw('YEAR(date) as year')->groupBy('year', 'id')->distinct();
                },
            ],
        ];
        return parent::index($request);
    }


    public function index(Request $request): JsonResponse|LengthAwarePaginator|Collection
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50)
        ]);
        return parent::index($request);
    }

    public function getMonths(Request $request)
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50)
        ]);
        $this->validationRules = [
            'year' => 'required',
        ];
        $this->overrideModelFunctions = [
            'index' => [
                'function' => function (Builder $model) use ($request) {
                    return $model->whereYear('date', $request->input('year'))->selectRaw('MONTH(date) as month')->groupBy('month', 'id')->distinct();
                },
            ],
        ];
        return parent::index($request);
    }

    public function getDays(Request $request)
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50)
        ]);
        $this->validationRules = [
            'year' => 'required',
            'month' => 'required'
        ];
        $this->overrideModelFunctions = [
            'index' => [
                'function' => function (Builder $model) use ($request) {
                    return $model->whereYear('date', $request->input('year'))->whereMonth('date', $request->input('month'));
                },
            ],
        ];
        return parent::index($request);
    }

    protected function getModel(): Model
    {
        return new EarthQuake();
    }

}
