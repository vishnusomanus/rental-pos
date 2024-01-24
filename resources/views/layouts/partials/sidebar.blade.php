<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('home')}}" class="brand-link">
        <img src="{{ asset('images/logo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
             style="opacity: .8">
        <span class="brand-text font-weight-light">{{ config('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
       

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{route('home')}}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('dashboard.title') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('products.index') }}" class="nav-link {{ activeSegment('products') }}">
                        <i class="nav-icon fas fa-cart-shopping"></i>   
                        <p>{{ __('product.title') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('customers.index') }}" class="nav-link {{ activeSegment('customers') }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('customer.title') }}</p>
                    </a>
                </li>
                @if(auth()->user()->role != 'super_admin')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('cart.index') }}" class="nav-link {{ activeSegment('cart') }}">
                            <i class="nav-icon fas fa-store"></i>
                            <p>Booking Order</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item has-treeview">
                    <a href="{{ route('orders.index') }}" class="nav-link {{ activeSegment('orders') }}">
                        <i class="nav-icon fas fa-bars-staggered"></i>
                        <p>{{ __('order.title') }}</p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('orders.pending') }}" class="nav-link {{ activeSegment('pending') }}">
                        <i class="nav-icon fas fa-clock-rotate-left"></i>
                        <p>Pending Orders</p>
                    </a>
                </li>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('users.index') }}" class="nav-link {{ activeSegment('users') }}">
                            <i class="nav-icon fas fa-users-gear"></i>
                            <p>Users</p>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->role === 'admin')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ activeSegment('settings') }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>{{ __('settings.title') }}</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ activeSegmentOpen('import') }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-import"></i>
                            <p>
                                Imports
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('import.products.index') }}" class="nav-link {{ activeSegment3('products') }}">
                                    <i class="nav-icon fas fa-cart-plus"></i>
                                    <p>Products</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('import.customers.index') }}" class="nav-link {{ activeSegment3('customers') }}">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Customers</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(auth()->user()->role === 'super_admin')
                    <li class="nav-item has-treeview">
                        <a href="{{ route('white-labels.index') }}" class="nav-link {{ activeSegment('white-labels') }}">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>White Labels</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="document.getElementById('logout-form').submit()">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>{{ __('common.Logout') }}</p>
                        <form action="{{route('logout')}}" method="POST" id="logout-form">
                            @csrf
                        </form>
                    </a>
                </li>
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
