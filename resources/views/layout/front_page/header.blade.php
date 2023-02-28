<header class="desktop">
    <section class="header__top">
        <div class="container">
            <ul class="header__top__nav">
                <li class="header__top__logo">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('/img/page/logo.png') }}" alt="">
                    </a>
                </li>
                <li class="header__top__dark_mode dark__btn">
                    <i class="fa-regular fa-lightbulb"></i>
                </li>
                <li class="header__top__dark_mode light__btn">
                    <i class="fa-sharp fa-solid fa-lightbulb"></i>
                </li>
                <li class="header__top__search">
                    <form action="" method="get">
                        <input type="search" name="q" id="input_search_desk" autocomplete="off" placeholder="nhập truyện cần tìm">
                        <button>
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                    <div class="box_search">
                    <ul class="autobox" id="autoBox_desk"></ul>
                    </div>
                </li>
                <li class="header__top__info header__top__info--close">
                    @auth
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle border info" data-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" class="avatar rounded-circle" alt="">
                                <span class="text-lowercase">{{ auth()->user()->name }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('user.info.edit', auth()->id()) }}">Thông tin cá nhân</a>
                                <a class="dropdown-item" href="{{ route('user.index') }}">Truyện của tôi</a>
                                @if(auth()->user()->level_id > 1)
                                    <a class="dropdown-item" href="{{ route('admin.index') }}">Đến trang quản lý</a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item">
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    <button href="{{ route('logout') }}" class="text-danger btn"
                                            style="border: none; background: transparent;">
                                        <i class="fa fa-sign-out"></i>
                                        Đăng xuất
                                    </button>
                                </form>
                                </a>
                            </div>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary text-white top__info__register">Đăng ký</a>
                        <a href="{{ route('login') }}" class="btn btn-primary text-white mx-3 top__info__login">Đăng nhập</a>
                    @endguest
                </li>
            </ul>
        </div>
    </section>
    <section class="header__bottom" id="navDesktop">
        <div class="container">
            <ul class="header__bottom__nav">
                <li><a href="{{ route('index') }}">Trang chủ</a></li>
                <li>
                    <a href="#">Thể loại <i class="fa-solid fa-caret-down"></i></a>
                    <ul class="header__bottom__subnav row">
                        @foreach(categoryList() as $category)
                            <li class="col-md-3"><a href="{{ route('show_categories', $category->slug) }}">{{ $category->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li><a href="{{ route('show_rank') }}">Xếp hạng</a></li>
                <li><a href="{{ route('advanced_search') }}">Tìm truyện</a></li>
                <li><a href="{{ route('show_history') }}">Lịch sử</a></li>
            </ul>
        </div>
    </section>
</header>
<header class="mobile">
    <section class="header__top">
        <div class="container">
            <ul class="header__top__nav">
                <li class="header__top__logo">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('/img/page/icon.png') }}" alt="">
                    </a>
                </li>
                <li class="header__top__dark_mode dark__btn">
                    <i class="fa-regular fa-lightbulb"></i>
                </li>
                <li class="header__top__dark_mode light__btn">
                    <i class="fa-sharp fa-solid fa-lightbulb"></i>
                </li>
                <li class="header__top__info header__top__info--close">
                    @auth
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle border info" data-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" class="avatar rounded-circle" alt="">
                                <span class="text-lowercase">{{ auth()->user()->name }}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('user.info.edit', auth()->id()) }}">Thông tin cá nhân</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('user.index') }}">Truyện của tôi</a>
                                <div class="dropdown-divider"></div>
                                @if(auth()->user()->level_id > 1)
                                    <a class="dropdown-item" href="{{ route('admin.index') }}">Đến trang quản lý</a>
                                @endif
                                <a class="dropdown-item" href="#">
                                    <form action="{{ route('logout') }}" method="post">
                                        @csrf
                                        <button href="{{ route('logout') }}" class="text-danger btn"
                                                style="border: none; background: transparent;">
                                            <i class="fa fa-sign-out"></i>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </a>
                            </div>
                        </div>
                    @endauth
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary text-white btn-sm top__info__register">Đăng ký</a>
                        <a href="{{ route('login') }}" class="btn btn-primary text-white btn-sm mx-2 top__info__login">Đăng nhập</a>
                    @endguest
                </li>
            </ul>
        </div>
    </section>
    <section class="header__middle">
        <div class="header__middle__search">
            <form action="" method="get">
                <input type="search" name="q" id="inputSearchMob" autocomplete="off" placeholder="nhập truyện cần tìm">
                <div class="box_search">
                    <ul class="autobox" id="autoboxMob"></ul>
                </div>
                <button>
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </section>
    <section class="header__bottom" id="navMobile">
        <input type="checkbox" id="showMenuBarCheck">
        <div class="index">
            <a href="{{ route('index') }}">Trang chủ</a>
            <label for="showMenuBarCheck" class="bars nav__btn">
                <i class="fa-solid fa-list"></i>
            </label>
            <label for="showMenuBarCheck" class="close nav__btn">
                <i class="fa-solid fa-rectangle-xmark"></i>
            </label>
        </div>
        <ul class="header__bottom__nav">
            <li>
                <a class="show__subnav__btn">Thể loại
                    <i class="fa-solid fa-caret-down"></i>
                    <i class="fa-solid fa-caret-up"></i>
                </a>
                <ul class="header__bottom__subnav">
                    @foreach(categoryList() as $category)
                        <li>
                            <a href="{{ route('show_categories', $category->slug) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li><a href="{{ route('show_rank') }}">Xếp hạng
                </a>
            </li>
            <li><a href="{{ route('advanced_search') }}">Tìm truyện</a></li>
            <li><a href="{{ route('show_history') }}">Lịch sử</a></li>
        </ul>
    </section>
</header>
