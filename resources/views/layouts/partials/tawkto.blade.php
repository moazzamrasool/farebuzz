@if(config('app.tawkto_property_id') && config('app.tawkto_widget_id'))
  <script type="text/javascript">
    var Tawk_API = Tawk_API || {};
    // Desktop: WhatsApp/call buttons live bottom-left (see floating_buttons.blade.php),
    // so the Tawk bubble stays on its default bottom-right corner with no extra offset.
    // Mobile: the full-width sticky Call/WhatsApp bar (sticky_contact_bar.blade.php) covers
    // the bottom ~58px + safe-area of the viewport, so the bubble is lifted above it here
    // to avoid the two overlapping.
    Tawk_API.customStyle = {
      visibility: {
        desktop: { position: 'br', xOffset: 20, yOffset: 20 },
        mobile:  { position: 'br', xOffset: 10, yOffset: 74 }
      }
    };
    var Tawk_LoadStart = new Date();
    (function () {
      var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
      s1.async = true;
      s1.src = 'https://embed.tawk.to/{{ config('app.tawkto_property_id') }}/{{ config('app.tawkto_widget_id') }}';
      s1.charset = 'UTF-8';
      s1.setAttribute('crossorigin', '*');
      s0.parentNode.insertBefore(s1, s0);
    })();
  </script>
@endif
