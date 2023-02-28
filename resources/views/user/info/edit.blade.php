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
<style>
    .item_box {
        font-size: 17px;
        overflow: hidden;
        text-overflow: ellipsis;
        height: 360px;
        line-height: 22px;
        margin: 3px 0;
    }

    .item_box a {
        text-decoration: none;
        color: var(--color-mode);
    }

    .item_box a:hover {
        color: #288ad6;
    }

    .dark .item_box a:hover {
        color: red;
    }
    .item_box .box_image {
        border: 1px solid #ddd;
        height: 68%;
        width: 100%;
        position: relative;
    }
    .item_box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .box_view {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        line-height: 30px;
        padding: 5px 12px;
        font-size: 16px;
        display: block;
        color: #fff;
        background-color: #000;
        opacity: .65;
    }

    .text_box {
        height: 23%;
    }

    .text_box_name {
        font-weight: 550;
        width: 100%;
        max-height: 45px;
        margin-top: 10px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .text_box_chapter_number {
        float: left;
        font-size: 14px;
    }
</style>
@endpush
    <div class="container">
        <div class="profile-content">
            <div class="container">
                @if (session()->has('success'))
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success alert-dismissible fade-in">
                                <a href="#" class="close" data-dismiss="alert" style="right: 0"
                                   aria-label="close">&times;</a>
                                <strong>Thành công!</strong> {{ session()->get('success') }}
                                @php
                                    session()->forget('success');
                                @endphp
                            </div>
                        </div>
                    </div>
                @endif
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
                                    <img src="{{ $user->avatar_url}}" class="img-responsive" alt="Thông tin cá nhân">
                                @else
                                    <img src="{{ asset('img/no_face.png') }}" class="img-responsive" alt="Thông tin cá nhân">
                                @endif
                            </div>
                            <input type="hidden" name="image_old" id="image_old" value="{{ $user->avatar}}">
                            <div class="profile-btn-img text-center">
                                <label for="image_new" class="btn btn-success btn-sm" style="margin: 0">Thay đổi ảnh</label>
                                <input type="file" name="image_new" id="image_new" style="display: none;">
                                <button class="btn btn-danger btn_remove btn-sm" type="button" >Xóa ảnh</button>
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
                                    >
                                        {{ $name }}
                                    </option>
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
                        <button type="reset" class="btn btn-danger">Bỏ</button>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function()
    {
        const profileUserPic = document.querySelector('.profile-userpic')
        const btnRemove = document.querySelector('.btn_remove')

        function changeImage(file = null) {
            let url = '{{ asset('img/no_face.png') }}';
            if (file !== null) {
                url = URL.createObjectURL(file)
            }
            profileUserPic.innerHTML = `<img src="${url}" class="img-responsive">`
        }

        btnRemove.onclick = () => {
            changeImage()
            submitRemoveAvatar()
        }

        function submitRemoveAvatar()
        {
            let data = {
                "_token": "{{ csrf_token() }}",
                "_method": "delete"
            }

            //Sử dụng hàm $.ajax()
            $.ajax({
                type : 'POST', //kiểu post
                url  : '{{ route('user.info.delete_avatar') }}', //gửi dữ liệu sang trang submit.php
                data : data,
                success :  function(data)
                {
                    alert(data.success)
                }
            });
            return false;
        }

    });
</script>
@endpush
@endsection
