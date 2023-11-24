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
use Illuminate\Support\Arr;

class EarthQuakesController extends ApiCrud
{


    public function getYears(Request $request)
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50),
            self::JUST_PAGINATOR_KEY => true,
        ]);
        $this->overrideModelFunctions = [
            'index' => [
                'function' => function (Builder $model) {
                    return $model->selectRaw('YEAR(date) as year')->groupBy('year', 'id')->distinct();
                },
            ],
        ];
        $pagination = parent::index($request);
        return $this->addParamsToPagination($pagination);
    }


    public function index(Request $request): JsonResponse|LengthAwarePaginator|Collection
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50)
        ]);
        return parent::index($request);
    }

    protected function addParamsToPagination(Collection|LengthAwarePaginator|JsonResponse $pagination): LengthAwarePaginator
    {
        return $pagination->appends(Arr::only(request()->query(), array_keys($this->validationRules)));
    }

    public function getMonths(Request $request)
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50),
            self::JUST_PAGINATOR_KEY => true,
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
        $pagination = parent::index($request);
        return $this->addParamsToPagination($pagination);

    }

    public function getDays(Request $request)
    {
        $request->merge([
            'paginate' => true,
            'per_page' => $request->input('per_page', 50),
            self::JUST_PAGINATOR_KEY => true,
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
        $pagination = parent::index($request);
        return $this->addParamsToPagination($pagination);
    }

    protected function getModel(): Model
    {
        return new EarthQuake();
    }

}
