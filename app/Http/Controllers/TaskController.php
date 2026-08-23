<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $user = auth('api')->user();

        $task = $user->tasks()->create($validated);

        return response()->json($task, 201);
    }

        return response()->json($task);
    }

    /**
     * Update task.
     */
    public function update(
        Request $request,
        Task $task
    ): JsonResponse {
        $user = auth('api')->user();

        abort_unless(
            $task->user_id === $user->id,
            403
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $task->update($validated);

        return response()->json($task);
    }

        return response()->noContent();
    }
}
