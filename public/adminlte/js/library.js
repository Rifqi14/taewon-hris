function blockMessage(element,message,color){
	$(element).block({
        message: '<span class="text-semibold"><span class="loading dots"></span>&nbsp; '+message+'</span>',
        overlayCSS: {
            backgroundColor: color,
            opacity: 0.8,
            cursor: 'wait'
        },
        css: {
            border: 0,
            padding: '10px 15px',
            color: '#fff',
            width: 'auto',
            '-webkit-border-radius': 2,
            '-moz-border-radius': 2,
            backgroundColor: '#333'
        }
    });
}