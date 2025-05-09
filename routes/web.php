<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Models\Task;


Route::get('/', function(){
    return redirect()->route('tasks.index');
});

Route::view('/tasks/create', 'create')->name('tasks.create');

Route::get('/tasks', function () {
    return view('index', ['tasks' => App\Models\Task::latest()->get()]);
})->name('tasks.index');

Route::get('/tasks/{id}', function ($id) {
        return view('show', ['task' => App\Models\Task::findOrFail($id)]);
})->name('tasks.show');

Route::get('/tasks/{id}/edit', function ($id) {
    return view('edit', ['task' => App\Models\Task::findOrFail($id)]);
})->name('tasks.edit');

Route::fallback(function(){
    return "Still got somewhere";
});

Route::post('/tasks', function(Request $request){
    $task = createOrUpdate(null, $request);
    return redirect()->route('tasks.show', ['id' => $task->id])
        ->with('success', 'Task was created successfully!');
})->name('tasks.store');

Route::put('/tasks/{id}', function($id, Request $request){
    $task = createOrUpdate($id, $request);
    return redirect()->route('tasks.show', ['id' => $task->id])
        ->with('success', 'Task was updated successfully!');
})->name('tasks.update');


function createOrUpdate ($id, Request $request) {
    $data = $request->validate([
        'title' => 'required|max:255',
        'description' => 'required',
        'long_description' => 'required'
    ]);

    $task = $id ? Task::findOrFail($id) : new Task;
    $task->title = $data['title'];
    $task->description = $data['description'];
    $task->long_description = $data['long_description'];

    $task->save();

    return $task;
}
