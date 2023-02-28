<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Level\StoreRequest;
use App\Http\Requests\Level\UpdateRequest;
use App\Models\Level;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class LevelController extends Controller
{
    private Builder $model;
    private string $table;
    private string $title;

    public function __construct()
    {
        $this->model = Level::query();
        $this->table = (new Level())->getTable();

        View::share('table', $this->table);
    }

    public function index(Request $request)
    {
        $q = $request->get('q');
        $query = $this->model->where('name', 'like', "%$q%")
            ->withCount('user');
        $data = $query->paginate();

        $this->title = 'Quản lý cấp bậc';
        View::share('title', $this->title);

        return view("admin.$this->table.index", [
            'data' => $data,
            'q' => $q,
        ]);
    }

    public function create()
    {
        $this->title = 'Thêm cấp bậc';
        View::share('title', $this->title);
        return view("admin.$this->table.create");
    }

    public function store(StoreRequest $request)
    {
        $this->model->create($request->validated());
        return redirect()->route("admin.$this->table.index")
            ->with('success', 'Đã thêm thành công');
    }

    public function edit($id)
    {
        $level = $this->model->find($id);
        $this->title = 'Sửa cấp bậc';
        View::share('title', $this->title);

        return view("admin.$this->table.edit", [
            'level' => $level,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $this->model->where('id', $id)->update($request->validated());
        return redirect()->route("admin.$this->table.index")
            ->with('success', 'Đã sửa thành công');
    }

    public function destroy($id)
    {
        Level::destroy($id);
        return redirect()->route("admin.$this->table.index")
            ->with('success', 'Đã xóa thành công');
    }
}
