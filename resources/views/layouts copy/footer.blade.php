<div class="footer">
         <div class="container-fluid">
            <div class="row">
               <div class="col-lg-3 col-md-12">
                  <div class="footer-logo">
                     <img src="{{asset('admin/asset/images/aa.png')}}" width="120">
                  </div>
                  <p>Winify Logistics Services is a global supplier of transport and logistics solutions. We have offices in more than 20 countries and an international network of partners and agents.</p>
                  <div class="social-media ">
                     <a href="https://www.facebook.com/winifylogistics/" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                     <a href="https://www.instagram.com/winifylogistics/?utm_medium=copy_link" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                     <a href="https://www.linkedin.com/company/Winify-worldwide/" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                     <a href="https://x.com/winify" target="_blank"><img src="{{asset('admin/asset/images/icons/twitterx.svg')}}" alt="twitterx" width="17"></a>
                     <a href="https://www.youtube.com/@winify" target="_blank"><i class="fa fa-youtube" aria-hidden="true"></i></a>
                     <a href="https://api.whatsapp.com/send/?phone=+91-7669922258" target="_blank"><i class="fa fa-whatsapp"></i></a>
                  </div>
               </div>
               <div class="col-lg-3 col-md-3">
                  <div class="ft-inner pl80">
                     <h6>Quick Links</h6>
                     <ul>
                        <li><a href="{{route('about_us')}}"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>About us </a></li>
                        <li><a href="{{--Track.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Track Order</a></li>
                        <li><a href="{{--documents.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Useful Links</a></li>
                        <li><a href="{{--restricted-items.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Restricted Items</a></li>
                        <li><a href="{{--kyc.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>KYC</a></li>
                        <li><a href="{{--blog/index.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Blog</a>
                        <li><a href="{{--contact-us.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Contact Us </a></li>
                        <li><a href="{{--shipping-policy.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Shipping Policy</a></li>
                        <li><a href="{{--faqs.html--}}#"><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>FAQs</a></li>
                     </ul>
                  </div>
               </div>
               <div class="col-lg-3 col-md-4">
                  <div class="ft-inner ">
                     <h6>Services</h6>
                     <ul>
                        <li ><a  href="{{--international-courier-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>International Courier Services </a></li>
                        <li ><a  href="{{--international-air-freights.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>International Air Freights</a></li>
                        <li ><a  href="{{--international-sea-cargo.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>International Sea Cargo  </a></li>
                        <li ><a  href="{{--freight-forwarding-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Freight Forwarder</a></li>
                        <li ><a  href="{{--import-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Import Services</a></li>
                        <li ><a  href="{{--export-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Export Services</a></li>
                        <li ><a  href="{{--domestic-cargo-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Domestic Cargo Services</a></li>
                        <li ><a  href="{{--worldwide-relocation-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Worldwide Relocation Services</a></li>
                        <li ><a  href="{{--custom-clearance-services.html--}}" ><span><i class="fa fa-angle-right" aria-hidden="true"></i></span>Custom Clearance Services</a></li>
                     </ul>
                  </div>
               </div>
               <div class="col-lg-3 col-md-5 ">
                  <div class="ft-inner">
                     <h6>Get In Touch </h6>
                     <p><strong>WINIFY PVT. LTD.</strong></p>
                     <p><span><i class="fa fa-map-marker" aria-hidden="true"></i></span>  26/34,3rd Floor , Office No.201 Palmohan Mansion,Near Hotel Siddhartha,East Patel Nagar, <br> New Delhi -110008
                     </p>
                     <p><span><i class="fa fa-phone" aria-hidden="true"></i></span><a href="tel:+91-9266332160">+91-76699-22258/+91-76699-22259</a> </p>
                     {{-- <p><span><i class="fa fa-phone" aria-hidden="true"></i></span><a href="tel:011-71523024">011-71588024</a></p> --}}
                     <p><span><i class="fa fa-envelope-o" aria-hidden="true"></i></span><a href="mailto:info@farebuzztravel.com">info@farebuzztravel.com</a></p>
                  </div>
               </div>
               <div class="col-md-12 text-center">
                  <div class="copy-right">
                     <div class="row">
                        <div class="col-md-8">
                           <ul>
                              <li><a href="{{route('pages','privacy-policy')}}">Privacy Policy</a></li>
                              <li><a href="{{route('pages','terms-and-conditions')}}">Terms and Conditions</a></li>
                           </ul>
                        </div>
                        <div class="col-md-4">
                           <p>© 2025 All Right Reserved. </p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

{{-- modal========================= --}}

     @php
         $states= DB::table('states')->get();
      @endphp
       <div class="modal fade " style="display:none" id="enquire-now">
         <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <button type="button" class="close " data-bs-dismiss="modal" aria-label="Close">
               <img src="{{asset('admin/asset/images/close.svg')}}" alt="close">
               </button>
               <div class="modal-body">
                  <h4>Your Package, Our Priority - Enquire Now!</h4>
                  <form id="enquiry_form_modal">
                    @csrf
                    @method('POST')
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <input type="text" class="form-control" name="firstname" id="firstname" required="" placeholder="First Name">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <input type="text" class="form-control" name="lastname" id="lastname" required="" placeholder="Last Name">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <input type="email" class="form-control" name="email" id="email" required="" placeholder="Email Id">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <input type="text" class="form-control" name="phone" id="phone" required="" placeholder="Phone Number" maxlength="10" size="10" pattern="[0-9]{10}" onkeypress="return isNumberKey(event,this)">
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              {{-- <select class="form-select bg-white" name="state" id="state1" required="">
                                 <option value="Select City">Select State</option>
                                 <option value="Delhi">Delhi</option>
                                 <option value="Maharashtra">Maharashtra</option>
                                 <option value="Uttar Pradesh">Uttar Pradesh</option>
                                 <option value="Uttarakhand">Uttarakhand</option>
                                 <option value="Punjab">Punjab</option>
                                 <option value="Rajasthan">Rajasthan</option>
                                 <option value="Haryana">Haryana</option>
                                 <option value="Andhra Pradesh">Andhra Pradesh</option>
                                 <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                                 <option value="Assam">Assam</option>
                                 <option value="Bihar">Bihar</option>
                                 <option value="Chhattisgarh">Chhattisgarh</option>
                                 <option value="Goa">Goa</option>
                                 <option value="Gujarat">Gujarat</option>
                                 <option value="Himachal Pradesh">Himachal Pradesh</option>
                                 <option value="Jharkhand">Jharkhand</option>
                                 <option value="Karnataka">Karnataka</option>
                                 <option value="Kerala">Kerala</option>
                                 <option value="Madhya Pradesh">Madhya Pradesh</option>
                                 <option value="Manipur">Manipur</option>
                                 <option value="Meghalaya">Meghalaya</option>
                                 <option value="Mizoram">Mizoram</option>
                                 <option value="Nagaland">Nagaland</option>
                                 <option value="Odisha">Odisha</option>
                                 <option value="Sikkim">Sikkim</option>
                                 <option value="Tamil Nadu">Tamil Nadu</option>
                                 <option value="Telangana">Telangana</option>
                                 <option value="Tripura">Tripura</option>
                                 <option value="West Bengal">West Bengal</option>
                                 <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                              </select> --}}
                              <select class="form-select bg-white" name="state" id="state1" required="" onchange="getDestination1()">
                                 <option value="">Select State</option>
                                 @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->state }}</option>
                                 @endforeach
                              </select>

                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                                <select class="form-select bg-white" name="destination" id="destination1" required="">
                                     <option value="" disabled selected>Select City</option>
                                </select>
                           </div>
                        </div>
                        <div class="col-md-12">
                           <div class="form-group">
                              <textarea type="textarea" id="message" required="" name="message" class="form-control" placeholder="Message" rows="4"></textarea>
                           </div>
                        </div>
                        <input type="hidden" name="form_type" value="enquire_now">
                        <div class="col-md-12 ">
                           <button type="submit" value="submit_enquir" name="submit_enquir" class="corier-btn form-btn mt-4 ">SUBMIT </button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>



         <script>
        $('#enquiry_form_modal').submit((event)=>{
            event.preventDefault();
            let formData = new FormData($('#enquiry_form_modal')[0]);
            $.ajax({
                type: 'POST',
                url: '{{route('submitquery')}}',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response){
                    $('#enquiry_form_modal')[0].reset();
                    $('#enquire-now').modal('hide');
                    Swal.fire(
                        'Thank you!',
                        'Your query has been submitted successfully. We will get back to you soon.',
                        'success'
                    )
                    // $('#enquire-now').modal('hide');
                },
                error: function(error){
                    Swal.fire(
                        'Oops!',
                        'Something went wrong. Please try again later.',
                        'error'
                    )
                }
            });
        })
    </script>
    <script>
      function getDestination1()
      {
        let state = $('#state1').val();

        console.log(state);

              let url = "{{ route('getcities', ['state' => ':id']) }}";
            url = url.replace(':id', state);

            $.ajax({
                type: 'GET',
                url:url,
                success: function(response){
                    let options = '<option value="" disabled selected>Select City</option>';
                    response.cities.forEach((item)=>{
                        options += `<option value="${item.id}">${item.city}</option>`;
                    });
                    $('#destination1').html(options);
                }
            });
      }
    </script>
