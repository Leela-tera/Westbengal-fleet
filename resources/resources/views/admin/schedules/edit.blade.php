@extends('admin.layout.base')

@section('title', 'Update Schedular ')

@section('content')
<style type="text/css">
	.shadow-gray {
    box-shadow: 0 0 5px 1px #3333332e !important;
	}
	.col-form-label{
		font-size: 13px !important;
		font-weight: 600;
	}
	.p-2-5{
		padding:2.5rem;
	}
</style>
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.schedulers') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.schedule.update_schedule')</h5>

      <form class="form-horizontal" action="{{route('admin.schedulers.update', $schedule->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
        <input type="hidden" name="_method" value="PATCH">
		<div class="top-cs box-block shadow-gray mb-3">
        	<h5 class="mb-2">Schedule Auto Assign Date</h5>
        	<div class="box-block pb-0">
        	<div class="form-group row">
						<div class="col-sm-10 col-md-10">
							<label for="url" class="col-form-label  ">Schedule Url
							</label>
							<input class="form-control select-box" type="text" value="{{ $schedule->url }}" name="url"  id="url" placeholder="Schedule Url" disabled>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 col-md-10">
						<label for="schedule_time" class="col-form-label">Time Frames<span class="look-a-like">*</span></label>
							<select class="form-control select-box" name="schedule_time" id="schedule_time" required>
								<option value="">Please Select Time Frame</option>
								<option value="*/20 * * * *" {{ '*/20 * * * *' == $schedule->schedule_time ? 'selected' : '' }}>Every 20 Minute</option>
								<option value="*/30 * * * *" {{ '*/30 * * * *' == $schedule->schedule_time ? 'selected' : '' }}>Every 30 Minute</option>
								<option value="0 * * * *" {{ '0 * * * *' == $schedule->schedule_time ? 'selected' : '' }}>Every Hour</option>
								<option value="0 0 * * *" {{ '0 * * * *' == $schedule->schedule_time ? 'selected' : '' }}>Every Day</option>
								<option value="custom" {{ 'custom' == $schedule->is_custom ? 'selected' : '' }}>Custom</option>
								
							</select>
						</div>
					</div>
					<div class="form-group row" id="custom_time" style="display: none;">
						<div class="col-sm-10 col-md-10">
							<label for="cst" class="col-form-label  ">Custom Schedule
							</label>
							<input class="form-control select-box" type="text" value="{{ $schedule->schedule_time }}" name="cst"  id="cst" placeholder="Ex: * * * * * -- Every Minute">
						</div>
					</div>
					<div class="form-group row">
						<label for="zipcode" class="col-xs-12 col-form-label"></label>
						<div class="col-xs-12 mt-2">
							<button type="submit" class="btn btn-primary btn-cstm pull-right ">@lang('admin.schedule.update_schedule')</button>
							<a href="{{route('admin.schedulers')}}" class="btn btn-default pull-right ">@lang('admin.cancel')</a>
						</div>
					</div>
			</div>
		</div>
			</form>
		</div>

    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cronstrue/2.23.0/cronstrue.min.js" integrity="sha512-PXJQ3X+ThkPqqzj+V+OpU/R4eW1INJIpDQgetBNVy4gr0auTfF7H2OAw4zrEUDHWsWGoKw70vdwzMk9Eiu7WSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	const selectElement = document.getElementById("schedule_time");
	if (selectElement.value === "custom") {
	  document.getElementById("custom_time").style.display = "block";
	}
	$("select[id='schedule_time']").change(function(){
	  var schedule_time = $(this).val();
	  if(schedule_time == 'custom'){
	  	document.getElementById("custom_time").style.display = "block";
	  } else {
	  	document.getElementById("custom_time").style.display = "none";
	  }
	});
	$("#cst").keyup(function(){
		try{
			cronstrue.toString(this.value, { verbose: true });
			document.getElementById("cst").style.borderColor = "rgba(0,0,0,.15)";
		}catch(error){
			document.getElementById("cst").style.borderColor = "red";
		}
	});
</script>

@endsection
