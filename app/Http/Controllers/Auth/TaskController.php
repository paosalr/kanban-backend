<?php

namespace App\Http\Controllers\Auth;

use App\Models\StatusType;
use App\Models\TagType;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class TaskController extends Controller
{
    public function index(Request $request)
    {

        $userId = $request->user()->id;

        $tasks = Task::whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }
    public function show($id){

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'La tarea no existe'
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function store(Request $request){

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expiration_date' => 'required|date|after_or_equal:today',
            'tag_id' => 'required|exists:tag_types,id',
            'status_id' => 'required|exists:status_types,id',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try{
            DB::beginTransaction();

            $task = Task::create($data);
            $task->users()->attach($data['user_ids'], ['created_at' => now(), 'updated_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La tarea se ha creado exitosamente',
                'data' => $task->load(['tag', 'status', 'users'])
            ], 201);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ha habido un error: ' . $e->getMessage()
            ]);
        }
    }
    public function update(Request $request, $id){

        $task = Task::find($id);

        if(!$task){
            return response()->json([
                'success' => false,
                'message' => 'La tarea no existe'
            ]);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'expiration_date' => 'sometimes|date',
            'tag_id' => 'sometimes|exists:tag_types,id',
            'status_id' => 'sometimes|exists:status_types,id'
        ]);

        try{
            DB::beginTransaction();

            $task->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La tarea se ha actualizado exitosamente.',
                'data' => $task
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ha habido un error: ' . $e->getMessage()
            ]);
        }
    }
        public function destroy($id){

            $task = Task::find($id);

            if (!$task) {
            return response()->json([
            'success' => false,
            'message' => 'La tarea no existe'
            ]);
        }

            try{
                DB::beginTransaction();

                $task->users()->detach();
                $task->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'La tarea se ha eliminado exitosamente'
                ]);
            }catch(\Exception $e){
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Ha habido un error: ' . $e->getMessage()
                ]);
            }
        }
        public function toggleComplete($id)
        {
            $task = Task::find($id);

            if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'La tarea no existe'
            ]);
        }
            $nextStatus = $this->getNextStatus($task->status_id);

            try {
            DB::beginTransaction();
            $task->status_id = $nextStatus;
            $task->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La tarea ha sido movida correctamente',
                'data' => $task
                ]);
            } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Ha habido un error: ' . $e->getMessage()
            ]);
        }
    }
    private function getNextStatus($currentStatusId){

        $nextStatuses = [
            1 => 2,
            2 => 3,
            3 => 1
        ];

        return $nextStatuses[$currentStatusId] ?? 1;
    }

    public function getTagTypes(){

        $tagTypes = TagType::all();

        return response()->json([
            'success' => true,
            'data' => $tagTypes
        ]);
    }

    public function getStatusTypes(){

        $statusTypes = StatusType::all();

        return response()->json([
            'success' => true,
            'data' => $statusTypes
        ]);
    }
}
