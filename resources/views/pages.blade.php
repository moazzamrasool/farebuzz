@extends('layouts.app')

@section('content')

{{-- {!!$page->page_link!!} --}}

<div class="pad100 ">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-12">
            <div class="service-text ">
               <h2 class="heading wow animate fadeInUp">International <span class="txt-color">Air Freights</span><br> </h2>
               {{-- <p class="wow animate fadeInUp">Need to ship large quantities of goods overseas? Look no further. Our international Freights services provide efficient and cost-effective solutions for your freight needs. With our extensive network and expertise, we ensure that your cargo reaches its destination safely and on time. </p> --}}
               <p class="wow animate fadeInUp">{!!$page->page_link!!}</p>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="pad100 bg-grey">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-12 ">
            <h2 class="heading wow animate fadeInUp"> Why Choose WINIFY for <br>International<span class="txt-color"> Freights</span></h2>
         </div>
         <div class="col-md-4">
            <div class="whychose-thumnail">
               <img src="{{asset('admin/asset/images/icons/Bulk-Shipments.png')}}" width="60">
               <h5>Bulk Shipments</h5>
               <p>We handle large volumes of Freights with ease, ensuring efficient and cost-effective transportation. </p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="whychose-thumnail">
               <img src="{{asset('admin/asset/images/icons/Worldwide-Reach.png')}}" width="60">
               <h5>Worldwide Reach</h5>
               <p>Shipping over 195 countries and territories. Nothing is too far from us. </p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="whychose-thumnail">
               <img src="{{asset('admin/asset/images/icons/Flawless-Integration.png')}}" width="60">
               <h5>Flawless Integration</h5>
               <p>Connecting your Freights with major global marketplaces. We are here for you.  </p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="whychose-thumnail">
               <img src="{{asset('admin/asset/images/icons/shipment-security.png')}}" width="60">
               <h5>Shipment Security</h5>
               <p>You don’t need to worry about the safety of your cargo. It is in good hands. </p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="whychose-thumnail">
               <img src="{{asset('admin/asset/images/icons/real-time.png')}}" width="60">
               <h5>Real-time updates</h5>
               <p>Worried about your Freights? No more! You’ll be kept informed with SMS and email notifications.  </p>
            </div>
         </div>
         <div class="col-md-4">
            <div class="whychose-thumnail">
               <img src="{{asset('admin/asset/images/icons/Rocket-speed-Delivery.png')}}" width="60">
               <h5>Rocket-speed Delivery</h5>
               <p>We know the word ‘urgent’. With our automated workflow your Freights will be delivered on time.  </p>
            </div>
         </div>
         <!--  <div class="col-md-3">
            <div class="whychose-thumnail">
             <img src="{{asset('admin/asset/images/icons/reasoning.png')}}" width="60">
               <h5>Branded Experience</h5>
               <p>We leave no stone unturned to make you happy.  </p>
            </div>
            </div>-->
      </div>
   </div>
</div>
<div class="pad100 ">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-12">
            <h2 class="heading wow animate fadeInUp text-center"> How it <span class="txt-color">Works</span></h2>
         </div>
         <div class="col-md-4">
            <div class="international-service">
               <img src="{{asset('admin/asset/images/shedule.png')}}" alt="" class="w-100">
               <div class="howit-title">
                  <h4>Shedule Free a Pickup</h4>
                  <p>Book a courier pickup online and enjoy hassle-free parcel collection from your doorstep.</p>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="international-service">
               <img src="{{asset('admin/asset/images/doorstep.png')}}" alt="" class="w-100">
               <div class="howit-title">
                  <h4>Arriving at your Doorstep </h4>
                  <p>Our partners reach your location within 24 hours after placing the order. </p>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <div class="international-service">
               <img src="{{asset('admin/asset/images/sit-pickup.png')}}" alt="" class="w-100">
               <div class="howit-title">
                  <h4>Have a Sip and Relax </h4>
                  <p>Your order will be delivered to your chosen location. Track it from the website or app</p>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="bg-grey simplified-sec">
   <div class="container-fluid fluid">
      <div class="row">
         <div class="col-lg-6 fluid">
            <div class="icon-box text-white">
               <h2 class="heading wow animate fadeInUp ">Benefits </h2>
               <div class="icons-thumb">
                  <img src="{{asset('admin/asset/images/icons/Rapid-Reliable-Delivery.png')}}" width="50">
                  <div class="icon-txt">
                     <p>Rapid, Reliable Delivery</p>
                  </div>
               </div>
               <div class="icons-thumb">
                  <img src="{{asset('admin/asset/images/icons/Swift-Efficient-Shipping.png')}}" width="50">
                  <div class="icon-txt">
                     <p>Swift, Efficient Shipping</p>
                  </div>
               </div>
               <div class="icons-thumb">
                  <img src="{{asset('admin/asset/images/icons/Quick-dependence.png')}}" width="50">
                  <div class="icon-txt">
                     <p>Quick, Dependable Transport </p>
                  </div>
               </div>
               <div class="icons-thumb">
                  <img src="{{asset('admin/asset/images/icons/secure-delivery.png')}}" width="50">
                  <div class="icon-txt">
                     <p>Prompt, Secure Delivery</p>
                  </div>
               </div>
               <div class="icons-thumb">
                  <img src="{{asset('admin/asset/images/icons/accuracy.png')}}" width="50">
                  <div class="icon-txt">
                     <p>Speedy, Accurate Shipping</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-lg-6 fluid">
            <div class="service-form ">
               <h5 >Your Global Shipping Solution Awaits! Get In Touch </h5>
               <form id="query_form" class="z-1" >
                        @method('POST')
                        @csrf
                        <div class="row">
                           <div class="col-md-6 mb-2 px-1">
                              <div class="form-group">
                                 <input type="text" class="form-control" name="firstname" id="firstname" required="" placeholder="First Name">
                              </div>
                           </div>
                           <div class="col-md-6 mb-2 px-1">
                              <div class="form-group">
                                 <input type="text" class="form-control" name="lastname" id="lastname" required="" placeholder="Last Name">
                              </div>
                           </div>
                           <div class="col-md-6 mb-2 px-1">
                              <div class="form-group">
                                 <input type="email" class="form-control" name="email" id="email" required="" placeholder="Email Id">
                              </div>
                           </div>
                           <div class="col-md-6 mb-2 px-1">
                              <div class="form-group">
                                 <input type="text" class="form-control" name="phone" id="phone" required="" placeholder="Phone Number" maxlength="10" size="10" pattern="[0-9]{10}" onkeypress="return isNumberKey(event,this)">
                              </div>
                           </div>
                           <div class="col-md-6 mb-2 px-1">
                              <div class="form-group">
                                 <select class="form-select bg-white" name="state" id="state" required="" onchange="getDestination()">
                                    <option value="Select City">Select State</option>
                                    @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->state }}</option>
                                    @endforeach
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-6 mb-2 px-1">
                              <div class="form-group">

                                 <select class="form-select bg-white" name="destination" id="destination" required="">
                                    <option value="" disabled selected>Select City</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-12 px-1">
                              <div class="form-group">
                                 <textarea type="textarea" id="message" name="message" class="form-control" placeholder="Message" rows="2"></textarea>
                              </div>
                           </div>
                           <input type="hidden" name="form_type" value="enquire_now">
                           <div class="col-md-12 px-1 ">
                              <button type="submit" value="submit_enquir" name="submit_enquir" class="corier-btn form-btn mt-4 ">SUBMIT </button>
                           </div>
                        </div>
                     </form>
            </div>
         </div>
      </div>
   </div>
</div>



  <script>
        $('#query_form').submit((event)=>{
            event.preventDefault();
            let formData = new FormData($('#query_form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{route('submitquery')}}',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response){
                    $('#query_form')[0].reset();
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
      function getDestination()
      {
        let state = $('#state').val();

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
                    $('#destination').html(options);
                }
            });
      }
    </script>
@endsection
