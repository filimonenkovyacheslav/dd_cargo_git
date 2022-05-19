@extends('layouts.front_signature_form')

@section('content')

<section class="app-content page-bg">
    <div class="container">                       
        <div class="parcel-form new-form">

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

            @php
            if ($data_parcel){
                $data_parcel = json_decode($data_parcel);
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

                                {!! Form::hidden('signature','signature') !!}

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

                <div class="modal fade" id="phoneExist" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p class="question">{{ session('phone_exist') }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clickAnswer2(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Да</button>
                                <button type="button" onclick="clickAnswer2(this)" class="btn btn-danger pull-left no" data-dismiss="modal">Нет</button>

                                {!! Form::open(['url'=>route('checkPhone'), 'class'=>'form-horizontal check-phone','method' => 'POST']) !!}

                                {!! Form::hidden('signature','signature') !!}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {!! Form::text('sender_phone',old('sender_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                            {!! Form::hidden('quantity_sender')!!}
                                            {!! Form::hidden('quantity_recipient')!!}
                                            {!! Form::hidden('draft','draft')!!}
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
            <p><a href="#addRuParcel" class="btn btn-success ru-modal" data-toggle="modal">Добавить посылку</a>
            </p>
            <a href="#phoneExist" class="btn btn-success ru-modal-2" data-toggle="modal"></a>

            @if (session('add_parcel'))
            <script type="text/javascript">
                var addParcel = '<?=session("add_parcel")?>'
            </script>
            @else
            <script type="text/javascript">
                var addParcel = ''
            </script>
            @endif

            @if (session('phone_exist'))
            <script type="text/javascript">
                var phoneExist = '<?=session("phone_exist")?>';
                var phoneNumber = '<?=session("phone_number")?>';
            </script>
            @else
            <script type="text/javascript">
                var phoneExist = ''
            </script>
            @endif             

            {!! Form::open(['url'=>route('addSignedRuForm'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'POST','accept-charset'=>'UTF-8']) !!}

            {!! Form::hidden('phone_exist_checked',isset($data_parcel->phone_exist_checked) ? $data_parcel->phone_exist_checked : '')!!}

            {!! Form::hidden('signature','signature') !!}   

            @if($worksheet)
            {!! Form::hidden('worksheet_id', $worksheet->id) !!}  
            @endif

            @if (isset($token))
            <input type="hidden" name="session_token" value="{{ $token }}">
            @endif 

            @if (isset($document_id))
            <input type="hidden" name="document_id" value="{{ $document_id }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="id" value="{{ $id }}">
            @endif           

            <h3>Данные отправителя</h3>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        {!! Form::select('sender_country', array('Israel' => 'Israel', 'Germany' => 'Germany'), isset($data_parcel->sender_country) ? $data_parcel->sender_country : '',['class' => 'form-control']) !!}
                    </div>
                    {!! Form::label('sender_city','Если вы не нашли в списке название вашего населенного пункта, выберите название ближайшего к вам города',['class' => 'col-md-6 control-label'])   !!}
                    <div class="col-md-3">
                        {!! Form::select('sender_city', $israel_cities, isset($data_parcel->sender_city) ? $data_parcel->sender_city : '',['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        {!! Form::text('first_name',isset($data_parcel->first_name) ? $data_parcel->first_name : old('first_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s first name*', 'required', 'oninput' => 'document.querySelector(".first-name").innerHTML = this.value'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('last_name',isset($data_parcel->last_name) ? $data_parcel->last_name : old('last_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s last name*', 'required', 'oninput' => 'document.querySelector(".last-name").innerHTML = this.value'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'placeholder' => 'Shipper\'s phone number*', 'required'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('sender_address',isset($data_parcel->sender_address) ? $data_parcel->sender_address : old('sender_address'),['class' => 'form-control', 'placeholder' => 'Shipper\'s address*', 'required'])!!}
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
                    <div class="col-md-3">
                        {!! Form::text('recipient_street',isset($data_parcel->recipient_street) ? $data_parcel->recipient_street : old('recipient_street'),['class' => 'form-control', 'placeholder' => 'Улица*', 'required'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('recipient_house',isset($data_parcel->recipient_house) ? $data_parcel->recipient_house : old('recipient_house'),['class' => 'form-control', 'placeholder' => 'Номер дома*', 'required'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('recipient_room',isset($data_parcel->recipient_room) ? $data_parcel->recipient_room : old('recipient_room'),['class' => 'form-control', 'placeholder' => 'Номер квартиры*', 'required'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('body',isset($data_parcel->body) ? $data_parcel->body : old('body'),['class' => 'form-control', 'placeholder' => 'Номер корпуса'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        {!! Form::text('recipient_city',isset($data_parcel->recipient_city) ? $data_parcel->recipient_city : old('recipient_city'),['class' => 'form-control', 'placeholder' => 'Город*', 'required'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('recipient_postcode',isset($data_parcel->recipient_postcode) ? $data_parcel->recipient_postcode : old('recipient_postcode'),['class' => 'form-control', 'placeholder' => 'Индекс'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('district',isset($data_parcel->district) ? $data_parcel->district : old('district'),['class' => 'form-control', 'placeholder' => 'Район'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::select('recipient_country', array('RU' => 'Россия (RU)', 'UA' => 'Украина (UA)', 'BY' => 'Беларусь (BY)', 'KZ' => 'Казахстан (KZ)'), isset($data_parcel->recipient_country) ? $data_parcel->recipient_country : '',['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::text('region',isset($data_parcel->region) ? $data_parcel->region : old('region'),['class' => 'form-control', 'placeholder' => 'Регион'])!!}
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
                    <div class="col-11">
                        {!! Form::text('other_content_1', isset($data_parcel->item_1) ? $data_parcel->item_1 : old('other_content_1'), ['class' => 'form-control', 'placeholder' => 'Описание 1', 'data-item' => '1'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_1', isset($data_parcel->q_item_1) ? $data_parcel->q_item_1 : old('other_quantity_1'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_2', isset($data_parcel->item_2) ? $data_parcel->item_2 : old('other_content_2'),['class' => 'form-control', 'placeholder' => 'Описание 2', 'data-item' => '2'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_2', isset($data_parcel->q_item_2) ? $data_parcel->q_item_2 : old('other_quantity_2'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_3', isset($data_parcel->item_3) ? $data_parcel->item_3 : old('other_content_3'),['class' => 'form-control', 'placeholder' => 'Описание 3', 'data-item' => '3'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_3', isset($data_parcel->q_item_3) ? $data_parcel->q_item_3 : old('other_quantity_3'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_4', isset($data_parcel->item_4) ? $data_parcel->item_4 : old('other_content_4'),['class' => 'form-control', 'placeholder' => 'Описание 4', 'data-item' => '4'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_4', isset($data_parcel->q_item_4) ? $data_parcel->q_item_4 : old('other_quantity_4'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_5', isset($data_parcel->item_5) ? $data_parcel->item_5 : old('other_content_5'),['class' => 'form-control', 'placeholder' => 'Описание 5', 'data-item' => '5'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_5', isset($data_parcel->q_item_5) ? $data_parcel->q_item_5 : old('other_quantity_5'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_6', isset($data_parcel->item_6) ? $data_parcel->item_6 : old('other_content_6'),['class' => 'form-control', 'placeholder' => 'Описание 6', 'data-item' => '6'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_6', isset($data_parcel->q_item_6) ? $data_parcel->q_item_6 : old('other_quantity_6'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_7', isset($data_parcel->item_7) ? $data_parcel->item_7 : old('other_content_7'),['class' => 'form-control', 'placeholder' => 'Описание 7', 'data-item' => '7'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_7', isset($data_parcel->q_item_7) ? $data_parcel->q_item_7 : old('other_quantity_7'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_8', isset($data_parcel->item_8) ? $data_parcel->item_8 : old('other_content_8'),['class' => 'form-control', 'placeholder' => 'Описание 8', 'data-item' => '8'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_8', isset($data_parcel->q_item_8) ? $data_parcel->q_item_8 : old('other_quantity_8'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_9', isset($data_parcel->item_9) ? $data_parcel->item_9 : old('other_content_9'),['class' => 'form-control', 'placeholder' => 'Описание 9', 'data-item' => '9'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_9', isset($data_parcel->q_item_9) ? $data_parcel->q_item_9 : old('other_quantity_9'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-11">
                        {!! Form::text('other_content_10', isset($data_parcel->item_10) ? $data_parcel->item_10 : old('other_content_10'),['class' => 'form-control', 'placeholder' => 'Описание 10', 'data-item' => '10'])!!}
                    </div>
                    <div class="col-1">
                        {!! Form::text('other_quantity_10', isset($data_parcel->q_item_10) ? $data_parcel->q_item_10 : old('other_quantity_10'),['class' => 'form-control', 'placeholder' => 'количество'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-3">
                        {!! Form::hidden('parcels_qty','1') !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-3">
                        {!! Form::text('weight',old('weight'),['class' => 'form-control', 'placeholder' => 'Вес посылки, кг'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('length',old('length'),['class' => 'form-control', 'placeholder' => 'Длина посылки, см'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('height',old('height'),['class' => 'form-control', 'placeholder' => 'Высота посылки, см'])!!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::text('width',old('width'),['class' => 'form-control', 'placeholder' => 'Ширина посылки, см'])!!}
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
                    <div class="col-md-6 control-label">
                        {!! Form::radio('need_box','Мне нужна коробка на 30 кг', false)!!}
                        <span>Мне нужна коробка на 30 кг</span>
                    </div>  
                    <div class="col-md-6 control-label">
                        {!! Form::radio('need_box','Мне нужна коробка на 20 кг', false)!!}
                        <span>Мне нужна коробка на 20 кг</span>
                    </div>                                          
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6 control-label">
                        {!! Form::radio('need_box','Мне нужна коробка на 10 кг', false)!!}
                        <span>Мне нужна коробка на 10 кг</span>
                    </div>  
                    <div class="col-md-6 control-label">
                        {!! Form::radio('need_box','Мне не нужна коробка', true)!!}
                        <span>Мне не нужна коробка</span>
                    </div>                  
                </div>
            </div>

            <div class="form-group">
                <div class="row">                       
                    <div class="col-md-12 control-label">
                        {!! Form::radio('need_box','Мне нужно несколько коробок', false)!!}
                        <span>Мне нужно несколько коробок</span>
                    </div>
                </div>
            </div>

            <h3>ГАРАНТИЙНЫЕ ОБЯЗАТЕЛЬСТВА ОТПРАВИТЕЛЯ:</h3>
            <p>Я, <span class="first-name">{{isset($data_parcel->first_name) ? $data_parcel->first_name : ''}}</span> <span class="last-name">{{isset($data_parcel->last_name) ? $data_parcel->last_name : ''}}</span>, нижеподписавшийся/нижеподписавшаяся, подтверждаю, что являюсь отправителем всех вышеуказанных предметов, перечисленных в этом упаковочном листе, включая прилагаемый дополнительный подписанный упаковочный лист (если таковой имеется), и что я лично их упаковал. Подписывая форму, я гарантирую следующее:</p>
            <ol>
                <li>в этом подробном упаковочном листе указано точное и правильное количество предметов, содержащихся в коробке;</li>
                <li>НЕТ необъявленных, запрещенных, незаконных или запрещенных предметов, включая:
                    <ul>
                        <li>лекарства</li>
                        <li>скоропортящиеся продукты</li>
                        <li>оружие</li>
                        <li>наркотики</li>
                        <li>органические материалы</li>
                        <li>боеприпасы</li>
                        <li>горючие предметы</li>
                        <li>наличные деньги</li>
                        <li>ювелирные изделия</li>
                        <li>порнографические материалы</li>
                    </ul>
                </li>
                <li>все предметы, содержащиеся в коробке, предназначены исключительно для личного некоммерческого использования.</li>
                <li>в случае нарушения данных гарантий я согласен оплатить расходы, понесенные по моей вине, вызванные задержками в таможенном оформлении и / или возврате посылки со склада в Израиле или из страны назначения мне.</li>
            </ol>
            <p>Подписывая эту форму, я подтверждаю, что я прочитал и понял все письменные и прилагаемые положения и условия</p>

            <input type="hidden" id="form_canvas" name="form_canvas">

            {!! Form::button('Подписать',['class'=>'btn','type'=>'submit']) !!}
            {!! Form::close() !!}                       
            
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
            alert('Кол-во знаков в телефоне отправителя должно быть от 10 до 13 !');
            return false;
        }

        const recipientPhone = document.querySelector('[name="recipient_phone"]');
        const regexp = /[0-9]/g;
        const phoneDigits = recipientPhone.value.slice(1);         
        if (recipientPhone.value.length < 6 || recipientPhone.value.length > 24) {
            alert('Кол-во знаков в телефоне получателя должно быть от 6 до 24 !');
            return false;
        }
        else if (recipientPhone.value[0] !== '+') {
            alert('Телефон получателя должен начинаться с "+" !');
            return false;
        }
        else if (!regexp.test(phoneDigits)) {
            alert('Телефон получателя должен содержать только цифры !');
            return false;
        }

        const input = document.querySelectorAll('.parcel-form input');
        const parcelsQty = document.querySelector('[name="parcels_qty"]');
        if (!parcelsQty.value) parcelsQty.value = 1;

        input.forEach(function(item) {           
            if (item.hasAttribute('data-item')) {
                const num = item.getAttribute('data-item');
                const content = document.querySelector('[name="other_content_'+num+'"]');
                const quantity = document.querySelector('[name="other_quantity_'+num+'"]');

                if (content.value && !(quantity.value)) {
                }
                else if(!(content.value) && quantity.value){
                    trueInput = true;
                    alert('Заполните описание !');
                    return false;
                }              
            }            
        })

        if (trueInput) return false;

        html2canvas(document.querySelector('.form-send-parcel')).then(function(canvas) {
            document.getElementById('form_canvas').value = canvas.toDataURL('image/jpeg');
        });

        setTimeout(()=>{
            form.submit();
        },500)        
    }

</script>

@endsection