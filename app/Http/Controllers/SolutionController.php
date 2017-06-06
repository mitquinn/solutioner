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
class SolutionController extends Controller
{
    /**
     * SolutionController constructor.
     */
    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Auth::user()->currentTeam->solutions()->orderBy('created_at', 'desc')->get()->load('tags'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = array_merge($request->all(), ['team_id' => Auth::user()->currentTeam->id]);
            $solution = Solution::create($data);
            return Response::json($solution->id, 200);
        } catch (QueryException $exception) {
            return Response::json($exception->errorInfo, 400);
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
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }
        //Get the solution tags as well.
        $solution->tags;
        return Response::json($solution, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }

        $data = $request->all();
        $update = array(
            'issue' => $data['issue'],
            'cause' => $data['cause'],
            'solution' => $data['solution']
        );

        try {
            $solution->update($update);
            return Response::json(["message" => "Solution updated.", "solution_id" => $solution->id], 200);
        } catch (QueryException $exception) {
            return Response::json($exception->errorInfo, 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }

        try {
            $solution->delete();
            return Response::json(["message" => "Solution deleted.", "solution_id" => $solution->id], 200);
        } catch (QueryException $exception) {
            return Response::json($exception->errorInfo, 400);
        }
    }

    public function tags($id)
    {
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }
        return Response::json($solution->tags, 400);
    }

    public function attachTag(Request $request, $id)
    {
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }

        $tag = Auth::user()->currentTeam->tags()->where('name', $request->name)->first();

        if (is_null($tag)) {
            $data = array('name' => $request->name, 'team_id' => Auth::user()->currentTeam->id);
            $tag = Tag::create($data);
        }

        $solution->tags()->attach($tag->id);
        $solution->tags;
        return Response::json($solution, 200);
    }

    public function removeTag($solution_id, $tag_id)
    {
        $solution = Auth::user()->currentTeam->solutions()->find($solution_id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution id is invalid."], 400);
        }

        $tag = Auth::user()->currentTeam->tags()->find($tag_id);
        if (is_null($tag)) {
            return Response::json(['message' => "No tag id is invalid."], 400);
        }

        $solution->tags()->detach($tag->id);
        $solution->tags;
        return Response::json($solution, 200);
    }


}
