@extends('layouts.front')

@section('content')

    <section class="app-content page-bg">
        <div class="container">                       
            <div class="parcel-form">

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('quantity_sender'))
                    <div class="alert alert-success">
                        {{ session('quantity_sender') }}
                    </div>
                @endif
                
                @php
                if (session('data_parcel')){
                    $data_parcel = json_decode(session('data_parcel'));
                }
                @endphp
                
                @if (session('no_phone'))
                    <div class="alert alert-danger">
                        {{ session('no_phone') }}
                    </div>
                @endif
                
                <h1>Заказ посылки</h1>
                <h5>Обязательные поля отмечены значком  (*)</h5>
                <h5>Данные отправителя (заполняется на английском)</h5>

                <div class="form-group">
                    <label class="control-label">Это не первый мой заказ</label>
                    <input type="checkbox" name="not_first_order">
                </div>
                
                <div class="container">
                    <!-- Modal -->
                    <div class="modal fade" id="addRuParcel" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="question">Ввести те же данные отправителя, которые были при предыдущем заказе?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="clickAnswer(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Да</button>
                                    <button type="button" onclick="clickAnswer(this)" class="btn btn-danger pull-left no" data-dismiss="modal">Нет</button>

                                        {!! Form::open(['url'=>route('checkPhone'), 'class'=>'form-horizontal check-phone','method' => 'POST']) !!}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::text('sender_phone',old('sender_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                                    {!! Form::hidden('quantity_sender')!!}
                                                    {!! Form::hidden('quantity_recipient')!!}
                                                </div>
                                                <div class="col-md-6">
                                                    {!! Form::button('Отправить',['class'=>'btn btn-success','type'=>'submit']) !!}
                                                </div>
                                            </div>
                                        </div>                                        
                                                                                
                                        {!! Form::close() !!}                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 

                <!-- Link to open the modal -->
                <p><a href="#addRuParcel" class="btn btn-success" data-toggle="modal">Добавить посылку</a></p>
                
                @if (session('add_parcel'))
                    <script type="text/javascript">
                        var addParcel = '<?=session("add_parcel")?>'
                    </script>
                @else
                    <script type="text/javascript">
                        var addParcel = ''
                    </script>
                @endif
                               
                
                {!! Form::open(['url'=>route('newParcelAdd'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'POST']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('first_name',isset($data_parcel->first_name) ? $data_parcel->first_name : old('first_name'),['class' => 'form-control', 'placeholder' => 'First name*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('last_name',isset($data_parcel->last_name) ? $data_parcel->last_name : old('last_name'),['class' => 'form-control', 'placeholder' => 'Last name*', 'required'])!!}
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('sender_address',isset($data_parcel->sender_address) ? $data_parcel->sender_address : old('sender_address'),['class' => 'form-control', 'placeholder' => 'Address (street, buliding, apt.)*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('sender_city',isset($data_parcel->sender_city) ? $data_parcel->sender_city : old('sender_city'),['class' => 'form-control', 'placeholder' => 'City*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('sender_postcode',isset($data_parcel->sender_postcode) ? $data_parcel->sender_postcode : old('sender_postcode'),['class' => 'form-control', 'placeholder' => 'Postcode'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('sender_country',isset($data_parcel->sender_country) ? $data_parcel->sender_country : old('sender_country'),['class' => 'form-control', 'placeholder' => 'Country*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'placeholder' => 'Phone (standard)*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('sender_phone',isset($data_parcel->sender_phone) ? $data_parcel->sender_phone : old('sender_phone'),['class' => 'form-control', 'placeholder' => 'Phone (additionally)'])!!}
                        </div>                        
                    </div>
                </div>

                <h5>Данные получателя (заполняется на русском)</h5>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('recipient_first_name',isset($data_parcel->recipient_first_name) ? $data_parcel->recipient_first_name : old('recipient_first_name'),['class' => 'form-control', 'placeholder' => 'Имя*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('recipient_last_name',isset($data_parcel->recipient_last_name) ? $data_parcel->recipient_last_name : old('recipient_last_name'),['class' => 'form-control', 'placeholder' => 'Фамилия*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <h5>Адрес</h5>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('recipient_street',isset($data_parcel->recipient_street) ? $data_parcel->recipient_street : old('recipient_street'),['class' => 'form-control', 'placeholder' => 'Улица*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('recipient_house',isset($data_parcel->recipient_house) ? $data_parcel->recipient_house : old('recipient_house'),['class' => 'form-control', 'placeholder' => 'Номер дома*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('recipient_room',isset($data_parcel->recipient_room) ? $data_parcel->recipient_room : old('recipient_room'),['class' => 'form-control', 'placeholder' => 'Номер квартиры*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('body',isset($data_parcel->body) ? $data_parcel->body : old('body'),['class' => 'form-control', 'placeholder' => 'Номер корпуса'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('recipient_city',isset($data_parcel->recipient_city) ? $data_parcel->recipient_city : old('recipient_city'),['class' => 'form-control', 'placeholder' => 'Город*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('recipient_postcode',isset($data_parcel->recipient_postcode) ? $data_parcel->recipient_postcode : old('recipient_postcode'),['class' => 'form-control', 'placeholder' => 'Индекс*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('district',isset($data_parcel->district) ? $data_parcel->district : old('district'),['class' => 'form-control', 'placeholder' => 'Район'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('region',isset($data_parcel->region) ? $data_parcel->region : old('region'),['class' => 'form-control', 'placeholder' => 'Регион'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::select('recipient_country', array('RU' => 'Россия (RU)', 'UA' => 'Украина (UA)', 'BY' => 'Беларусь (BY)', 'KZ' => 'Казахстан (KZ)'), isset($data_parcel->recipient_country) ? $data_parcel->recipient_country : '',['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('recipient_phone',isset($data_parcel->recipient_phone) ? $data_parcel->recipient_phone : old('recipient_phone'),['class' => 'form-control', 'placeholder' => 'Номер телефона*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        {!! Form::label('tariff','Тариф',['class' => 'col-md-4 control-label'])   !!}
                        <div class="col-md-8">
                            {!! Form::select('tariff', array('Море' => 'Море', 'Авиа' => 'Авиа'), '',['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <h5>Содержимое посылки (заполняется на русском)</h5>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-9">
                            {!! Form::label('clothing_quantity','Одежда',['class' => 'control-label']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('clothing_quantity',old('clothing_quantity'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-9">
                            {!! Form::label('shoes_quantity','Обувь',['class' => 'control-label']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::text('shoes_quantity',old('shoes_quantity'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <h5>Другое</h5>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_1',old('other_content_1'),['class' => 'form-control', 'placeholder' => 'Описание 1', 'data-item' => '1'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_1',old('other_quantity_1'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_2',old('other_content_2'),['class' => 'form-control', 'placeholder' => 'Описание 2', 'data-item' => '2'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_2',old('other_quantity_2'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_3',old('other_content_3'),['class' => 'form-control', 'placeholder' => 'Описание 3', 'data-item' => '3'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_3',old('other_quantity_3'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_4',old('other_content_4'),['class' => 'form-control', 'placeholder' => 'Описание 4', 'data-item' => '4'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_4',old('other_quantity_4'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_5',old('other_content_5'),['class' => 'form-control', 'placeholder' => 'Описание 5', 'data-item' => '5'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_5',old('other_quantity_5'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_6',old('other_content_6'),['class' => 'form-control', 'placeholder' => 'Описание 6', 'data-item' => '6'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_6',old('other_quantity_6'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_7',old('other_content_7'),['class' => 'form-control', 'placeholder' => 'Описание 7', 'data-item' => '7'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_7',old('other_quantity_7'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('other_content_8',old('other_content_8'),['class' => 'form-control', 'placeholder' => 'Описание 8', 'data-item' => '8'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('other_quantity_8',old('other_quantity_8'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-3">
                            {!! Form::number('package_cost',old('package_cost'),['class' => 'form-control', 'required'])!!}
                        </div>
                        {!! Form::label('package_cost','Декларируемая стоимость содержимого*, EUR',['class' => 'col-md-9 control-label']) !!}
                    </div>
                </div>

                <h5>Упаковка</h5>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-2">
                            {!! Form::radio('need_box','Мне нужна коробка на 30 кг', false)!!}
                        </div>
                        <span class="col-md-10 control-label">Мне нужна коробка на 30 кг</span>                        
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-2">
                            {!! Form::radio('need_box','Мне нужна коробка на 20 кг', false)!!}
                        </div>
                        <span class="col-md-10 control-label">Мне нужна коробка на 20 кг</span> 
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-2">
                            {!! Form::radio('need_box','Мне нужна коробка на 10 кг', false)!!}
                        </div>
                        <span class="col-md-10 control-label">Мне нужна коробка на 10 кг</span> 
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-2">
                            {!! Form::radio('need_box','Мне не нужна коробка', true)!!}
                        </div>
                        <span class="col-md-10 control-label">Мне не нужна коробка</span> 
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-2">
                            {!! Form::radio('need_box','Мне нужно несколько коробок', false)!!}
                        </div>
                        <span class="col-md-10 control-label">Мне нужно несколько коробок</span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">                       
                        <div class="col-md-2">
                            {!! Form::checkbox('signed_form','', false)!!}
                        </div>
                        <div class="col-md-10"><strong>Подписывая эту форму гарантирую следующее:*</strong> описание содержимого посылки и ее заявленной стоимости являются полными и правдивыми. Я ознакомился/лась со списками предметов, запрещенных ко ввозу и имеющих ограничения на ввоз в страну назначения, и моя посылка либо не содержит эти предметы, либо соответствует таможенным правилам. В случае нарушения этих гарантий я согласен/на оплатить расходы, возникшие по моей вине, в частности, дополнительные таможенные проверки и/или возврат моей посылки в страну отправления.</div>
                    </div>
                </div>

                {!! Form::button('Отправить',['class'=>'btn','type'=>'submit']) !!}
                {!! Form::close() !!}
               
                <!-- временное -->
                <br>
                <div class="tracking">
                    <a href="{{ route('parcelForm') }}">
                        <div class="style-tracking">
                            <span>{{__('front.create_another')}}</span> 
                        </div>           
                    </a>
                </div>
                <br>
                <div class="ask">
                    <a href="{{__('front.home_link')}}" aria-haspopup="true" target="_self" aria-label="ЗАДАЙТЕ ВОПРОС">
                        <div class="style-ask">
                            <span>{{__('front.back')}}</span>
                        </div>    
                    </a>    
                </div>
                <!-- /временное -->           
            
            </div>
        </div> 
    
    </section><!-- /.app-content -->

    <script>

    function сonfirmSigned(event)
    {
        event.preventDefault();
        const form = event.target;
        
        let trueInput = false;
        
        const phone = document.querySelector('[name="standard_phone"]'); 
        if (phone.value.length < 10 || phone.value.length > 13) {
            alert('Кол-во знаков в телефоне должно быть от 10 до 13 !');
            return false;
        }
        
        const input = document.querySelectorAll('.parcel-form input');
        input.forEach(function(item) {           
            if (item.hasAttribute('data-item')) {
                const num = item.getAttribute('data-item');
                const content = document.querySelector('[name="other_content_'+num+'"]');
                const quantity = document.querySelector('[name="other_quantity_'+num+'"]');
                
                if (content.value && !(quantity.value)) {
                    /*trueInput = true;
                    alert('Заполните количество !');
                    return false;*/
                }
                else if(!(content.value) && quantity.value){
                    trueInput = true;
                    alert('Заполните описание !');
                    return false;
                }              
            }            
        })

        if (trueInput) return false;
        
        var x = document.querySelector('[name="signed_form"]').checked;
        if (x)
            form.submit();
        else{
            alert('Подпишите форму !');
            return false;
        }
    }

</script>

@endsection