<?php

namespace App\Http\Controllers;

use App\Services\RequestDraftService;
use Illuminate\Http\Request;

class RequestDraftController extends Controller
{
    public function __invoke(Request $request, RequestDraftService $service)
    {
        abort_unless($request->user()->isWorker(), 403);

        $data = $request->validate(['text' => ['required', 'string', 'min:10', 'max:800']]);

        return response()->json($service->draft($data['text']));
    }
}
