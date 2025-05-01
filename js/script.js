var win = $(window);
var header = $('header');
var _doc = $(document);
var HEADER = {
    menuMoblie: function() {
        var menu_html = $('.main-nav').children('ul').clone(true);
        var menu_mobile = $('.mobile-menu');
        var box_mb = menu_mobile.find('.box_mb_menu');
        var search_btn = $('.d_seach_btn.toggle_seach')
        var form_search = $('.h-fast-search .form_fast_search');
        box_mb.html(menu_html);
        $('.menu-btn').click(function() {
            menu_mobile.toggleClass('active');
        })
        if (win.width() < 992) {
            win.click(function(e) {
                if (box_mb.has(e.target).length == 0 && !box_mb.is(e.target) && $('header .menu-btn').has(e.target).length == 0 && !$('header .menu-btn').is(e.target)) {
                    menu_mobile.removeClass('active')
                }
                if (form_search.has(e.target).length == 0 && !form_search.is(e.target) && search_btn.has(e.target).length == 0 && !search_btn.is(e.target)) {
                    $('.h-fast-search').removeClass('active');
                }
            });
        }
        $(".mobile-menu ul li").each(function() {
            if ($(this).find("ul>li").length > 0) {
                $(this).append('<i class="fa fa-angle-down drop-btn inblock cspoint text-center"></i>');
            }
        });
        $('.drop-btn').click(function(e) {
            e.preventDefault();
            var ul = $(this).parent().children("ul");
            if (ul.is(":hidden") === true) {
                ul.parent('li').parent('ul').children('li').children('ul').slideUp(200);
                ul.parent('li').parent('ul').children('li').children('i').removeClass("fa-angle-up").addClass("fa-angle-down");
                $(this).removeClass("fa-angle-down").addClass("fa-angle-up");
                ul.slideDown(200);
            } else {
                $(this).removeClass("fa-angle-up").addClass("fa-angle-down");
                ul.slideUp();
                ul.find('ul').slideUp(200);
                ul.find('li>i').removeClass("fa-angle-up").addClass("fa-angle-down");
            }
        })
        $('header .show-search').click(function(e) {
            e.preventDefault();
            $('header .h-search-form').toggleClass('active');
        })
    },
    menuProCate: function() {
        if ($('.list-pro-cate.cate-menu').length == 0) return;
        $(".list-pro-cate.cate-menu ul li").each(function() {
            if ($(this).find("ul>li").length > 0) {
                $(this).append('<i class="icon-plus drop-cate-btn text-center d-inline-block cspoint"></i>');
            }
        });
        $('.list-pro-cate .drop-cate-btn').click(function(e) {
            $(this).parent().children('ul').stop().slideToggle(200);
            $(this).parent().children('a').toggleClass('active');
            $(this).toggleClass('icon-plus').toggleClass('icon-minus');
        })
    },
    scrollFixed: function() {
        if (win.scrollTop() > header.height()) {
            header.addClass('fixed');
        }
        win.scroll(function() {
            if (win.scrollTop() > header.height()) {
                header.addClass('fixed');
            } else {
                header.removeClass('fixed');
            }
        })
    }
}
var UI = {
    social: function() {
        setTimeout(function() {
            if (navigator.userAgent.indexOf("Speed Insights ") == -1) {
                $(window).bind("load", function() {
                    $('body').append('<div id="fb-root"></div>');
                    $.ajax({
                        global: false,
                        url: "theme/frontend/js/social.js",
                        dataType: "script"
                    });
                    window.___gcfg = {
                        lang: 'vi',
                        parsetags: 'onload'
                    };
                });
            }
        }, 5000);
    },
    sendContact: function() {
        $('.send-contact,.order_pro').submit(function(event) {
            event.preventDefault();
            var _this = $(this);
            _this.find('button[type="submit"]').prop('disabled', true);
            var req = UI.validateForm(_this);
            if (req == true) {
                $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: $(this).serialize() + '&' + $(this).find('button[type="submit"]').attr('name') + '=""'
                    })
                    .done(function(data) {
                        _this.find('button[type="submit"]').prop('disabled', false);
                        try {
                            var json = JSON.parse(data);
                            if ((json.code) == 200) {
                                toastr['success'](json.message);
                                window.location.reload();
                            } else {
                                toastr['error'](json.message);
                            }
                        } catch (ex) {}
                    })
                    .fail(function() {
                        console.log("error");
                    })
                    .always(function() {
                        console.log("complete");
                    });
            } else {
                _this.find('button[type="submit"]').prop('disabled', false);
            }
        });
    },
    starVote: function() {
        $('.star-rating a').click(function(event) {
            event.preventDefault();
            $.ajax({
                    url: 'Vindex/rating',
                    type: 'GET',
                    data: { val: $(this).attr('dt-value'), id: $('input[name=pid]').val(), table: $('input[name=table]').val() },
                })
                .done(function(data) {
                    try {
                        var json = JSON.parse(data);
                        if (json.code != 200) {
                            alert(json.message);
                            return;
                        }
                        alert(json.message);
                        var score = parseFloat(json.score);
                        var total = (json.total);
                        var s = Math.round(score * 100) / 100;
                        $('.star-rate').width((s * 100 / 5) + '%');
                        $('span.average').text(s);
                        $('.best').text(Math.round(s * 100 / 5) + '%');
                        $('.votes').text(total);
                    } catch (ex) {}
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
        });
    },
    inputNumber: function() {
        $(document).on('click', '#minus_number', function(e) {
            e.preventDefault();
            var input = $(this).parent().children('input');
            var val = parseInt(input.val());
            if (val <= 0) {
                val = 0;
            } else {
                val = val - 1
            }
            input.val(val);
        });
        $(document).on('click', '#plus_number', function(e) {
            e.preventDefault();
            var input = $(this).parent().children('input');
            var val = parseInt(input.val());
            input.val(val + 1);
        })
    },
    fancyBox: function() {
        if ($('.d_fancy').length > 0) {
            $('.d_fancy').fancybox({
                loop: true,
                arrows: false,
                infobar: false,
                thumbs: false,
            })
        }
    },
    validateForm: function(_this) {
        var inputs = _this.find('input.req');
        var submit = true;
        inputs.each(function(index, el) {
            if ($(el).val() == '') {
                toastr.clear();
                var noti = $(el).attr('dt-req');
                if (noti == '' || noti == undefined) {
                    noti = 'Vui lòng nhập đầy đủ thông tin';
                }
                toastr['error'](noti);
                return submit = false;
            }
        });
        return submit;
    }
}
var SORT = {
    selectChange: function() {
        if ($('#sort_procate').length == 0) return;
        $item = $('#sort_procate');
        var sort = $item.val();
        var parent = $item.data('parent');
        SORT.loadAjax(sort, parent);
        $('.main_cate .d_sort').change(function(event) {
            event.preventDefault();
            sort = $(this).val();
            SORT.loadAjax(sort, parent);
        });
        _doc.on('click', '#result_ajax.loaded_ajax .pagi a', function(event) {
            event.preventDefault();
            $scroll = $('.scrollHere').offset().top;
            $("html, body").stop().animate({ scrollTop: $scroll - 100 }, '500');
            $.ajax({
                    url: $(this).attr('href'),
                    type: 'GET'
                })
                .done(function(data) {
                    $('#result_ajax').addClass('loaded_ajax').html(data);
                });
        });
    },
    loadAjax: function(sort, parent) {
        $.ajax({
                url: 'Vindex/sortPro',
                type: 'GET',
                data: {
                    sort: sort,
                    parent: parent,
                },
            })
            .done(function(data) {
                $('#result_ajax').addClass('loaded_ajax').html(data);
            });
    }
}
var _initSearchAjax1 = function() {
    $('.form_fast_search').submit(function(event) {
        event.preventDefault();
        $.ajaxSetup({
            data: { csrf_enuy_name: $('meta[name="csrf-token"]').attr('content') }
        });
        $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
            })
            .done(function(data) {
                window.location.href = 'tim-kiem';
            });
    });
}
var backToTop = function() {
    var back_top = $('.back-to-top');
    if (win.scrollTop() > 500 && win.width() <= 991) { back_top.fadeIn(); }
    back_top.click(function() {
        $("html, body").animate({ scrollTop: 0 }, 800);
        return false;
    });
    win.resize(function() {
        if ($(this).width() > 991) {
            back_top.fadeOut();
        }
    });
    win.scroll(function() {
        if (win.scrollTop() > 500 && win.width() <= 991) back_top.fadeIn();
        else back_top.fadeOut();
    });
}
var uiDetail = function() {
    $('.pro-img').slick({
        arrows: false,
        swipeToSlide: true,
        asNavFor: '.pro-thumb',
    })
    $('.pro-thumb').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        nextArrow: '<i class="fa fa-angle-right smooth next"></i>',
        prevArrow: '<i class="fa fa-angle-left smooth prev"></i>',
        autoplay: true,
        swipeToSlide: true,
        autoplaySpeed: 5000,
        asNavFor: '.pro-img',
        focusOnSelect: true,
        responsive: [{
                breakpoint: 600,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 450,
                settings: {
                    slidesToShow: 2,
                }
            }
        ],
    })
    if (win.width() > 768) {
        CloudZoom.quickStart();
    }
    $(".pro-img .item:not(.slick-cloned) a").fancybox({
        transitionIn: 'elastic',
        transitionOut: 'elastic',
        speedIn: 600,
        speedOut: 200,
        overlayShow: false,
        autoScale: true,
        helpers: {
            thumbs: {
                width: 80,
                height: 80
            }
        },
        afterLoad: function() {
            this.title = 'Ảnh ' + (this.index + 1) + ' / ' + this.group.length + (this.title ? ' - ' + this.title : '');
        }
    });
}
var SCRIPT = {
    popupIntro: function() {
        var popup = $('#modal_intro');
        $('#modal_intro .modal-content .choose_address').click(function(e) {
            e.preventDefault();
            localStorage.address = $(this).attr('data-address');
            var str = JSON.parse(localStorage.address);
            $('#d_address .city_view').text(str.city);
            $('#d_address .address_view').text(str.address);
            $('#modal_intro').addClass('active');
        });
        if (localStorage.address != undefined) {
            var str = JSON.parse(localStorage.address);
            $('#d_address .city_view').text(str.city);
            if (str.city == 'Hà Nội') {
                loadMap('data-map');
                $('.call-hot.hn').addClass('active');
                $('.call-hot.hcm').removeClass('active');
                $('.plugins-social .zalo.hn').addClass('active');
                $('.plugins-social .zalo.hcm').removeClass('active');
                $('.plugins-social .fb.hn').addClass('active');
                $('.plugins-social .fb.hcm').removeClass('active');
            } else {
                loadMap('data-map-hcm');
                $('.call-hot.hcm').addClass('active');
                $('.call-hot.hn').removeClass('active');
                $('.plugins-social .zalo.hcm').addClass('active');
                $('.plugins-social .zalo.hn').removeClass('active');
                $('.plugins-social .fb.hcm').addClass('active');
                $('.plugins-social .fb.hn').removeClass('active');
            }
            $('#d_address .address_view').text(str.address);
            $('#modal_intro').addClass('active');
        } else {
            if ($('#modal_intro').length == 0) return false;
            var getLocalStorage = localStorage.getItem('open');
            if (getLocalStorage == null) {
                setTimeout(function() {
                    localStorage.setItem('open', 'has_view');
                    popup.modal("show");
                }, 3000);
            }
            loadMap('data-map');
        }
    },
    showSeach: function() {
        $('.d_btn.toggle_seach').click(function(e) {
            e.preventDefault();
            $('.h-fast-search').toggleClass('active')
        })
    },
    loadContact: function() {
        if ($('.choose_address').length > 0) {
            $('.choose_address').click(function(event) {
                var _this = $(this).attr('data-address');
                var str = JSON.parse(_this);
                var address = str.city;
                if (address == 'Hà Nội') {
                    $('.call-hot.hn').addClass('active');
                    $('.call-hot.hcm').removeClass('active');
                    $('.plugins-social .zalo.hn').addClass('active');
                    $('.plugins-social .zalo.hcm').removeClass('active');
                    $('.plugins-social .fb.hn').addClass('active');
                    $('.plugins-social .fb.hcm').removeClass('active');
                } else {
                    $('.call-hot.hcm').addClass('active');
                    $('.call-hot.hn').removeClass('active');
                    $('.plugins-social .zalo.hcm').addClass('active');
                    $('.plugins-social .zalo.hn').removeClass('active');
                    $('.plugins-social .fb.hcm').addClass('active');
                    $('.plugins-social .fb.hn').removeClass('active');
                }
            });
        }
    },
    loadVideobanner: function() {
        if ($('#video_banner').length == 0) return;
        setTimeout(function() {
            $('.d_rm_banner').remove();
            $('.video_banner').removeClass('d-none');
            var video = document.getElementById('video_banner');
            $('#src_video').attr('src', $('#video_banner').data('src'))
            video.load();
            win.scroll(function() {
                if (win.scrollTop() > $('.banner').height()) {
                    video.pause();
                } else {
                    video.play();
                }
            })
        }, 2000)
    },
    modalOrder: function() {
        if ($('#modal_order').length == 0) return;
    }
}

function loadMap($attr_map) {
    if ($('.loadmap').length > 0) {
        setTimeout(function() {
            $('.loadmap').each(function() {
                var map = $(this).attr($attr_map);
                $(this).append(map);
            })
        }, 3000);
    }
}

function sendRequestOrderPro() {
    $('.f_info_cart').submit(function(event) {
        event.preventDefault();
        var _this = $(this);
        var req = UI.validateForm(_this);
        if (req == true) {
            _this.find('button[type="submit"]').prop('disabled', true);
            $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize() + '&' + $(this).find('button[type="submit"]').attr('name') + '=""'
                })
                .done(function(data) {
                    _this.find('button[type="submit"]').prop('disabled', false);
                    try {
                        var json = JSON.parse(data);
                        if ((json.code) == 200) {
                            toastr['success'](json.message);
                            location.href = 'thanh-cong';
                        } else {
                            toastr['error'](json.message);
                        }
                    } catch (ex) {}
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    console.log("complete");
                });
        }
    });
}

$(document).ready(function() {
    $(function() {
        $('.lazy').lazyload();
    });
    $(function() {
        if ($('.lazy').length > 0) {
            $("img.lazy").show().lazyload({
                effect: "fadeIn",
                effectTime: 100,
                threshold: 0
            });
        }
        new WOW().init();
        sendRequestOrderPro();
        HEADER.menuMoblie();
        HEADER.menuProCate();
        HEADER.scrollFixed();
        UI.social();
        UI.starVote();
        UI.sendContact();
        backToTop();
        // uiDetail();
        UI.inputNumber();
        UI.fancyBox();
        // _initSearchAjax1();
        SORT.selectChange();
        SCRIPT.popupIntro();
        SCRIPT.showSeach();
        SCRIPT.modalOrder();
        SCRIPT.loadContact();
        $('.bgloading').delay(100).fadeOut(200);
        SCRIPT.loadVideobanner();
        $(document).on('click', '.d_box_number .number_btn', function(e) {
            e.preventDefault();
            var input = $(this).parent().children('input');
            var val = parseInt(input.val());
            if ($(this).hasClass('d_minus')) {
                if (val <= 1) {
                    val = 1;
                } else {
                    val = val - 1
                }
            } else {
                val++;
            }
            input.val(val);
        });
    })
})
var SLIDER = (function() {
    var extend = function() {
        var obj, name, copy,
            target = arguments[0] || {},
            i = 1,
            length = arguments.length;
        for (; i < length; i++) {
            if ((obj = arguments[i]) !== null) {
                for (name in obj) {
                    opy = obj[name];
                    if (target === copy) {
                        continue;
                    } else if (copy !== undefined) {
                        target[name] = copy;
                    }
                }
            }
        }
        return target;
    };
    var toCamel = function(str) {
        return str.replace(
            /([-_][a-z])/g,
            (group) => group.toUpperCase()
            .replace('-', '')
            .replace('_', '')
        );
    }
    var merge = function(obj1, obj2) {
        var obj3 = {};
        for (var attrname in obj1) {
            var new_attrname = toCamel(attrname);
            obj3[new_attrname] = obj1[attrname];
        }
        for (var attrname in obj2) {
            var new_attrname = toCamel(attrname);
            obj3[new_attrname] = obj2[attrname];
        }
        return obj3;
    }
    var sliders = $('.tiny-slider');
    if (sliders.length == 0) return;
    for (var i = 0; i < sliders.length; i++) {
        var item = $(sliders[i]);
        var data = item.data();
        var options = merge({
            container: item[0],
            items: 1,
            gutter: 10,
            slideBy: 'page',
            mouseDrag: true,
            autoplay: false,
            controls: false,
            autoplayButtonOutput: false,
            nav: false
        }, data || {});
        tns(options);
    }
})();

var FILTER = (function() {
    var filterSubmit = function() {
        $('select[name="size"]').on('change', function(event) {
            event.preventDefault();
            $(this).parents('.filter_form').submit();
            $('input[name="id"]').submit();
        });
        $('select[name="collection"]').on('change', function(event) {
            event.preventDefault();
            $(this).parents('.filter_form').submit();
        });
        $('select[name="trademark"]').on('change', function(event) {
            event.preventDefault();
            $(this).parents('.filter_form').submit();
        });
        $('select[name="materies"]').on('change', function(event) {
            event.preventDefault();
            $(this).parents('.filter_form').submit();
        });
        $('select[name="segments"]').on('change', function(event) {
            event.preventDefault();
            $(this).parents('.filter_form').submit();
        });
    }
    return {
        _: function() {
            filterSubmit();
        }
    };
})();
jQuery(document).ready(function($) {
    FILTER._();
});

var CUSTOM_FUNCTION = (function(){
    var showOrHiddenToc = function () {
        var button_show_or_hidden_toc = document.querySelector('.show_or_hidden_toc');
        var toc_list = document.querySelector('.toc_list');
        button_show_or_hidden_toc.addEventListener("click", function(){
            var _this = this;
            if (_this.classList.contains("show")) {
                _this.classList.remove("show");
                toc_list.classList.add('d-none');
            } else {
                _this.classList.add("show");
                toc_list.classList.remove('d-none');
            }
        });
    }

    return{
        init:function(){
            showOrHiddenToc();
        }
    }
})();
CUSTOM_FUNCTION.init();