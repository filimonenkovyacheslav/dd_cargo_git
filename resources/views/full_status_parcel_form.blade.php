@extends('layouts.front')

@section('content')

<style type="text/css">
	input[name="get_tracking"]::placeholder {
		color: black;
		font-weight: bold;
		opacity: 1; /* Firefox */
	}

	::-ms-input-placeholder { /* Edge 12 -18 */
		color: black;
	}

	.tracking-form button[type="submit"]:hover{
		background-color: #0050ff;
	}
</style>

	<section class="app-content page-bg">
		<div class="container">
			<div class="tracking-form">

				{!! Form::open(['url'=>route('getFullStatusParcel'), 'class'=>'form-horizontal','method' => 'POST']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="get_tracking" class="form-control" placeholder="{{__('tracking.parcel_num')}}" required>
                        </div>
                    </div>
                </div>

                {!! Form::button(__('tracking.track_parcel'),['class'=>'btn btn-primary','type'=>'submit']) !!}

                <h1 class="tracking-result">Полный статус посылки:</h1>

                @if (session('message_ru') && App\Http\Middleware\LocaleMiddleware::getLocale() === 'ru')
					<h1 class="tracking-result">
						{{ session('message_ru') }}
					</h1>
				@elseif (session('message_en') && App\Http\Middleware\LocaleMiddleware::getLocale() === 'en')
					<h1 class="tracking-result">
						{{ session('message_en') }}
					</h1>
				@elseif (session('message_he') && App\Http\Middleware\LocaleMiddleware::getLocale() === 'he')
					<h1 class="tracking-result">
						{{ session('message_he') }}
					</h1>
				@elseif (session('message_ua') && App\Http\Middleware\LocaleMiddleware::getLocale() === 'uk')
					<h1 class="tracking-result">
						{{ session('message_ua') }}
					</h1>
				@elseif (session('message_ru'))
					<h1 class="tracking-result">
						{{ session('message_ru') }}
					</h1>	
				@elseif (session('not_found'))
					<h1 class="tracking-result">{{__('tracking.not_found')}}</h1>
				@endif 						               
                
                {!! Form::close() !!}
				
				<!-- временное -->
                <br>
                <div class="tracking">
                	<a href="{{ route('fullStatusParcelForm') }}">
                		<div class="style-tracking">
                			<span>{{__('front.track_another')}}</span> 
                		</div>           
                	</a>
                </div>
                <br>
                <div class="ask">
                	<a href="{{__('front.home_link')}}">
                		<div class="style-ask">
                			<span>{{__('front.back')}}</span>
                		</div>    
                	</a>    
                </div>
				<!-- /временное -->			
			
			</div>
		</div>           
	</section><!-- /.app-content -->

@endsection