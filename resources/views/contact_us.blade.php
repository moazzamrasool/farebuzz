@extends('layouts.app')
@section('content')
<div class="pad100  bg-white" id="letst-get-started">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-5">
            <!-- <span class="sub-title ">Let's Talk</span>-->
            <h5 class="heading mb-3 " >Get In <span class="txt-color">Touch</span></h5>
            <p class="mb-1"><strong>WINIFY PVT. LTD.</strong></p>
            <div class="regis-add">
               <span><img src="{{asset('admin/asset/images/location.svg')}}" width="50" ></span>
               <div class="add-detais">
                  <h5>Address</h5>
                  <p>26/34,3rd Floor , Office No.201 Palmohan Mansion,Near Hotel Siddhartha,East Patel Nagar, <br> New Delhi</p>
               </div>
            </div>
            <div class="regis-add ">
               <span><img src="{{asset('admin/asset/images/phone.svg')}}" width="40" ></span>
               <div class="add-detais">
                  <h5>Contact Number.</h5>
                  <p><a href="tel:+91-9266332160">+91-76699-22258/76699-22259</a></p>
                  {{-- <a href="tel:011-71523024">011-71523024</a> --}}
               </div>
            </div>
            <div class="regis-add">
               <span><img src="{{asset('admin/asset/images/email.svg')}}" width="40" ></span>
               <div class="add-detais">
                  <h5>Email Id:</h5>
                  <p>
                     <a href="mailto:info@farebuzztravel.com">info@farebuzztravel.com</a>
                  </p>
               </div>
            </div>
         </div>
         <div class="col-md-7">
            <div class="contact-box">
               <form id="contact_form" >
                    @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <input type="text" class="form-control" name="name" id="name" required="" placeholder="Name">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <input type="email" class="form-control" name="email" id="email" required="" placeholder="Email Id">
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <input type="text" class="form-control" name="phone" id="phone" required="" placeholder="Phone Number" maxlength="10" size="10" pattern="[0-9]{10}" onkeypress="return isNumberKey(event,this)">
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <textarea type="textarea" id="message" required="" name="message" class="form-control" placeholder="Message" rows="4"></textarea>
                        </div>
                     </div>
                     <input type="hidden" name="form_type" value="contact_us">
                     <div class="col-md-12 ">
                        <button type="submit" value="submit" name="submit" class="corier-btn form-btn mt-4 ">SUBMIT </button>
                     </div>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<div class="pad800 ">
   <div class="map">
      <iframe src="https://www.google.com/maps/embed?pb=!1m13!1m8!1m3!1d7003.038252867398!2d77.173711!3d28.644171!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMjjCsDM4JzM5LjAiTiA3N8KwMTAnMzQuNiJF!5e0!3m2!1sen!2sin!4v1764361223693!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
   </div>
</div>

<script>
    // contact_form=document.getElementById('contact_form');
    // contact_form.onsubmit=function(e){
    //     e.preventDefault();
    //     let formdata=new FormData(contact_form);
    //     fetch("{{route('submit_contact_form')}}",{
    //         headers:{
    //             'X-CSRF-TOKEN':'{{csrf_token()}}'
    //         },
    //         method:"POST",
    //         body:formdata
    //     }).then(response=>response.json()).then(data=>{
    //         sweatalert("Success","Your message has been sent successfully! We will get back to you soon.","success");
    //         contact_form.reset();
    //     }).catch(error=>{
    //         sweatalert("An error occurred. Please try again later.","error");
    //     });
    // }

     $('#contact_form').submit((event)=>{
        
            event.preventDefault();
            let formData = new FormData($('#contact_form')[0]);
            $.ajax({
                type: 'POST',
                url: '{{route('submit_contact_form')}}',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(response){
                    $('#contact_form')[0].reset();
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
@endsection
