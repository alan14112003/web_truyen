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
</style>
    @endpush
    <div class="container">
        <div class="profile-content">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center"><b>Thông tin của {{ $story->user->name }}</b></h1> <br>
                        <div class="profile-userpic text-center"> 
                            @if (isset($story->user->avatar))
                                <img src="{{ $story->user->avatar }}" class="img-responsive" alt="Thông tin cá nhân">
                            @else
                                <img src="{{ asset('img/no_face.png') }}" class="img-responsive" alt="Thông tin cá nhân">
                            @endif
                        </div>
                        <div class="profile-usertitle">
                            <div class="profile-usertitle-name"> Tên người đăng: {{ $story->user->name }} </div>
                            <div class="profile-usertitle-gender"> Giới tính: {{ $story->user->gender_name }}</div>
                        </div>
                    </div>
                </div>
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

    @endsection