@php
    $segment = request()->segment(1);
@endphp
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                        <img alt="image" class="img-circle"
                            src="{{ isset(Auth::user()->image) ? \Storage::url(Auth::user()->image) : 'https://t4.ftcdn.net/jpg/02/29/75/83/360_F_229758328_7x8jwCwjtBMmC6rgFzLFhZoEpLobB6L8.jpg' }}"
                            width="50" />
                    </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="clear"> <span class="block m-t-xs"> <strong
                                    class="font-bold">{{ Auth::user()->name }}</strong>
                            </span> <span class="text-muted text-xs block">Cài đặt <b class="caret"></b></span>
                        </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile.html">Profile</a></li>
                        <li><a href="contacts.html">Contacts</a></li>
                        <li><a href="mailbox.html">Mailbox</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('auth.logout') }}">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    TE+
                </div>
            </li>
            @foreach (__('sidebar.module') as $item => $value)
                <li class="{{ (isset($value['class']) ? $value['class'] : '') }} {{ in_array($segment, $value['name']) ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard.index') }}"><i class="{{ $value['icon'] }}"></i><span
                            class="nav-label">{{ $value['title'] }}</span> <span class="fa arrow"></span></a>
                    @if (isset($value['subModule']))
                        <ul class="nav nav-second-level">
                            @foreach ($value['subModule'] as $item => $val)
                                <li><a href="{{ $val['route'] }}">{{ $val['title'] }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</nav>
