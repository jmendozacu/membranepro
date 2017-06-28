<!-- SimpleReach Slide Plugin Version: {{srslide_plugin_version}} -->
<script type='text/javascript' id='simplereach-slide-tag'>
    __reach_config = {
      pid: '{{srslide_pid}}',
      title: '{{title}}',
      tags: {{{tags}}},
      authors: {{{authors}}},
      channels: {{{channels}}},{{slide_logo_elem}}
      slide_active: {{srslide_disable_on_post}},{{slide_icon_elem}}
      date: '{{published_date}}',
      url: '{{canonical_url}}',
      header: '{{srslide_header_text}}'
    };
    var content = document.getElementById('simplereach-slide-tag').parentNode, loc;
    if (content.className){ loc = '.' + content.className; }
    if (content.id){ loc = '#' + content.id; }
    __reach_config.loc = loc || content;
    (function(){
    var s = document.createElement('script');
      s.async = true;
      s.type = 'text/javascript';
      s.src = document.location.protocol + '//d8rk54i4mohrb.cloudfront.net/js/slide.js';
      __reach_config.css = '{{srslide_css_url}}';
      var tg = document.getElementsByTagName('head')[0];
      if (!tg) {tg = document.getElementsByTagName('body')[0];}
      if (tg) {tg.appendChild(s);}
    })();
</script>
