jQuery(function( $ ) {
	'use strict';
	var prices = [];

	function get_update_price(custom_price) { 
		let product_id = parseInt($('input[name="woocv_product_id"]').val());
		let priceHolder = $('.summary.entry-summary').find('.price');

		if(product_id){
			$.ajax({
				type: "get",
				url: woocv_ajax.ajaxurl,
				data: {
					action: "get_woo_price",
					product_id: product_id,
					custom_price: custom_price
				},
				beforeSend: function(){
					$(document).find('div#lr-loader-loading').css('display', 'flex');
				},
				success: function (response) {
					$(document).find('div#lr-loader-loading').css('display', 'none');
					priceHolder.html(response);

					$([document.documentElement, document.body]).animate({
						scrollTop: $(".summary.entry-summary").offset().top-100
					}, 500);
				}
			});
		}
	}

	$(document).find(".wcv_field_top").each(function(){
		$(this).on("click", function(){
			$(this).parents(".wcv_field ").toggleClass("woocv_close");
		});
	});

	$(document).find("#woocv_variation").find("button").on("click", function(e){
		e.preventDefault();
	});

	// Text button select
	$(document).find(".woocv_text_btn").each(function(){
		$(this).on("click", function(){
			$(this).siblings(".woocv_text_btn.active").removeClass("active");

			$(this).toggleClass("active");
			let itemId = $(this).parent().find(".woocv_text_btn.active").data("id");
			$(this).parent().find("input.btnInpValue").val(itemId);

			let tempid = $(this).parent().find("input.btnInpValue").data("tempid");
			prices = prices.filter(function(el){
				return el.id !== tempid;
			});

			let p = $(this).parent().find(".woocv_text_btn.active").data("price");
			if(itemId !== undefined){
				let price = {
					id: tempid,
					price: ((p) ? parseFloat(p) : 0)
				}
				prices.push(price);
			}
		});
	});

	$('.colorInpValue').minicolors({defaultValue:''});

	// Color button select
	$(document).find(".colorInpValue").on("input", function(){
		let itemId = $(this).data("tempid");
		
		prices = prices.filter(function(el){
			return el.id !== itemId;
		});
		
		if($(this).val() !== ""){
			let p = $(this).data("price");
			if(itemId !== undefined){
				let price = {
					id: itemId,
					price: ((p) ? parseFloat(p) : 0)
				}
				prices.push(price);
			}
		}
	});

	// User color choose
	$(document).find(".woocv_available_color").each(function(){
		let colors = $(this).data("support");
		colors = colors.split(",");
		$(this).removeAttr("data-support");

		$(this).colorPick({
			'initialColor' : '',
			'allowCustomColor':false,
			'allowRecent':false,
			'paletteLabel': 'Available Colors',
			'palette': colors,
			'onColorSelected': function() {
				let itemValue = $(this.element).parent().find("input.available_color_v");
				itemValue.val(this.color);
				this.element.css({'backgroundColor': this.color, 'color': this.color});
				let id = $(this.element).data("id").toString();
				let cp = $(this.element).data("price");
				if($(this.element).parent().find("input.available_color_v").val() !== ""){
					let price = {
						id: id,
						price: ((cp) ? parseFloat(cp) : 0)
					}
					prices = prices.filter(function(el){
						return el.id !== id;
					});
					prices.push(price);
				}
			}
		});
	});

	$(document).find(".clearColor").each(function(){ // Clear selected color
		$(this).on("click", function(){
			$(this).parent().find("input.available_color_v").val("");
			$(this).parent().find(".woocv_available_color").css({'backgroundColor': "#f6f6f6"});
			let fid = $(this).parent().find(".woocv_available_color").data("id").toString();
			prices = prices.filter(function(el){
				return el.id !== fid;
			});
		});
	});

	$(document).find(".wcv_empty_field").each(function(){ // Empty inputs
		$(this).find("input[type='text']").on("blur", function(){
			let element = $(this);

			prices = prices.filter(function(el){
				return el.id !== element.data("id");
			});

			let ep = element.data("price");
			if($(this).val().length > 0){
				let price = {
					id: element.data("id"),
					price: ((ep) ? parseFloat(ep) : 0)
				}
				prices.push(price);
			}
		});
	});

	$(document).find(".calculate_woocv_total").on("click", function(){
		let numaricPrices = 0;
		prices.forEach(f => {
			numaricPrices += f.price;
		});
		get_update_price(numaricPrices);
	});
});
