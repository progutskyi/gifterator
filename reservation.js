function showButtonData(a) {
	
}

$("[data-presentId]").on("click", function () {
	
	var text = $(this).data("presentid");
	
	$("input[name='presentId']").attr("value", text);
	
	$("#presentModal").modal("show");
})

$("#btn-reserve").on("click", function () {

	var formData = $("#form-reserve").serialize();
	
	$.post(document.URL, formData, function () {
		
		$("#presentModal").modal("hide");
		
		window.location.href = document.URL;
		 
	});
})