// Очистка URL
history.pushState("", document.title, window.location.pathname);
/**
 * Switch of languages
**/ 
$(document).ready(()=>{
	$('.language-list > div').each((k,el)=>{
		if ($(el).hasClass('active-language')) {
			const target = $(el);
			target.detach();
			target.prependTo('.language-list');
		}
	});
})

function languageParent(elem) {
	const target = $(elem);
	if (target.hasClass('active-div-too')) {
		location.href = target.attr('data-locale');
	}
	else if(target.hasClass('active-language')){
		if (!target.hasClass('active-div') && !target.hasClass('child-event')) {
			target.addClass('active-div');
			$('.language-list > div').each((k,el)=>{
				if (!$(el).hasClass('active-div')) {
					$(el).addClass('active-div-too');
				}
			});
			$('.language-list div').css('display','flex');
			$('.language-list > div').css('width','153px');
			$('.language-list').css('background-color','rgba(243, 243, 243, 1)');
		}
		else{
			$('.language-list div').removeClass('child-event');		
			$('.language-list div').removeClass('active-div');
			$('.language-list div').removeClass('active-div-too');
			$('.language-list div').css('display','none');
			$('.language-list > div').css('width','99px');
			$('.language-list .active-language').css('display','flex');
			$('.language-list').css('background-color','#E2E2E2');
		}
	}
}


function languageChild(elem) {	
	const target = $(elem).parent();
	if (target.hasClass('active-div-too')) {
		location.href = target.attr('data-locale');
	}
	else if(target.hasClass('active-language')){
		if (!target.hasClass('active-div')) {
			target.addClass('active-div');
			$('.language-list > div').each((k,el)=>{
				if (!$(el).hasClass('active-div')) {
					$(el).addClass('active-div-too');
				}
			});
			$('.language-list div').css('display','flex');
			$('.language-list > div').css('width','153px');
			$('.language-list').css('background-color','rgba(243, 243, 243, 1)');
		}
		else{		
			$('.language-list div').removeClass('active-div');
			$('.language-list div').removeClass('active-div-too');
			$('.language-list div').css('display','none');
			$('.language-list > div').css('width','99px');
			$('.language-list .active-language').css('display','flex');
			$('.language-list').css('background-color','#E2E2E2');
		}
	}
}  


/**
 * Adaptive
**/ 
$(document).ready(()=>{
	const widthW = $(window).width();
	if (widthW < 992) {
		const target = $('.language-menu');
		target.removeClass('col-md-2');
		$('.guarantee').removeClass('col-md-2');
		target.detach();
		target.prependTo('.row.first-row');
	}
	
	$('ul.navbar-nav>li.nav-item').click((e)=>{		
		const target = $(e.target);
		if (target.hasClass('nav-link')) {
			e.preventDefault();
		}
		if (target.siblings('ul').css('display') == 'none') {
			target.siblings('ul').css('display','flex');
		}
		else{
			target.siblings('ul').css('display','none');
		}		
	})

	$('.temporary-account > a').click((e)=>{		
		const target = $(e.target);
		e.preventDefault();
		if (target.siblings('.dd-dropdown-menu').css('display') == 'none') {
			target.siblings('.dd-dropdown-menu').css('display','block');
		}
		else{
			target.siblings('.dd-dropdown-menu').css('display','none');
		}		
	})
})

$(window).resize(()=>{
	const widthW = $(window).width();
	if (widthW < 992) {
		const target = $('.language-menu');
		target.removeClass('col-md-2');
		$('.guarantee').removeClass('col-md-2');
		target.detach();
		target.prependTo('.row.first-row');
	}
	else{
		const target = $('.language-menu');
		target.addClass('col-md-2');
		$('.guarantee').addClass('col-md-2');
		target.detach();
		target.appendTo('.row.first-row');
	}
})


/**
 * Parcel form modal
**/ 

$('[name="not_first_order"]').change((e)=>{
	if (e.target.checked === true) $('[data-toggle="modal"]').click()		
})

var quantityClick = 0;
var quantityYes = 0;
var quantityNo = 0;
var quantitySender = 0;
var quantityRecipient = 0;

function clickAnswer(elem) {
	quantityClick++;
	if ($(elem).hasClass('yes')) quantityYes++;
	if ($(elem).hasClass('no')) quantityNo++;
	if ($(elem).hasClass('sender')) {
		quantitySender++;
		$('[name="quantity_sender"]').val(quantitySender);
	}
	if ($(elem).hasClass('recipient')) {
		quantityRecipient++;
		$('[name="quantity_recipient"]').val(quantityRecipient);
	}
	
	if (quantityClick == 1) {		
		setTimeout(
			()=>{ 
				$('#addRuParcel .question').text('Ввести те же данные получателя, которые были при предыдущем заказе?');
				if ($(elem).hasClass('yes')) {
					$(elem).removeClass('sender').addClass('recipient');
				}				
				$('#addRuParcel').modal(); 
			}, 500);		
	}
	else if(quantityClick == 2 && quantityYes > 0) {		
		setTimeout(
			()=>{ 
				$('#addRuParcel .question').text('Введите ваш номер телефона');
				$('#addRuParcel .yes').hide();
				$('#addRuParcel .no').hide();
				$('#addRuParcel .check-phone').show();
				$('#addRuParcel').modal(); 
			}, 500);					
	}
}


function philIndAnswer(elem) {
	quantityClick++;
	if ($(elem).hasClass('yes')) quantityYes++;
	if ($(elem).hasClass('no')) quantityNo++;
	if ($(elem).hasClass('sender')) {
		quantitySender++;
		$('[name="quantity_sender"]').val(quantitySender);
	}
	if ($(elem).hasClass('recipient')) {
		quantityRecipient++;
		$('[name="quantity_recipient"]').val(quantityRecipient);
	}
	
	if (quantityClick == 1) {		
		setTimeout(
			()=>{ 
				$('#philIndParcel .question').text('Enter the same recipient data that you had on the previous order?');
				if ($(elem).hasClass('yes')) {
					$(elem).removeClass('sender').addClass('recipient');
				}				
				$('#philIndParcel').modal(); 
			}, 500);		
	}
	else if(quantityClick == 2 && quantityYes > 0) {		
		setTimeout(
			()=>{ 
				$('#philIndParcel .question').text('Enter your phone number');
				$('#philIndParcel .yes').hide();
				$('#philIndParcel .no').hide();
				$('#philIndParcel .check-phone').show();
				$('#philIndParcel').modal(); 
			}, 500);					
	}
}


// Phone mask
let count_error = 0;
$('.standard-phone').on('input', function() {
	$('div.error-phone').remove();
    if ($(this).val()[0] !== '+' && $(this).val().length == 1) {
        $(this).val('+972');
    }
    else if($(this).val().length > 16){
        if ($(this).val().length == 17) {
            $(this).val($(this).val().slice(0, -1));
        }
        else{
            $(this).val('+972');
        }
    }
    else if($(this).val().length < 5){
    	$(this).val('+972');
    }
    else{
        var regexp = /^\+972[0-9]+$/i;
        if (!regexp.test($(this).val()) && count_error == 0) {
        	for (var i = $(this).val().length - 1; i >= 0; i--) {
        		if (!regexp.test($(this).val())) {
        			$(this).val($(this).val().slice(0, -1));
        		}
        		else break;
        	}       	
            count_error = 1; 
            if (location.href.indexOf('phil-ind') == -1) {
                $(this).before(`
        		<div class="error-phone">
	        		Пожалуйста, заполните поле "Номер телефона отправителя (основной)" в 
	        		международном формате, например: "+972531111111".
        		</div>`);
            }
            else{
            	$(this).before(`
        		<div class="error-phone">
	        		Please fill the box "Shipper\'s phone number (standard)" in the 
	        		international format, i.e. "+972531111111".
        		</div>`);
            }        
        } else if (!regexp.test($(this).val()) && count_error == 1 && $(this).val().length > 1) {
        	for (var i = $(this).val().length - 1; i >= 0; i--) {
        		if (!regexp.test($(this).val())) {
        			$(this).val($(this).val().slice(0, -1));
        		}
        		else break;
        	}
            if (location.href.indexOf('phil-ind') == -1) {
                $(this).before(`
        		<div class="error-phone">
	        		Пожалуйста, заполните поле "Номер телефона отправителя (основной)" в 
	        		международном формате, например: "+972531111111".
        		</div>`);
            }
            else{
            	$(this).before(`
        		<div class="error-phone">
	        		Please fill the box "Shipper\'s phone number (standard)" in the 
	        		international format, i.e. "+972531111111".
        		</div>`);
            }
        } else if ($(this).val().length < 5 || regexp.test($(this).val())) {
            count_error = 0;
        }
    }            
});

