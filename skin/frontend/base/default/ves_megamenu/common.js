

 (function($) {
 var SidebarMenuEffects = (function() {

    function hasParentClass( e, classname ) {
        if(e === document) return false;
        if( classie.has( e, classname ) ) {
            return true;
        }
        return e.parentNode && hasParentClass( e.parentNode, classname );
    }

    function init() {
        
        $(document).ready( function(){

            var $mcontent = $('#mainmenutop .navbar .navbar-nav');
            var $mcontent_parent = $mcontent.parent();
            var is_offcanvas = $('#mainmenutop').data("offcanvas");
            if(is_offcanvas == "" || typeof(is_offcanvas) == "undefined") {
                is_offcanvas = true;
            }
            if(is_offcanvas) {
                var effect = 3;

                var $offcmenu = $('<nav id="menu-offcanvas" class="offcanvas-menu offcanvas-effect-'+effect+' hidden-lg hidden-md"><div class="menu-offcanvas-inner"></div></nav>');
                $(".menu-offcanvas-inner", $offcmenu ).append( $mcontent.clone() );

                $("body").append( $offcmenu ); 
                $(".navbar-nav", $offcmenu  ).removeClass("navbar-nav").removeClass("nav").addClass("menu-offcanvas-content");
                

                /* Append navigation on top */
                var $navigation_top = $mcontent_parent.find(".close-block");
                if($navigation_top.length > 0) {
                    $(".menu-offcanvas-inner").prepend($navigation_top.first());
                }
                /* Append site logo on top */
                /* Append search form on top */
                /* Append social links on bottom (facebook, twitter, google plus, instagram, youtube, website) and Append copyright on bottom */
                var $menufooter = $mcontent_parent.find(".menu-footer");
                if($navigation_top.length > 0) {
                    $(".menu-offcanvas-inner").append($menufooter.first());
                }

                if($navigation_top.length == 0) {
                    $(".menu-offcanvas-inner").append("<div class='button-close-menu'><i class='fa fa-times-circle-o'></i></div>");
                }

                var $btn = $("#mainmenutop .navbar-toggle, .menu-offcanvas-inner .button-close-menu");

                var eventtype = mobilecheck() ? 'touchstart' : 'click';
                    $($btn).bind( eventtype, function(e){  
                    $("#offcanvas-container").toggleClass(  "offcanvas-menu-open" ).addClass( "offcanvas-effect-"+effect );
                   
                     
                    $("#page").bind( eventtype , function (){
                        $("#offcanvas-container").toggleClass(  "offcanvas-menu-open" );
                        $("#page").unbind( eventtype );
                    } );
                    e.stopPropagation();       
                   return false;
                } );
                /*  Fix First Click Menu */
                $(document.body).on(eventtype, '#menu-offcanvas [data-toggle="dropdown"]' ,function(){
                    if(!$(this).parent().hasClass('open') && this.href && this.href != '#' && this.href != 'javascript:;' && this.href != 'javascript:void(0);'){
                        window.location.href = this.href;
                    }

                });

                $(document.body).on(eventtype, '#menu-offcanvas .open-child' ,function(){
                    var find_sub_class = ".dropdown-menu";
                    if ($(this).parent().hasClass('over')) {
                        $(this).parent().removeClass('over').find('>'+find_sub_class).removeClass("in");
                        $(this).parent().removeClass('over').find('>'+find_sub_class).slideUp(200);
                    }else{
                        $(this).parent().removeClass('over').find('>'+find_sub_class).addClass("in");
                        $(this).parent().parent().find('>li.over').removeClass('over').find('>'+find_sub_class).slideUp(200);
                        $(this).parent().addClass('over').find('>'+find_sub_class).slideDown(200);
                    }
                });

            }
           
        } );    
    }
    init();
})();
