function menuToggle() {
    const toggleMenu = document.querySelector('.menu');
    const toggleMenuicon = document.querySelector('.menu-accord-ico');
    toggleMenu.classList.toggle('active');
    toggleMenuicon.classList.toggle('open');
}


(function ($) {

$(document).ready(function() {


	
	
	
$(document).on('change', '.fl_inp', function () {

$(this).parents('.inp-val-wrap').find('.invalid-feedback').removeClass('visible');
var filename = $(this).val().replace(/.*\\/, "");

$(this).parents(".file-form-wrap").find(".file-name1").html(filename);
$(this).parents('.my-btn').css('backgroundColor', '#008000');

	

});	
	
	
	
	

	
	
 var accordions = document.getElementsByClassName("accordion");

for (var i = 0; i < accordions.length; i++) {
  accordions[i].onclick = function() {
    this.classList.toggle('is-open');

    var content = this.nextElementSibling;
    if (content.style.maxHeight) {
      // accordion is currently open, so close it
      content.style.maxHeight = null;
    } else {
      // accordion is currently closed, so open it
      content.style.maxHeight = content.scrollHeight + "px";
    }
  }
}	


	
	
	
	
		//product form preference
$('.tr-btn').on('click', function (e) {
  e.preventDefault();
 
  var $this = $(this);
  var content = $this.data('content');
  console.log(content);

  $('.trener-info').html(content);
});

	
	
	
		
	
	
	
	$(".faq__item").on("click", function(){
		$(this).toggleClass("active")
		if($(this).hasClass("active")){
			$(this).children(".faq__answer").fadeIn(100);
			
		}
		else{
			$(this).children(".faq__answer").fadeOut(0);
			
		}
	});	
	


	
	
	
	
	
	
	
	
	

	
	

	




	/*var max_col_height = 0; // максимальная высота, первоначально 0
	$('.programm-content').each(function(){ // цикл "для каждой из колонок"
		if ($(this).height() > max_col_height) { // если высота колонки больше значения максимальной высоты,
			max_col_height = $(this).height() + 30; // то она сама становится новой максимальной высотой
		}
	});
	$('.programm-content').height(max_col_height); // устанавливаем высоту каждой колонки равной значению максимальной высоты
*/











    
  
    
    



   
 $(".f-phone").mask("+7(999) 999-9999");   
    
	
$(".slideT").hide();
    $(".more").click(function (e) {
		e.preventDefault();
	
      // можно и иначе выбрать все другие блоки
      // вот так $(".pointer").not(this)
	  console.log($(this).siblings(".slideT"));
      $(this).siblings(".slideT")
        .slideDown("slow");
      $(this)
      	.children()
        .slideDown("slow");
    });	
	
	$('.content_toggle').click(function(e){
		e.preventDefault();
		 $(this).siblings('.content_block').toggleClass('hide');	
		if ($(this).siblings('.content_block').hasClass('hide')) {
			$(this).html('Подробнее <img src="img/more-arr.png" alt="">');
		} else {
			$(this).html('Скрыть <img src="img/more-arr2.png" alt="">');
		}		
		return false;
	});		
	
	
(function () {

    const smoothScroll = function (targetEl, duration) {
        /*const headerElHeight =  document.querySelector('.header-n').clientHeight;*/
        let target = document.querySelector(targetEl);
        let targetPosition = target.getBoundingClientRect().top/* - headerElHeight*/;
        let startPosition = window.pageYOffset;
        let startTime = null;
    
        const ease = function(t,b,c,d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t + b;
            t--;
            return -c / 2 * (t * (t - 2) - 1) + b;
        };
    
        const animation = function(currentTime){
            if (startTime === null) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const run = ease(timeElapsed, startPosition, targetPosition, duration);
            window.scrollTo(0,run);
            if (timeElapsed < duration) requestAnimationFrame(animation);
        };
        requestAnimationFrame(animation);

    };

    const scrollTo = function () {
        const links = document.querySelectorAll('.js-scroll');
        links.forEach(each => {
            each.addEventListener('click', function () {
                const currentTarget = this.getAttribute('href');
                smoothScroll(currentTarget, 1000);
            });
        });
    };
    scrollTo();
}());	
	
	
	
	
	
	
	
	
	
	var $data_t;
var $data_tooltip;
  
	 $( ".pop__close" ).click(function(eventObject){

		  $( ".tooltip2" ).removeClass('shows');

	  });
	 
	 
	  $( "[data-tooltip]" ).click(function(e){  
		e.preventDefault();
		$data_t = $(this).siblings(".tooltip2");
		$( ".tooltip2" ).not($data_t).removeClass('shows');
		$data_tooltip = $(this).attr("data-tooltip");
		$data_t.children(".text").html($data_tooltip)
        $data_t.toggleClass('shows');
		
	  });

	
	
	
	
	



	
/*$(".f-form").submit(function(e) { //устанавливаем событие отправки для формы с id=form
                e.preventDefault();
				
                var form_data = $(this).serialize(); //собераем все данные из формы
                $.ajax({
                type: 'POST', //Метод отправки
                url: 'mail.php', //путь до php фаила отправителя
                data: form_data,
                        success: function(data){ 
						  $("#callModal").modal('hide');
                          window.location.href = "thanks.html";
						  
						  
                        }
                });
        });	
	*/	
		
		
		
function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
};
		
		
		
		
$(document).on('click', '.js--form-submit', function () {
	
		

        var btn = $(this);
        var form = btn.closest('.main-form');
        var errors = false;
		
        $(form).find('.required').each(function () {
            var inp = $(this);
            var val = inp.prop('value');
            if (val == '' || val == '0') {
                inp.addClass('error');
				$(this).siblings('.error-p').addClass('visible');
                errors = true;
            } else {
				if (inp.hasClass('inp-mail')) {
                    if (validateEmail(val) == false) {
                        inp.addClass('error');
						$(this).siblings('.error-p').addClass('visible');
                        errors = true;
						
                    }
                }
                if (inp.hasClass('inp-phone')) {
                    if (val.length < 6) {
                        inp.addClass('error');
						$(this).siblings('.error-p').addClass('visible');
                        errors = true;
                    }
                }
            }
        });

        if (errors == false) {

				
            var button_value = btn.val();
            btn.val('Отправляем...');

            var method = form.attr('method');
            var data = form.serialize();
            
			var form_id = form.children('.form-id').val();
			
			var formData = new FormData(form[0]);

            $.ajax({
                type: method,
                url: "mail.php",
                data: formData,
				contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
				processData: false,
                success: function (data) {
					console.log (data);
					 window.location.href = "thanks.html";
					
					
	
	$("form").trigger('reset');
	
					
					
                },
                error: function (data) {
                    btn.val('Ошибка');
                    setTimeout(function () {
                        btn.val(button_value);
                    }, 2000);
                }
            });
			
        }

        return false;
    });

    $('.inp').focus(function () {
        $(this).removeClass('error');
		$(this).siblings('.error-p').removeClass('visible');
    });			
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
	
var core = {

	navMenu: function() {
		if (!$("header").hasClass(".light-navbar")) {
			$(window).scroll(function() {
				// 100 = The point you would like to fade the nav in. 
				if ($(window).scrollTop() > 100) {
					$('.light-navbar').addClass('show');
				} else {
					$('.light-navbar').removeClass('show');
				}
			});
		}
	},

	
	bootstrapParent: function() {

		$('#TopNav .dropdown > a').click(function() {
			$(this).css( "color", "#d31145" ); 
			var location = $(this).attr('href'); 
			window.location.href = location;
			return false;
		});
		
		$('#TopNav a').click(function() { 
			//$(this).css( "color", "#d31145" ); 
			/*var location = $(this).attr('href'); 
			window.location.href = location;
			return false; */
		});

		$("#TopNav .caret").click(function() {
			$(this).next().toggleClass("highlight");
		});

		$("#TopNav .caret").click(function() {
			$(this).toggleClass("copen");
		});

		
		
		$(".navbar-toggler").click(function() {
			$('#navbarNav').toggleClass("show");
		});
		
		
		

	}

};

jQuery(function() {
	// Vars
	

	core.navMenu();
	core.bootstrapParent();
});	
	


$('.moscow-map').on('click', function (e) {
  e.preventDefault();
  
   $('.footer-phone').html('<a class="" href="tel:+79773309998">+7(977) 330-9998</a>');
  
  $('.footer-point').html('Филиал. Москва, проспект Мира,<br>д.102.Бизнес центр «Парк Мира»<a href="" data-toggle="modal" data-target="#callModal" class="" >предварительная запись</a>');
  
  
  var $this = $(this);
  $this.addClass('active');
  $('.novosib-map').removeClass('active');
  var content = $this.data('content');
  console.log(content);

  $('#map').html('<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A61d655ffa44beea5b638aef8f158d7b8af850a97babe0ef223f6f75962eb3cfa&amp;source=constructor" width="100%" height="480" frameborder="0"></iframe>');
 
});	

	
	
		//product form preference
$('.novosib-map').on('click', function (e) {
  e.preventDefault();
  
  $('.footer-phone').html('<a href="tel:+73832135770">+7(383)213-57-70</a><br><a href="tel:+79139855770">+7(913)985-57-70</a>');
  
  $('.footer-point').html('Новосибирск, ул. Кутателадзе, д. 4<a href="" data-toggle="modal" data-target="#callModal" class="" >предварительная запись</a>');
  
  
 
  var $this = $(this);
  $this.addClass('active');
  $('.moscow-map').removeClass('active');
  var content = $this.data('content');
  console.log(content);

  $('#map').html('<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A73201d45dc9fdd7dc55f08f7df0cd09b0bcbefe83eeed57ac4e6cf5c87adaaba&amp;source=constructor" width="100%" height="480" frameborder="0"></iframe>');
	
});	
	
	

	
	


 $(window).load(function() {
	
	var event = function() {
  $("#map").html('<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Acb233c9f629a6f12957f29ba0e8d19b66079f3cb3dcff52ba4f7333f885c6613&amp;source=constructor" width="100%" height="360" frameborder="0"></iframe>');
  window.removeEventListener('scroll', event);
}

window.addEventListener('scroll', event);

}); 









});


})(jQuery);