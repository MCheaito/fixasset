<!--
    DEV APP
    Created date : 15-7-2022
 -->
@extends('gui.main_gui')
@section('styles')
@endsection
@section('content')

	   @include('patients_list.patient_view')
	  
@endsection

@section('scripts')
 @include('patients_list.patient_scripts') 

@endsection
