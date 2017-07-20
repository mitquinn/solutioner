<?php

namespace App\Http\Controllers;

use App\Tag;
use Response;
use Auth;
use App\Solution;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Response::json(Auth::user()->currentTeam->solutions()
            ->orderBy('created_at', 'desc')->get()->load('tags'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = array_merge($request->all(), ['team_id' => Auth::user()->currentTeam->id]);
            $solution = Solution::create($data);
            return Response::json($solution->id, 200);
        } catch (QueryException $exception) {
            return Response($exception->errorInfo, 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Solution $solution */
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        /** @var Solution $solution */
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        /** @var Solution $solution */
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

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags($id)
    {
        /** @var Solution $solution */
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }
        return Response::json($solution->tags, 400);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachTag(Request $request, $id)
    {
        /** @var Solution $solution */
        $solution = Auth::user()->currentTeam->solutions()->find($id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution found."], 404);
        }

        /** @var Tag $tag */
        $tag = Auth::user()->currentTeam->tags()->where('name', $request->name)->first();

        if (is_null($tag)) {
            $data = array('name' => $request->name, 'team_id' => Auth::user()->currentTeam->id);
            $tag = Tag::create($data);
        }

        $solution->tags()->attach($tag->id);
        $solution->tags;
        return Response::json($solution, 200);
    }

    /**
     * @param $solution_id
     * @param $tag_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeTag($solution_id, $tag_id)
    {
        /** @var Solution $solution */
        $solution = Auth::user()->currentTeam->solutions()->find($solution_id);
        if (is_null($solution)) {
            return Response::json(['message' => "No solution id is invalid."], 400);
        }

        /** @var Tag $tag */
        $tag = Auth::user()->currentTeam->tags()->find($tag_id);
        if (is_null($tag)) {
            return Response::json(['message' => "No tag id is invalid."], 400);
        }
        $solution->tags()->detach($tag->id);
        $solution->tags;

        //Tag cleanup
        $tag_cleanup = Tag::find($tag_id)->solutions;
        if ($tag_cleanup->isEmpty()) {
            $tag->delete();
        }

        return Response::json($solution, 200);
    }

    /**
     * @param Request $request This ends up being an array of tag ids.
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterByTags(Request $request)
    {
        $tag_ids = $request->all();

        $solutions = array();
        foreach ($tag_ids as $id) {
            $tag = Auth::user()->currentTeam->tags()->find($id);
            foreach ($tag->solutions as $solution) {
                $solution_tags = $solution->tags;
                $solution_tag_ids = array();
                foreach ($solution_tags as $tag) {
                    $solution_tag_ids[] = $tag->id;
                }

                if ($tag_ids == array_intersect($tag_ids, $solution_tag_ids)) {
                    $solutions[] = $solution;
                }
            }
        }

        $temp = array();
        $unique = array_filter($solutions, function ($v) use (&$temp) {
            if (in_array($v['id'], $temp)) {
                return false;
            } else {
                array_push($temp, $v['id']);
                return true;
            }
        });

        return Response::json($unique, 200);
    }

    public function search(Request $request)
    {
        $search = $request->all();
        $search = $search[0];

        $solutions = Solution::where('issue', 'LIKE', '%'.$search.'%')
            ->orWhere('cause', 'LIKE', '%'.$search.'%')
            ->orWhere('solution', 'LIKE', '%'.$search.'%')
            ->get()
            ->load('tags');

        return Response::json($solutions, 200);
    }
}
