@extends('layouts.front')

@section('content')

    <section class="app-content">
        <div class="home-bg">
            <div class="home-empty"></div>
            <div class="home-text">
                <p class="home-title">{{__('welcome.big_title')}}</p>
                <p class="home-tel">972-55-966-1641</p>
                <p class="home-desc">{{__('welcome.small_title')}}</p>
            </div>
        </div>           
    </section><!-- /.app-content -->

@endsection