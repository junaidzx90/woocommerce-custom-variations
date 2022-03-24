const woocv = new Vue({
	el: "#woocv",
	data: {
		isDisabled: true,
		variation_title: '',
		variation_switch: '',
		woocvFields: [],
		productSelection: {
			type: "single",
			data: [""],
		}
	},
	methods: {
		changeProduct: function () {
			this.productSelection.data = [""];
		},
		add_woocv_field: function (event) {
			event.preventDefault();
			let woocv_field = {
				id: (new Date()).getTime(),
				title: "Untitled",
				fieldsData: []
			}
			this.woocvFields.push(woocv_field);
		},
		add_woocv_field_item: function (id, event) {
			event.preventDefault();
			let field = this.woocvFields.find(el => el['id'] === id);
			if (field !== undefined) {
				let fieldItem = {
					id: (new Date()).getTime(),
					type: "unselected",
					label: "",
					placeholder: "",
					color: "#000000",
					price: "",
					longtxt: ""
				}
				field.fieldsData.push(fieldItem);
			}
		},
		get_itemTypeFullText: function (type) {
			switch (type) {
				case 'unselected':
					return 'Unselected';
					break;
				case 'empty_input':
					return 'Empty Input';
					break;
				case 'color_input':
					return 'Color Input';
					break;
				case 'button_show':
					return 'Button Show';
					break;
				case 'color_show':
					return 'Color Show';
					break;
				case 'longtext_field':
					return 'Long Text';
					break;
			}
		},
		changeFieldType: function (fid, fiid) {
			let field = this.woocvFields.find(el => el['id'] === fid);
			if (field !== undefined) {
				let fieldItem = field.fieldsData.find(el => el['id'] === fiid);
				if (fieldItem !== undefined) {
					fieldItem.label = "";
					fieldItem.placeholder = "";
					fieldItem.color = "#000000";
					fieldItem.price = "";
					fieldItem.longtxt = "";
				}
			}
		},
		collapsable_elements: function (which, event) { 
			switch (which) {
				case 'field':
					jQuery(event.target).parents('.wcv_filed').toggleClass('datanone');
					break;
				case 'fieldItem':
					jQuery(event.target).parents('.woocv_filed_item').toggleClass('datanone');
					break;
			}
		},
		remove_field: function (id) {
			if (confirm("The field will be removed with the data!")) {
				this.woocvFields = this.woocvFields.filter(function( obj ) {
					return obj.id !== id;
				});
			}
		},
		remove_field_item: function (fid, fiid) {
			let field = this.woocvFields.find(el => el['id'] === fid);
			if (field !== undefined) {
				if (confirm("The item will be removed!")) {
					field.fieldsData = field.fieldsData.filter(function (obj) {
						return obj.id !== fiid;
					});
				}
			}
		},
		makeSortableFields: function () { 
			let fields = jQuery(document).find('#vf_contents').children('.wcv_filed');
						
			let fieldIds = [];
			jQuery.each(fields, function () { 
				fieldIds.push(jQuery(this).data('id'));
			});
			
			let sortedArr = [];
			fieldIds.forEach(element => {
				let field = woocv.woocvFields.find(el => el['id'] === element);
				sortedArr.push(field.id);
			});

			this.woocvFields.sort(function (a, b) {
				return sortedArr.indexOf(a.id) - sortedArr.indexOf(b.id);
			});

		},
		makeSortableFieldItems: function (fid) { 
			let fieldObj = this.woocvFields.find(el => el['id'] === fid);

			if (fieldObj !== undefined) {
				let fields = jQuery(".wcv_filed[data-id="+fid+"]").find('.woocv_filed_items').children('.woocv_filed_item');
						
				let fieldIds = [];
				jQuery.each(fields, function () { 
					fieldIds.push(jQuery(this).data('id'));
				});
				
				let sortedArr = [];
				fieldIds.forEach(element => {
					let field = fieldObj.fieldsData.find(el => el['id'] === element);
					if (field !== undefined) {
						sortedArr.push(field.id);
					}
				});

				fieldObj.fieldsData.sort(function (a, b) {
					return sortedArr.indexOf(a.id) - sortedArr.indexOf(b.id);
				});
			}
		},
		sortableData: function () {
			if (this.woocvFields.length > 0) {
				jQuery("#vf_contents").sortable({
					axis: "y",
					containment: "parent",
					placeholder: 'sortable-placeholder',
					opacity: 0.6,
					animation: 200,
					start: function (event, ui) {
						ui.item.toggleClass("highlight");
					},
					update: function (event, ui) {
						ui.item.toggleClass("highlight");
						woocv.makeSortableFields();
					},
				});
				jQuery(".woocv_filed_items").sortable({
					axis: "y",
					containment: "parent",
					placeholder: 'sortable-placeholder2',
					opacity: 0.6,
					animation: 200,
					start: function (event, ui) {
						ui.item.toggleClass("highlight");
					},
					update: function (event, ui) {
						ui.item.toggleClass("highlight");
						let parentField = jQuery(ui.item).parents('.wcv_filed').data("id");
						woocv.makeSortableFieldItems(parentField);
					},
				});
			}
		},
		save_woocv_form_data: function (event) {
			event.preventDefault();
			let data = {
				title: this.variation_title,
				switch: this.variation_switch,
				products: this.productSelection,
				fields: this.woocvFields
			}

			jQuery.ajax({
				type: "post",
				url: admin_ajax.ajaxurl,
				data: {
					action: "save_woocv_data",
					nonce: admin_ajax.nonce,
					data: data,
					variation_id: admin_ajax.variation_id
				},
				dataType: "json",
				success: function (response) {
					if (response.redirect) {
						location.href = response.redirect;
					}
					if (response.reload) {
						location.reload();
					}
				}
			});
		}
	},
	updated: function () { 
		this.sortableData();
	},
	mounted: function () {
		if (admin_ajax.variation_id !== null) {
			jQuery.ajax({
				type: "get",
				url: admin_ajax.ajaxurl,
				data: {
					action: "get_woocv_form_data",
					nonce: admin_ajax.nonce,
					variation_id: admin_ajax.variation_id
				},
				dataType: "json",
				success: function (response) {
					if (response.success) {
						let data = response.success;
						woocv.woocvFields = data.fields_data;
						woocv.productSelection = data.products;
						woocv.variation_switch = data.switch;
						woocv.variation_title = data.variation_title;
					}
					woocv.isDisabled = false;
				}
			});
		}
		this.sortableData();
	}
});