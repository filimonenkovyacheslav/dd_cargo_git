@extends('layouts.front')

@section('content')

	<section class="app-content page-bg">
		<div class="container">
			<div class="tracking-form">
				<h2>חבילות לישראל | parcels to Israel | посилки до Ізраїлю | посылки в Израиль</h2>
				<h2>חבילות מישראל | parcels from Israel | посилки з Ізраїлю | посылки из Израиля</h2> 

				{!! Form::open(['url'=>route('getTracking'), 'class'=>'form-horizontal','method' => 'POST']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('get_tracking',old('get_tracking'),['class' => 'form-control', 'placeholder' => __('tracking.parcel_num'), 'required'])!!}
                        </div>
                    </div>
                </div>

                {!! Form::button(__('tracking.track_parcel'),['class'=>'btn','type'=>'submit']) !!}

                <h1 class="tracking-result">{{__('tracking.status_title')}}</h1>

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
                	<a href="{{ route('trackingForm') }}">
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

	<script type="text/javascript">
		const updateStatus = '{{ session("update_status_date") }}';
		console.log(updateStatus);

		if (updateStatus === '0'){
			$.ajax({
				url: "{{ route('updateStatus') }}",
				type: "GET",
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				success: function (data) {
					console.log(data);
				},
				error: function (msg) {
					alert('Ошибка admin');
				}
			});
		}

	</script>

@endsection