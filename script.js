$('#view-register-form').click(function (e) {
    e.preventDefault();
    $('#login-form').fadeOut(500, function() {
        $(this).css('display', 'none');
        setTimeout(() => {
            $('#register-form').fadeIn(500);
        }, 500);
    })
})

$('#view-login-form').click(function (e) {
    e.preventDefault();
    $('#register-form').fadeOut(500, function() {
        $(this).css('display', 'none');
        setTimeout(() => {
            $('#login-form').fadeIn(500);
        }, 500);
    })
})