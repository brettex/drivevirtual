/*---LEFT BAR ACCORDION----*/
$(function() {
    $('#nav-accordion').dcAccordion({
        eventType: 'click',
        autoClose: true,
        saveState: true,
        disableLink: true,
        speed: 'slow',
        showCount: false,
        autoExpand: true,
//        cookie: 'dcjq-accordion-1',
        classExpand: 'dcjq-current-parent'
    });
});


jQuery(document).ready( function($){
	
	$('#logout').on('click', function(){
		logOut();
	});
	
});

var Script = function () {


//    sidebar dropdown menu auto scrolling

    jQuery('#sidebar .sub-menu > a').click(function () {
        var o = ($(this).offset());
        diff = 250 - o.top;
        if(diff>0)
            $("#sidebar").scrollTo("-="+Math.abs(diff),500);
        else
            $("#sidebar").scrollTo("+="+Math.abs(diff),500);
    });



//    sidebar toggle

    $(function() {
        function responsiveView() {
            var wSize = $(window).width();
            if (wSize <= 768) {
                $('#container').addClass('sidebar-close');
                $('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                $('#container').removeClass('sidebar-close');
                $('#sidebar > ul').show();
            }
        }
        $(window).on('load', responsiveView);
        $(window).on('resize', responsiveView);
    });

    $('.fa-bars').click(function () {
        if ($('#sidebar > ul').is(":visible") === true) {
            $('#main-content').css({
                'margin-left': '0px'
            });
            $('#sidebar').css({
                'margin-left': '-210px'
            });
            $('#sidebar > ul').hide();
            $("#container").addClass("sidebar-closed");
        } else {
            $('#main-content').css({
                'margin-left': '210px'
            });
            $('#sidebar > ul').show();
            $('#sidebar').css({
                'margin-left': '0'
            });
            $("#container").removeClass("sidebar-closed");
        }
    });

// custom scrollbar
    $("#sidebar").niceScroll({styler:"fb",cursorcolor:"#4ECDC4", cursorwidth: '3', cursorborderradius: '10px', background: '#404040', spacebarenabled:false, cursorborder: ''});

    $("html").niceScroll({styler:"fb",cursorcolor:"#4ECDC4", cursorwidth: '6', cursorborderradius: '10px', background: '#404040', spacebarenabled:false,  cursorborder: '', zindex: '1000'});

// widget tools

    jQuery('.panel .tools .fa-chevron-down').click(function () {
        var el = jQuery(this).parents(".panel").children(".panel-body");
        if (jQuery(this).hasClass("fa-chevron-down")) {
            jQuery(this).removeClass("fa-chevron-down").addClass("fa-chevron-up");
            el.slideUp(200);
        } else {
            jQuery(this).removeClass("fa-chevron-up").addClass("fa-chevron-down");
            el.slideDown(200);
        }
    });

    jQuery('.panel .tools .fa-times').click(function () {
        jQuery(this).parents(".panel").parent().remove();
    });


//    tool tips

    $('.tooltips').tooltip();

//    popovers

    $('.popovers').popover();



// custom bar chart

    if ($(".custom-bar-chart")) {
        $(".bar").each(function () {
            var i = $(this).find(".value").html();
            $(this).find(".value").html("");
            $(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }


}();

/** 
	Function to check whether the user is logged in or not
	amd whether their session ID is still valid

	@variable - onlyAdmin, boolean - whether this page is only accessible by admins
**/

function checkLogin(onlyAdmin){
	
	var userID = localStorage.UserID;
	var sessionID = localStorage.SessionID;
	
	// Reset the Session variable in the DB
	$.ajax({
		url: 'assets/includes/userFunctions.php?action=checkLogin&userID='+ userID + '&sessionID=' + sessionID,
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		timeout: 15000,
		error: function(){
			console.log('Fail');
			},
		cache: false,
		success: function(data, status){
			var isAdmin = data.admin;
			var loggedIn = data.logged;
			//Cant authenticate, redirect	
			console.log(loggedIn);
			console.log('Success');
			if(loggedIn == 'false'){ 
				location.href='index.html';
			} else if(onlyAdmin == true && isAdmin == 'false'){
				//Admin only page!
				location.href='index.html';
			}
				
				
		}
		
	});
	
	
}

/** Delete session variable and redirect to the
    login page
	
**/
function logOut(){
	
	// localStorage Reset
	var userID = localStorage.UserID;
	
	// Reset the Session variable in the DB
	$.ajax({
		url: 'assets/includes/userFunctions.php?action=logOut&userID='+ userID,
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		timeout: 15000,
		error: function(){},
		cache: false,
		success: function(data, status){
			localStorage.SessionID = localStorage.FtpID = localStorage.UserID = '';
			//Redirect
			location.href='index.html';
		}
		
	});
	
	
}

/** Validate the users login and redirect them to
	the folder page if its a success
	
**/
function logIn(){
	
	var username = $('#username').val();
	var password = $('#password').val();
	var msg = ''
	
	// Reset the Session variable in the DB
	$.ajax({
		url: 'assets/includes/userFunctions.php?action=logIn&userID='+ username,
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		type: 'POST',
		data:{'password': password},
		timeout: 15000,
		error: function(){},
		cache: false,
		success: function(data, status){
			var msg = data.result;
			if(msg == 'success'){
				localStorage.SessionID = data.sessionID;
				localStorage.FtpID = data.ftp;
				localStorage.Company = data.company;
				localStorage.UserID = data.ID;
				//Redirect
				location.href='folder.html' 
			} else {
				$('#login-msg').html(msg).fadeIn();
			}
			
		}
		
	});
	
}

/** 
	Reset the Users password
	
**/
function retrievePassword(){
	
	var email = $('#email').val();
	var msg = '';
	
	// Reset the Session variable in the DB
	$.ajax({
		url: 'assets/includes/userFunctions.php?action=getPassword&email='+ email,
		dataType: 'jsonp',
		jsonp: 'jsoncallback',
		timeout: 15000,
		error: function(){},
		cache: false,
		success: function(data, status){
			var msg = data.msg;
			if(data.result == 'success'){
				$('#pass-msg').removeClass('alert-danger').html(msg).fadeIn();
			} else {
				$('#pass-msg').addClass('alert-danger').html(msg).fadeIn();
			}
			
		}
		
	});
	
}

/** Functino to check a connection

	@variable - string, host
	@variable - string, user
	@variable - string, password
	
**/
function checkFTP(host, user, pass){

	var html = 'Sorry, could not connect to your FTP using current credantials';
	
  $.ajax({
	url: 'assets/includes/getDirectories.php?check=true&Host='+ host + '&FTPUser=' + user,
	dataType: 'jsonp',
	jsonp: 'jsoncallback',
	type:'POST',
	data:{'password': pass },
	timeout: 15000,
	error: function(){

		$('.alert').addClass('alert-danger').html(html);
	},
	cache: false,
	success: function(data, status){
		if(data.msg){
			html = 'Test Connection Successful!';
			$('#login-msg').removeClass('alert-danger').addClass('alert-success').fadeIn().html(html);
			$('#register').removeAttr('disabled');
		} else {
			$('#login-msg').addClass('alert-danger').fadeIn().html(html);
		}
	}
	
  });
}

function populateCompanyName(){
	var company = "Guest";
	if(localStorage.Company != undefined || localStorage.Company != null){
		company = localStorage.Company;
	}
	
	$('#company').html(company);	
}