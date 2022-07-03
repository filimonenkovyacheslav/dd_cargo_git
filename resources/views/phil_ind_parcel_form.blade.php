@extends('layouts.front')

@section('content')

<section class="app-content page-bg">
    <div class="container">                       
        <div class="parcel-form">

            @if (session('status') === 'The phone number is exist in Draft!')
            <div class="alert alert-danger">
                {{ session('status') }}
            </div>
            @elseif (session('status'))
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

            <div class="form-group">
                <label class="control-label">I need empty box/boxes</label>
                <input onclick="clickRadio(this)" type="radio" name="need_box" value="need">
                <h6>please specify the boxes types and quantity</h6>
                <h6>TYPE - QUANTITY</h6>
                <ul class="box-group">
                    <li style="width: 150px;">
                        <label class="control-label">Extra Large</label>
                        <input type="number" name="extra_large" style="width: 40px;float: right;" min="0">
                    </li>
                    <li style="width: 150px;">
                        <label class="control-label">Large</label>
                        <input type="number" name="large" style="width: 40px;float: right;" min="0">
                    </li>
                    <li style="width: 150px;">
                        <label class="control-label">Medium</label>
                        <input type="number" name="medium" style="width: 40px;float: right;" min="0">
                    </li>
                    <li style="width: 150px;">
                        <label class="control-label">Small</label>
                        <input type="number" name="small" style="width: 40px;float: right;" min="0">
                    </li>
                </ul>
                
                <label class="control-label">I send in my own box</label>
                <input onclick="clickRadio(this)" type="radio" name="need_box" value="not_need" checked>
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
                                <button type="button" onclick="philIndAnswer2(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Yes</button>
                                <button type="button" onclick="philIndAnswer2(this)" class="btn btn-danger pull-left no" data-dismiss="modal">No</button>

                                {!! Form::open(['url'=>route('philIndCheckPhone'), 'class'=>'form-horizontal check-phone','method' => 'POST']) !!}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                            {!! Form::hidden('quantity_sender')!!}
                                            {!! Form::hidden('quantity_recipient')!!}
                                            {!! Form::hidden('draft','draft')!!}
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
            <p><a href="#philIndParcel" class="btn btn-success eng-modal" data-toggle="modal">Add shipment</a></p>
            <a href="#phoneExist" class="btn btn-success eng-modal-2" data-toggle="modal"></a>
            
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

            {!! Form::open(['url'=>route('philIndParcelAdd'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'POST']) !!}

            {!! Form::hidden('phone_exist_checked',isset($data_parcel->phone_exist_checked) ? $data_parcel->phone_exist_checked : '')!!}
            {!! Form::hidden('status_box','')!!}
            {!! Form::hidden('comments_2','')!!}
            {!! Form::hidden('short_order','short_order') !!}

            <h3>Shipper’s Data</h3>
            
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::select('shipper_country', array('Israel' => 'Israel', 'Germany' => 'Germany'), isset($data_parcel->shipper_country) ? $data_parcel->shipper_country : '',['class' => 'form-control']) !!}
                    </div>
                </div>                    
            </div>

            <div class="form-group">
                <div class="row">
                    @php
                    $temp = array('' => 'Please choose the nearest city');
                    $israel_cities = array_merge($temp, $israel_cities);
                    @endphp
                    <div class="col-md-12">
                        {!! Form::select('shipper_city', $israel_cities, isset($data_parcel->shipper_city) ? $data_parcel->shipper_city : '',['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

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
                        {!! Form::text('shipper_address',isset($data_parcel->shipper_address) ? $data_parcel->shipper_address : old('shipper_address'),['class' => 'form-control', 'placeholder' => 'Shipper\'s address*', 'required'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    {!! Form::label('consignee_country','Destination County',['class' => 'col-md-6 control-label'])   !!}
                    <div class="col-md-6">
                        {!! Form::select('consignee_country', $to_country, isset($data_parcel->consignee_country) ? $data_parcel->consignee_country: '',['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-9">
                        <h3>Parcels qty</h3>
                    </div>
                    <div class="col-3">
                        {!! Form::number('parcels_qty',isset($data_parcel->parcels_qty) ? $data_parcel->parcels_qty :'1',['class' => 'form-control', 'min' => '1'])!!}
                    </div>
                </div>
            </div>

            {!! Form::hidden('item_1','')!!}
            {!! Form::hidden('q_item_1','')!!}
            {!! Form::hidden('consignee_address','')!!}
            
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
    const boxGroup = document.querySelectorAll('.box-group input');
    boxGroup.forEach(function(item) {
        if (localStorage.getItem('boxString')) 
            item.disabled = false;
        else
            item.disabled = true; 
    })


    setTimeout(()=>{
        if (localStorage.getItem('boxString')) {
            const boxString = localStorage.getItem('boxString');
            const tempArr = boxString.split('; ');

            $('[name="need_box"]').each((k,el)=>{
                if ($(el).val() === 'need') 
                    $(el).prop( "checked", true );
                else
                    $(el).prop( "checked", false );
            });

            $('.box-group input').each((k,el)=>{
                for (let i = 0; i < tempArr.length; i++) {
                    if ($(el).attr('name') === tempArr[i].split(': ')[0]) 
                        $(el).val(tempArr[i].split(': ')[1])                    
                }
            });
        }

        const button = document.querySelector('form.form-send-parcel button[type="submit"]')
        const checkPhone = document.querySelector('form.form-send-parcel [name="phone_exist_checked"]').value
        if (checkPhone) {
            result = confirm("Do you want to send form ?")
            if (result) button.click()
        }
    },500)
    
    
    function clickRadio(elem){    
        const boxGroup = document.querySelectorAll('.box-group input');       
        if (elem.value === 'need') {                
            boxGroup.forEach(function(item) {
                item.disabled = false;
            })               
        }
        else{
            boxGroup.forEach(function(item) {
                item.disabled = true;
            })
        }
    }


    function сonfirmSigned(event)
    {
        event.preventDefault();
        const form = event.target;

        if (!document.querySelector('[name="shipper_country"]').value){
            alert('The country field is required !');
            return false;
        }
        if (document.querySelector('[name="shipper_country"]').value !== 'Germany') {
            if (!document.querySelector('[name="shipper_city"]').value){
                alert('The city field is required !');
                return false;
            }
        }
        else{
            if (!document.querySelector('input[name="shipper_city"]').value){
                alert('The city field is required !');
                return false;
            }
        }
        
        if (!document.querySelector('[name="consignee_country"]').value){
            alert('The country field is required !');
            return false;
        }

        const phone = document.querySelector('[name="standard_phone"]'); 
        if (phone.value.length < 10 || phone.value.length > 24) {
            alert('The number of characters in the standard phone must be from 10 to 24 !');
            return false;
        }

        /*Parcel content items*/
        const parcelsQty = document.querySelector('[name="parcels_qty"]');
        if (!parcelsQty.value) parcelsQty.value = 1;
        let contentFull = false;

        if(!contentFull){
            document.querySelector('[name="item_1"]').value = "Empty";
            document.querySelector('[name="q_item_1"]').value = "0";
        } 

        /*Boxes info*/
        const needBox = $('[name="need_box"]:checked').val();        
        if (needBox === 'need') {
            $('[name="status_box"]').val('true');
            let boxString = '';
            let boxVal = 0;
            $('.box-group input').each((k,el)=>{
                boxVal += parseInt($(el).val());
            })
            if (boxVal < 1) {
                alert('PLEASE SPECIFY THE TYPE OF AT LEAST ONE BOX !');
                return false;
            }
            else{
                $('.box-group input').each((k,el)=>{
                    if(parseInt($(el).val())){
                        boxString += $(el).attr('name') +': '+ $(el).val() + '; ';
                    }                   
                })
                $('[name="comments_2"]').val(boxString);

                if (!$('[name="phone_exist_checked"]').val()) {
                    localStorage.setItem('boxString',boxString);
                }
                else{
                    localStorage.removeItem('boxString');
                }
            }            
        }
        
        form.submit();
    }

</script>

@endsection