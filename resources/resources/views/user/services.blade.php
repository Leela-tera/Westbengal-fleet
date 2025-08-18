@extends('user.layout.base')

@section('title', 'Dashboard ')

@section('content')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">Select Services</h4>
            </div>
        </div>
        @include('common.notify')
        <div class="services row no-margin"> 
            {{ csrf_field() }}
        @foreach($services as $service) 
            <div class="col-md-4">
                <div class="services-sel-box">
                    <a href="#" class="sel-ser-link" onclick="select_service({{$service->id}})">
                        <img src="{{img($service->image)}}" style="width: 247px; height: 111px;">
                        <h3 class="sel-ser-tit" style="text-align: center;">{{$service->name}}</h3>
                    </a>
                </div>
            </div> 
        @endforeach

        </div>

    </div>
</div> 
<script type="text/javascript">
    
    function select_service(service_id) { 
        window.location.href = "{{url('dashboard')}}?service_id="+service_id;   
    }

</script>
@endsection

