// JavaScript Document





function make_maskes(){
	$.mask.definitions['~'] = "[+-]";
	$('[type="date"]').mask("99/99/9999",{completed:function(){ }});
	/*$("#phone").mask("(999) 999-9999");
	$("#phoneExt").mask("(999) 999-9999? x99999");
	$("#iphone").mask("+33 999 999 999");
	$("#tin").mask("99-9999999");
	$("#ssn").mask("999-99-9999");
	$("#product").mask("a*-999-a999", { placeholder: " " });
	$("#eyescript").mask("~9.99 ~9.99 999");
	$("#po").mask("PO: aaa-999-***");
	$("#pct").mask("99%");
	*/
	$("input").blur(function() {
		//$("#info").html("Unmasked value: " + $(this).mask());
	}).dblclick(function() {
		$(this).unmask();
	});
	$( "label i[title]" ).tooltip();

	$.each($( '[type="date"]' ),function(){
		var tar = $(this);
		if( typeof(tar.data('end')) == "undefind" ){
			tar.data('end',"-10Y");
		}
		$( '[type="date"]' ).datepicker({ changeMonth: true,changeYear: true, minDate:"-80Y", maxDate: tar.data('end') });
	});
	
}




$(document).ready(function() {
	make_maskes();
	
	if($('.datagrid').length){
		$.each($('.datagrid'),function(){
			var datatable = $(this)
			var table = datatable.dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers"
			});
		});
	}
	var uitabs = $( ".uitabs" ).tabs();
	var uiaccordions = $( ".accordions" ).accordion({active:false ,collapsible: true,heightStyle: "content",
	beforeActivate:function( event, ui ){

		}
	});
	var uibuttons = $( ".buttons" ).button();
	
	$('.deleteRecord').on('click',function(e){
		e.preventDefault();
		e.stopPropagation() 
		var trig=$(this);
		var tar=$(this).closest('h3');
		if(tar.next('div').find('.remove').val()>0){
			trig.find('span').text('[x]');
			tar.removeClass('removing');
			tar.css('opacity','1.0');
			tar.next('div').css('opacity','1.0').find('.remove').val('');
		}else{
			trig.find('span').text('[-]');
			tar.addClass('removing');
			tar.css('opacity','.85');
			tar.next('div').css('opacity','.15').find('.remove').val('1');
		}
	});
});