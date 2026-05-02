<?php

namespace App\Http\Controllers;

use App\Models\CV;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CVController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth('api')->user();

        $cvs = CV::where('user_id', $user->id)
            ->with('latestAnalysis')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'CVs retrieved successfully',
            'data'    => ['cvs' => $cvs],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $cv = CV::where('id', $id)->where('user_id', $user->id)->with('latestAnalysis')->first();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'CV not found',
                'data'    => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'CV retrieved successfully',
            'data'    => ['cv' => $cv],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'                          => 'required|string|max:255',
            'full_name'                      => 'nullable|string|max:255',
            'email'                          => 'nullable|email|max:255',
            'phone'                          => 'nullable|string|max:50',
            'summary'                        => 'nullable|string|max:2000',
            'experience'                     => 'nullable|array',
            'experience.*.title'             => 'required_with:experience|string',
            'experience.*.company'           => 'required_with:experience|string',
            'experience.*.start_date'        => 'nullable|string',
            'experience.*.end_date'          => 'nullable|string',
            'experience.*.description'       => 'nullable',
            'education'                      => 'nullable|array',
            'education.*.degree'             => 'required_with:education|string',
            'education.*.institution'        => 'required_with:education|string',
            'education.*.year'               => 'nullable|string',
            'skills'                         => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data'    => ['errors' => $validator->errors()],
            ], 422);
        }

        $user = auth('api')->user();

        $cv = CV::create([
            'user_id'    => $user->id,
            'title'      => $request->title,
            'full_name'  => $request->full_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'summary'    => $request->summary,
            'experience' => $request->experience,
            'education'  => $request->education,
            'skills'     => $request->skills,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CV created successfully',
            'data'    => ['cv' => $cv],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = auth('api')->user();
        $cv = CV::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'CV not found',
                'data'    => [],
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title'      => 'sometimes|required|string|max:255',
            'full_name'  => 'nullable|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'summary'    => 'nullable|string|max:2000',
            'experience' => 'nullable|array',
            'education'  => 'nullable|array',
            'skills'     => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data'    => ['errors' => $validator->errors()],
            ], 422);
        }

        $cv->update($request->only([
            'title', 'full_name', 'email', 'phone',
            'summary', 'experience', 'education', 'skills',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'CV updated successfully',
            'data'    => ['cv' => $cv->fresh('latestAnalysis')],
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = auth('api')->user();
        $cv = CV::where('id', $id)->where('user_id', $user->id)->first();

        if (!$cv) {
            return response()->json([
                'success' => false,
                'message' => 'CV not found',
                'data'    => [],
            ], 404);
        }

        $cv->delete();

        return response()->json([
            'success' => true,
            'message' => 'CV deleted successfully',
            'data'    => [],
        ]);
    }
}
