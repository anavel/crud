<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header">{{ $headerName }}</li>
            @foreach ($menuItems as $group)
                @if(count($group['items'])==1)
                    <li{!! $group['items'][0]['isActive'] ? ' class="active"' : '' !!}>
                        <a href="{{ $group['items'][0]['route'] }}"><span>{{ $group['items'][0]['name'] }}</span></a>
                    </li>
                @else
                    <li class="treeview {!! isset($group['isActive']) && $group['isActive']==true?'active':'' !!}">
                        <a href="#">
                            <span>{!! $group['name'] !!}</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            @foreach ($group['items'] as $item)
                                <li{!! $item['isActive'] ? ' class="active"' : '' !!}>
                                    <a href="{{ $item['route'] }}"><span>{{ $item['name'] }}</span></a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach
        </ul>
    </section>
</aside>