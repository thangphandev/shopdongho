	$(function() {
		$.ajax({
			url: 'Vindex/viewCart' ,
			type: 'GET',
			global: false,
		})
		.done(function(data) {
			var arr =$(data).find('input#_tt').val();		
			$('.num-cart').text(arr);
		});
		$.ajax({
			url: 'Vindex/viewCartPop',
			type: 'GET',
			global: false,
		})
		.done(function(data) {
			$('.scart').html(data);
		});
		$(document).on('click', '._cart_buynow', function(event) {
			event.preventDefault();
			var $id = $(this).attr('dt-id');
			$count = $('#qty_cart').val();
			$count = ($count==undefined ||$count<1 ) ? 1:$count;
			$.ajax({
				url: 'Vindex/addCart/'+$id+'/'+$count,
				type: 'GET',
			})
			.done(function(data) {
				// reloadCart(data);
				location.href='gio-hang';
			})
			.fail(function() {
				
			})
			.always(function() {
				
			});
			
		});

		$(document).on('submit', 'form.frm-pro', function(event) {
			event.preventDefault();
			$.ajax({
				url: $(this).attr("action"),
				type: 'POST',
				data:$(this).serialize()
			})
			.done(function(data) {
				reloadCart(data);
			})
			.fail(function() {
				
			})
			.always(function() {
				
			});
			
		});

		$(document).on('click','.destroy_cart', function(event) {
			event.preventDefault();
			$.ajax({
				url: 'Vindex/desCart',
				type: 'GET',
			})
			.done(function(data) {
				reloadCart(data);
			})
			.fail(function() {
				
			})
			.always(function() {
				
			});
			
		});

		$(document).on('click','._cart_delete', function(event) {
			event.preventDefault();
			var $id = $(this).parent().parent().find('input[name=rowid]').val();
			$.ajax({
				url: 'Vindex/delCart/'+$id,
				type: 'GET',
			})
			.done(function(data) {
				reloadCart(data);

			})
			.fail(function() {
				
			})
			.always(function() {
				
			});
			
		});

		var k = $('meta[name=csrf-name]').attr('content');
		var v = $('meta[name=csrf-token]').attr('content');
		var __obj = {};
		__obj[k]=v;
		$.ajaxSetup({

			data: __obj,
		});
		var lasttime = 0;
		var vbreak = false;
		$(document).on('click', '._cart_updateall', function(event) {
			event.preventDefault();
			updateCart();
			
		});

		$('._cart_city').change(function(event) {
			$.ajax({
				url: 'Vindex/getCity',
				type: 'POST',
				data: {id:$(this).val()},
			})
			.done(function(data) {
				try{
					var json = JSON.parse(data);
					if(json.code==200){
						$.simplyToast(json.message, 'success');
						var arr = json.data;
						var str = "";
						for(var i =0;i<arr.length;i++){
							str +="<option value='"+arr[i].id+"'>"+arr[i].name+"</option>";
						}
						$('._cart_provide').html(str);
					}
					else{
						$.simplyToast(json.message, 'danger');
					}
				}
				catch(ex){

				}
			})
			.fail(function() {
			})
			.always(function() {
			});
			
		});

		$(document).on('submit', 'form._cart_frm_order', function(event) {
			event.preventDefault();
			if(validateFormCart(this)){
				$.ajax({
					url: 'Vindex/order',
					type: 'POST',
					data: $(this).serialize(),
				})
				.done(function(data) {

					try{
						var json = JSON.parse(data);
						if(json.code==200){
							window.location.href="thanh-cong";
						}
						else{
							$.simplyToast(json.message, 'danger');
						}
					}
					catch(ex){

					}
				})
				.fail(function() {
				})
				.always(function() {
				});
				
			}
		});
		var _obj = {};
		_obj.code = 200;
		reloadCart(JSON.stringify(_obj));

	});

	function validateFormCart(frm){
		var inputs= $(frm).find('input');
		for (var i = 0; i < inputs.length; i++) {
			var item = $(inputs[i]);
			if(item!=undefined && item.val().trim()==""){
				$.simplyToast("Vui lòng nhập " + item.attr('placeholder'), 'danger');
				return false;
			}
		}
		return true;
	}
	function reloadCart(data){
		try{
			var json = JSON.parse(data);
			toastr.clear();
			if(json.code==200){
				if(json.message!=undefined){
					toastr['success'](json.message);
				}
				
				$.ajax({
					url: 'Vindex/viewCart',
					type: 'GET',
					global: false,
				})
				.done(function(data) {
					$('.box-cart-content').html(data);
					var arr =$('input#_tt').val();
					if(arr == undefined){
						arr = 0;
					}
					$('.num-cart').text(arr);
				});
				$('.scart-list').load('Vindex/viewCartPop');
				
			}
			else{
				toastr['warning'](json.message);
			}
		}
		catch(ex){

		}
	}
	
	function updateCart(){
		var _obj = {};
			_obj['cart'] = JSON.stringify(updateAllCart());
			$.ajax({
				url: 'Vindex/updateCart',
				type: 'POST',
				data: _obj,
			})
			.done(function(data) {
				reloadCart(data);
			})
			.fail(function() {
				
			})
			.always(function() {

		});
	}
	function updateAllCart(){
		var trs = $('.row-item-cart');
		var send =[];
		for (var i = 0; i < trs.length; i++) {
			var item = $(trs[i]);
			var rowid = item.find('input[name=rowid]').val();
			var qty = item.find('input.qty').val();
			var obj = new Object();
			obj.rowid = rowid;
			obj.qty = qty;
			send.push(obj);
		}
		return send;
	}