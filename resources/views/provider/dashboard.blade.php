@extends('provider.layout.pbase')

@section('title', 'Dashboard ')

@section('styles')
	<!--<link rel="stylesheet" href="{{asset('main/vendor/jvectormap/jquery-jvectormap-2.0.3.css')}}">-->
@endsection

@section('content')
<?php
use \Carbon\Carbon;
$todaydate = Carbon::today();
$today = $todaydate->toDateString();

$yesterdaydate = Carbon::yesterday();
$yesterday= $yesterdaydate->toDateString();

?>
	@endsection
