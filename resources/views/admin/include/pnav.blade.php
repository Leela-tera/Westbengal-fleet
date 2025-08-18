<div class="site-sidebar">
	<div class="custom-scroll custom-scroll-light">
		<ul class="sidebar-menu">
                        
			<li class="menu-title">@lang('admin.include.admin_dashboard')</li>
                        
                       
			<li>
				<a href="{{ route('provider.index') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-anchor"></i></span>
					<span class="s-text">@lang('admin.include.dashboard')</span>
				</a>
			</li>
                        

                                          
                       

                        <li>
				<a href="{{ url('/provider/tickets') }}" class="waves-effect waves-light">
					<span class="s-icon"><i class="ti-ticket"></i></span>
					<span class="s-text">Tickets</span>
				</a>
			</li>
                       

                       

                    			
                  
          			<li class="compact-hide">
				<a href="{{ url('/provider/signout') }}"
                            onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
					<span class="s-icon"><i class="ti-power-off"></i></span>
					<span class="s-text">@lang('admin.include.logout')</span>
                </a>

                <form id="logout-form" action="{{ url('/provider/signout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
			</li>
                     
			
		</ul>
	</div>
</div>