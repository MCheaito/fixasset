<!--
    DEV APP
    Created date : 22-7-2022
 -->
@extends('gui.main_gui')
@section('styles')
  
@endsection
@section('content')

	   @include('patients_list.patient_view',['patient'=>$patient])
       @include('patients_list.dashboard.NewVisitModal')     
@endsection

@section('scripts')
 @include('patients_list.patient_scripts') 

@endsection