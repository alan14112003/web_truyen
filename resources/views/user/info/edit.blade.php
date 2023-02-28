@extends('layout.front_page.master')
@section('main')
@push('css')
<style>
    body {
        background: #F1F3FA;
    }
    .profile-userpic img {
        float: none;
        margin: 0 auto;
        width: 18%;
        -webkit-border-radius: 50% !important;
        -moz-border-radius: 50% !important;
        border-radius: 50% !important;
    }
    
    .profile-usertitle {
        text-align: center;
        margin-top: 20px;
    }
    
    .profile-usertitle-name {
        text-transform: uppercase;
        color: black;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 7px;
    }
    
    .profile-usertitle-gender {
        color: black;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 7px;
    }
    
    .profile-content {
        padding: 20px;
        background: #fff;
        min-height: 460px;
    }
    .text-center{
        text-transform: uppercase;
    }
    .title {
        text-transform: uppercase;
        font-weight: 600;
        margin: 0 12px 24px;
        padding-bottom: 4px;
        border-bottom: 1px solid #aaa;
        width: fit-content;
    }
    .group{
        margin: 50px 0;
    }
    .profile-btn-img{
        margin-top: 20px;
    }
    .profile-userbuttons{
        margin-bottom: 50px;
    }
</style>
    @endpush
    <div class="container">
        <div class="profile-content">
            <div class="container">
                <form action="{{ route("user.info.update", $user->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <div class="row">
                        <div class="col-12">
                            <h1 class="text-center"><b>Thông tin cá nhân</b></h1> <br>
                            <div class="profile-userpic text-center"> 
                                @if (isset($user->avatar))
                                    <img src="{{ $user->avatar}}" class="img-responsive" alt="Thông tin cá nhân">
                                @else
                                    <img src="{{ asset('img/no_face.png') }}" class="img-responsive" alt="Thông tin cá nhân">
                                @endif
                            </div>
                            <input type="hidden" name="image_old" value="{{ $user->avatar}}">
                            <div class="profile-btn-img text-center">
                                <label for="image_new" class="btn btn-success btn-sm">Thay đổi ảnh</label>
                                <input type="file" name="image_new" id="image_new" style="display: none;">
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <div class="form-group col-12">
                            <label for="name">Tên</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $user->name }}">
                            @if ($errors->any())
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-12">
                            <label for="gender">Giới tính</label>
                            <select name="gender" id="gender" class="form-control">
                                @foreach ($gender as $value => $name)
                                    <option value="{{ $value }}"
                                        @if($value === $user->gender)
                                            selected
                                        @endif
                                    >{{ $name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->any())
                                <span class="text-danger">{{ $errors->first('gender') }}</span>
                            @endif
                        </div>
                    <div class="form-group col-12">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ $user->email }}">
                        @if ($errors->any())
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                </div>
                    <div class="profile-userbuttons text-center">
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
                <div class="row">
                    <div class="col-12">
                        <h4 class="title">Truyện đã đăng</h4>
                    </div>
                </div>
                <div class="row">
                    @foreach($approvedStories as $approvedStory)
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6">
                        <div class="item_box">
                            <a href="{{ route('user.stories.show', [$approvedStory->slug, $approvedStory->id]) }}">
                                <div class="box_image">
                                    <img src="{{ $approvedStory->image_url }}" alt="">
                                    <div class="box_view" style="display: flex; justify-content: space-around;">
                                        <span><i class="fa fa-eye"></i> {{ $approvedStory->view_count }}</span>
                                        <span><i class="fa fa-star-o"></i> {{ round($approvedStory->star_avg_total, 1) }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="text_box">
                                <p class="text_box_name text-capitalize">
                                    <a href="{{ route('user.stories.show', [$approvedStory->slug, $approvedStory->id]) }}">
                                        {{ $approvedStory->name }}
                                    </a>
                                </p>
                                <p class="text_box_chapter">
                                    <span class="text_box_chapter_number">
                                        Số chương: {{ $approvedStory->chapter_count }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach 
                </div>
            </div>
        </div>
    </div>
@push('js')
<script>
    const profileUserpic = document.querySelector('.profile-userpic');
    const image_new = document.querySelector('#image_new');

    
</script>
@endpush
    @endsection