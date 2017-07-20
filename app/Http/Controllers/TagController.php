<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Database\QueryException;
use Response;
use App\Solution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class SolutionController
 * @package App\Http\Controllers
 */
class TagController extends Controller
{
    /**
     * SolutionController constructor.
     */
    public function __construct()
    {
        $this->middleware('web');
    }

    public function index()
    {
        return Response::json(Auth::user()->currentTeam->tags()->whereHas('solutions')->get(), 200);
    }

    public function store(Request $request)
    {
        try {
            $data = array_merge($request->all(), ['team_id' => Auth::user()->currentTeam->id]);
            $tag = Tag::create($data);
            return Response::json($tag->id, 200);
        } catch (QueryException $exception) {
            return Response::json($exception->errorInfo, 400);
        }
    }

    public function show($id)
    {
        $tag = Auth::user()->currentTeam->tags()->find($id);
        if (is_null($tag)) {
            return Response::json(['message' => "No tag found."], 404);
        }
        $tag->solutions;
        return Response::json($tag, 200);
    }
}
