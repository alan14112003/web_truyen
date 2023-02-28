@extends('layout.front_page.master')
@section('main')
    @push('css')
        <link rel="stylesheet" href="{{ asset('front_asset/css/own/index.css') }}">
        <style>
            body.dark #form-filter {
                color: var(--color-dark);
            }
        </style>
    @endpush
    <div class="row">
        <div class="col-md-12">
            {{ Breadcrumbs::render("advanced_search") }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="" method="get" id="form-filter">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="text-center my-4">Tìm truyện nâng cao</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-1 form-group">
                                <label class="control-label">Thể loại</label>
                            </div>
                            <div class="col-md-11">
                                <div class="row">
                                    @foreach ($categories as $category)
                                        <div class="col-sm-2 col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                       @if (isset($categoriesFilter) && in_array($category->id, $categoriesFilter)) checked @endif
                                                       id="categories{{ $category->id }}" name="categories[]"
                                                       type="checkbox" value="{{ $category->id }}"
                                                       class="custom-control-input"
                                                >
                                                <label for="categories{{ $category->id }}"
                                                        class="custom-control-label"
                                                >
                                                    {{ $category->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4" >
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status" class="control-label">Tình trạng</label>
                            <select name="status" id="status" class="form-control filter-input"
                                    style="margin-left: 6px">
                                <option value="All">Tất cả</option>
                                @foreach ($status as $value => $name)
                                    <option value="{{ $value }}"
                                            @if ($statusFilter === (string) $value) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="level" class="control-label">Phân loại</label>
                            <select name="level" id="level" class="form-control filter-input"
                                    style="margin-left: 6px">
                                <option value="All">Tất cả</option>
                                @foreach ($level as $value => $name)
                                    <option value="{{ $value }}"
                                            @if ($levelFilter === (string) $value) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="author" class="control-label">Tác giả</label>
                            <select name="author" id="author" class="form-control filter-input"
                                    style="margin-left: 6px">
                                <option value="All">Tất cả</option>
                                @foreach ($authors as $author)
                                    <option value="{{ $author->id }}"
                                            @if ($authorFilter === (string) $author->id) selected @endif>{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="{{ route("advanced_search") }}" class="btn btn-sm btn-primary"
                       style="margin: 24px">
                        Bỏ chọn
                    </a>
                    <button class="btn btn-sm btn-success">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        @if(empty($data->items()))
            <h2 class="text-danger">Không có truyện nào phù hợp</h2>
        @else
        @foreach($data as $story)
            <div class="col-lg-2 col-sm-3 col-6">
                <x-story :story="$story"/>
            </div>
        @endforeach
        @endif
    </div>
@endsection
