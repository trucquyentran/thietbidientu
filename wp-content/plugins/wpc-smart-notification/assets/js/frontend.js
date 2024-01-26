(function($) {
  class wpcsn {
    constructor($el, options) {
      let _this = this;

      let _data = {
        action: 'wpcsn',
        _nonce: WPCSNOptions.nonce,
        form_data: {
          ID: WPCSNOptions.ID,
          action: 'get_content',
        },
      };

      _this.index = 0;
      _this.sleep = false;
      _this.$wrapper = $el;

      $.ajax({
        url: WPCSNOptions.ajaxurl,
        dataType: 'json',
        type: 'post',
        data: _data,
        success: function(response) {
          _this.build(response);

          // hide all items first
          _this.$inner.children().hide();

          // start
          setTimeout(function() {
            // delay start
            _this.start();
          }, parseInt(_this.options.delay_start) * 1000);

          $(document.body).trigger('wpcsn_load_success', [response]);
        },
        error: function(e) {
          _this.isLoading($form);
          _this.noti({
            status: 'error',
            alert: WPCSNOptions.saveFail,
          });
          $(document.body).trigger('wpcsn_load_error');
        },
      });
    }

    build(data) {
      let _this = this;

      _this.$items = [];

      _this.options = $.extend({
        position: 'bottom-left',
        autoplay: 5,
        delay_start: 0,
        delay_switch: 0,
        effect: {
          show: 'bounceInUp',
          hide: 'bounceOutDown',
        },
      }, data.options);

      _this.$wrapper.addClass(_this.options.position);

      _this.$inner = $('<div>').attr({
        class: 'wpcsn-notification-inner',
      }).appendTo(_this.$wrapper);

      $('<a>').attr({
        href: '#close',
        class: 'close',
      }).appendTo(_this.$wrapper);

      if (typeof data.data === 'object' && data.data.length > 0) {
        data.data.forEach(function(item) {

          let $item = $('<div>').attr({
            class: 'wpcsn-notification-item ' + item['class'],
          }).appendTo(_this.$inner);

          _this.$items.push($item);

          let $itemContent = $('<div>').attr({
            class: 'wpcsn-notification-content-wrap',
          });

          let fields = ['thumbnail', 'title', 'content', 'price', 'time'];

          fields.forEach(function(field) {
            if (item[field] != undefined) {
              $('<div>').
                  attr({
                    class: 'wpcsn-notification-' + field,
                  }).
                  html(item[field]).
                  appendTo(field != 'thumbnail' ? $itemContent : $item);
            }
          });

          $itemContent.appendTo($item);

          $('<div class="wpcsn-notification-close">').appendTo($item);
        });
      }

      $(document.body).trigger('wpcsn_build_template', [data]);
    }

    stop() {
      clearInterval(this.theInterval);
      this.$inner.children(':visible').
          removeClass(this.options.effect.show).
          addClass(this.options.effect.hide);
    }

    start() {
      let _this = this;
      let _sec = 0;

      if (_this.$items.length) {
        _this.$inner.children().hide();
        _this.$items[_this.index].addClass(_this.options.effect.show).
            css('display', 'flex');

        _this.theInterval = setInterval(function() {
          let _active = ((typeof document.hasFocus != 'undefined' ?
              document.hasFocus() :
              1) ? 1 : 0);

          if (_active && (_this.sleep === false)) {
            _sec++;
          }

          if (_sec === parseInt(_this.options.autoplay)) {
            _this.index++;
            _this.$inner.children(':visible').
                removeClass(_this.options.effect.show).
                addClass(_this.options.effect.hide);
          }

          if (_sec === (parseInt(_this.options.autoplay) +
              parseInt(_this.options.delay_switch))) {
            _this.$items[_this.index].removeClass(_this.options.effect.hide).
                addClass(_this.options.effect.show).
                css('display', 'flex');
            _sec = 0;
          }

          if (_this.index >= _this.$items.length) {
            if (_this.options.loop === 'yes') {
              _this.index = 0;
            } else {
              _this.stop();
              return false;
            }
          }
        }, 1000);

        $(document).on('click touch', '.wpcsn-notification-close',
            function(e) {
              e.preventDefault();
              _this.stop();
            });

        if (_this.options.pause === 'yes') {
          _this.$wrapper.on('mouseenter', function() {
            _this.sleep = true;
          });
          _this.$wrapper.on('mouseleave', function() {
            _this.sleep = false;
          });
        }
      }
    }
  }

  if ($('.wpcsn-notification').length) {
    new wpcsn($(document).find('.wpcsn-notification'));
  }
})(jQuery);