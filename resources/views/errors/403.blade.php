@extends('errors::layout')

@section('title', __('Vietata'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Vietata'))
