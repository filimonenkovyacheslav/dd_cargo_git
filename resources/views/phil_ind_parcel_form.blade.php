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
                
                <h1>ORDER FORM</h1>
                <h5>Required fields are marked with (*)</h5>

                <div class="form-group">
                    <label class="control-label">This is not my first order</label>
                    <input type="checkbox" name="not_first_order">
                </div>

                <div class="container">
                    <!-- Modal -->
                    <div class="modal fade" id="philIndParcel" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="question">Enter the same sender data that was on the previous order?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="philIndAnswer(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Yes</button>
                                    <button type="button" onclick="philIndAnswer(this)" class="btn btn-danger pull-left no" data-dismiss="modal">No</button>

                                        {!! Form::open(['url'=>route('philIndCheckPhone'), 'class'=>'form-horizontal check-phone','method' => 'POST']) !!}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                                    {!! Form::hidden('quantity_sender')!!}
                                                    {!! Form::hidden('quantity_recipient')!!}
                                                </div>
                                                <div class="col-md-6">
                                                    {!! Form::button('Send',['class'=>'btn btn-success','type'=>'submit']) !!}
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
                <p><a href="#philIndParcel" class="btn btn-success" data-toggle="modal">Add shipment</a></p>
                
                @if (session('add_parcel'))
                    <script type="text/javascript">
                        var addParcel = '<?=session("add_parcel")?>'
                    </script>
                @else
                    <script type="text/javascript">
                        var addParcel = ''
                    </script>
                @endif

                {!! Form::open(['url'=>route('philIndParcelAdd'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'POST']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('first_name',isset($data_parcel->first_name) ? $data_parcel->first_name : old('first_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s first name*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('last_name',isset($data_parcel->last_name) ? $data_parcel->last_name : old('last_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s last name*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'placeholder' => 'Shipper\'s phone number (standard)*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('shipper_phone',isset($data_parcel->shipper_phone) ? $data_parcel->shipper_phone : old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Shipper\'s phone number (additionally)'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('shipper_address',isset($data_parcel->shipper_address) ? $data_parcel->shipper_address : old('shipper_address'),['class' => 'form-control', 'placeholder' => 'Shipper\'s address*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('consignee_first_name',isset($data_parcel->consignee_first_name) ? $data_parcel->consignee_first_name : old('consignee_first_name'),['class' => 'form-control', 'placeholder' => 'Consignee\'s first name*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('consignee_last_name',isset($data_parcel->consignee_last_name) ? $data_parcel->consignee_last_name : old('consignee_last_name'),['class' => 'form-control', 'placeholder' => 'Consignee\'s last name*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::select('consignee_country', array('India' => 'India', 'Nepal' => 'Nepal', 'The Philippines' => 'The Philippines'), '',['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('consignee_address',isset($data_parcel->consignee_address) ? $data_parcel->consignee_address : old('consignee_address'),['class' => 'form-control', 'placeholder' => 'Consignee\'s address*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('consignee_phone',isset($data_parcel->consignee_phone) ? $data_parcel->consignee_phone : old('consignee_phone'),['class' => 'form-control', 'placeholder' => 'Consignee\'s phone number*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-9">
                            <h3>Shipped items (description)</h3>
                        </div>
                        <div class="col-md-3">
                            <h3>Quantity</h3>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_1',old('item_1'),['class' => 'form-control', 'placeholder' => 'item 1', 'required', 'data-item' => '1'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_1',old('q_item_1'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_2',old('item_2'),['class' => 'form-control', 'placeholder' => 'item 2', 'data-item' => '2'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_2',old('q_item_2'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_3',old('item_3'),['class' => 'form-control', 'placeholder' => 'item 3', 'data-item' => '3'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_3',old('q_item_3'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_4',old('item_4'),['class' => 'form-control', 'placeholder' => 'item 4', 'data-item' => '4'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_4',old('q_item_4'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_5',old('item_5'),['class' => 'form-control', 'placeholder' => 'item 5', 'data-item' => '5'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_5',old('q_item_5'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_6',old('item_6'),['class' => 'form-control', 'placeholder' => 'item 6', 'data-item' => '6'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_6',old('q_item_6'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_7',old('item_7'),['class' => 'form-control', 'placeholder' => 'item 7', 'data-item' => '7'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_7',old('q_item_7'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_8',old('item_8'),['class' => 'form-control', 'placeholder' => 'item 8', 'data-item' => '8'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_8',old('q_item_8'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_9',old('item_9'),['class' => 'form-control', 'placeholder' => 'item 9', 'data-item' => '9'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_9',old('q_item_9'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_10',old('item_10'),['class' => 'form-control', 'placeholder' => 'item 10', 'data-item' => '10'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_10',old('q_item_10'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('weight',old('weight'),['class' => 'form-control', 'placeholder' => 'Shipment weight, kg'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('length',old('length'),['class' => 'form-control', 'placeholder' => 'Shipment length, cm'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('height',old('height'),['class' => 'form-control', 'placeholder' => 'Shipment height, cm'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('width',old('width'),['class' => 'form-control', 'placeholder' => 'Shipment width, cm'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('shipment_val',old('shipment_val'),['class' => 'form-control', 'placeholder' => 'Shipment\'s declared value*', 'required'])!!}
                        </div>
                    </div>
                </div>
                                
                {!! Form::button('Send',['class'=>'btn','type'=>'submit']) !!}
                {!! Form::close() !!}
               
                <!-- временное -->
                <br>
                <div class="tracking">
                    <a href="{{ route('philIndParcelForm') }}">
                        <div class="style-tracking">
                            <span>{{__('front.create_another')}}</span> 
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

    <script>

    function сonfirmSigned(event)
    {
        event.preventDefault();
        const form = event.target;

        const phone = document.querySelector('[name="standard_phone"]'); 
        if (phone.value.length < 10 || phone.value.length > 13) {
            alert('The number of characters in the phone must be from 10 to 13 !');
            return false;
        }
        
        let trueInput = false;
        const input = document.querySelectorAll('.form-send-parcel input');
        input.forEach(function(item) {
            if (item.hasAttribute('data-item')) {
                const num = item.getAttribute('data-item');
                const content = document.querySelector('[name="item_'+num+'"]');
                const quantity = document.querySelector('[name="q_item_'+num+'"]');
                if (content.value && !(quantity.value)) {
                    /*trueInput = true;
                    alert('Fill in the quantity !');
                    return false;*/
                }
                else if(!(content.value) && quantity.value){
                    trueInput = true;
                    alert('Fill in the description !');
                    return false;
                }
            }
        })

        if (trueInput) return false;
        
        form.submit();
    }

</script>

@endsection