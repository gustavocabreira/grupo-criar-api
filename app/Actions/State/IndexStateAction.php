<?php

namespace App\Actions\State;

use App\Http\Requests\State\IndexStateRequest;
use App\Models\State;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexStateAction
{
    public function handle(IndexStateRequest $request): LengthAwarePaginator
    {
        $states = State::query();

        if ($request->has('includes')) {
            $states->with($request->input('includes'));
        }

        $states
            ->when($request->has('name'), function ($query) use ($request) {
                $query->filterByName($request->input('name'));
            })->when($request->has('acronym'), function ($query) use ($request) {
                $query->filterByAcronym($request->input('acronym'));
            });

        return $states->paginate($request->input('perPage') ?? 10);
    }
}
