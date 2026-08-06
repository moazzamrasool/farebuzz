<div class="top-bar">
         <div class="container-fluid">
            <div class="row">
               <div class="col-md-9">
                  <div class="top-number">
                     <a href="tel:+91-9266332160">
                     <img src="{{asset('admin/asset/images/phone2.svg')}}" alt="phone" width="22">
                     <span class="call-glow">Call for Quote  +91 - 9266332160/9266332157</span>
                     </a>
                     <span>|</span>
                     <a href="mailto:info@farebuzztravel.com">
                        <img src="{{asset('admin/asset/images/email.svg')}}" alt="email" width="20">
                        <p class="d-inline fw-bold">info@farebuzztravel.com</p>
                     </a>
                  </div>
                  <style>
                     .call-glow {
                     display: inline-block;
                     font-weight: bold;
                     color: #28a745;
                     animation: pulseGlow 2s infinite ease-in-out;
                     font-size: 16px;
                     }
                     /* Glow and Scale Animation */
                     @keyframes pulseGlow {
                     0% {
                     transform: scale(1);
                     text-shadow: 0 0 5px rgba(40, 167, 69, 0.05);
                     }
                     50% {
                     transform: scale(1.05);
                     text-shadow: 0 0 15px rgba(40, 167, 69, 0.08);
                     }
                     100% {
                     transform: scale(1);
                     text-shadow: 0 0 5px rgba(40, 167, 69, 0.05);
                     }
                     }
                  </style>
               </div>
               <div class="col-md-3">
                  <div class="top-social">
                     <a href="https://www.facebook.com/winifylogistics/" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                     <a href="https://www.instagram.com/winifylogistics/?utm_medium=copy_link" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                     <a href="https://www.linkedin.com/company/winify/" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                     <a href="https://x.com/winify" target="_blank"><img src="{{asset('admin/asset/images/icons/twitterx2.svg')}}" alt="twitterx" width="18"></a>
                     <a href="https://www.youtube.com/@winify" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a>
                     <a href="https://api.whatsapp.com/send/?phone=+91-7669922258" target="_blank"><i class="fa fa-whatsapp"></i></a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <style>.call-btn i {
         margin-right: 10px;
         animation: bounceIcon 1s infinite;
         }@keyframes bounceIcon {
         0%, 100% {
         transform: translateY(0);
         }
         50% {
         transform: translateY(-4px);
         }
         }
      </style>
      <!---================= HEADER =================-->
      <nav class="navbar-expand-lg navbar-light">
         <div class="top-btn">
            <a href="tel:+91-9266332160" class="call-btn"><i class="fa fa-phone fa-bounce" aria-hidden="true"></i> Get Quote</a>
            <a href="javascript:void(0)"  class="form-btn "  data-bs-toggle="modal" data-bs-target="#enquire-now"><i class="fa fa-plus" aria-hidden="true"></i>Send Enquiry </a>
         </div>
         <div class="container-fluid">
            <div class="row">
               <a class="navbar-brand" href="{{route('home')}}">
               <img src="{{asset('admin/asset/images/aa.png')}}" alt="winify" >
               </a>
               <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
               </button>
               <div class="navbar-collapse collapse main-nav" id="navbarSupportedContent">
                  <ul class="nav navbar-nav">
                     <li class="nav-item"><a class="nav-link" href="{{route('home')}}">Home</a></li>
                     <li class="nav-item"><a class="nav-link" href="{{route('about_us')}}">About Us</a></li>
                     <li class="nav-item">
                        <a class="nav-link " href="javascript:(void);" data-bs-toggle="dropdown" aria-expanded="false"> Services <i class="fa fa-angle-down" aria-hidden="true"></i></a>

                        <ul class="dropdown-menu">
                            @forelse ($pages as $page)
                            <li class="nav-item"><a  href="{{route('pages',['slug'=>$page->page_url])}}" class="nav-link">{{$page->page_name}}</a></li>

                            @empty

                            @endforelse
                           {{-- <li class="nav-item"><a  href="international-air-freights.html" class="nav-link">International Air Freights</a></li>
                           <li class="nav-item"><a  href="international-sea-cargo.html" class="nav-link">International Sea Cargo  </a></li>
                           <li class="nav-item"><a  href="freight-forwarding-services.html" class="nav-link">Freight Forwarder</a></li>
                           <li class="nav-item"><a  href="import-services.html" class="nav-link">Import Services</a></li>
                           <li class="nav-item"><a  href="export-services.html" class="nav-link">Export Services</a></li>
                           <li class="nav-item"><a  href="domestic-cargo-services.html" class="nav-link">Domestic Cargo Services</a></li>
                           <li class="nav-item"><a  href="worldwide-relocation-services.html" class="nav-link">Worldwide Relocation Services  </a></li>
                           <li class="nav-item"><a  href="custom-clearance-services.html" class="nav-link">Custom Clearance Services  </a></li> --}}
                        </ul>
                     </li>
                     <li class="nav-item"><a class="nav-link" href="{{--route('blog')--}}">Blog</a></li>
                     {{--<li class="nav-item">
                         <a class="nav-link " href="javascript:(void);" data-bs-toggle="dropdown" aria-expanded="false">Support <i class="fa fa-angle-down" aria-hidden="true"></i></a>
                        <ul class="dropdown-menu">
                           <li class="nav-item"><a  href="documents.html" class="nav-link">Useful Links </a></li>
                           <li class="nav-item"><a  href="restricted-items.html" class="nav-link"> Restricted Items </a></li>
                           <li class="nav-item"><a  href="kyc.html" class="nav-link">KYC </a></li>
                           <li class="nav-item"><a  href="blog/index.html" class="nav-link">Blog</a></li>
                        </ul>
                     </li>--}}
                     <!--  <li class="nav-item"><a class="nav-link " href="career.php"> CAREER</a></li> -->
                     <li class="nav-item"><a class="nav-link " href="{{route('contact_us')}}">Contact Us</a></li>
                     {{-- <li class="nav-item mt-2"><a class="nav-link getin-touch"  href="pay-now.html">PAY NOW</a></li>--}}
                     <li class="nav-item mt-2"><a class="nav-link getin-touch"  href="#">TRACK ORDER</a></li>
                  </ul>
                  <a href="javascript:void(0)"  class="getin-touch ml15" style="padding: 8px 15px;" data-bs-toggle="modal" data-bs-target="#enquire-now">Get Quote </a>
                  <a href="{{route('admin.login')}}"  class="getin-touch user"  style="padding: 8px 15px;"><i class="fa fa-user" aria-hidden="true"></i>  </a>
               </div>
            </div>
         </div>
      </nav>

      <style>
      @media (max-width: 520px) {
         .banner .item.slide1 {
         background-position-x: 0px !important;
         }
         }
         @media (max-width: 375px) {
         .banner .item.slide1 {
         background-position-x: -100px !important;
         }
         }

      </style>
