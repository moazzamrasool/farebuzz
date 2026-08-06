<!doctype html>
<html lang="en">
        <!---================= HEAD =================-->
   @include('layouts.head')
   <body>
      <!-- Google Tag Manager (noscript) -->
      <!-- End Google Tag Manager (noscript) -->
      {{-- jquery --}}
    <script src="{{asset('admin/asset/js/jquery-3.6.0.min.js')}}"></script>
        <!---================= TOP BAR =================-->
      @include('layouts.header')
      <!---================= BANNER =================-->
      @yield('content')
      <!---================= FOOTER =================-->
      @include('layouts.footer')

    <div class="wfy_whatsapp ">
        <a href="https://api.whatsapp.com/send?phone=+91-7669922258&amp;text=Let’s Discuss Your Plan" target="_blank">
        <i class="fa fa-whatsapp" aria-hidden="true"></i> Chat With Us
        </a>
    </div>
      <!---================= MODAL =================-->


      {{-- </div> --}}
                {{-- <div class="modal fade"  id="myModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="displa:none">
         <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
               <img src="{{asset('admin/asset/images/close.svg')}}" alt="close">
               </button>
               <div class="modal-body otp-modal">
                  <h4>Track your Order or Shipment</h4>
                  <p>Need the status of your shipment or proof of delivery? <br>Enter your tracking number below.</p>
                  <form action="#" method="post" >
                     <div class="row">
                        <div class="col-md-12">
                           <div class="form-group trak">
                              <input type="Search" class="form-control" name="pincode" id="pincode"  placeholder="Enter your tracking ID" >
                              <button type="submit" value="" name="submit" class="corier-btn ">SUBMIT</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div> --}}
        <!---================= SCRIPTS =================-->




      <script src="{{asset('admin/asset/js/bootstrap.bundle.min.js')}}"></script>
      <script src="{{asset('admin/asset/js/wow.min.js')}}"></script>
      <script src="{{asset('admin/asset/js/jquery.fancybox.min.js')}}"></script>
      <script defer src="{{asset('admin/asset/js/slick.min.js')}}"></script>
      <script src="{{asset('admin/asset/js/custom.js')}}"></script>

      {{-- Sweet Alert JS --}}
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
         function isNumberKey(evt,element){
             var charCode = (evt.which) ? evt.which : evt.keyCode
             if (charCode > 31 && (charCode < 48 || charCode > 57))
                 return false;
             return true;
         }
      </script>
      <!--Start of Tawk.to Script-->
        <script type="text/javascript">
        var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
        (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/60b795756699c7280daa54d4/1f76hvl2j';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
        })();
        </script>
        <!--End of Tawk.to Script-->


   </body>
</html>
