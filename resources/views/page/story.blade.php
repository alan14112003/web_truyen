@extends('layout.front_page.master')
@section('main')
    @push('css')
        <link rel="stylesheet" href="{{ asset('front_asset/css/star-rating-svg.css') }}">
        <link rel="stylesheet" href="{{ asset('front_asset/css/story.css') }}">
    @endpush
    <div class="row">
        <div class="col-md-7">
            {{ Breadcrumbs::render("show_story", $story) }}
        </div>
    </div>
    <div class="story_info">
        <div class="story_title">
            <h3>Thông tin truyện</h3>
        </div>
        <div class="story_name">
            <h2>{{ $story->name }}</h2>
        </div>
        <div class="story_box">
            <div class="story_box_left">
                <div class="story_box_left_top">
                    <a href="{{ $story->image_url }}" target="_blank">
                        <div class="story_box_left_top_image">
                            <img src="{{ $story->image_url }}" alt="{{ $story->name }}">
                            <div class="book-shadow"></div>
                        </div>
                    </a>
                </div>
                <div class="story_box_left_bottom">
                    <div class="story_box_left_bottom_item">
                        <strong>Phân loại:</strong>
                        <span>{{ $story->level_name }}</span>
                    </div>
                    <div class="story_box_left_bottom_item">
                        <strong>Tác giả:</strong>
                        <a href="{{ route('advanced_search') }}?author={{ $story->author->id }}">{{ $story->author->name }}</a>
                    </div>
                    <div class="story_box_left_bottom_item">
                        <strong>Người đăng:</strong>
                        <a href="{{ route('show_info', $story->user->id) }}">{{ $story->user->name }}</a>
                    </div>
                    <div class="story_box_left_bottom_item">
                        <strong>Thể loại:</strong>
                        <span>{!! $story->categories_link !!}</span>
                    </div>
                    <div class="story_box_left_bottom_item">
                        <strong>Tình trạng:</strong>
                        <span>{{ $story->status_name }}</span>
                    </div>
                    <div class="story_box_left_bottom_item">
                        <strong>Số chương:</strong>
                        <span>{{ $story->chapter->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="story_box_right">
                <div class="story_box_right_rate">
                    <div class="rate_box" data-rating="{{ $starAvg }}"></div>
                    <div class="rate_desc">
                        Đánh giá: <span>{{ $starAvg }}</span>/5 từ <span>{{ $starPerson }}</span> lượt
                    </div>
                </div>
                <div class="story_box_right_desc">
                    {!! $story->descriptions !!}
                </div>
            </div>
        </div>
    </div>
    <div class="chapter_box">
        <div class="story_title chapter_title">
            <h3>Danh sách chương</h3>
            <form action="" method="get" class="form-inline" id="formFilter">
                <label class="control-label" style="text-transform: lowercase; margin-right: 8px;" for="selectFilter">Sắp xếp theo</label>
                <select class="form-control" style="height: auto; background: transparent" name="sort" id="selectFilter">
                    <option @if($sort === 'asc') selected @endif value="asc">cũ nhất</option>
                    <option @if($sort === 'desc') selected @endif value="desc">mới nhất</option>
                </select>
            </form>
        </div>
        <div class="chapter_list">
            @foreach ($chapters as $chapter)
                <div class="chapter_list_item">
                    <a href="{{ route('show_chapter', [$story->slug, $chapter->number]) }}">
                        <span class="chapter_box_item_text">
                            <span>Chương </span> {{ $chapter->number }}: {{ $chapter->name }}
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    <div class="comments_box">
        <div class="fb-comments" data-href="{{ route('show_story', $story->slug) }}"
             data-width="100%" data-numposts="10" data-colorscheme="dark"></div>
    </div>
    @push('js')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="{{ asset('front_asset/js/jquery.star-rating-svg.js') }}"></script>
        <script>
            const formFilter = document.getElementById('formFilter')
            const selectFilter = document.getElementById('selectFilter')
            selectFilter.onchange = () => {
                formFilter.submit();
            }
        </script>
        <script type="text/javascript">
            $(document).ready(function()
            {
                const rateBox = $('.rate_box')
                rateBox.starRating({
                    starSize: 20,
                    useFullStars: true,
                    starShape: 'rounded',
                    ratedColor: '#FE7 !important',
                    callback: function(currentRating){
                        submitRate(currentRating)
                    },
                })

                // khi thực hiện kích vào nút Sign in
                function submitRate(star)
                {
                    let data = {
                        "_token": "{{ csrf_token() }}",
                        "star": star,
                    }
                    //Sử dụng hàm $.ajax()
                    $.ajax({
                        type : 'POST', //kiểu post
                        url  : '{{ route('star.create', $story->id) }}', //gửi dữ liệu sang trang submit.php
                        data : data,
                        success :  function(data)
                        {
                            if($.isEmptyObject(data.error)){
                                alert(data.success);
                            }else{
                                alert(data.error);
                            }
                        }
                    });
                    return false;
                }
            });
        </script>
    @endpush
@endsection
